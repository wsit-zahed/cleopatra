<?php

Namespace Model;

class FirewallCentos extends FirewallUbuntu {

    // Compatibility
    public $os = array("Linux") ;
    public $linuxType = array("Debian") ;
    public $distros = array("Ubuntu") ;
    public $versions = array("11.04", "11.10", "12.04", "12.10", "13.04") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;
    protected $firewallRule ;
    protected $actionsToMethods =
        array(
            "enable" => "performFirewallEnable",
            "disable" => "performFirewallDisable",
            "allow" => "performFirewallAllow",
            "deny" => "performFirewallDeny",
            "reject" => "performFirewallReject",
            "limit" => "performFirewallLimit",
            "delete" => "performFirewallDelete",
            "insert" => "performFirewallInsert",
            "reset" => "performFirewallReset",
        ) ;

    public function __construct($params) {
        parent::__construct($params);
        $this->autopilotDefiner = "Firewall";
        $this->installCommands = array("yum install -y ufw");
        $this->uninstallCommands = array("yum remove -y ufw");
        $this->programDataFolder = "";
        $this->programNameMachine = "firewall"; // command and app dir name
        $this->programNameFriendly = "!Firewall!!"; // 12 chars
        $this->programNameInstaller = "Firewall";
        $this->initialize();
    }

}