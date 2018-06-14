<?php
			$isReadOnly=empty($isReadOnly)?0:1;
			$readOnlyText=$isReadOnly?'readonly="readonly"':"";
			$disabledText=$isReadOnly?'disabled="disabled"':"";
			$aqsFilled=1;
?>
			<h1 class="page-title">
				<a href="<?php
					$args=array("controller"=>"assessment","action"=>"assessment");
					if(in_array($user['user_id'],$assessment['user_ids']))
						$args["myAssessment"]=1;
					echo createUrl($args); 
					?>">
					<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
				</a>
				<?php echo $assessment['client_name']; ?>
			</h1>
			<form id="aqsFormWrapper" class="<?php echo $isReadOnly?'isReadOnly':'isEditable'; ?>" method="post" onsubmit="return false;">
				<div id="aqsForm">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr">
							<ul class="yellowTab nav nav-tabs">          
								<li class="item active"><a href="#aqs-step1" data-toggle="tab" class="vtip" title="Basic information">Step 1</a></li>
								<li class="item"><a href="#aqs-step2" data-toggle="tab" class="vtip" title="Advanced information">Step 2</a></li>
								<li class="item"><a href="#aqs-step3" data-toggle="tab" class="vtip" title="Booking related information">Step 3</a></li>
								<li class="item"><a href="#aqs-step4" data-toggle="tab" class="vtip" title="AQS contract">Step 4</a></li>
								<li class="item"><a href="#aqs-step5" data-toggle="tab" class="vtip" title="Teams related information">Step 5</a></li>
							</ul>
						</div>
					</div>  
					<div class="subTabWorkspace pad26">
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="aqs-step1">
								<h2>Basic information</h2>
								<div class="boxBody">
									<dl class="fldList">
										<dt>Referred by:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<select name="aqs[referrer_id]" autocomplete="off" class="selectpicker show-tick form-control" <?php echo $disabledText; ?>>
														<option value=""> - Select Referrer - </option>
														<?php
														$refId=empty($aqs['referrer_id'])?"":$aqs['referrer_id'];
														foreach($referrer_list as $referrer){
															echo '<option '.($refId==$referrer['referrer_id']?'selected="selected"':'').' value="'.$referrer['referrer_id'].'">'.$referrer['referrer_name'].'</option>';
														}
														?>
													</select>
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>School status:</dt>
										<dd>
											<div class="clearfix chkBpxPane">
												<div class="chkHldr"><input type="radio" autocomplete="off" name="nstatus" disabled="disabled" <?php echo $assessment['network_id']>0?'':'checked="checked"'; ?>><label class="chkF radio"><span>Standalone </span></label></div>
												<div class="chkHldr"><input type="radio" autocomplete="off" name="nstatus" disabled="disabled" <?php echo $assessment['network_id']>0?'checked="checked"':''; ?>><label class="chkF radio"><span>Have Network </span></label></div>
											</div>
										</dd>
									</dl>
									<?php if($assessment['network_id']>0){ ?>
									<dl class="fldList">
										<dt>Network name:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6"><input type="text" autocomplete="off" readonly="readonly" disabled="disabled" value="<?php echo $assessment['network_name']; ?>" class="form-control"></div>
											</div>
										</dd>
									</dl>
									<?php } ?>
									<dl class="fldList">
										<dt>Name of the school:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[school_name]" value="<?php echo empty($aqs['school_name'])?$assessment['client_name']:$aqs['school_name']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Name of the principal:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[principal_name]" value="<?php echo empty($aqs['principal_name'])?$principal['name']:$aqs['principal_name']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Principal Ph. & Email:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<div class="haveIcon"><i class="fa fa-mobile"></i><input autocomplete="off" type="text" class="form-control aqs_ph" name="aqs[principal_phone_no]" value="<?php echo empty($aqs['principal_phone_no'])?$assessment['principal_phone_no']:$aqs['principal_phone_no']; ?>" <?php echo $readOnlyText; ?>></div>
												</div>
												<div class="col-sm-6">
													<div class="haveIcon"><i class="fa fa-envelope"></i><input autocomplete="off" type="email" name="aqs[principal_email]" value="<?php echo empty($aqs['principal_email'])?$principal['email']:$aqs['principal_email']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Name of the co-ordinator:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<input type="text" name="aqs[coordinator_name]" autocomplete="off" value="<?php echo empty($aqs['coordinator_name'])?'':$aqs['coordinator_name']; ?>" class="form-control" <?php echo $readOnlyText; ?>>
													<div class="fltInfo"><i class="fa fa-info-circle vtip" title="Role of the co-ordinator appointed for AQS programme :-
				<br>Appoint one member of your school's review team as a co-ordinator for the AQS programme whom the Adhyayan team can contact for all the requirements before, during and after the AQS programme.
				<br>The role will include:
				<br>&#8226;	Point of contact for Adhyayan for all the information required for the programme
				<br>&#8226;	Arranging for all the resources required for the programme from Day 1 to Day 5
				<br>&#8226;	Arranging for the interview slots with students & parents for self-review and external review"></i></div>
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Co-ordinator Ph. & Email:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<div class="haveIcon"><i class="fa fa-mobile"></i><input type="text" autocomplete="off" name="aqs[coordinator_phone_number]" value="<?php echo empty($aqs['coordinator_phone_number'])?'':$aqs['coordinator_phone_number']; ?>" class="form-control aqs_ph" <?php echo $readOnlyText; ?>></div>
												</div>
												<div class="col-sm-6">
													<div class="haveIcon"><i class="fa fa-envelope"></i><input type="email" autocomplete="off" name="aqs[coordinator_email]" value="<?php echo empty($aqs['coordinator_email'])?'':$aqs['coordinator_email']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>School Accountant Name:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[accountant_name]" value="<?php echo empty($aqs['accountant_name'])?'':$aqs['accountant_name']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>School Accountant Ph. & Email:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<div class="haveIcon"><i class="fa fa-mobile"></i><input type="text" autocomplete="off" name="aqs[accountant_phone_no]" value="<?php echo empty($aqs['accountant_phone_no'])?'':$aqs['accountant_phone_no']; ?>" class="form-control aqs_ph" <?php echo $readOnlyText; ?>></div>
												</div>
												<div class="col-sm-6">
													<div class="haveIcon"><i class="fa fa-envelope"></i><input type="email" autocomplete="off" name="aqs[accountant_email]" value="<?php echo empty($aqs['accountant_email'])?'':$aqs['accountant_email']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>School Address:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[school_address]" value="<?php echo empty($aqs['school_address'])?'':$aqs['school_address']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>School Website & Email:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<div class="haveIcon"><i class="fa fa-link"></i><input autocomplete="off" type="url" name="aqs[school_website]" value="<?php echo empty($aqs['school_website'])?'':$aqs['school_website']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
												</div>
												<div class="col-sm-6">
													<div class="haveIcon"><i class="fa fa-envelope"></i><input autocomplete="off" type="email" name="aqs[school_email]" value="<?php echo empty($aqs['school_email'])?'':$aqs['school_email']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Billing name:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[billing_name]" id="billing_name" value="<?php echo empty($aqs['billing_name'])?'':$aqs['billing_name']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
												<div class="col-sm-6">                                    
													<div class="clearfix chkBpxPane">
														<div class="chkHldr" style="width:300px;"><input autocomplete="off" type="checkbox" value="1" <?php echo $disabledText; ?> class="bName_same" name="other[bName_same]"><label class="chkF checkbox"><span>Same as school name</span></label></div>
													</div>
												</div>
											</div>
										</dd>
									</dl>                    
									<dl class="fldList">
										<dt>Billing address:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[billing_address]" id="billing_address" value="<?php echo empty($aqs['billing_address'])?'':$aqs['billing_address']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
												<div class="col-sm-6">
													<div class="clearfix chkBpxPane">
														<div class="chkHldr" style="width:300px;"><input autocomplete="off" type="checkbox" value="1" <?php echo $disabledText; ?> class="bAddress_same" name="other[bAddress_same]"><label class="chkF checkbox"><span>Same as school address</span></label></div>
													</div>
												</div>
											</div>
										</dd>
									</dl>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane fade in" id="aqs-step2">
								<h2>Advanced information</h2>
								<div class="boxBody">
									<dl class="fldList">
										<dt>Board affiliation:</dt>
										<dd>
											<div class="clearfix advInfo">
											<?php
											$board_id=empty($aqs['board_id'])?'':$aqs['board_id'];
											foreach($board_list as $board)
												echo '<div class="chkHldr"><input autocomplete="off" type="radio" name="aqs[board_id]" '.($board_id==$board['board_id']?'checked="checked"':'').' value="'.$board['board_id'].'" '.$disabledText.'><label class="chkF radio"><span>'.$board['board'].'</span></label></div>';
											?>
											</div> 
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Type of school:</dt>
										<dd>
											<div class="clearfix advInfo">
											<?php
											$school_type_id=empty($aqs['school_type_id'])?'':$aqs['school_type_id'];
											foreach($school_type_list as $school_type)
												echo '<div class="chkHldr" '.($school_type['school_type_id']==5?'style="width:240px;"':'').'><input autocomplete="off" type="radio" name="aqs[school_type_id]" value="'.$school_type['school_type_id'].'" '.($school_type_id==$school_type['school_type_id']?'checked="checked"':'').' '.$disabledText.'><label class="chkF radio"><span>'.$school_type['school_type'].'</span></label></div>';
											?>
											</div> 
										</dd>
									</dl>
									<dl class="fldList">
										<dt>IT Support:</dt>
										<dd>
											<div class="clearfix advInfo">
											<?php
											$it_support_ids=empty($aqs['it_support_ids'])?array():$aqs['it_support_ids'];
											foreach($school_it_support_list as $it_support)
												echo '<div class="chkHldr" ><input autocomplete="off" type="checkbox" name="other[it_support][]" '.(in_array($it_support['it_support_id'],$it_support_ids)?'checked="checked"':'').' value="'.$it_support['it_support_id'].'" '.$disabledText.'><label class="chkF checkbox"><span>'.$it_support['it_support'].'</span></label></div>';
											?>
											</div> 
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Number of gates for entry/exit and their locations:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-4">
													<select name="aqs[no_of_gates]" autocomplete="off" class="selectpicker show-tick form-control" <?php echo $disabledText; ?>>
														<option value=""> - Select no. of gates - </option>
													<?php
													$g_cnt=empty($aqs['no_of_gates'])?'':$aqs['no_of_gates'];
													for($i=1;$i<7;$i++)
														echo '<option '.($i==$g_cnt?'selected="selected"':'').' value="'.$i.'">'.$i.'</option>';
													?>
													</select>
												</div>
												<div class="col-sm-8"><input autocomplete="off" type="text" class="form-control" name="aqs[gates_location]" value="<?php echo empty($aqs['gates_location'])?'':$aqs['gates_location']; ?>" placeholder="Location of gates" <?php echo $readOnlyText; ?>></div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Number of buildings:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<select autocomplete="off" class="selectpicker show-tick form-control" name="aqs[no_of_buildings]" <?php echo $readOnlyText; ?>>
														<option value=""> - Select no. of buildings - </option>
													<?php
													$b_cnt=empty($aqs['no_of_buildings'])?'':$aqs['no_of_buildings'];
													for($i=1;$i<6;$i++)
														echo '<option '.($i==$b_cnt?'selected="selected"':'').' value="'.$i.'">'.$i.'</option>';
													?>
													</select>
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Distance from the main buildings (in meters):</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<select autocomplete="off" name="aqs[distance_main_building]" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
														<option value=""> - Select distance - </option>
													<?php $distance=empty($aqs['distance_main_building'])?'':$aqs['distance_main_building']; 
														$distances=array(25,50,100,500,1000,"1000+");
														foreach($distances as $d)
															echo '<option '.($d==$distance?'selected="selected"':'').' value="'.$d.'">'.$d.'</option>';
													?>
													</select>
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Classes:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-12">
													<div class="row">
														<div class="col-sm-6">
															<div class="dropCapHldr">
																<label>From</label>
																<div class="dropFld">
																	<select autocomplete="off" name="aqs[classes_from]" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
																		<option value=""> - Select Class - </option>
																		<?php
																		$cFrom=empty($aqs['classes_from'])?'':$aqs['classes_from']; 
																		foreach($school_class_list as $school_class){
																			echo '<option '.($cFrom==$school_class['class_id']?'selected="selected"':'').' value="'.$school_class['class_id'].'">'.$school_class['class_name'].'</option>';
																		}
																		?>
																	</select>
																</div>
															</div>
														</div>
														<div class="col-sm-6">
															<div class="dropCapHldr">
																<label>To</label>
																<div class="dropFld">
																	<select autocomplete="off" name="aqs[classes_to]" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
																		<option value=""> - Select Class - </option>
																		<?php
																		$cTo=empty($aqs['classes_to'])?'':$aqs['classes_to']; 
																		foreach($school_class_list as $school_class){
																			echo '<option '.($cTo==$school_class['class_id']?'selected="selected"':'').' value="'.$school_class['class_id'].'">'.$school_class['class_name'].'</option>';
																		}
																		?>
																	</select>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</dd>
									</dl>
									
									<dl class="fldList">
										<dt>Total student strength of the school:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<select name="aqs[no_of_students]" autocomplete="off" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
														<option value=""> - Select no. of students - </option>
													<?php $strnth=empty($aqs['no_of_students'])?'':$aqs['no_of_students']; 
														$strnths=array("50-250","250-1000","1000-1500","1500-2000","2000-3000","3000+");
														foreach($strnths as $s)
															echo '<option '.($s==$strnth?'selected="selected"':'').' value="'.$s.'">'.$s.'</option>';
													?>
													</select>
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Student type (Gender):</dt>
										<dd>
											<div class="clearfix advInfo">
											<?php
											$stId=empty($aqs['student_type_id'])?'':$aqs['student_type_id']; 
											foreach($student_type_list as $stype)
												echo '<div class="chkHldr"><input autocomplete="off" type="radio" name="aqs[student_type_id]" '.($stype['student_type_id']==$stId?'checked="checked"':'').' value="'.$stype['student_type_id'].'" '.$disabledText.'><label class="chkF radio"><span>'.$stype['studen_type'].'</span></label></div>';
											?>
											</div> 
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Annual fee per child(in <i class="fa fa-inr inherit"></i>):</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<select name="aqs[annual_fee]" autocomplete="off" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
														<option value=""> - Select fee - </option>
													<?php $fee=empty($aqs['annual_fee'])?'':$aqs['annual_fee']; 
														$fees=array("below 6000","6000-12000","12000-24000","24000-50000","50000 and above");
														foreach($fees as $f)
															echo '<option '.($f==$fee?'selected="selected"':'').' value="'.$f.'">'.$f.'</option>';
													?>
													</select>
												</div>
											</div>
										</dd>
									</dl>
									
									<dl class="fldList">
										<dt class="noFlt">School timings of all sections:</dt>
										<dd class="nomgn nobg">
											<div class="row">
												<div class="col-sm-12 clearfix">
												<?php
												foreach($school_level_list as $school_level){
													$school_timing=array("id"=>0,"start_time"=>"","end_time"=>"");
													$disText='disabled="disabled"';
													if(!empty($aqs['school_timing'][$school_level['school_level_id']])){
														$school_timing=$aqs['school_timing'][$school_level['school_level_id']];
														$disText='';
													}
													
												?>
													<div class="schSectBox">
														<div class="schSecHdr clearfix">
															<h4><?php echo $school_level['school_level']; ?>:</h4>
															<div class="chkHldr"><input autocomplete="off" type="checkbox" class="TTNotApplicable" name="other[timing][<?php echo $school_level['school_level_id']; ?>][not_applicable]" value="1" <?php echo $disabledText.($school_timing['id']>0?'':' checked="checked"'); ?>><label class="chkF checkbox"><span>Not Applicable</span></label></div>
														</div>
														<div class="schSectBody" <?php echo $school_timing['id']>0?'':'style="display:none;"'; ?>>
															<div class="row">
																<div class="col-sm-6 lblInpsec clearfix">
																	<label>From</label>
																	<div class="inpFld">
																		<div class="input-group time">
																			<input type="text" autocomplete="off" class="form-control" name="other[timing][<?php echo $school_level['school_level_id']; ?>][start_time]" value="<?php echo $school_timing['start_time']; ?>" <?php echo $readOnlyText." ".$disText; ?> >
																			<span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
																		</div>
																	</div>
																</div>
																<div class="col-sm-6 lblInpsec clearfix">
																	<label>To</label>
																	<div class="inpFld">
																		<div class="input-group time">
																			<input type="text" autocomplete="off" class="form-control" name="other[timing][<?php echo $school_level['school_level_id']; ?>][end_time]" value="<?php echo $school_timing['end_time']; ?>" <?php echo $readOnlyText." ".$disText; ?>>
																			<span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												<?php } ?>
												</div>
											</div>
										</dd>
									</dl>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane fade in" id="aqs-step3">
								<h2>Booking related information</h2>
								<div class="boxBody">
									<dl class="fldList brdW">
										<dt>School preferred dates for AQS:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-9">
													<div class="row">
														<div class="col-sm-6 lblInpsec clearfix">
															<label>From</label>
															<div class="inpFld">
																<div class="input-group aqsDate">
																	<input autocomplete="off" type="text" class="form-control" placeholder="MM/DD/YYYY" name="aqs[school_aqs_pref_start_date]" value="<?php echo empty($aqs['school_aqs_pref_start_date'])?'':$aqs['school_aqs_pref_start_date']; ?>" <?php echo $readOnlyText; ?>>
																	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
																</div>
															</div>
														</div>
														<div class="col-sm-6 lblInpsec clearfix">
															<label>To</label>
															<div class="inpFld">
																<div class="input-group aqsDate">
																	<input autocomplete="off" type="text" class="form-control" placeholder="MM/DD/YYYY" name="aqs[school_aqs_pref_end_date]" value="<?php echo empty($aqs['school_aqs_pref_end_date'])?'':$aqs['school_aqs_pref_end_date']; ?>" <?php echo $readOnlyText; ?>>
																	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</dd>
									</dl>
									<?php
									$travel_arrg=empty($aqs["travel_arrangement_for_adhyayan"])?0:$aqs["travel_arrangement_for_adhyayan"];
									?>
									<dl class="fldList brdW">
										<dt>Travel arrangements:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-7">
													<div class="clearfix advInfo">
														<div class="chkHldr"><input autocomplete="off" class="travel_arrang" type="radio" name="aqs[travel_arrangement_for_adhyayan]" <?php echo $travel_arrg==1?'checked="checked"':''; ?> value="1" <?php echo $disabledText; ?>><label class="chkF radio"><span>By School</span></label></div>
														<div class="chkHldr"><input autocomplete="off" class="travel_arrang" type="radio" name="aqs[travel_arrangement_for_adhyayan]" <?php echo $travel_arrg==2?'checked="checked"':''; ?> value="2" <?php echo $disabledText; ?>><label class="chkF radio"><span>By Adhyayan</span></label></div>
														<div class="fl padT6"><a href="#"><strong>More Info ...</strong></a></div>
													</div> 
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList brdW travel-arr-info" <?php echo $travel_arrg==2?'':'style="display:none;"'; ?>>
										<dt>Name of the nearest Airport:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6"><input autocomplete="off" type="text" class="form-control" value="<?php echo empty($aqs['airport_name'])?'':$aqs['airport_name']; ?>" name="aqs[airport_name]" <?php echo $readOnlyText; ?>></div>
											</div>
										</dd>
									</dl>
									<dl class="fldList brdW travel-arr-info" <?php echo $travel_arrg==2?'':'style="display:none;"'; ?>>
										<dt>Name of the nearest railway station:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6"><input autocomplete="off" type="text" class="form-control" value="<?php echo empty($aqs['rail_station_name'])?'':$aqs['rail_station_name']; ?>" name="aqs[rail_station_name]" <?php echo $readOnlyText; ?>></div>
											</div>
										</dd>
									</dl>
									<?php
									$accm_arrg=empty($aqs["accomodation_arrangement_for_adhyayan"])?0:$aqs["accomodation_arrangement_for_adhyayan"];
									?>
									<dl class="fldList brdW">
										<dt>Accommodation arrangements:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-7">
													<div class="clearfix advInfo">
														<div class="chkHldr"><input autocomplete="off" class="accom_arrang" type="radio" name="aqs[accomodation_arrangement_for_adhyayan]" <?php echo $accm_arrg!=1?'':'checked="checked"'; ?> value="1" <?php echo $disabledText; ?>><label class="chkF radio"><span>By School</span></label></div>
														<div class="chkHldr"><input autocomplete="off" class="accom_arrang" type="radio" name="aqs[accomodation_arrangement_for_adhyayan]" <?php echo $accm_arrg!=2?'':'checked="checked"'; ?> value="2" <?php echo $disabledText; ?>><label class="chkF radio"><span>By Adhyayan</span></label></div>
														<div class="fl padT6"><a href="#"><strong>More Info ...</strong></a></div>
													</div> 
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList brdW hotel-arr-info" <?php echo $accm_arrg==2?'':'style="display:none;"'; ?>>
										<dt>Name of the nearest Hotel:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6"><input autocomplete="off" type="text" class="form-control" value="<?php echo empty($aqs['hotel_name'])?'':$aqs['hotel_name']; ?>" name="aqs[hotel_name]" <?php echo $readOnlyText; ?>></div>
											</div>
										</dd>
									</dl>
									<dl class="fldList brdW hotel-arr-info" <?php echo $accm_arrg==2?'':'style="display:none;"'; ?>>
										<dt>Distance between the hotel and school:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-3"><input autocomplete="off" type="text" class="form-control number" value="<?php echo empty($aqs['hotel_school_distance'])?'':$aqs['hotel_school_distance']; ?>" name="aqs[hotel_school_distance]" <?php echo $readOnlyText; ?>></div>
												<div class="col-sm-3 pt10 text-left">(in KM)</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList brdW hotel-arr-info" <?php echo $accm_arrg==2?'':'style="display:none;"'; ?>>
										<dt>Distance between hotel and railway station:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-3"><input autocomplete="off" type="text" class="form-control number" value="<?php echo empty($aqs['hotel_station_distance'])?'':$aqs['hotel_station_distance']; ?>" name="aqs[hotel_station_distance]" <?php echo $readOnlyText; ?>></div>
												<div class="col-sm-3 pt10 text-left">(in KM)</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList brdW hotel-arr-info" <?php echo $accm_arrg==2?'':'style="display:none;"'; ?>>
										<dt>Distance between hotel and Airport station:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-3"><input autocomplete="off" type="text" class="form-control number" value="<?php echo empty($aqs['hotel_airport_distance'])?'':$aqs['hotel_airport_distance']; ?>" name="aqs[hotel_airport_distance]" <?php echo $readOnlyText; ?>></div>
												<div class="col-sm-3 pt10 text-left">(in KM)</div>
											</div>
										</dd>
									</dl>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane fade in" id="aqs-step4">
								<h2>AQS contract</h2>
								<div class="boxBody">
									AQS contract to be downloaded, signed and uploaded or couriered to office address. (<a href="<?php echo SITEURL."public/pdf/AQS_Blank_Letter_of_Agreement.pdf"; ?>">Download here</a>)
								</div>
							</div>
							<div role="tabpanel" class="tab-pane fade" id="aqs-step5">
								<h2>Teams related information</h2>
								<div class="boxBody">
									<p><b>List of the school team chosen for Adhyayan Quality Standard Programme:</b></p>
									<div class="tableHldr teamsInfoHldr school_team team_table">
										<?php if(!$isReadOnly){ ?><a href="#" class="fltdAddRow" data-type="school"><i class="fa fa-plus"></i></a><?php } ?>
										<table class="table customTbl">
											<thead>
												<tr>
													<th>Sr. No.</th>
													<th>Name</th>
													<th>Designation</th>
													<th>Language preference for training materials<br><small>English / Hindi / Marathi / Bengali / Gujrati</small></th>
													<th>Email</th>
													<th>Mobile No.</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
											<?php
											if(count($school_team)){
												$t_cnt=0;
												foreach($school_team as $team){
													$t_cnt++;
													echo aqsDataModel::getAqsTeamHtmlRow($t_cnt,1,$team['name'],$team['designation'],$team['language'],$team['email'],$team['mobile'],$readOnlyText,(!$isReadOnly && $t_cnt>0?1:0));
												}
											}else
												echo aqsDataModel::getAqsTeamHtmlRow(1,1,'','','','','',$readOnlyText,0);
											?>
											</tbody>
										</table>
									</div>
								<?php if(in_array("view_all_assessments",$user['capabilities'])){ ?>
									<p><b>List of the Adhyayan team chosen for Adhyayan Quality Standard Programme:</b></p>
									<div class="tableHldr teamsInfoHldr adhyayan_team team_table">
										<?php if(!$isReadOnly){ ?><a href="#" class="fltdAddRow" data-type="adhyayan"><i class="fa fa-plus"></i></a><?php } ?>
										<table class="table customTbl">
											<thead>
												<tr>
													<th>Sr. No.</th>
													<th>Name</th>
													<th>Designation</th>
													<th>Language preference for training materials<br><small>English / Hindi / Marathi / Bengali / Gujrati</small></th>
													<th>Email</th>
													<th>Mobile No.</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
											<?php
											if(count($adhyayan_team)){
												$t_cnt=0;
												foreach($adhyayan_team as $team){
													$t_cnt++;
													echo aqsDataModel::getAqsTeamHtmlRow($t_cnt,0,$team['name'],$team['designation'],$team['language'],$team['email'],$team['mobile'],$readOnlyText,(!$isReadOnly && $t_cnt>0?1:0));
												}
											}else
												echo aqsDataModel::getAqsTeamHtmlRow(1,0,'','','','','',$readOnlyText,0);
											?>
											</tbody>
										</table>
									</div>
								<?php } ?>
								</div>
							</div>
						</div>
					</div>
					
					<div class="fr clearfix">
						<?php if(!$isReadOnly){ ?>
						<input type="button" autocomplete="off"  id="saveAqsForm" class="fl nuibtn saveBtn" value="Save" />
						<?php if($assessment['aqs_status']==0){ ?><input type="button" autocomplete="off" id="submitAqsForm" <?php echo $aqsFilled==1?"":'disabled="disabled"'; ?> class="fl nuibtn submitBtn" value="Submit" /><?php } ?>
						<?php } ?>
					</div>
					
					<div class="clearfix"></div>
					
				</div>
				<input type="hidden" name="assessment_id" value="<?php echo $assessment_id; ?>" />
				<div id="validationErrors"></div>
		  </form>