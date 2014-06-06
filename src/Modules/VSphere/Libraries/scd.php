<?php
class soapclientd extends soapclient
{
public $action = false;

public function __construct($wsdl, $options = array())
{
parent::__construct($wsdl, $options);
}

public function __doRequest($request, $location, $action, $version, $one_way = 0)
{
//        echo '<pre>' . htmlspecialchars(str_replace(array ('<ns', '></'), array (PHP_EOL . '<ns', '>'.PHP_EOL.'</'), $request)) . '</pre>';
$resp = parent::__doRequest($request, $location, $action, $version, $one_way);
return $resp;
}

}