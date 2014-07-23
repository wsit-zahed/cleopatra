<?php

Namespace Model;

class RaAllLinux extends BasePHPApp {

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
        $this->autopilotDefiner = "Ra";
        $this->fileSources = array(
          array(
            "http://git.pharoah-tools.org.uk/git/phpengine/ra.git",
            "ra",
            null // can be null for none
          )
        );
        $this->programNameMachine = "ra"; // command and app dir name
        $this->programNameFriendly = " Ra "; // 12 chars
        $this->programNameInstaller = "Ra";
        $this->programExecutorTargetPath = 'ra/src/Bootstrap.php';
        $this->initialize();
    }

}