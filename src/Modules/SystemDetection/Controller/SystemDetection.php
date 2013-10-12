<?php

Namespace Controller ;

class SystemDetection extends Base {

    public function execute($pageVars) {

        $thisModel = new \Model\SystemDetection($pageVars["route"]["extraParams"]);

        $isDefaultAction = parent::checkDefaultActions($pageVars, array(), $thisModel) ;
        if ( is_array($isDefaultAction) ) { return $isDefaultAction; }

        if ($pageVars["route"]["action"]=="detect") {
            $this->content["result"] = $thisModel;
            return array ("type"=>"view", "view"=>"environmentConfig", "pageVars"=>$this->content); }

        $this->content["messages"][] = "Invalid Action";
        return array ("type"=>"control", "control"=>"index", "pageVars"=>$this->content);
    }

}