				<h1 class="page-title">
				<?php if($isPop==0){?>
				<a href="<?php
						$args=array("controller"=>"user","action"=>"user");												
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						
					Manage <?php
                                                if(current($user['role_ids'])==8){
                                                    echo 'Assessors';
                                                } else {
                                                    echo 'Users';
                                                }
                                                ?>
					</a> &mdash;
				<?php } ?>	
					Add  <?php
                                                if(current($user['role_ids'])==8){
                                                    echo 'Assessor';
                                                } else {
                                                    echo 'User';
                                                }
                                                ?>
				</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26">
						<div class="form-stmnt">
							<form method="post" id="create_user_form" action="">
								<div class="boxBody">
									<?php if(in_array("manage_all_users",$user['capabilities']) || in_array("manage_own_network_users",$user['capabilities'])){ ?>
									<dl class="fldList">
                                                                            <input type="hidden" name="login_user_id" id="login_user_id" value="<?php echo current($user['role_ids'])?>"/>
										<dt>School/College<span class="astric">*</span>:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<!--<select class="form-control" name="client_id" required>
														<option value=""> - Select School - </option>
														<?php
														/*foreach($clients as $client)
															echo "<option value=\"".$client['client_id']."\">".$client['client_name'].($client['street']!=""?", ".$client['street']:'').($client['city']!=""?", ".$client['city']:'').($client['state']!=""?", ".$client['state']:'')."</option>\n";
														*/
														?>
													</select>-->
                                                                                                        <?php
                                                                                                        if(in_array(8, $user['role_ids'])){
                                                                                                            $span = 'Independent Consultant';
                                                                                                            $value = '221';
                                                                                                            $labal = 'Change School/College';
                                                                                                        } else {
                                                                                                            $span = '';
                                                                                                            $value = '';
                                                                                                            $labal = 'Select School/College';
                                                                                                        }
                                                                                                        ?>
													<span id="selected_client_name"><?php echo $span;?></span> &nbsp;
													<a href="?controller=client&action=clientList" data-postformid="for" class="btn btn-danger vtip execUrl" title="Click to select a school/college." data-size="1050" id="selectClientBtn"><?php echo $labal?></a>
                                                                                                        <input type="hidden" autocomplete="off" name="client_id" id="selected_client_id" value="<?php echo $value;?>" />
												</div>
											</div>
										</dd>
									</dl>
									<?php }else{ ?>
										<input type="hidden" autocomplete="off" name="client_id" id="selected_client_id" value="<?php echo $user['client_id']; ?>" />
									<?php } ?>
									<dl class="fldList">
										<dt>Name<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6"><input type="text" value="" class="form-control" name="name" required autocomplete="off" /></div></div></dd>
									</dl>
									<dl class="fldList">
										<dt>Email ID<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6"><input type="email" class="form-control" value="" placeholder="this will be the username" name="email" required autocomplete="off" /></div></div></dd>
									</dl>
									<dl class="fldList">
										<dt>Password<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6"><input type="password" class="form-control pwd" value="" name="password" required="required" autocomplete="off" /></div></div></dd>
									</dl>
									<dl class="fldList">
										<dt>Confirm Password<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6"><input type="password" class="form-control cpwd" value="" required="required" autocomplete="off" /></div></div></dd>
									</dl>
									<?php 
                                                                        
                                                                        if(in_array(1, $user ['role_ids'])){
                                                                            $superRoleId = 1;
                                                                        } else {
                                                                            $superRoleId = 2;
                                                                        }
                                                                        if(in_array("manage_all_users",$user['capabilities'])){ ?>
									<dl class="fldList">
										<dt>User Role<span class="astric">*</span>:</dt>
										<dd>
											<div class="clearfix">
											<?php $disabled='';
                                                                                        // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar
											foreach($roles as $role){
                                                                                                if($role['role_id']==8 && (!in_array('1',$user['role_ids']) && !in_array('2',$user['role_ids']))){
                                                                                                    $disabled="disabled=''";
                                                                                                }
                                                                                                if(in_array(8, $user ['role_ids']) && $role['role_id']==4){
                                                                                                    echo "<div class=\"chkHldr\" style='margin-top: 5px;'><input type=\"hidden\" class=\"user-roles\" name=\"roles[]\" autocomplete=\"off\" value='4' id='role_id_4'><label class=\"chkF\"><span style='margin-left: -30px;'>External reviewer</span></label></div>\n";
                                                                                                    //echo "<div class=\"chkHldr\"><input type=\"checkbox\" ".($role['role_id']==6?'readonly="readyonly" disabled="disabled"':"")." class=\"user-roles\" name=\"roles[]\" autocomplete=\"off\" value=\"".$role['role_id']."\"><label class=\"chkF checkbox\"><span>".$role['role_name']."</span></label></div>\n";
                                                                                                } else if(!in_array(8, $user ['role_ids'])){
                                                                                                    echo "<div class=\"chkHldr\"><input type=\"checkbox\" class=\"user-roles\" name=\"roles[]\" autocomplete=\"off\" value=\"".$role['role_id']."\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>".$role['role_name']."</span></label></div>\n";
                                                                                                }
                                                                                        }   
                                                                                        ?>
											</div>
										</dd>
									</dl>
									<?php 
										}
										else if(in_array("manage_own_users",$user['capabilities']) && in_array(6,$user['role_ids']))
										{?>
										<dl class="fldList">
										<dt>User Role<span class="astric">*</span>:</dt>
										<dd>
											<div class="clearfix">
										<?php $disabled='';
											//school principal is able to add internal reviewer and school admin only
											foreach($roles as $role){
                                                                                            // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar
                                                                                            if($role['role_id']==8 && (!in_array('1',$user['role_ids']) && !in_array('2',$user['role_ids']))){
                                                                                                $disabled="disabled=''";
                                                                                            }
                                                                                            
												in_array($role['role_id'],array(3,5))? print "<div class=\"chkHldr\" style='margin-top:8px;'><input type=\"checkbox\" class=\"user-roles\" name=\"roles[]\" autocomplete=\"off\" value=\"".$role['role_id']."\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>".$role['role_name']."</span></label></div>\n":'';											
                                                                                        }
                                                                                    ?>
										</div>
										</dd>
										</dl>
										<?php 
										}else if(in_array("manage_own_users",$user['capabilities']) && in_array(7,$user['role_ids']))
										{?>
										<dl class="fldList">
										<dt>User Role<span class="astric">*</span>:</dt>
										<dd>
											<div class="clearfix">
										<?php $disabled='';
											//school principal is able to add internal reviewer and school admin only
											foreach($roles as $role){
                                                                                            // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar
                                                                                            if($role['role_id']==8 && (!in_array('1',$user['role_ids']) && !in_array('2',$user['role_ids']))){
                                                                                                $disabled="disabled=''";
                                                                                            }
                                                                                            
												in_array($role['role_id'],array(3,6,5))? print "<div class=\"chkHldr\" style='margin-top:8px;'><input type=\"checkbox\" class=\"user-roles\" name=\"roles[]\" autocomplete=\"off\" value=\"".$role['role_id']."\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>".$role['role_name']."</span></label></div>\n":'';											
                                                                                        }
                                                                                    ?>
										</div>
										</dd>
										</dl>
										<?php 
										}
									
									?>
                                                                        <?php
                                                                        if(in_array("manage_all_users",$user['capabilities'])){ ?>        
                                                                        <dl class="fldList">
										<dt>Add /Update to Moodle<span class="astric">*</span>:</dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6"> 
                                                                                                    
                                                                                            <div class="chkHldr wAuto"><input name="moodle_user" value="1" type="radio"><label class="chkF radio"><span>Yes</span></label></div>
                                                                                            <div class="chkHldr wAuto"><input checked="checked" name="moodle_user" value="0" type="radio"><label class="chkF radio"><span>No</span></label></div>
                                                                                                </div>
											</div>
										</dd>
									</dl> 
                                                                        <?php
                                                                        }else{
                                                                        ?>
                                                                                <input type="hidden" name="moodle_user" value="0" >       
                                                                        <?php       
                                                                        }
                                                                        ?>
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" value="Add User" class="btn btn-primary">
												</div>
											</div>
										</dd>
									</dl>
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
							</form>
						</div>
					</div>
				</div>