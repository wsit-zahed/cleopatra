<?php

Namespace Model;

class VSphereBoxPowerOff extends BaseVSphereAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("BoxPowerOff") ;

    public function askWhetherToPowerOff($params=null) {
        return $this->powerOffBox($params);
    }

    public function powerOffBox() {
        if ($this->askForBoxPowerOffExecute() != true) { return false; }
        $this->domainUser = $this->askForVSphereDomainUser();
        $this->vSpherePass = $this->askForVSpherePassword();
        $this->vSphereUrl = $this->askForVSphereUrl();

        $this->powerOffByEnvironment() ;
        $this->powerOffById() ;

    }

    protected function powerOffByEnvironment() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $environments = \Model\AppConfig::getProjectVariable("environments");
        if (isset($this->params["environment-name"])) {
            foreach ($environments as $environment) {
                if ($environment["any-app"]["gen_env_name"] == $this->params["environment-name"]) {
                    $logging->log("Found environment {$this->params["environment-name"]}, powering off...") ;
                    foreach ($environment["servers"] as $server) {
                        $callData = array() ;
                        $callData["vmid"] = $server["id"] ;
                        $logging->log("Powering off box with id {$callData["vmid"]}...") ;
                        $this->vSphereCall($callData) ; } } } }
        else {
            $logging->log("No environment name specified") ; }
    }

    protected function powerOffById() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        if (isset($this->params["vmid"])) { $this->params["vm-id"] = $this->params["vmid"] ; }
        if (isset($this->params["vm-id"])) {
            $callData = array() ;
            $callData["vmid"] = $this->params["vm-id"] ;
            $logging->log("powering off box with id {$callData["vmid"]}...") ;
            $this->vSphereCall($callData) ; }
        else {
            $logging->log("No VM ID specified") ; }
    }

    protected function askForBoxPowerOffExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'PowerOff VMWare VSphere Server Boxes?';
        return self::askYesOrNo($question);
    }

    private function getWorkingEnvironment() {
        if (isset($this->params["environment-name"])) {
            return $this->params["environment-name"] ; }
        $question = 'Enter Environment to add Servers to';
        return self::askForInput($question);
    }

    protected function getServerGroupSourceVMId() {
        if (isset($this->params["vmid"])) { return $this->params["vmid"] ; }
        if (isset($this->params["vm-id"])) { return $this->params["vm-id"] ; }
        $question = 'Enter VM ID for Powering Off';
        return self::askForInput($question, true);
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

        // powerOff a vm
        try {
            $request = new \stdClass();
            $request->_this = $callVars["vmid"] ;
            $res1 = $this->client->__soapCall('PowerOffVM_Task', array((array)$request));   }
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
