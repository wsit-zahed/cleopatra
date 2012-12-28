<?php

phpMDExecutor::execute();

class phpMDExecutor {

    public static function execute(){
        self::setWorkingDirectory();
        self::performTests(); }

    private function setWorkingDirectory(){
        $scriptLocation = dirname(__FILE__);
        chdir($scriptLocation); }

    private function performTests(){
        $basePath = str_replace('build/config/phpmd', "", dirname(__FILE__));
        $command = 'phpmd '.dirname(__FILE__).'/../../../src/ html '.dirname(__FILE__).'/rules/standard.xml ';
        $command .= ' --exclude '.$basePath.'src/Core/View.php';
        $command .= ' --reportfile '.dirname(__FILE__).'/../../reports/phpmd/index.html';
        self::executeAndOutput($command); }

    private static function executeAndOutput($command) {
        $outputArray = array();
        exec($command, $outputArray);
        echo "\nOutput for Command $command:\n";
        foreach ($outputArray as $outputValue) {
            echo "$outputValue\n"; } }

}

?>