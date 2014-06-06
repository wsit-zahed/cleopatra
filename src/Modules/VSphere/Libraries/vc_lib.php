<?php

function Find($RootFolder, $ObjectType, $Details)
{
	
	// $RootFolder="group-d1";
	
	$soapmsg ="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><SOAP-ENV:Envelope SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\"><SOAP-ENV:Body>";
	
	$soapmsg.="<RetrieveProperties xmlns=\"urn:vim25\">";
	$soapmsg.="<_this type=\"PropertyCollector\">propertyCollector</_this>";
	$soapmsg.="<specSet>";
	$soapmsg.=" <propSet>";
	$soapmsg.="  <type>$ObjectType</type>";   // Specify Object Type

        $count = count($Details);
        for ($i=0; $i < $count; $i++)
          {
   	   $soapmsg.="  <pathSet>" . $Details[$i] . "</pathSet>";
 	  }
        $soapmsg.=" </propSet>";
	$soapmsg.="<objectSet>";
	$soapmsg.="<obj type=\"Folder\">$RootFolder</obj>";  // Specify RootFolder
	$soapmsg.="<selectSet xsi:type=\"TraversalSpec\">";
	$soapmsg.="<name>folderTraversalSpec</name>";
	$soapmsg.="<type>Folder</type>";
	$soapmsg.="<path>childEntity</path>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>folderTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>datacenterHostTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>datacenterVmTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>computeResourceRpTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>computeResourceHostTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>hostVmTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>resourcePoolVmTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet xsi:type=\"TraversalSpec\">";
	$soapmsg.="<name>datacenterVmTraversalSpec</name>";
	$soapmsg.="<type>Datacenter</type>";
	$soapmsg.="<path>vmFolder</path>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>folderTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet xsi:type=\"TraversalSpec\">";
	$soapmsg.="<name>datacenterHostTraversalSpec</name>";
	$soapmsg.="<type>Datacenter</type>";
	$soapmsg.="<path>hostFolder</path>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>folderTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet xsi:type=\"TraversalSpec\">";
	$soapmsg.="<name>computeResourceHostTraversalSpec</name>";
	$soapmsg.="<type>ComputeResource</type>";
	$soapmsg.="<path>host</path>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet xsi:type=\"TraversalSpec\">";
	$soapmsg.="<name>computeResourceRpTraversalSpec</name>";
	$soapmsg.="<type>ComputeResource</type>";
	$soapmsg.="<path>resourcePool</path>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>resourcePoolTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet xsi:type=\"TraversalSpec\">";
	$soapmsg.="<name>resourcePoolTraversalSpec</name>";
	$soapmsg.="<type>ResourcePool</type>";
	$soapmsg.="<path>resourcePool</path>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>resourcePoolTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>resourcePoolVmTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet xsi:type=\"TraversalSpec\">";
	$soapmsg.="<name>hostVmTraversalSpec</name>";
	$soapmsg.="<type>HostSystem</type>";
	$soapmsg.="<path>vm</path>";
	$soapmsg.="<selectSet>";
	$soapmsg.="<name>folderTraversalSpec</name>";
	$soapmsg.="</selectSet>";
	$soapmsg.="</selectSet>";
	$soapmsg.="<selectSet xsi:type=\"TraversalSpec\">";
	$soapmsg.="<name>resourcePoolVmTraversalSpec</name>";
	$soapmsg.="<type>ResourcePool</type>";
	$soapmsg.="<path>vm</path>";
	$soapmsg.="</selectSet>";
	$soapmsg.="</objectSet>";
	$soapmsg.="</specSet>";
	$soapmsg.="</RetrieveProperties>";
	$soapmsg.="</SOAP-ENV:Body></SOAP-ENV:Envelope>";
	
	
	return $soapmsg;

}


function QueryPerf($starttime, $endtime, $interval,$counterID, $ManagedObjectID) {

 // interval defaults are 
 // 300 = once every 5 minutes
 // 1800 = once every 30 minutes
 // etc, check your VC stats settings for your interval counters

 $soapmsg ="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
           <SOAP-ENV:Envelope SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\"
          xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"
          xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"
          xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
          xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\"><SOAP-ENV:Body>";

 $soapmsg.="<QueryPerf xmlns=\"urn:vim25\">";
 $soapmsg.="<_this type=\"PerformanceManager\">PerfMgr</_this>";
 $soapmsg.="<querySpec>";
 $soapmsg.="<entity type=\"ManagedObject\">$ManagedObjectID</entity>";
 $soapmsg.="<startTime>$starttime</startTime>";
 $soapmsg.="<endTime>$endtime</endTime>";
 $soapmsg.="<maxSample>1</maxSample>";
 $soapmsg.="<metricId>";
 $soapmsg.="<counterId>$counterID</counterId>";
 $soapmsg.="<instance></instance>";
 $soapmsg.="</metricId>";
 $soapmsg.="<intervalId>$interval</intervalId>";
// $soapmsg.="<format>csv</format>";
 $soapmsg.="</querySpec>";
 $soapmsg.="</QueryPerf>";
 $soapmsg.="</SOAP-ENV:Body></SOAP-ENV:Envelope>";

 return $soapmsg;
 
}

