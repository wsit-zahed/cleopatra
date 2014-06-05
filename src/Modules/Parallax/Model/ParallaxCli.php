<?php

Namespace Model;

class ParallaxCli extends BaseLinuxApp {

    // Compatibility
    public $os = array("Linux", "Darwin") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    private $arrayOfCommands = array();
    private $commandResults = array();

    public function __construct($params) {
        parent::__construct($params) ;
    }

    public function askWhetherToRunParallelCommand() {
        $doRunParallel = $this->askToScreenWhetherToRunParallelCommand();
        if ($doRunParallel != true) { return false ; }
        $this->askForAllCommands() ;
        return $this->executeAllCommandInput() ;
    }

    public function askToScreenWhetherToRunParallelCommand() {
        if (isset($this->params["yes"])) { return true; }
        $question = 'Run Commands in Parallel?';
        return self::askYesOrNo($question, true);
    }

    public function askForAllCommands() {
      if (isset($this->params["command-1"])) {
          $this->setParameterCommands() ;
          return ; }
      $commandInput = "anything";
      while ($commandInput != "") {
        $question = "Enter Command to include next. Enter none to end." ;
        $commandInput = self::askForInput($question) ;
        if ($commandInput != "") {
          $this->arrayOfCommands[] = $commandInput ; } }
    }

    private function setParameterCommands() {
        $stillMore = true ;
        $i = 1;
        while ($stillMore == true) {
            if (isset($this->params["command-$i"])) {
                $this->arrayOfCommands[] = $this->params["command-$i"] ;
                $i++;  }
            else {
                $stillMore = false ; }}
    }

    /*
     * @todo
     * It may/may not be "better" to use stout/sterr to read from than a file
     * as a large file in memory (for thousands of lines of output) might be
     * dud, but surely stout and sterr are in memory somewhere anyway? may save
     * on file IO but lose on logging
     * http://us1.php.net/manual/en/function.getmypid.php
     *
     * Also maybe a message queue instead between this and the forked process
     *
     * We are kinda mocking threading, so also look at pthreads
     *
     */
    private function executeAllCommandInput() {
      $allPlxOuts = array();
      $i = 1 ;
      foreach ($this->arrayOfCommands as $command) {
        $tempScript = $this->makeCommandFile($command);
        $outfile = $this->getFileToWrite("final");
        $cmd = 'cleopatra parallax child --command-to-execute="sh '.$tempScript.'" --output-file="'.$outfile.'" > /dev/null &';
        system($cmd, $plxExit);
        $allPlxOuts[] = array($tempScript, $outfile); }
      $copyPlxOuts = $allPlxOuts;
      $fileData = "";
      $ignores = array();
      sleep(3);

      while (count($this->commandResults) < count($allPlxOuts)) {
        for ($i=0; $i<count($copyPlxOuts); $i++) {
          if (in_array($i, $ignores)) {
              continue; }
          $fileToScan = $copyPlxOuts[$i][1];
          $file = new \SplFileObject($fileToScan);
          $file->seek(1);
          $completionStatus = substr($file->current(), 10, 1);
          if ($completionStatus=="1") {
            $file->seek(0);
            echo "Completed task: ".substr($file->current(), 9);
            $file->seek(2);
            $exitStatus = substr($file->current(), 13, 1);
            $this->commandResults[] = $exitStatus;
            $fileData .= file_get_contents($fileToScan);
            $ignores[] = $i;
              // remove our child output file and temp script
              unlink($copyPlxOuts[$i][0]);
              unlink($copyPlxOuts[$i][1]); }}
        echo ".";
        sleep(3); }

        $anyFailures = in_array("1", $this->commandResults);
        return array ($fileData, $anyFailures);
    }

    private function makeCommandFile($command) {
        $random = $this->tempDir.DIRECTORY_SEPARATOR.mt_rand(100, 99999999999);
        file_put_contents($random.'-parallax-temp.sh', $command);
        return $random.'-parallax-temp.sh';
    }

    private function getFileToWrite($file_type) {
      $random = $this->tempDir.DIRECTORY_SEPARATOR.mt_rand(100, 99999999999);
      if ($file_type == "temp") { return $random.'temp.txt'; }
      if ($file_type == "final") { return $random.'final.txt'; }
      else { return null ; }
    }

}