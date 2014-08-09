<?php

Namespace Model;

class RackspaceSshKey extends BaseRackspaceAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("SshKey") ;

//    public function __construct($params = array()) {
//        parent::__construct($params) ;
//    }

    public function askWhetherToSaveSshKey() {
        return $this->performRackspaceSaveSshKey();
    }

    public function performRackspaceSaveSshKey() {
        var_dump($this->params) ;
        if ($this->askForSSHKeyExecute() != true) { return false; }
        $this->username = $this->askForRackspaceUsername();
        $this->apiKey = $this->askForRackspaceAPIKey();
        $this->getClient();
        $fileLocation = $this->askForSSHKeyPublicFileLocation();
        $fileData = file_get_contents($fileLocation);
        $keyName = $this->askForSSHKeyNameForRackspace();
        return $this->saveSshKeyToRackspace($fileData, $keyName);
    }

    private function askForSSHKeyExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Save local SSH Public Key file to Rackspace?';
        return self::askYesOrNo($question);
    }

    private function askForSSHKeyPublicFileLocation() {
        if (isset($this->params["rackspace-ssh-key-path"]) && $this->params["rackspace-ssh-key-path"]==true) {
            return $this->params["rackspace-ssh-key-path"] ; }
        $question = 'Enter Location of ssh public key file to upload';
        return self::askForInput($question, true);
    }

    private function askForSSHKeyNameForRackspace(){
        if (isset($this->params["rackspace-ssh-key-name"]) && $this->params["rackspace-ssh-key-name"]==true) {
            return $this->params["rackspace-ssh-key-name"] ; }
        $question = 'Enter name to store ssh key under on Rackspace';
        return self::askForInput($question, true);
    }

    public function saveSshKeyToRackspace($keyData, $keyName){
        $keyData = str_replace("\n", "", $keyData);
        // 2. Create Compute service object
        $region = 'ORD';
        $service = $this->rackspaceClient->computeService(null, $region);
        // 3. Get empty keypair
        $keypair = $service->keypair();
        // 4. Create
        $keypair->create(array(
            'name'      => $keyName,
            'publicKey' => $keyData
        ));
        // @todo check if it actually worked
        return true ;
    }

}