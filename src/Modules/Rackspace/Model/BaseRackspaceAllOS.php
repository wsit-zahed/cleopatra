<?php

Namespace Model;

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
    protected $rackspaceClient ;

    public function __construct($params) {
    }

    protected function askForRackspaceAPIKey(){
        if (isset($this->params["rackspace-api-key"])) { return $this->params["rackspace-api-key"] ; }
        $papyrusVar = \Model\AppConfig::getProjectVariable("rackspace-api-key") ;
        if ($papyrusVar != null) {
            if (isset($this->params["guess"])) {
                return $papyrusVar ; }
            if (isset($this->params["use-project-api-key"]) && $this->params["use-project-api-key"] == true) {
                return $papyrusVar ; }
            $question = 'Use Project saved Rackspace API Key?';
            if (self::askYesOrNo($question, true) == true) { return $papyrusVar ; } }
        $appVar = \Model\AppConfig::getProjectVariable("rackspace-api-key") ;
        if ($appVar != null) {
            $question = 'Use Application saved Rackspace API Key?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ; } }
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
                return $papyrusVar ; } }
        $appVar = \Model\AppConfig::getProjectVariable("rackspace-user-name") ;
        if ($appVar != null) {
            $question = 'Use Application saved Rackspace User Name?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ; } }
        $question = 'Enter Rackspace User Name';
        return self::askForInput($question, true);
    }

    protected function getClient() {


        require __DIR__ . '/../../vendor/autoload.php';

    use OpenCloud\Rackspace;
    use Guzzle\Http\Exception\BadResponseException;

// 1. Instantiate a Rackspace client.
        $this->rackspaceClient = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
            'username' => getenv('RAX_USERNAME'),
            'apiKey'   => getenv('RAX_API_KEY')
        ));
    }

}