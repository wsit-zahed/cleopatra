<?php

Namespace Model;
use OpenCloud\Compute\Constants\Network;
use OpenCloud\Compute\Constants\ServerState;

class RackspaceDomain extends BaseRackspaceAllOS {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Domain") ;

    public function askWhetherToAddDomain($params=null) {
        return $this->addDomain($params);
    }

    public function askWhetherToAddRecord($params=null) {
        return $this->addRecord($params);
    }

    public function askWhetherToListDomains($params=null) {
        return $this->listDomain($params);
    }

    public function askWhetherToListRecords($params=null) {
        return $this->listRecords($params);
    }

    public function addDomain() {
        if ($this->askForDomainAddExecute() != true) { return false; }
        $this->initialiseRackspace();
        $this->getDomainName() ;
        $service = $this->rackspaceClient->dnsService();
        $domains = $service->domainList();
        foreach ($domains as $domain) {
            if ($domain->name == $this->params["domain-name"]) {
                $domainsResult = new \StdClass() ;
                $domainsResult->status = "already-exists" ;
                $domainsResult->requested = $this->params["domain-name"] ;
                $domainsResult->domain_name = $domain->name ;
                $domainsResult->domain_id = $domain->id ;
                return $domainsResult ;
            }
        }
        // doesn't exist, create it
        $domain = $service->domain();
        $domain->create(array(
            'emailAddress' => $this->params["domain-email"] ,
            'ttl'          => $this->params["domain-ttl"] ,
            'name'         => $this->params["domain-name"],
            'comment'      => $this->params["domain-comment"]
        ));
        $domainsResult = new \StdClass() ;
        $domainsResult->status = "created" ;
        $domainsResult->domain_name = $domain->name ;
        $domainsResult->domain_id = $domain->id ;
        $domainsResult->requested = $this->params["domain-name"] ;
        return $domainsResult ;
    }

    public function addRecord() {
        if ($this->askForRecordAddExecute() != true) { return false; }
        $this->initialiseRackspace();
        $this->getDomainName() ;
        $this->getRecordName() ;
        $this->getRecordType() ;
        $this->getRecordData() ;
        $this->getRecordTTL() ;
        $service = $this->rackspaceClient->dnsService();
        $recordsResult = new \StdClass() ;
        $recordsResult->requested_type = $this->params["record-type"] ;
        $recordsResult->requested_data = $this->params["record-data"] ;
        $recordsResult->requested_name = $this->params["record-name"] ;
        $domains = $service->domainList();
        $domain = null ;
        foreach ($domains as $dom) {
            if ($dom->name == $this->params["domain-name"]) {
                // @todo log domain found or exit if not found
                $domain = $dom;
            }
        }
        $records = $domain->recordList();
        foreach ($records as $record) {
            if ($record->type == $this->params["record-type"] &&
                $record->name == $this->params["record-name"] &&
                $record->data == $this->params["record-data"]) {
                $recordsResult->status = "already-exists" ;
                $recordsResult->record = $record ;
                return $recordsResult ;
            }
        }
        // @todo log creation
        // doesn't exist, create it
        $record = $domain->record(array(
            'type' => $this->params["record-type"],
            'name' => $this->params["record-name"],
            'data' => $this->params["record-data"],
            'ttl'  => $this->params["record-ttl"]
        ));
        $record->create();
        $recordsResult->status = "created" ;
        $recordsResult->record = $record ;
        return $recordsResult ;
    }

    public function listDomain() {
        if ($this->askForDomainAddExecute() != true) { return false; }
        $this->initialiseRackspace();
        $service = $this->rackspaceClient->dnsService();
        $domains = $service->domainList();
        return $domains ;
    }

    public function listRecords() {
        if ($this->askForDomainAddExecute() != true) { return false; }
        $this->initialiseRackspace();
        $service = $this->rackspaceClient->dnsService();
        $domains = $service->domainList();
        $recordRay = array();
        foreach ($domains as $domain) {
            $records = $domain->recordList();
            foreach ($records as $record) {
                $recordRay[$domain->name][] = $record ;
            }
        }
        return $recordRay ;
    }

    protected function askForDomainAddExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Ensure Rackspace Domains?';
        return self::askYesOrNo($question);
    }

    protected function askForRecordAddExecute() {
        if (isset($this->params["yes"]) && $this->params["yes"]==true) { return true ; }
        $question = 'Ensure Rackspace Records?';
        return self::askYesOrNo($question);
    }

    protected function getDomainName() {
        if (isset($this->params["domain-name"])) { return ; }
        $question = 'Enter Domain Name';
        $this->params["domain-name"] = self::askForInput($question, true);
    }

    protected function getRecordName() {
        if (isset($this->params["record-name"])) { return ; }
        $question = 'Enter Record Name';
        $this->params["record-name"] = self::askForInput($question, true);
    }

    protected function getDomainEmail() {
        if (isset($this->params["domain-email"])) { return ; }
        $question = 'Enter Domain EMail';
        $this->params["domain-email"] = self::askForInput($question, true);
    }

    protected function getDomainTTL() {
        if (isset($this->params["domain-ttl"])) { return ; }
        $question = 'Enter Domain TTL';
        $this->params["domain-ttl"] = self::askForInput($question, true);
    }

    protected function getRecordType() {
        if (isset($this->params["record-type"])) { return ; }
        $question = 'Enter Record Type';
        $this->params["record-type"] = self::askForInput($question, true);
    }

    protected function getRecordData() {
        if (isset($this->params["record-data"])) { return ; }
        $question = 'Enter Record Data';
        $this->params["record-data"] = self::askForInput($question, true);
    }

    protected function getRecordTTL() {
        if (isset($this->params["record-ttl"])) { return ; }
        $question = 'Enter Record TTL';
        $this->params["record-ttl"] = self::askForInput($question, true);
    }

    protected function getDomainComment() {
        if (isset($this->params["domain-comment"])) { return ; }
        if (isset($this->params["guess"])) {
            $this->params["domain-comment"] = "" ;
            return ;
        }
        $question = 'Enter an optional Domain Comment';
        $this->params["domain-comment"] = self::askForInput($question, true);
    }

    protected function getMaxWaitTime() {
        if (isset($this->params["max-active-wait-time"])) { return $this->params["max-active-wait-time"] ; }
        return "300";
    }

}