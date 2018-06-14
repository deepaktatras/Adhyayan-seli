<?php
$isReadOnly = empty($isReadOnly) ? 0 : 1;
$readOnlyText = $isReadOnly ? 'readonly="readonly"' : "";
$disabledText = $isReadOnly ? 'disabled="disabled"' : "";
$aqsFilled = 1;
$isSelfReview = isset($assessment['subAssessmentType']) && $assessment['subAssessmentType'] == 1 ? 1 : 0;
$isTeacherReview = isset($assessment['assessment_type_id']) && ($assessment['assessment_type_id'] == 2 || $assessment['assessment_type_id'] == 4) ? 1 : 0;
//$isSchoolReview = isset($assessment['subAssessmentType']) && $assessment['subAssessmentType']==2?1:0;
$isSchoolReview = isset($assessment['assessment_type_id']) && $assessment['assessment_type_id'] == 1 ? 1 : 0;
$isStudentReview = isset($assessment['assessment_type_id']) && ($assessment['assessment_type_id'] == 4) ? 1 : 0;
$isCollegeReview = isset($assessment['assessment_type_id']) && $assessment['assessment_type_id'] == 5 ? 1 : 0;
$principal_phone = empty($aqs['principal_phone_no']) ? $assessment['principal_phone_no'] : $aqs['principal_phone_no'];
$co_phone = empty($aqs['coordinator_phone_number']) ? '' : $aqs['coordinator_phone_number'];
$ac_phone = empty($aqs['accountant_phone_no']) ? '' : $aqs['accountant_phone_no'];
$pr_country_code = array();
$principal_phone_no = '';
if (!empty($principal_phone)) {
    $number = explode(")", $principal_phone);

    if (isset($number[0]) && count($number) > 1) {
        $pr_country_code = explode("+", $number[0]);
        $principal_phone_no = trim($number[1]);
    } else if (count($number) == 1) {
        $principal_phone_no = trim($number[0]);
    } else if (isset($number[1])) {
        $principal_phone_no = trim($number[1]);
    }
}
if (!empty($principal_phone_no)) {
    $principal_phone_no = str_replace("-", '', $principal_phone_no);
}
$cr_country_code = array();
$coordinator_phone_number = '';
if (!empty($co_phone)) {
    $number = explode(")", $co_phone);

    if (isset($number[0]) && count($number) > 1) {
        $cr_country_code = explode("+", $number[0]);
        $coordinator_phone_number = trim($number[1]);
    } else if (count($number) == 1) {
        $coordinator_phone_number = trim($number[0]);
    } else if (isset($number[1])) {
        $coordinator_phone_number = trim($number[1]);
    }
}
if (!empty($coordinator_phone_number)) {
    $coordinator_phone_number = str_replace("-", '', $coordinator_phone_number);
}
$ac_country_code = array();
$accountant_phone_no = '';
if (!empty($ac_phone)) {
    $number = explode(")", $ac_phone);

    if (isset($number[0]) && count($number) > 1) {
        $ac_country_code = explode("+", $number[0]);
        $accountant_phone_no = trim($number[1]);
    } else if (count($number) == 1) {
        $accountant_phone_no = trim($number[0]);
    } else if (isset($number[1])) {
        $accountant_phone_no = trim($number[1]);
    }
}
if (!empty($accountant_phone_no)) {
    $accountant_phone_no = str_replace("-", '', $accountant_phone_no);
}
$i = 0;
//echo "<pre>";print_r($aqs);
?>
<form id="aqsFormWrapper" class="<?php echo $isReadOnly ? 'isReadOnly' : 'isEditable'; ?>" method="post" onsubmit="return false;">
    <div class="row">
        <div class="fl">
            <h1 class="page-title">
                <a href="<?php
                $args = array("controller" => "assessment", "action" => "assessment");
