<?php
 // Example PHP code to get Performance Statistics from a VMware Vsphere Server (v5.x)
 // Written by: Richard Garsthagen - the.anykey@gmail.com - www.run-virtual.com
 // 
 // This code uses the nusoap library, not the buildin PHP soap library.

 $debug = false;  // set to true is you want raw results to be displayed
 $ServerIP = "192.168.1.1"; // Your VC IP address or dns name 
 $username = "your_username";
 $password = "your_password";

 // choose which objects you want to query by unremarking that line.
 $objecttype = "ClusterComputeResource";
 //$objecttype = "HostSystem";
 //$objecttype = "VirtualMachine";
?>

<html>
<head>
 <title>Example of performance stats</title>
</head>
<body>

<?php
 require_once("lib/nusoap.php");
 require_once("vc_lib.php");
 $myconnection = new nusoap_client("https://".$ServerIP."/sdk"); // Change to your own server!
 $namespace = "urn:vim25";
 $soapmsg[data] = new soapval('_this','ServiceInstance','ServiceInstance');
 $result = $myconnection->call("RetrieveServiceContent",$soapmsg,$namespace);

 if ($debug) {
  print ("<br><textarea cols=80 rows=40>");
  print_r($result);
  print ("</textarea><br>");
 }

 print ("<br>");
 print "Connected to version: " . $result[about][fullName] . "<br>";
 $rootFolder = $result[rootFolder];
 unset($soapmsg);

 $soapmsg[this] = new soapval('_this','SessionManager','SessionManager');
 $soapmsg[userName] = $username;
 $soapmsg[password] = $password;
 $result = $myconnection->call("Login",$soapmsg,$namespace);

 if ($debug) {
  print ("<br><textarea cols=80 rows=20>");
  print_r($result);
  print ("</textarea><br>");
 }
 
 //Let's use the login time returned from the server as the end time
 //for performance stats and calculate based on this time 30 minutes back as start time.
 $t = $result[loginTime];
 print ("Login time from server is: $t <br>");
 $d1 = DateTime::createFromFormat('Y-m-d H:i:s',substr($t,0,10)." ".substr($t,11,8) );
 date_sub($d1, date_interval_create_from_date_string('30 minutes'));
 $st = $d1->format("Y-m-d") . "T" . $d1->format("H:i:s") . ".0000" . substr($t,24,3);
 print ("Calculated time minus 30 minutes is: $st<br><br>");
 unset($soapmsg);

 $RootFolder=$rootFolder;  // this was collected with the RetrieveServiceContent API Call
 $soapmsg = Find($RootFolder,$objecttype,array("name"));  // Do a Retrieve Properties with extensive traversal to find anything.
 $result = $myconnection->send($soapmsg,30);
 if ($debug) {
  print ("<br><textarea cols=80 rows=20>");
  print_r($result);
  print ("</textarea><br>");
 }
 unset($soapmsg);

 // if a single object is returned place this back into the object as array at index 0 
 // so we have a uniform way to process the data with one or multiple objects
 $objects=$result[returnval];
 if ($debug) {
  print ("Return is array : ". is_array($objects[0]) . "<br>");
 }
 if (is_array($objects[0]) != 1) { 
   $objects[0] = $objects; 
   $totalobjects = 1;
  }
 else
  { 
    $totalobjects = count($objects);
  }

 // for each object get the performance data
 for ($o=0; $o<$totalobjects; $o++) {

 $MOI = $objects[$o][obj];
 print ("<b>Object : ". $MOI . " - " . $objects[$o][propSet][val] . "</b>");

 $soapmsg = QueryPerf($st,$t,300,2,$MOI);
 $result = $myconnection->send($soapmsg,30);

 if ($debug) {
  print ("<br><textarea cols=80 rows=20>");
  print_r($result);
  print ("</textarea><br>");
 }

 $a = $result[returnval][value][value];
 $b = count($a) -1;

 print ("<br>");
 for ($i=0; $i < $b+1; $i++) {
  print $result[returnval][sampleInfo][$i][timestamp] . " - ";
  print ($a[$i]/100) . "%<br>";
 }

 print "<b>Current average CPU is: " . ($a[$b]/100) . "%</b><br><br>";

 }


?>
</body>
</html>

