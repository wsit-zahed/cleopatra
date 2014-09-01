<?php

Namespace Model;

class RackspaceAllBoxesDestroy extends BaseRackspaceAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("BoxDestroyAll") ;

    public function destroyAllBoxes() {
        if ($this->askForOverwriteExecute() != true) { return false; }
        $this->apiKey = $this->askForRackspaceAPIKey();
        $this->username = $this->askForRackspaceUsername();

        $doFactory = new \Model\Rackspace();
        $listParams = array("yes" => true, "guess" => true, "rackspace-list-data-type" => "servers") ;
        $doListing = $doFactory->getModel($listParams, "Listing") ;
        $allBoxes = $doListing->askWhetherToListData();

        foreach($allBoxes->servers as $box) {
            $serverData["serverID"] = $box->id ;
            $responses[] = $this->destroyServerFromRackspace($serverData) ;
        }

        return true ;

    }

    private function askForOverwriteExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Destroy All Rackspace Server Boxes? (Careful!)';
        return self::askYesOrNo($question);
    }

    private function destroyServerFromRackspace($serverData) {
        $callVars = array() ;
        $callVars["server_id"] = $serverData["serverID"];
        $curlUrl = "https://api.rackspace.com/v1/servers/{$callVars["server_id"]}/destroy" ;
        $callOut = $this->rackspaceCall($callVars, $curlUrl);
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Request for destroying Server {$callVars["server_id"]} complete") ;
        return $callOut ;
    }

}