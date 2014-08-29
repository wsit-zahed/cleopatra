<?php

Namespace Controller ;

class Rackspace extends Base {

    public function execute($pageVars) {

        $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "Base") ;
        if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
        $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
        if ( is_array($isDefaultAction) ) { return $isDefaultAction; }

        $action = $pageVars["route"]["action"];

        if ($action=="box-add") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "BoxAdd") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $this->content["rackspaceResult"] = $thisModel->addBox();
            return array ("type"=>"view", "view"=>"rackspaceAPI", "pageVars"=>$this->content); }

        if ($action=="box-remove") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "BoxRemove") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $this->content["rackspaceResult"] = $thisModel->askWhetherToSaveOverwriteCurrent();
            return array ("type"=>"view", "view"=>"rackspaceAPI", "pageVars"=>$this->content); }

        if ($action=="box-destroy") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "BoxDestroy") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $this->content["rackspaceResult"] = $thisModel->destroyBox();
            return array ("type"=>"view", "view"=>"rackspaceAPI", "pageVars"=>$this->content); }

        if ($action=="box-destroy-all") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "BoxDestroyAll") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $this->content["rackspaceResult"] = $thisModel->destroyAllBoxes();
            return array ("type"=>"view", "view"=>"rackspaceAPI", "pageVars"=>$this->content); }

        if (in_array($action, array("save-ssh-key", "sshkey", "ssh-key"))) {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "SshKey") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $this->content["rackspaceResult"] = $thisModel->performRackspaceSaveSshKey();
            return array ("type"=>"view", "view"=>"rackspaceAPI", "pageVars"=>$this->content); }

        if ($action=="list") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "Listing") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $this->content["rackspaceResult"] = $thisModel->askWhetherToListData();
            return array ("type"=>"view", "view"=>"rackspaceList", "pageVars"=>$this->content); }

        $this->content["messages"][] = "Invalid Rackspace Action";
        return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);

    }

}