<?php

Namespace Controller ;

class Base {

  public $content;
  protected $registeredModels = array();

  public function __construct() {
    $this->content = array(); }

  public function execute($pageVars) {
    $defaultExecution = $this->defaultExecution($pageVars) ;
    if (is_array($defaultExecution)) { return $defaultExecution ; }
  }

  public function checkDefaultActions($pageVars, $ignored_actions=array(), $thisModel=null) {
    $this->content["route"] = $pageVars["route"];
    $this->content["messages"] = $pageVars["messages"];
    $action = $pageVars["route"]["action"];

    if ($action=="help" && !in_array($action, $ignored_actions)) {
        $helpModel = new \Model\Help();
        $this->content["helpData"] = $helpModel->getHelpData($pageVars["route"]["control"]);
        return array ("type"=>"view", "view"=>"help", "pageVars"=>$this->content); }

    if (isset($thisModel)) {
        // @todo child controllers should specify this
        if ($action=="install" || $action=="uninstall" || $action=="status" && !in_array($action, $ignored_actions)) {
            $this->content["params"] = $thisModel->params;
            $this->content["appName"] = $thisModel->autopilotDefiner;
            $newAction = ucfirst($action) ;
            $this->content["appInstallResult"] = $thisModel->{"ask".$newAction}();
            return array ("type"=>"view", "view"=>"app".$newAction, "pageVars"=>$this->content); }
        if (in_array($action, array("init", "initialize")) && !in_array($action, $ignored_actions)) {
            $this->content["params"] = $thisModel->params;
            $this->content["appName"] = $thisModel->autopilotDefiner;
            $this->content["appInstallResult"] = $thisModel->askInit();
            return array ("type"=>"view", "view"=>"appInstall", "pageVars"=>$this->content); }
        if (in_array($action, array("exec", "execute")) && !in_array($action, $ignored_actions)) {
            $this->content["params"] = $thisModel->params;
            $this->content["appName"] = $thisModel->autopilotDefiner;
            $this->content["appInstallResult"] = $thisModel->askExec();
            return array ("type"=>"view", "view"=>"appInstall", "pageVars"=>$this->content); } }

     else if (!isset($thisModel)) {
         $this->content["messages"][] = "Required Model Missing. Cannot Continue.";
         return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content); }

    return false;
  }

  public function checkForRegisteredModels($params, $modelOverrides = null) {
    $modelsToCheck = (isset($modelOverrides)) ? $modelOverrides : $this->registeredModels ;
    $errors = array();
    foreach ($modelsToCheck as $modelClassNameOrArray) {
      if ( is_array($modelClassNameOrArray) && array_key_exists("command", $modelClassNameOrArray) ) {
            $currentKey = $modelClassNameOrArray["command"] ;
            $fullClassName = '\Model\\'.$currentKey;
            if (class_exists($fullClassName)) {
                $moduleModelFactory = new $fullClassName($params);
                $compatibleObject = $moduleModelFactory::getModel($params) ;
                if ( !is_object($compatibleObject) ) {
                    $errors[] = $currentKey ; } } }
      else if ( is_array($modelClassNameOrArray) ) {
            $currentKeys = array_keys($modelClassNameOrArray) ;
            $currentKey = $currentKeys[0] ;
            $fullClassName = '\Model\\'.$currentKey;
            $moduleModelFactory = new $fullClassName($params);
            $compatibleObject = $moduleModelFactory::getModel($params) ;
            if ( !is_object($compatibleObject) ) {
                $errors[] = "Module $currentKey Does not have compatible models for this system: \n"; } }
      else {
        $fullClassName = '\Model\\'.$modelClassNameOrArray;
        $moduleModelFactory = new $fullClassName($params);
        $compatibleObject = $moduleModelFactory::getModel($params) ;
        if ( !is_object($compatibleObject) ) {
            $errors[] = "Module $modelClassNameOrArray Does not have compatible models for this system: \n"; } } }
    if ( count($errors) > 0 ) {
      return $errors; }
    // echo "All required Modules found, all with compatible Models"."\n";
    return true ;
  }

  protected function executeMyRegisteredModels($params = null) {
    foreach ($this->registeredModels as $modelClassNameOrArray) {
      if ( is_array($modelClassNameOrArray) ) {
        $currentKeys = array_keys($modelClassNameOrArray) ;
        $currentKey = $currentKeys[0] ;
        $fullClassName = '\Model\\'.$currentKey;}
      else {
        $fullClassName = '\Model\\'.$modelClassNameOrArray; }
      $currentModelFactory = new $fullClassName();
      $currentModel = new $currentModelFactory->getModel($params);
      $miniRay = array();
      $miniRay["appName"] = $currentModel->programNameInstaller;
      $miniRay["installResult"] = $currentModel->askInstall();
      $this->content["results"][] = $miniRay ; }
  }

  protected function executeMyRegisteredModelsAutopilot($autoPilot, $params = null) {
    foreach ($autoPilot->steps as $modelArray) {
        $currentKeys = array_keys($modelArray) ;
        $currentKey = $currentKeys[0] ;
        $fullClassName = '\Model\\'.$currentKey;
        $modelFactory = new $fullClassName($params);
        $currentModel = $modelFactory->getModel($params);
        $miniRay = array();
        $miniRay["appName"] = $currentModel->programNameInstaller;
        $miniRay["installResult"] = $currentModel->runAutoPilotInstall($modelArray);
        $this->content["results"][] = $miniRay ; }
  }

  protected function getModelAndCheckDependencies($module, $pageVars, $moduleType="Installer") {
        $myInfo = \Core\AutoLoader::getSingleInfoObject($module);
        $myModuleAndDependencies = array_merge(array($module), $myInfo->dependencies() ) ;
        $dependencyCheck = $this->checkForRegisteredModels($pageVars["route"]["extraParams"], $myModuleAndDependencies) ;
        if ($dependencyCheck === true) {
            $thisModel = \Model\SystemDetectionFactory::getCompatibleModel($module, $moduleType, $pageVars["route"]["extraParams"]);
            return $thisModel; }
        return $dependencyCheck ;
    }

    protected function failDependencies($pageVars, $content, $errors) {
        $this->content = array_merge($pageVars, $content) ;
        foreach($errors as $error) { $this->content["messages"][] = $error ; }
        return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);
    }

    protected function defaultExecution($pageVars) {
        $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars) ;
        // if we don't have an object, its an array of errors
        if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
        $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
        if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
        return null ;
    }

}