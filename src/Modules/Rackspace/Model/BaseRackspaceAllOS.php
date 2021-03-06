<?php

Namespace Model;

// use OpenCloud\Rackspace;
use Guzzle\Http\Exception\BadResponseException;

class BaseRackspaceAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Base") ;

    protected $username ;
    protected $apiKey ;
    protected $region ;
    protected $rackspaceClient;

    protected function initialiseRackspace() {
        $this->apiKey = $this->askForRackspaceAPIKey();
        $this->username = $this->askForRackspaceUsername();
        $this->region = $this->askForRackspaceRegion();
        $this->getClient();
    }

    protected function askForRackspaceAPIKey(){
        if (isset($this->params["rackspace-api-key"])) { return $this->params["rackspace-api-key"] ; }
        $papyrusVar = \Model\AppConfig::getProjectVariable("rackspace-api-key") ;
        if ($papyrusVar != null) {
            if (isset($this->params["guess"])) {
                return $papyrusVar ;
            }
            if (isset($this->params["use-project-api-key"]) && $this->params["use-project-api-key"] == true) {
                return $papyrusVar ;
            }
            $question = 'Use Project saved Rackspace API Key?';
            if (self::askYesOrNo($question, true) == true) {
                return $papyrusVar ;
            }
        }
        $appVar = \Model\AppConfig::getProjectVariable("rackspace-api-key") ;
        if ($appVar != null) {
            $question = 'Use Application saved Rackspace API Key?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ;
            }
        }
        $question = 'Enter Rackspace API Key';
        return self::askForInput($question, true);
    }

    protected function askForRackspaceUsername(){
        if (isset($this->params["rackspace-user-name"])) { return $this->params["rackspace-user-name"] ; }
        $papyrusVar = \Model\AppConfig::getProjectVariable("rackspace-user-name") ;
        if ($papyrusVar != null) {
            if ($this->params["guess"] == true) { return $papyrusVar ; }
            if ($this->params["use-project-user-name"] == true) { return $papyrusVar ; }
            $question = 'Use Project saved Rackspace User Name?';
            if (self::askYesOrNo($question, true) == true) {
                return $papyrusVar ;
            }
        }
        $appVar = \Model\AppConfig::getProjectVariable("rackspace-user-name") ;
        if ($appVar != null) {
            $question = 'Use Application saved Rackspace User Name?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ;
            }
        }
        $question = 'Enter Rackspace User Name';
        return self::askForInput($question, true);
    }

    protected function askForRackspaceRegion(){
        if (isset($this->params["rackspace-region"])) { return $this->params["rackspace-region"] ; }
        $papyrusVar = \Model\AppConfig::getProjectVariable("rackspace-region") ;
        if ($papyrusVar != null) {
            if ($this->params["guess"] == true) { return $papyrusVar ; }
            if ($this->params["use-project-region"] == true) { return $papyrusVar ; }
            $question = 'Use Project saved Rackspace Region?';
            if (self::askYesOrNo($question, true) == true) {
                return $papyrusVar ; } }
        $appVar = \Model\AppConfig::getProjectVariable("rackspace-region") ;
        if ($appVar != null) {
            $question = 'Use Application saved Rackspace Region?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ;
            }
        }
        $question = 'Enter Rackspace Region';
        return self::askForInput($question, true);
    }

    protected function getClient() {
        require dirname(__DIR__) . '/Libraries/vendor/autoload.php';
        // 1. Instantiate a Rackspace client.
        \Model\AppConfig::setProjectVariable("rackspace-user-name", $this->username) ;
        \Model\AppConfig::setProjectVariable("rackspace-api-key", $this->apiKey) ;
        \Model\AppConfig::setProjectVariable("rackspace-region", $this->region) ;
        $this->rackspaceClient = new \OpenCloud\Rackspace(\OpenCloud\Rackspace::UK_IDENTITY_ENDPOINT, array(
            'username' => $this->username,
            'apiKey'   => $this->apiKey
        ));
    }

    protected function getServerGroupRegionID() {
        if (isset($this->params["region-id"])) {
            return $this->params["region-id"] ;
        }
        if (isset($this->params["guess"])) {
            return $this->region ;
        }
        $question = 'Enter Region ID for this Server Group';
        return self::askForInput($question, true);
    }

}