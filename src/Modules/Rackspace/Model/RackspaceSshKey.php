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
        if ($this->askForSSHKeyExecute() != true) { return false; }
        $this->initialiseRackspace();
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
        if (isset($this->params["key-path"]) && $this->params["key-path"]==true) {
            return $this->params["key-path"] ; }
        $question = 'Enter Location of ssh public key file to upload';
        return self::askForInput($question, true);
    }

    private function askForSSHKeyNameForRackspace(){
        if (isset($this->params["key-name"]) && $this->params["key-name"]==true) {
            return $this->params["key-name"] ; }
        $question = 'Enter name to store ssh key under on Rackspace';
        return self::askForInput($question, true);
    }

    public function saveSshKeyToRackspace($keyData, $keyName){
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $service = $this->rackspaceClient->computeService(null, $this->getServerGroupRegionID());
        // 3. Get empty keypair
        $keypair = $service->keypair();
        // 4. Create
        try {
            $keypair->create(array(
                'name'      => $keyName,
                'publicKey' => $keyData
            ));
            // @todo check if it actually worked
            $logging->log("Request for Key $keyName complete") ;
            return true ; }
        catch (\Guzzle\Http\Exception\BadResponseException $e) {
            // No! Something failed. Let's find out:
            $responseBody = (string) $e->getResponse()->getBody();
            $statusCode   = $e->getResponse()->getStatusCode();
            $headers      = $e->getResponse()->getHeaderLines();
            $logging->log(sprintf("Status: %s\nBody: %s\nHeaders: %s", $statusCode, $responseBody, implode(', ', $headers))) ;
            return null ; }
    }

}