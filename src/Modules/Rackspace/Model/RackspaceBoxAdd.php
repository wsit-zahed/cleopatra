<?php

Namespace Model;
use OpenCloud\Compute\Constants\Network;
use OpenCloud\Compute\Constants\ServerState;

class RackspaceBoxAdd extends BaseRackspaceAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("BoxAdd") ;

    public function askWhetherToBoxAdd($params=null) {
        return $this->addBox($params);
    }

    public function addBox() {
        if ($this->askForBoxAddExecute() != true) { return false; }
        $this->initialiseRackspace();
        $serverPrefix = $this->getServerPrefix();
        $environments = \Model\AppConfig::getProjectVariable("environments");
        $workingEnvironment = $this->getWorkingEnvironment();

        foreach ($environments as $environment) {
            if ($environment["any-app"]["gen_env_name"] == $workingEnvironment) {
                $environmentExists = true ; } }

        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);

        if (isset($environmentExists)) {
            foreach ($environments as $environment) {
                if ($environment["any-app"]["gen_env_name"] == $workingEnvironment) {
                    $envName = $environment["any-app"]["gen_env_name"];

                    if (isset($this->params["yes"]) && $this->params["yes"]==true) {
                        $addToThisEnvironment = true ; }
                    else {
                        $question = 'Add Rackspace Server Boxes to '.$envName.'?';
                        $addToThisEnvironment = self::askYesOrNo($question); }

                    if ($addToThisEnvironment == true) {
                        for ($i = 0; $i < $this->getServerGroupBoxAmount(); $i++) {
                            $serverData = array();
                            $serverData["prefix"] = $serverPrefix ;
                            $serverData["envName"] = $envName ;
                            $serverData["sCount"] = $i ;
                            $serverData["sizeID"] = $this->getServerGroupSizeID() ;
                            $serverData["imageID"] = $this->getServerGroupImageID() ;
                            $serverData["regionID"] = $this->getServerGroupRegionID() ;
                            $serverData["name"] = (isset( $serverData["prefix"]) && strlen( $serverData["prefix"])>0)
                                ? $serverData["prefix"].'-'.$serverData["envName"].'-'.$serverData["sCount"]
                                : $serverData["envName"].'-'.$serverData["sCount"] ;
                            $response = $this->getNewServerFromRackspace($serverData) ;
                            $this->addServerToPapyrus($envName, $response); } } } }

                return true ; }
        else {
            \Core\BootStrap::setExitCode(1) ;
            $logging->log("The environment $workingEnvironment does not exist.") ; }
    }

    protected function askForBoxAddExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Add Rackspace Server Boxes?';
        return self::askYesOrNo($question);
    }

    protected function getServerPrefix() {
        if (isset($this->params["server-prefix"])) {
            return $this->params["server-prefix"] ; }
        $question = 'Enter Prefix for all Servers (None is fine)';
        return self::askForInput($question);
    }

    protected function getWorkingEnvironment() {
        if (isset($this->params["environment-name"])) {
            return $this->params["environment-name"] ; }
        $question = 'Enter Environment to add Servers to';
        return self::askForInput($question);
    }

    protected function getServerGroupImageID() {
        if (isset($this->params["image-id"])) {
            return $this->params["image-id"] ; }
        $question = 'Enter Image ID for this Server Group';
        return self::askForInput($question, true);
    }

    protected function getServerGroupSizeID() {
        if (isset($this->params["size-id"])) {
            return $this->params["size-id"] ; }
        $question = 'Enter size ID for this Server Group';
        return self::askForInput($question, true);
    }

    protected function getServerGroupBoxAmount() {
        if (isset($this->params["box-amount"])) {
            return $this->params["box-amount"] ; }
        $question = 'Enter number of boxes to add to Environment';
        return self::askForInput($question, true);
    }

    protected function getUsernameOfBox($boxName = null) {
        if (isset($this->params["box-user-name"])) {
            return $this->params["box-user-name"] ; }
        if (isset($this->params["box-username"])) {
            return $this->params["box-username"] ; }
        $question = (isset($boxName))
            ? 'Enter SSH username of box '.$boxName
            : 'Enter SSH username of remote box';
        $this->params["box-user-name"] = self::askForInput($question, true) ;
        return $this->params["box-user-name"] ;
    }

    protected function getSSHKeyLocation() {
        if (isset($this->params["private-ssh-key-path"])) {
            return $this->params["private-ssh-key-path"] ; }
        $question = 'Enter file path of private SSH Key';
        $this->params["private-ssh-key-path"] = self::askForInput($question, true) ;
        return $this->params["private-ssh-key-path"] ;
    }

    protected function getNewServerFromRackspace($serverData) {
        $compute = $this->rackspaceClient->computeService('cloudServersOpenStack', $serverData["regionID"]);
        $server = $compute->server();
        try {
            $server->create(array(
                'name'     => $serverData["name"],
                'image'    => $compute->image($serverData["imageID"]),
                'flavor'   => $compute->flavor($serverData["sizeID"]),
                'networks' => array(
                    $compute->network(Network::RAX_PUBLIC),
                    $compute->network(Network::RAX_PRIVATE) ) ) ); }
        catch (\Guzzle\Http\Exception\BadResponseException $e) {
            // No! Something failed. Let's find out:
            $responseBody = (string) $e->getResponse()->getBody();
            $statusCode   = $e->getResponse()->getStatusCode();
            $headers      = $e->getResponse()->getHeaderLines();
            echo sprintf("Status: %s\nBody: %s\nHeaders: %s", $statusCode, $responseBody, implode(', ', $headers)); }
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Request for {$serverData["name"]} complete") ;
        return $server ;
    }

    protected function addServerToPapyrus($envName, $serverData) {
        if (isset($this->params["wait-until-active"])) {
            $serverData = $this->waitUntilActive($serverData); }
        var_dump($serverData);

        $server["target"] = $serverData->addresses->public[0]->addr;
        $server["user"] = $this->getUsernameOfBox() ;
        $server["password"] = $this->getSSHKeyLocation() ;
        $server["provider"] = "Rackspace";
        $server["id"] = $serverData->id;
        $server["name"] = $serverData->name;
        $environments = \Model\AppConfig::getProjectVariable("environments");
        for ($i= 0 ; $i<count($environments); $i++) {
            if ($environments[$i]["any-app"]["gen_env_name"] == $envName) {
                $environments[$i]["servers"][] = $server; } }
        \Model\AppConfig::setProjectVariable("environments", $environments);
    }

    protected function waitUntilActive($server) {
        $compute = $this->rackspaceClient->computeService('cloudServersOpenStack', $this->getServerGroupRegionID());
        $server = $compute->server($server->id);
        $maxWaitTime = (isset($this->params["max-active-wait-time"])) ? $this->params["max-active-wait-time"] : "300" ;
        $callback = function($server) {
            if (!empty($server->error)) {
                var_dump($server->error);
                exit; }
            else {
                $loggingFactory = new \Model\Logging();
                $logging = $loggingFactory->getModel($this->params);
                $logging->log (sprintf(
                    "Waiting on %s/%-12s %4s%%",
                    $server->name(),
                    $server->status(),
                    isset($server->progress) ? $server->progress : 0 ) ) ; } };
        $server->waitFor(ServerState::ACTIVE, $maxWaitTime, $callback);
        return $server;
    }

}