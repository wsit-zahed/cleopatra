<?php

Namespace Model;

class DapperstranoAllLinux extends BasePHPApp {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian", "Redhat") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Installer") ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "Dapperstrano";
        $this->fileSources = array(
          array(
            "https://github.com/phpengine/dapperstrano.git",
            "dapperstrano",
            null // can be null for none
          )
        );
        $this->programNameMachine = "dapperstrano"; // command and app dir name
        $this->programNameFriendly = " Dapperstrano "; // 12 chars
        $this->programNameInstaller = "Dapperstrano";
        $this->programExecutorTargetPath = 'dapperstrano/src/Bootstrap.php';
        $this->initialize();
    }

}