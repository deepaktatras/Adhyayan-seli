<?php
/* Backup to GoogleDrive example script
   Copyright (C) 2013 Matthew Hipkin <http://www.matthewhipkin.co.uk>

   settings.inc.php
   Settings required for script execution */

  // User home directory (absolute)  
   //$homedir = trim(shell_exec("cd ~ && pwd"))."/";  // If this doesn't work, you can provide the full path yourself
  $homedir="C:\\wamp\\bin\\mysql\\mysql5.6.17\\bin\\";
  // Site directory (relative)
 // $sitedir = dirname( __FILE__)."\\";//."\\dump.sql";//"www/"; 
  // $sitedir = dirname( __FILE__);
  //$sitedir = "/home/algoinsighttest/public_html/projects/adhyayan_testing/adhyayanReloaded/backup-to-googledrive/";
  $sitedir = "/var/www/Adhyayan-app/temp/";
  // Base filename for backup file
  $fprefix = "AWS-sitebackup-";
  // Base filename for database file
  $dprefix = "AWS-dbbackup-";
  // MySQL username
  //$dbuser = "techPHP";
  $dbuser = "production";
  // MySQL password
  //$dbpass = "@PPl!c@Tion";
  //$dbname = "adhyayan_prod";
  
  $dbpass = "khk86jgjgjg876*%gj3";
  $dbname = "adhyayan_production";

  // Google Drive Client ID
  $clientId = "419988143343-6f65d61al0r07ee7gja8r8ogb9b6l93f.apps.googleusercontent.com";//"822104352970-8n0jeuhtqb49bkr8jhi2p8fola6jcei4.apps.googleusercontent.com"; // Get this from the Google APIs Console https://code.google.com/apis/console/
  // Google Drive Client Secret
  $clientSecret = "tMwVa--VSmskY4EuiyFW8zMi";//"rIJAJY_bwMhymUDRCJgukKxF"; // Get this from the Google APIs Console https://code.google.com/apis/console/
  // Google Drive authentication code
  $authCode = "4/P97K6Aleruh8Gr2JX3iT5simciaZ5npUpx6zol69EkA"; // Needs to be set using getauthcode.php first!   
  //$authCode = "4/5htkngxwPkz8slJDlOhKTUdgkTxlweXNdMK8jQO4oJ0";
    
?>
