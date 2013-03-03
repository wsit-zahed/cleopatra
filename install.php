#!/usr/bin/php
<?php

$installer = new Installer();
$installer->installProgram();

class Installer {

    private $titleData;
    private $completionData;
    private $bootStrapData;
    private $programDataFolder;
    private $programExecutorFolder;

    public function __construct() {
        $this->populateTitle();
        $this->populateCompletion();
        $this->populateExecutorFile();
    }

    public function installProgram() {
        $this->showTitle();
        $this->askForProgramDataFolder();
        $this->askForProgramExecutorFolder();
        $this->deleteProgramDataFolderAsRootIfExists();
        $this->makeProgramDataFolderIfNeeded();
        $this->copyFilesToProgramDataFolder();
        $this->deleteExecutorIfExists();
        $this->saveExecutorFile();
        $this->deleteInstallationFiles();
        $this->showCompletion();
    }

    private function showTitle() {
        print $this->titleData ;
    }

    private function showCompletion() {
        print $this->completionData ;
    }

    private function askForProgramDataFolder() {
        $question = 'What is the program data directory?';
        $question .= ' Found "/opt/devhelper" - use this? (Enter nothing for yes, No Trailing Slash)';
        $input = self::askForInput($question);
        $this->programDataFolder = ($input=="") ? "/opt/devhelper" : $input ;
    }

    private function askForProgramExecutorFolder(){
        $question = 'What is the program executor directory?';
        $question .= ' Found "/usr/bin" - use this? (Enter nothing for yes, No Trailing Slash)';
        $input = self::askForInput($question);
        $this->programExecutorFolder = ($input=="") ? "/usr/bin" : $input ;
    }

    private function deleteProgramDataFolderAsRootIfExists(){
        if ( is_dir($this->programDataFolder)) {
            $command = 'rm -rf '.$this->programDataFolder;
            self::executeAndOutput($command, "Program Data Folder $this->programDataFolder Deleted if existed"); }
        return true;
    }

    private function makeProgramDataFolderIfNeeded(){
        if (!file_exists($this->programDataFolder)) {
            mkdir($this->programDataFolder,  0777, true); }
    }

    private function copyFilesToProgramDataFolder(){
        $command = 'cp -r '.getcwd().'/* '.$this->programDataFolder;
        return self::executeAndOutput($command, "Program Data folder populated");
    }

    private function deleteExecutorIfExists(){
        $command = 'rm -f '.$this->programExecutorFolder.'/devhelper';
        self::executeAndOutput($command, "Program Executor Deleted  if existed");
        return true;
    }

    private function deleteInstallationFiles(){
        $installFilesDir = getcwd();
        $command = 'cd ..';
        self::executeAndOutput($command);
        $command = 'rm -rf '.$installFilesDir;
        self::executeAndOutput($command);
    }

    private function saveExecutorFile(){
        $this->populateExecutorFile();
        return file_put_contents($this->programExecutorFolder.'/devhelper', $this->bootStrapData);
    }

    private function askForInput($question, $required=null) {
        $fp = fopen('php://stdin', 'r');
        $last_line = false;
        while (!$last_line) {
            print "$question\n";
            $inputLine = fgets($fp, 1024);
            if ($required && strlen($inputLine)==0 ) {
                print "You must enter a value. Please try again.\n"; }
            else {$last_line = true;} }
        $inputLine = $this->stripNewLines($inputLine);
        return $inputLine;
    }

    private function populateTitle() {
$this->titleData = <<<TITLE
*******************************
*   Golden Contact Computing  *
*          Dev Helper         *
*******************************


TITLE;
    }

    private function populateCompletion() {
$this->completionData = <<<COMPLETION
... All done!
*******************************
Thanks for installing , visit www.gcsoftshop.co.uk for more

COMPLETION;
    }

    private function populateExecutorFile() {
$this->bootStrapData = "#!/usr/bin/php\n
<?php\n
require('$this->programDataFolder/src/Bootstrap.php');\n
?>";
    }

    private function stripNewLines($inputLine) {
        $inputLine = str_replace("\n", "", $inputLine);
        $inputLine = str_replace("\r", "", $inputLine);
        return $inputLine;
    }

    private function executeAndOutput($command, $message=null) {
        $outputArray = array();
        exec($command, $outputArray);
        $outputText = "";
        foreach ($outputArray as $outputValue) {
            $outputText .= "$outputValue\n"; }
        if ($message !== null) {
            $outputText .= "$message\n"; }
        print $outputText;
        return true;
    }

}