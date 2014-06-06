<?php

Namespace Model;

class BaseVSphereAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Base") ;

    protected $vSpherePass ;
    protected $domainUser ;
    protected $vSphereUrl ;

    protected function askForVSphereDomainUser(){
        if (isset($this->params["vsphere-domain-user"])) { return $this->params["vsphere-domain-user"] ; }
        $papyrusVar = \Model\AppConfig::getProjectVariable("vsphere-domain-user") ;
        if ($papyrusVar != null) {
            if (isset($this->params["guess"]) && $this->params["guess"] == true) { return $papyrusVar ; }
            if (isset($this->params["use-project-domain-user"]) && $this->params["use-project-domain-user"] == true) {
                return $papyrusVar ; }
            $question = 'Use Project saved VMWare VSphere Domain/Username?';
            if (self::askYesOrNo($question, true) == true) { return $papyrusVar ; } }
        $appVar = \Model\AppConfig::getProjectVariable("vsphere-domain-user") ;
        if ($appVar != null) {
            $question = 'Use Application saved VMWare VSphere Domain/Username?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ; } }
        $question = 'Enter VMWare VSphere Domain/Username';
        return self::askForInput($question, true);
    }

    protected function askForVSpherePassword(){
        if (isset($this->params["vsphere-pass"])) { return $this->params["vsphere-pass"] ; }
        $papyrusVar = \Model\AppConfig::getProjectVariable("vsphere-pass") ;
        if ($papyrusVar != null) {
            if (isset($this->params["guess"]) && $this->params["guess"] == true) { return $papyrusVar ; }
            if (isset($this->params["use-project-pass"]) && $this->params["use-project-pass"] == true) { return $papyrusVar ; }
            $question = 'Use Project saved VMWare VSphere Password?';
            if (self::askYesOrNo($question, true) == true) {
                return $papyrusVar ; } }
        $appVar = \Model\AppConfig::getProjectVariable("vsphere-pass") ;
        if ($appVar != null) {
            $question = 'Use Application saved VMWare VSphere Password?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ; } }
        $question = 'Enter VMWare VSphere Password';
        return self::askForInput($question, true);
    }

    protected function askForVSphereUrl(){
        if (isset($this->params["vsphere-url"])) { return $this->params["vsphere-url"] ; }
        $papyrusVar = \Model\AppConfig::getProjectVariable("vsphere-url") ;
        if ($papyrusVar != null) {
            if (isset($this->params["guess"])) {
                return $papyrusVar ; }
            if (isset($this->params["use-project-url"]) && $this->params["use-project-url"] == true) {
                return $papyrusVar ; }
            $question = 'Use Project saved VMWare VSphere URL?';
            if (self::askYesOrNo($question, true) == true) { return $papyrusVar ; } }
        $appVar = \Model\AppConfig::getProjectVariable("vsphere-url") ;
        if ($appVar != null) {
            $question = 'Use Application saved VMWare VSphere URL?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ; } }
        $question = 'Enter VMWare VSphere URL';
        return self::askForInput($question, true);
    }

    protected function vSphereCall(Array $curlParams) {

        // @todo do we actually need to set this every time? highly unlikely
        \Model\AppConfig::setProjectVariable("vsphere-pass", $this->vSpherePass) ;
        \Model\AppConfig::setProjectVariable("vsphere-domain-user", $this->domainUser) ;
        \Model\AppConfig::setProjectVariable("vsphere-url", $this->vSphereUrl) ;

        require_once (__DIR__."/../Libraries/scd.php") ;

        $client = new \soapclientd("$this->vSphereUrl/sdk/vimService.wsdl", array ('location' => "$this->vSphereUrl/sdk/", 'trace' => 1));

        // this is to get us a root folder, $ret->rootFolder
        try {
            $request = new \stdClass();
            $request->_this = array ('_' => 'ServiceInstance', 'type' => 'ServiceInstance');
            $response = $client->__soapCall('RetrieveServiceContent', array((array)$request)); }
        catch (\Exception $e) {
            echo $e->getMessage();
            exit; }
        $ret = $response->returnval;

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

        // Create conditions to find objects
        $ss1 = new \soapvar(array ('name' => 'FolderTraversalSpec'), SOAP_ENC_OBJECT, null, null, 'selectSet', null);
        $ss2 = new \soapvar(array ('name' => 'DataCenterVMTraversalSpec'), SOAP_ENC_OBJECT, null, null, 'selectSet', null);
        $a = array ('name' => 'FolderTraversalSpec', 'type' => 'Folder', 'path' => 'childEntity', 'skip' => false, $ss1, $ss2);

        $ss = new \soapvar(array ('name' => 'FolderTraversalSpec'), SOAP_ENC_OBJECT, null, null, 'selectSet', null);
        $b = array ('name' => 'DataCenterVMTraversalSpec', 'type' => 'Datacenter', 'path' => 'vmFolder', 'skip' => false, $ss);

        $res = null;

        // get the objects
        try {
            $request = new \stdClass();
            $request->_this = $ret->propertyCollector;
            $request->specSet = array (
                'propSet' => array (
                    array ('type' => 'VirtualMachine', 'all' => 0, 'pathSet' => array ('name', 'guest.ipAddress', 'guest.guestState', 'runtime.powerState', 'config.hardware.numCPU', 'config.hardware.memoryMB')),
                ),
                'objectSet' => array (
                    'obj' => $ret->rootFolder,
                    'skip' => false,
                    'selectSet' => array (
                        new \soapvar($a, SOAP_ENC_OBJECT, 'TraversalSpec'),
                        new \soapvar($b, SOAP_ENC_OBJECT, 'TraversalSpec'),
                    ),
                )
            );
            $res = $client->__soapCall('RetrieveProperties', array((array)$request)); }
        catch (\Exception $e) {
            echo $e->getMessage(); }
        foreach ($res->returnval as $key => $resEntry) {
            echo "Object $key\n" ;
            echo "  Name: ".$resEntry->obj->_."\n" ;
            echo "  Type: ".$resEntry->obj->type."\n" ;
            echo "    Properties:\n" ;
            foreach ($resEntry->propSet as $entry) {
                echo "      ".$entry->name." = ".$entry->val."\n" ; }
        }

        // This logs out
        try {
            $request = new \stdClass();
            $request->_this = $ret->sessionManager;
            $request->userName = $this->domainUser;
            $request->password =  $this->vSpherePass;
            $response = $client->__soapCall('Logout', array((array)$request));
            // echo "User " . $response->returnval->fullName .', '. $response->returnval->userName . " Logged Out successfully\n";
        }
        catch (\Exception $e) {
            echo $e->getMessage();
            exit; }
    }

}