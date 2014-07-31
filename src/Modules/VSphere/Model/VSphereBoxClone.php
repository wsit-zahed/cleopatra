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
                            $serverData["folder"] = $this->getServerGroupFolderID() ;
                            $serverData["vmid"] = $this->getServerGroupSourceVMId() ;
                            if (isset($this->params["force-name"])) {
                                $serverData["name"] = $this->params["force-name"] ; }
                            else {
                                $serverData["name"] = (isset( $serverData["prefix"]) && strlen( $serverData["prefix"])>0)
                                    ? $serverData["prefix"].'-'.$serverData["envName"]
                                    : $serverData["envName"] ;
                                if (isset( $serverData["suffix"]) && strlen( $serverData["suffix"])>0) {
                                    $serverData["name"] .= '-'.$serverData["suffix"] ; }
                                $serverData["name"] .= '-'.$serverData["sCount"] ; }
                            $response = $this->getNewServerFromVSphere($serverData) ;
                            $this->addServerToPapyrus($serverData["name"], $envName, $response);
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
        if (isset($this->params["force-name"])) { return "" ; }
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
        if (isset($this->params["force-name"])) { return "" ; }
        if (isset($this->params["server-suffix"])) {
            return $this->params["server-suffix"] ; }
        $question = 'Enter Suffix for all Servers (None is fine)';
        return self::askForInput($question);
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
        $callVars["folder"] = $serverData["folder"];
        $callVars["vmid"] = $serverData["vmid"];
        $callOut = $this->vSphereCall($callVars);
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Request for {$callVars["name"]} complete") ;
        $ar = array();
        $ar["response"] = $callOut;
        $ar["request"] = $callVars;
        return $ar ;
    }

    protected function addServerToPapyrus($sName, $envName, $data) {
        $vmData = $this->getVirtualMachineData($sName);
        if (isset($this->params["wait-until-active"])) {
            $vmData = $this->waitUntilActive($sName); }
        $server = array();
        $server["target"] = (isset($this->params["force-papyrus-ip"])) ? $this->params["force-papyrus-ip"] : $vmData["guest.ipAddress"] ;
        $server["user"] = $this->getUsernameOfBox() ;
        $server["password"] = $this->getSSHKeyLocation() ;
        $server["provider"] = "VSphere";
        $server["id"] = $vmData["vm-id"] ;
        $server["name"] = $sName;
        $environments = \Model\AppConfig::getProjectVariable("environments");
        for ($i= 0 ; $i<count($environments); $i++) {
            if ($environments[$i]["any-app"]["gen_env_name"] == $envName) {
                $environments[$i]["servers"][] = $server; } }
        \Model\AppConfig::setProjectVariable("environments", $environments);
    }

    protected function getVirtualMachineData($sName) {
        $vSphereFactory = new VSphere();
        $listVM = $vSphereFactory->getModel($this->params, "ListVM") ;
        $vmObject = $listVM->performVSphereListVMByName($sName) ;

        var_dump("sname", $sName, "vmo", $vmObject );
        return $vmObject;
    }

    protected function waitUntilActive($vmId) {
        $maxWaitTime = (isset($this->params["max-active-wait-time"])) ? $this->params["max-active-wait-time"] : "300" ;
        $i2 = 1 ;
        for($i=0; $i<=$maxWaitTime; $i=$i+10){
            $loggingFactory = new \Model\Logging();
            $logging = $loggingFactory->getModel($this->params);
            $logging->log("Attempt $i2 for vm $vmId to become active...") ;
            $vmData = $this->getVirtualMachineData($vmId);
            if (isset($vmData["guest.guestState"]) && $vmData["guest.guestState"]=="running" &&
                isset($vmData["guest.ipAddress"]) && strlen($vmData["guest.ipAddress"])>1 &&
                isset($vmData["runtime.powerState"]) && $vmData["runtime.powerState"]=="poweredOn" ) {
                return $vmData ; }
            sleep (10);
            $i2++; }
        return null;
    }

    protected function vSphereCall($callVars) {
        \Model\AppConfig::setProjectVariable("vsphere-pass", $this->vSpherePass) ;
        \Model\AppConfig::setProjectVariable("vsphere-domain-user", $this->domainUser) ;
        \Model\AppConfig::setProjectVariable("vsphere-url", $this->vSphereUrl) ;

        $context = stream_context_create(array(
            'http'=>array(
                'user_agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0'
            ),
            'ssl' => array(
                'verify_peer' => false,
                'allow_self_signed' => true
            )
        ));

        try {
            if (isset($this->params["ignore-ssl-check"])) {
                $this->client = new \soapclient("$this->vSphereUrl/sdk/vimService.wsdl", array ('location' => "$this->vSphereUrl/sdk/", 'trace' => 1, "stream_context" => $context, 'cache_wsdl' => WSDL_CACHE_NONE)); }
            else {
                $this->client = new \soapclient("$this->vSphereUrl/sdk/vimService.wsdl", array ('location' => "$this->vSphereUrl/sdk/", 'trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE)); } }
        catch (\Exception $e) {
                echo $e->getMessage();
                exit; }

        // this is to get us a root folder, $ret->rootFolder
        try {
            $request = new \stdClass();
            $request->_this = array ('_' => 'ServiceInstance', 'type' => 'ServiceInstance');
            $response = $this->client->__soapCall('RetrieveServiceContent', array((array)$request)); }
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
            $response = $this->client->__soapCall('Login', array((array)$request));
            echo "User " . $response->returnval->fullName .', '. $response->returnval->userName . " Logged In successfully\n"; }
        catch (\Exception $e) {
            echo $e->getMessage();
            exit; }

        // clone a vm
        try {
            $request = new \stdClass();
            $request->_this = $callVars["vmid"] ; //"vm-156" ; //this is the vm-*** id, a Managed Object Reference
            $request->folder = $callVars["folder"] ; //"group-v3" ;
            $request->name = $callVars["name"] ;
            $request->spec = array (
                // 'config' => "dave_box",
                // 'customization' => array(),
                'location' => array(),
                'powerOn' => true,
                // 'snapshot' => array(),
                'template' => false,
            );
            $res1 = $this->client->__soapCall('CloneVM_Task', array((array)$request));   }
        catch (\Exception $e) {
            echo $e->getMessage();
            exit; }

        // This logs out
        try {
            $request = new \stdClass();
            $request->_this = $ret->sessionManager;
            $request->userName = $this->domainUser;
            $request->password =  $this->vSpherePass;
            $res2 = $this->client->__soapCall('Logout', array((array)$request)); }
        catch (\Exception $e) {
            echo $e->getMessage() ;
            exit; }

        // var_dump("rv", $res1->returnval) ;
        return $res1->returnval ;
    }

}