//						if(in_array($user['user_id'],$assessment['user_ids']))
//							$args["myAssessment"]=1;
//$args["filter"]=1;
                echo createUrl($args);
                ?>">
                    <i class="fa fa-chevron-circle-left vtip" title="Back"></i> Manage MyReviews
                </a> &rarr;
                <?php echo $assessment['client_name']; ?>
            </h1>
        </div>
        <?php if ($assessment['aqs_status'] == 0) { ?>
            <div class="fr clearfix">
                <div class="fl"><label style="padding:5px 10px 0 0;">Select Data Version</label></div>
                <div class="fl width200">
                    <select name="aqsversion" id="aqsf_version_id" autocomplete="off" class="selectpicker show-tick form-control" <?php echo $disabledText; ?>>
                        <option value=""> - Select <?php echo $isCollegeReview ? 'CRR' : 'AQS'; ?> version - </option>
                        <?php
                        foreach ($AQSversions as $version)
                            echo '<option value="' . $version['assessment_id'] . '">' . ChangeFormat($version['create_date'], "d-m-Y H:i:s") . '</option>';
                        ?>	
                    </select>
                </div>
                <ul id="aqsf_version_asstype" style="display:none;" >

                    <?php
                    foreach ($AQSversions as $version)
                        echo '<li data-assessment-type="' . $version['assessment_type_id'] . '" data-id="' . $version['assessment_id'] . '"></li>';
                    ?>	
                </ul>
            </div>
        <?php } ?>
    </div>

    <div id="aqsForm" class="feedForm">
        <div class="ylwRibbonHldr">
            <a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab4_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
            <div class="collapse navbar-collapse tabitemsHldr" id="tab4_Toggle">
                <ul class="yellowTab nav nav-tabs"> 
                    <li class="item active  <?php echo!empty($aqs['terms_agree']) && $aqs['terms_agree'] == 1 ? 'completed' : ''; ?>"><a href="#aqs-step1" data-toggle="tab" class="vtip" title="<?php echo $isCollegeReview ? 'CRR Contract' : 'AQS contract' ?>">Step <?php echo ++$i; ?></a></li>         
                    <li class="item"><a href="#aqs-step2" data-toggle="tab" class="vtip" title="Basic information">Step <?php echo ++$i; ?></a></li>
                    <li class="item"><a href="#aqs-step3" data-toggle="tab" class="vtip" title="Advanced information">Step <?php echo ++$i; ?></a></li>
                    <li class="item"><a href="#aqs-step4" data-toggle="tab" class="vtip" title="Teams related information ">Step <?php echo ++$i; ?></a></li>

                    <?php if (!$isCollegeReview) { ?> <li class="item"><a href="#aqs-step5" data-toggle="tab" class="vtip" title="Booking related information">Step <?php echo ++$i; ?></a></li> <?php } ?>
                    <?php if (!empty($assessment['table_name']) && !$isSelfReview) { ?><li class="item"><a href="#aqs-step6" data-toggle="tab" class="vtip" title="Additional information">Step <?php echo ++$i; ?></a></li><?php } ?>
                    <?php if (!empty($assessment['table_name']) && !$isSelfReview) { ?><li class="item"><a href="#aqs-step7" data-toggle="tab" class="vtip" title="Additional information">Step <?php echo ++$i; ?></a></li><?php } ?>
                </ul>
                <div class="tab-pane-mand"><div class="wrapNote"><span>Fields marked with * are mandatory.</span></div></div>
            </div>

        </div>  
        <div class="subTabWorkspace pad26">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="aqs-step1">

                    <h2 class="mb25 text-left"><?php echo $isCollegeReview ? "CRR Terms and Conditions" : "AQS Terms and Conditions"; ?></h2>
                    <div class="boxBody">
                        <div class="transLayer">
                            <div class="">
                                <?php echo $isCollegeReview ? "<ol>
									  	<li>Adhyayan Foundation agrees to undertake the Career Readiness Review (hereinafter  referred to as &#8220;CRR&#8221;) with the INSTITUTION on the understanding that the following documents and information will not be shared with any third parties: The Orientation Manual, the rubric, and the Adhyayan Foundation Action Plan and any other document that informs the process, except the diagnostic tool which the INSTITUTION is free to use and circulate. 
									  		<ol type='a'><li>The INSTITUTION undertakes that it shall not share the aforesaid documents with any third parties even after expiry of the term of this Agreement.</li>
									  			<li>The INSTITUTION undertakes that it shall not copy any of the aforesaid documents other than for the purposes of its own requirements. If confidential information has to be copied due to its own requirements, the copies are exclusively owned by Adhyayan Foundation.</li>
									  		</ol>
									  	</li>
									 	<li>The INSTITUTION agrees that the CRR rating is based entirely on the external evaluation and is valid for 2 years from the date of being awarded to the INSTITUTION, at the end of which period the INSTITUTION can choose to be revalidated.
									 		<ol type='a'>
									 		 	<li>Both parties agree that the report provided to the INSTITUTION by Adhyayan Foundation on the findings of the external evaluation is for the purposes of INSTITUTION improvement and is not binding on the INSTITUTION.</li>
									 		 	<li>Both parties agree that the rating given to the INSTITUTION can be mentioned on the INSTITUTION’s website, the INSTITUTION diary and any other publication of the INSTITUTION, limited to and within the validity period.</li>
									 			</ol>
									 	</li>
									 	<li>The INSTITUTION agrees to participate in Adhyayan Foundation’s research study to evaluate the impact of the organization. The INSTITUTION also agrees that Adhyayan Foundation can use this data to study the strengths and areas of improvement of INSTITUTIONs in India in the areas of inclusion. All data will be anonymous and the INSTITUTION shall not be named.
									 		
									 	</li>
									 	<li>Photographs, videos, audio, and documented evidence from within and around the INSTITUTION may be used for research and documentation purposes.
									 		<ol type='a'>
									 			<li>This data will only be used internally by Adhyayan Foundation. If Adhyayan Foundation wishes to use photographs or other documented evidence for external circulation (like sharing of best practice or publishing on its website or report) prior written permission will be taken from the INSTITUTION.</li>
									 			<li>Adhyayan Foundation ensures that it will not distribute any of the photographs taken at the INSTITUTION taken to any other organization. Under no circumstances will a child’s classified at the publication of any photograph without parental consent. All photographs will be taken in accordance to the guidelines laid down by the National Commission for Protection of Child Rights.</li>
									 		</ol>
									 	</li>
									 	<li>The INSTITUTION authorizes Adhyayan Foundation to send it regular updates and agrees to contact Adhyayan Foundation directly to unsubscribe</li>
									 	<li>All disputes and differences directly or indirectly arising at any time under, out of, in connection with or in relation to this Agreement including, without limitation, all disputes, differences, controversies and questions related to the validity, interpretation, construction, performance and enforcement of any provision of this Agreement shall be finally, exclusively and conclusively settled amicably by the parties.</li>
									 	
                                                                                <li>In the event the parties are unable to resolve the dispute/difference between them, then the courts in Mumbai shall have the exclusive jurisdiction to try disputes and differences arising out of, in connection with or in relation to this Agreement.</li>
									  </ol>" : "<ol>
									  	<li>Adhyayan  agrees  to  undertake  the  Adhyayan  Quality Standard  (hereinafter  referred to as &#8220;AQS&#8221;) process with the  schoolon  the  understanding  that  the  following documents and information will not be shared with any third parties: the School Self-Review and Evaluation (hereinafter referred to as &#8220;SSRE&#8221;) training manual, the  SSRE diagnostic  and  the  Adhyayan Action  Plan  and  any  other  document  that  informs the process. 
									  		<ol type='a'><li>The school undertakes that it shall not share the aforesaid documents with any third parties even after expiry of the term of this Agreement.</li>
									  			<li>The school undertakes that it shall not copy the aforesaid documents other than for the purposes of its own requirements. If confidential information has to be copied due to its work requirements, the copies (including but not limited to files, discs, CDs, etc.) are exclusively owned by Adhyayan.</li>
									  		</ol>
									  	</li>
									 	<li>The school agrees that the AQS is based entirely on the external evaluation and is valid for two(2) years from the date of being awarded to the school, at the end of which period,the school will need to re-validate their AQS.
									 		<ol type='a'>
									 		 	<li>Both parties agree that the Report provided to the school by Adhyayan on the findings of the external evaluation is for the purposes of school improvement and is not binding on the school.</li>
									 		 	<li>Both parties agree that Adhyayan&#8217;s certificate to the school confirming the Award cannot be changed or tampered with in any way.</li>
									 			<li>Both parties agree that the Award can be mentioned on the website, the school diary and any other publication of the school, limited to and within the validity period.</li>
									 		</ol>
									 	</li>
									 	<li>The school agrees that Adhyayan can publish the AQS awarded to the school on Adhyayan&#8217;s official website (http://www.adhyayan.asia) during its validity period.
									 		<ol type='a'>
									 			<li>Adhyayan will agree to refrain from publishing the awarded AQS if it differs greatly from the School expectations. In this event, Adhyayan will allow the school to re-validate AQS after 6 (six) months from the date of this Agreement and that the new AQS awarded will be published on the Adhyayan&#8217;s official website.</li>
									 			<li>Any assistance and improvement services provided by Adhyayan within this 6 (six) month period will be on a separate/individual commission basis. The improvement service provided will be based only on the SSRE and not on the findings of external evaluation.</li>
									 		</ol>
									 	</li>
									 	<li>The Parties agree that two years from the date of the Award, this Agreement will expire. Thereafter, Adhyayan will extend its products and services offered to the school only upon execution of a renewal agreement on such terms and conditions which are agreeable to both parties.
									 		<ol type='a'>
									 			<li>The costs of re-validation will vary according to the AQS tier to which the school applies for at the time and the rates then applicable.</li>
									 			<li>If the school decides not to re-validate or fails the re-validation process, Adhyayan will remove the name of the school from the official AQS website and online lists published on the Adhyayan website.</li>
									 		</ol>
									 	</li>
									 	<li>Adhyayan may conduct spot inspections within the validity period of two (2) years to confirm robustness of the external review results.</li>
									 	<li>The school agrees to participate in Adhyayan&#8217;s research study to evaluate the impact of the organisation&#8217;s services throughout the two year AQS validity. This includes the Adhyayan team documenting progress within the school and collecting data for its own records.</li>
									 	<li>Photographs, videos, audio and documented evidence from within and around the school premises may be used forresearch and documentation purposes. The school allows Adhyayan Quality Education Services Pvt. Ltd.,the copyright to use and publish the same photos in print and/or electronically.</li>
									 		<ol type=\"a\">
									 			<li>The school agrees that these photographs, video and audio may be used in some 
													promotional material released by the organisation from time to time across print and digital mediums. In any such event, I agree that Adhyayan Quality Education Services Pvt. Ltd. may use the photographs and videos of the principal/management, the school premises, its staff and its minor students with or without my name or name of the school/network and for any lawful purposes, 
													including such purposes as publicity, media coverage, documentation, case studies, illustration, advertising, and Web content.</li>
									 			<li>Adhyayan ensures that it will not distribute the photographs taken at the school to any other organisation. Under no circumstances, will a child&#8217;s identity be classified at the publication of any photograph without parental consent. All the photographs will be taken in accordance to the guidelines laid down by the National Commission for Protection of Child Right (NCPCR). <a href=\"http://www.ncpcr.gov.in/childparticipationtv.htm\" target=\"_blank\">http://www.ncpcr.gov.in/childparticipationtv.htm</a></li>
									 		</ol>
									 	<li>The parties agree that Service Tax as applicable for Adhyayan Quality Standard programme that is commissioned will be borne by the school in addition to the quoted cost of the programme.</li>
									 	<li>The school agrees to the advance payment of 50% of programme fees at time of programme confirmation, to pay balance 50% on or before the last day of the AQS Programme.</li>
									 	<li>All disputes and differences directly or indirectly arising at any time under, out of, in connection with or in relation to this Agreement including, without limitation, all disputes, differences, controversies and questions relating to the validity, interpretation, construction, performance and enforcement of any provision of this Agreement shall be finally, exclusively and conclusively settled amicably by the parties.</li>
									 	<li>In the event the parties are unable to resolve the dispute/difference between them, then courts in Mumbai shall have the exclusive jurisdiction to try disputes and differences arising out of, in connection with or in relation to this Agreement.</li>
									  </ol>";
                                ?> 
                            </div>
                        </div>

                        <input type="hidden" name="aqs[terms_agree]" id="aqsf_terms_agree" value="<?php echo $aqs['terms_agree']; ?>" />								
                    </div>								
                </div>
                <div role="tabpanel" class="tab-pane fade in" id="aqs-step2">
                    <h2>Basic information</h2>
                    <div class="boxBody">
                        <dl class="fldList">
                            <dt>Referred by:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select name="aqs[referrer_id]" id="aqsf_referrer_id" autocomplete="off" class="selectpicker show-tick form-control" <?php echo $disabledText; ?>>
                                            <option value=""> - Select Referrer - </option>
                                            <?php
                                            $refId = empty($aqs['referrer_id']) ? "" : $aqs['referrer_id'];
                                            foreach ($referrer_list as $referrer) {
                                                echo '<option ' . ($refId == $referrer['referrer_id'] ? 'selected="selected"' : '') . ' value="' . $referrer['referrer_id'] . '">' . $referrer['referrer_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <dl class="fldList" style="<?php echo $refId > 0 ? ($refId == 7 ? 'display:block;' : 'display:none') : 'display:none;' ?>" id="aqsf_row_referred_text">
                            <dt>Please specify (referred by):</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[referrer_text]" id="aqsf_referrer_text" value="<?php echo!empty($aqs['referrer_text']) ? $aqs['referrer_text'] : ''; ?>" class="form-control"></div>
                                </div>
                            </dd>
                        </dl>
                        <dl class="fldList">
                            <dt><?php echo $isCollegeReview == 1 ? 'Institution status' : 'School status' ?>:</dt>
                            <dd>
                                <div class="clearfix chkBpxPane radioRow">
                                    <div class="chkHldr"><input type="radio" autocomplete="off" name="nstatus" disabled="disabled" <?php echo $assessment['network_id'] > 0 ? '' : 'checked="checked"'; ?>><label class="chkF radio"><span>Standalone </span></label></div>
                                    <div class="chkHldr network"><input type="radio" autocomplete="off" name="nstatus" disabled="disabled" <?php echo $assessment['network_id'] > 0 ? 'checked="checked"' : ''; ?>><label class="chkF radio"><span><?php echo $isCollegeReview == 1 ? 'Belongs to a Network' : 'Belongs to a School Network'; ?> </span></label></div>
                                </div>
                            </dd>
                        </dl>
                        <?php if ($assessment['network_id'] > 0) { ?>
                            <dl class="fldList">
                                <dt>Network name:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6"><input type="text" autocomplete="off" readonly="readonly" disabled="disabled" value="<?php echo $assessment['network_name']; ?>" class="form-control"></div>
                                    </div>
                                </dd>
                            </dl>
                        <?php } ?>

                        <?php if ($assessment['province_name'] && $isCollegeReview != 1) { ?>
                            <dl class="fldList">
                                <dt>Province name:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6"><input type="text" autocomplete="off" readonly="readonly" disabled="disabled" value="<?php echo $assessment['province_name']; ?>" class="form-control"></div>
                                    </div>
                                </dd>
                            </dl>
                        <?php } ?>
                        <dl class="fldList">
                            <dt><?php echo $isCollegeReview == 1 ? 'Name of the institution' : 'Name of the school' ?><span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[school_name]" id="aqsf_school_name" value="<?php echo empty($aqs['school_name']) ? $assessment['client_name'] : $aqs['school_name']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
                                </div>
                            </dd>
                        </dl>
                        <dl class="fldList">
                            <dt>Name of the principal<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[principal_name]" id="aqsf_principal_name" value="<?php echo empty($aqs['principal_name']) ? $principal['name'] : $aqs['principal_name']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
                                </div>
                            </dd>
                        </dl>
                        <dl class="fldList">
                            <dt>Principal Ph. &amp; Email<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="inlContBox brd">                                
                                    <div class="inlCBItm">
                                        <label>Country</label>
                                        <div class="fld">
                                            <div> 
                                                <select name="aqs[pr_country_code]" id="pr_country_code" class="form-control" >
                                                    <?php
                                                    foreach ($countryCodeList as $value) {
                                                        ?>
                                                        <option value="<?php echo $value['phonecode'] ?>"
                                                                <?php echo!empty($pr_country_code[1]) && $pr_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
                                                            <?php echo "(+".$value['phonecode'] .") " ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="inlCBItm">
                                        <label>Ph. Number</label>
                                        <div class="fld">
                                            <div></i><input autocomplete="off" id="aqsf_principal_phone_no" type="text" class="form-control aqs_ph" name="aqs[principal_phone_no]" value="<?php echo $principal_phone_no; ?>" <?php echo $readOnlyText; ?>></div>
                                        </div>
                                    </div>
                                    <div class="inlCBItm">
                                        <label>Email</label>
                                        <div class="fld">
                                            <div class="haveIcon"><i class="fa fa-envelope"></i><input autocomplete="off" id="aqsf_principal_email" type="email" readonly="readonly" disabled="disabled" value="<?php echo empty($aqs['principal_email']) ? $principal['email'] : $aqs['principal_email']; ?>" class="form-control" ></div>
                                        </div>
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <dl class="fldList">
                            <dt>Name of the co-ordinator:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" name="aqs[coordinator_name]" id="aqsf_coordinator_name" autocomplete="off" value="<?php echo empty($aqs['coordinator_name']) ? '' : $aqs['coordinator_name']; ?>" class="form-control" <?php echo $readOnlyText; ?>>
                                        <div class="fltInfo"><i class="fa fa-info-circle vtip" title="Role of the co-ordinator appointed for <?php echo $isCollegeReview == 1 ? 'CRR' : 'AQS' ?> programme :-
                                                                <br/>Appoint one member of your <?php echo $isCollegeReview == 1 ? 'college\'s' : 'school\'s' ?> review team as a co-ordinator for the <?php echo $isCollegeReview == 1 ? 'CRR' : 'AQS' ?> programme whom the <?php echo $isCollegeReview == 1 ? 'Adhyayan' : 'Adhyayan' ?> team can contact for all the requirements before, during and after the <?php echo $isCollegeReview == 1 ? 'CRR' : 'AQS' ?> programme.
                                                                <br/>The role will include:
                                                                <br/>&#8226;	Point of contact for <?php echo $isCollegeReview == 1 ? 'Adhyayan' : 'Adhyayan' ?> for all the information required for the programme
                                                                <br/>&#8226;	Arranging for all the resources required for the programme from Day 1 to Day 5
                                                                <br/>&#8226;	Arranging for the interview slots with students & parents for self-review and external review"></i></div>
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <dl class="fldList">
                            <dt>Co-ordinator Ph. &amp; Email:</dt>
                            <dd>
                                <div class="inlContBox brd">                                
                                    <div class="inlCBItm">
                                        <label>Country</label>
                                        <div class="fld">
                                            <div> 
                                                <select name="aqs[cr_country_code]" id="cr_country_code" class="form-control" >
                                                    <?php
                                                    foreach ($countryCodeList as $value) {
                                                        ?>
                                                        <option value="<?php echo $value['phonecode'] ?>"
                                                                <?php echo!empty($cr_country_code[1]) && $cr_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
                                                            <?php echo "(+".$value['phonecode'] .") " ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="inlCBItm">
                                        <label>Ph. Number</label>
                                        <div class="fld">
                                            <div><input autocomplete="off" id="aqsf_coordinator_phone_number" type="text" class="form-control aqs_ph" name="aqs[coordinator_phone_number]" value="<?php echo $coordinator_phone_number; ?>" <?php echo $readOnlyText; ?>></div>
                                        </div>
                                    </div>
                                    <div class="inlCBItm">
                                        <label>Email</label>
                                        <div class="fld">
                                            <div class="haveIcon"><i class="fa fa-envelope"></i><input type="email" id="aqsf_coordinator_email" autocomplete="off" name="aqs[coordinator_email]" value="<?php echo empty($aqs['coordinator_email']) ? '' : $aqs['coordinator_email']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
                                        </div>
                                    </div>
                                </div>
                            </dd>
                        </dl>

                        <dl class="fldList">
                            <dt><?php echo $isCollegeReview == 1 ? 'Institution Address' : 'School Address' ?><span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6"><input type="text" autocomplete="off" id="aqsf_school_address" name="aqs[school_address]" value="<?php
                                        $address = $assessment['street'];
                                        $address.=($address == "" ? "" : ", ") . $assessment['city'];
                                        $address.=($address == "" ? "" : ", ") . $assessment['state'];

                                        echo empty($aqs['school_address']) ? $address : $aqs['school_address'];
                                        ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
                                </div>
                            </dd>
                        </dl>

                        <dl class="fldList">
                            <dt><?php echo $isCollegeReview == 1 ? 'Institution Website & Email' : 'School Website & Email' ?>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="haveIcon"><i class="fa fa-link"></i><input autocomplete="off" id="aqsf_school_website" type="url" name="aqs[school_website]" value="<?php echo empty($aqs['school_website']) ? '' : $aqs['school_website']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="haveIcon"><i class="fa fa-envelope"></i><input autocomplete="off" id="aqsf_school_email" type="email" name="aqs[school_email]" value="<?php echo empty($aqs['school_email']) ? '' : $aqs['school_email']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
                                    </div>
                                </div>
                            </dd>
                        </dl>

                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade in" id="aqs-step3">
                    <h2>Advanced information</h2>
                    <div class="boxBody">
                        <?php
                        if ($isCollegeReview != 1) {
                            ?>    
                            <dl class="fldList">
                                <dt>Board affiliation<span class="astric">*</span>:</dt>
                                <dd id="aqsf_board_id">
                                    <div class="clearfix advInfo radioRow">
                                        <?php
                                        $board_id = empty($aqs['board_id']) ? '' : $aqs['board_id'];
                                        foreach ($board_list as $board)
                                            echo '<div class="chkHldr"><input autocomplete="off" type="radio" name="aqs[board_id]" id="aqsf_board_id_' . $board['board_id'] . '" ' . ($board_id == $board['board_id'] ? 'checked="checked"' : '') . ' value="' . $board['board_id'] . '" ' . $disabledText . '><label class="chkF radio"><span>' . $board['board'] . '</span></label></div>';
                                        ?>
                                    </div> 
                                </dd>
                            </dl>
                            <?php
                        }
                        ?>
                        <dl class="fldList">
                            <dt><?php echo $isCollegeReview == 1 ? 'Type of institution' : 'Type of school' ?><span class="astric">*</span>:</dt>
                            <dd id="aqsf_school_type_id">
                                <div class="clearfix checkboxRow advInfo">
                                    <?php
                                    foreach ($school_type_list as $school_type) {
                                        if ($isCollegeReview == 1 && ($school_type['school_type_id'] == 3 || $school_type['school_type_id'] == 5 || $school_type['school_type_id'] == 6))
                                            continue;
                                        if ($isCollegeReview != 1 && ($school_type['school_type_id'] == 8 || $school_type['school_type_id'] == 9))
                                            continue;

                                        $checked = "";

                                        if (array_search($school_type['school_type_id'], array_column($aqs_school_type, 'school_type_id')) !== FALSE) {
                                            $checked = 'checked="checked"';
                                        }
                                        echo '<div class="chkHldr" ' . ($school_type['school_type_id'] == 5 ? 'style="width:310px;"' : ($school_type['school_type_id'] == 6 ? 'style="width:310px;"' : '')) . '><input autocomplete="off" type="checkbox" name="aqs[school_type_id][]" value="' . $school_type['school_type_id'] . '" ' . $checked . ' ' . $disabledText . ' id="aqsf_school_type_id_' . $school_type['school_type_id'] . '"><label class="chkF checkbox"><span>' . $school_type['school_type'] . '</span></label></div>';
                                    }
                                    ?>
                                </div> 
                            </dd>
                        </dl>
                        <dl class="fldList"> <!-- .brdW -->
                            <dt><?php echo $isCollegeReview == 1 ? 'Is your institution a minority' : 'Is your school a minority' ?><span class="astric">*</span>:</dt>
                            <dd id="aqsf_school_miniroity">
                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="clearfix advInfo radioRow">
                                            <div class="chkHldr"><input autocomplete="off"  type="radio" id="aqsf_school_minority_yes" name="aqs[aqs_school_minority]" <?php echo isset($aqs['aqs_school_minority']) && $aqs['aqs_school_minority'] == 1 ? 'checked="checked"' : ''; ?>  value="1" ><label class="chkF radio"><span>Yes</span>
                                                </label></div>
                                            <div class="chkHldr"><input autocomplete="off"  type="radio" id="aqsf_school_minority_no" name="aqs[aqs_school_minority]"  value="2" <?php echo isset($aqs['aqs_school_minority']) && $aqs['aqs_school_minority'] == 2 ? 'checked="checked"' : ''; ?> ><label class="chkF radio"><span>No</span>
                                                </label></div>
                                        </div> 
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <dl class="fldList">
                            <dt><?php echo $isCollegeReview == 1 ? 'Is your institution recognised' : 'Is your school recognised' ?><span class="astric">*</span>:</dt>
                            <dd id="aqsf_travel_arrangement_for_adhyayan">
                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="clearfix advInfo radioRow">
                                            <div class="chkHldr"><input autocomplete="off" type="radio" id="aqsf_school_recognised_yes" class="aqsf_school_recognised" name="aqs[aqs_school_recognised]" <?php echo isset($aqs['aqs_school_recognised']) && $aqs['aqs_school_recognised'] == 1 ? 'checked="checked"' : ''; ?>   value="1" ><label class="chkF radio"><span>Yes</span>
                                                </label></div>
                                            <div class="chkHldr"><input autocomplete="off"  type="radio" id="aqsf_school_recognised_no" class="aqsf_school_recognised" name="aqs[aqs_school_recognised]" <?php echo isset($aqs['aqs_school_recognised']) && $aqs['aqs_school_recognised'] == 2 ? 'checked="checked"' : ''; ?>   value="2" ><label class="chkF radio"><span>No</span>
                                                </label></div>
                                        </div> 
                                    </div>
                                </div>
                            </dd>
                            <dl class="fldList reg-info" <?php echo isset($aqs['aqs_school_recognised']) && $aqs['aqs_school_recognised'] == 1 ? '' : 'style="display:none;"'; ?>>
                                <dt>Registration Number<span class="astric">*</span>:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6"><input autocomplete="off" id="aqsf_reg_num"  type="text" class="form-control" value="<?php echo empty($aqs['aqs_school_registration_num']) ? '' : $aqs['aqs_school_registration_num']; ?>" name="aqs[aqs_school_registration_num]"></div>
                                    </div>
                                </dd>
                            </dl>
                        </dl>
                        <dl class="fldList">
                            <dt><?php echo $isCollegeReview == 1 ? 'Institution Location' : 'School Location' ?><span class="astric">*</span>:</dt>
                            <dd id="aqsf_school_region_id">
                                <div class="clearfix advInfo radioRow">
                                    <?php
                                    $school_region_id = empty($aqs['school_region_id']) ? '' : $aqs['school_region_id'];

                                    foreach ($school_region_list as $region) {
                                        if ($isCollegeReview == 1) {
                                            $region['info'] = str_replace('school', 'college', $region['info']);
                                        }
                                        echo '<div class="chkHldr"><input autocomplete="off" type="radio" name="aqs[school_region_id]" value="' . $region['region_id'] . '" ' . ($school_region_id == $region['region_id'] ? 'checked="checked"' : '') . ' ' . $disabledText . ' id="aqsf_school_region_id_' . $region['region_id'] . '"><label class="chkF radio"><span>' . $region['region_name'] . '</span></label><div class="fltInfo"><i class="fa fa-info-circle vtip chkinfo" title="' . $region['info'] . '"></i></div></div>';
                                    }
                                    ?>
                                </div> 
                            </dd>
                        </dl>
                        <?php if (!$isSelfReview) { ?>
                            <dl class="fldList">
                                <dt>IT Support:</dt>
                                <dd id="aqsf_it_support">
                                    <div class="clearfix advInfo">
                                        <?php
                                        $it_support_ids = empty($aqs['it_support_ids']) ? array() : $aqs['it_support_ids'];
                                        foreach ($school_it_support_list as $it_support)
                                            echo '<div class="chkHldr" ><input autocomplete="off" type="checkbox" name="other[it_support][]" ' . (in_array($it_support['it_support_id'], $it_support_ids) ? 'checked="checked"' : '') . ' value="' . $it_support['it_support_id'] . '" ' . $disabledText . ' id="aqsf_it_support_' . $it_support['it_support_id'] . '"><label class="chkF checkbox"><span>' . $it_support['it_support'] . '</span></label></div>';
                                        ?>
                                    </div> 
                                </dd>
                            </dl>
                            <?php
                            if ($isCollegeReview != 1) {
                                ?>
                                <dl class="fldList">
                                    <dt>Number of gates for entry/exit:</dt>
                                    <dd>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <select name="aqs[no_of_gates]" id="aqsf_no_of_gates" autocomplete="off" class="selectpicker show-tick form-control" <?php echo $disabledText; ?>>
                                                    <option value=""> - Select no. of gates - </option>
                                                    <?php
                                                    $g_cnt = empty($aqs['no_of_gates']) ? '' : $aqs['no_of_gates'];
                                                    for ($i = 1; $i < 7; $i++)
                                                        echo '<option ' . ($i == $g_cnt ? 'selected="selected"' : '') . ' value="' . $i . '">' . $i . '</option>';
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <?php
                            }
                            ?>
                            <?php
                            if ($isCollegeReview != 1) {
                                ?>
                                <dl class="fldList">
                                    <dt>Number of buildings:</dt>
                                    <dd>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <select autocomplete="off" id="aqsf_no_of_buildings" class="selectpicker show-tick form-control" name="aqs[no_of_buildings]" <?php echo $readOnlyText; ?>>
                                                    <option value=""> - Select no. of buildings - </option>
                                                    <?php
                                                    $b_cnt = empty($aqs['no_of_buildings']) ? '' : $aqs['no_of_buildings'];
                                                    for ($i = 1; $i < 6; $i++)
                                                        echo '<option ' . ($i == $b_cnt ? 'selected="selected"' : '') . ' value="' . $i . '">' . $i . '</option>';
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <?php
                            }
                            ?>
                            <?php
                            if ($isCollegeReview != 1) {
                                ?>
                                <dl class="fldList" id="distance_main" style=" display: <?php echo ($b_cnt == 1) ? 'none' : 'block'; ?>">
                                    <dt>Distance from the main buildings (in meters):</dt>
                                    <dd>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <select autocomplete="off" id="aqsf_distance_main_building" name="aqs[distance_main_building]" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
                                                    <option value=""> - Select distance - </option>
                                                    <?php
                                                    $distance = empty($aqs['distance_main_building']) ? '' : $aqs['distance_main_building'];
                                                    $distances = array(25, 50, 100, 500, 1000, "1000+");
                                                    foreach ($distances as $d)
                                                        echo '<option ' . ($d == $distance ? 'selected="selected"' : '') . ' value="' . $d . '">' . $d . '</option>';
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <?php
                            }
                            ?>
                        <?php } ?>
                        <?php
                        if ($isCollegeReview != 1) {
                            ?>
                            <dl class="fldList">
                                <dt>Classes<span class="astric">*</span>:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="dropCapHldr">
                                                        <label>From</label>
                                                        <div class="dropFld">
                                                            <select autocomplete="off" id="aqsf_classes_from" name="aqs[classes_from]" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
                                                                <option value=""> - Select Class - </option>
                                                                <?php
                                                                $cFrom = empty($aqs['classes_from']) ? '' : $aqs['classes_from'];
                                                                foreach ($school_class_list as $school_class) {
                                                                    echo '<option ' . ($cFrom == $school_class['class_id'] ? 'selected="selected"' : '') . ' value="' . $school_class['class_id'] . '">' . $school_class['class_name'] . '</option>';
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
                                                            <select autocomplete="off" id="aqsf_classes_to" name="aqs[classes_to]" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
                                                                <option value=""> - Select Class - </option>
                                                                <?php
                                                                $cTo = empty($aqs['classes_to']) ? '' : $aqs['classes_to'];
                                                                foreach ($school_class_list as $school_class) {
                                                                    echo '<option ' . ($cTo == $school_class['class_id'] ? 'selected="selected"' : '') . ' value="' . $school_class['class_id'] . '">' . $school_class['class_name'] . '</option>';
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
                            <?php
                        }
                        ?>
                        <dl class="fldList">
                            <dt><?php echo $isCollegeReview == 1 ? 'Total student strength of the institution' : 'Total student strength of the school' ?><span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select name="aqs[no_of_students]" id="aqsf_no_of_students" autocomplete="off" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
                                            <option value=""> - Select no. of students - </option>
                                            <?php
                                            $strnth = empty($aqs['no_of_students']) ? '' : $aqs['no_of_students'];
                                            $strnths = array("50-250", "251-500", "501-1000", "1001-1500", "1501-2000", "2001-3000", "3001+");
                                            foreach ($strnths as $s)
                                                echo '<option ' . ($s == $strnth ? 'selected="selected"' : '') . ' value="' . $s . '">' . $s . '</option>';
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <?php
                        if ($isCollegeReview != 1) {
                            ?>
                            <dl class="fldList">
                                <dt>Total no. of classrooms<span class="astric">*</span>:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6"><input autocomplete="off" type="text"  id="aqsf_class_rooms" class="form-control number" value="<?php echo empty($aqs['num_class_rooms']) ? '' : $aqs['num_class_rooms']; ?>" name="aqs[num_class_rooms]"  ></div>
                                    </div>
                                </dd>
                            </dl>
                            <?php
                        }
                        ?>
                        <dl class="fldList">
                            <dt>Medium of instruction<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select name="aqs[medium_instruction]" id="aqsf_medium_instruction" autocomplete="off" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
                                            <option value=""> - Select Medium of instruction - </option>
                                            <?php
                                            $medium_instrn_lang = empty($aqs['medium_instruction']) ? '' : $aqs['medium_instruction'];
                                            foreach ($medium_instrn_langs as $k => $v)
                                                echo '<option ' . ($v['language_id'] == $medium_instrn_lang ? 'selected="selected"' : '') . ' value="' . $v['language_id'] . '">' . $v['language_name'] . '</option>';
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <dl class="fldList">
                            <dt>Student type (Gender)<span class="astric">*</span>:</dt>
                            <dd id="aqsf_student_type_id">
                                <div class="clearfix advInfo radioRow">
                                    <?php
                                    $stId = empty($aqs['student_type_id']) ? '' : $aqs['student_type_id'];
                                    foreach ($student_type_list as $stype)
                                        echo '<div class="chkHldr"><input autocomplete="off" type="radio" id="aqsf_student_type_id_' . $stype['student_type_id'] . '" name="aqs[student_type_id]" ' . ($stype['student_type_id'] == $stId ? 'checked="checked"' : '') . ' value="' . $stype['student_type_id'] . '" ' . $disabledText . '><label class="chkF radio"><span>' . $stype['studen_type'] . '</span></label></div>';
                                    ?>
                                </div> 
                            </dd>
                        </dl>
                        <dl class="fldList">


                            <dt><?php echo $isCollegeReview ? 'Annual fee per student' : 'Annual fee per child' ?>(in <i class="fa fa-inr inherit"></i>)<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select name="aqs[annual_fee]" autocomplete="off" id="aqsf_annual_fee" class="selectpicker show-tick form-control" <?php echo $readOnlyText; ?>>
                                            <option value=""> - Select fee - </option>
                                            <?php
                                            $fee = empty($aqs['annual_fee']) ? '' : $aqs['annual_fee'];
                                            $fees = array("below 6000", "6000-12000", "12000-24000", "24000-50000", "50000 and above");
                                            foreach ($fees as $f)
                                                echo '<option ' . ($f == $fee ? 'selected="selected"' : '') . ' value="' . $f . '">' . $f . '</option>';
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <?php if (!$isSelfReview && !$isCollegeReview) { ?>
                            <dl class="fldList">
                                <dt class="noFlt">School timings of all sections:</dt>
                                <dd id="aqsf_other_timing">											
                                    <div class="clearfix">
                                        <?php
                                        $allEmpty = empty($aqs['school_timing']) ? true : false;

                                        foreach ($school_level_list as $school_level) {
                                            $school_timing = array("id" => 0, "start_time" => "", "end_time" => "");
                                            $disText = 'disabled="disabled"';
                                            $enabled = false;
                                            if ($allEmpty) {
                                                $disText = '';
                                                $enabled = true;
                                            } else if (!empty($aqs['school_timing'][$school_level['school_level_id']])) {
                                                $school_timing = $aqs['school_timing'][$school_level['school_level_id']];
                                                $disText = '';
                                                $enabled = true;
                                            }
                                            ?>
                                            <div class="" id="aqsf_other_schoolTeam_<?php ?>">
                                                <div class="schSecHdr row <?php echo $enabled ? 'enabled' : 'disabled'; ?>">
                                                    <div class="col-sm-3">
                                                        <h4 style="float:none;"><?php echo $school_level['school_level']; ?>:</h4>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label>From</label>
                                                        <div class="inpFld">
                                                            <div class="input-group time">
                                                                <input type="text" autocomplete="off" id="aqsf_other_timing_<?php echo $school_level['school_level_id']; ?>_start_time" class="form-control" name="other[timing][<?php echo $school_level['school_level_id']; ?>][start_time]" value="<?php echo $school_timing['start_time']; ?>" <?php echo $readOnlyText . " " . $disText; ?> >
                                                                <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label>To</label>
                                                        <div class="inpFld">
                                                            <div class="input-group time">
                                                                <input type="text" autocomplete="off" id="aqsf_other_timing_<?php echo $school_level['school_level_id']; ?>_end_time" class="form-control" name="other[timing][<?php echo $school_level['school_level_id']; ?>][end_time]" value="<?php echo $school_timing['end_time']; ?>" <?php echo $readOnlyText . " " . $disText; ?>>
                                                                <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="chkHldr" style="width:auto;"><input autocomplete="off" type="checkbox" class="TTNotApplicable" name="other[timing][<?php echo $school_level['school_level_id']; ?>][not_applicable]" value="1" <?php echo $disabledText . ($enabled ? '' : ' checked="checked"'); ?>><label class="chkF checkbox"><span>Not Applicable</span></label></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </dd>
                            </dl>
                        <?php } ?>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane fade in" id="aqs-step4">
                    <h2>Teams related information</h2>
                    <div class="boxBody">
                        <p><b><?php echo $isCollegeReview == 1 ? 'List of the institution team chosen for Career Readiness Review Programme' : 'List of the school team chosen for Adhyayan Quality Standard Programme' ?></b>
                            <i class="fa fa-info-circle vtip" title="The team will be handpicked at the discretion of the Principal, keeping in mind: (a) their active participation for the entire duration of the <?php echo $isCollegeReview == 1 ? 'Career Readiness Review Programme' : 'Adhyayan Quality Standard Process'; ?> and (b) their involvement in the post-review <?php echo $isCollegeReview == 1 ? 'college' : 'school' ?> improvement journey.<br><br>

                               Team size participating in the <?php echo $isCollegeReview == 1 ? 'Career Readiness Review' : 'Adhyayan Quality Standard' ?> programme will depend on your <?php echo $isCollegeReview == 1 ? 'college' : 'school' ?> size.<br><br>

                               <?php echo $isCollegeReview == 1 ? 'College' : 'School' ?> Strength	Team <br>
                               &#8226; > 1500	8 – 15 people<br>
                               &#8226; 1500 < 3000	15 – 20 people<br>
                               &#8226; 3000 & above	20 – 25 people<br><br>

                               In addition to the Principal, representing any or all of the following stakeholder groups:<br>
                               &#8226;	Management (director /  secretary / chairman / trustee)	<br>
                               &#8226;	Teachers (representative of each section in the school)<br>
                               &#8226;	Admin staff <br>
                               &#8226;	Non-teaching staff (peons / clerks / cleaning team)<br>
                               &#8226;	Alumni<br>
                               &#8226;	PTA members/parents<br>
                               &#8226;	Students"></i> <b>:</b>
                        </p>
                        <div data-trigger="aqsDataChanged" class="tableHldr teamsInfoHldr school_team team_table">
                            <?php if (!$isReadOnly) { ?><a href="javascript:void(0)" class="fltdAddRow" data-type="school"><i class="fa fa-plus"></i></a><?php } ?>
                            <table class="table customTbl">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Name<span class="astric">*</span></th>
                                        <th>Designation<span class="astric">*</span></th>
                                        <th>Language preference for training materials<span class="astric">*</span></th>
                                        <th>Email</th>
                                        <th style="width: 250px;">Mobile No.</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $aqsDataModel = new aqsDataModel();
                                    if (count($school_team)) {
                                        $t_cnt = 0;
                                        foreach ($school_team as $team) {
                                            $t_cnt++;
                                            $country_code = array();
                                            $c_code = '';
                                            $phone_no = '';
                                            $number = explode(")", $team['mobile']);
                                            if (isset($number[0]) && count($number) > 1) {
                                                $country_code = explode("+", $number[0]);
                                                $c_code = (isset($country_code[1])) ? $country_code[1] : '';
                                                $phone_no = trim($number[1]);
                                            } else if (count($number) == 1) {
                                                $phone_no = trim($number[0]);
                                            } else if (isset($number[1])) {
                                                $phone_no = trim($number[1]);
                                                if (!empty($phone_no)) {
                                                    $phone_no = str_replace('-', '', $phone_no);
                                                }
                                            }
                                            echo $aqsDataModel->getAqsTeamHtmlRow($t_cnt, 1, $team['name'], $team['designation_id'], $team['lang_id'], $team['email'], $phone_no, $readOnlyText, (!$isReadOnly && $t_cnt > 0 ? 1 : 0), $c_code);
                                        }
                                    } else
                                        echo $aqsDataModel->getAqsTeamHtmlRow(1, 1, '', '', '', '', '', $readOnlyText, 0);
                                    ?>
                                </tbody>
                            </table>
                        </div>								
                    </div>
                </div>

                <?php
                if ($isCollegeReview) {
                    ?>
                    <input autocomplete="off" id="aqsf_school_aqs_pref_start_date"  type="hidden" class="form-control" placeholder="MM/DD/YYYY" name="aqs[school_aqs_pref_start_date]" value="<?php echo empty($aqs['school_aqs_pref_start_date']) ? '' : $aqs['school_aqs_pref_start_date']; ?>" readonly="readonly" >
                    <input autocomplete="off" id="aqsf_school_aqs_pref_end_date" type="hidden" class="form-control" placeholder="MM/DD/YYYY" name="aqs[school_aqs_pref_end_date]" value="<?php echo empty($aqs['school_aqs_pref_end_date']) ? '' : $aqs['school_aqs_pref_end_date']; ?>" readonly="readonly">
                    <?php
                }
                if (!$isCollegeReview) {
                    ?>
                    <div role="tabpanel" class="tab-pane fade" id="aqs-step5">

                        <h2>Information for Adhyayan Operations</h2>
                        <div class="boxBody">
                            <dl class="fldList" style=" display: <?php echo ($isSelfReview == 1) ? 'none' : 'block'; ?>">
                                <dt>School preferred dates for AQS:</dt>
                                <dd>
                                    <div class="inlContBox">                                
                                        <div class="inlCBItm">
                                            <label>From</label>
                                            <div class="fld">
                                                <div class="aqsDate">
                                                            <?php if (!$isSelfReview && $isTeacherReview) { ?>
                                                                <input autocomplete="off" id="aqsf_school_aqs_pref_start_date"  type="text" class="form-control" placeholder="DD-MM-YYYY" name="aqs[school_aqs_pref_start_date]" value="<?php echo empty($aqs['school_aqs_pref_start_date']) ? '' : $aqs['school_aqs_pref_start_date']; ?>">
                                                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>

                                                            <?php } else if (!$isSelfReview && $isSchoolReview) { ?>

                                                                <input autocomplete="off" id="aqsf_school_aqs_pref_start_date"  type="text" class="form-control" placeholder="DD-MM-YYYY" name="aqs[school_aqs_pref_start_date]" value="<?php echo empty($aqs['school_aqs_pref_start_date']) ? '' : $aqs['school_aqs_pref_start_date']; ?>" readonly="readonly" >
                                                               <!--<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>-->
                                                            <?php } ?>
                                                        </div>
                                            </div>
                                        </div>
                                        <div class="inlCBItm">
                                            <label>To</label>
                                            <div class="fld">
                                                <div class="aqsDate">
                                                            <?php if (!$isSelfReview && $isTeacherReview) { ?>
                                                                <input autocomplete="off" id="aqsf_school_aqs_pref_end_date" type="text" class="form-control" placeholder="DD-MM-YYYY" name="aqs[school_aqs_pref_end_date]" value="<?php echo empty($aqs['school_aqs_pref_end_date']) ? '' : $aqs['school_aqs_pref_end_date']; ?>">
                                                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                            <?php } else if (!$isSelfReview && $isSchoolReview) { ?>

                                                                <input autocomplete="off" id="aqsf_school_aqs_pref_end_date" type="text" class="form-control" placeholder="DD-MM-YYYY" name="aqs[school_aqs_pref_end_date]" value="<?php echo empty($aqs['school_aqs_pref_end_date']) ? '' : $aqs['school_aqs_pref_end_date']; ?>" readonly="readonly">
                                                                <!--<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>-->
                                                            <?php } ?>
                                                        </div>
                                            </div>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                            <?php if (!$isSelfReview) { ?>

                                <?php
                                $travel_arrg = empty($aqs["travel_arrangement_for_adhyayan"]) ? 0 : $aqs["travel_arrangement_for_adhyayan"];
                                ?>
                                <dl class="fldList">
                                    <dt>Travel arrangements:</dt>
                                    <dd id="aqsf_travel_arrangement_for_adhyayan">
                                        <div class="row">
                                            <div class="col-sm-7">
                                                <div class="clearfix advInfo radioRow">
                                                    <div class="chkHldr"><input autocomplete="off" class="travel_arrang" type="radio" id="aqsf_travel_arrangement_for_adhyayan_1" name="aqs[travel_arrangement_for_adhyayan]" <?php echo $travel_arrg == 1 ? 'checked="checked"' : ''; ?> value="1" <?php echo $disabledText; ?>><label class="chkF radio"><span>By School</span>
                                                            <i class="fa fa-info-circle vtip" title="Note for timelines :- <br>If the school agrees to do the bookings, Adhyayanwould expect the school's administration team to send the confirmations of the travel bookings one week in advance of the scheduled dates for the programme.  If we do not receive booking confirmation as per the timelines, Adhyayan team will arrange to book the travel and issue a debit note for reimbursement with the applicable service tax charges."></i>
                                                        </label></div>
                                                    <div class="chkHldr"><input autocomplete="off" class="travel_arrang" type="radio" id="aqsf_travel_arrangement_for_adhyayan_2" name="aqs[travel_arrangement_for_adhyayan]" <?php echo $travel_arrg == 2 ? 'checked="checked"' : ''; ?> value="2" <?php echo $disabledText; ?>><label class="chkF radio"><span>By Adhyayan</span>
                                                            <i class="fa fa-info-circle vtip" title="If Adhyayan needs to book travel for their team members, our operations team need this information to ensure that bookings are done on time for the right mode of transport."></i>
                                                        </label></div>
                                                    <div class="fl padT6"><a href="<?php echo SITEURL; ?>public/pdf/Union_Budget_2015-Changes_in_Service_Tax.pdf" target="_blank"><strong>More Info ...</strong></a></div>
                                                </div> 
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <dl class="fldList travel-arr-info" <?php echo $travel_arrg == 2 ? '' : 'style="display:none;"'; ?>>
                                    <dt>Name of the nearest Airport:</dt>
                                    <dd>
                                        <div class="inlContBox brd">                                
                                            <div class="inlCBItm">
                                                <div class="fld">
                                                    <input autocomplete="off" type="text" id="aqsf_airport_name" class="form-control w300" value="<?php echo empty($aqs['airport_name']) ? '' : $aqs['airport_name']; ?>" name="aqs[airport_name]" <?php echo $readOnlyText; ?>>
                                                </div>
                                            </div>
                                            <div class="inlCBItm">
                                                <label>Distance from school (in Kms)</label>
                                                <div class="fld">
                                                    <input autocomplete="off" type="text" id="aqsf_airport_distance" class="form-control w60 number" value="<?php echo empty($aqs['airport_distance']) ? '' : $aqs['airport_distance']; ?>" name="aqs[airport_distance]" >
                                                </div>
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <dl class="fldList travel-arr-info" <?php echo $travel_arrg == 2 ? '' : 'style="display:none;"'; ?>>
                                    <dt>Name of the nearest railway station:</dt>
                                    <dd>
                                        <div class="inlContBox brd">                                
                                            <div class="inlCBItm">
                                                <div class="fld">
                                                    <input autocomplete="off" type="text" id="aqsf_rail_station_name" class="form-control w300" value="<?php echo empty($aqs['rail_station_name']) ? '' : $aqs['rail_station_name']; ?>" name="aqs[rail_station_name]" <?php echo $readOnlyText; ?>>
                                                </div>
                                            </div>
                                            <div class="inlCBItm">
                                                <label>Distance from school (in Kms)</label>
                                                <div class="fld">
                                                    <input autocomplete="off" type="text" id="aqsf_rail_station_distance" class="form-control w60 number" value="<?php echo empty($aqs['rail_station_distance']) ? '' : $aqs['rail_station_distance']; ?>" name="aqs[rail_station_distance]">
                                                </div>
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <?php
                                $accm_arrg = empty($aqs["accomodation_arrangement_for_adhyayan"]) ? 0 : $aqs["accomodation_arrangement_for_adhyayan"];
                                ?>
                                <dl class="fldList">
                                    <dt>Accommodation arrangements:</dt>
                                    <dd id="aqsf_accomodation_arrangement_for_adhyayan">
                                        <div class="row">
                                            <div class="col-sm-7">
                                                <div class="clearfix advInfo radioRow">
                                                    <div class="chkHldr"><input autocomplete="off" id="aqsf_accomodation_arrangement_for_adhyayan_1" class="accom_arrang" type="radio" name="aqs[accomodation_arrangement_for_adhyayan]" <?php echo $accm_arrg != 1 ? '' : 'checked="checked"'; ?> value="1" <?php echo $disabledText; ?>><label class="chkF radio"><span>By School</span>
                                                            <i class="fa fa-info-circle vtip" title="Note for timelines :- <br>If the school agrees to do the bookings, Adhyayanwould expect the school's administration team to send the confirmations of the travel bookings one week in advance of the scheduled dates for the programme.  If we do not receive booking confirmation as per the timelines, Adhyayan team will arrange to book the travel and issue a debit note for reimbursement with the applicable service tax charges."></i>
                                                        </label></div>
                                                    <div class="chkHldr"><input autocomplete="off" id="aqsf_accomodation_arrangement_for_adhyayan_2" class="accom_arrang" type="radio" name="aqs[accomodation_arrangement_for_adhyayan]" <?php echo $accm_arrg != 2 ? '' : 'checked="checked"'; ?> value="2" <?php echo $disabledText; ?>><label class="chkF radio"><span>By Adhyayan</span>
                                                            <i class="fa fa-info-circle vtip" title="If the school wishes for Adhyayan to book travel and accommodation for our resource team, the school will be liable to pay service tax of 14% on the actual reimbursable costs. <br>Please click 'more info' link to refer point (iv)a highlighted in section 5.2 for the changes made in Union Budget 2015."></i>
                                                        </label></div>
                                                    <div class="fl padT6"><a href="<?php echo SITEURL; ?>public/pdf/Union_Budget_2015-Changes_in_Service_Tax.pdf" target="_blank"><strong>More Info ...</strong></a></div>
                                                </div> 
                                            </div>
                                        </div>
                                    </dd>
                                </dl>
                                <dl class="fldList hotel-arr-info" <?php echo $accm_arrg == 2 ? '' : 'style="display:none;"'; ?>>
                                    <dt>Name of the nearest Hotel:</dt>
                                    <dd>
                                        <div class="row">
                                            <div class="col-sm-6"><input autocomplete="off" id="aqsf_hotel_name"  type="text" class="form-control" value="<?php echo empty($aqs['hotel_name']) ? '' : $aqs['hotel_name']; ?>" name="aqs[hotel_name]" <?php echo $readOnlyText; ?>></div>
                                            <i class="fa fa-info-circle vtip" title="If Adhyayan needs to make accommodation arrangements for its team, our operations team need the details of a good reasonable hotel to ensure their stay is comfortable and convenient.  Recommendations for hotel in the range of 2000-3000 per night will be appreciated."></i>
                                        </div>
                                    </dd>
                                </dl>
                                <dl class="fldList hotel-arr-info" <?php echo $accm_arrg == 2 ? '' : 'style="display:none;"'; ?>>
                                    <dt>Distance between the hotel and school (in KM):</dt>
                                    <dd>
                                        <div class="row">
                                            <div class="col-sm-3"><input autocomplete="off" id="aqsf_hotel_school_distance" type="text" class="form-control number" value="<?php echo empty($aqs['hotel_school_distance']) ? '' : $aqs['hotel_school_distance']; ?>" name="aqs[hotel_school_distance]" <?php echo $readOnlyText; ?>></div>
                                        </div>
                                    </dd>
                                </dl>
                                <dl class="fldList hotel-arr-info" <?php echo $accm_arrg == 2 ? '' : 'style="display:none;"'; ?>>
                                    <dt>Distance between hotel and railway station (in KM):</dt>
                                    <dd>
                                        <div class="row">
                                            <div class="col-sm-3"><input autocomplete="off" id="aqsf_hotel_station_distance" type="text" class="form-control number" value="<?php echo empty($aqs['hotel_station_distance']) ? '' : $aqs['hotel_station_distance']; ?>" name="aqs[hotel_station_distance]" <?php echo $readOnlyText; ?>></div>
                                        </div>
                                    </dd>
                                </dl>
                                <dl class="fldList hotel-arr-info" <?php echo $accm_arrg == 2 ? '' : 'style="display:none;"'; ?>>
                                    <dt>Distance between hotel and Airport (in KM):</dt>
                                    <dd>
                                        <div class="row">
                                            <div class="col-sm-3"><input autocomplete="off" id="aqsf_hotel_airport_distance" type="text" class="form-control number" value="<?php echo empty($aqs['hotel_airport_distance']) ? '' : $aqs['hotel_airport_distance']; ?>" name="aqs[hotel_airport_distance]" <?php echo $readOnlyText; ?>></div>
                                        </div>
                                    </dd>
                                </dl>
                            <?php } ?>
                            <dl class="fldList">
                                <dt>School Accountant Name:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[accountant_name]" id="aqsf_accountant_name" value="<?php echo empty($aqs['accountant_name']) ? '' : $aqs['accountant_name']; ?>" class="form-control" <?php echo $readOnlyText; ?>>
                                            <div class="fltInfo"><i class="fa fa-info-circle vtip" title="Role of an accountant in the AQS Programme:<br/>
                                                                    &#8226; Co-ordinate with Adhyayan team and school Principal to arrange for part and full payments for the programmes conducted <br/>
                                                                    &#8226; Acknowledge the receipt of invoice and payments<br/>
                                                                    &#8226; Point of contact for taxation purpose"></i></div>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                            <dl class="fldList">
                                <dt>School Accountant Ph. & Email:</dt>
                                <dd>
                                    <div class="inlContBox brd">                                
                                        <div class="inlCBItm">
                                            <label>Country</label>
                                            <div class="fld">
                                                    <select name="aqs[ac_country_code]" id="ac_country_code" class="form-control" >
                                                        <?php
                                                        foreach ($countryCodeList as $value) {
                                                            ?>
                                                            <option value="<?php echo $value['phonecode'] ?>"
                                                                    <?php echo!empty($ac_country_code[1]) && $ac_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
                                                                <?php echo "(+". $value['phonecode'].")"; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                            </div>
                                        </div>
                                        <div class="inlCBItm">
                                            <label>Ph. Number</label>
                                            <div class="fld">
                                                <input autocomplete="off" id="aqsf_accountant_phone_no" type="text" class="form-control aqs_ph" name="aqs[accountant_phone_no]" value="<?php echo $accountant_phone_no; ?>" <?php echo $readOnlyText; ?>>
                                            </div>
                                        </div>
                                        <div class="inlCBItm">
                                            <label>Email</label>
                                            <div class="fld">
                                                <div class="haveIcon"><i class="fa fa-envelope"></i><input id="aqsf_accountant_email" type="email" autocomplete="off" name="aqs[accountant_email]" value="<?php echo empty($aqs['accountant_email']) ? '' : $aqs['accountant_email']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
                                            </div>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                            <dl class="fldList">
                                <dt><?php echo $isCollegeReview == 1 ? 'Is your institution registered under GST?' : 'Is your school registered under GST?' ?><span class="astric">*</span></dt>
                                <dd id="aqsf_travel_arrangement_for_adhyayan">
                                    <div class="row">
                                        <div class="col-sm-7">
                                            <div class="clearfix advInfo radioRow">
                                                <div class="chkHldr autoW"><input autocomplete="off" type="radio" id="aqsf_school_gst_yes" class="aqsf_school_gst" name="aqs[aqs_school_gst]" <?php echo isset($aqs['aqs_school_gst']) && $aqs['aqs_school_gst'] == 1 ? 'checked="checked"' : ''; ?>   value="1" ><label class="chkF radio"><span>Yes</span>
                                                    </label></div>
                                                <div class="chkHldr autoW"><input autocomplete="off"  type="radio" id="aqsf_school_gst_no" class="aqsf_school_gst" name="aqs[aqs_school_gst]" <?php echo isset($aqs['aqs_school_gst']) && $aqs['aqs_school_gst'] == 2 ? 'checked="checked"' : ''; ?>   value="2" ><label class="chkF radio"><span>No</span>
                                                    </label></div>
                                            </div> 
                                        </div>
                                    </div>
                                </dd>
                                <dl class="fldList gst-info" <?php echo isset($aqs['aqs_school_gst']) && $aqs['aqs_school_gst'] == 1 ? 'block' : 'style="display:none;"'; ?>>
                                    <dt>GST Registration Number<span class="astric">*</span>:</dt>
                                    <dd>
                                        <div class="row">
                                            <div class="col-sm-6"><input autocomplete="off" id="aqsf_gst_num"  type="text" class="form-control" value="<?php echo empty($aqs['aqs_school_gst_num']) ? '' : $aqs['aqs_school_gst_num']; ?>" name="aqs[aqs_school_gst_num]"></div>
                                        </div>
                                    </dd>
                                </dl>
                            </dl>
                            <dl class="fldList">
                                <dt>Billing name:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[billing_name]" id="aqsf_billing_name" value="<?php echo empty($aqs['billing_name']) ? '' : $aqs['billing_name']; ?>" placeholder="The billing name should be the same as registered under GST" class="form-control" <?php echo $readOnlyText; ?>>
                                            <b style=" color: blue;">The billing name should be the same as registered under GST</b></div>
                                        <div class="col-sm-6">                                    
                                            <div class="clearfix chkBpxPane">
                                                <div class="chkHldr" style="width:300px;"><input autocomplete="off" id="aqsf_other_bName_same" type="checkbox" value="1" <?php echo $disabledText; ?> class="bName_same" name="other[bName_same]" ><label class="chkF checkbox"><span>Same as school name</span></label></div>
                                            </div>
                                        </div>
                                    </div>
                                </dd>
                            </dl>                    
                            <dl class="fldList">
                                <dt>Billing address:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6"><input type="text" autocomplete="off" name="aqs[billing_address]" id="aqsf_billing_address" value="<?php echo empty($aqs['billing_address']) ? '' : $aqs['billing_address']; ?>" class="form-control" <?php echo $readOnlyText; ?>></div>
                                        <div class="col-sm-6">
                                            <div class="clearfix chkBpxPane">
                                                <div class="chkHldr" style="width:300px;"><input autocomplete="off" type="checkbox" value="1" <?php echo $disabledText; ?> class="bAddress_same" id="aqsf_other_bAddress_same" name="other[bAddress_same]"><label class="chkF checkbox"><span>Same as school address</span></label></div>
                                            </div>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                        </div>

                    </div>
                    <?php
                }
                ?>

                <!--  for ashoka diagnostic only starts -->
                <?php if (!empty($assessment['table_name']) && !$isSelfReview) { ?>
                    <div role="tabpanel" class="tab-pane fade" id="aqs-step6">								
                        <input type="hidden" name="additional[aqs_additional_id]" value="<?php echo!empty($aqs_additional_id) ? $aqs_additional_id : ''; ?>" />
                        <h2>Additional information</h2>
                        <div class="boxBody">
                            <?php
                            $form_ele = $form_elements[$assessment['table_name']];
                            //print_r($form_elements);
                            foreach ($form_ele as $element):
                                //print_r($element);
                                ?>	
                                <dl class="fldList">
                                    <dt><?php echo $element['label'] ?></dt>
                                    <dd id="aqsf_additional_<?php echo $element['id'] ?>">
                                        <?php
                                        switch ($element['type']) {
                                            case 'checkbox' : echo '<div class="clearfix">';
                                                foreach ($$element['id'] as $key => $val) {
                                                    //echo($val[$element['attr']]);
                                                    $arr = null;
                                                    $arr = !empty($additional_data[$element['id']]) ? explode(',', $additional_data[$element['id']]) : array();
                                                    echo '<div class="chkHldr">
																<input autocomplete="off" ' . ((in_array($val[$element['id']], $arr) === true) ? "checked" : "") . ' type="checkbox" name="additional[' . $element['id'] . '][]" value="' . $val[$element['id']] . '" id="aqsf_additional_' . $element['id'] . '_' . $val[$element['id']] . '">	
																<label class="chkF checkbox"><span>' . $val[$element['attr']] . '</span></label>
																</div>';
                                                }
                                                echo '</div>';
                                                break;
                                            case 'text' : echo '<div class="row"><div class="col-sm-6"><input class="form-control" max-length="40" type="text" autocomplete="off"  class="form-control" name="additional[' . $element['id'] . ']" id="aqsf_additional_' . $element['id'] . '" value ="' . (empty($additional_data[$element['id']]) ? '' : $additional_data[$element['id']]) . '"></div></div>';
                                                break;
                                            case 'number' : echo '<div class="row"><div class="col-sm-6"><input class="form-control" min="0" max="10000" type="number" autocomplete="off"  class="form-control" name="additional[' . $element['id'] . ']" id="aqsf_additional_' . $element['id'] . '" value ="' . (empty($additional_data[$element['id']]) ? 0 : $additional_data[$element['id']]) . '"></div></div>';
                                                break;
                                            case 'nested' : echo '<div id=""><div class="inlContBox brd">';
                                                //print_r($element['elements']);
                                                foreach ($element['elements'] as $key => $val) {
                                                    //print_r($val);
                                                    //echo $additional_data[$val['id']];
                                                    echo '<div class="inlCBItm"><label>' . $val['label'] . ':</label><div class="fld"><input type="number" min="0" max="10000" class="form-control" name="additional[' . $val['id'] . ']" id="aqsf_additional_' . $val['id'] . '" value ="' . (empty($additional_data[$val['id']]) ? 0 : $additional_data[$val['id']]) . '"></div></div>';
                                                }
                                                echo '</div>';
                                                break;
                                            case "row" : echo '<div class="row">';
                                                foreach ($element['elements'] as $key => $val)
                                                    echo '<div class="col-sm-6"><div class="' . $val['class'] . '">' . (empty($val['icon']) ? '' : '<i class="' . $val['icon'] . '"></i>') . '<input type="' . $val['type'] . '" class="form-control" name="additional[' . $val['id'] . ']" id="aqsf_additional_' . $val['id'] . '" value ="' . (empty($additional_data[$val['id']]) ? '' : $additional_data[$val['id']]) . '" /></div></div>';
                                                echo '</div>';
                                        }
                                        ?>
                                    </dd>
                                </dl>
                                <?php
                            endforeach;
                            ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($assessment['table_name']) && !$isSelfReview) { ?>
                    <div role="tabpanel" class="tab-pane fade" id="aqs-step7">																
                        <h2>School References</h2>
                        <div class="boxBody">
                            <p><b>Please provide us with several references we can contact in case Adhyayan has any further questions about your school.</b></p>
                            <div data-trigger="aqsDataChanged" class="tableHldr teamsInfoHldr additionalTeam_table">
                                <?php if (!$isReadOnly) { ?><a href="javascript:void(0)" class="fltdAddRow" ><i class="fa fa-plus"></i></a><?php } ?>
                                <table class="table customTbl">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Full Name</th>
                                            <th style="width: 250px;">Phone No.</th>													
                                            <th>Email</th>
                                            <th>Role/Stakeholder group</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $aqsDataModel = new aqsDataModel();
                                        if (count($aqs_additional_ref)) {
                                            $t_cnt = 0;
                                            foreach ($aqs_additional_ref as $team) {
                                                $country_code = array();
                                            $c_code = '';
                                            $phone_no = '';
                                            $number = explode(")", $team['phone']);
                                            if (isset($number[0]) && count($number) > 1) {
                                                $country_code = explode("+", $number[0]);
                                                $c_code = (isset($country_code[1])) ? $country_code[1] : '';
                                                $phone_no = trim($number[1]);
                                            } else if (count($number) == 1) {
                                                $phone_no = trim($number[0]);
                                            } else if (isset($number[1])) {
                                                $phone_no = trim($number[1]);
                                                if (!empty($phone_no)) {
                                                    $phone_no = str_replace('-', '', $phone_no);
                                                }
                                            }
                                                $t_cnt++;
                                                echo $aqsDataModel->getAqsAdditonalRefHtmlRow($t_cnt, $team['name'], $phone_no, $team['email'], $team['role_stakeholder'], ($t_cnt > 1 ? 1 : 0),$c_code);
                                            }
                                        } else
                                            echo $aqsDataModel->getAqsAdditonalRefHtmlRow(1, '', '', '', '', 0);
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                <?php } ?>
                <!--  for ashoka diagnostic only ends -->
            </div>
        </div>

        <?php if (empty($aqs['terms_agree']) && !$isReadOnly) { ?>
            <div class="fr clearfix" id="termsRow" <?php echo!empty($aqs['terms_agree']) && $aqs['terms_agree'] == 1 ? 'style="display:none;"' : '' ?>>																
                <button autocomplete="off" title="Click to accept." class="vtip btn btn-primary" id="aqs_acceptTerms">Accept</button>									
                <button autocomplete="off" title="Click to decline." class="vtip btn btn-primary" id="aqs_declineTerms">Decline</button>									
            </div>
        <?php } ?>

        <div class="fr clearfix" id="savSbtRrow" <?php echo empty($aqs['terms_agree']) ? 'style="display:none;"' : '' ?>>
            <?php if (!$isReadOnly) { ?>
                <input type="button" autocomplete="off"  id="saveAqsForm" class="fl nuibtn saveBtn" disabled="disabled" value="Save" />
                <?php if ($assessment['aqs_status'] == 0) { ?><input type="button" autocomplete="off" id="submitAqsForm" <?php echo $aqsFilled == 1 ? "" : 'disabled="disabled"'; ?> class="fl nuibtn submitBtn" value="Submit" /><?php } ?>
            <?php } ?>
        </div>

        <div class="clearfix"></div>

    </div>
    <input type="hidden" name="assmntId_or_grpAssmntId" value="<?php echo $assmntId_or_grpAssmntId; ?>" />
    <input type="hidden" name="assessment_type_id" value="<?php echo $assessment_type_id; ?>" />
    <div id="validationErrors"></div>
</form>

<script>
    selfReview = <?php echo $isSelfReview ?>;
    collegeReview = <?php echo $isCollegeReview ?>;
</script>