<?php

if (is_object($pageVars["vSphereResult"]) || is_array($pageVars["vSphereResult"])) {
    $arrayObject = new \ArrayObject($pageVars["vSphereResult"]);
    $outVar = "" ;
    foreach ($arrayObject as $arrayObjectKey => $arrayObjectValue) {
        $outVar .= "$arrayObjectKey: $arrayObjectValue\n"; }
    echo $outVar."\n" ; }
else {
    echo "There was a problem retrieving Data. No results were found"; }
?>

------------------------------
VMWare VSphere Listing Finished