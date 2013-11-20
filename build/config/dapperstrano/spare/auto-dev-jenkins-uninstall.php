<?php

/*************************************
*      Generated Autopilot file      *
*     ---------------------------    *
*Autopilot Generated By Dapperstrano *
*     ---------------------------    *
*************************************/

Namespace Core ;

class AutoPilotConfigured extends AutoPilot {

    public $steps ;

    public function __construct() {
	      $this->setSteps();
    }

    /* Steps */
    private function setSteps() {

	    $this->steps =
	      array(
          array ( "HostEditor" => array(
                    "hostEditorDeletionExecute" => true,
                    "hostEditorDeletionIP" => "127.0.0.1",
                    "hostEditorDeletionURI" => "bluevip.dev-jenkins.tld",
          ) , ) ,
          array ( "VHostEditor" => array(
                    "virtualHostEditorDeletionExecute" => true,
                    "virtualHostEditorDeletionDirectory" => "/etc/apache2/sites-available",
                    "virtualHostEditorDeletionTarget" => "bluevip.dev-jenkins.tld",
                    "virtualHostEditorDeletionVHostDisable" => true,
                    "virtualHostEditorDeletionSymLinkDirectory" => "/etc/apache2/sites-enabled",
                    "virtualHostEditorDeletionApacheCommand" => "httpd",
          ) , ) ,
          array ( "DBConfigure" => array(
                    "dbResetExecute" => true,
                    "dbResetPlatform" => "joomla30",
          ) , ) ,
          array ( "DBInstall" => array(
                    "dbDropExecute" => true,
                    "dbDropDBHost" => "127.0.0.1",
                    "dbDropDBName" => "bvip_ts_db",
                    "dbDropDBRootUser" => "gcTestAdmin",
                    "dbDropDBRootPass" => "gcTest1234",
                    "dbDropUserExecute" => true,
                    "dbDropDBUser" => "bvip_ts_user",
          ) , ) ,
          array ( "CukeConf" => array(
                    "cukeConfDeletionExecute" => true,
          ) , ) ,
	      );

	  }

}