<?php

Namespace Info;

class PythonInfo extends CleopatraBase {

    public $hidden = false;

    public $name = "Python - The programming language";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "Python" =>  array_merge(parent::routesAvailable(), array() ) );
    }

    public function routeAliases() {
      return array("python"=>"Python");
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This command allows you to install the latest available Python in the Ubuntu
  repositories.

  Python, python

        - install
        Installs the latest version of Python
        example: cleopatra python install

HELPDATA;
      return $help ;
    }

}