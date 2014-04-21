<?php

Namespace Info;

class SFTPInfo extends CleopatraBase {

    public $hidden = false;

    public $name = "SFTP Functionality";

    public function _construct() {
      parent::__construct();
    }

    public function routesAvailable() {
      return array( "SFTP" => array_merge(parent::routesAvailable(), array("put", "get") ) );
    }

    public function routeAliases() {
      return array("sftp" => "SFTP");
    }

  public function helpDefinition() {
      $help = <<<"HELPDATA"
  This command handles SFTP Transfer Functions.

  SFTP, sftp

        - put
        Will ask you for details for servers, then copy a file or directory from local to remote
        example: cleopatra sftp put
        example: cleopatra sftp put --yes --environment-name=staging --source="/tmp/file" --target="/home/user/file"
        example: cleopatra sftp put --yes --source="/tmp/file" --target="/home/user/file" # will ask for server details

        - get
        Will ask you for details for servers, then copy a file or directory from remote to local
        example: cleopatra sftp get
        example: cleopatra sftp get --yes --environment-name=staging --source="/tmp/file" --target="/home/user/file"
        example: cleopatra sftp get --yes --source="/tmp/file" --target="/home/user/file" # will ask for server details

HELPDATA;
      return $help ;
    }

}