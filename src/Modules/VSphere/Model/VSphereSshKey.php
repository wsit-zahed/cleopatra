<?php

Namespace Model;

class VSphereSshKey extends BaseVSphereAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("SshKey") ;

    public function askWhetherToSaveSshKey($params=null) {
        return $this->performVSphereSaveSshKey($params);
    }

    public function performVSphereSaveSshKey($params=null){
        if ($this->askForSSHKeyExecute() != true) { return false; }
        $this->domainUser = $this->askForVSphereDomainUser();
        $this->vSpherePass = $this->askForVSpherePassword();
        $fileLocation = $this->askForSSHKeyPublicFileLocation();
        $fileData = file_get_contents($fileLocation);
        $keyName = $this->askForSSHKeyNameForVSphere();
        return $this->saveSshKeyToVSphere($fileData, $keyName);
    }

    private function askForSSHKeyExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Save local SSH Public Key file to VMWare VSphere?';
        return self::askYesOrNo($question);
    }

    private function askForSSHKeyPublicFileLocation() {
        if (isset($this->params["vsphere-ssh-key-path"]) && $this->params["vsphere-ssh-key-path"]==true) {
            return $this->params["vsphere-ssh-key-path"] ; }
        $question = 'Enter Location of ssh public key file to upload';
        return self::askForInput($question, true);
    }

    private function askForSSHKeyNameForVSphere(){
        if (isset($this->params["vsphere-ssh-key-name"]) && $this->params["vsphere-ssh-key-name"]==true) {
            return $this->params["vsphere-ssh-key-name"] ; }
        $question = 'Enter name to store ssh key under on VMWare VSphere';
        return self::askForInput($question, true);
    }

    public function saveSshKeyToVSphere($keyData, $keyName){
        $callVars = array();
        $keyData = str_replace("\n", "", $keyData);
        $callVars["ssh_pub_key"] = urlencode($keyData);
        $callVars["name"] = $keyName;
        $curlUrl = "https://api.digitalocean.com/ssh_keys/new" ;
        return $this->vSphereCall($callVars, $curlUrl);
    }

}