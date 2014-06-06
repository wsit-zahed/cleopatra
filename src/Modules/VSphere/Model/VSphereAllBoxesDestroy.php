<?php

Namespace Model;

class VSphereAllBoxesDestroy extends BaseVSphereAllOS {

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
        $this->domainUser = $this->askForVSphereDomainUser();
        $this->vSpherePass = $this->askForVSpherePassword();

        $doFactory = new \Model\VSphere();
        $listParams = array("yes" => true, "guess" => true, "vsphere-list-data-type" => "droplets") ;
        $doListing = $doFactory->getModel($listParams, "Listing") ;
        $allBoxes = $doListing->askWhetherToListData();

        foreach($allBoxes->droplets as $box) {
            $serverData["dropletID"] = $box->id ;
            $responses[] = $this->destroyServerFromVSphere($serverData) ;
        }

        return true ;

    }

    private function askForOverwriteExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Destroy All VMWare VSphere Server Boxes? (Careful!)';
        return self::askYesOrNo($question);
    }

    private function destroyServerFromVSphere($serverData) {
        $callVars = array() ;
        $callVars["droplet_id"] = $serverData["dropletID"];
        $curlUrl = "https://api.vmware-vsphere.com/droplets/{$callVars["droplet_id"]}/destroy" ;
        $callOut = $this->vSphereCall($callVars, $curlUrl);
        $loggingFactory = new \Model\Logging();
        $logging = $loggingFactory->getModel($this->params);
        $logging->log("Request for destroying Droplet {$callVars["droplet_id"]} complete") ;
        return $callOut ;
    }

}