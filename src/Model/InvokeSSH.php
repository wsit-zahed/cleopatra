<?php

Namespace Model;

class InvokeSSH extends Base {

    private $servers = array();
    private $sshCommands;

    public function askWhetherToInvokeSSHShell() {
        return $this->performInvokeSSHShell();
    }

    public function askWhetherToInvokeSSHScript($params=null) {
        return $this->performInvokeSSHScript($params=null);
    }

    public function runAutoPilotInvokeSSHData($autoPilot) {
        if ( $autoPilot->sshInvokeSSHDataExecute !== true ) { return false; }
        $this->populateServers($autoPilot);
        $this->sshCommands = explode("\n", $autoPilot->sshInvokeSSHDataData ) ;
        foreach ($this->sshCommands as $sshCommand) {
            foreach ($this->servers as &$server) {
                $this->doSSHCommand($server["ssh2Object"], $sshCommand ) ;
                echo "[".$server["target"]."] Executed $sshCommand"  ; } }
        return true;
    }

    public function runAutoPilotInvokeSSHScript($autoPilot) {
        if ( $autoPilot->sshInvokeSSHScriptExecute !== true ) { return false; }
        $this->populateServers($autoPilot);
        $this->sshCommands = explode("\n", file_get_contents($autoPilot->sshInvokeSSHScriptFile) ) ;
        foreach ($this->sshCommands as $sshCommand) {
            foreach ($this->servers as &$server) {
                $this->doSSHCommand($server["ssh2Object"], $sshCommand ) ;
                echo "[".$server["target"]."] Executed $sshCommand"  ; } }
    }

    public function performInvokeSSHShell(){
        $this->askForSSHShellExecute();
        $this->populateServers();
        $commandExecution = true;
        while ($commandExecution == true) {
            $command = $this->askForACommand();
            if ( $command == false) {
                $commandExecution = false; }
            else {
                foreach ($this->servers as &$server) {
                    $this->doSSHCommand($server["ssh2Object"], $command) ;
                    echo "[".$server["target"]."] Executed $command"  ; } } }
            echo "Shell Completed";
        return true;
    }

    public function performInvokeSSHScript($params=null){
        if ($params==null) {
            $params = array();
            $params[0] = $this->askForScriptLocation(); }
        $this->askForSSHScriptExecute();
        $this->populateServers();
        $this->sshCommands = explode("\n", file_get_contents($params[0]) ) ;
        foreach ($this->sshCommands as $sshCommand) {
            foreach ($this->servers as &$server) {
                $this->doSSHCommand($server["ssh2Object"], $sshCommand ) ;
                echo "[".$server["target"]."] Executed $sshCommand"  ; } }
        echo "Script Completed";
        return true;
    }

    public function populateServers($autoPilot=null) {
        $this->loadServerData($autoPilot);
        $this->loadSSHConnections();
    }

    private function loadServerData($autoPilot=null) {
        $srvAvail = (isset($autoPilot->sshInvokeServers) && is_array($autoPilot->sshInvokeServers) &&
            count($autoPilot->sshInvokeServers) > 0);
        if ($srvAvail == true) {
            $this->servers = $autoPilot->sshInvokeServers; }
        else {
            $this->askForServerInfo(); }
    }

    private function loadSSHConnections() {
        foreach ($this->servers as &$server) {
            $attempt = $this->attemptSSH2Connection($server) ;
            if ($attempt == null) {
                echo 'Connection to Server '.$server["target"].' failed. '; }
            else {
                $server["ssh2Object"] = $attempt ; } }
        return true;
    }

    private function attemptSSH2Connection($server) {
        $srcFolder =  str_replace("/Model", "", dirname(__FILE__) ) ;
        $ssh2File = $srcFolder."/Libraries/seclib/Net/SSH2.php" ;
        require_once($ssh2File) ;
        $ssh = new \Net_SSH2($server["target"]);
        if ($ssh->login($server["user"], $server["pword"]) == true) {
            return $ssh; }
        return null;
    }

    private function askForSSHShellExecute(){
        $question = 'Invoke SSH Shell on Server group?';
        return self::askYesOrNo($question);
    }

    private function askForSSHScriptExecute(){
        $question = 'Invoke SSH Script on Server group?';
        return self::askYesOrNo($question);
    }

    private function askForScriptLocation(){
        $question = 'Enter Location of bash script to execute';
        return self::askForInput($question, true);
    }

    private function askForServerInfo(){
        $startQuestion = "Enter Server Info:\n";
        echo $startQuestion;
        $serverAddingExecution = true;
        $question = '';
        while ($serverAddingExecution == true) {
            $server = array();
            $server["target"] = $this->askForServerTarget();
            $server["user"] = $this->askForServerUser();
            $server["pword"] = $this->askForServerPassword();
            $this->servers[] = $server;
            $question .= 'Add Another Server?';
            if ( count($this->servers)<1) { $question .= "You need to enter at least one server\n"; }
            $serverAddingExecution = self::askYesOrNo($question); }
    }

    private function askForServerTarget(){
        $question = 'Please Enter Server Target Host Name/IP';
        $input = self::askForInput($question, true) ;
        return  $input ;
    }

    private function askForServerUser(){
        $question = 'Please Enter Server User';
        $input = self::askForInput($question, true) ;
        return  $input ;
    }

    private function askForServerPassword(){
        $question = 'Please Enter Server Password';
        $input = self::askForInput($question) ;
        return  $input ;
    }

    private function askForACommand(){
        $question = 'Enter command to be executed on remote servers? Enter none to close connection and end program';
        $input = self::askForInput($question) ;
        return ($input=="") ? false : $input ;
    }

    private function doSSHCommand($sshObject, $command){
        echo $sshObject->exec(escapeshellcmd($command));
    }

}