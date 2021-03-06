<?php

Namespace Info;

class AWSCloudWatchInfo extends CleopatraBase {

  public $hidden = false;

  public $name = "The AWS CloudWatch CLI Tools";

  public function __construct() {
    parent::__construct();
  }

  public function routesAvailable() {
    return array( "AWSCloudWatch" =>  array_merge(parent::routesAvailable(), array("install") ) );
  }

  public function routeAliases() {
    return array("awscloudwatch"=>"AWSCloudWatch", "aws-cloud-watch"=>"AWSCloudWatch", "aws-cloudwatch"=>"AWSCloudWatch");
  }

  public function helpDefinition() {
    $help = <<<"HELPDATA"
  This command allows you to install a few GC recommended Standard Tools
  for productivity in your system.  The kinds of tools we found ourselves
  installing on every box we have, client or server. These include curl,
  vim, drush and zip.

  AWSCloudWatch, aws-cloud-watch, aws-cloudwatch

        - install
        Installs AWSCloudWatch. Note, you'll also need Java installed
        as it is a prerequisite for AWSCloudWatch
        example: cleopatra aws-cloudwatch install

HELPDATA;
    return $help ;
  }

}