<?php

Namespace Model;

class VSphereBoxClone extends BaseVSphereAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("BoxClone") ;

    public function askWhetherToBoxClone($params=null) {
        return $this->cloneBox($params);
    }

    public function cloneBox() {
        if ($this->askForBoxCloneExecute() != true) { return false; }
        $this->domainUser = $this->askForVSphereDomainUser();
        $this->vSpherePass = $this->askForVSpherePassword();
        $this->vSphereUrl = $this->askForVSphereUrl();
        $serverPrefix = $this->getServerPrefix();
        $serverSuffix = $this->getServerSuffix();
        $environments = \Model\AppConfig::getProjectVariable("environments");
        $workingEnvironment = $this->getWorkingEnvironment();

        foreach ($environments as $environment) {
            if ($environment["any-app"]["gen_env_name"] == $workingEnvironment) {
                $environmentExists = true ; } }

        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);

        if (isset($environmentExists)) {
            foreach ($environments as $environment) {
                if ($environment["any-app"]["gen_env_name"] == $workingEnvironment) {
                    $envName = $environment["any-app"]["gen_env_name"];

                    if (isset($this->params["yes"]) && $this->params["yes"]==true) {
                        $addToThisEnvironment = true ; }
                    else {
                        $question = 'Add VMWare VSphere Server Boxes to '.$envName.'?';
                        $addToThisEnvironment = self::askYesOrNo($question); }

                    if ($addToThisEnvironment == true) {
                        for ($i = 0; $i < $this->getServerGroupBoxAmount(); $i++) {
                            $serverData = array();
                            $serverData["prefix"] = $serverPrefix ;
                            $serverData["envName"] = $envName ;
                            $serverData["sCount"] = $i ;
                            // $serverData["sizeID"] = $this->getServerGroupSizeID() ;
                            // $serverData["imageID"] = $this->getServerGroupImageID() ;
                            // $serverData["regionID"] = $this->getServerGroupRegionID() ;
                            $serverData["name"] = (isset( $serverData["prefix"]) && strlen( $serverData["prefix"])>0)
                                ? $serverData["prefix"].'-'.$serverData["envName"].'-'.$serverData["sCount"]
                                : $serverData["envName"].'-'.$serverData["sCount"] ;
                            $response = $this->getNewServerFromVSphere($serverData) ;
                            // var_dump("response", $response) ;
                            $this->addServerToPapyrus($envName, $response); }

                        $cloneToThisEnvironment = true ; }
                    else {
                        $question = 'Clone VMWare VSphere Server Boxes to '.$envName.'?';
                        $cloneToThisEnvironment = self::askYesOrNo($question); }

                    if ($cloneToThisEnvironment == true) {
                        for ($i = 0; $i < $this->getServerGroupBoxAmount(); $i++) {
                            $serverData = array();
                            $serverData["prefix"] = $serverPrefix ;
                            $serverData["suffix"] = $serverSuffix ;
                            $serverData["envName"] = $envName ;
                            $serverData["sCount"] = $i ;
                            // $serverData["sizeID"] = $this->getServerGroupSizeID() ;
                            $serverData["folder-id"] = $this->getServerGroupFolderID() ;
                            $serverData["source-vm-id"] = $this->getServerGroupSourceVMId() ;
                            $serverData["name"] = (isset( $serverData["prefix"]) && strlen( $serverData["prefix"])>0)
                                ? $serverData["prefix"].'-'.$serverData["envName"]
                                : $serverData["envName"] ;
                            if (isset( $serverData["suffix"]) && strlen( $serverData["suffix"])>0) {
                                $serverData["name"] .= '-'.$serverData["suffix"] ; }
                            $serverData["name"] .= '-'.$serverData["sCount"] ;
                            $response = $this->getNewServerFromVSphere($serverData) ;
                            // var_dump("response", $response) ;
                            // $this->addServerToPapyrus($envName, $response);
                        }
                    }
                }
            }
                return true ; }
        else {
            \Core\BootStrap::setExitCode(1) ;
            $logging->log("The environment $workingEnvironment does not exist.") ; }
    }


    protected function askForBoxCloneExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Clone VMWare VSphere Server Boxes?';
        return self::askYesOrNo($question);
    }

    protected function getServerPrefix() {
        if (isset($this->params["server-prefix"])) {
            return $this->params["server-prefix"] ; }
        $question = 'Enter Prefix for all Servers (None is fine)';
        return self::askForInput($question);
    }

    private function getWorkingEnvironment() {
        if (isset($this->params["environment-name"])) {
            return $this->params["environment-name"] ; }
        $question = 'Enter Environment to add Servers to';
        return self::askForInput($question);
    }

    protected function getServerSuffix() {
        if (isset($this->params["server-suffix"])) {
            return $this->params["server-suffix"] ; }
        $question = 'Enter Suffix for all Servers (None is fine)';
        return self::askForInput($question);
    }

    protected function getServerGroupImageID() {
        if (isset($this->params["image-id"])) {
            return $this->params["image-id"] ; }
        $question = 'Enter Image ID for this Server Group';
        return self::askForInput($question, true);
    }

    protected function getServerGroupSourceVMId() {
        if (isset($this->params["source-vm-id"])) {
            return $this->params["source-vm-id"] ; }
        $question = 'Enter Source VM ID for this Server Group';
        return self::askForInput($question, true);
    }

    protected function getServerGroupFolderID() {
        if (isset($this->params["folder-id"])) {
            return $this->params["folder-id"] ; }
        $question = 'Enter Folder ID for this Server Group';
        return self::askForInput($question, true);
    }

    protected function getServerGroupBoxAmount() {
        if (isset($this->params["box-amount"])) {
            return $this->params["box-amount"] ; }
        $question = 'Enter number of boxes to clone to Environment';
        return self::askForInput($question, true);
    }

    protected function getUsernameOfBox($boxName = null) {
        if (isset($this->params["box-user-name"])) {
            return $this->params["box-user-name"] ; }
        if (isset($this->params["box-username"])) {
            return $this->params["box-username"] ; }
        $question = (isset($boxName))
            ? 'Enter SSH username of box '.$boxName
            : 'Enter SSH username of remote box';
        $this->params["box-user-name"] = self::askForInput($question, true) ;
        return $this->params["box-user-name"] ;
    }

    protected function getSourceVMId() {
        if (isset($this->params["source-vm-id"])) {
            return $this->params["source-vm-id"] ; }
        if (isset($this->params["source-vm-name"])) {
            return $this->getVMIdFromName($this->params["source-vm-id"]) ; }
        $question = 'Enter Source VM ID for cloning' ;
        $this->params["source-vm-id"] = self::askForInput($question, true) ;
        return $this->params["source-vm-id"] ;
    }

    protected function getSSHKeyLocation() {
        if (isset($this->params["private-ssh-key-path"])) {
            return $this->params["private-ssh-key-path"] ; }
        $question = 'Enter file path of private SSH Key';
        $this->params["private-ssh-key-path"] = self::askForInput($question, true) ;
        return $this->params["private-ssh-key-path"] ;
    }

    protected function getNewServerFromVSphere($serverData) {
        $callVars = array() ;
        $callVars["name"] = $serverData["name"];
        // $callVars["size_id"] = $serverData["sizeID"];
        // $callVars["image_id"] = $serverData["imageID"];
        // $callVars["region_id"] = $serverData["regionID"];
        // $callVars["ssh_key_ids"] = $this->getAllSshKeyIdsString();

        $callOut = $this->vSphereCall($callVars);
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Request for {$callVars["name"]} complete") ;
        return $callOut ;
    }

    protected function addServerToPapyrus($envName, $data) {
        $dropletData = $this->getDropletData($data->droplet->id);
        if (($dropletData->status != "active") && isset($this->params["wait-until-active"])) {
            $dropletData = $this->waitUntilActive($data->droplet->id); }
        $server = array();
        $server["target"] = $dropletData->droplet->ip_address;
        $server["user"] = $this->getUsernameOfBox() ;
        $server["password"] = $this->getSSHKeyLocation() ;
        $server["provider"] = "VSphere";
        $server["id"] = $data->droplet->id;
        $server["name"] = $data->droplet->name;
        // file_put_contents("/tmp/outloc", getcwd()) ;
        // file_put_contents("/tmp/outsrv", $server) ;
        $environments = \Model\AppConfig::getProjectVariable("environments");
        // file_put_contents("/tmp/outenv1", serialize($environments)) ;
        for ($i= 0 ; $i<count($environments); $i++) {
            if ($environments[$i]["any-app"]["gen_env_name"] == $envName) {
                $environments[$i]["servers"][] = $server; } }
        // file_put_contents("/tmp/outenv2", serialize($environments)) ;
        \Model\AppConfig::setProjectVariable("environments", $environments);
    }

    private function getAllSshKeyIdsString() {
        if (isset($this->params["ssh-key-ids"])) {
            return $this->params["ssh-key-ids"] ; }
        $curlUrl = "https://api.vmware-vsphere.com/ssh_keys" ;
        $sshKeysObject =  $this->vSphereCall(array(), $curlUrl);
        $sshKeys = array();
        // @todo use the list call to get ids, this uses name
        foreach($sshKeysObject->ssh_keys as $sshKey) {
            $sshKeys[] = $sshKey->id ; }
        $keysString = implode(",", $sshKeys) ;
        return $keysString;
    }

    protected function getDropletData($dropletId) {
        $curlUrl = "https://api.vmware-vsphere.com/droplets/$dropletId" ;
        $dropletObject = "" ; // $this->vSphereCall(array(), $curlUrl);
        return $dropletObject;
    }

    protected function waitUntilActive($dropletId) {
        $maxWaitTime = (isset($this->params["max-active-wait-time"])) ? $this->params["max-active-wait-time"] : "300" ;
        $i2 = 1 ;
        for($i=0; $i<=$maxWaitTime; $i=$i+10){
            $loggingFactory = new \Model\Logging();
            $logging = $loggingFactory->getModel($this->params);
            $logging->log("Attempt $i2 for droplet $dropletId to become active...") ;
            $dropletData = $this->getDropletData($dropletId);
            if (isset($dropletData->droplet->status) && $dropletData->droplet->status=="active") {
                return $dropletData ; }
            sleep (10);
            $i2++; }
        return null;
    }


    protected function vSphereCall($callVars) {
        // @todo do we actually need to set this every time? highly unlikely
        \Model\AppConfig::setProjectVariable("vsphere-pass", $this->vSpherePass) ;
        \Model\AppConfig::setProjectVariable("vsphere-domain-user", $this->domainUser) ;
        \Model\AppConfig::setProjectVariable("vsphere-url", $this->vSphereUrl) ;
        $client = new \soapclient("$this->vSphereUrl/sdk/vimService.wsdl", array ('location' => "$this->vSphereUrl/sdk/", 'trace' => 1));

        // this is to get us a root folder, $ret->rootFolder
        try {
            $request = new \stdClass();
            $request->_this = array ('_' => 'ServiceInstance', 'type' => 'ServiceInstance');
            $response = $client->__soapCall('RetrieveServiceContent', array((array)$request)); }
        catch (\Exception $e) {
            echo $e->getMessage();
            exit; }
        $ret = $response->returnval; // group-d1

        // This makes sure we can login
        try {
            $request = new \stdClass();
            $request->_this = $ret->sessionManager;
            $request->userName = $this->domainUser;
            $request->password =  $this->vSpherePass;
            $response = $client->__soapCall('Login', array((array)$request));
            echo "User " . $response->returnval->fullName .', '. $response->returnval->userName . " Logged In successfully\n"; }
        catch (\Exception $e) {
            echo $e->getMessage();
            exit; }

        // create a vm
        try {
            echo "trying to create vm\n" ;
            $request = new \stdClass();
            $request->_this = $callVars["source-vm-id"] ; //"vm-156" ; //this is the vm-*** id, a Managed Object Reference
            $request->folder = $callVars["folder-id"] ; //"group-v3" ;
            $request->name = $callVars["name"] ;
            $request->spec = array (
                // 'config' => "dave_box",
                // 'customization' => array(),
                'location' => array(),
                'powerOn' => true,
                // 'snapshot' => array(),
                'template' => false,
            );
            $res1 = $client->__soapCall('CloneVM_Task', array((array)$request)); }
        catch (\Exception $e) {
            echo $e->getMessage();
            exit; }

        // This logs out
        try {
            $request = new \stdClass();
            $request->_this = $ret->sessionManager;
            $request->userName = $this->domainUser;
            $request->password =  $this->vSpherePass;
            $res2 = $client->__soapCall('Logout', array((array)$request)); }
        catch (\Exception $e) {
            var_dump($e->getMessage());
            echo $e->getMessage() ;
            exit; }

        return $res1->returnval ;
    }

}
