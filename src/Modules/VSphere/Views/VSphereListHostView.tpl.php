<?php

if (is_object($pageVars["vSphereResult"]) || is_array($pageVars["vSphereResult"])) {

    var_dump($pageVars["vSphereResult"]) ;

}
else {
    echo "There was a problem retrieving Data. No results were found"; }
?>

------------------------------
VMWare VSphere Listing Finished