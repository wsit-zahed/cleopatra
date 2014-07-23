<?php

Namespace Info;

class RaInfo extends CleopatraBase {

    public $hidden = false;

    public $name = "Ra - The Pharoah Tools Build Server";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "Ra" =>  array_merge(parent::routesAvailable(), array("install", "ensure", "uninstall") ) );
    }

    public function routeAliases() {
      return array("ra"=>"Ra");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This command allows you to install or update Ra.

  Ra, ra

        - install
        Installs the latest version of ra
        example: cleopatra ra install

        - ensure
        Ensures the latest version of ra
        example: cleopatra ra ensure

        - uninstall
        Installs the latest version of ra
        example: cleopatra ra uninstall

HELPDATA;
      return $help ;
    }

}