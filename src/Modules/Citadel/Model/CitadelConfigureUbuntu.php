<?php

Namespace Model;

class CitadelConfigureUbuntu extends BaseTemplater {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("12.04", "12.10", "13.04", "13.10", "14.04", "14.10") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Configure") ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "Citadel";
        $this->installCommands = array(
            array("method"=> array("object" => $this, "method" => "setDefaultReplacements", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "setOverrideReplacements", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "setTemplateFile", "params" => array()) ),
            array("method"=> array("object" => $this, "method" => "setTemplate", "params" => array()) ),
        );
        $this->uninstallCommands = array();
        $this->programDataFolder = "/opt/Citadel"; // command and app dir name
        $this->programNameMachine = "citadel"; // command and app dir name
        $this->programNameFriendly = "Citadel Server!"; // 12 chars
        $this->programNameInstaller = "Citadel Server";
        $this->targetLocation = "/etc/citadel/citadel.cfg" ;
        $this->initialize();
    }

    protected function setDefaultReplacements() {
        // set array with default values
        $this->replacements = array(
            // @todo the suffix string here is to denote that you should include the whole line (or multiple lines) in your override answer
            "global_log" => "127.0.0.1 local0 notice",
            "global_maxconn" => "2000",
            "global_user" => "citadel",
            "global_group" => "citadel",
            "defaults_log" => "global",
            "defaults_mode" => "http",
            "defaults_option_string" => "option httplog\n    option dontlognull\n    option redispatch",
            "defaults_retries" => "3",
            "defaults_timeout_connect" => "5000",
            "defaults_timeout_client" => "10000",
            "defaults_timeout_server" => "10000",
            "listen_appname" => "appname",
            "listen_ip_port" => "0.0.0.0:80",
            "listen_mode" => "http",
            "listen_stats_enable" => "enable",
            "listen_stats_uri_string" => "stats uri /citadel?stats",
            "listen_stats_realm_string" => 'stats realm Strictly\ Private',
            "listen_stats_auth_string" => "stats auth cleopatra:cleopatra", # use whole line so we can include multiple
            "listen_balance" => "roundrobin",
            "listen_option_string" => "option httpclose\n    option forwardfor",
            "listen_server_string" => $this->getServerString()
        ) ;
    }

    protected function getServerString() {
        $servers = $this->getServersArray() ;
        $st = "" ;
        foreach ($servers as $server) {
            $st .= "server {$server["name"]} {$server["target"]}:80 check\n" ; }
        return $st ;
    }

    protected function setTemplateFile() {
        $this->templateFile = str_replace("Model", "Templates", dirname(__FILE__) ) ;
        $this->templateFile .= DIRECTORY_SEPARATOR."citadel.cfg" ;
    }

    protected function getServersArray() {
        if (!isset($this->params["environment-name"])) {
            $loggingFactory = new \Model\Logging() ;
            $log = $loggingFactory->getModel($this->params) ;
            $log->log("No environment name provided for Load Balancing") ;
            $this->params["environment-name"] = $this->askForEnvironment() ; }
        $envs = $this->getEnvironments();
        $names = $this->getEnvironmentNames($envs) ;
        $servers = $envs[$names[$this->params["environment-name"]]]["servers"];
        return $servers ;
    }

    private function askForEnvironment(){
        $question = 'What is the environment name you want to balance load to? ';
        $input = self::askForInput($question, true);
        return $input ;
    }

    protected function getEnvironmentNames($envs) {
        $eNames = array() ;
        foreach ($envs as $envKey => $env) {
            $envName = $env["any-app"]["gen_env_name"] ;
            $eNames[$envName] = $envKey ; }
        return $eNames ;
    }

    protected function getEnvironments() {
        return \Model\AppConfig::getProjectVariable("environments");
    }

}