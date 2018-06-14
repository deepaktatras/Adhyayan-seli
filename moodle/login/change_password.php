<?php

require('../config.php');

sso_check_session();

require_once($CFG->dirroot.'/user/lib.php');
//require_once('change_password_form.php');
require_once($CFG->libdir.'/authlib.php');

$userauth = get_auth_plugin($USER->auth);

$new_password = sso_random_password();

if (!$userauth->user_update_password($USER, $new_password)) {
    print_error('errorpasswordupdate', 'auth');
}

user_add_password_history($USER->id, $new_password);

if (!empty($CFG->passwordchangelogout)) {
    \core\session\manager::kill_user_sessions($USER->id, session_id());
}

// Reset login lockout - we want to prevent any accidental confusion here.
login_unlock_account($USER);

// register success changing password
unset_user_preference('auth_forcepasswordchange', $USER);
unset_user_preference('create_password', $USER);

redirect(new moodle_url('/'));