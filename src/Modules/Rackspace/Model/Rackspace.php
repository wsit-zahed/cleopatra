<?php

Namespace Model;

class Rackspace extends BaseModelFactory {

    public static function getModel($params, $modGroup="Base") {
        $thisModule = substr(get_called_class(), 6) ;
        $model = \Model\SystemDetectionFactory::getCompatibleModel($thisModule, $modGroup, $params);
        return $model;
    }

}