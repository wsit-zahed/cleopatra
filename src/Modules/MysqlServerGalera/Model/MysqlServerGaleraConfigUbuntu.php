<?php

Namespace Model;

class MysqlServerGaleraConfigUbuntu extends Base {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("12.04", "12.10") ;
    public $architectures = array("32", "64") ;

    // Model Group
    public $modelGroup = array("GaleraConfig") ;

    protected $environments ;
    protected $environmentReplacements ;

    public function __construct($params) {
      parent::__construct($params);
    }

    public function askWhetherToConfigGalera() {
        if ($this->askToScreenWhetherToConfigGalera() != true) { return false; }
        $this->setEnvironmentReplacements() ;
        $this->getEnvironments() ;
        $this->doConfigGalera() ;
        return true;
    }

    public function askToScreenWhetherToConfigGalera() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'MySQL Galera Config Here?';
        return self::askYesOrNo($question, true);
    }

    public function setEnvironmentReplacements() {
        $this->environmentReplacements =
            array( "cleo" => array(
               // array("var"=>"dap_proj_cont_dir", "friendly_text"=>"Project Container directory, (inc slash)"),
            ) );
    }

    public function getEnvironments() {
        $environmentConfigModelFactory = new EnvironmentConfig();
        $environmentConfigModel = $environmentConfigModelFactory->getModel($this->params);
        $environmentConfigModel->askWhetherToEnvironmentConfig($this->environmentReplacements) ;
        $this->environments = $environmentConfigModel->environments ;
    }

    public function getClusterStarter($serversArray) {
        return $serversArray[0]["target"];
    }

    public function getServersFromEnvName($envName) {
        foreach ($this->environments as $environment) {
            if ($environment["any-app"]["gen_env_name"] == $envName) {
                return $environment["servers"] ; } }
        return null ;
    }

    private function askForEnvironment() {
        $question = 'What is the environment name you want to balance load to? ';
        $input = self::askForInput($question, true);
        return $input ;
    }

    protected function doConfigGalera() {
        if ($this->params["route"]["action"] == "config-cluster-starter") {
          $templateSubDir = "Templates/ClusterStarter" ;  }
        else {
          $templateSubDir = "Templates/ClusterJoiner" ;  }
        $templatesDir = str_replace("Model", $templateSubDir, dirname(__FILE__) ) ;

        if (!isset($this->params["environment-name"])) {
            $loggingFactory = new \Model\Logging() ;
            $log = $loggingFactory->getModel($this->params) ;
            $log->log("No environment name provided for Galera Cluster") ;
            $this->params["environment-name"] = $this->askForEnvironment() ; }

        $templatorFactory = new \Model\Templating();
        $templator = $templatorFactory->getModel($this->params);
        $targetLocation = "/etc/mysql/conf.d/wsrep.cnf" ;

        $templator->template(
            file_get_contents($templatesDir.DIRECTORY_SEPARATOR."wsrep.cnf"),
            array(
                "cluster_starter_target" => $this->getClusterStarter($this->getServersFromEnvName($this->params["environment-name"])) ,
            ),
            $targetLocation );
        echo $targetLocation."\n";
    }

}