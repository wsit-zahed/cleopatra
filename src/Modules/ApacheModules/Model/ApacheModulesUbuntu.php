<?php

Namespace Model;

class ApacheModulesUbuntu extends BaseLinuxApp {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("12.04", "12.10") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "ApacheModules";
        $this->installCommands = array(
            array("method"=> array("object" => $this, "method" => "packageAdd", "params" => array("Apt", "libxml2-dev")) ),
            array("command"=> "a2enmod rewrite" ),
            array("command"=> "a2enmod deflate" ),
            array("command"=> "a2enmod ssl" ),
            array("method"=> array("object" => $this, "method" => "packageAdd", "params" => array("Apt", "libapache2-mod-php5")) ),
            array("command"=> "a2enmod php5" ),
            array("method"=> array("object" => $this, "method" => "apacheRestart", "params" => array())) );
        $this->uninstallCommands = array(
            array("method"=> array("object" => $this, "method" => "packageRemove", "params" => array("Apt", "libxml2-dev")) ),
            array("command"=> "a2dismod rewrite" ),
            array("command"=> "a2dismod deflate" ),
            array("command"=> "a2dismod ssl" ),
            array("method"=> array("object" => $this, "method" => "packageRemove", "params" => array("Apt", "libapache2-mod-php5")) ),
            array("command"=> "a2dismod php5" ),
            array("method"=> array("object" => $this, "method" => "apacheRestart", "params" => array())) );
        $this->programDataFolder = "/opt/ApacheModules"; // command and app dir name
        $this->programNameMachine = "apachemodules"; // command and app dir name
        $this->programNameFriendly = "Apache Mods!"; // 12 chars
        $this->programNameInstaller = "Apache Modules";
        $this->initialize();
    }

    public function askStatus() {
        $modsTextCmd = 'apachectl -t -D DUMP_MODULES';
        $modsText = $this->executeAndLoad($modsTextCmd) ;
        $modsToCheck = array("deflate_module", "php5_module", "rewrite_module", "ssl_module" ) ;
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $passing = true ;
        foreach ($modsToCheck as $modToCheck) {
            if (!strstr($modsText, $modToCheck)) {
                $logging->log("Apache Module {$modToCheck} does not exist.") ;
                $passing = false ; } }
        return $passing ;
    }

    public function apacheRestart() {
        $serviceFactory = new Service();
        $serviceManager = $serviceFactory->getModel($this->params) ;
        $serviceManager->setService("apache2");
        $serviceManager->restart();
    }

}