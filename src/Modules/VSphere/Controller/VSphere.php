<?php

Namespace Controller ;

class VSphere extends Base {

    public function execute($pageVars) {

        $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "Base") ;
        if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
        $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
        if ( is_array($isDefaultAction) ) { return $isDefaultAction; }

        $action = $pageVars["route"]["action"];

        // @todo do this in a loop

        if ($action=="box-add") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "BoxAdd") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
            if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
            $this->content["vSphereResult"] = $thisModel->addBox();
            return array ("type"=>"view", "view"=>"vSphereAPI", "pageVars"=>$this->content); }

        if ($action=="box-clone") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "BoxClone") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
            if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
            $this->content["vSphereResult"] = $thisModel->cloneBox();
            return array ("type"=>"view", "view"=>"vSphereAPI", "pageVars"=>$this->content); }

        if ($action=="box-remove") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "BoxRemove") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
            if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
            $this->content["vSphereResult"] = $thisModel->askWhetherToSaveOverwriteCurrent();
            return array ("type"=>"view", "view"=>"vSphereAPI", "pageVars"=>$this->content); }

        if ($action=="box-power-off") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "BoxPowerOff") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
            if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
            $this->content["vSphereResult"] = $thisModel->askWhetherToPowerOff();
            return array ("type"=>"view", "view"=>"vSphereAPI", "pageVars"=>$this->content); }

        if ($action=="box-destroy") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "BoxDestroy") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
            if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
            $this->content["vSphereResult"] = $thisModel->destroyBox();
            return array ("type"=>"view", "view"=>"vSphereAPI", "pageVars"=>$this->content); }

        if ($action=="box-destroy-all") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "BoxDestroyAll") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
            if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
            $this->content["vSphereResult"] = $thisModel->destroyAllBoxes();
            return array ("type"=>"view", "view"=>"vSphereAPI", "pageVars"=>$this->content); }

        if ($action=="save-ssh-key") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "SshKey") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
            if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
            $this->content["vSphereResult"] = $thisModel->askWhetherToSaveSshKey();
            return array ("type"=>"view", "view"=>"vSphereAPI", "pageVars"=>$this->content); }

        if ($action=="list-vm" || $action=="list-vms") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "ListVM") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
            if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
            $this->content["vSphereResult"] = $thisModel->askWhetherToListData();
            return array ("type"=>"view", "view"=>"vSphereListVM", "pageVars"=>$this->content); }

        if ($action=="list-host" || $action=="list-hosts") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "ListHost") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
            if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
            $this->content["vSphereResult"] = $thisModel->askWhetherToListData();
            return array ("type"=>"view", "view"=>"vSphereListHost", "pageVars"=>$this->content); }

        if ($action=="test") {
            $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars, "Testing") ;
            if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
            $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
            if ( is_array($isDefaultAction) ) { return $isDefaultAction; }
            $this->content["vSphereResult"] = $thisModel->askWhetherToListData();
            return array ("type"=>"view", "view"=>"vSphereList", "pageVars"=>$this->content); }

        $this->content["messages"][] = "Invalid VMWare VSphere Action";
        return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);

    }

}