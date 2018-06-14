<a id="addUserBtn" class="btn btn-primary pull-right execUrl vtip fixonmodal" title="Click to add a user." href="?controller=user&action=createUser&ispop=1">Add user</a>
<h1 class="page-title">
    <?php //if ($isPop == 0) { ?>
    <a href="<?php
    $args = array("controller" => "assessment", "action" => "assessment");
    echo createUrl($args);
    ?>">
        <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
        Manage MyReviews
    </a> &rarr;
    <?php //} ?>
    Create School Review
</h1>
<div class="clr"></div>
<div class="modal fade" id="notificationSettingsModal" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Notification Settings</h4></div>
            <div class="modal-body"> 
                <div class="ylwRibbonHldr">
                    <div class="tabitemsHldr">&nbsp;</div>
                </div>
                <div class="subTabWorkspace pad26">
                    <div class="chkFldBlocks">
                       
                        <?php  //echo "<pre>";print_r($notifications);
                         foreach ($notifications as $data) { ?>
                        <div class="chkHldr"><input type="checkbox" class="assessment-notification" name="notifySett[]"  value="<?php echo $data['id'];?>"><label class="chkF checkbox"><span><?php echo $data['notification_label'];?></span></label></div>
                            
                        <?php } ?> 
                        <!--<div class="assessorSubFlds">
                            <div class="chkHldr"><input type="checkbox" name="assessorLog"><label class="chkF checkbox"><span>Team member 1</span></label></div>
                            <div class="chkHldr"><input type="checkbox" name="assessorLog"><label class="chkF checkbox"><span>Team member 2</span></label></div>
                            <div class="chkHldr"><input type="checkbox" name="assessorLog"><label class="chkF checkbox"><span>Team member 3</span></label></div>
                            <div class="chkHldr"><input type="checkbox" name="assessorLog"><label class="chkF checkbox"><span>Team member 4</span></label></div>
                        </div>-->
                    </div>
                </div> 
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="">
    <div class="ylwRibbonHldr">
        <div class="collapse navbar-collapse tabitemsHldr" id="tab4_Toggle">
			<ul class="yellowTab nav nav-tabs">          
				<li class="item active" id="collaborative-step1"><a href="#ctreateSchoolAssessment-step1" data-toggle="tab" class="vtip" title="Create School Review">Step 1</a></li>
                                <li class="item disabledTab" id="collaborative-step2" style="display: none;"><a href="#ctreateSchoolAssessment-step2" data-toggle="tab" class="vtip" title="Add Collaborative details" id="step2" >Step 2</a></li>				
			</ul>
		</div>
        <div class="tabitemsHldr pad0 clearfix">
           <!-- <a href="#" class="fr btn btnTxt" data-toggle="modal" data-target="#notificationSettingsModal"><i class="fa fa-cogs"></i> Notification Settings</a>-->
        </div>
    </div>
    <div id="ctreateSchoolAssessment-step1" role="tabpanel" class="tab-pane fade in active">
      <form method="post" id="create_school_assessment_form" action="">
            <div class="subTabWorkspace pad26">
        <div class="form-stmnt">
            
                <div class="boxBody">
                    <dl class="fldList">
                        <dt>Review Type<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control " name="review_type" id="review_type" >
                                      
                                        <option value="0">Validated School Review </option>
                                        <option value="1">Collaborative School Review</option>
                                       
                                    </select>
                                </div></div></dd>
                    </dl>
                    <dl class="fldList">
                        <dt>School<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control internal_client_id" name="client_id" required>
                                        <option value=""> - Select School - </option>
                                        <?php
                                        foreach ($clients as $client)
                                            echo "<option value=\"" . $client['client_id'] . "\">" . $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '') . "</option>\n";
                                        ?>
                                    </select>
                                </div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt>Internal Reviewer<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control internal_assessor_id" name="internal_assessor_id" required>
                                        <option value=""> - Select Internal Reviewer - </option>
                                    </select>
                                </div></div></dd>
                    </dl>
                    
                    <dl class="fldList">
                        <dt>Review Criteria:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <input type="text" class="form-control" value="" name="review_criteria" maxlength="100" />
                                </div></div></dd>
                    </dl>



                    <div class="boxBody">
                        <div class="clr" style="margin-top:20px;"></div>
                        <p><b>External Review team</b><span class="astric">*</span>:</p>
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
                                        <td><select class="form-control external_client_id" id="external_client_id" required <?php echo $disabled ?>>
                                                <option value=""> - Select School - </option>
                                                <?php
                                                foreach ($clients as $client)
                                                    echo "<option value=\"" . $client['client_id'] . "\">" . $client['client_name'] . "</option>\n";
                                                ?>
                                            </select></td>
                                        <td>Lead/Senior Associate</td>
                                        <td><select class="form-control external_assessor_id" name="external_assessor_id" id="lead_assessor" required  <?php echo $disabled ?>>
                                                <option value=""> - Select Member - </option>
                                            </select></td>
                                        <td></td>	
                                    </tr>										
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="boxBody">
                        <div class="clr" style="margin-top:20px;"></div>
                        <p><b>Facilitator:</b></p>
                        <div class="tableHldr teamsInfoHldr facilitator_team facilitator_table noShadow">
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
                                        <td><select class="form-control facilitator_id" name="facilitator_id"  <?php //echo $disabled  ?>>
                                                <option value=""> - Select Facilitator - </option>

                                            </select></td>
                                        <td></td>	
                                    </tr>										
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!--<dl class="fldList">
                            <dt>Facilitator:</dt>
                            <dd><div class="row">
                            <div class="col-sm-6">
                            <select class="form-control facilitator_client_id" id="facilitator_client_id"  <?php //echo $disabled   ?>>
                                            <option value=""> - Select School - </option>
                    <?php /* foreach($clients as $client)
                      echo "<option value=\"".$client['client_id']."\">".$client['client_name']."</option>\n";
                     */ ?>
                                    </select>

                        
                                        
                            </div>
                            <div class="col-sm-6">
                            <select class="form-control facilitator_id" name="facilitator_id"  <?php //echo $disabled   ?>>
                                            <option value=""> - Select Facilitator - </option>
                                            
                                    </select>
                        
                                        
                            </div>    
                                
                                </div></dd>
                    </dl>-->

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
                                                    <input autocomplete="off" id="aqsf_school_aqs_pref_start_date"  type="text" class="form-control" placeholder="DD-MM-YYYY" name="aqs[school_aqs_pref_start_date]" value="<?php //echo empty($aqs['school_aqs_pref_start_date'])?'':$aqs['school_aqs_pref_start_date'];    ?>" readonly="readonly">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width:45%">
                                            <label>End Date</label><span class="astric">*</span>:</p>
                                            <div class="inpFld">
                                                <div class="input-group aqsDate aqs_edate">
                                                    <input autocomplete="off" id="aqsf_school_aqs_pref_end_date" type="text" class="form-control" placeholder="DD-MM-YYYY" name="aqs[school_aqs_pref_end_date]" value="<?php //echo empty($aqs['school_aqs_pref_end_date'])?'':$aqs['school_aqs_pref_end_date'];    ?>" readonly="readonly">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                </div>
                                            </div>
                                        </td>
                                </tbody>            
                            </table>
                        </div>
                    </div>

                  
                    <dl class="fldList">
                        <dt>Diagnostic<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control" name="diagnostic_id" id="diagnostic_id" required>
                                        <option value=""> - Select Diagnostic - </option>
                                        <?php
                                        foreach ($diagnostics as $diagnostic)
                                            echo "<option value=\"" . $diagnostic['diagnostic_id'] . "\">" . $diagnostic['name'] . "</option>\n";
                                        ?>
                                    </select>
                                </div></div></dd>
                    </dl>
                      <dl class="fldList">
                        <dt>Preferred Language<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control" name="diagnostic_lang" id="diagnostic_lang_id" required>
                                        <option value=""> - Select Diagnostic Language - </option>
                                        <?php
                                        //foreach ($languages as $language)
                                           // echo "<option value=\"" . $language['language_id'] . "\">" . $language['language_words'] . "</option>\n";
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
                                            echo "<option value=\"" . $tier['standard_id'] . "\">" . $tier['standard_name'] . "</option>\n";
                                        ?>
                                    </select>
                                </div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt>Award Scheme<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control" name="award_scheme_name" id="award" required>
                                        <option value=""> - Select Award Scheme - </option>
                                        <?php
                                        foreach ($awardSchemes as $awardScheme)
                                            echo "<option value=\"" . $awardScheme['award_scheme_id'] . "\">" . $awardScheme['award_scheme_name'] . "</option>\n";
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
                                            echo "<option value=\"" . $aqsRound['aqs_round'] . "\">" . $aqsRound['aqs_round'] . "</option>\n";
                                        ?>
                                    </select>
                                </div></div></dd>
                    </dl> 
                  
                                     <div class="text-right"><input type="submit" title="Click to create a review."  value="Create Review" id="assessment" class="btn btn-primary vtip">
                                </div>
                           

                </div>
                <div class="ajaxMsg"></div>
                <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
           
        </div>
    </div>
      </form>
    </div>
    <div id="ctreateSchoolAssessment-step2" role="tabpanel" class="tab-pane fade in" style="display: none; ">
        <div class="subTabWorkspace pad26">
            <form id="create-review-kpa">
            <div class="form-stmnt" id="kpa-step2">
            
                <div class="boxBody">
                        <div class="clr" style="margin-top:20px;"></div>
                        <p><b>External Review team</b></p>
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
                                        <td><select class="form-control colla_external_client_id" id="colla_external_client_id" required <?php echo $disabled ?>>
                                                <option value=""> - Select School - </option>
                                                <?php
                                                foreach ($clients as $client)
                                                    echo "<option value=\"" . $client['client_id'] . "\">" . $client['client_name'] . "</option>\n";
                                                ?>
                                            </select></td>
                                        <td>Lead/Senior Associate</td>
                                        <td><select class="form-control colla_external_assessor_id" name="colla_external_assessor_id" id="colla_lead_assessor" required  <?php echo $disabled ?>>
                                                <option value=""> - Select Member - </option>
                                            </select></td>
                                        <td></td>	
                                    </tr>										
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            
                                     <div class="text-right"><input type="submit" title="Click to assign kpa's"  value="Create Review" id="assign-kpa" class="btn btn-primary vtip">
                                </div>
                            
                <div class="ajaxMsg"></div>
                <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
            </form>
        </div>
    </div>
    </div>

<script>
    $(function () {
   $('#team_kpa_id').multiselect({
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            numberDisplayed: 2,
            maxHeight: 120,
            templates: {
                 filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
               ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
});
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    $('.aqs_sdate').datetimepicker({format: 'DD-MM-YYYY',useCurrent: false, pickTime: false, minDate: today});
    $('.aqs_edate').datetimepicker({format: 'DD-MM-YYYY',useCurrent: false, pickTime: false, minDate: today});
    $(document).on("click", "#aqsf_school_aqs_pref_start_date,#aqsf_school_aqs_pref_end_date", function () {
        $(this).trigger('change')
    });
    $(function(){
       $('.chkFldBlocks .chkHldr input#assessorLog').on('click', function(){
          $('.assessorSubFlds').slideToggle();
       }); 
    });
</script>