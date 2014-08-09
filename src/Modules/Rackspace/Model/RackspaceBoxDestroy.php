<?php

Namespace Model;

class RackspaceBoxDestroy extends BaseRackspaceAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("BoxDestroy") ;

    public function askWhetherToBoxDestroy() {
        $out = $this->destroyBox();
        return $out ;
    }

    public function destroyBox() {
        if ($this->askForOverwriteExecute() != true) { return false; }
        $this->apiKey = $this->askForRackspaceAPIKey();
        $this->username = $this->askForRackspaceUsername();
        $environments = \Model\AppConfig::getProjectVariable("environments");
        $workingEnvironment = $this->getWorkingEnvironment();

        foreach ($environments as $environment) {
            if (isset($environment["any-app"]["gen_env_name"]) && $environment["any-app"]["gen_env_name"] == $workingEnvironment) {
                $environmentExists = true ; } }

        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);

        if (isset($environmentExists)) {
            for ($i = 0; $i<count($environments); $i++) {
                if ($environments[$i]["any-app"]["gen_env_name"] == $workingEnvironment) {
                    $envName = $environments[$i]["any-app"]["gen_env_name"];

                    if (isset($this->params["yes"]) && $this->params["yes"]==true) {
                        $removeFromThisEnvironment = true ; }
                    else {
                        $question = 'Remove Rackspace Server Boxes from '.$envName.'?';
                        $removeFromThisEnvironment = self::askYesOrNo($question); }

                    if ($removeFromThisEnvironment == true) {
                        if (isset($this->params["destroy-all-boxes"])) {
                            $responses = array();
                            for ($iBox = 0; $iBox < count($environments[$i]["servers"]); $iBox++) {
                                $serverData = array();
                                $serverData["serverID"] = $environments[$i]["servers"][$iBox]["id"] ;
                                $responses[] = $this->destroyServerFromRackspace($serverData) ;
                                $this->deleteServerFromPapyrus($workingEnvironment, $serverData["serverID"]); }
                            return true ; }
                        else if (isset($this->params["destroy-box-id"])) {
                            $responses = array();
                            $serverData = array();
                            $serverData["serverID"] = $this->params["destroy-box-id"] ;
                            $responses[] = $this->destroyServerFromRackspace($serverData) ;
                            $this->deleteServerFromPapyrus($workingEnvironment, $serverData["serverID"]);
                            return true ; }
                        else {
                            \Core\BootStrap::setExitCode(1) ;
                            $logging->log("You must provide either parameter --destroy-all-boxes or --destroy-box-id");
                            return false ; } } } }
            return true ; }
        else {
            \Core\BootStrap::setExitCode(1) ;
            $logging->log("The environment $workingEnvironment does not exist.") ; }
    }

    private function askForOverwriteExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Destroy Rackspace Server Boxes?';
        return self::askYesOrNo($question);
    }

    private function getWorkingEnvironment() {
        if (isset($this->params["environment-name"])) {
            return $this->params["environment-name"] ; }
        $question = 'Enter Environment to destroy Servers in';
        return self::askForInput($question);
    }

    private function destroyServerFromRackspace($serverData) {
        $callVars = array() ;
        $callVars["server_id"] = $serverData["serverID"];
        $curlUrl = "https://api.rackspace.com/v1/servers/{$callVars["server_id"]}/destroy" ;
        $callOut = $this->rackspaceCall($callVars, $curlUrl);
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Request for destroying Droplet {$callVars["server_id"]} complete") ;
        return $callOut ;
    }

    private function deleteServerFromPapyrus($workingEnvironment, $serverId) {
        $environments = \Model\AppConfig::getProjectVariable("environments");
        $newServers = array() ;
        foreach ($environments as &$environment) {
            if ($environment["any-app"]["gen_env_name"] == $workingEnvironment) {
                foreach ($environment["servers"] as $server ) {
                    if ($server["id"] != $serverId) { $newServers[] = $server ; }
                    $environment["servers"] = $newServers ; }
                \Model\AppConfig::setProjectVariable("environments", $environments); } }
    }

}