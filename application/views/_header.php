<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 4.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=8" >
<meta content="text/html;charset=utf-8" http-equiv="content-type">
	<title>Adhyayan</title>
	 <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo SITEURL; ?>favicon.ico">
<?php echo $addToHeader; ?>
</head>
<body>
	<header>
         <section class="nuiHeader">
            <div class="nuiWrapper clearfix">
                <div class="fl logo"><a href="<?php echo SITEURL;?>"><img src="<?php echo SITEURL;?>public/images/logo.png" alt="Logo - Adhyayan"></a></div>
                <div class="fr hdrNavTP clearfix">
                    <ul class="fr hdrTopNav clearfix">
                        <li class="http://adhyayan.asia/site/category/blog/"><a href="#">Adhyayan Blog</a></li>
                        <li><a href="http://adhyayan.asia/site/ssre-image-gallery/">Gallery</a></li>
                        <li><a href="http://adhyayan.asia/site/downloads/">Downloads</a></li>
                        <li><a href="http://adhyayan.asia/site/job-vacancies/">Job Vacancies</a></li>
                        <li><a href="http://adhyayan.asia/site/contact-us/">Contact Us</a></li>
                    </ul>
                </div>
            </div>
         </section>
         <section class="nuiHdrBtmBar">
             <div class="nuiWrapper clearfix">
                <ul class="fl mainNav clearfix">
                    <li class="active"><a href="#">Manage<i class="fa fa-sort-desc"></i></a>
                        <ul>
							<?php
							if(in_array("manage_all_users",$user['capabilities']) || in_array("manage_own_users",$user['capabilities']) || ($user['network_id']>0 && in_array("manage_own_network_users",$user['capabilities']))){
							?>
								<li>
									<a href="<?php echo createUrl(array("controller"=>"user","action"=>"user")); ?>">Manage Users</a>
									<ul>
										<li><a href="<?php echo createUrl(array("controller"=>"user","action"=>"createUser")); ?>">Add User</a></li>
										<li><a href="<?php echo createUrl(array("controller"=>"user","action"=>"user")); ?>">All Users</a></li>
										<li><a href="<?php echo createUrl(array("controller"=>"user","action"=>"editUser","id"=>$user['user_id'])); ?>">My Profile</a></li>
									</ul>
								</li>
							<?php 
							}else{ ?>
								<li><a href="<?php echo createUrl(array("controller"=>"user","action"=>"editUser","id"=>$user['user_id'])); ?>">My Profile</a></li>
							<?php }
							if(in_array("create_client",$user['capabilities']) || in_array("manage_own_network_clients",$user['capabilities'])){ ?>
								<li>
									<a href="<?php echo createUrl(array("controller"=>"client","action"=>"client")); ?>">Manage Schools</a>
									<ul>
									<?php if(in_array("create_client",$user['capabilities'])){ ?><li><a href="<?php echo createUrl(array("controller"=>"client","action"=>"createClient")); ?>">Add School</a></li><?php } ?>
										<li><a href="<?php echo createUrl(array("controller"=>"client","action"=>"client")); ?>">All Schools</a></li>
									</ul>
								</li>
							<?php 
							}
							if(in_array("create_network",$user['capabilities'])){ ?>
								<li>
									<a href="<?php echo createUrl(array("controller"=>"network","action"=>"network")); ?>">Manage Networks</a>
									<ul>
										<li><a href="<?php echo createUrl(array("controller"=>"network","action"=>"createNetwork")); ?>">Add Network</a></li>
										<li><a href="<?php echo createUrl(array("controller"=>"network","action"=>"network")); ?>">All Networks</a></li>
									</ul>
								</li>
							<?php 
							} 
							
							?>
								<li>
									<a href="<?php echo createUrl(array("controller"=>"assessment","action"=>"assessment")); ?>">Manage Reviews</a>
									<ul>
										<?php if(in_array("create_assessment",$user['capabilities'])){ ?><li><a href="<?php echo createUrl(array("controller"=>"assessment","action"=>"createSchoolAssessment")); ?>">Create School Reviews</a></li><?php } ?>
										<li><a href="<?php echo createUrl(array("controller"=>"assessment","action"=>"assessment")); ?>">All Reviews</a></li>
									</ul>
								</li>
							<?php
							
							if(in_array("manage_diagnostic",$user['capabilities'])){ ?>
								<li>
									<a href="<?php echo createUrl(array("controller"=>"diagnostic","action"=>"diagnostic")); ?>">Manage Diagnostics</a>
									<ul>
										<li><a href="<?php echo createUrl(array("controller"=>"index","action"=>"comingsoon")); ?>">Add Diagnostic</a></li>
										<li><a href="<?php echo createUrl(array("controller"=>"diagnostic","action"=>"diagnostic")); ?>">All Diagnostics</a></li>
									</ul>
								</li>
							<?php 
							}
							if(in_array("manage_app_settings",$user['capabilities'])){ ?>
								<li>
									<a href="<?php echo createUrl(array("controller"=>"settings","action"=>"settings")); ?>">Manage Settings</a>
								<li>
							<?php } ?>
                        </ul>
                    </li>

                </ul>
                <div class="fr">
                    <ul class="hlinks clearfix">
                        <li><span>Welcome <?php echo $user['name']; ?></span></li>
                        <li><a href="#"><i class="fa fa-envelope-o"></i><b>0</b></a></li>
                        <li><a href="#"><i class="fa fa-bell-o"></i><b>0</b></a></li>
                        <li><a href="<?php echo createUrl(array("controller"=>"login","action"=>"logout")); ?>">Log Out</a></li>
                    </ul>
                </div>
             </div>
         </section>
      </header>
	  <section class="nuibody">
         <div class="nuiWrapper">