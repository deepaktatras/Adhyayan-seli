<?php
//echo '<pre>';
//print_r($languages);
//echo '</pre>';
$externalClient = explode(',', $assessment['user_ids']);
//$externalClient = $externalClient[1];
$externalClient = end($externalClient);
$externalClientKpa = isset($assignedKpas[$externalClient])?$assignedKpas[$externalClient]:array();
$allAssessors = array($externalClient);
$subRolesT = $assessment['subroles'];
$subRolesT = !empty($subRolesT) ? explode(',', $subRolesT) : '';
$i = 1;
if (!empty($subRolesT))
    foreach ($subRolesT as $subRoleT) {
        $i++;
        $subRoleT = explode('_', $subRoleT);
        $memberT = $subRoleT[1];
        //$subRT = $subRoleT[1];
        array_push($allAssessors, $memberT);
    }
$hideDiagnostics = explode(',', $hideDiagnostics['hidediagnostics']);
$externalRevPerc = null;
$internalRevPerc = null;
$rev = explode(',', $assessment['percCompletes']);
if (count($rev) > 1) {
    $externalRevPerc = $rev[1];
    $internalRevPerc = $rev[0];
} else
    $externalRevPerc = $internalRevPerc = $assessment['percCompletes'];
$isEditable = ($disabled !== 0) ? $disabled : ( ($externalRevPerc > 0) ? 'disabled' : '');

if (count($reviewNotifications)) {
    $notifications = $reviewNotifications;
}
$active = (isset($step2)&& $step2 == 1)?1:0;
$numKpas = 0;
$assessor_id='';
//print_r($assessment);
?>
<?php if(empty($editStatus)){ ?>
    <a id="addUserBtn" class="btn btn-primary pull-right execUrl vtip fixonmodal" title="Click to add a user." href="?controller=user&action=createUser&ispop=1">Add user</a>
<?php } ?>
<h1 class="page-title">
    <?php if ($isPop == 0) { ?>
        <a href="<?php
        $args = array("controller" => "assessment", "action" => "assessment");
        $args["filter"] = 1;
        echo createUrl($args);
        ?>">
            <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
            Manage MyReviews
        </a> &mdash;
    <?php } ?>	
    Edit Review - <?php echo $assessment['client_name']; ?>
</h1>
<div class="clr"></div>

