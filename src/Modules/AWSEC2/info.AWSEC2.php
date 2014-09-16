<?php

Namespace Info;

class AWSEC2Info extends CleopatraBase {

    public $hidden = false;

    public $name = "AWS EC2 Server Management Functions";

    public function _construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "AWSEC2" => array_merge(parent::routesAvailable(), array("save-ssh-key",
          "box-add", "box-remove", "box-destroy", "box-destroy-all", "list") ) );
    }

    public function routeAliases() {
      return array("awsec2"=>"AWSEC2", "aws-ec2"=>"AWSEC2");
    }

    public function boxProviderName() {
        return "AWSEC2";
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This is an extension provided for Handling Servers on AWS EC2.

    AWSEC2, awsec2, aws-ec2

        - box-add
        Lets you add boxes to Rackspace, and adds them to your papyrusfile
        example: cleopatra aws-ec2 box-add
                    --yes
                    --aws-ssh-key-path="/home/dave/.ssh/bastion.pub"
                    --aws-ssh-key-name="bastion"

        - box-destroy
        Will destroy box/es in an environment for you, and remove them from the papyrus file
        example: cleopatra aws-ec2 box-destroy --yes --guess --aws-ssh-key-path="/home/dave/.ssh/bastion.pub" --aws-ssh-key-name="bastion"

        - box-destroy-all
        Will destroy all boxes in your Rackspace account - Careful - its irreversible
        example: cleopatra aws-ec2 box-destroy-all --yes --guess

        - save-ssh-key
        Will let you save a local ssh key to your AWS EC2 account, so you can ssh in to your nodes
        securely and without a password
        example: dapperstrano aws-ec2 save-ssh-key

        - list
        Will display data about your digital ocean account
        example: dapperstrano aws-ec2 list

        Note: region must be one of the following...
        us-east-1, ap-northeast-1, sa-east-1, ap-southeast-1, ap-southeast-2, us-west-2, us-gov-west-1, us-west-1, cn-north-1, eu-west-1

HELPDATA;
      return $help ;
    }

}