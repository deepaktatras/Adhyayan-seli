<?php
//echo '<pre>';
//print_r($facilitators);
//echo '</pre>';
$externalClient = explode(',',$assessment['user_ids']);
//$externalClient = $externalClient[1];
$externalClient = end($externalClient);
$allAssessors  = array($externalClient);
$subRolesT = $assessment['subroles'];												
$subRolesT = !empty($subRolesT)? explode(',',$subRolesT):'';
$i=1;													
if(!empty($subRolesT))
foreach($subRolesT as $subRoleT)
{
	$i++;
	$subRoleT = explode('_',$subRoleT);
	$memberT = $subRoleT[1];
	//$subRT = $subRoleT[1];
	array_push($allAssessors,$memberT);
}
$hideDiagnostics = explode(',',$hideDiagnostics['hidediagnostics']);
$externalRevPerc = null;
$internalRevPerc = null;
$rev = explode(',',$assessment['percCompletes']);
if(count($rev)>1){   
    $externalRevPerc = $rev[1];
    $internalRevPerc = $rev[0];
}
else
    $externalRevPerc = $internalRevPerc = $assessment['percCompletes'];
$isEditable = ($disabled!==0)?$disabled : ( ($externalRevPerc>0)?'disabled':'');
 
//print_r($assessment);
?>				
				<a id="addUserBtn" class="btn btn-primary pull-right execUrl vtip fixonmodal" title="Click to add a user." href="?controller=user&action=createUser&ispop=1">Add user</a>
				<h1 class="page-title">
				<?php if($isPop==0){?>
					<a href="<?php
						$args=array("controller"=>"assessment","action"=>"assessment");								
						$args["filter"]=1;		
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						Manage MyReviews
					</a> &mdash;
				<?php }?>	
				 Edit Review - <?php echo $assessment['client_name']; ?>
				</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26">
						<div class="form-stmnt">
							<form method="post" id="edit_college_assessment_form" action="">
							<input type="hidden" name="client_id" value="<?php echo $assessment['client_id']; ?>" />
                                                        <input type="hidden" name="diagnostic_lang" value="<?php echo DEFAULT_LANGUAGE; ?>" />
								<div class="boxBody">
									<dl class="fldList">
										<dt>College<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
										<input type="hidden" name="assessment_id" id="assessment_id" value="<?php echo $assessment_id; ?>" />
											<select class="form-control internal_client_id" name="client_id" required disabled readonly>
												<option value=""> - Select School - </option>
												<?php
												foreach($clients as $client)
													print $assessment['client_id']==$client['client_id']? "<option selected=\"selected\" value=\"".$client['client_id']."\">".$client['client_name'].($client['city']!=""?", ".$client['city']:'')."</option>\n":"<option value=\"".$client['client_id']."\">".$client['client_name'].($client['city']!=""?", ".$client['city']:'')."</option>\n";
												?>
											</select>
										</div></div></dd>
									</dl>
									
									<dl class="fldList">
										<dt>Internal Reviewer<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control internal_assessor_id" name="internal_assessor_id" required <?php echo ($internalRevPerc>0||$externalRevPerc>0)?'disabled':'' ?> >
												<option value=""> - Select Internal Reviewer - </option>
											</select>
										</div></div></dd>
									</dl>
																		
							<!---external team-->
							<div class="clr" style="margin-top:20px;"></div>
							<div class="boxBody">									
									<p><b>External Review team:</b><span class="astric">*</span></p>
									<div class="tableHldr teamsInfoHldr school_team team_table noShadow">
                                                                        <?php if(!$disabled){?>
									<a href="javascript:void(0)" class="extteamAddRow"><i class="fa fa-plus"></i></a>
									<?php }?>
                                                                        <table class='table customTbl'>
									<thead>
										<tr><th style="width:5%">Sr. No.</th><th style="width:35%">College</th><th style="width:25%">External Reviewer Role</th><th style="width:25%">External Reviewer Team member</th><th style="width:5%;"></th></tr>
									</thead>
									<tbody>
										<tr class='team_row'><td class='s_no'>1</td>
										<td><select class="form-control external_client_id" id="external_client_id" required <?php echo $isEditable?>>
												<option value=""> - Select College - </option>
												<?php
												foreach($clients as $client)
													echo "<option value=\"".$client['client_id']."\"".($assessment['external_client']==$client['client_id']?"selected=selected":'').">".$client['client_name'].($client['city']!=""?", ".$client['city']:'')."</option>\n";
												?>
											</select></td>
										<td>Lead/Senior Associate</td>	
                                                                                        <td><select class="form-control external_assessor_id" name="external_assessor_id" id="lead_assessor" required  <?php echo $isEditable; ?>>
													<option value=""> - Select Member - </option>
													<?php
													foreach($externalAssessors as $index=>$ext)
													{
														 if($externalClient==$ext['user_id'] || !in_array($ext['user_id'],$allAssessors))
														 echo "<option value=\"".$ext['user_id']."\"".($externalClient==$ext['user_id']?'selected=selected':'').">".$ext['name']."</option>";
													}	 
												?>
												</select></td>
											<td></td>	
										</tr>
                                                                                <?php
