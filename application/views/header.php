<?php $disableForSelfReview = (( $user['is_web'] == 1 && (in_array(6, $user['role_ids']) || in_array(5, $user['role_ids']))) && $user['has_view_video'] == 0) ? 'disabled=disabled style="pointer-events:none;"' : ''; ?>
<!DOCTYPE html>
<html lang="en" ng-app="app">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>Adhyayan</title>
        <!--[if lt IE 9]>
     <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
     <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
   <![endif]-->
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo SITEURL; ?>favicon.ico">
        <?php echo $addToHeader; ?>
        <script type="text/javascript">
<?php
//print_r($user);
if (in_array(8, $user['role_ids'])) {
    ?>
                $(document).ready(function () {
                    alertCounts();
                });
    <?php }
?>
<?php $assessor_profile=in_array(4, $user['role_ids'])?(isset($user['assessor_profile']) && $user['assessor_profile']==1)?1:0:1 ?>
    
    </script>
    </head>
    <body>
        <header>
            <section class="nuiHeader">
                <div class="container clearfix"> 
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#hdrTopLinks" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="navbar-brand logo"><a href="<?php echo SITEURL; ?>"><img src="<?php echo SITEURL; ?>public/images/logo.png" alt="Logo - Adhyayan"></a></div>
                    </div>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="hdrTopLinks">
                        <ul class="nav navbar-nav hdrTopNav">
                            <li class="http://adhyayan.asia/site/category/blog/"><a href="#">Adhyayan Blog</a></li>
                            <li><a href="http://adhyayan.asia/site/ssre-image-gallery/">Gallery</a></li>
                            <li><a href="http://adhyayan.asia/site/downloads/">Downloads</a></li>
                            <li><a href="http://adhyayan.asia/site/job-vacancies/">Job Vacancies</a></li>
                            <?php
                            if($assessor_profile==1){
                            ?>
                            <!--<li><a href="<?php //echo SITEURL; ?>moodle/">Moodle</a></li>-->
                            <li><a href="<?php echo MOODLE_URL; ?>/">Moodle</a></li>
                            <?php
                            }else{
                            ?>
                            <li><a href="Javascript:alert('Please complete your profile before accessing the courses.');window.location.href='<?php echo SITEURL; ?>/index.php?controller=user&action=userProfile&id=<?php echo $user['user_id'] ?>&client_id=<?php echo $user['client_id'] ?>'">Moodle</a></li>
                            <?php
                            }
                            ?>
                            <li><a href="http://adhyayan.asia/site/contact-us/">Contact Us</a></li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div>
            </section>
            <section class="nuiHdrBtmBar">
                <div class="container clearfix">
                    <?php
                    if (!isset($_REQUEST['process'])) {
                        ?>
                        <ul class="fl mainNav clearfix">
                            <li class="<?php echo $this->_controller == 'index' ? 'active' : ''; ?>"><a class="bigIcon" href="?"><i class="fa fa-home"></i></a></li>
                            <li class="<?php echo $this->_controller == 'index' ? '' : 'active'; ?>"><a href="#" <?php echo $disableForSelfReview ?>>Manage<i class="fa fa-sort-desc"></i></a>
                                <ul>
                                    <?php
                                    if (in_array(4, $user['role_ids']) || in_array(9,$user['role_ids'])) {
                                        $url = createUrl(array("controller" => "user", "action" => "userProfile", "id" => $user['user_id'], 'client_id' => $user['client_id']));
                                    } else {
                                        $url = createUrl(array("controller" => "user", "action" => "editUser", "id" => $user['user_id']));
                                    }
                                    if ((in_array("manage_all_users", $user['capabilities']) || in_array("manage_own_users", $user['capabilities']) || ($user['network_id'] > 0 && in_array("manage_own_network_users", $user['capabilities']))) && $user['is_guest']!=1) {
                                        ?>
                                        <li>
                                            <a href="<?php echo createUrl(array("controller" => "user", "action" => "user")); ?>" <?php echo $disableForSelfReview ?>>Manage <?php
                                if (current($user['role_ids']) == 8) {
                                    echo 'Assessors';
                                } else {
                                    echo 'Users';
                                }
                                        ?></a><i class="fa fa-caret-right"></i>
                                            <ul>
                                                <li><a href="<?php echo createUrl(array("controller" => "user", "action" => "createUser")); ?>" <?php echo $disableForSelfReview ?>>Add <?php
                                        if (current($user['role_ids']) == 8) {
                                            echo 'Assessor';
                                        } else {
                                            echo 'User';
                                        }
                                        ?></a></li>
                                                <li><a href="<?php echo createUrl(array("controller" => "user", "action" => "user")); ?>" <?php echo $disableForSelfReview ?>>All <?php
                                                        if (current($user['role_ids']) == 8) {
                                                            echo 'Assessors';
                                                            $user_type = 'Assessors';
                                                        } else {
                                                            echo 'Users';
                                                            $user_type = 'Users';
                                                        }
                                                        ?></a></li>
                                                <li><a href="<?php echo $url; ?>" <?php echo $disableForSelfReview ?>>My Profile</a></li>
                                                <?php $imp_user_url = createUrl(array("controller" => "user", "action" => "importUserDetails")); ?>
                                                <li><a href="<?php echo $imp_user_url; ?>" <?php echo $disableForSelfReview ?>>Import <?php echo $user_type; ?> Profile</a></li>
                                            </ul>
                                        </li>
        <?php } else {
        ?>
                                        <li><a href="<?php echo $url; ?>" <?php echo $disableForSelfReview ?>>My Profile</a></li>
                                    <?php }
                                    if (in_array("create_client", $user['capabilities']) || in_array("manage_own_network_clients", $user['capabilities'])) {
                                        ?>
                                        <li>
                                            <a href="<?php echo createUrl(array("controller" => "client", "action" => "client")); ?>" <?php echo $disableForSelfReview ?>>Manage Schools/Colleges</a><i class="fa fa-caret-right"></i>
                                            <ul>
                                                <?php if (in_array("create_client", $user['capabilities'])) { ?><li><a href="<?php echo createUrl(array("controller" => "client", "action" => "createClient")); ?>" <?php echo $disableForSelfReview ?>>Add School/College</a></li><?php } ?>
                                                <li><a href="<?php echo createUrl(array("controller" => "client", "action" => "client")); ?>" <?php echo $disableForSelfReview ?>>All Schools/Colleges</a></li>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                    if (in_array("create_network", $user['capabilities'])) {
                                        ?>
                                        <li>
                                            <a href="<?php echo createUrl(array("controller" => "network", "action" => "network")); ?>" <?php echo $disableForSelfReview ?>>Manage Networks</a><i class="fa fa-caret-right"></i>
                                            <ul>
                                                <li><a href="<?php echo createUrl(array("controller" => "network", "action" => "createNetwork")); ?>" <?php echo $disableForSelfReview ?>>Add Network</a></li>                                               
                                                <li><a href="<?php echo createUrl(array("controller" => "network", "action" => "network")); ?>" <?php echo $disableForSelfReview ?>>All Networks</a></li>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <li>
                                        <a href="<?php echo createUrl(array("controller" => "assessment", "action" => "assessment")); ?>" <?php echo $disableForSelfReview ?>>Manage Reviews</a><i class="fa fa-caret-right"></i>
                                        <ul>
                                            <?php
                                            if (!in_array(8, $user['role_ids'])) {
                                                if (in_array("create_assessment", $user['capabilities'])) {
                                                    ?>
                                                    <li><a href="<?php echo createUrl(array("controller" => "assessment", "action" => "createSchoolAssessment")); ?>" <?php echo $disableForSelfReview ?>>Create School Review</a></li>
                                                    <li><a href="<?php echo createUrl(array("controller" => "assessment", "action" => "createTeacherAssessment")); ?>" <?php echo $disableForSelfReview ?>>Create Teacher Review</a></li>
                                                    <li><a href="<?php echo createUrl(array("controller" => "assessment", "action" => "createStudentAssessment")); ?>" <?php echo $disableForSelfReview ?>>Create Student Review</a></li>
                                                        <?php
                                                }
                                            }
                                            ?>
                                            <?php if (in_array("create_self_review", $user['capabilities'])) { ?>
                                                <li><a href="<?php echo createUrl(array("controller" => "assessment", "action" => "createSchoolSelfAssessment")); ?>" <?php echo $disableForSelfReview ?>>Create School Self-Review</a></li>											
    <?php } ?>
                                            <li><a href="<?php echo createUrl(array("controller" => "assessment", "action" => "assessment")); ?>" <?php echo $disableForSelfReview ?>>All Reviews</a></li>
                                        </ul>
                                    </li>
                                    <?php if (in_array("manage_diagnostic", $user['capabilities'])) { ?>
                                        <li>
                                            <a href="<?php echo createUrl(array("controller" => "diagnostic", "action" => "diagnostic")); ?>" <?php echo $disableForSelfReview ?>>Manage Diagnostics</a><i class="fa fa-caret-right"></i>
                                            <ul>
                                                <li><a href="<?php echo createUrl(array("controller" => "diagnostic", "action" => "addDiagnostic")); ?>" <?php echo $disableForSelfReview ?>>Create Diagnostic</a></li>
                                                <li><a href="<?php echo createUrl(array("controller" => "diagnostic", "action" => "diagnostic")); ?>" <?php echo $disableForSelfReview ?>>All Diagnostics</a></li>
                                            </ul>
                                        </li>									
                                        <?php
                                    }
                                    if (in_array("view_all_assessments", $user['capabilities']) && (in_array(1, $user['role_ids']) || in_array(2, $user['role_ids']))) {
                                        ?>
                                        <li>
                                            <a href="<?php echo createUrl(array("controller" => "customreport", "action" => "networkreportlist")); ?>" <?php echo $disableForSelfReview ?>>Manage Reports</a><i class="fa fa-caret-right"></i>
                                            <ul>
                                                <li><a href="<?php echo createUrl(array("controller" => "customreport", "action" => "network")); ?>" <?php echo $disableForSelfReview ?>>Create Network Report</a></li>
                                                <li><a href="<?php echo createUrl(array("controller" => "customreport", "action" => "networkreportlist")); ?>" <?php echo $disableForSelfReview ?>>All Network Reports</a></li>
                                            </ul>
                                        </li>									
                                        <?php
                                    }

                                    if (in_array("manage_diagnostic", $user['capabilities'])) {
                                        ?>                                                               
                                        <li>
                                            <a href="<?php echo createUrl(array("controller" => "exportExcel", "action" => "allData")); ?>" <?php echo $disableForSelfReview ?>>Export to Excel</a><i class="fa fa-caret-right"></i>
                                            <ul>
                                                <li><a href="<?php echo createUrl(array("controller" => "exportExcel", "action" => "allData")); ?>" <?php echo $disableForSelfReview ?>>AQS School Report</a></li>
                                                <li><a href="<?php echo createUrl(array("controller" => "exportExcel", "action" => "evidenceData")); ?>" <?php echo $disableForSelfReview ?>>Evidence Report</a></li>
                                                <li><a href="<?php echo createUrl(array("controller" => "exportExcel", "action" => "overallsummary")); ?>" <?php echo $disableForSelfReview ?>>Overall CRR Summary Report</a></li>
                                            </ul>
                                        <li>
                                            <?php
                                        }
                                         
                                        if((in_array("manage_workshop",$user['capabilities']) || in_array("view_own_workshop",$user['capabilities'])) && $user['is_guest']!=1){ ?>
                                          <li><?php
                                            if(in_array("view_own_workshop",$user['capabilities']) && !in_array("manage_workshop",$user['capabilities'])){
                                            ?>
                                            <a href="<?php echo createUrl(array("controller" => "workshop", "action" => "myworkshop")); ?>" <?php echo $disableForSelfReview ?>>Manage Workshops</a>
                                            <?php
                                            }else{
                                             ?>
                                            <a href="<?php echo createUrl(array("controller" => "workshop", "action" => "allworkshop")); ?>" <?php echo $disableForSelfReview ?>>Manage Workshops</a><i class="fa fa-caret-right"></i>
                                            <?php
                                            }
                                            ?>
                                            <ul>
                                                <?php 
                                                if(in_array("manage_workshop",$user['capabilities'])) {
                                                ?>
                                                <li><a href="<?php echo createUrl(array("controller" => "workshop", "action" => "createWorkshop")); ?>" <?php echo $disableForSelfReview ?>>Add Workshop</a></li>
                                                <li><a href="<?php echo createUrl(array("controller" => "workshop", "action" => "allworkshop")); ?>" <?php echo $disableForSelfReview ?>>All Workshops</a></li>
                                                <?php 
                                                }
                                                if(in_array("view_own_workshop",$user['capabilities'])) {
                                                ?>
                                                <li><a href="<?php echo createUrl(array("controller" => "workshop", "action" => "myworkshop")); ?>" <?php echo $disableForSelfReview ?>>My Workshops</a></li>
                                                <?php 
                                                }
                                                
                                                ?>
                                            </ul>
                                        <li>
                                            
                                         <?php
                                        }
                                         
                                        if (in_array("manage_app_settings", $user['capabilities'])) {
                                            ?>
                                        <li>
                                            <a href="<?php echo createUrl(array("controller" => "settings", "action" => "settings")); ?>" <?php echo $disableForSelfReview ?>>Manage Settings</a>
                                        <li>
                                        <?php } ?>
                                        <?php
                                        if (current($user['role_ids']) == 8) {
                                            ?>
                                        <li>
                                            <a href="<?php echo createUrl(array("controller" => "communication", "action" => "communication")); ?>" <?php echo $disableForSelfReview ?>>MyCommunications</a>
                                        <li>  
                                            <?php
                                        }
                                        ?>
                                </ul>
                            </li>
                            
                        </ul>
                        <div class="fr desktop">

                            <ul class="hlinks clearfix">
                                <li><span>Welcome <?php echo $user['name']; ?></span></li>
                                <li><a href="<?php echo SITEURL . "?video=1" ?>"><i class="fa fa-question vtip" style="margin:0;" title="Click here to watch the video again"></i></a></li>
                                <li><a href="#"><i class="fa fa-envelope-o"></i><b>0</b></a></li>
                                <li>
                                    <a href="#" class="dropdown-toggle" id="notification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <i class="fa fa-bell-o"></i>
                                        <b id="totalAlertCount">
    <?php echo count( array_intersect(array(8,1,2), $user['role_ids']))==1 ? $user['alert_count'] : 0; ?>
                                        </b>
                                    </a>
                                    <input type="hidden" name="assessor_count" id="assessor_value" value=""/>
                                    <input type="hidden" name="review_count" id="review_value" value=""/>
                                    <?php
                                    if (count(array_intersect(array(8,1,2), $user['role_ids']))==1) {
                                        $assessorRef = $user['assessor_count'] > 0 ? 1 : 0;
                                        $reviewRef = $user['review_count'] > 0 ? 1 : 0;
                                        ?>
                                        <ul class="dropdown-menu" aria-labelledby="notification">
                                            <?php if(in_array(1, $user['role_ids']) || in_array(8, $user['role_ids'])) { ?>
                                            <li>
                                                <a href="<?php echo createUrl(array("controller" => "user", "action" => "user", 'ref' => $assessorRef)); ?>" 
                                                   id="assessor_count">New Assessor -
        <?php echo in_array(1, $user['role_ids']) || in_array(8, $user['role_ids']) ? $user['assessor_count'] : 0; ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo createUrl(array("controller" => "assessment", "action" => "assessment", 'ref' => $reviewRef)); ?>" 
                                                   id="review_count">New Review -
        <?php echo in_array(1, $user['role_ids']) || in_array(8, $user['role_ids']) ? $user['review_count'] : 0; ?>
                                                </a>
                                            </li>
                                            <?php } ?>
                                            <?php if(in_array(1, $user['role_ids']) || in_array(2, $user['role_ids'])) { ?>
                                            <li>
                                                <a href="<?php echo createUrl(array("controller" => "assessment", "action" => "assessment", 'ref' => 2)); ?>" 
                                                   id="pending_assessment">Pending Assessment -
        <?php echo in_array(1, $user['role_ids']) || in_array(2, $user['role_ids'])  ? $user['ass_count'] : 0; ?>
                                                </a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                        <?php
                                    }
                                    ?>

                                </li>
                                <li><a href="<?php echo createUrl(array("controller" => "login", "action" => "logout")); ?>">Log Out</a></li>
                            </ul>

                        </div>
                        <div class="fr mobile">
                            <ul class="hlinks clearfix">
                                <li><a href="<?php echo createUrl(array("controller" => "login", "action" => "logout")); ?>"><i class="fa fa-unlock-alt"></i>Log Out</a></li>
                            </ul>
                        </div> 
    <?php }
    //echo $this->_controller;
    //echo $this->_action;
?>
                </div>
            </section>
        </header>
        <section class="nuibody">
            <div class="container" <?php if($this->_controller=="actionplan" && ($this->_action=="actionplan1" || $this->_action=="actionplan2")) echo 'style="width:95%"'; ?> >

