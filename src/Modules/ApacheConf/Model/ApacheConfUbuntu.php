<?php

Namespace Model;

class ApacheConfUbuntu extends BaseTemplater {

  // Compatibility
  public $os = array("Linux") ;
  public $linuxType = array("Debian") ;
  public $distros = array("Ubuntu") ;
  public $versions = array("12.04", "12.10") ;
  public $architectures = array("any") ;

  // Model Group
  public $modelGroup = array("Installer") ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "ApacheConf";
        $this->installCommands = array();
        $this->uninstallCommands = array();
        $this->programDataFolder = "/opt/ApacheConf"; // command and app dir name
        $this->programNameMachine = "apacheconf"; // command and app dir name
        $this->programNameFriendly = "Apache Conf!"; // 12 chars
        $this->programNameInstaller = "Apache Conf";
        $this->targetLocation = "/etc/apache2/apache2.conf" ;
        $this->registeredPreInstallFunctions = array("setDefaultReplacements", "setOverrideReplacements", "setTemplateFile", "setTemplate") ;
        $this->initialize();
    }

    protected function setDefaultReplacements() {
        // set array with default values
        $this->replacements = array(
            "LockFile" => '${APACHE_LOCK_DIR}/accept.lock',
            "PidFile" => '${APACHE_PID_FILE}',
            "Timeout" => '300',
            "KeepAlive" => 'On',
            "MaxKeepAliveRequests" => '100',
            "KeepAliveTimeout" => '5',
        ) ;
    }

    protected function setTemplateFile() {
        $this->templateFile = str_replace("Model", "Templates", dirname(__FILE__) ) ;
        $this->templateFile .= DIRECTORY_SEPARATOR."apache2.conf" ;
    }

}