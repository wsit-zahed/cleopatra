<?php

Namespace Info;

class RackspaceInfo extends CleopatraBase {

    public $hidden = false;

    public $name = "Rackspace Server Management Functions";

    public function _construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "Rackspace" => array_merge(parent::routesAvailable(), array("save-ssh-key",
          "box-add", "box-remove", "box-destroy", "box-destroy-all", "list") ) );
    }

    public function routeAliases() {
      return array("rackspace"=>"Rackspace", "rackspace"=>"Rackspace");
    }

    public function boxProviderName() {
        return "Rackspace";
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This is an extension provided for Handling Servers on Rackspace.

    Rackspace, rackspace, rackspace

        - box-add
        Lets you add boxes to Rackspace, and adds them to your papyrusfile
        example: cleopatra rackspace box-add
                    --yes
                    --rackspace-ssh-key-path="/home/dave/.ssh/bastion.pub"
                    --rackspace-ssh-key-name="bastion"

        - box-destroy
        Will destroy box/es in an environment for you, and remove them from the papyrus file
        example: cleopatra rackspace box-destroy --yes --guess --rackspace-ssh-key-path="/home/dave/.ssh/bastion.pub" --rackspace-ssh-key-name="bastion"

        - box-destroy-all
        Will destroy all boxes in your digital ocean account - Careful - its irreversible
        example: cleopatra rackspace box-destroy-all --yes --guess

        - save-ssh-key
        Will let you save a local ssh key to your Rackspace account, so you can ssh in to your nodes
        securely and without a password
        example: cleopatra rackspace save-ssh-key
                    --yes
                    --rackspace-ssh-key-path="/home/dave/.ssh/bastion.pub"
                    --rackspace-ssh-key-name="bastion"

        - list
        Will display data about your digital ocean account
        example: cleopatra rackspace list
        example: cleopatra rackspace list --yes
                    --guess # use project saved connection details if possible
                    --rackspace-list-data-type=sizes # servers, sizes, images, domains, regions, ssh_keys

HELPDATA;
      return $help ;
    }

}