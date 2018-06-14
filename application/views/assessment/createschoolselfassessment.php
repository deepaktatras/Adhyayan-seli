<?php 
//echo $selfReviewsPast."past";	
//print_r($user);
//echo $tiers[0]['standard_name'];
//print_r($clientReviews);
//print_r($lastReviewSettings);
//print_r($fdl);
?> 
                                <?php
                                if($user['is_guest']!=1){ ?>
				<a id="addUserBtn" class="btn btn-primary pull-right execUrl vtip fixonmodal" title="Click to add a user." href="?controller=user&action=createUser&ispop=1">Add user</a>
                                <?php }?>
				<h1 class="page-title">
				Create School Self Review
				</h1>
				<div class="clr"></div>
                                
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
                                
					<div class="subTabWorkspace pad26">
					<div class="form-stmnt">
                                <?php
                                //print_r($user);
                                if(!$previous_status || $admin_role==1 || $user['is_guest']==1){
                                if(count($firstdefaultdiagnostic)>0){
                                $firstdefaultdiagnostic=$firstdefaultdiagnostic['diagnostic_id']; 
                                ?>
							<form method="post" id="create_school_self_assessment_form" action="">
								<div class="boxBody">
									<dl class="fldList">
										<dt>School<span class="astric">*</span>:</dt>										
										<dd><div class="row"><div class="col-sm-6">
                                                                                <input type="hidden" name="reviewtype" value="" id="reviewtype">
                                                                                <?php
                                                                                if($admin_role==1){
                                                                                ?>
                                                                                <select class="form-control internal_client_id" name="client_id" required>
                                                                                <option value=""> - Select School - </option>
                                                                                <?php
                                                                                foreach ($clientsList as $client)
                                                                                    echo "<option value=\"" . $client['client_id'] . "\">" . $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '') . "</option>\n";
                                                                                ?>
                                                                                </select>
                                                                                <?php
                                                                                }else{
                                                                                ?>    
										<input class="internal_client_id" type="hidden" name="client_id" value="<?php echo $user['client_id']; ?>" required>
											<span class="form-control internal_client_id">
												<?php echo $client['client_name'].($client['city']!=""?", ".$client['city']:'');?>
											</span>
                                                                                <?php
                                                                                }
                                                                                ?>
										</div></div></dd>
									</dl>
                                                                        
									<dl class="fldList">
										<dt>Internal Reviewer<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control internal_assessor_id" name="internal_assessor_id" required>
												<option value=""> - Select Internal Reviewer - </option>
												<?php
                                                                                                if($admin_role==0){
												foreach($internalAssessors as $internal)
													echo "<option value=\"".$internal['user_id']."\">".$internal['name']."</option>\n";
                                                                                                }
                                                                                                ?>
											</select>
										</div></div></dd>
									</dl>
                                                                        <?php
									if($admin_role==1 || $user['is_guest']==1){
                                                                        ?>
                                                                        <dl class="fldList">
										<dt>Diagnostic<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control diagnostic_id" name="diagnostic_id" id="diagnostic_id" required>
												<option value=""> - Select Diagnostic - </option>
												<?php
                                                                                                if($user['is_guest']==1){
												foreach($guestdiagnostic as $diagnostic)
													 print "<option value=\"".$diagnostic['diagnostic_id']."\"".($diagnostic['diagnostic_id']==$lastReviewSettings['diagnostic_id']?'selected=selected':'').">".$diagnostic['name']."</option>\n";
                                                                                                }
                                                                                                ?>
											</select>
                                                                                            <?php
                                                                                            if($user['is_guest']==1){
                                                                                            ?>
                                                                                            <span class="text-danger warn-show-hide"><?php echo $message_guest ?></span>            
										            <?php
                                                                                            }else{
                                                                                            ?>
                                                                                            <span class="text-danger warn-show-hide">&nbsp;</span>
                                                                                            <?php
                                                                                            }
                                                                                            ?>
                                                                                        </div></div></dd>
									</dl>
                                                                        <dl class="fldList" style="display:none;">
										<dt>Award Scheme<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
										<input type="hidden" name="award_scheme_name" id="award_scheme_name" value="1">	
										</div></div></dd>
									</dl>
                                                                        <input type="hidden" name="tier_id" id="tier_id" value="3">
                                                                        <?php
                                                                        } else{
                                                                        ?>
									<?php if($selfReviewsPast==0){//if user has not done any self-review in the past, check other validated reviews
										  		if($validatedReviews==0)//if user has no validated review, show him 6 KPA diagnostic and do not enable tier and award scheme choice
										  		{// tier will be fixed to state and award scheme to adhyayan standard
										  		?>
                                                                    
										<dl class="fldList">
										<dt>Diagnostic<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<!--<select class="form-control" name="diagnostic_id" required>
												<option value=""> - Select Diagnostic - </option>
												<?php
												//foreach($diagnostics as $diagnostic)
												//	 $diagnostic['diagnostic_id']!=1 ? print "<option value=\"".$diagnostic['diagnostic_id']."\">".$diagnostic['name']."</option>\n":'';
												?>
											</select>-->
                                                                                            <input type="hidden" name="diagnostic_id" id="diagnostic_id" value="<?php echo $firstdefaultdiagnostic?>">	
										<label class="printTxt">
										<?php
												foreach($diagnostics as $diagnostic)
													$diagnostic['diagnostic_id']==$firstdefaultdiagnostic? print $diagnostic['name']:'';
												?>	
										</label>
										</div></div></dd>
									        </dl>
												<input type="hidden" name="tier_id" value="3">	
												<input type="hidden" name="award_scheme_name" value="1">
                                                                                                
										  		<?php 	
										  		}
										  		else //if user has done a validated review in the past, show him the same tier, award and diagnostic as the last assessment done. 
										  		{   //enable diagnostic choice and disable tier and award scheme choice
										  		?>
										  		<dl class="fldList">
										<dt>Diagnostic<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control" name="diagnostic_id" id="diagnostic_id" required>
												<option value=""> - Select Diagnostic - </option>
												<?php
												foreach($lastandfreediagnostic as $diagnostic)
													 print "<option value=\"".$diagnostic['diagnostic_id']."\">".$diagnostic['name']."</option>\n";
												?>
											</select>
										</div></div></dd>
									</dl>
									
									<!--<dl class="fldList">
										<dt>Tier<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<label class="printTxt">
										<?php 
										//foreach($tiers as $tier)
										//	$tier['standard_id']==$lastReviewSettings['tier_id']? print $tier['standard_name']:'';
										?>
										</label>
										<input type="hidden" name="tier_id" value="<?php //echo $lastReviewSettings['tier_id']?>">	
										</div></div></dd>
									</dl>-->
									<input type="hidden" name="tier_id" value="<?php echo $lastReviewSettings['tier_id']?>">
									<dl class="fldList" style="display:none;">
										<dt>Award Scheme<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
										<input type="hidden" name="award_scheme_name" value="<?php echo $lastReviewSettings['award_scheme_id']?>">	
										<label class="printTxt">
										<?php
												foreach($awardSchemes as $awardScheme)
													$awardScheme['award_scheme_id']==$lastReviewSettings['award_scheme_id']? print $awardScheme['award_scheme_name']:'';
												?>	
										</label>												
										</div></div></dd>
									</dl>	
										  		<?php 
										  		}									
									}
									elseif($selfReviewsPast>0) //if user has done self review in the past, enable diagnostic, award scheme  and tier choice.
									{
									?>
                                                                        
									<dl class="fldList">
										<dt>Diagnostic<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<?php     
                                                                                       if($validatedReviews>0){
                                                                                       
                                                                                       ?>
                                                                                        <select class="form-control" name="diagnostic_id" id="diagnostic_id" required>
												<option value=""> - Select Diagnostic - </option>
												<?php
												foreach($lastandfreediagnostic as $diagnostic)
													$diagnostic['diagnostic_id']!=1 ? print "<option value=\"".$diagnostic['diagnostic_id']."\">".$diagnostic['name']."</option>\n":'';
												?>
											</select>  
                                                                                       <?php    
                                                                                       }else{
                                                                                       ?>
                                                                                        <select class="form-control" name="diagnostic_id" id="diagnostic_id" required>
												<option value=""> - Select Diagnostic - </option>
												<?php
												foreach($freediagnostic as $diagnostic)
													$diagnostic['diagnostic_id']!=1 ? print "<option value=\"".$diagnostic['diagnostic_id']."\">".$diagnostic['name']."</option>\n":'';
												?>
											</select>
                                                                                            <?php
                                                                                       }
                                                                                        ?>
										</div></div></dd>
									</dl>
									
									<!--<dl class="fldList">
										<dt>Tier<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control" name="tier_id" required>
												<option value=""> - Select Tier - </option>
												<?php
												foreach($tiers as $tier)
													echo "<option value=\"".$tier['standard_id']."\">".$tier['standard_name']."</option>\n";
												?>
											</select>
										</div></div></dd>
									</dl>-->
                                                                        <input type="hidden" name="tier_id" value="3">	
									<input type="hidden" name="award_scheme_name" value="1">									
										<?php 
									}
                                                                        }
									?>
								
									<dl class="fldList">
                                                                            <dt>Preferred Language<span class="astric">*</span>:</dt>
                                                                                <dd><div class="row">
                                                                                        <div class="col-sm-6">
                                                                                            <select class="form-control" name="diagnostic_lang" id="diagnostic_lang_id" required>
                                                                                                <option value=""> - Select Diagnostic Language - </option>
                                                                                                
                                                                                                <?php
                                                                                                if($selfReviewsPast==0 && $validatedReviews==0 && $admin_role!=1 && $user['is_guest']!=1){
                                                                                                foreach ($fdl as $language)
                                                                                                    echo "<option value=\"" . $language['language_id'] . "\">" . $language['language_words'] . "</option>\n";
                                                                                                
                                                                                                }
                                                                                                ?>
                                                                                                
                                                                                            </select>
                                                                                         </div>
                                                                                    </div>
                                                                                </dd>
                                                                        </dl>
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" title="Click to create a review."  value="Create Review" class="btn btn-primary vtip">
												</div>
											</div>
										</dd>
									</dl>
									
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
							</form>
                                                    <?php
                                }else{
                                  echo"Default Diagnostic not assigned";    
                                  //return ;    
                                }
                                }else{
                                  echo"<div class='alert alert-danger'><strong>Some of the your reviews are still pending. Please complete the existing review in order to generate new self-review.</strong></div>";
                                }
                                ?>
						</div>
					</div>
                                    
				</div>
				<script>
				$(function(){
					//$("#reviewtype").val(reviewType);
                                        $("#reviewtype").val(1);
				});
                                $(document).ready(function () {

                                $("#create_school_self_assessment_form .warn-show-hide").hide();


                                });
				</script>				