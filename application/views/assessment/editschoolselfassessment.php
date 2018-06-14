<?php 
/*$selfReviewsPast = 0;
$validatedReviews = 0; // all the published reviews except online reviews are validated 
$lastReviewSettings = array();
if(!empty($clientReviews))
	foreach($clientReviews as $review)
	{
		$review['sub_assessment_type']==1 ? $selfReviewsPast++ : '';
		$review['isPublished']==1 && $review['sub_assessment_type']!=1 ?$validatedReviews++ && $lastReviewSettings = $review:'';
	}
 
 */
//echo $selfReviewsPast."past";	
//print_r($user);
//echo $tiers[0]['standard_name'];
//print_r($clientReviews);
//print_r($lastReviewSettings);
?> 

				<a id="addUserBtn" class="btn btn-primary pull-right execUrl vtip fixonmodal" title="Click to add a user." href="?controller=user&action=createUser&amp;ispop=1">Add user</a>				
				<h1 class="page-title">
				<?php if($isPop==0){?>
					<a href="<?php
						$args=array("controller"=>"assessment","action"=>"assessment");							
						$args["filter"]=1;						
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						Manage MyReviews
					</a> > 
				<?php } ?>	
				  Edit School Self Review 
				</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26">
						<div class="form-stmnt">
                                                    <?php
                                if(count($firstdefaultdiagnostic)>0){
                                //$firstdefaultdiagnostic=$firstdefaultdiagnostic[0]['diagnostic_id']; 
                                ?>
							<form method="post" id="edit_school_self_assessment_form" action="">
								<div class="boxBody">
									<dl class="fldList">
										<dt>School<span class="astric">*</span>:</dt>										
										<dd><div class="row"><div class="col-sm-6">
										<input type="hidden" name="assessment_id" value="<?php echo $assessment['assessment_id'];?>" />										
										<input class="internal_client_id" type="hidden" name="client_id" value="<?php echo $client['client_id']; ?>" required>
											<span class="form-control internal_client_id">
												<?php echo $client['client_name'].($client['city']!=""?", ".$client['city']:'');?>
											</span>
										</div></div></dd>
									</dl>
									
									<dl class="fldList">
										<dt>Internal Reviewer<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control internal_assessor_id" name="internal_assessor_id" required>
												<option value=""> - Select Internal Reviewer - </option>
												<?php 
												foreach($internalAssessors as $internal)
													echo "<option value=\"".$internal['user_id']."\" ".($assessment['user_ids']==$internal['user_id']?'selected=selected':'')."	 >".$internal['name']."</option>\n";
												?>
											</select>
										</div></div></dd>
									</dl>
                                                                    
                                                                        
                                                                         
									<?php if($selfReviewsPast==1){//if user has not done any self-review in the past, check other validated reviews
										  		if($validatedReviews==0)//if user has no validated review, show him 6 KPA diagnostic and do not enable tier and award scheme choice
										  		{// tier will be fixed to state and award scheme to adhyayan standard
										  		?>
										  		<dl class="fldList">
										<dt>Diagnostic<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control" name="diagnostic_id" id="diagnostic_id" required>
												<option value=""> - Select Diagnostic - </option>
												<?php
                                                                                                if($user['is_guest']==1){
                                                                                                foreach($guestdiagnostic as $diagnostic)
													  print "<option value=\"".$diagnostic['diagnostic_id']."\" ".($assessment['diagnostic_id']==$diagnostic['diagnostic_id']?'selected=selected':'').">".$diagnostic['name']."</option>\n";
                                                                                                  
                                                                                                }else{
												foreach($firstdefaultdiagnostic as $diagnostic)

												print "<option value=\"".$diagnostic['diagnostic_id']."\" ".($assessment['diagnostic_id']==$diagnostic['diagnostic_id']?'selected=selected':'').">".$diagnostic['name']."</option>\n";
                                                                                                }
												?>
											</select>
                                                                                            	
										</label>
                                                                                    <?php
                                                                                            if($user['is_guest']==1){
                                                                                            ?>
                                                                                            <span class="text-danger"><?php echo $message_guest ?></span>            
										            <?php
                                                                                            }
                                                                                            ?>
										</div></div></dd>
									        </dl>
												<input type="hidden" name="tier_id" value="3">	
												<input type="hidden" name="award_scheme_name" value="1">					
										  		<?php 	
										  		}
										  		else //if user has done a validated review in the past, show him the same tier, award and diagnostic as the last assessment done. 
										  		{//enable diagnostic choice and disable tier and award scheme choice
										  		?>
										  		<dl class="fldList">
										<dt>Diagnostic<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control" name="diagnostic_id" id="diagnostic_id" required>
												<option value=""> - Select Diagnostic - </option>
												<?php
                                                                                                if($user['is_guest']==1){
                                                                                                foreach($guestdiagnostic as $diagnostic)
													  print "<option value=\"".$diagnostic['diagnostic_id']."\" ".($assessment['diagnostic_id']==$diagnostic['diagnostic_id']?'selected=selected':'').">".$diagnostic['name']."</option>\n";
                                                                                                  
                                                                                                }else{
												foreach($lastandfreediagnostic as $diagnostic)
													  print "<option value=\"".$diagnostic['diagnostic_id']."\" ".($assessment['diagnostic_id']==$diagnostic['diagnostic_id']?'selected=selected':'').">".$diagnostic['name']."</option>\n";
                                                                                                }
                                                                                                ?>
											</select>
                                                                                            <?php
                                                                                            if($user['is_guest']==1){
                                                                                            ?>
                                                                                            <span class="text-danger"><?php echo $message_guest ?></span>            
										            <?php
                                                                                            }
                                                                                            ?>
										</div></div></dd>
									</dl>
									
									<!--<dl class="fldList">
										<dt>Tier<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
										<label class="printTxt">
										<?php 
										//foreach($tiers as $tier)
										//	$tier['standard_id']==$assessment['tier_id']? print $tier['standard_name']:'';
										?>
										</label>
										<input type="hidden" name="tier_id" value="<?php //echo $assessment['tier_id']?>">	
										</div></div></dd>
									</dl>-->
									<input type="hidden" name="tier_id" value="<?php echo $assessment['tier_id']?>">
									<dl class="fldList" style="display:none;">
										<dt>Award Scheme<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
										<label class="printTxt">
										<input type="hidden" name="award_scheme_name" value="<?php echo $assessment['award_scheme_id']?>">	
										<?php
												foreach($awardSchemes as $awardScheme)
													$awardScheme['award_scheme_id']==$assessment['award_scheme_id']? print $awardScheme['award_scheme_name']:'';
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
                                                                                                if($user['is_guest']==1){
                                                                                                foreach($guestdiagnostic as $diagnostic)
													  print "<option value=\"".$diagnostic['diagnostic_id']."\" ".($assessment['diagnostic_id']==$diagnostic['diagnostic_id']?'selected=selected':'').">".$diagnostic['name']."</option>\n";
                                                                                                  
                                                                                                }else{
												foreach($lastandfreediagnostic as $diagnostic)
													 $diagnostic['diagnostic_id']!=1 ? print "<option value=\"".$diagnostic['diagnostic_id']."\" ".($assessment['diagnostic_id']==$diagnostic['diagnostic_id']?'selected=selected':'').">".$diagnostic['name']."</option>\n":'';
                                                                                                }
                                                                                                ?>
											</select>   
                                                                                       <?php    
                                                                                       }else{
                                                                                       ?>
                                                                                         <select class="form-control" name="diagnostic_id" id="diagnostic_id" required>
												<option value=""> - Select Diagnostic - </option>
												<?php
                                                                                                if($user['is_guest']==1){
                                                                                                foreach($guestdiagnostic as $diagnostic)
													  print "<option value=\"".$diagnostic['diagnostic_id']."\" ".($assessment['diagnostic_id']==$diagnostic['diagnostic_id']?'selected=selected':'').">".$diagnostic['name']."</option>\n";
                                                                                                  
                                                                                                }else{
												foreach($freediagnostic as $diagnostic)
													 $diagnostic['diagnostic_id']!=1 ? print "<option value=\"".$diagnostic['diagnostic_id']."\" ".($assessment['diagnostic_id']==$diagnostic['diagnostic_id']?'selected=selected':'').">".$diagnostic['name']."</option>\n":'';
                                                                                                }
                                                                                                ?>
											</select>
                                                                                        <?php
                                                                                       }
                                                                                        ?>
                                                                                           <?php
                                                                                            if($user['is_guest']==1){
                                                                                            ?>
                                                                                            <span class="text-danger"><?php echo $message_guest ?></span>            
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
													echo "<option value=\"".$tier['standard_id']."\" ".($assessment['tier_id']==$tier['standard_id']?'selected=selected':'')." >".$tier['standard_name']."</option>\n";
												?>
											</select>
										</div></div></dd>
									</dl>-->
                                                                        <input type="hidden" name="tier_id" value="3">
									<input type="hidden" name="award_scheme_name" value="1">									
										<?php 
									}
									?>
                                                                      <dl class="fldList">
                                                                            <dt>Preferred Language<span class="astric">*</span>:</dt>
                                                                            <dd><div class="row"><div class="col-sm-6">
                                                                                        <select class="form-control" name="diagnostic_lang" id="diagnostic_lang_id" required>
                                                                                            <option  value=""> - Select Diagnostic Language - </option>
                                                                                            <?php
                                                                                            foreach ($languages as $language)
                                                                                                //echo "<option selected=\" ".selected."\"  value=\"".$language['language_id']."\"  >" . $language['language_words'] . "</option>\n";
                                                                                                print $assessment['language_id']==$language['language_id']?"<option selected='selected' value=\"".$language['language_id']."\">".$language['language_words']."</option>\n":"<option value=\"".$language['language_id']."\">".$language['language_words']."</option>\n";													
                                                                                            ?>
                                                                                        </select>
                                                                                    </div></div></dd>
                                                                        </dl>
								<?php if($assessment['percCompletes']=='0.00' || $assessment['percCompletes']=='0.0' || $assessment['percCompletes']=='0'   ){?>
									
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" title="Click to edit a review."  value="Update Review" class="btn btn-primary vtip">
												</div>
											</div>
										</dd>
									</dl>
								<?php } ?>	
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
							</form><?php
                                }else{
                                      echo"Default Diagnostic not assigned";    
                                  return ;    
                                }
                                ?>
						</div>
					</div>
				</div>				