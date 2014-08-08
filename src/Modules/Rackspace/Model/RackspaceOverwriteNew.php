<?php

Namespace Model;

class RackspaceOverwriteNew extends BaseRackspaceAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("OverwriteNew") ;

    public function askWhetherToSaveOverwriteNew($params=null) {
        return $this->performRackspaceOverwriteNew($params);
    }

    public function performRackspaceOverwriteNew($params=null){
        if ($this->askForOverwriteExecute() != true) { return false; }
        $this->apiKey = $this->askForRackspaceAPIKey();
        $this->username = $this->askForRackspaceUsername();
        $environments = \Model\AppConfig::getProjectVariable("environments");
        $serverPrefix = $this->getServerPrefix();
        foreach ($environments as $environment) {
            $envName = $environment["any-app"]["gen_env_name"];
            $question = 'Overwrite current server details for '.$envName.' with new Rackspace Servers?';
            $overwriteThisEnvironment = self::askYesOrNo($question);
            if ($overwriteThisEnvironment == true) {
                $sCount = 0;
                foreach ($environment["servers"] as $server) {
                    $serverData = array();
                    $serverData["prefix"] = $serverPrefix ;
                    $serverData["envName"] = $envName;
                    $serverData["sCount"] = $sCount;
                    $serverData["imageID"] = $this->getServerGroupImageID();
                    $serverData["sizeID"] = $this->getServerGroupSizeID();
                    $serverData["regionID"] = $this->getServerGroupRegionID();
                    $newRackspaceServer = $this->getNewServerFromRackspace($serverData) ;
                    $sCount++; } } }
        $envConfig = new EnvironmentConfig();
        $envConfig->environments = $environments ;
        $envConfig->writeEnvsToProjectFile();
        return $envConfig->environments ;
    }

    private function askForOverwriteExecute(){
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Overwrite current server details with new Rackspace Servers?';
        return self::askYesOrNo($question);
    }

    private function getServerPrefix(){
        $question = 'Enter Prefix for all Servers (None is fine)';
        return self::askForInput($question);
    }

    private function getServerGroupImageID(){
        $question = 'Enter Image ID for this Server Group';
        return self::askForInput($question, true);
    }

    private function getServerGroupSizeID(){
        $question = 'Enter size ID for this Server Group';
        return self::askForInput($question, true);
    }

    private function getServerGroupRegionID(){
        $question = 'Enter Region ID for this Server Group';
        return self::askForInput($question, true);
    }

    private function getNewServerFromRackspace($serverData){
        $callVars = (array) $this->getNewServerCallVarsFromData($serverData) ;
        $curlUrl = "https://api.rackspace.com/v1/servers/new" ;
        return $this->rackspaceCall($callVars, $curlUrl);
    }

    private function getNewServerCallVarsFromData($serverData){
        # name=[server_name]
        # size_id=[size_id]
        # image_id=[image_id]
        # region_id=[region_id]
        # ssh_key_ids=[ssh_key_id1],[ssh_key_id2]
        $callVars = array() ;
        $callVars["name"] = $serverData["prefix"].'-'.$serverData["envName"].'-'.$serverData["sCount"];
        $callVars["size_id"] = $serverData["sizeID"];
        $callVars["image_id"] = $serverData["imageID"];
        $callVars["region_id"] = $serverData["regionID"];
        $callVars["ssh_key_ids"] = $this->getAllSshKeyIdsString();
        $curlUrl = "https://api.rackspace.com/v1/servers/new" ;
        return $this->rackspaceCall($callVars, $curlUrl);
    }

    private function getAllSshKeyIdsString(){
        $curlUrl = "https://api.rackspace.com/v1/ssh_keys" ;
        $sshKeysObject =  $this->rackspaceCall(array(), $curlUrl);
        $keysString = "";
        for ($i=0; $i<count($sshKeysObject->ssh_keys); $i++) {
            $keysString .= "{$sshKeysObject->ssh_keys[$i]->id}" ;
            if ($i < (count($sshKeysObject->ssh_keys)) ) {
                $keysString .= "," ; } }
        return $keysString;
    }

}