//                                                                                if($isEditable!==''){
//                                                                                    echo '<input type="hidden" id="external_client_id" class="external_client_id" value="'.$assessment['external_client'].'" name="external_client_id">';
//                                                                                    echo '<input type="hidden" id="external_assessor_id" class="external_assessor_id" value="'.$externalClient.'" name="external_assessor_id">';
//                                                                                }
                                                                                ?>
										<?php
											//print_r($externalAssessors);
											$subRoles = $assessment['subroles'];												
												$subRoles = !empty($subRoles)? explode(',',$subRoles):'';
												$sn=2;													
												if(!empty($subRoles))													
													foreach($subRoles as $subRole)
													{
														$rowTeam = explode('_',$subRole);
														$teamExternalClientId = $rowTeam[0];
														$teamExternalMemberId = $rowTeam[1];
														$teamExternalRoleId = $rowTeam[2];
														$row = '<tr class="team_row">
										<td class="s_no">'.$sn.'</td>';
//                                                                                                                if($disabled!==0){
//                                                                                                                    echo '<input type="hidden" id="team_external_client_id'.$sn.'" class="team_external_client_id" value="'.$teamExternalClientId.'" name="externalReviewTeam[clientId][]">';
//                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalRoleId.'" name="externalReviewTeam[role][]">';
//                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalMemberId.'" name="externalReviewTeam[member][]" id="team_external_assessor_id'.$sn.'">';
//                                                                                                                }
														$row .= '<td><select class="form-control team_external_client_id" id="team_external_client_id'.$sn.'" required name="externalReviewTeam[clientId][]" '.$disabled.'>
																	<option value=""> - Select School - </option>';
														foreach($clients as $client)
															$row .= "<option value=\"".$client['client_id']."\"".($teamExternalClientId==$client['client_id']?'selected=selected':'').">".$client['client_name'].($client['city']!=""?", ".$client['city']:'')."</option>\n";
																
															$row .= '</select></td>';
																
															$row .=	'<td><select class="form-control " name="externalReviewTeam[role][]" required '.$disabled.'>
											<option value=""> - Select Role - </option>
											';
															foreach($externalReviewRoles as $externalReviewer)
																$row .= $externalReviewer['sub_role_id']=='1'?'':"<option value=\"".$externalReviewer['sub_role_id']."\"".($externalReviewer['sub_role_id']==$teamExternalRoleId?'selected=selected':'').">".$externalReviewer['sub_role_name']."</option>";
																																		
																$row .= '</select></td>
										<td><select class="form-control team_external_assessor_id" name="externalReviewTeam[member][]" id="team_external_assessor_id'.$sn.'" required '.$disabled.'>
											<option value=""> - Select Member - </option>';
											foreach($externalAssessorsTeam[$sn-2] as $index=>$ext)
													{
														
														 if($teamExternalMemberId==$ext['user_id'] || !in_array($ext['user_id'],$allAssessors))
														$row .= "<option value=\"".$ext['user_id']."\"".($teamExternalMemberId==$ext['user_id']?'selected=selected':'').">".$ext['name']."</option>";																											
													}
																
																
										$row .= '</select></td>
										<td>';
                                                                                if($disabled==''){
                                                                                    $row .= '<a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a>';
                                                                                }        
                                                                                $row .= '</td>
									</tr>';
																echo $row;
																$sn++;
													}													
										?>										
									</tbody>
									</table>
									</div>
									</div>
							<!--external team ends-->
                                                        
                                                       
                                                        
                                                       <div class="boxBody">
                                                            <div class="clr" style="margin-top:20px;"></div>
                                                            <p><b>Facilitator:</b></p>
                                                            <div class="tableHldr teamsInfoHldr school_team facilitator_table noShadow">
                                                                <?php if (!$disabled) { ?>
                                                                    <a href="javascript:void(0)" class="extteamAddRow"><i class="fa fa-plus"></i></a>
                                                                <?php } ?>
                                                                <table class='table customTbl'>
                                                                    <thead>
                                                                        <tr><th style="width:5%">Sr. No.</th><th style="width:35%">College</th><th style="width:25%">Facilitator Role</th><th style="width:25%">Facilitator Team member</th><th style="width:5%;"></th></tr>	
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr class='facilitator_row'><td class='s_no'>1</td>
                                                                            <td><select class="form-control facilitator_client_id" name="facilitator_client_id" id="facilitator_client_id"  <?php //echo $disabled ?>>
                                                                                    <option value=""> - Select College - </option>
                                                                                    <?php
                                                                                    foreach ($clients as $client)
                                                                                    //echo "<option value=\"".$client['client_id']."\">".$client['client_name']."</option>\n";
                                                                                        print isset($assessment['f_client_id']) && $assessment['f_client_id'] == $client['client_id'] ? "<option selected='selected' value=\"" . $client['client_id'] . "\">" . $client['client_name'] . "</option>\n" : "<option value=\"" . $client['client_id'] . "\">" . $client['client_name'] . "</option>\n";
                                                                                    ?>
                                                                                </select></td>
                                                                            <td>Lead/Senior Associate</td>
                                                                            <td><select class="form-control facilitator_id" name="facilitator_id"  <?php //echo $disabled?>>
                                                                                    <option value=""> - Select Facilitator - </option>

                                                                                </select></td>
                                                                            <td></td>	
                                                                        </tr>	
                                                                            <?php
                                                                //print_r($externalAssessors);
                                                                        $subRoles = $assessment['subroles'];	
                                                                        //print_r($subRoles);
                                                                        $subRoles = !empty($subRoles)? explode(',',$subRoles):'';
                                                                        $sn=2;													
                                                                        if(!empty($facilitators)) {													
                                                                                foreach($facilitators as $data)
                                                                                {
                                                                                        //$rowTeam = explode('_',$subRole);
                                                                                        $facilitatorClientId = $data['client_id'];
                                                                                        $facilitatorMemberId = $data['user_id'];
                                                                                        //print_r($facilitatorTeam);
                                                                                        $facilitatorRoleId = $data['sub_role_id'];
                                                                                        if($facilitatorRoleId>2) {
                                                                                        $row = '<tr class="facilitator_row"><td class="s_no">'.$sn.'</td>';
    //                                                                                                                if($disabled!==0){
    //                                                                                                                    echo '<input type="hidden" id="team_external_client_id'.$sn.'" class="team_external_client_id" value="'.$teamExternalClientId.'" name="externalReviewTeam[clientId][]">';
    //                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalRoleId.'" name="externalReviewTeam[role][]">';
    //                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalMemberId.'" name="externalReviewTeam[member][]" id="team_external_assessor_id'.$sn.'">';
    //                                                                                                                }
                                                                                        $row .= '<td><select class="form-control team_facilitator_client_id" id="team_facilitator_client_id'.$sn.'" required name="facilitatorReviewTeam[clientId][]" '.$disabled.'>
                                                                                                                <option value=""> - Select School - </option>';
                                                                                        foreach($clients as $client)
                                                                                                $row .= "<option value=\"".$client['client_id']."\"".($facilitatorClientId==$client['client_id']?'selected=selected':'').">".$client['client_name'].($client['city']!=""?", ".$client['city']:'')."</option>\n";

                                                                                                $row .= '</select></td>';

                                                                                                $row .=	'<td><select class="form-control " name="facilitatorReviewTeam[role][]" ><option value=""> - Select Role - </option>';
                                                                                                foreach($externalReviewRoles as $externalReviewer)
                                                                                                        $row .= $externalReviewer['sub_role_id']=='1' || $externalReviewer['sub_role_id']=='2'?'':"<option value=\"".$externalReviewer['sub_role_id']."\"".($externalReviewer['sub_role_id']==$facilitatorRoleId?'selected=selected':'').">".$externalReviewer['sub_role_name']."</option>";

                                                                                                        $row .= '</select></td>
                                                                                                                 <td><select class="form-control team_external_facilitator_id" name="facilitatorReviewTeam[member][]" id="team_external_facilitator_id'.$sn.'" required '.$disabled.'>
                                                                                                                 <option value=""> - Select Member - </option>';
                                                                                                        foreach($facilitatorTeam[$facilitatorMemberId] as $index=>$ext) {
                                                                                                            
                                                                                                           
                                                                                                                if($facilitatorRoleId==$ext['user_id'] || !in_array($ext['user_id'],$allAssessors))
                                                                                                                     $row .= "<option value=\"".$ext['user_id']."\"".($facilitatorMemberId==$ext['user_id']?'selected=selected':'').">".$ext['name']."</option>";																											
                                                                                                        }


                                                                                                        $row .= '</select></td><td>';
                                                                                                        if($disabled==''){
                                                                                                            $row .= '<a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a>';
                                                                                                        }        
                                                                                                        $row .= '</td></tr>';
                                                                                                        echo $row;
                                                                                                        $sn++;
                                                                                }
                                                                                }
                                                                        }
                                                        ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                        <div class="boxBody">									
                                                            <p><b>College preferred dates for AQS:</b></p>
                                                            <div class="tableHldr noShadow">
                                                                <table class='table customTbl'>
                                                                    <tbody>
                                                                        <tr class='team_row'>
                                                                            <td class="trans" style="width:45%">
                                                                                <label>Start Date</label><span class="astric">*</span>:</p>
                                                                                <div class="inpFld">
                                                                                    <div class="input-group aqsDate aqs_sdate">
                                                                                        <input autocomplete="off" id="aqsf_school_aqs_pref_start_date"  type="text" class="form-control external_assessor_id" placeholder="DD-MM-YYYY" name="school_aqs_pref_start_date" value="<?php echo empty($assessment['school_aqs_pref_start_date'])?'':$assessment['school_aqs_pref_start_date'];  ?>" readonly="readonly">
                                                                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td style="width:45%">
                                                                                <label>End Date</label><span class="astric">*</span>:</p>
                                                                                <div class="inpFld">
                                                                                    <div class="input-group aqsDate aqs_edate">
                                                                                        <input autocomplete="off" id="aqsf_school_aqs_pref_end_date" type="text" class="form-control external_assessor_id" placeholder="DD-MM-YYYY" name="school_aqs_pref_end_date" value="<?php echo empty($assessment['school_aqs_pref_end_date'])?'':$assessment['school_aqs_pref_end_date'];  ?>" readonly="readonly">
                                                                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                    </tbody>            
                                                                </table>

                                                            </div>

                                                        </div>
                                                                       <!--<dl class="fldList">
										<dt>Facilitator:</dt>
										<dd><div class="row">
                                                                                <div class="col-sm-6">
										<select class="form-control facilitator_client_id" id="facilitator_client_id"  <?php //echo $disabled?>>
												<option value=""> - Select School - </option>
												<?php
												/*foreach($clients as $client)
													//echo "<option value=\"".$client['client_id']."\">".$client['client_name']."</option>\n";
												print $assessment['f_client_id']== $client['client_id']?"<option selected='selected' value=\"".$client['client_id']."\">".$client['client_name']."</option>\n":"<option value=\"".$client['client_id']."\">".$client['client_name']."</option>\n";

                                                                                                   */ ?>
											</select>
                                                                            
                                                                                            
										</div>
                                                                                <div class="col-sm-6">
										<select class="form-control facilitator_id" name="facilitator_id"  <?php //echo $disabled?>>
												<option value=""> - Select Facilitator - </option>
												
											</select>
                                                                            
                                                                                            
										</div>    
                                                                                    
                                                                                    </div></dd>
									</dl>-->
									<dl class="fldList">
										<dt>Diagnostic<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control" name="diagnostic_id" required <?php echo ($internalRevPerc>0||$externalRevPerc>0)?'disabled':'' ?>>
												<option value=""> - Select Diagnostic - </option>
												<?php
												foreach($diagnostics as $diagnostic)
													print $assessment['diagnostic_id']==$diagnostic['diagnostic_id']?"<option selected='selected' value=\"".$diagnostic['diagnostic_id']."\">".$diagnostic['name']."</option>\n":(in_array($diagnostic['diagnostic_id'], $hideDiagnostics)?'':"<option value=\"".$diagnostic['diagnostic_id']."\">".$diagnostic['name']."</option>\n");													
												?>
											</select>
										</div></div></dd>
									</dl>
									
									<!--<dl class="fldList">
										<dt>Tier<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control" name="tier_id" required>
												<option value=""> - Select Tier - </option>
												<?php
												//foreach($tiers as $tier)
												//	print $assessment['tier_id']==$tier['standard_id']?"<option selected='selected' value=\"".$tier['standard_id']."\">".$tier['standard_name']."</option>\n":"<option value=\"".$tier['standard_id']."\">".$tier['standard_name']."</option>\n";													
												?>
											</select>
										</div></div></dd>
									</dl>
									
									<dl class="fldList">
										<dt>Award Scheme<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control" name="award_scheme_name" required>
												<option value=""> - Select Award Scheme - </option>
												<?php
												//foreach($awardSchemes as $awardScheme)
												//	print $assessment['award_scheme_id']== $awardScheme['award_scheme_id']?"<option selected='selected' value=\"".$awardScheme['award_scheme_id']."\">".$awardScheme['award_scheme_name']."</option>\n":"<option value=\"".$awardScheme['award_scheme_id']."\">".$awardScheme['award_scheme_name']."</option>\n";
													
												?>
											</select>
										</div></div></dd>
									</dl>-->
                                                                        
                                                                        <dl class="fldList">
										<dt>AQS Round <span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control" name="aqs_round" required>
												<option value=""> - Select AQS Round - </option>
												<?php
												foreach($aqsRounds as $aqsRound)
