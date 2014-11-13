<?php

Namespace Model;

class RackspaceList extends BaseRackspaceAllOS {

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
        return $this->performRackspaceListData();
    }

    protected function performRackspaceListData(){
        if ($this->askForListExecute() != true) { return false; }
        $this->initialiseRackspace() ;
        $dataToList = $this->askForDataTypeToList();
        return $this->getDataListFromRackspace($dataToList);
    }

    private function askForListExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'List Data?';
        return self::askYesOrNo($question);
    }

    private function askForDataTypeToList() {
        $question = 'Please choose a data type to list:';
        // $options = array("servers", "sizes", "images", "domains", "regions", "ssh_keys");
        $options = array("servers", "sizes", "images") ;
        if (isset($this->params["rackspace-list-data-type"]) &&
            in_array($this->params["rackspace-list-data-type"], $options)) {
            return $this->params["rackspace-list-data-type"] ;
        }
        return self::askForArrayOption($question, $options, true);
    }

    public function getDataListFromRackspace($dataToList) {
        if ($dataToList == "images") {
            $compute = $this->rackspaceClient->computeService('cloudServersOpenStack', $this->region);
            $list = $compute->imageList();
        }
        if ($dataToList == "servers") {
            $compute = $this->rackspaceClient->computeService('cloudServersOpenStack', $this->region);
            $list = $compute->serverList();
        }
        if ($dataToList == "sizes") {
            $compute = $this->rackspaceClient->computeService('cloudServersOpenStack', $this->region);
            $list = $compute->flavorList();
        }
        return array($dataToList => $list);
    }

}