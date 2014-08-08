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

    public function __construct($params = array()) {
        parent::__construct($params) ;
    }

    public function askWhetherToSaveSshKey($params=null) {
        return $this->performRackspaceSaveSshKey($params);
    }

    public function performRackspaceSaveSshKey($params=null) {
        if ($this->askForSSHKeyExecute() != true) { return false; }
        $this->apiKey = $this->askForRackspaceAPIKey();
        $this->username = $this->askForRackspaceUsername();
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
        $callVars = array();
        $keyData = str_replace("\n", "", $keyData);
        $callVars["ssh_pub_key"] = urlencode($keyData);
        $callVars["name"] = $keyName;
        // $curlUrl = "https://api.rackspace.com/v1/ssh_keys/new" ;
        // return $this->rackspaceCall($callVars, $curlUrl);

        // 2. Create Compute service object
        $region = 'ORD';
        $service = $client->computeService(null, $region);

        // 3. Get empty keypair
        $keypair = $service->keypair();

        $payload =

        // 4. Create
        $keypair->create(array(
            'name'      => 'new_public_key',
            'publicKey' => $payload
        ));

        // @todo
        return true ;

    }

}