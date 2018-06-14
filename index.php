<?php
global $start_time, $started_memory;
$start_time=microtime(true);
$started_memory = array("memory_usage_real_usage_off" => memory_get_usage(false),"memory_usage_real_usage_on" => memory_get_usage(true));
$web_down=0;
$web_down_allow_ips=array('127.0.0.1','180.151.85.178','103.48.109.90');
$web_template='web_maintenance.php';
$web_maintenance_msg='Adhyayan software is under service and will be functional by 26-Dec-2017 17:00 HRS IST';
$current_ip=$_SERVER['REMOTE_ADDR'];
if($web_down==1 && !in_array($current_ip,$web_down_allow_ips)){
require_once($web_template);    
die();
}

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__).DS);

require_once (ROOT . 'config' . DS . 'config.php');
require_once (ROOT . 'config' . DS . 'config_var.php');
require_once (ROOT . 'library' . DS . 'shared.php');
