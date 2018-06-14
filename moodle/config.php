<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'adhyayan_moodle';
$CFG->dbuser    = 'root';
$CFG->dbpass    = '';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
);

$CFG->wwwroot   = 'http://localhost/Adhyayan/adhyayanReloaded/moodle';
$CFG->dataroot  = "D:\\xampp\\htdocs\\moodleData";
$CFG->admin     = 'admin';
/*$CFG->theme     = 'standard';*/

$CFG->directorypermissions = 0777;

require_once(dirname(__FILE__) . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
error_reporting(E_ALL);
        ini_set('display_errors', 'On');
require(dirname(__FILE__) . '/sso_files/init.php');
