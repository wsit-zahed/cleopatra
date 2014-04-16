<?php

Namespace Model;

class AppConfig {

    private static function checkSettingsExistOrCreateIt($pfile = null) {
        $pfile = (isset($pfile)) ? $pfile : 'papyrusfile' ;
        if (!file_exists($pfile)) { touch($pfile); }
        return true;
    }

    public static function setProjectVariable($variable, $value, $listAdd=null, $listAddKey=null) {
        if (self::checkSettingsExistOrCreateIt()) {
            $appConfigArray = self::loadProjectFile();
            if ( $listAdd == true && $listAddKey==null ) {
                if ( isset($appConfigArray[$variable]) && is_array($appConfigArray[$variable]) && !in_array($value, $appConfigArray[$variable])) {
                    $appConfigArray[$variable][] = $value ; } }
            else if ( $listAdd == true && $listAddKey!=null ) {
                $appConfigArray[$variable][$listAddKey] = $value ; }
            else { $appConfigArray[$variable] = $value ; }
            self::saveProjectFile( $appConfigArray ) ; }
    }

    /*
     *  to delete a value from an array with keys call deleteProjectVariable($variable, $key)
     *  to delete a value from an array without keys call deleteProjectVariable($variable, "any", $value)
     *  to delete a plain variable call deleteProjectVariable($variable)
     *
     */
    public static function deleteProjectVariable($variable, $key=null, $value=null) {
        if (self::checkSettingsExistOrCreateIt()) {
            $appConfigArray = self::loadProjectFile();
            if ( isset($key) ) {
                // if variable is array without keys, delete entry by value
                if ($key=="any" && isset($value)) {
                    for ($i = 0; $i<count($appConfigArray[$variable]); $i++) {
                        if ($appConfigArray[$variable][$i] == $value) {
                            unset($appConfigArray[$variable][$i]) ; } } }
                // if variable is array with keys, delete entry by key
                else if (isset($appConfigArray[$variable][$key]) && !isset($value)) {
                    unset($appConfigArray[$variable][$key]) ; } }
            else {
                unset($appConfigArray[$variable]) ; }
            self::saveProjectFile( $appConfigArray ) ; }
    }

    public static function getProjectVariable($variable) {
        $value = null;
        if (self::checkSettingsExistOrCreateIt()) {
            $appConfigArray = self::loadProjectFile();
            $value = (isset($appConfigArray[$variable])) ? $appConfigArray[$variable] : null ; }
        return $value;
    }

    public static function loadProjectFile($pfile = null) {
        $pfile = (isset($pfile)) ? $pfile : 'papyrusfile' ;
        if (file_exists($pfile)) {
            $appConfigArraySerialized = file_get_contents($pfile);
            $decoded = unserialize($appConfigArraySerialized);
            return $decoded ; }
        return array();
    }

    public static function saveProjectFile($appConfigArray, $pfile = null) {
        $pfile = (isset($pfile)) ? $pfile : 'papyrusfile' ;
        $appConfigSerialized = serialize($appConfigArray);
        file_put_contents($pfile, $appConfigSerialized);
        chmod($pfile, 0777);
    }

    public static function setAppVariable($variable, $value, $listAdd=null) {
        $appConfigArray = self::loadAppFile();
        if ( $listAdd == true ) { $appConfigArray[$variable][] = $value ; }
        else { $appConfigArray[$variable] = $value ; }
        self::saveAppFile( $appConfigArray ) ;
    }

    public static function deleteAppVariable($variableToDelete) {
        $appConfigArray = self::loadAppFile();
        if (array_key_exists($variableToDelete, $appConfigArray) ) {
            unset($appConfigArray[$variableToDelete]) ; }
        self::saveAppFile( $appConfigArray ) ;
    }

    public static function getAppVariable($variable) {
        $appConfigArray = self::loadAppFile();
        $value = (isset($appConfigArray[$variable])) ? $appConfigArray[$variable] : null ;
        return $value;
    }

    public static function getAllAppVariables() {
        $appConfigArray = self::loadAppFile();
        return $appConfigArray;
    }

    private static function loadAppFile() {
        $appFile = self::getAppBaseDir().DIRECTORY_SEPARATOR.'cleovars';
        if (!file_exists($appFile)){ shell_exec("touch ".$appFile); }
        $appConfigArrayString = file_get_contents($appFile);
        $decoded = unserialize($appConfigArrayString);
        return $decoded;
    }

    private static function saveAppFile($appConfigArray) {
        $coded = serialize($appConfigArray);
        file_put_contents(self::getAppBaseDir().DIRECTORY_SEPARATOR.'cleovars', $coded);
    }

    private static function getAppBaseDir() {
        $baseDir = dirname(__FILE__)."/../../..";
        return $baseDir;
    }

}