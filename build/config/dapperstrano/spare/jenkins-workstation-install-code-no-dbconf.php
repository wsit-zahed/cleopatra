<?php

/*************************************
*      Generated Autopilot file      *
*     ---------------------------    *
*Autopilot Generated By Dapperstrano *
*     ---------------------------    *
*************************************/

Namespace Core ;

class AutoPilotConfigured extends AutoPilot {

    public $steps ;

    public function __construct() {
	    $this->setSteps();
        $this->setVHostTemplate();
    }

    /* Steps */
    private function setSteps() {

	    $this->steps =
	      array(
          array ( "Project" => array(
                    "projectInitializeExecute" => true,
          ) , ) ,
          array ( "HostEditor" => array(
                    "hostEditorAdditionExecute" => true,
                    "hostEditorAdditionIP" => "127.0.0.1",
                    "hostEditorAdditionURI" => "www.alca-enterprise.tld.local",
          ) , ) ,
          array ( "VHostEditor" => array(
                    "virtualHostEditorAdditionExecute" => true,
                    "virtualHostEditorAdditionDocRoot" => "/var/www/jenkins/alca-enterprise",
                    "virtualHostEditorAdditionURL" => "www.alca-enterprise.tld.local",
                    "virtualHostEditorAdditionIp" => "127.0.0.1",
                    "virtualHostEditorAdditionTemplateData" => "",
                    "virtualHostEditorAdditionDirectory" => "/etc/apache2/sites-available",
                    "virtualHostEditorAdditionFileSuffix" => "",
                    "virtualHostEditorAdditionVHostEnable" => true,
                    "virtualHostEditorAdditionSymLinkDirectory" => "/etc/apache2/sites-enabled",
                    "virtualHostEditorAdditionApacheCommand" => "apache2",
          ) , ) ,
              array ( "ApacheControl" => array(
                  "apacheCtlRestartExecute" => true,
              ) , ) ,
	      );

	}


 // This function will set the vhost template for your Virtual Host
 // You need to call this from your constructor
 private function setVHostTemplate() {
   $this->steps[2]["VHostEditor"]["virtualHostEditorAdditionTemplateData"] =
  <<<'TEMPLATE'
 NameVirtualHost ****IP ADDRESS****:80
 <VirtualHost ****IP ADDRESS****:80>
   ServerAdmin webmaster@localhost
 	ServerName ****SERVER NAME****
 	DocumentRoot ****WEB ROOT****src
 	<Directory ****WEB ROOT****src>
 		Options Indexes FollowSymLinks MultiViews
 		AllowOverride All
 		Order allow,deny
 		allow from all
 	</Directory>
   ErrorLog /var/log/apache2/error.log
   CustomLog /var/log/apache2/access.log combined
 </VirtualHost>

 NameVirtualHost ****IP ADDRESS****:443
 <VirtualHost ****IP ADDRESS****:443>
 	 ServerAdmin webmaster@localhost
 	 ServerName ****SERVER NAME****
 	 DocumentRoot ****WEB ROOT****src
     SSLEngine on
     SSLCertificateFile /etc/ssl/certs/ssl-cert-snakeoil.pem
     SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key
   # SSLCertificateChainFile /etc/apache2/ssl/bundle.crt
 	 <Directory ****WEB ROOT****src>
 		 Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		allow from all
	</Directory>
  ErrorLog /var/log/apache2/error.log
  CustomLog /var/log/apache2/access.log combined
  </VirtualHost>
TEMPLATE;
}


}