print $assessment['aqs_round']== $aqsRound['aqs_round']?"<option selected='selected' value=\"".$aqsRound['aqs_round']."\">".$aqsRound['aqs_round']."</option>\n":"<option value=\"".$aqsRound['aqs_round']."\">".$aqsRound['aqs_round']."</option>\n";
															
//echo "<option value=\"".$aqsRound['aqs_round']."\">".$aqsRound['aqs_round']."</option>\n";
												?>
											</select>
										</div></div></dd>
									</dl>
									<?php 									
									//if(intval($assessment['percCompletes'])==0){ ?>
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" title="Click to edit review."  value="Update Review" class="btn btn-primary vtip">
												</div>
											</div>
										</dd>
									</dl>
									<?php //} ?>
									
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
							</form>
						</div>
					</div>
				</div>
				<script>
				$(document).ready(function(){
					
					loadAssesorListForEditAssessment($("#edit_college_assessment_form"),"internal",'<?php echo $assessment['user_ids']?>');
					//loadAssesorListForEditAssessment($("#edit_college_assessment_form"),"external",'<?php echo $assessment['user_ids']?>');
					getExternalAssesorListForAssessment($("#edit_college_assessment_form"));
                                        loadFacilitatorListForEditAssessment($("#edit_college_assessment_form"),'facilitator','<?php echo $assessment['facilitator_id']?>');
                                        var date = new Date();
                                        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                                        $('.aqs_sdate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false, minDate: today});
                                        $('.aqs_edate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false, minDate: today});
				});
				</script>