<?php

Namespace Info;

class AWSCloudFormationInfo extends Base {

  public $hidden = false;

  public $name = "The AWS CloudFormation CLI Tools";

  public function __construct() {
    parent::__construct();
  }

  public function routesAvailable() {
    return array( "AWSCloudFormation" =>  array_merge(parent::routesAvailable(), array("install") ) );
  }

  public function routeAliases() {
    return array("awscloudformation"=>"AWSCloudFormation", "aws-cloud-formation"=>"AWSCloudFormation", "aws-cloudformation"=>"AWSCloudFormation");
  }

  public function autoPilotVariables() {
    return array(
      "AWSCloudFormation" => array(
        "AWSCloudFormation" => array(
          "programDataFolder" => "/opt/AWSCloudFormation", // command and app dir name
          "programNameMachine" => "seleniumserver", // command and app dir name
          "programNameFriendly" => "AWSCloudFormation Srv", // 12 chars
          "programNameInstaller" => "AWSCloudFormation Server",
        ),
      )
    );
  }

  public function helpDefinition() {
    $help = <<<"HELPDATA"
  This command allows you to install a few GC recommended Standard Tools
  for productivity in your system.  The kinds of tools we found ourselves
  installing on every box we have, client or server. These include curl,
  vim, drush and zip.

  AWSCloudFormation, selenium-server, selenium, selenium-srv, seleniumserver

        - install
        Installs AWSCloudFormation. Note, you'll also need Java installed
        as it is a prerequisite for AWSCloudFormation
        example: cleopatra selenium install

HELPDATA;
    return $help ;
  }

}