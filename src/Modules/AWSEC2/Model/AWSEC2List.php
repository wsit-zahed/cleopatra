<?php

Namespace Model;

class AWSEC2List extends BaseAWSEC2AllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Listing") ;

    public function askWhetherToListData($params=null) {
        return $this->performAWSEC2ListData($params);
    }

    protected function performAWSEC2ListData($params=null){
        if ($this->askForListExecute() != true) { return false; }
        $this->initialiseAWS();
        $dataToList = $this->askForDataTypeToList();
        return $this->getDataListFromAWSEC2($dataToList);
    }

    protected function askForListExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'List Data?';
        return self::askYesOrNo($question);
    }

    protected function getClient() {
        $this->setProjVars() ;
        $c = array(
            'key'    => $this->accessKey,
            'secret' => $this->secretKey,
            'region' => $this->region ) ;
        $this->awsClient = \Aws\Ec2\Ec2Client::factory($c);
    }

    protected function askForDataTypeToList(){
        $question = 'Please choose a data type to list:';
        $options = array("droplets", "sizes", "images", "domains", "regions", "ssh_keys");
        if (isset($this->params["list-type"]) &&
            in_array($this->params["list-type"], $options)) {
            return $this->params["list-type"] ; }
        return self::askForArrayOption($question, $options, true);
    }

    public function getDataListFromAWSEC2($dataToList){
        $calls = array(
            "images" => "DescribeImages",
        ) ;
        $method = $calls[$dataToList] ;
        $list = $this->awsClient->$method()  ;


        $iterator = $this->awsClient->getIterator($method, array( ), array(
            'limit'     => 10,
            'page_size' => 10
        ));


        return $iterator ;
    }

}