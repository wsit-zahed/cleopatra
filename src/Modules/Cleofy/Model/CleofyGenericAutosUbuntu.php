<?php

Namespace Model;

class CleofyGenericAutosUbuntu extends BaseLinuxApp {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("GenericAutos") ;

    protected $templateGroup;
    protected $templateGroupsToDirs;
    protected $destination;

    protected $actionsToMethods =
        array(
            "install-generic-autopilots" => "performGenericAutopilotInstall",
        ) ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "Cleofy";
        $this->programNameMachine = "cleofy"; // command and app dir name
        $this->programNameFriendly = "Cleofy!"; // 12 chars
        $this->programNameInstaller = "Cleofy your Environments";
        $this->initialize();
    }

    public function performGenericAutopilotInstall() {
        $this->setTemplateGroupsToDirs();
        $this->setTemplateGroup();
        $this->setDestination();
        return $this->doCopy() ;
    }

    public function setTemplateGroupsToDirs() {
        $dir = str_replace("Model", "", __DIR__) ;
        $dir = $dir.'Templates/' ;
        $this->templateGroupsToDirs = array(
            "tiny" => "{$dir}Generic/Tiny",
            "medium" => "{$dir}Generic/Medium",
            "dbcluster" => "{$dir}Generic/DBCluster",
            "db-cluster" => "{$dir}Generic/DBCluster"
        );
    }

    public function setTemplateGroup($templateGroup = null) {
        // @this should log that you  have specified an invalid template group if that is the case and go to prompt
        if (isset($templateGroup)) {
            $this->templateGroup = $templateGroup; }
        else if (isset($this->params["templategroup"])) {
            $this->templateGroup = $this->params["templategroup"]; }
        else if (isset($this->params["template-group"])) {
            $this->templateGroup = $this->params["template-group"]; }
        else {
            $this->templateGroup = self::askForArrayOption("Enter Template Group:",array("tiny", "medium"), true) ; }
    }

    public function setDestination($destination = null) {
        // @this should log that you have specified an invalid destination if that is the case and go to prompt
        if (isset($destination)) {
            $this->destination = $destination; }
        else if (isset($this->params["destinationdir"])) {
            $this->destination = $this->params["destinationdir"]; }
        else if (isset($this->params["destination-dir"])) {
            $this->destination = $this->params["destination-dir"]; }
        else if (isset($this->params["guess"])) {
            $defaultdir = getcwd()."/build/config/cleopatra/cleofy/autopilots/generic/" ;
            if (!file_exists($defaultdir)) { mkdir($defaultdir, 0777, true) ; }  ;
            $this->destination = $defaultdir ; }
        else {
            $this->destination = self::askForInput("Enter Destination Directory:", true); }
    }

    protected function doCopy() {
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $source = $this->templateGroupsToDirs[$this->templateGroup] ;
        $target = $this->destination ;
        $logging->log("Performing file copy from $source to $target") ;
        // @todo php cannot do a recursive copy so change the copy module to one of these
        $result = $this->executeAndGetReturnCode("cp -r $source $target") ;
        return $result ;
    }

}