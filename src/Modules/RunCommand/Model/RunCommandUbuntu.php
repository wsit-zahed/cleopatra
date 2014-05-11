<?php

Namespace Model;

class RunCommandUbuntu extends BaseLinuxApp {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("11.04", "11.10", "12.04", "12.10", "13.04") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    protected $runUser ;
    protected $command ;
    protected $background ;


    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "RunCommand";
        $this->installCommands = array(
            array("method"=> array("object" => $this, "method" => "askForUserName", "params" => array() ) ) ,
            array("method"=> array("object" => $this, "method" => "askForCommand", "params" => array() ) ) ,
            array("method"=> array("object" => $this, "method" => "askForBackground", "params" => array() ) ) ,
            array("method"=> array("object" => $this, "method" => "runCommand", "params" => array()) ),
        );
        $this->uninstallCommands = array();
        $this->programDataFolder = "";
        $this->programNameMachine = "runcommand"; // command and app dir name
        $this->programNameFriendly = "Run Command"; // 12 chars
        $this->programNameInstaller = "Run a Command";
        $this->initialize();
    }

    protected function runCommand() {
        $commandRay = array() ;
        $commandRay[] = "cd ".getcwd() ;
        if (isset($this->runUser) && !is_null($this->runUser))  {
            $commandRay[] = "su  ".$this->runUser ; }
        if (isset($this->background) && !is_null($this->background))  {
            $commandRay[] = $this->command.' &' ; }
        else  {
            $commandRay[] = $this->command ; }
        if (isset($this->runUser) && !is_null($this->runUser))  {
            $commandRay[] = "exit" ; }
        $this->executeAsShell($commandRay) ;
    }

    public function askForUserName() {
        if (isset($this->params["run-as-user"]) && strlen($this->params["run-as-user"])>0) {
            $this->runUser = $this->params["run-as-user"] ; }
        else if (isset($this->params["run-as-user"]) && strlen($this->params["run-as-user"])==0) {
            $this->runUser = null ; }
        else {
            $this->runUser = null ; }
    }

    public function askForCommand() {
        $question = "Enter Command to run:";
        $this->command = (isset($this->params["command"])) ? $this->params["command"] : self::askForInput($question);
    }

    public function askForBackground() {
        if (isset($this->params["background"]) && strlen($this->params["background"])>0) {
            $this->background = true ; }
        else if (isset($this->params["background"]) && $this->params["background"]===true) {
            $this->background = true ; }
        else if (isset($this->params["background"]) && strlen($this->params["background"])==0) {
            $this->background = null ; }
        else {
            $this->background = null; }
    }

}