<?php

Namespace Controller ;

class SFTP extends Base {

    public function execute($pageVars) {

        $thisModel = $this->getModelAndCheckDependencies(substr(get_class($this), 11), $pageVars) ;
        // if we don't have an object, its an array of errors
        if (is_array($thisModel)) { return $this->failDependencies($pageVars, $this->content, $thisModel) ; }
        $isDefaultAction = self::checkDefaultActions($pageVars, array(), $thisModel) ;
        if ( is_array($isDefaultAction) ) { return $isDefaultAction; }

        $action = $pageVars["route"]["action"];
        $this->content["route"] = $pageVars["route"] ;

        if ($action=="put") {
            $this->content["shlResult"] = $thisModel->askWhetherToSFTPPut();
            return array ("type"=>"view", "view"=>"SFTP", "pageVars"=>$this->content); }

        if ($action=="get") {
            $this->content["shlResult"] = $thisModel->askWhetherToSFTPGet();
            return array ("type"=>"view", "view"=>"SFTP", "pageVars"=>$this->content); }

    }

}