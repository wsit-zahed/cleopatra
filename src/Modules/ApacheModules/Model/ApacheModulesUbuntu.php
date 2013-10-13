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
    public $modelGroup = array("Installer") ;

  public function __construct($params) {
    parent::__construct($params);
    $this->autopilotDefiner = "ApacheModules";
    $this->installCommands = array(
        "apt-get clean", "apt-get update",
        "apt-get install -y libxml2-dev",
        "a2enmod rewrite",
        "a2enmod deflate",
        "a2enmod ssl",
        "apt-get install -y libapache2-mod-proxy-html",
        "a2enmod proxy",
        "a2enmod proxy_http",
        "apt-get install -y libapache2-mod-php5",
        "a2enmod php5",
        "service apache2 restart" );
    $this->uninstallCommands = array(
        "apt-get clean", "apt-get update",
        "apt-get remove -y libxml2-dev",
        "a2dismod rewrite",
        "a2dismod deflate",
        "a2dismod ssl",
        "a2dismod proxy",
        "a2dismod proxy_http",
        "apt-get remove -y libapache2-mod-proxy-html",
        "a2dismod php5",
        "apt-get remove -y libapache2-mod-php5",
        "service apache2 restart" );
    $this->programDataFolder = "/opt/ApacheModules"; // command and app dir name
    $this->programNameMachine = "apachemodules"; // command and app dir name
    $this->programNameFriendly = "Apache Mods!"; // 12 chars
    $this->programNameInstaller = "Apache Modules";
    $this->initialize();
  }

}