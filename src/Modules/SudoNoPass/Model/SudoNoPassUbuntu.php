<?php

Namespace Model;

class SudoNoPassUbuntu extends BaseLinuxApp {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("11.04", "11.10", "12.04", "12.10", "13.04") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Installer") ;

  public function __construct($params) {
    parent::__construct($params);
    $this->autopilotDefiner = "SudoNoPass";
    $this->installCommands = array();
    $this->uninstallCommands = array( "" );
    $this->programDataFolder = "";
    $this->programNameMachine = "sudonopass"; // command and app dir name
    $this->programNameFriendly = "Sudo NoPass!"; // 12 chars
    $this->programNameInstaller = "Sudo w/o Pass for User";
    $this->registeredPreInstallFunctions = array("askForInstallUserName",
        "setInstallCommandsWithNewUserName");
    $this->registeredPreUnInstallFunctions = array("askForInstallUserName");
    $this->initialize();
  }

  protected function setInstallCommandsWithNewUserName() {
      $this->installCommands = array(
        'echo "The following will be written to /etc/sudoers" ',
        'echo "Please check if it looks wrong" ',
        'echo "It may break your system if wrong !!!" ',
        'echo "'.$this->installUserName.' ALL=NOPASSWD: ALL" ',
        'echo "'.$this->installUserName.' ALL=NOPASSWD: ALL" >> /etc/sudoers '
    );
  }

}