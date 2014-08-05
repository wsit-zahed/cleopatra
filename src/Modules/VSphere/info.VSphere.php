<?php

Namespace Info;

class VSphereInfo extends CleopatraBase {

    public $hidden = false;

    public $name = "VMWare VSphere - Server Management Functions";

    public function _construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "VSphere" => array_merge(parent::routesAvailable(), array("save-ssh-key", "box-add", "box-clone",
          "box-remove", "box-destroy", "box-destroy-all", "box-power-off", "list-vm", "list-vms", "list-host",
          "list-hosts", "test") ) );
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

        - box-clone
        Lets you clone boxes in VMWare VSphere, and adds them to your papyrusfile
        example: cleopatra vsphere box-add
                    --yes
                    --source-vm-id="vm-***"
                    --folder-id="box-2"

        - box-destroy
        Will destroy box/es in an environment for you, and remove them from the papyrus file
        example: cleopatra vsphere box-destroy --yes --guess --vsphere-ssh-key-path="/home/dave/.ssh/bastion.pub" --vsphere-ssh-key-name="bastion"

        - list-vm, list-vms
        Will display data about the VM's on the specified Host
        example: cleopatra vsphere list-vm
        example: cleopatra vsphere list-vm --yes
                    --guess # use project saved connection details if possible

        - list-host, list-hosts
        Will display data about the Hosts in the specified Datacenter
        example: cleopatra vsphere list-hosts
        example: cleopatra vsphere list-hosts --yes
                    --guess # use project saved connection details if possible

HELPDATA;
      return $help ;
    }

}