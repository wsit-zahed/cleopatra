<?php

if (is_object($pageVars["vSphereResult"]) || is_array($pageVars["vSphereResult"])) {

    // var_dump($pageVars["vSphereResult"]) ;
    // echo $pageVars["vSphereResult"]->propSet[2]->name ;
    // echo $pageVars["vSphereResult"]->propSet[3]->name ;

    $i = 0 ;
    foreach ($pageVars["vSphereResult"]->propSet as $property) {
        if ($property->name == "childEntity") {
            echo "Child Item $i: " ;
            echo "  Name: {$property->val->ManagedObjectReference->_ }\n" ;
            echo "  Type: {$property->val->ManagedObjectReference->type}\n" ;
            echo $property->val->ManagedObjectReference->_ ."\n";
            $i++; } } }

else {
    echo "There was a problem retrieving Data. No results were found"; }
?>

------------------------------
VMWare VSphere Listing Finished