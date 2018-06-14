<?php
global $CFG;

if( empty($CFG) || ! property_exists($CFG, "httpswwwroot") ){
    return;
}

define("ADHYAYAN_APP_URL", rtrim( $CFG->httpswwwroot, 'moodle'));

function sso_check_session() {
    global $USER, $DB, $CFG;

    if (empty($_COOKIE['ADH_TOKEN'])) {
        sso_redirect_to_adhyayan_app_login();
    }

    $tokenArray = explode("--", $_COOKIE['ADH_TOKEN']);

    if (count($tokenArray) < 2 || empty($tokenArray[1])) {
        sso_redirect_to_adhyayan_app_login();
    }

    if( !isloggedin() ) { //if ( empty($USER) || !property_exists($USER, 'email') || empty($USER->email) ) {
        //user not logged in into moodle
        $adhyayan_user = sso_get_current_adhyayan_user( $_COOKIE['ADH_TOKEN'] );
        
        if( empty($adhyayan_user['email']) ) {
            //no active session in Adhyayan or invalid cookiee
            sso_redirect_to_adhyayan_app_login();
        }
        
        $moodle_user = $DB->get_record('user', array('email' => $adhyayan_user['email']) );
        
        if( empty( $moodle_user ) ) {//die('need to register');
            require_once($CFG->dirroot.'/user/profile/lib.php');
            require_once($CFG->dirroot.'/user/lib.php');
            //user session exists in adhyayan app but that user does not exists in moodle, register him
            $nameArray = explode(" ", $adhyayan_user['name']);
            $pwd = sso_random_password();
            $user = array(
                'username' => $adhyayan_user['email'],
                'email' => $adhyayan_user['email'],
                'firstname' => array_shift($nameArray),
                'lastname' => is_array($nameArray) ? implode(" ", $nameArray) : '',
                "password" => md5($pwd),
                'confirmed' => 1
            );
            $user = (object) $user;
            $authplugin = get_auth_plugin('email');
            
            $user->id = user_create_user($user, false, false);
            user_add_password_history($user->id, $pwd);
            //if ( $authplugin->user_signup((object) $user, $notify=FALSE) ) {
            if( $user->id ){
                //profile_save_data($user);
                redirect(new moodle_url('/'));
                exit();
            } else {
                //some issues while creating a new user
                redirect(ADHYAYAN_APP_URL);
            }
        } else {
            //user session exists in Adhyayan app but not in Moodle so create user session in moodle
            $user = get_complete_user_data('id', $moodle_user->id);
            complete_user_login($user);
            redirect(new moodle_url('/'));
            exit();
        }
        
        
    } else if ( md5(strtolower($USER->email)) != $tokenArray[1] ) {
        // users are different, logout current moodle user
        require_logout();
        redirect(new moodle_url('/'));
    } else {
        return TRUE;
    }
}

function sso_get_current_adhyayan_user( $token ) {
    
    $res = @json_decode( sso_curl_call( ADHYAYAN_APP_URL . "index.php?controller=api&action=getCurrentUser", array("token"=>$token) ), TRUE);
    
    if( empty($res['status']) || $res['status'] != 1 ) {
        
        return FALSE;
        
    } else {
        
        return $res['data'];
        
    }
}

function sso_curl_call($url, $data = array(), $requestType = 'post') {
    $flag = false;
    $elements = '';
    foreach ($data as $name => $value) {
        if ($flag)
            $elements.='&';
        $elements.="{$name}=" . urlencode($value);
        $flag = true;
    }
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);

    //curl_setopt($ch, CURLOPT_REFERER, "http://www.google.com/");

    //curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");

    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $elements);
    
    $output = curl_exec($ch);

    curl_close($ch);
    
    return $output;
}

function sso_redirect_to_adhyayan_app_login(){
    redirect( ADHYAYAN_APP_URL . "index.php?controller=login&action=login&redirect=" . urlencode(new moodle_url('/')) );
    exit;
}

function sso_random_password($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $password = substr(str_shuffle($chars), 0, $length);
    return $password;
}

sso_check_session();