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

    private $environments ;
    private $environmentReplacements ;

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

    public function getServerArrayText($serversArray) {
        $serversText = "";
        foreach($serversArray as $serverArray) {
            $serversText .= 'array(';
            $serversText .= '"target" => "'.$serverArray["target"].'", ';
            $serversText .= '"user" => "'.$serverArray["user"].'", ';
            $serversText .= '"pword" => "'.$serverArray["password"].'", ';
            $serversText .= '),'."\n"; }
        return $serversText;
    }

    private function doConfigGalera() {
      if ($this->params["route"]["action"] == "config-cluster-starter") {
          $templateSubDir = "Templates/ClusterStarter" ;  }
      else {
          $templateSubDir = "Templates/ClusterJoiner" ;  }
      $templatesDir = str_replace("Model", $templateSubDir, dirname(__FILE__) ) ;
      $templates = scandir($templatesDir);
      foreach ($this->environments as $environment) {
        foreach ($templates as $template) {
          if (!in_array($template, array(".", ".."))) {
              $templatorFactory = new \Model\Templating();
              $templator = $templatorFactory->getModel($this->params);
              $newFileName = str_replace("environment", $environment["any-app"]["gen_env_name"], $template ) ;
              $autosDir = getcwd().'/build/config/cleopatra/cleofy/autopilots/generated';
              $targetLocation = $autosDir.DIRECTORY_SEPARATOR.$newFileName ;
              $templator->template(
                  file_get_contents($templatesDir.DIRECTORY_SEPARATOR.$template),
                  array(
                      "cluster_address" => $this->getClusterStarter($environment["servers"]) ,
                  ),
                  $targetLocation );
          echo $targetLocation."\n"; } } }
    }

}