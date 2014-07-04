<?php

Namespace Model;

class VSphereListVM extends BaseVSphereAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("ListVM") ;

    public function __construct($params) {
        parent::__construct($params) ;
    }

    public function askWhetherToListData() {
        return $this->performVSphereListData();
    }

    protected function performVSphereListData(){
        if ($this->askForListExecute() != true) { return false; }
        $this->domainUser = $this->askForVSphereDomainUser();
        $this->vSpherePass = $this->askForVSpherePassword();
        $this->vSphereUrl = $this->askForVSphereUrl();
        // $dataToList = $this->askForDataTypeToList();
        return $this->getDataListFromVSphere();
    }

    public function performVSphereListVMByName($reqName){
        if ($this->askForListExecute() != true) { return false; }
        $this->domainUser = $this->askForVSphereDomainUser();
        $this->vSpherePass = $this->askForVSpherePassword();
        $this->vSphereUrl = $this->askForVSphereUrl();
        $dl = $this->getDataListFromVSphere();
        foreach ($dl as $vm) {
            foreach($vm->propSet as $prop) {
                if ( $prop->name == "name" && $prop->val == $reqName) {
                    $props = array() ;
                    $i = 0;
                    foreach ($vm->propSet as $proppy) {
                        $props[$proppy->name] = $proppy->val;
                        $i++; }
                    return $props ; } } }
        return null ;
    }

    private function askForListExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'List VM Data?';
        return self::askYesOrNo($question);
    }

    private function askForDataTypeToList(){
        $question = 'Please choose a data type to list:';
        $options = array("droplets", "sizes", "images", "domains", "regions", "ssh_keys");
        if (isset($this->params["vsphere-list-data-type"]) &&
            in_array($this->params["vsphere-list-data-type"], $options)) {
            return $this->params["vsphere-list-data-type"] ; }
        return self::askForArrayOption($question, $options, true);
    }

    public function getDataListFromVSphere(){
        $callVars = array();
        return $this->vSphereCall($callVars);
    }

    protected function vSphereCall(Array $curlParams) {

        \Model\AppConfig::setProjectVariable("vsphere-pass", $this->vSpherePass) ;
        \Model\AppConfig::setProjectVariable("vsphere-domain-user", $this->domainUser) ;
        \Model\AppConfig::setProjectVariable("vsphere-url", $this->vSphereUrl) ;

        // require_once (__DIR__."/../Libraries/scd.php") ;

        $client = new \soapclient("$this->vSphereUrl/sdk/vimService.wsdl", array ('location' => "$this->vSphereUrl/sdk/", 'trace' => 1));

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
            // echo "User " . $response->returnval->fullName .', '. $response->returnval->userName . " Logged In successfully\n";
        }
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

        return $res->returnval ;
    }


}