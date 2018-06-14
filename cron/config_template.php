<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define("DEVELOPMENT_ENVIRONMENT",false);
define("STAGING_ENVIRONMENT",false);

if(DEVELOPMENT_ENVIRONMENT){
	define("DB_USER","root");
	define("DB_PASSWORD","");
	define("DB_NAME","adh");
	define("DB_HOST","localhost");
	define("SITEURL","http://localhost/Adhyayan/");         
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

}else if(STAGING_ENVIRONMENT){
/*	define("DB_USER","adhyayan_s_app");
	define("DB_PASSWORD","ckLn5Ub_v_%k");
	define("DB_NAME","adhyayan_staging_app_adhyayan_reloaded");
	define("DB_HOST","localhost");
	define("SITEURL","http://staging-app.adhyayan.asia/");        
        error_reporting(E_ALL);
        ini_set('display_errors', 1);*/

		define("DB_USER","techPHP");
        define("DB_PASSWORD","@PPl!c@Tion");
        define("DB_NAME","adhyayan_prod");
        define("DB_HOST","localhost");
        define("SITEURL","http://stage.app.adhyayan.asia/");

}else{	
	define("DB_USER","techPHP");
	define("DB_PASSWORD","@PPl!c@Tion");
	define("DB_NAME","adhyayan_prod");
	define("DB_HOST","localhost");
	define("SITEURL","http://app.adhyayan.asia/");
}
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__).DS.'../');
//define("DEVELOPMENT_ENVIRONMENT",true);
 //require_once  ''.'application' . DS . 'helpers' . DS . "functionshelper.php" ;
require_once ( ROOT.'application' . DS .'helpers'.DS. 'functionshelper.php');

define("LAST_REMINDERS_DAYS",'12');
define("REIMBURSEMENT_SHEET_DAYS",'11');
define("SHEET_TO_EMAIL",'ujwala.punjabi@adhyayan.asia');
define("SHEET_TO_NAME",'Ujwala');
define("LAST_REIMBURSEMENT_SHEET_DAYS",'9');