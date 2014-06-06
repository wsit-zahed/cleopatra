<?php

Namespace Model;

class VSphereList extends BaseVSphereAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Listing") ;

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
        $dataToList = $this->askForDataTypeToList();
        return $this->getDataListFromVSphere($dataToList);
    }

    private function askForListExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'List Data?';
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

    public function getDataListFromVSphere($dataToList){
        $callVars = array();
        $curlUrl = "https://api.vmware-vsphere.com/$dataToList/" ;
        return $this->vSphereCall($callVars, $curlUrl);
    }

}