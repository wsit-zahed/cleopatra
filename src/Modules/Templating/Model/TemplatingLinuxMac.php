<?php

Namespace Model;

class TemplatingLinuxMac extends BaseTemplater {

    // Compatibility
    public $os = array("Linux", "Darwin") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("any") ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "Templating";
        $this->installCommands = array(
            array("method"=> array("object" => $this, "method" => "runTemplating", "params" => array()) ),
        );
        $this->uninstallCommands = array();
        $this->programDataFolder = "";
        $this->programNameMachine = "templating"; // command and app dir name
        $this->programNameFriendly = "Templating !"; // 12 chars
        $this->programNameInstaller = "Templating Functionality";
        $this->initialize();
    }

    /*
     * @description create a file with inserted values from values and a template
     * @param $original a file path or string of data - a template
     * @param $replacements an array of replacements
     * @param $targetLocation a file path string to put the end file
     * @param $perms string
     * @param $owner string
     * @param $group string
     *
     * @todo the recursive mkdir should specify perms, owner and group
     */
    public function template($original, $replacements, $targetLocation, $perms = null, $owner = null, $group = null) {
        $fData = (is_file($original)) ? file_get_contents($original) : $original ;
        foreach ($replacements as $replaceKey => $replaceValue) {
            $fData = $this->replaceData($fData, $replaceKey, $replaceValue); }
        if (!file_exists(dirname($targetLocation))) {
            mkdir(dirname($targetLocation), 0775, true) ; }
        file_put_contents($targetLocation, $fData) ;
        if ($perms != null) { exec("chmod $perms $targetLocation") ; }
        if ($owner != null) { exec("chown $owner $targetLocation") ; }
        if ($group != null) { exec("chgrp $group $targetLocation") ; }
    }

    public function replaceData($fData, $replaceKey, $replaceValue, $startTag='<%tpl.php%>', $endTag='</%tpl.php%>') {
        $lookFor = $startTag.$replaceKey.$endTag ;
        $fData = str_replace($lookFor, $replaceValue, $fData) ;
        return $fData ;
    }

    public function runTemplating() {
        $this->askForSource() ;
        $this->askForTarget() ;
        $this->getParameterReplacements() ;
        $this->setOverrideReplacements() ;
        $this->template($this->params["source"], $this->replacements, $this->params["target"]) ;
    }

    protected function askForSource(){
        if (isset($this->params["source"])) { $this->templateFile = $this->params["source"] ; }
        else {
            $question = 'Enter Template Source';
            $this->templateFile = self::askForInput($question, true); }
    }

    protected function askForTarget(){
        if (isset($this->params["target"])) { $this->templateFile = $this->params["target"] ; }
        else {
            $question = 'Enter Template Target';
            $this->targetLocation = self::askForInput($question, true); }
    }

    protected function getParameterReplacements(){
        foreach ($this->params as $paramKey => $paramValue) {
            if (substr($paramKey, 0, 9) == "template_") {
                $newKey = substr($paramKey, 9) ;
                $this->replacements[$newKey] = $paramValue ; } }
    }

}