<div class="">

    <div class="ylwRibbonHldr">
        <?php if(empty($editStatus)){ ?>
                <a href="#" id="notification_settings" class="fr btn btnTxt sett" data-toggle="modal" data-target="#reviewNotificationSettingsModal"><i class="fa fa-cogs"></i> Notification Settings</a>
        <?php } ?>
        <div class="tabitemsHldr pad0 clearfix" >            
            <?php if(isset($review_type) && $review_type == 1) { ?>
            <ul class="yellowTab nav nav-tabs ">          
		<li class="item <?php echo ($active == 0)?'active':'';?>" id="collaborative-step1"><a href="#ctreateSchoolAssessment-step1" data-toggle="tab" class="vtip" title="Create School Review">Step 1</a></li>
                <li class="item edit-assessment <?php echo ($active == 1)?'active':'';?>" id="collaborative-step2" style=" display: <?php echo(isset($review_type)&$review_type == 1)?'':'none';?> "><a href="#ctreateSchoolAssessment-step2" data-toggle="tab" class="vtip" title="Add Collaborative details" id="step2" onclick="getStep2('<?php echo $assessment_id;?>','<?php echo $editStatus;?>')" >Step 2</a></li>				
	    </ul>
            <?php } ?>
            <!--<a href="#" class="fr btn btnTxt" data-toggle="modal" data-target="#notificationSettingsModal"><i class="fa fa-cogs"></i> Notification Settings</a>-->
            
        </div>
    </div>

    <div id="ctreateSchoolAssessment-step1" role="tabpanel" class="tab-pane fade in" style=" display: <?php echo (isset($step2)&& $step2 == 1)?'none':'block';?>">
    <div class="subTabWorkspace pad26">
        <form method="post" id="edit_school_assessment_form" action="">
            <div class="form-stmnt">

                <input type="hidden" name="client_id" value="<?php echo $assessment['client_id']; ?>" />
                <div class="boxBody">
                    
                    <dl class="fldList">
                        <dt>Review Type<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control " name="review_type" id="review_type" required disabled readonly >
                                        <option value=""> - Select School - </option>
                                        <option value="0" <?php echo (isset($assessment['iscollebrative']) && $assessment['iscollebrative'] == 0?'selected="selected"':'')?> >Validated School Review </option>
                                        <option value="1" <?php echo (isset($assessment['iscollebrative']) && $assessment['iscollebrative'] == 1?'selected="selected"':'')?>>Collaborative School Review</option>
                                       
                                    </select>
                                </div></div></dd>
                    </dl>
                    <dl class="fldList">
                        <dt>School<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <input type="hidden" name="assessment_id" id="assessment_id" value="<?php echo $assessment_id; ?>" />
                                    <select class="form-control internal_client_id" name="client_id" required disabled readonly>
                                        <option value=""> - Select School - </option>
                                        <?php
                                        foreach ($clients as $client)
                                            print $assessment['client_id'] == $client['client_id'] ? "<option selected=\"selected\" value=\"" . $client['client_id'] . "\">" . $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '') . "</option>\n" : "<option value=\"" . $client['client_id'] . "\">" . $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '') . "</option>\n";
                                        ?>
                                    </select>
                                </div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt>Internal Reviewer<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control internal_assessor_id" name="internal_assessor_id" required <?php echo ($internalRevPerc > 0 || $externalRevPerc > 0) ? 'disabled' : '' ?> >
                                        <option value=""> - Select Internal Reviewer - </option>
                                    </select>
                                </div></div></dd>
                    </dl>
                    
                      <dl class="fldList">
                        <dt>Review Criteria:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                   <input type="text" class="form-control" value="<?php echo $assessment['review_criteria']; ?>" name="review_criteria"  maxlength="100"/>
                                </div></div></dd>
                    </dl>

                    <!---external team-->
                    <div class="clr" style="margin-top:20px;"></div>
                    <div class="boxBody">									
                        <p><b>External Review team:</b><span class="astric">*</span></p>
                        <div class="tableHldr teamsInfoHldr school_team team_table noShadow">
                            <?php if (!$disabled) { ?>
                                <a href="javascript:void(0)" class="extteamAddRow"><i class="fa fa-plus"></i></a>
                            <?php } ?>
                            <table class='table customTbl'>
                                <thead>
                                    <tr><th style="width:5%">Sr. No.</th><th style="width:35%">School</th><th style="width:25%">External Reviewer Role</th><th style="width:25%">External Reviewer Team member</th><th style="width:5%;"></th></tr>
                                </thead>
                                <tbody>
                                    <tr class='team_row'><td class='s_no'>1</td>
                                        <td><select class="form-control external_client_id" id="external_client_id" required <?php echo $isEditable ?>>
                                                <option value=""> - Select School - </option>
                                                <?php
                                                foreach ($clients as $client)
                                                    echo "<option value=\"" . $client['client_id'] . "\"" . ($assessment['external_client'] == $client['client_id'] ? "selected=selected" : '') . ">" . $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '') . "</option>\n";
                                                ?>
                                            </select></td>
                                        <td>Lead/Senior Associate</td>	
                                        <td><select class="form-control external_assessor_id" name="external_assessor_id" id="lead_assessor" required  <?php echo $isEditable; ?>>
                                                <option value=""> - Select Member - </option>
                                                <?php
                                                foreach ($externalAssessors as $index => $ext) {
                                                    if ($externalClient == $ext['user_id'] || !in_array($ext['user_id'], $allAssessors))
                                                        echo "<option value=\"" . $ext['user_id'] . "\"" . ($externalClient == $ext['user_id'] ? 'selected=selected' : '') . ">" . $ext['name'] . "</option>";
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
                                    $subRoles = !empty($subRoles) ? explode(',', $subRoles) : '';
                                    $sn = 2;
                                    if (!empty($subRoles))
                                        foreach ($subRoles as $subRole) {
                                            $rowTeam = explode('_', $subRole);
                                            $teamExternalClientId = $rowTeam[0];
                                            $teamExternalMemberId = $rowTeam[1];
                                            $teamExternalRoleId = $rowTeam[2];
                                            $row = '<tr class="team_row">
										<td class="s_no">' . $sn . '</td>';
//                                                                                                                if($disabled!==0){
//                                                                                                                    echo '<input type="hidden" id="team_external_client_id'.$sn.'" class="team_external_client_id" value="'.$teamExternalClientId.'" name="externalReviewTeam[clientId][]">';
//                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalRoleId.'" name="externalReviewTeam[role][]">';
//                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalMemberId.'" name="externalReviewTeam[member][]" id="team_external_assessor_id'.$sn.'">';
//                                                                                                                }
                                            $row .= '<td><select class="form-control team_external_client_id" id="team_external_client_id' . $sn . '" required name="externalReviewTeam[clientId][]" ' . $disabled . '>
																	<option value=""> - Select School - </option>';
                                            foreach ($clients as $client)
                                                $row .= "<option value=\"" . $client['client_id'] . "\"" . ($teamExternalClientId == $client['client_id'] ? 'selected=selected' : '') . ">" . $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '') . "</option>\n";

                                            $row .= '</select></td>';

                                            $row .= '<td><select class="form-control " name="externalReviewTeam[role][]" required ' . $disabled . '>
											<option value=""> - Select Role - </option>
											';
                                            foreach ($externalReviewRoles as $externalReviewer)
                                                $row .= $externalReviewer['sub_role_id'] == '1' ? '' : "<option value=\"" . $externalReviewer['sub_role_id'] . "\"" . ($externalReviewer['sub_role_id'] == $teamExternalRoleId ? 'selected=selected' : '') . ">" . $externalReviewer['sub_role_name'] . "</option>";

                                            $row .= '</select></td>
										<td><select class="form-control team_external_assessor_id" name="externalReviewTeam[member][]" id="team_external_assessor_id' . $sn . '" required ' . $disabled . '>
											<option value=""> - Select Member - </option>';
                                            foreach ($externalAssessorsTeam[$sn - 2] as $index => $ext) {

                                                if ($teamExternalMemberId == $ext['user_id'] || !in_array($ext['user_id'], $allAssessors))
                                                    $row .= "<option value=\"" . $ext['user_id'] . "\"" . ($teamExternalMemberId == $ext['user_id'] ? 'selected=selected' : '') . ">" . $ext['name'] . "</option>";
                                            }


                                            $row .= '</select></td>
										<td>';
                                            if ($disabled == '') {
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
                                    <tr><th style="width:5%">Sr. No.</th><th style="width:35%">School</th><th style="width:25%">Facilitator Role</th><th style="width:25%">Facilitator Team member</th><th style="width:5%;"></th></tr>	
                                </thead>
                                <tbody>
                                    <tr class='facilitator_row'><td class='s_no'>1</td>
                                        <td><select class="form-control facilitator_client_id" name="facilitator_client_id" id="facilitator_client_id"  <?php //echo $disabled   ?>>
                                                <option value=""> - Select School - </option>
                                                <?php
                                                foreach ($clients as $client)
//echo "<option value=\"".$client['client_id']."\">".$client['client_name']."</option>\n";
                                                    print isset($assessment['f_client_id']) && $assessment['f_client_id'] == $client['client_id'] ? "<option selected='selected' value=\"" . $client['client_id'] . "\">" . $client['client_name'] . "</option>\n" : "<option value=\"" . $client['client_id'] . "\">" . $client['client_name'] . "</option>\n";
                                                ?>
                                            </select></td>
                                        <td>Lead/Senior Associate</td>
                                        <td><select class="form-control facilitator_id" name="facilitator_id"  <?php //echo $disabled ?>>
                                                <option value=""> - Select Facilitator - </option>

                                            </select></td>
                                        <td></td>	
                                    </tr>	
                                    <?php
//print_r($externalAssessors);
                                    $subRoles = $assessment['subroles'];
//print_r($subRoles);
                                    $subRoles = !empty($subRoles) ? explode(',', $subRoles) : '';
                                    $sn = 2;
                                    if (!empty($facilitators)) {
                                        foreach ($facilitators as $data) {
                                            //$rowTeam = explode('_',$subRole);
                                            $facilitatorClientId = $data['client_id'];
                                            $facilitatorMemberId = $data['user_id'];
                                            //print_r($facilitatorTeam);
                                            $facilitatorRoleId = $data['sub_role_id'];
                                            if ($facilitatorRoleId > 2) {
                                                $row = '<tr class="facilitator_row"><td class="s_no">' . $sn . '</td>';
                                                //                                                                                                                if($disabled!==0){
                                                //                                                                                                                    echo '<input type="hidden" id="team_external_client_id'.$sn.'" class="team_external_client_id" value="'.$teamExternalClientId.'" name="externalReviewTeam[clientId][]">';
                                                //                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalRoleId.'" name="externalReviewTeam[role][]">';
                                                //                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalMemberId.'" name="externalReviewTeam[member][]" id="team_external_assessor_id'.$sn.'">';
                                                //                                                                                                                }
                                                $row .= '<td><select class="form-control team_facilitator_client_id" id="team_facilitator_client_id' . $sn . '" required name="facilitatorReviewTeam[clientId][]" ' . $disabled . '>
                                                                                                                <option value=""> - Select School - </option>';
                                                foreach ($clients as $client)
                                                    $row .= "<option value=\"" . $client['client_id'] . "\"" . ($facilitatorClientId == $client['client_id'] ? 'selected=selected' : '') . ">" . $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '') . "</option>\n";

                                                $row .= '</select></td>';

                                                $row .= '<td><select class="form-control " name="facilitatorReviewTeam[role][]" ><option value=""> - Select Role - </option>';
                                                foreach ($externalReviewRoles as $externalReviewer)
                                                    $row .= $externalReviewer['sub_role_id'] == '1' || $externalReviewer['sub_role_id'] == '2' ? '' : "<option value=\"" . $externalReviewer['sub_role_id'] . "\"" . ($externalReviewer['sub_role_id'] == $facilitatorRoleId ? 'selected=selected' : '') . ">" . $externalReviewer['sub_role_name'] . "</option>";

                                                $row .= '</select></td>
                                                                                                                 <td><select class="form-control team_external_facilitator_id" name="facilitatorReviewTeam[member][]" id="team_external_facilitator_id' . $sn . '" required ' . $disabled . '>
                                                                                                                 <option value=""> - Select Member - </option>';
                                                foreach ($facilitatorTeam[$facilitatorMemberId] as $index => $ext) {

                                                    if ($facilitatorRoleId == $ext['user_id'] || !in_array($ext['user_id'], $allAssessors))
                                                        $row .= "<option value=\"" . $ext['user_id'] . "\"" . ($facilitatorMemberId == $ext['user_id'] ? 'selected=selected' : '') . ">" . $ext['name'] . "</option>";
                                                }
                                                $row .= '</select></td><td>';
                                                if ($disabled == '') {
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
                        <p><b>School preferred dates for AQS:</b></p>
                        <div class="tableHldr noShadow">
                            <table class='table customTbl'>
                                <tbody>
                                    <tr class='team_row'>
                                        <td class="trans" style="width:45%">
                                            <label>Start Date</label><span class="astric">*</span>:</p>
                                            <div class="inpFld">
                                                <div class="input-group aqsDate aqs_sdate">
                                                    <input autocomplete="off" id="aqsf_school_aqs_pref_start_date"  type="text" class="form-control external_assessor_id" placeholder="DD-MM-YYYY" name="school_aqs_pref_start_date" value="<?php echo empty($assessment['school_aqs_pref_start_date']) ? '' : $assessment['school_aqs_pref_start_date']; ?>" readonly="readonly">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>

                                                </div>
                                            </div>
                                        </td>
                                        <td style="width:45%">
                                            <label>End Date</label><span class="astric">*</span>:</p>
                                            <div class="inpFld">
                                                <div class="input-group aqsDate aqs_edate">
                                                    <input autocomplete="off" id="aqsf_school_aqs_pref_end_date" type="text" class="form-control external_assessor_id" placeholder="DD-MM-YYYY" name="school_aqs_pref_end_date" value="<?php echo empty($assessment['school_aqs_pref_end_date']) ? '' : $assessment['school_aqs_pref_end_date']; ?>" readonly="readonly">
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
                             <select class="form-control facilitator_client_id" id="facilitator_client_id"  <?php //echo $disabled  ?>>
                                             <option value=""> - Select School - </option>
                    <?php /* foreach($clients as $client)
                      //echo "<option value=\"".$client['client_id']."\">".$client['client_name']."</option>\n";
                      print $assessment['f_client_id']== $client['client_id']?"<option selected='selected' value=\"".$client['client_id']."\">".$client['client_name']."</option>\n":"<option value=\"".$client['client_id']."\">".$client['client_name']."</option>\n";

                     */ ?>
                                     </select>
                         
                                         
                             </div>
                             <div class="col-sm-6">
                             <select class="form-control facilitator_id" name="facilitator_id"  <?php //echo $disabled ?>>
                                             <option value=""> - Select Facilitator - </option>
                                             
                                     </select>
                         
                                         
                             </div>    
                                 
                                 </div></dd>
                     </dl>-->

                    <dl class="fldList">
                        <dt>Diagnostic<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control" name="diagnostic_id" id="diagnostic_id" required <?php echo ($internalRevPerc > 0 || $externalRevPerc > 0) ? 'disabled' : '' ?>>
                                        <option value=""> - Select Diagnostic - </option>
                                        <?php
                                        foreach ($diagnostics as $diagnostic)
                                            print $assessment['diagnostic_id'] == $diagnostic['diagnostic_id'] ? "<option selected='selected' value=\"" . $diagnostic['diagnostic_id'] . "\">" . $diagnostic['name'] . "</option>\n" : (in_array($diagnostic['diagnostic_id'], $hideDiagnostics) ? '' : "<option value=\"" . $diagnostic['diagnostic_id'] . "\">" . $diagnostic['name'] . "</option>\n");
                                        ?>
                                    </select>
                                </div></div></dd>
                    </dl>
                    <dl class="fldList">
                        <dt>Preferred Language<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control" name="diagnostic_lang" id="diagnostic_lang_id" required>
                                        <option  value=""> - Select Diagnostic Language - </option>
                                        <?php
                                        foreach ($languages as $language)
//echo "<option selected=\" ".selected."\"  value=\"".$language['language_id']."\"  >" . $language['language_words'] . "</option>\n";
                                            print $assessment['language_id'] == $language['language_id'] ? "<option selected='selected' value=\"" . $language['language_id'] . "\">" . $language['language_words'] . "</option>\n" : "<option value=\"" . $language['language_id'] . "\">" . $language['language_words'] . "</option>\n";
                                        ?>
                                    </select>
                                </div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt>Tier<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control" name="tier_id" required>
                                        <option value=""> - Select Tier - </option>
                                        <?php
                                        foreach ($tiers as $tier)
                                            print $assessment['tier_id'] == $tier['standard_id'] ? "<option selected='selected' value=\"" . $tier['standard_id'] . "\">" . $tier['standard_name'] . "</option>\n" : "<option value=\"" . $tier['standard_id'] . "\">" . $tier['standard_name'] . "</option>\n";
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
                                        foreach ($awardSchemes as $awardScheme)
                                            print $assessment['award_scheme_id'] == $awardScheme['award_scheme_id'] ? "<option selected='selected' value=\"" . $awardScheme['award_scheme_id'] . "\">" . $awardScheme['award_scheme_name'] . "</option>\n" : "<option value=\"" . $awardScheme['award_scheme_id'] . "\">" . $awardScheme['award_scheme_name'] . "</option>\n";
                                        ?>
                                    </select>
                                </div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt>AQS Round <span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control" name="aqs_round" required>
                                        <option value=""> - Select AQS Round - </option>
                                        <?php
                                        foreach ($aqsRounds as $aqsRound)
                                            print $assessment['aqs_round'] == $aqsRound['aqs_round'] ? "<option selected='selected' value=\"" . $aqsRound['aqs_round'] . "\">" . $aqsRound['aqs_round'] . "</option>\n" : "<option value=\"" . $aqsRound['aqs_round'] . "\">" . $aqsRound['aqs_round'] . "</option>\n";

//echo "<option value=\"".$aqsRound['aqs_round']."\">".$aqsRound['aqs_round']."</option>\n";
                                        ?>
                                    </select>
                                </div></div></dd>
                    </dl>
                    <?php //if(intval($assessment['percCompletes'])==0){ 
                    if($editStatus != 1){
                    ?>
                    
                                    <div class="text-right"><input type="submit" title="Click to edit review."  value="Update Review" class="btn btn-primary vtip"></div>
                                
                    <?php }  ?>

                </div>
                <div class="ajaxMsg" id="createresource"></div>
                <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />

            </div>
        </form>
    </div>
</div>
    <div id="ctreateSchoolAssessment-step2" role="tabpanel" class="tab-pane fade in" style=" display: <?php echo (isset($step2)&& $step2 == 1)?'block':'none';?>;">
        <div class="subTabWorkspace pad26">
            <form id="edit-review-kpa">
            <div class="form-stmnt" id="kpa-step2">
            
                <div class="boxBody">									
                        <p><b>External Review team</b></p>
                        <div class="tableHldr teamsInfoHldr school_team team_table noShadow">
                            
                            <table class='table customTbl'>
                                <thead>
                                    <tr><th style="width:5%">Sr. No.</th><th style="width:35%">School</th><th style="width:25%">External Reviewer Role</th><th style="width:25%">External Reviewer Team member</th><th style="width:5%;">KPAs</th></tr>
                                </thead>
                                <tbody>
                                    <tr class='team_row'><td class='s_no'>1</td>
                                        <td>
                                                <?php
                                                foreach ($clients as $client){
                                                    if($assessment['external_client'] == $client['client_id']) {
                                                        echo $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '');
                                                       // echo ' <input type="hidden" name="external_client_id" id="external_client_id" value='.$client['client_id'].' >';
                                                    }
                                                }
                                                ?>
                                            </select></td>
                                        <td>Lead/Senior Associate</td>	
                                        <td>
                                                <?php
                                                foreach ($externalAssessors as $index => $ext) {
                                                    if ($externalClient == $ext['user_id'] ){
                                                        echo ' <input type="hidden" name="external_assessor_id" id="lead_assessor" value='.$ext['user_id'].' >';
                                                        echo  $ext['name'];
                                                }}
                                                ?>
                                            </select></td>
                                              <?php       $row1 = '<td><select multiple="multiple" class="form-control team_kpa_id" id="team_kpa_id' . 1 . '"  name="team_kpa_id['.$externalClient.'][]" ' . '>';
																	
                                            foreach ($assessmentKpas as $kpas){
                                                    $numKpas++;
                                                    $row1 .= "<option value=\"" . $kpas['kpa_id'] . "\"" . (!empty($kpas) && in_array($kpas['kpa_id'],$externalClientKpa) ? 'selected=selected' : '') . ">" . $kpas['kpa_name'] . "</option>\n";
                                            }

                                            $row1 .= '</select></td>';
                                            echo $row1; echo "</td>";?>
                                            
                                    </tr>
                                   
                                    <?php
                                     echo ' <input type="hidden" name="assessment_id"  value='.$assessment['assessment_id'].' >';
                                     echo ' <input type="hidden" name="num_kpa"  value='.$numKpas.' >';
                                    $subRoles = $assessment['subroles'];
                                    $subRoles = !empty($subRoles) ? explode(',', $subRoles) : '';
                                    //print_r($subRoles);
                                    $sn = 2;
                                    if (!empty($subRoles))
                                        foreach ($subRoles as $subRole) {
                                            $rowTeam = explode('_', $subRole);
                                            $teamExternalClientId = $rowTeam[0];
                                            $teamExternalMemberId = $rowTeam[1];
                                            $teamExternalRoleId = $rowTeam[2];
                                            $row = '<tr class="team_row">
										<td class="s_no">' . $sn . '</td>';
//                                                                                                                if($disabled!==0){
//                                                                                                                    echo '<input type="hidden" id="team_external_client_id'.$sn.'" class="team_external_client_id" value="'.$teamExternalClientId.'" name="externalReviewTeam[clientId][]">';
//                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalRoleId.'" name="externalReviewTeam[role][]">';
//                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalMemberId.'" name="externalReviewTeam[member][]" id="team_external_assessor_id'.$sn.'">';
//                                                                                                                }
                                            $row .= '<td>';
								
                                            foreach ($clients as $client){
                                                if($teamExternalClientId == $client['client_id'] )
                                                     $row .= $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '');
                                            }

                                            $row .= '</td>';

                                            $row .= '<td>';
                                            foreach ($externalReviewRoles as $externalReviewer){
                                                if($externalReviewer['sub_role_id'] != '1' && $externalReviewer['sub_role_id'] == $teamExternalRoleId)                                            
                                                    $row .= $externalReviewer['sub_role_name'];
                                            }

                                            $row .= '</td>
										<td>';
                                            foreach ($externalAssessorsTeam[$sn - 2] as $index => $ext) {

                                                if ($teamExternalMemberId == $ext['user_id'] ){
                                                    $row .=  $ext['name'] ;
                                                    $assessor_id = $ext['user_id'];
                                                }
                                                //echo ' <input type="hidden" name="external_assessor_id" id="lead_assessor" value='.$ext['user_id'].' >';
                                            }


                                            $row .= '</select></td>';
                                                
                                             $row .= '<td><select multiple="multiple" class="form-control team_kpa_id" id="team_kpa_id' . $sn . '"  name="team_kpa_id['.$teamExternalMemberId.'][]" ' . '>';
																	
                                            foreach ($assessmentKpas as $kpas)
                                                $row .= "<option value=\"" . $kpas['kpa_id'] . "\"". (!empty($assignedKpas[$assessor_id]) && in_array($kpas['kpa_id'],$assignedKpas[$assessor_id]) ? 'selected=selected' : ''). ">" . $kpas['kpa_name'] . "</option>\n";

                                            $row .= '</select></td>';

										//'<td>';
                                            /*if ($disabled == '') {
                                                $row .= '<a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a>';
                                            }*/
                                           // $row .= '</td>
						$row .= '</tr>';

                                            echo $row;
                                            $sn++;
                                        }
                                    ?>										
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php if($assessmentStatus != 1) { ?>
                    <div class="text-right"><input type="submit" title="Click to assign KPAs"  value="Assign KPA" id="edit-kpa" class="btn btn-primary vtip"></div>
                <?php } ?>
                <div class="ajaxMsg" id="createresource"></div>
                <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                <input type="hidden" class="isNewReview" name="isNewReview" value="<?php echo $isNewReview; ?>" />
                <input type="hidden" class="editStatus" name="editStatus" value="<?php echo $editStatus; ?>" />
                <input type="hidden" class="assessmentRating" name="assessmentRating" value="<?php echo $assessmentRating; ?>" />
                <input type="hidden" class="assessmentRatingKpa" name="assessmentRatingKpa" value="<?php echo $assessmentRatingKpa; ?>" />
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewNotificationSettingsModal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">SERE Notification Settings <?php echo " - " . $assessment['client_name']; ?></h4></div>
            <div class="modal-body"> 
                <div class="ylwRibbonHldr">
                    <div class="tabitemsHldr">&nbsp;</div>
                </div>

                <form id="notification-setting-form">
                    <div class="subTabWorkspace pad26">
                        <div class="twoGrids">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4>Notification Settings</h4>
                                <div class="chkFldBlocks settBox">
                                    <?php
                                    // echo "<pre>";print_r(array_column($notificationUsers, 'notification_id'));
                                    $notificationStatus = 0;


                                    // $notificationsArray = array_filter(array_column($notificationUsers, 'notification_id'));
                                    //print_r($reviewNotifications);
                                    $i = 1;
                                    foreach ($assessmentUsers as $usersData) {

                                        $sheetOption = 0;
                                        if ($usersData['sub_role_name'] != 'Observer' && isset($reviewNotifications[$usersData['user_id']]) && in_array(10, $reviewNotifications[$usersData['user_id']]))
                                            $sheetOption = 1;
                                        ?>

                                        <div class="notificationOption">
                                            <span><b><?php echo $i . ". "; ?><?php echo ucfirst($usersData['name']) . "</b> - " . $usersData['sub_role_name']; ?></span>

                                            <?php
                                            foreach ($allNotifications as $data) {
                                                
                                                if($data['type'] == 1){
                                                    $disable = '';
                                                    $checked = '';
                                                    if (isset($reviewNotifications[$usersData['user_id']]) && in_array($data['id'], $reviewNotifications[$usersData['user_id']])) {
                                                        $checked = 'checked="checked"';
                                                    }
                                                    if (!count($reviewNotifications))
                                                        $checked = 'checked="checked"';
                                                    if ($usersData['sub_role_name'] == 'Observer')
                                                        $disable = 'disabled="disabled"';
                                                    if (!empty($disable))
                                                        $checked = '';
                                                    ?>
                                                    <?php if ($sheetOption && $data['id'] == 10) { ?>
                                                        <div class="inlineRow">
                                                        <?php } 
                                                        if($usersData['sub_role_name'] == 'Observer') {
                                                        ?>
                                                            <input type="hidden" name="obsNotif[]" value="<?php echo $usersData['user_id'];?>">
                                                        <?php } ?>
                                                        <div class="checkBlock"><div class="chkHldr"><input  type="checkbox" <?php echo $checked; ?> <?php echo $disable; ?> name="notifySett[<?php echo $usersData['user_id']; ?>][]" class="<?php echo $usersData['user_id']; ?>" id="<?php echo $data['notification_name'] . "-" . $usersData['user_id']; ?>" value="<?php echo $data['id']; ?>"><label class="chkF checkbox"><span><?php echo $data['notification_label']; ?></span></label></div></div>
                                                        <?php if ($sheetOption && $data['id'] == 10) { ?>
                                                            <div class="checkBlock subText">
                                                               <ul><li> <span class="sml"><b>Reimbursement sheet received from assessor?</b></span>
                                                                    <div class="inlineClm">
                                                                        <div class="chkHldr" >
                                                                            <input autocomplete="off" id="reim_sheet_yes_<?php echo $usersData['user_id']; ?>" type="radio" value="1" name="reim_sheet_<?php echo $usersData['user_id']; ?>"  <?php echo isset($reimSheetUsers[$usersData['user_id']]) && $reimSheetUsers[$usersData['user_id']] == 1 ? 'checked="checked"' : ''; ?> ><label class="chkF radio"><span>Yes</span></label>
                                                                        </div>
                                                                        <div class="chkHldr" >
                                                                            <input autocomplete="off" id="reim_sheet_no_<?php echo $usersData['user_id']; ?>" type="radio" value="0" name="reim_sheet_<?php echo $usersData['user_id']; ?>" <?php echo !isset($reimSheetUsers[$usersData['user_id']]) || (isset($reimSheetUsers[$usersData['user_id']]) && $reimSheetUsers[$usersData['user_id']] == 0) ? 'checked="checked"' : ''; ?>  ><label class="chkF radio"><span>No</span></label>
                                                                        </div>
                                                                    </div>
                                                                </li></ul>
                                                            </div>
                                                            <?php if ($sheetOption && $data['id'] == 10) { ?>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>

                                                <?php }
                                            }
                                            ?>
                                        </div>

                                   <?php
                                        $i++;
                                    }
                                    ?> 

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <h4>Reminder Settings</h4>
                                <div class="settBox">
                                    <div class="chkFldBlocks settBox">

                                    <?php
                                    // echo "<pre>";print_r(array_column($notificationUsers, 'notification_id'));
                                    $notificationStatus = 0;


                                    // $notificationsArray = array_filter(array_column($notificationUsers, 'notification_id'));
                                    //print_r($reviewNotifications);
                                    $i = 1;
                                    foreach ($assessmentUsers as $usersData) {

                                        $sheetOption = 0;
                                        if($usersData['sub_role_name'] != 'Observer') {
                                        if ( isset($reviewNotifications[$usersData['user_id']]) && in_array(10, $reviewNotifications[$usersData['user_id']]))
                                            $sheetOption = 1;
                                        ?>

                                        <div class="notificationOption remindrOption">
                                            <span><b><?php echo $i . ". "; ?><?php echo ucfirst($usersData['name']) . "</b> - " . $usersData['sub_role_name']; ?></span>

                                            <?php
                                            foreach ($allNotifications as $data) {
                                                
                                                if($data['type'] == 2 ){
                                                    $disable = '';
                                                    $checked = '';
                                                    if (isset($reviewReminders[$usersData['user_id']]) && in_array($data['id'], $reviewReminders[$usersData['user_id']])) {
                                                        $checked = 'checked="checked"';
                                                    }
                                                    //if (!count($reviewReminders))
                                                        //$checked = 'checked="checked"';
                                                    if ($usersData['sub_role_name'] == 'Observer')
                                                        $disable = 'disabled="disabled"';
                                                    if (!empty($disable))
                                                        $checked = '';
                                                    ?>
                                                    <div class="checkBlock"><div class="chkHldr"><input  type="checkbox" <?php echo $checked; ?> <?php echo $disable; ?> name="remindrSett[<?php echo $usersData['user_id']; ?>][]" class="<?php echo "remindr-".$usersData['user_id']; ?>" id="<?php echo $data['notification_name'] . "_remindr-" . $usersData['user_id']; ?>" alt="<?php echo $usersData['user_id']."-". $data['id']; ?>" value="<?php echo $data['id']; ?>"><label class="chkF checkbox"><span><?php echo $data['notification_label']; ?></span></label></div></div>

                                                    <?php
                                                }
                                                
                                            } ?>

                                        </div>

                                        <?php
                                        $i++;
                                    }
                                    }
                                    ?> 

                                </div>                                
                                </div>
                            </div>
                        </div>  
                        </div>
                        <div class="text-center padT15">
                            <input type="hidden" name="assessment_id" value="<?php echo $assessment_id;?>">
                            <input type="hidden" name="diagnostic_id" value="<?php echo $diagnostic_id;?>">
                            <button class="btn btn-primary">Save</button>
                            
                        </div>
                        <div class="ajaxMsg mt10 text-center" id="createresource"></div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- <div class="modal fade" id="notificationSettingsModal" role="dialog">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Notification Settings</h4></div>
             <div class="modal-body"> 
                 <div class="ylwRibbonHldr">
                     <div class="tabitemsHldr">&nbsp;</div>
                 </div>
                 <div class="subTabWorkspace pad26">
                     <div class="chkFldBlocks">
<?php
//echo "<pre>";print_r(array_column($notificationUsers, 'notification_id'));
$notificationStatus = 0;
$notificationsArray = array_filter(array_column($notificationUsers, 'notification_id'));
//print_r($notificationsArray);
foreach ($notifications as $data) {
    if ($data['notification_name'] == 'assessor_log') {
        $notificationStatus = 1;
        ?>
                                                <input type="hidden" name="notification_type" value="<?php echo $data['id']; ?>">
                                                
    <?php } ?>
                                     <div class="chkHldr"><input type="checkbox" class="assessment-notification" <?php echo in_array($data['id'], $notificationsArray) ? 'checked' : ''; ?> name="notifySett[]" <?php echo ($data['notification_name'] == 'assessor_log' ? 'id="assessorLog"' : '') ?>  value="<?php echo $data['id']; ?>"><label class="chkF checkbox"><span><?php echo $data['notification_label']; ?></span></label></div>

<?php } ?> 
                             <div class="assessorSubFlds" style=" display:  <?php echo (count($notificationsArray)) ? 'block' : 'none'; ?>;">
<?php foreach ($notificationUsers as $data) { ?>
                                             <div class="chkHldr"><input type="checkbox" <?php echo!empty($data['notification_id']) ? 'checked' : ''; ?>  name="assessorLog[]" value="<?php echo $data['user_id']; ?>"><label class="chkF checkbox"><span><?php echo $data['name']; ?></span></label></div>
<?php } ?>
                             
                         </div>
                     </div>
                 </div> 
             </div>
         </div><!-- /.modal-content 
     </div><!-- /.modal-dialog 
 </div><!-- /.modal  -->
<script>
    $(document).ready(function () {

        loadAssesorListForEditAssessment($("#edit_school_assessment_form"), "internal", '<?php echo $assessment['user_ids'] ?>','<?php echo $editStatus ?>');
        //loadAssesorListForEditAssessment($("#edit_school_assessment_form"),"external",'<?php echo $assessment['user_ids'] ?>');
        getExternalAssesorListForAssessment($("#edit_school_assessment_form"));
        loadFacilitatorListForEditAssessment($("#edit_school_assessment_form"), 'facilitator', '<?php echo $assessment['facilitator_id'] ?>');
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        $('.aqs_sdate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false, minDate: today});
        $('.aqs_edate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false, minDate: today});

        $(function () {
            $('#assessorLog').on('change', function () {
                //alert("ok")

                if ($(this).is(":checked")) {
                    $('.assessorSubFlds').slideToggle();

                } else {

                    $('.assessorSubFlds').slideUp();
                }
            });
        });
    });
</script>