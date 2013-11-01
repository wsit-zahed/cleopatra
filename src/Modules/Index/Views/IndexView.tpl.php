Cleopatra by Golden Contact Computing
-------------------

About:
-----------------

This tool is for provisioning software and configurations to your boxes. You can set up complex provisions to your
systems with one or two PHP files, or quickly set up cloud friendly deployment patterns.

Cleopatra is extremely extendable, you can pretty easily write your own module that will configure your prod servers
the way that you want them - and then use that as you will get the same automation benefits along with the security
and infrastructure benefits of doing it correctly for your own setup

-------------------------------------------------------------

Available Commands:
---------------------------------------

<?php
foreach ($pageVars["modulesInfo"] as $moduleInfo) {
  if ($moduleInfo["hidden"] != true) {
    echo $moduleInfo["command"].' - '.$moduleInfo["name"]."\n";
  }
}

?>