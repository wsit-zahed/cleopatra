<?php

Namespace Model;

class BaseVSphereAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Base") ;

    protected $vSpherePass ;
    protected $domainUser ;
    protected $vSphereUrl ;

    protected $client ;

    protected function askForVSphereDomainUser(){
        if (isset($this->params["vsphere-domain-user"])) { return $this->params["vsphere-domain-user"] ; }
        $papyrusVar = \Model\AppConfig::getProjectVariable("vsphere-domain-user") ;
        if ($papyrusVar != null) {
            if (isset($this->params["guess"]) && $this->params["guess"] == true) { return $papyrusVar ; }
            if (isset($this->params["use-project-domain-user"]) && $this->params["use-project-domain-user"] == true) {
                return $papyrusVar ; }
            $question = 'Use Project saved VMWare VSphere Domain/Username?';
            if (self::askYesOrNo($question, true) == true) { return $papyrusVar ; } }
        $appVar = \Model\AppConfig::getProjectVariable("vsphere-domain-user") ;
        if ($appVar != null) {
            $question = 'Use Application saved VMWare VSphere Domain/Username?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ; } }
        $question = 'Enter VMWare VSphere Domain/Username';
        return self::askForInput($question, true);
    }

    protected function askForVSpherePassword(){
        if (isset($this->params["vsphere-pass"])) { return $this->params["vsphere-pass"] ; }
        $papyrusVar = \Model\AppConfig::getProjectVariable("vsphere-pass") ;
        if ($papyrusVar != null) {
            if (isset($this->params["guess"]) && $this->params["guess"] == true) { return $papyrusVar ; }
            if (isset($this->params["use-project-pass"]) && $this->params["use-project-pass"] == true) { return $papyrusVar ; }
            $question = 'Use Project saved VMWare VSphere Password?';
            if (self::askYesOrNo($question, true) == true) {
                return $papyrusVar ; } }
        $appVar = \Model\AppConfig::getProjectVariable("vsphere-pass") ;
        if ($appVar != null) {
            $question = 'Use Application saved VMWare VSphere Password?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ; } }
        $question = 'Enter VMWare VSphere Password';
        return self::askForInput($question, true);
    }

    protected function askForVSphereUrl(){
        if (isset($this->params["vsphere-url"])) { return $this->params["vsphere-url"] ; }
        $papyrusVar = \Model\AppConfig::getProjectVariable("vsphere-url") ;
        if ($papyrusVar != null) {
            if (isset($this->params["guess"])) {
                return $papyrusVar ; }
            if (isset($this->params["use-project-url"]) && $this->params["use-project-url"] == true) {
                return $papyrusVar ; }
            $question = 'Use Project saved VMWare VSphere URL?';
            if (self::askYesOrNo($question, true) == true) { return $papyrusVar ; } }
        $appVar = \Model\AppConfig::getProjectVariable("vsphere-url") ;
        if ($appVar != null) {
            $question = 'Use Application saved VMWare VSphere URL?';
            if (self::askYesOrNo($question, true) == true) {
                return $appVar ; } }
        $question = 'Enter VMWare VSphere URL';
        return self::askForInput($question, true);
    }

}