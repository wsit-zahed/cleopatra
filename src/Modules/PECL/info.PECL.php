<?php

Namespace Info;

class PECLInfo extends CleopatraBase {

    public $hidden = false;

    public $name = "Add, Remove or Modify PECLs";

    public function __construct() {
      parent::__construct();
    }

    public function routesAvailable() {
        // return array( "PECL" =>  array_merge(parent::routesAvailable(), array() ) );
        return array( "PECL" =>  array_merge(
            array("help", "status", "pkg-install", "pkg-ensure", "pkg-remove", "update")
        ) );
    }

    public function routeAliases() {
        return array("apt"=>"PECL");
    }

    public function packagerName() {
        return "PECL";
    }

    public function helpDefinition() {
      $help = <<<"HELPDATA"
  This command allows you to modify create or modify apts

  PECL, apt

        - create
        Create a new system apt, overwriting if it exists
        example: cleopatra apt create --aptname="somename"

        - remove
        Remove a system apt
        example: cleopatra apt remove --aptname="somename"

        - set-password
        Set the password of a system apt
        example: cleopatra apt set-password --aptname="somename" --new-password="somepassword"

        - exists
        Check the existence of a apt
        example: cleopatra apt exists --aptname="somename"

        - show-groups
        Show groups to which a apt belongs
        example: cleopatra apt show-groups --aptname="somename"

        - add-to-group
        Add apt to a group
        example: cleopatra apt add-to-group --aptname="somename" --groupname="somegroupname"

        - remove-from-group
        Remove apt from a group
        example: cleopatra apt remove-from-group --aptname="somename" --groupname="somegroupname"

HELPDATA;
      return $help ;
    }

}