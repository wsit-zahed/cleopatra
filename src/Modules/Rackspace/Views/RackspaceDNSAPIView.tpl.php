<?php
//var_dump($pageVars);
if ($pageVars["route"]["action"]=="ensure-domain-exists") {
    if (is_object($pageVars["rackspaceResult"])) {
        if ($pageVars["rackspaceResult"]->status == "already-exists") {
            echo "Requested domain {$pageVars["rackspaceResult"]->requested} found with id {$pageVars["rackspaceResult"]->domain->id}" ; }
        else {
            echo "Requested domain {$pageVars["rackspaceResult"]->requested} not found, so was created." ; } }
    else {
        echo "No Object."; } }
else if ($pageVars["route"]["action"]=="ensure-record-exists") {
    if (is_object($pageVars["rackspaceResult"])) {
        if ($pageVars["rackspaceResult"]->status == "already-exists") {
            echo "Requested domain record {$pageVars["rackspaceResult"]->requested_type} {$pageVars["rackspaceResult"]->requested_name} {$pageVars["rackspaceResult"]->requested_data} found with id {$pageVars["rackspaceResult"]->record->id}" ; }
        else {
            echo "Requested domain record {$pageVars["rackspaceResult"]->requested_type} {$pageVars["rackspaceResult"]->requested_name} {$pageVars["rackspaceResult"]->requested_data} not found, so was created." ; } }
    else {
        echo "No Object."; } }
else if ($pageVars["route"]["action"]=="list-domains") {
    if (is_array($pageVars["rackspaceResult"])) {
        foreach ($pageVars["rackspaceResult"] as $domain) {
            echo "\n" ;
            echo "Name: ".$domain->name."\n";
            echo "ID: ".$domain->id."\n"; } } }
else if ($pageVars["route"]["action"]=="list-records") {
    if (is_array($pageVars["rackspaceResult"])) {
        foreach ($pageVars["rackspaceResult"] as $domainName => $domainOfRecords) {
            echo "Domain: $domainName\n" ;
            foreach ($domainOfRecords as $domainRecord) {
                echo "  Record:\n" ;
                echo "    Type: {$domainRecord->type}\n" ;
                echo "    Name: {$domainRecord->name}\n" ;
                echo "    ID: {$domainRecord->id}\n" ;
                echo "    TTL: {$domainRecord->ttl}\n" ;
                echo "    Data: {$domainRecord->data}\n" ; } } } }

?>

------------------------------
Rackspace DNS API Finished