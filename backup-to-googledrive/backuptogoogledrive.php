<?php
/* Backup to GoogleDrive example script
   Copyright (C) 2013 Matthew Hipkin <http://www.matthewhipkin.co.uk>

   backuptogoogledrive.php
   Main script file which creates gzip files and sends them to GoogleDrive */
   
  set_time_limit(0);
  ini_set("memory_limit",'512M');
  require_once("google-api-php-client/src/Google_Client.php");
  require_once("google-api-php-client/src/contrib/Google_DriveService.php");
  include("settings.inc.php");
  
  if($authCode == "") {
     // print_r($_POST);
      //print_r($_SERVER);
      die("You need to run getauthcode.php first!\n\n");
  }
   if(file_exists($sitedir.$dprefix.$uid.".sql")){
  @unlink($sitedir.$dprefix.$uid.".sql");}
  
  /* PREPARE FILES FOR UPLOAD */
   //$output = system("mysqldump $dbname -u $dbuser > $sitedir");
  //print_r($output);
  
  // Use the current date/time as unique identifier
  $uid = date("YmdHis");
 //print_r($homedir);
 //die;
  // Create tar.gz file
// shell_exec("cd ".$homedir." && tar cf - ".$sitedir." -C ".$homedir." | gzip -9 > ".$homedir.$fprefix.$uid.".tar.gz");
  // Dump datamabase
   //$output=shell_exec("C:\\wamp\\bin\\mysql\\mysql5.6.17\\bin\\mysqldump $dbname -u $dbuser > $sitedir");
  //$output=shell_exec("C:\\wamp\\bin\\mysql\\mysql5.6.17\\bin\\mysqldump -u".$dbuser." ".$dbname." > ".$homedir.$dprefix.$uid.".sql");
 $output = shell_exec("mysqldump --routines $dbname -u $dbuser -p'".$dbpass."'> $sitedir".$uid.".sql");
  //shell_exec("gzip ".$homedir.$dprefix.$uid.".sql");
  //$output = system("mysqldump $dbname -u $dbuser > $sitedir".$fprefix.$uid.".sql");
  //shell_exec("gzip ".$homedir.$dprefix.$uid.".sql");
  //print($sitedir);
  print_r($output);
  //die;
  /* SEND FILES TO GOOGLEDRIVE */
  
  $client = new Google_Client();
  // Get your credentials from the APIs Console
  $client->setClientId($clientId);
  $client->setClientSecret($clientSecret);
  $client->setRedirectUri("urn:ietf:wg:oauth:2.0:oob");
  $client->setScopes(array("https://www.googleapis.com/auth/drive"));
  $service = new Google_DriveService($client);  
  // Exchange authorisation code for access token
  
  if(!file_exists("token.json")) {
    // Save token for future use
    $accessToken = $client->authenticate($authCode);      
    file_put_contents("token.json",$accessToken);  
  }
  else $accessToken = file_get_contents("token.json");
  
  $client->setAccessToken($accessToken);  
  // Upload file to Google Drive  
  $file = new Google_DriveFile();
  $file->setTitle($fprefix.$uid.".sql");
  $file->setDescription("Server backup file");
  $file->setMimeType("application/octet-stream");
  $data = file_get_contents($sitedir.$uid.".sql");
  //$data = file_get_contents($sitedir.$fprefix.$uid.".tar.gz");
  $createdFile = $service->files->insert($file, array('data' => $data, 'mimeType' => "application/octet-stream",));
  unlink($sitedir.$uid.".sql")  
?>
