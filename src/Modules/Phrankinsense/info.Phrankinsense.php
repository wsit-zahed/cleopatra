<?php

Namespace Info;

class PhrankinsenseInfo extends CleopatraBase {

    public $hidden = false;

    public $name = "Phrankinsense - The Pharaoh Tools Project Management Solution";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "Phrankinsense" =>  array_merge(parent::routesAvailable(), array("install", "ensure", "uninstall") ) );
    }

    public function routeAliases() {
      return array("phrankinsense"=>"Phrankinsense");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This command allows you to install or update Phrankinsense.

  Phrankinsense, phrankinsense

        - install
        Installs the latest version of phrankinsense
        example: cleopatra phrankinsense install

        - ensure
        Ensures phrankinsense is installed
        example: cleopatra phrankinsense ensure

        - uninstall
        Uninstalls the latest version of phrankinsense
        example: cleopatra phrankinsense uninstall
HELPDATA;
      return $help ;
    }

}