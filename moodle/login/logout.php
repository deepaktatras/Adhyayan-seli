<?php

require('../config.php');

sso_check_session();

redirect( ADHYAYAN_APP_URL . "index.php?controller=login&action=logout" );
