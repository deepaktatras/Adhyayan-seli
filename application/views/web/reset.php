<?php 
if(isset($key) && $key==-1)
{
	echo "<h1>The Password key is invalid</h1>";
	
}
else {
?>
		<div class="nuiLogBox">
              <header class="hdr">Reset Password</header>
              <article class="cont">
                  <form name="resetForm" id="resetForm" method="post" action="">
					<?php echo isset($error)?'<div class="alert alert-danger">'.$error.'</div>':''; ?>
                    <ul class="logList">
                    <?php if(isset($key) && $key==1){?>
                        <?php
                        if(isset($_GET['process']) && $_GET['process']=='assessor'){
                            ?>
                            <li class="loginputHldr">
                                <input type="text" name="email" id="email" value="<?php echo $confirmUserKey['email']?>" disabled/>
                                <i class="fa fa-user"></i>
                                <input type="hidden" value="<?php echo $_GET['process']?>" name="process" id="process"/>
                                <input type="hidden" value="login" name="autologin" id="autologin"/>
                            </li>  
                            <?php
                        }
                        ?>
                        <li class="loginputHldr">
                            <input type="password" name="password" class='pwd' placeholder="Enter Password" value="" required>
                            <i class="fa fa-lock"></i>
                        </li>
                        <li class="loginputHldr">
                            <input type="password" name="confirmpassword" class='cpwd' placeholder="Confirm Password" value="" required>
                            <i class="fa fa-lock"></i>
                            <input type="hidden" name="hashkey" id="hashkey" value="<?php echo $hashkey ?>" />
                        </li>
                    <?php }                    	
                    	else 
                    	{
                    ?>
                        <li class="loginputHldr">
                            <input type="text" name="email" placeholder="Enter Email" value="" required>
                            <i class="fa fa-user"></i>
                        </li>
                     <?php } ?>   
                       
                        <li class="clearfix">                                                    
                            <div class="fr">
                                <input class="redlogBtn" type="submit" value="Submit">
                            </div>
                        </li>  
                        <div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />                     
                    </ul>					
                  </form>
              </article>             
          </div>
<?php } ?>							
				