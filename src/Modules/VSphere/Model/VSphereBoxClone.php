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
                        $cloneToThisEnvironment = true ; }
                    else {
                        $question = 'Clone VMWare VSphere Server Boxes to '.$envName.'?';
                        $cloneToThisEnvironment = self::askYesOrNo($question); }

                    if ($cloneToThisEnvironment == true) {
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
                            $this->cloneServerToPapyrus($envName, $response); } } } }

                return true ; }
        else {
            \Core\BootStrap::setExitCode(1) ;
            $logging->log("The environment $workingEnvironment does not exist.") ; }
    }

    private function askForBoxCloneExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Clone VMWare VSphere Server Boxes?';
        return self::askYesOrNo($question);
    }

    private function getServerPrefix() {
        if (isset($this->params["server-prefix"])) {
            return $this->params["server-prefix"] ; }
        $question = 'Enter Prefix for all Servers (None is fine)';
        return self::askForInput($question);
    }

    private function getWorkingEnvironment() {
        if (isset($this->params["environment-name"])) {
            return $this->params["environment-name"] ; }
        $question = 'Enter Environment to clone Servers to';
        return self::askForInput($question);
    }

    private function getServerGroupImageID() {
        if (isset($this->params["image-id"])) {
            return $this->params["image-id"] ; }
        $question = 'Enter Image ID for this Server Group';
        return self::askForInput($question, true);
    }

    private function getServerGroupSizeID() {
        if (isset($this->params["size-id"])) {
            return $this->params["size-id"] ; }
        $question = 'Enter size ID for this Server Group';
        return self::askForInput($question, true);
    }

    private function getServerGroupRegionID() {
        if (isset($this->params["region-id"])) {
            return $this->params["region-id"] ; }
        $question = 'Enter Region ID for this Server Group';
        return self::askForInput($question, true);
    }

    private function getServerGroupBoxAmount() {
        if (isset($this->params["box-amount"])) {
            return $this->params["box-amount"] ; }
        $question = 'Enter number of boxes to clone to Environment';
        return self::askForInput($question, true);
    }

    private function getUsernameOfBox($boxName = null) {
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

    private function getSSHKeyLocation() {
        if (isset($this->params["private-ssh-key-path"])) {
            return $this->params["private-ssh-key-path"] ; }
        $question = 'Enter file path of private SSH Key';
        $this->params["private-ssh-key-path"] = self::askForInput($question, true) ;
        return $this->params["private-ssh-key-path"] ;
    }

    private function getNewServerFromVSphere($serverData) {
        $callVars = array() ;
        $callVars["name"] = $serverData["name"];
        // $callVars["size_id"] = $serverData["sizeID"];
        // $callVars["image_id"] = $serverData["imageID"];
        // $callVars["region_id"] = $serverData["regionID"];
        // $callVars["ssh_key_ids"] = $this->getAllSshKeyIdsString();

        $callOut = $this->vSphereCall();
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Request for {$callVars["name"]} complete") ;
        return $callOut ;
    }

    private function cloneServerToPapyrus($envName, $data) {
        $dropletData = $this->getDropletData($data->droplet->id);
        if (!isset($dropletData->ip_cloneress) && isset($this->params["wait-for-box-info"])) {
            $dropletData = $this->waitForBoxInfo($data->droplet->id); }
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

    private function getDropletData($dropletId) {
        $curlUrl = "https://api.vmware-vsphere.com/droplets/$dropletId" ;
        $dropletObject =  $this->vSphereCall(array(), $curlUrl);
        return $dropletObject;
    }

    private function waitForBoxInfo($dropletId) {
        $maxWaitTime = (isset($this->params["max-box-info-wait-time"])) ? $this->params["max-box-info-wait-time"] : "300" ;
        $i2 = 1 ;
        for($i=0; $i<=$maxWaitTime; $i=$i+10){
            $loggingFactory = new \Model\Logging();
            $logging = $loggingFactory->getModel($this->params);
            $logging->log("Attempt $i2 for droplet $dropletId box info...") ;
            $dropletData = $this->getDropletData($dropletId);
            if (isset($dropletData->droplet->ip_address)) {
                return $dropletData ; }
            sleep (10);
            $i2++; }
        return null;
    }

    private function waitUntilActive($dropletId) {
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


    protected function vSphereCall() {

        // @todo do we actually need to set this every time? highly unlikely
        \Model\AppConfig::setProjectVariable("vsphere-pass", $this->vSpherePass) ;
        \Model\AppConfig::setProjectVariable("vsphere-domain-user", $this->domainUser) ;
        \Model\AppConfig::setProjectVariable("vsphere-url", $this->vSphereUrl) ;

        require_once (__DIR__."/../Libraries/scd.php") ;

        // ($request, $location, $action, $version, $one_way = 0)

        // $client = new \soapclientd("$this->vSphereUrl/sdk/vimService.wsdl", array ('location' => "$this->vSphereUrl/sdk/", 'trace' => 1));

        $client = new \soapclient("$this->vSphereUrl/sdk/vimService.wsdl", array ('location' => "$this->vSphereUrl/sdk/", 'trace' => 1));
            // SoapClient->__getLastResponse

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
            echo "trying to clone vm\n" ;
            $request = new \stdClass();
            $request->_this = $ret->sessionManager; // i think this is the vm-*** id, it requires a M.O.R.
            $request->config = array (
                'name' => "dave_box",
                'annotation' => "Go on, its friday, just work"
            );
            $request->pool = $ret->rootFolder;
            $res1 = $client->__soapCall('CreateVM_Task', array((array)$request));
            var_dump("r1: ", $res1) ; }
        catch (\Exception $e) {
            var_dump(
                "exception message: ",
                $e->getMessage(),
                "last request: ",
                $client->__getLastRequest(),
                "last response: ",
                $client->__getLastResponse()
            );
        }

        // This logs out
        try {
            $request = new \stdClass();
            $request->_this = $ret->sessionManager;
            $request->userName = $this->domainUser;
            $request->password =  $this->vSpherePass;
            $res2 = $client->__soapCall('Logout', array((array)$request)); }
        catch (\Exception $e) {
            var_dump($e->getMessage());
            exit; }

        return $res1->returnval ;
    }

}
