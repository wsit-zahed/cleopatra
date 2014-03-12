<?php

Namespace Core ;

class AutoPilotConfigured extends AutoPilot {

    public $steps ;

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
              <%tpl.php%>gen_srv_array_text</%tpl.php%>
            ),
          ) , ) ,
        );

    }


//
// This function will set the sshInvokeSSHDataData variable with the data that
// you need in it. Call this in your constructor
//
  private function setSSHData() {
    $this->steps[0]["InvokeSSH"]["sshInvokeSSHDataData"] = <<<"SSHDATA"
sudo apt-get install -y php5 git
git clone https://github.com/phpengine/cleopatra && sudo php cleopatra/install-silent
sudo cleopatra dapperstrano install --yes=true
SSHDATA;
  }

}
