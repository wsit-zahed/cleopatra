<?php

Namespace Model;

class AWSEC2BoxAdd extends BaseAWSEC2AllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("BoxAdd") ;

    public function askWhetherToBoxAdd($params=null) {
        return $this->addBox($params);
    }

    public function addBox() {
        if ($this->askForBoxAddExecute() != true) { return false; }
        $this->initialiseAWS();
        $environments = \Model\AppConfig::getProjectVariable("environments");
        $workingEnvironment = $this->getWorkingEnvironment();
        if (is_array($environments) && count($environments)>0) {
            foreach ($environments as $environment) {
                if ($environment["any-app"]["gen_env_name"] == $workingEnvironment) {
                    $environmentExists = true ; } } }

        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);

        if (isset($environmentExists)) {
            foreach ($environments as $environment) {
                if ($environment["any-app"]["gen_env_name"] == $workingEnvironment) {
                    $envName = $environment["any-app"]["gen_env_name"];

                    if (isset($this->params["yes"]) && $this->params["yes"]==true) {
                        $addToThisEnvironment = true ; }
                    else {
                        $question = 'Add AWS EC2 Server Boxes to '.$envName.'?';
                        $addToThisEnvironment = self::askYesOrNo($question); }

                    if ($addToThisEnvironment == true) {
                        $this->createAllBoxes($envName) ;} } }

            return true ; }
        else {
            \Core\BootStrap::setExitCode(1) ;
            $logging->log("The environment $workingEnvironment does not exist.") ; }

    }

    protected function createAllBoxes ($envName) {
        $serverPrefix = $this->getServerPrefix();
        if (isset($this->params["parallax"])) {
            $command  = 'cleopatra parallax cli --yes --guess ';
            for ($i = 0; $i < $this->getServerGroupBoxAmount(); $i++) {
                $serverData = array();
                $serverData["sCount"] = $i ;
                if (isset($this->params["force-name"])) {
                    $serverData["name"] = $this->params["force-name"] ; }
                else {
                    $serverData["name"] = (isset( $serverData["prefix"]) && strlen( $serverData["prefix"])>0)
                        ? $serverData["prefix"].'-'.$envName
                        : $envName ;
                    if (isset( $serverData["suffix"]) && strlen( $serverData["suffix"])>0) {
                        $serverData["name"] .= '-'.$serverData["suffix"] ; }
                    $serverData["name"] .= '-'.$serverData["sCount"] ; }
                $force_name = $serverData["name"] ;
                $command .= ' --command-'.($i+1).'="cleopatra boxify box-add --guess --yes ' ;
                $command .= ' --environment-name='.$envName.' --provider-name=AWSEC2 ' ;
                $command .= ' --box-amount=1 --image-id='.$this->getServerGroupImageID() ;
                $command .= ' --region-id='.$this->getServerGroupRegionID().' --size-id='.$this->getServerGroupSizeID() ;
                $command .= ' --server-prefix='.$serverPrefix.' --box-user-name='.$this->getUsernameOfBox() ;
                $command .= ' --ssh-key-name='.$this->getSSHKeyName().' --private-ssh-key-path='.$this->getSSHKeyLocation() ;
                $command .= ' --wait-until-active --max-active-wait-time='.$this->getMaxWaitTime().' ' ;
                $command .= ' --force-name='.$force_name.' "' ; }
            $this->executeAndOutput($command) ; }
        else {
            for ($i = 0; $i < $this->getServerGroupBoxAmount(); $i++) {
                $serverData = array();
                $serverData["prefix"] = $serverPrefix ;
                $serverData["sCount"] = $i ;
                $serverData["sizeID"] = $this->getServerGroupSizeID() ;
                $serverData["imageID"] = $this->getServerGroupImageID() ;
                $serverData["regionID"] = $this->getServerGroupRegionID() ;
                if (isset($this->params["force-name"])) {
                    $serverData["name"] = $this->params["force-name"] ; }
                else {
                    $serverData["name"] = (isset( $serverData["prefix"]) && strlen( $serverData["prefix"])>0)
                        ? $serverData["prefix"].'-'.$envName
                        : $envName ;
                    if (isset( $serverData["suffix"]) && strlen( $serverData["suffix"])>0) {
                        $serverData["name"] .= '-'.$serverData["suffix"] ; }
                    $serverData["name"] .= '-'.$serverData["sCount"] ; }
                $response = $this->getNewServerFromAWSEC2($serverData) ;
                $this->addServerToPapyrus($envName, $response); } }
    }

    protected function askForBoxAddExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Add AWS EC2 Server Boxes?';
        return self::askYesOrNo($question);
    }

    protected function getMaxWaitTime() {
        if (isset($this->params["max-active-wait-time"])) { return $this->params["max-active-wait-time"] ; }
        return "300";
    }

    protected function getServerPrefix() {
        if (isset($this->params["server-prefix"])) {
            return $this->params["server-prefix"] ; }
        $question = 'Enter Prefix for all Servers (None is fine)';
        return self::askForInput($question);
    }

    protected function getWorkingEnvironment() {
        if (isset($this->params["environment-name"])) {
            return $this->params["environment-name"] ; }
        $question = 'Enter Environment to add Servers to';
        return self::askForInput($question);
    }

    protected function getServerGroupImageID() {
        if (isset($this->params["image-id"])) {
            return $this->params["image-id"] ; }
        $question = 'Enter Image ID for this Server Group';
        return self::askForInput($question, true);
    }

    protected function getServerGroupSizeID() {
        if (isset($this->params["size-id"])) {
            return $this->params["size-id"] ; }
        $question = 'Enter size ID for this Server Group';
        return self::askForInput($question, true);
    }

    protected function getServerGroupBoxAmount() {
        if (isset($this->params["box-amount"])) {
            return $this->params["box-amount"] ; }
        $question = 'Enter number of boxes to add to Environment';
        return self::askForInput($question, true);
    }

    protected function getUsernameOfBox($boxName = null) {
        if (isset($this->params["aws-box-user-name"])) {
            return $this->params["aws-box-user-name"] ; }
        if (isset($this->params["aws-box-username"])) {
            return $this->params["aws-box-username"] ; }
        $question = (isset($boxName))
            ? 'Enter SSH username of box '.$boxName
            : 'Enter SSH username of remote box';
        return self::askForInput($question, true);
    }

    protected function getSSHKeyLocation() {
        if (isset($this->params["aws-private-ssh-key-path"])) {
            return $this->params["aws-private-ssh-key-path"] ; }
        $question = 'Enter file path of private SSH Key';
        return self::askForInput($question, true);
    }

    protected function getSSHKeyName() {
        if (isset($this->params["ssh-key-name"])) {
            return $this->params["ssh-key-name"] ; }
        $question = 'Enter AWS EC2 SSH Key Name (Empty for none)';
        $this->params["ssh-key-name"] = self::askForInput($question, true) ;
        return $this->params["ssh-key-name"] ;
    }

    protected function getClient() {
        $this->setProjVars() ;
        $c = array(
            'key'    => $this->accessKey,
            'secret' => $this->secretKey,
            'region' => $this->region ) ;
        $this->awsClient = \Aws\Ec2\Ec2Client::factory($c);
    }

    protected function getNewServerFromAWSEC2($serverData) {

        $result = $this->awsClient->runInstances(array(
            'DryRun' => false,
            // ImageId is required
            'ImageId' => $serverData["imageID"],
            // MinCount is required
            'MinCount' => $serverData["sCount"],
            // MaxCount is required
            'MaxCount' => $serverData["sCount"],
            'KeyName' => $this->getSSHKeyName(),
            // @todo sg's
            // 'SecurityGroups' => array('string' ),
            // 'SecurityGroupIds' => array('string' ),
//            'UserData' => 'string',
//            'InstanceType' => 'string',
//            'Placement' => array(
//                'AvailabilityZone' => 'string',
//                'GroupName' => 'string',
//                'Tenancy' => 'string',
//            ),
//            'KernelId' => 'string',
//            'RamdiskId' => 'string',
//            'BlockDeviceMappings' => array(
//                array(
//                    'VirtualName' => 'string',
//                    'DeviceName' => 'string',
//                    'Ebs' => array(
//                        'SnapshotId' => 'string',
//                        'VolumeSize' => integer,
//                        'DeleteOnTermination' => true || false,
//                        'VolumeType' => 'string',
//                        'Iops' => integer,
//                    ),
//                    'NoDevice' => 'string',
//                ),
//                // ... repeated
//            ),
            'Monitoring' => array(
                // Enabled is required
                'Enabled' => false,
            ),
//            'SubnetId' => 'string',
//            'DisableApiTermination' => false,
//            'InstanceInitiatedShutdownBehavior' => 'string',
//            'PrivateIpAddress' => 'string',
//            'ClientToken' => 'string',
//            'AdditionalInfo' => 'string',
//            'NetworkInterfaces' => array(
//                array(
//                    //'NetworkInterfaceId' => 'string',
//                    //'DeviceIndex' => integer,
//                    'SubnetId' => 'string',
//                    'Description' => 'string',
//                    'PrivateIpAddress' => 'string',
//                    'Groups' => array('string' ),
//                    'DeleteOnTermination' => true || false,
//                    'PrivateIpAddresses' => array(
//                        array(
//                            // PrivateIpAddress is required
//                            'PrivateIpAddress' => 'string',
//                            'Primary' => true || false,
//                        ),
//                        // ... repeated
//                    ),
//                    'SecondaryPrivateIpAddressCount' => integer,
//                    'AssociatePublicIpAddress' => true || false,
//                ),
//                // ... repeated
//            ),
            // @todo iam bits
//            'IamInstanceProfile' => array(
//                'Arn' => 'string',
//                'Name' => 'string',
//            ),
            'EbsOptimized' => false,
        ));
//        $result = $this->awsClient->runInstances(array(
//            'DryRun' => true || false,
//            // ImageId is required
//            'ImageId' => 'string',
//            // MinCount is required
//            'MinCount' => integer,
//            // MaxCount is required
//            'MaxCount' => integer,
//            'KeyName' => 'string',
//            'SecurityGroups' => array('string' ),
//            'SecurityGroupIds' => array('string' ),
//            'UserData' => 'string',
//            'InstanceType' => 'string',
//            'Placement' => array(
//                'AvailabilityZone' => 'string',
//                'GroupName' => 'string',
//                'Tenancy' => 'string',
//            ),
//            'KernelId' => 'string',
//            'RamdiskId' => 'string',
//            'BlockDeviceMappings' => array(
//                array(
//                    'VirtualName' => 'string',
//                    'DeviceName' => 'string',
//                    'Ebs' => array(
//                        'SnapshotId' => 'string',
//                        'VolumeSize' => integer,
//                        'DeleteOnTermination' => true || false,
//                        'VolumeType' => 'string',
//                        'Iops' => integer,
//                    ),
//                    'NoDevice' => 'string',
//                ),
//                // ... repeated
//            ),
//            'Monitoring' => array(
//                // Enabled is required
//                'Enabled' => true || false,
//            ),
//            'SubnetId' => 'string',
//            'DisableApiTermination' => true || false,
//            'InstanceInitiatedShutdownBehavior' => 'string',
//            'PrivateIpAddress' => 'string',
//            'ClientToken' => 'string',
//            'AdditionalInfo' => 'string',
//            'NetworkInterfaces' => array(
//                array(
//                    'NetworkInterfaceId' => 'string',
//                    'DeviceIndex' => integer,
//                    'SubnetId' => 'string',
//                    'Description' => 'string',
//                    'PrivateIpAddress' => 'string',
//                    'Groups' => array('string' ),
//                    'DeleteOnTermination' => true || false,
//                    'PrivateIpAddresses' => array(
//                        array(
//                            // PrivateIpAddress is required
//                            'PrivateIpAddress' => 'string',
//                            'Primary' => true || false,
//                        ),
//                        // ... repeated
//                    ),
//                    'SecondaryPrivateIpAddressCount' => integer,
//                    'AssociatePublicIpAddress' => true || false,
//                ),
//                // ... repeated
//            ),
//            'IamInstanceProfile' => array(
//                'Arn' => 'string',
//                'Name' => 'string',
//            ),
//            'EbsOptimized' => true || false,
//        ));

        var_dump($result) ;

        $callVars = array() ;
        $callVars["name"] = $serverData["name"];
        $callVars["size_id"] = $serverData["sizeID"];
        $callVars["image_id"] = $serverData["imageID"];
        $callVars["region_id"] = $serverData["regionID"];
        $callVars["ssh_key_ids"] = $this->getAllSshKeyIdsString();
        $curlUrl = "https://api.awsec2.com/droplets/new" ;
        $callOut = "" ; // $this->awsCall($callVars, $curlUrl);
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Request for {$callVars["name"]} complete") ;
        return $callOut ;
    }

    protected function addServerToPapyrus($envName, $data) {
        $environments = \Model\AppConfig::getProjectVariable("environments");
        var_dump($data->droplet);
        $dropletData = $this->getDropletData($data->droplet->id);
        $server = array();
        $server["target"] = $dropletData->ip_address;
        $server["user"] = $this->getUsernameOfBox($data->name) ;
        $server["password"] = $this->getSSHKeyLocation() ;
        $server["provider"] = "AWSEC2";
        $server["id"] = $data->droplet->id;
        $server["name"] = $data->droplet->name;
        for ($i= 0 ; $i<count($environments); $i++) {
            if ($environments[$i]["any-app"]["gen_env_name"] == $envName) {
                $environments[$i]["servers"][] = $server; } }
        \Model\AppConfig::setProjectVariable("environments", $environments);
    }

    protected function getAllSshKeyIdsString() {
        if (isset($this->params["aws-ssh-key-ids"])) {
            return $this->params["aws-ssh-key-ids"] ; }
//        $curlUrl = "https://api.awsec2.com/ssh_keys" ;
//        $sshKeysObject =  $this->awsCall(array(), $curlUrl);
        $sshKeys = array();
//        foreach($sshKeysObject->ssh_keys as $sshKey) {
//            $sshKeys[] = $sshKey->name ; }
        $keysString = implode(",", $sshKeys) ;
        return $keysString;
    }

    protected function getDropletData($dropletId) {
        $curlUrl = "https://api.awsec2.com/droplets/$dropletId" ;
        $dropletObject =  $this->awsCall(array(), $curlUrl);
        return $dropletObject;
    }

}