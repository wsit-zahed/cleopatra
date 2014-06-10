<?php

if (is_object($pageVars["vSphereResult"]) || is_array($pageVars["vSphereResult"])) {

    foreach ($pageVars["vSphereResult"] as $key => $resEntry) {
        echo "Object $key\n" ;
        echo "  Name: ".$resEntry->obj->_."\n" ;
        echo "  Type: ".$resEntry->obj->type."\n" ;
        echo "    Properties:\n" ;
        foreach ($resEntry->propSet as $entry) {
            echo "      ".$entry->name." = ".$entry->val."\n" ; }
    }

//    $arrayObject = new \ArrayObject($pageVars["vSphereResult"]);
//    $outVar = "" ;
//    foreach ($arrayObject as $arrayObjectKey => $arrayObjectValue) {
//        $outVar .= "$arrayObjectKey: $arrayObjectValue\n"; }
//    echo $outVar."\n" ;

}
else {
    echo "There was a problem retrieving Data. No results were found"; }
?>

------------------------------
VMWare VSphere Listing Finished