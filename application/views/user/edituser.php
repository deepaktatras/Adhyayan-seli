			<?php 
                        if(isset($eUser['user_id'])){
                            
                            ?>
<script type="text/javascript">
    // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar
<?php
if(current($eUser['role_ids'])!=''){
    ?>
    window.document.onload = checkboxEnableDisable("role_id_",'<?php echo current($eUser['role_ids'])?>',"user-roles");    
    <?php
}
?>   

</script>
<h1 class="page-title">
<?php if($isPop==0 && current($eUser['role_ids'])!=8){?>
				<a href="<?php
						$args=array("controller"=>"user","action"=>"user");												
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						
					Manage Users
					</a> &mdash;
				<?php } ?>
<?php
if(current($eUser['role_ids'])==8){
    echo 'My Profile';
} else {
    echo 'Update User';
}
//print_r($eUser);
?>

	
</h1>
<div class="clr"></div>
<div class="">
	<div class="ylwRibbonHldr">
		<div class="tabitemsHldr"></div>
	</div>
	<div class="subTabWorkspace pad26">
		<div class="form-stmnt">
			<form method="post" id="update_user_form" action="">
				<div class="boxBody">
                                    <?php if(in_array("manage_all_users",$user['capabilities']) && in_array("edit_all_submitted_assessments",$user['capabilities'])){ ?>	
                    
                                    <dl class="fldList">
                                                                            <input type="hidden" name="login_user_id" id="login_user_id" value="<?php echo current($user['role_ids'])?>"/>
									                                                            <dt>School/College<span class="astric">*</span>:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													
                                                                                                        <?php
                                                                                                        
                                                                                                            $span = $eUser['client_name'];;
                                                                                                            $value = $eUser['client_id'];
                                                                                                            $labal = 'Change School/College';
                                                                                                        
                                                                                                        ?>
													<span id="selected_client_name"><?php echo $span;?></span> &nbsp;
													<a href="?controller=client&action=clientList" data-postformid="for" class="btn btn-danger vtip execUrl" title="Click to select a school." data-size="1050" id="selectClientBtn"><?php echo $labal?></a>
                                                                                                        <input type="hidden" autocomplete="off" name="client_id" id="selected_client_id" value="<?php echo $value;?>" />
												</div>
											</div>
										</dd>
									</dl>
                                    <?php }else{
                                    ?>
                                    <input type="hidden" value="<?php echo $eUser['client_id']; ?>" name="client_id" id="selected_client_id" />
                                    <?php
                                    } ?>				
                                    
					<dl class="fldList">
						<dt>
							Name<span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">
									<input type="text" value="<?php echo $eUser['name']; ?>"
										class="form-control" name="name"  />
								</div>
							</div>
						</dd>
					</dl>
					<dl class="fldList">
						<dt>
							Email ID<span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">
                                                                    <?php
                                                                    if(in_array("manage_all_users",$user['capabilities']) && in_array("edit_all_submitted_assessments",$user['capabilities'])){
                                                                    ?>
									<!--<input type="email" disabled="disabled" class="form-control"
										value="<?php //echo $eUser['email']; ?>"
										placeholder="this will be the username" name="email" required />-->
                                                                    <input type="text"  class="form-control"
										value="<?php echo $eUser['email']; ?>"
										placeholder="this will be the username" name="email" required />
                                                                    <?php
                                                                    }else{
                                                                    ?>
                                                                    <input type="email" disabled="disabled" class="form-control"
										value="<?php echo $eUser['email']; ?>"
										placeholder="this will be the username" name="email" required />
                                                                    <?php
                                                                    }
                                                                    ?>
								</div>
							</div>
						</dd>
					</dl>

					<dl class="fldList">
						<dt>
							New Password<span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">
									<input type="password" class="form-control pwd" value=""
										name="password" />
								</div>
							</div>
						</dd>
					</dl>
					<dl class="fldList">
						<dt>
							Confirm Password<span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">
									<input type="password" class="form-control cpwd" value="" />
								</div>
							</div>
						</dd>
					</dl>
									<?php 
                                                                        if(in_array(1, $user ['role_ids'])){
                                                                            $superRoleId = 1;
                                                                        } else {
                                                                            $superRoleId = 2;
                                                                        }
                                                                       // print_r($user);
                                                                        
                                                                        if(in_array("manage_all_users",$user['capabilities']) && !in_array(8, $user ['role_ids'])){ ?>
									<dl class="fldList">
						<dt>
							User Role<span class="astric">*</span>:
						</dt>
						<dd>
							<div class="clearfix">
											<?php $disabled='';
					foreach ( $roles as $role ){
                                            // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar
                                            $disabled='';
                                            if(in_array(8,$eUser['role_ids'])){
                                                if((!in_array('1',$user['role_ids']))){
                                                    $disabled="disabled=''";
                                                }
                                            } else {
                                                if($role['role_id']==8 && (!in_array('1',$user['role_ids']))){
                                                    $disabled="disabled=''";
                                                }
                                            }
                                            
						echo "<div class=\"chkHldr\"><input type=\"checkbox\" " . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") . " class=\"user-roles\" name=\"roles[]\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";
                                        }
                                        ?>
											</div>
						</dd>
					</dl>
									<?php
				
} else if (in_array ( "manage_own_users", $user ['capabilities'] ) && in_array ( 6, $user ['role_ids'] ) && !in_array(8, $user ['role_ids'])) {
					?>
					<dl class="fldList">
						<dt>
							User Role<span class="astric">*</span>:
						</dt>
						<dd>
							<div class="clearfix">
																			<?php
                                         $disabled='';                                                                                                                      
					// school principal is able to add internal reviewer and school admin only
					foreach ( $roles as $role )
					{
                                            // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar
                                            if(in_array(8,$eUser['role_ids'])){
                                                if((!in_array('1',$user['role_ids']))){
                                                    $disabled="disabled=''";
                                                }
                                            } else {
                                                if($role['role_id']==8 && (!in_array('1',$user['role_ids']))){
                                                    $disabled="disabled=''";
                                                }
                                            }
						if(in_array(6,$eUser['role_ids']) && $role ['role_id']==6)
						{
							echo in_array ( $role ['role_id'], array(3,5,6)) ? "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") .(  $role ['role_id']==6  ? 'disabled="disabled"' : "") ." name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n" : '';
							echo "<input type='hidden' name=\"roles[]\" value='6' />";
						}
						else{
                                                        if(in_array ( $role ['role_id'], array(3,5))){
                                                         echo "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") ." name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";   
                                                        }else if (in_array($role ['role_id'], $eUser ['role_ids'])){
                                                        $disabled_etc="disabled=''";
                                                        echo "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") ." name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled_etc."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";
                                                        }
							//echo in_array ( $role ['role_id'], array(3,5)) ? "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") ." name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n" : '';
                                                }
					}
						?>
																			</div>
						</dd>
					</dl>
					<?php
                                }else if (in_array ( "manage_own_users", $user ['capabilities'] ) && in_array ( 7, $user ['role_ids'] ) && !in_array(8, $user ['role_ids'])) {
                                    if($eUser['user_id']!=$user['user_id']){
					?>
					<dl class="fldList">
						<dt>
							User Role<span class="astric">*</span>:
						</dt>
						<dd>
							<div class="clearfix">
																			<?php
                                         $disabled='';                                                                                                                      
					// school principal is able to add internal reviewer and school admin only
					foreach ( $roles as $role )
					{
                                            // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar
                                            if(in_array(8,$eUser['role_ids'])){
                                                if((!in_array('1',$user['role_ids']))){
                                                    $disabled="disabled=''";
                                                }
                                            } else {
                                                if($role['role_id']==8 && (!in_array('1',$user['role_ids']))){
                                                    $disabled="disabled=''";
                                                }
                                            }
						if(in_array(7,$eUser['role_ids']) && $role ['role_id']==7)
						{
							echo in_array ( $role ['role_id'], array(5,6,7)) ? "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") .(  $role ['role_id']==7  ? 'disabled="disabled"' : "") ." name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n" : '';
							echo "<input type='hidden' name=\"roles[]\" value='7' />";
						}
						else{
							//echo in_array ( $role ['role_id'], array(3,5,6)) ? "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") ." name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n" : '';
                                                       if(in_array ( $role ['role_id'], array(3,5,6))){
                                                        echo "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") ." name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";   
                                                       }else if (in_array($role ['role_id'], $eUser ['role_ids'])){
                                                        $disabled_etc="disabled=''";
                                                        echo "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") ." name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled_etc."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";   
                                                       }
                                                }
                                                
                                                }
						?>
																			</div>
						</dd>
					</dl>
					<?php
                                    }
                                } else if(in_array(8, $user ['role_ids'])){
                                    echo '<input type="hidden" name="roles[]" value="8">';
                                }
				
				?>
                                    
                                      <?php
                                      if(in_array("manage_all_users",$user['capabilities'])){ ?> 
                                    
                                                                        <dl class="fldList">
										<dt>Add /Update to Moodle<span class="astric">*</span>:</dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6"> 
                                                                                                    
                                                                                            <div class="chkHldr wAuto"><input name="moodle_user" value="1" type="radio" <?php if($eUser['add_moodle']==1) echo"checked=checked"; ?> ><label class="chkF radio"><span>Yes</span></label></div>
                                                                                            <div class="chkHldr wAuto"><input  name="moodle_user" value="0" type="radio" <?php if($eUser['add_moodle']==0 || empty($eUser['add_moodle'])) echo"checked=checked"; ?> ><label class="chkF radio"><span>No</span></label></div>
                                                                                                </div>
											</div>
										</dd>
									</dl> 
				   <?php
                                    }else{
                                                                        ?>
                                                                                <input type="hidden" name="moodle_user" value="<?php echo isset($eUser['add_moodle'])?$eUser['add_moodle']:0 ?>" >       
                                                                        <?php       
                                                                        }
                                                                        ?>
                                    
									<dl class="fldList">
						<dt></dt>
                                               
						<dd class="nobg">
							<div class="row">
								<div class="col-sm-6">
									<br> <input type="submit" value="Update User"
										class="btn btn-primary"> <input type="hidden"
										value="<?php echo $eUser['user_id']; ?>" name="id" /> 
                                                                                <input
										type="hidden" value="<?php echo $eUser['client_id']; ?>"
										name="client_id_old" id="selected_client_id_old" />
								</div>
							</div>
						</dd>
					</dl>
				</div>
				<div class="ajaxMsg"></div>
				<input type="hidden" class="isAjaxRequest" name="isAjaxRequest"
					value="<?php echo $ajaxRequest; ?>" />
			</form>
		</div>
	</div>
</div>
<?php }else{ ?>
<h1>User does not exist</h1>
<?php } ?>