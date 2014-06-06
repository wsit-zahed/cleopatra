<?php

Namespace Info;

class VSphereInfo extends CleopatraBase {

    public $hidden = false;

    public $name = "VMWare VSphere - Server Management Functions";

    public function _construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "VSphere" => array_merge(parent::routesAvailable(), array("save-ssh-key",
          "box-add", "box-remove", "box-destroy", "box-destroy-all", "list", "test") ) );
    }

    public function routeAliases() {
      return array("vmware-vsphere"=>"VSphere", "vsphere"=>"VSphere");
    }

    public function boxProviderName() {
        return "VSphere";
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This is an extension provided for Handling Servers on VMWare VSphere.

    VSphere, vmware-vsphere, vsphere

        - box-add
        Lets you add boxes to VMWare VSphere, and adds them to your papyrusfile
        example: cleopatra vsphere box-add
                    --yes
                    --vsphere-ssh-key-path="/home/dave/.ssh/bastion.pub"
                    --vsphere-ssh-key-name="bastion"

        - box-destroy
        Will destroy box/es in an environment for you, and remove them from the papyrus file
        example: cleopatra vsphere box-destroy --yes --guess --vsphere-ssh-key-path="/home/dave/.ssh/bastion.pub" --vsphere-ssh-key-name="bastion"

        - box-destroy-all
        Will destroy all boxes in your digital ocean account - Careful - its irreversible
        example: cleopatra vsphere box-destroy-all --yes --guess

        - save-ssh-key
        Will let you save a local ssh key to your VMWare VSphere account, so you can ssh in to your nodes
        securely and without a password
        example: cleopatra vsphere save-ssh-key
                    --yes
                    --vsphere-ssh-key-path="/home/dave/.ssh/bastion.pub"
                    --vsphere-ssh-key-name="bastion"

        - list
        Will display data about your digital ocean account
        example: cleopatra vsphere list
        example: cleopatra vsphere list --yes
                    --guess # use project saved connection details if possible
                    --vsphere-list-data-type=sizes # droplets, sizes, images, domains, regions, ssh_keys

HELPDATA;
      return $help ;
    }

}