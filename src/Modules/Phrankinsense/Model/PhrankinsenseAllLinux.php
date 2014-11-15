<?php

Namespace Model;

class PhrankinsenseAllLinux extends BasePHPApp {

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
        $this->autopilotDefiner = "Phrankinsense";
        $this->fileSources = array(
          array(
            "https://github.com/PharaohTools/phrankinsense.git",
            "phrankinsense",
            null // can be null for none
          )
        );
        $this->programNameMachine = "phrankinsense"; // command and app dir name
        $this->programNameFriendly = " Phrankinsense "; // 12 chars
        $this->programNameInstaller = "Phrankinsense";
        $this->programExecutorTargetPath = 'phrankinsense/src/Bootstrap.php';
        $this->initialize();
    }

}