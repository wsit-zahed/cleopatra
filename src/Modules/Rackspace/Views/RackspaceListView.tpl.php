<?php
if (is_array($pageVars["rackspaceResult"])) {
    $outVar = "" ;
    $resultKeys = array_keys($pageVars["rackspaceResult"]);
    $resultKey = $resultKeys[0];
    // var_dump($pageVars["rackspaceResult"]);
    if ($resultKey == "status") {
        echo $resultKey.": {$pageVars["rackspaceResult"][$resultKey]}\n";  }
    else if ($resultKey == "servers") {
        foreach($pageVars["rackspaceResult"][$resultKey] as $serverEntry) {
            $outVar .= "id - ".$serverEntry->id.", ";
            $outVar .= "name - ".$serverEntry->name.", ";
            $outVar .= "image_id - ".$serverEntry->image_id.", ";
            $outVar .= "size_id - ".$serverEntry->size_id.", ";
            $outVar .= "region_id - ".$serverEntry->region_id.", ";
            $outVar .= "backups_active - ".$serverEntry->backups_active.", ";
            $outVar .= "ip_address - ".$serverEntry->ip_address.", ";
            $outVar .= "private_ip_address - ".$serverEntry->private_ip_address.", ";
            $outVar .= "locked - ".$serverEntry->locked.", ";
            $outVar .= "status - ".$serverEntry->status.", ";
            $outVar .= "created_at - ".$serverEntry->created_at;
            $outVar .= "\n" ; } }
    else if ($resultKey == "sizes") {
        foreach($pageVars["rackspaceResult"][$resultKey] as $sizeEntry) {
            $outVar .= "id - ".$sizeEntry->id.", ";
            $outVar .= "name - ".$sizeEntry->name.", ";
            $outVar .= "memory - ".$sizeEntry->ram.", ";
            $outVar .= "cpu count - ".$sizeEntry->vcpus.", ";
            $outVar .= "disk - ".$sizeEntry->disk.", ";
            $outVar .= "\n" ; } }
    else if ($resultKey == "images") {
        foreach($pageVars["rackspaceResult"][$resultKey] as $imageEntry) {
            $outVar .= "name - ".$imageEntry->name.", ";
            $outVar .= "id - ".$imageEntry->id ;
            $outVar .= "\n" ; } }
    else if ($resultKey == "domains") {
        foreach($pageVars["rackspaceResult"][$resultKey] as $domainEntry) {
            $outVar .= "id - ".$domainEntry->id.", ";
            $outVar .= "name - ".$domainEntry->name.", ";
            $outVar .= "ttl - ".$domainEntry->ttl.", ";
            $outVar .= "live_zone_file - ".$domainEntry->live_zone_file.", ";
            $outVar .= "zone_file_with_error - ".$domainEntry->zone_file_with_error ;
            $outVar .= "\n" ; } }
    else if ($resultKey == "regions") {
        foreach($pageVars["rackspaceResult"][$resultKey] as $regionEntry) {
            $outVar .= "id - ".$regionEntry->id.", ";
            $outVar .= "name - ".$regionEntry->name.", ";
            $outVar .= "slug - ".$regionEntry->slug;
            $outVar .= "\n" ; } }
    else if ($resultKey == "ssh_keys") {
        foreach($pageVars["rackspaceResult"][$resultKey] as $sshKeyEntry) {
            $outVar .= "id - ".$sshKeyEntry->id.", ";
            $outVar .= "name - ".$sshKeyEntry->name ;
            $outVar .= "\n" ; } }
    echo $resultKey.":\n";
    echo $outVar."\n" ; }
else {
    echo "There was a problem listing Data. No results were found"; }
?>

------------------------------
Rackspace Listing Finished