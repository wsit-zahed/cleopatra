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
 //$objecttype = "ClusterComputeResource";
 //$objecttype = "HostSystem";
 $objecttype = "VirtualMachine";
?>

<html>
<head>
 <title>List VMs</title>
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
 
 $RootFolder=$rootFolder;  // this was collected with the RetrieveServiceContent API Call

// Do a Retrieve Properties with extensive traversal to find anything.
 $soapmsg = Find($RootFolder,$objecttype,array("name","runtime"));  
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

 // List VMs
 for ($o=0; $o<$totalobjects; $o++) {

  // check how many fields are returned, if only one, place in array 
  // so it can be uniformly be treated
  if (is_array($objects[$o][propSet][0]) !=1) {
    $objects[$o][propSet][0] = $objects[$o][propSet];
    $totalfields = 1;
  }
  else 
  {
    $totalfields = count($objects[$o][propSet]);
  }

  //convert each field to a VM variable using the right field name
  unset($vm);
  for ($fields=0; $fields<$totalfields; $fields++) {
    $vm[$objects[$o][propSet][$fields][name]] = $objects[$o][propSet][$fields][val];
  }

  $MOI = $objects[$o][obj];
  print ("Object : ". $MOI . " - " . $vm[name] . "<br>");
  print ("PowerState: " . $vm[runtime][powerState] . "<br>");
  print ("Running on host: " . $vm[runtime][host] . "(Managed Object Reference)<br>");
  print ("<br>");
 }


?>
</body>
</html>

