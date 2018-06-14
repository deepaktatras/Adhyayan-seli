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
    Create College Review
</h1>
<div class="clr"></div>
<div class="">
    <div class="ylwRibbonHldr">
        <!--<div class="tabitemsHldr pad0 clearfix">
            <a href="#" class="fr btn btnTxt" data-toggle="modal" data-target="#notificationSettingsModal"><i class="fa fa-cogs"></i> Notification Settings</a>
        </div>-->
    </div>
    <div class="subTabWorkspace pad26">
        <div class="form-stmnt">
            <form method="post" id="create_college_assessment_form" action="">
            <input type="hidden" name="diagnostic_lang" value="<?php echo DEFAULT_LANGUAGE; ?>" />
                <div class="boxBody">
                    <dl class="fldList">
                        <dt>College<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <select class="form-control internal_client_id" name="client_id" required>
                                        <option value=""> - Select College - </option>
                                        <?php
                                        foreach ($clientsCollege as $client)
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



                    <div class="boxBody">
                        <div class="clr" style="margin-top:20px;"></div>
                        <p><b>External Review team</b><span class="astric">*</span>:</p>
                        <div class="tableHldr teamsInfoHldr school_team team_table noShadow">
                            <?php if (!$disabled) { ?>
                                <a href="javascript:void(0)" class="extteamAddRow"><i class="fa fa-plus"></i></a>
                            <?php } ?>
                            <table class='table customTbl'>
                                <thead>
                                    <tr><th style="width:5%">Sr. No.</th><th style="width:35%">College</th><th style="width:25%">External Reviewer Role</th><th style="width:25%">External Reviewer Team member</th><th style="width:5%;"></th></tr>	
                                </thead>
                                <tbody>
                                    <tr class='team_row'><td class='s_no'>1</td>
                                        <td><select class="form-control external_client_id" id="external_client_id" required <?php echo $disabled ?>>
                                                <option value=""> - Select College - </option>
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
                                    <tr><th style="width:5%">Sr. No.</th><th style="width:35%">College</th><th style="width:25%">Facilitator Role</th><th style="width:25%">Facilitator Team member</th><th style="width:5%;"></th></tr>	
                                </thead>
                                <tbody>
                                    <tr class='facilitator_row'><td class='s_no'>1</td>
                                        <td><select class="form-control facilitator_client_id" name="facilitator_client_id" id="facilitator_client_id"  <?php //echo $disabled   ?>>
                                                <option value=""> - Select College - </option>
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
                        <p><b>College preferred dates for AQS:</b></p>
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
                                    <select class="form-control" name="diagnostic_id" required>
                                        <option value=""> - Select Diagnostic - </option>
                                        <?php
                                        foreach ($diagnostics as $diagnostic)
                                            echo "<option value=\"" . $diagnostic['diagnostic_id'] . "\">" . $diagnostic['name'] . "</option>\n";
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
                                        //foreach ($tiers as $tier)
                                          //  echo "<option value=\"" . $tier['standard_id'] . "\">" . $tier['standard_name'] . "</option>\n";
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
                                        //foreach ($awardSchemes as $awardScheme)
                                        //echo "<option value=\"" . $awardScheme['award_scheme_id'] . "\">" . $awardScheme['award_scheme_name'] . "</option>\n";
                                        ?>
                                    </select>
                                </div></div></dd>
                    </dl>!-->

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
        </div>
    </div>
</div>

<div class="modal fade" id="notificationSettingsModal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Notification Settings</h4></div>
            <div class="modal-body"> 
                <div class="ylwRibbonHldr">
                    <div class="tabitemsHldr">&nbsp;</div>
                </div>
                <div class="subTabWorkspace pad26">
                    <div class="chkFldBlocks">
                        <div class="chkHldr"><input type="checkbox" name="notifySett" id="schoolProfReminder"><label class="chkF checkbox"><span>School profile reminder</span></label></div>
                        <div class="chkHldr"><input type="checkbox" name="notifySett" id="selfReviewScore"><label class="chkF checkbox"><span>Self-review score and evidence reminder</span></label></div>
                        <div class="chkHldr"><input type="checkbox" name="notifySett" id="annualSelfreview"><label class="chkF checkbox"><span>Annual self-review</span></label></div>
                        <div class="chkHldr"><input type="checkbox" name="notifySett" id="actionPlanning"><label class="chkF checkbox"><span>Action planning</span></label></div>
                        <div class="chkHldr"><input type="checkbox" name="notifySett" id="validation"><label class="chkF checkbox"><span>Validation</span></label></div>
                        <div class="chkHldr"><input type="checkbox" name="notifySett" id="postReview"><label class="chkF checkbox"><span>Post review</span></label></div>
                        <div class="chkHldr"><input type="checkbox" name="notifySett" id="assessorLog"><label class="chkF checkbox"><span>Assessor log</span></label></div>
                        <div class="assessorSubFlds">
                            <div class="chkHldr"><input type="checkbox" name="assessorLog"><label class="chkF checkbox"><span>Team member 1</span></label></div>
                            <div class="chkHldr"><input type="checkbox" name="assessorLog"><label class="chkF checkbox"><span>Team member 2</span></label></div>
                            <div class="chkHldr"><input type="checkbox" name="assessorLog"><label class="chkF checkbox"><span>Team member 3</span></label></div>
                            <div class="chkHldr"><input type="checkbox" name="assessorLog"><label class="chkF checkbox"><span>Team member 4</span></label></div>
                        </div>
                    </div>
                </div> 
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    $('.aqs_sdate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false, minDate: today});
    $('.aqs_edate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false, minDate: today});
    $(document).on("click", "#aqsf_school_aqs_pref_start_date,#aqsf_school_aqs_pref_end_date", function () {
        $(this).trigger('change')
    });
    $(function(){
       $('.chkFldBlocks .chkHldr input#assessorLog').on('click', function(){
          $('.assessorSubFlds').slideToggle();
       }); 
    });
</script>