<?php

Namespace Model;

class BehatAllLinux extends BaseComposerApp {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "Behat";
        $this->programNameMachine = "behat"; // command and app dir name
        $this->programNameFriendly = " Behat "; // 12 chars
        $this->programNameInstaller = "Behat";
        $this->programExecutorTargetPath = 'behat/bin/behat';
        $this->initialize();
    }

    protected function copyComposerJsonToProgramDataFolder($originalTemplate) {
        $originalTemplate = __DIR__."/../Templates/composer.json" ;
        parent::copyComposerJsonToProgramDataFolder($originalTemplate);
    }

}