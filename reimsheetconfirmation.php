<?php 
require_once 'config/config.php';
//require_once 'library/shared.php';
//require_once 'index.php';
//echo SITEURL;
function createUrl($data=array()){
	return SITEURL."index.php?".http_build_query($data);
}
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmation: Adhyayan</title>
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600' rel='stylesheet' type='text/css'>
    	<link href="<?php echo SITEURL;?>public/css/nui-login.css" rel="stylesheet" />
	<link href="<?php echo SITEURL;?>public/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo SITEURL;?>public/css/font-awesome.min.css" rel="stylesheet" />
	<link href="<?php echo SITEURL;?>public/css/bootstrap-social.css" rel="stylesheet" />
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <![endif]-->
  </head>
  <body class="nuiLogInBody">
	<header class="nuiLogInHdrOuter">
	  <div class="nuiLogInHdr">
		  <a href=""><img src="<?php echo SITEURL;?>public/images/login/logo_login.png" alt="" style="height:80px;"></a>
	  </div>
  </header>
  <section class="nuiLogBoxBody">
      <div class="nuiLogBoxCont">
          <?php
          if(isset($_REQUEST['status']) && $_REQUEST['status'] == 1) {
          ?>
            <h1 style="color: #7C111B;">Thank you for your confirmation regarding Reimbursement Sheet</h1>
          <?php
          }else if(isset($_REQUEST['status']) && $_REQUEST['status'] == 0) {
          ?>
            <h2 style="color: #7C111B;">You have already confirmed</h2>
          <?php
          } ?>
            <h3><a style="color: #000000;" href="<?php
        $args = array("controller" => "login", "action" => "login");
       
        echo createUrl($args);
        ?>">
            Click here to Login
        </a> </h3> 
      </div>							
   </section>
      <footer class="nuiloginFooter">&copy; Powered by <a target="_blank" href="http://tatrasdata.com/">Tatras</a></footer>
  </body>
</html>