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
              array ( "Git" => array(
                  "gitDeletorExecute" => true,
                  "gitDeletorCustomFolder" => "/var/www/jenkins/alca-enterprise/"
              ), ) ,
              array ( "HostEditor" => array(
                  "hostEditorDeletionExecute" => true,
                  "hostEditorDeletionIP" => "127.0.0.1",
                  "hostEditorDeletionURI" => "www.alca-enterprise.local-jenkins.tld.local",
              ) , ) ,
              array ( "VHostEditor" => array(
                  "virtualHostEditorDeletionExecute" => "boolean",
                  "virtualHostEditorDeletionDirectory" => "/etc/apache2/sites-available",
                  "virtualHostEditorDeletionTarget" => "www.alca-enterprise.local-jenkins.tld",
                  "virtualHostEditorDeletionVHostDisable" => false,
                  "virtualHostEditorDeletionSymLinkDirectory" => "/etc/apache2/sites-enabled",
                  "virtualHostEditorDeletionApacheCommand" => "apache2",
              ) , ) ,
              array ( "ApacheControl" => array(
                  "apacheCtlRestartExecute" => true,
              ) , ) ,
	      );

	  }

}
