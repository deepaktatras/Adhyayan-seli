<?php

define("DEVELOPMENT_ENVIRONMENT",true);

if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false){
	define("DB_USER","root");
	define("DB_PASSWORD","");
	define("DB_NAME","adh");
	define("DB_HOST","localhost");
	define("SITEURL","http://".$_SERVER['HTTP_HOST']."/Adhyayan/"); 

}else if(DEVELOPMENT_ENVIRONMENT){
	define("DB_USER","adhyayan_s_app");
	define("DB_PASSWORD","ckLn5Ub_v_%k");
	define("DB_NAME","adhyayan_staging_app_adhyayan_reloaded");
	define("DB_HOST","localhost");
	define("SITEURL","http://staging-app.adhyayan.asia/");  

}else{	
	define("DB_USER","techPHP");
	define("DB_PASSWORD","@PPl!c@Tion");
	define("DB_NAME","adhyayan_prod");
	define("DB_HOST","localhost");
	define("SITEURL","http://app.adhyayan.asia/");
}


define("TOKEN_LIFE",12000); //in Seconds
define("INDIAN_SCHOOL_BORAD",101); 
define("DEFAULT_COUNTRY_ID",101); 
define("DEFAULT_DIAGNOSTIC",2); 
define("DEFAULT_TIER",3); 
define("DEFAULT_AWARD_STANDARD",1);
define("DEFAULT_AQS_ROUND",1);
define("RATING_OTHERS_ID",39);
define('KEY_BEHAVIOURS','Objectivity,Positivity,Maintaining confidentiality');
define('CLASSROOM_OBSERVATION','Quietly find a seat or space at the back of the room to observe~Spend between 5 to 7 minutes in a class');
define('STAKEHOLEDER','Introduce yourself, the purpose of the review and share that all responses will be confidential~Ask for examples and documentation to verify the information~Adapt language to suit the stakeholder being interviewed');
define('SCHOOL_RATING','The meetings are irregular~There is some contribution to events from the teachers, and there is some documented evidence of opportunities to meet.~The meeting minutes indicate that meetings are directed by the Principal');

date_default_timezone_set("Asia/Kolkata");
define("DEFAULT_STUDENT_PROFILE_ATTRIBUTE",49);
define('OFFLINE_STATUS', FALSE); // false=> offline sync code will not be executed
define("FEEDBACK_ROLES",'8,1,2');
define("DEFAULT_LANGUAGE",'9');
define("LAST_REMINDERS_DAYS",'12');
define("REIMBURSEMENT_SHEET_DAYS",'12');
//session_save_path(ROOT.'tmp'.DS.'sessions');
//session_start();

//define("FEEDBACK_ROLES",'8,1,2');