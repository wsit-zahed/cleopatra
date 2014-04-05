<?php

Namespace Info;

class PackageManagerInfo extends CleopatraBase {

  public $hidden = false;

  public $name = "Native Package Manager Wrapper - Install OS neutral packages";

  public function __construct() {
    parent::__construct();
  }

  public function routesAvailable() {
    return array( "PackageManager" =>  array_merge(parent::routesAvailable(), array("pkg-ensure", "pkg-install", "pkg-remove") ) );
  }

  public function routeAliases() {
    return array("package-manager"=>"PackageManager", "packagemanager"=>"PackageManager", "package-mgr"=>"PackageManager",
        "pkgmgr"=>"PackageManager");
  }

  public function autoPilotVariables() {
    return array(
      "PackageManager" => array(
        "PackageManager" => array(
          "programDataFolder" => "/opt/PackageManager", // command and app dir name
          "programNameMachine" => "packagemanager", // command and app dir name
          "programNameFriendly" => "Package Mgr.", // 12 chars
          "programNameInstaller" => "Native Package Manager Wrapper",
        ),
      )
    );
  }

  public function helpDefinition() {
    $help = <<<"HELPDATA"
  This command allows you to use a Package Management wrapper.

  PackageManager, package-manager, packagemanager, package-mgr, pkgmgr

        - pkg-install
        Installs a Package through a Package Manager
        example: cleopatra package-manager install --package-name="mysql" --package-version="5.0" --packager="apt-get"

        - pkg-ensure
        Installs a Package through a Package Manager
        example: cleopatra package-manager install --package-name="mysql" --package-version="5.0" --packager="apt-get"

        - pkg-remove
        Removes a Package through a Package Manager
        example: cleopatra package-manager install --package-name="mysql" --package-version="5.0" --packager="apt-get"

  A package manager wrapper that will allow you to install packages on any system

HELPDATA;
    return $help ;
  }

}