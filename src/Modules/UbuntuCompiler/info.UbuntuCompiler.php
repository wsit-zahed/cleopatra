<?php

Namespace Info;

class UbuntuCompilerInfo extends Base {

  public $hidden = false;

  public $name = "Ubuntu Compiler - For Compiling Linux Programs";

  public function __construct() {
    parent::__construct();
  }

  public function routesAvailable() {
    return array( "UbuntuCompiler" =>  array_merge(parent::routesAvailable(), array("install") ) );
  }

  public function routeAliases() {
    return array("ubuntu-compiler"=>"UbuntuCompiler", "ubuntucompiler"=>"UbuntuCompiler");
  }

  public function autoPilotVariables() {
    return array(
      "UbuntuCompiler" => array(
        "UbuntuCompiler" => array(
          "programDataFolder" => "/opt/UbuntuCompiler", // command and app dir name
          "programNameMachine" => "ubuntucompiler", // command and app dir name
          "programNameFriendly" => "UbuntuCompiler", // 12 chars
          "programNameInstaller" => "UbuntuCompiler",
        ),
      )
    );
  }

  public function helpDefinition() {
    $help = <<<"HELPDATA"
  This command allows you to install Node JS, The Server Side JS Language

  UbuntuCompiler, ubuntu-compiler, ubuntucompiler

        - install
        Installs Ubuntu Compiling tools through apt-get.
        example: cleopatra ubuntu-compiler install

HELPDATA;
    return $help ;
  }

}