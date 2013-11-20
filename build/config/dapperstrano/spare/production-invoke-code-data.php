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
  public $swaps ;

  public function __construct() {
    $this->setSteps();
    $this->setSSHData();
  }

  /* Steps */
  private function setSteps() {

    $this->steps =
      array(
        array ( "InvokeSSH" => array(
          "sshInvokeSSHDataExecute" => true,
          "sshInvokeSSHDataData" => "",
          "sshInvokeServers" => array(
              array("target" => "178.63.72.156", "user" => "gcdeployman", "pword" => "turbulentDeploy1995", ),

            ),
        ) , ) ,
        );

    }


//
// This function will set the sshInvokeSSHDataData variable with the data that
// you need in it. Call this in your constructor
//
  private function setSSHData() {
    $timeDrop = time();
    $this->steps[0]["InvokeSSH"]["sshInvokeSSHDataData"] = <<<"SSHDATA"
cd /tmp/
git clone -b production --no-checkout --depth 1 https://goldencontact:turbulentDeploy1995@bitbucket.org/goldencontact dapper$timeDrop
cd dapper$timeDrop
git show HEAD:build/config/dapperstrano/autopilots/production-node-install-code-data.php > /tmp/production-node-install-code-data.php
rm -rf /tmp/dapper$timeDrop
cd /tmp/
sudo dapperstrano autopilot execute production-node-install-code-data.php
sudo chown -R www-data /var/www/gcapplications/live/alca-enterprise/alca-enterprise/current/src
sudo rm production-node-install-code-data.php
SSHDATA;
  }

}
