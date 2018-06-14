<?php
define("MESSAGE_GUEST",'This is a guest log-in and we have provided access to only 1 Key Performance Area for your reference.Contact School Review Programme Lead, Poonam Choksi on 9773187331 for more details. You can even email her at poonam.choksi@adhyayan.asia');

define("AWS_VERSION","latest");
define("AWS_REGION","ap-south-1");

if(DEVELOPMENT_ENVIRONMENT){
define ("MOODLE_URL","http://".$_SERVER['HTTP_HOST']."/Adhyayan-Moodle");
define ("MOODLE_API_KEY","84b1c8a6d0e16ff1ae18e10bb8d9b95f");
define("KEYS_PATH","".$_SERVER['DOCUMENT_ROOT']."/jwt-keys/");
define("COOKIE_GEN",1);

define("COOKIE_DOMAIN","");

define("AWS_KEY",'AKIAJWJBQ3VENZG2FYZQ');
define("AWS_SECRET",'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');
define("AWS_BUCKET",'adhyayan-uploads-dev');

define ("UPLOAD_PATH","uploads/");

define ("UPLOAD_URL","https://s3.".AWS_REGION.".amazonaws.com/".AWS_BUCKET."/uploads/");

define ("UPLOAD_PATH_RESOURCE","resources/");
define ("UPLOAD_URL_RESOURCE","https://s3.".AWS_REGION.".amazonaws.com/".AWS_BUCKET."/resources/");
define ("UPLOAD_PATH_DIAGNOSTIC","diagnostic/");
define ("UPLOAD_URL_DIAGNOSTIC","https://s3.".AWS_REGION.".amazonaws.com/".AWS_BUCKET."/diagnostic/");
define("DOWNLOAD_DIAGNOSTIC","uploads/wordFile/");
define("DOWNLOAD_CHART_URL","http://13.127.82.199:7801/");
define("CHART_URL_GENERATE","../node-export-server/tmp/"); //URL to delete highcharts image
}else if(STAGING_ENVIRONMENT){
define ("MOODLE_URL","http://stage.moodle.adhyayan.asia");
define ("MOODLE_API_KEY","84b1c8a6d0e16ff1ae18e10bb8d9b95f");
define("KEYS_PATH","".$_SERVER['DOCUMENT_ROOT']."/../jwt-keys/"); 
define("COOKIE_GEN",1);
define("COOKIE_DOMAIN",".adhyayan.asia");

define("AWS_KEY",'AKIAJDLMSOR6YTL6LHLA');
define("AWS_SECRET",'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');
define("AWS_BUCKET",'adhyayan-uploads-stage');


define ("UPLOAD_PATH","uploads/");

define ("UPLOAD_URL","https://s3.".AWS_REGION.".amazonaws.com/".AWS_BUCKET."/uploads/");

define ("UPLOAD_PATH_RESOURCE","resources/");
define ("UPLOAD_URL_RESOURCE","https://s3.".AWS_REGION.".amazonaws.com/".AWS_BUCKET."/resources/");
define ("UPLOAD_PATH_DIAGNOSTIC","diagnostic/");
define ("UPLOAD_URL_DIAGNOSTIC","https://s3.".AWS_REGION.".amazonaws.com/".AWS_BUCKET."/diagnostic/");
define("DOWNLOAD_DIAGNOSTIC","uploads/wordFile/");
define("DOWNLOAD_CHART_URL","http://13.127.82.199:7801/");
define("CHART_URL_GENERATE","../node-export-server/tmp"); //URL to delete highcharts image
}else{
define ("MOODLE_URL","http://moodle.adhyayan.asia");
define ("MOODLE_API_KEY","422c1c9fc3b6dc0e8ad7e6852e2f9b37");
define("KEYS_PATH","".$_SERVER['DOCUMENT_ROOT']."/../jwt-keys/");

define("COOKIE_GEN",1);

define("COOKIE_DOMAIN",".adhyayan.asia");

define("AWS_KEY",'AKIAISPRYZYTUYFMB5OQ');
define("AWS_SECRET",'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');
define("AWS_BUCKET",'adhyayan-uploads-prod');


define ("UPLOAD_PATH","uploads/");

define ("UPLOAD_URL","https://s3.".AWS_REGION.".amazonaws.com/".AWS_BUCKET."/uploads/");

define ("UPLOAD_PATH_RESOURCE","resources/");
define ("UPLOAD_URL_RESOURCE","https://s3.".AWS_REGION.".amazonaws.com/".AWS_BUCKET."/resources/");
define ("UPLOAD_PATH_DIAGNOSTIC","diagnostic/");
define ("UPLOAD_URL_DIAGNOSTIC","https://s3.".AWS_REGION.".amazonaws.com/".AWS_BUCKET."/diagnostic/");   
define("DOWNLOAD_DIAGNOSTIC","uploads/wordFile/");  //to save diagnostic in word
define("DOWNLOAD_CHART_URL","http://13.127.82.199:7801/"); //URL to save highcharts image
define("CHART_URL_GENERATE","../node-export-server/tmp/"); //URL to delete highcharts image
}

//define("TOKEN_LIFE_REFRESH",1800); //in Seconds
define("TOKEN_LIFE_REFRESH",((TOKEN_LIFE/2)-2)); //in Seconds
$privateKey= file_get_contents(''.KEYS_PATH.'privkey.pem');

define ("PRIVATEKEY",$privateKey);
$publicKey= file_get_contents(''.KEYS_PATH.'pubkey.pem');

define ("PUBLICKEY",$publicKey);

define ("RATINGS","1,2,3,4");