<?php

Namespace Model;

class BasePHPWindowsApp extends BasePHPApp {

    public function __construct($params) {
        parent::__construct($params);
    }

    protected function askForProgramDataFolder() {
        $progDir = str_replace(" (x86)", "", getenv('ProgramFiles'));
        if (isset($this->params["program-data-directory"])) { return $this->params["program-data-directory"] ; }
        $question = 'What is the program data directory?';
        $question .= ' Found "'.$progDir.DS.$this->programNameMachine.'" - use this? (Enter nothing for yes, no end slash)';
        $input = (isset($this->params["yes"]) && $this->params["yes"]==true) ? $progDir.DS.$this->programNameMachine : self::askForInput($question);
        return ($input=="") ? $progDir.DS.$this->programNameMachine : $input ;
    }

    protected function askForProgramExecutorFolder(){        
        $sd = getenv('SystemDrive') ;
        $this->programExecutorFolder = $sd.DS.'PharaohTools' ;
        $question = 'What is the program executor directory?';
        $question .= ' Found "'.$sd.DS.'PharaohTools'.'" - use this? (Enter nothing for yes, No Trailing Slash)';
        $input = (isset($this->params["yes"]) && $this->params["yes"]==true) ? $sd.DS.'PharaohTools' : self::askForInput($question);
        return ($input=="") ? $sd.DS.'PharaohTools' : $input ;
    }

    protected function populateExecutorFile() {
        if (isset($this->params["no-executor"])) { return ; }
            $this->bootStrapData =
                "@echo off\r\n\r\nphp ".'"'.$this->programDataFolder.DS.$this->programNameMachine.DS."src".DS."Bootstrap.php".'" %*' ;
    }

    protected function deleteProgramDataFolderAsRootIfExists() {
        if ( file_exists($this->programDataFolder)) {
            echo "it exists: {$this->programDataFolder}" ;
            $command = 'rmdir /s /q "'.$this->programDataFolder.'"';
            self::executeAndOutput($command, "Program Data Folder $this->programDataFolder Deleted"); }        
        echo "No existing Directory at {$this->programDataFolder} to remove\n" ;
        return true;
    }

    protected function makeProgramDataFolderIfNeeded() {       
        if (!file_exists($this->programDataFolder)) {
            $comm = 'mkdir "'.$this->programDataFolder.'"' ;
            self::executeAndOutput($comm) ;
            echo $comm."\n" ; }
    }

    protected function copyFilesToProgramDataFolder() {
        $command = 'xcopy /h /q /s /e /y "'.$this->tempDir.DIRECTORY_SEPARATOR.$this->programNameMachine.'" '.
            '"'.$this->programDataFolder.'"';
        echo $command."\n" ;
        return self::executeAndOutput($command, "Program Data folder populated");
    }

    protected function deleteExecutorIfExists() {
        if (file_exists($this->programExecutorFolder.DIRECTORY_SEPARATOR.$this->programNameMachine.".cmd")) {
            $command = 'del '.$this->programExecutorFolder.DIRECTORY_SEPARATOR.$this->programNameMachine.".cmd";
            self::executeAndOutput($command, "Program Executor Deleted if existed");
            return true; }
    }

    protected function saveExecutorFile(){
        $this->populateExecutorFile();
        $res = file_put_contents($this->programExecutorFolder.DS.$this->programNameMachine.".cmd", $this->bootStrapData);
        echo ($res) ? "Saved executor file\n" : "Error saving executor file" ;
        return $res ;
    }

    protected function deleteTempAsRootIfExists(){
        if ( is_dir($this->tempDir.DIRECTORY_SEPARATOR.$this->programNameMachine)) {
            $command = 'rmdir /s /q '.$this->tempDir.DIRECTORY_SEPARATOR.$this->programNameMachine;
            self::executeAndOutput($command, "Temp files at ".$this->tempDir.DIRECTORY_SEPARATOR.$this->programNameMachine." Deleted"); }
        return true;
    }

    protected function deleteInstallationFiles(){
        $command = 'rmdir /s /q '.$this->tempDir.DS.$this->programNameMachine;
        self::executeAndOutput($command, "Installation files deleted");
    }

  protected function changePermissions(){
      // @todo fix this
//    $command = "chmod -R 775 $this->programDataFolder";
//    self::executeAndOutput($command);
//    $command = "chmod 775 $this->programExecutorFolder/$this->programNameMachine";
//    self::executeAndOutput($command);
  }

}