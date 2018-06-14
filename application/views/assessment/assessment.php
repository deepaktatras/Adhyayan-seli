<?php
$isNetworkAdmin = (in_array("view_own_network_assessment", $user['capabilities']) && $user['network_id'] > 0) ? 1 : 0;
$isSchoolAdmin = in_array("view_own_institute_assessment", $user['capabilities']) ? 1 : 0;
$isAdmin = in_array("view_all_assessments", $user['capabilities']) ? 1 : 0;
$canEditAfterSubmit = in_array("edit_all_submitted_assessments", $user['capabilities']) ? 1 : 0;
$isPrincipal = in_array(6, $user['role_ids']) ? 1 : 0;
$isTAPAdmin = in_array(8, $user['role_ids']) ? 1 : 0;
$isInternalReviewer = in_array(3, $user['role_ids']) ? 1 : 0;
$isExternalReviewer = in_array(4, $user['role_ids']) ? 1 : 0;
$addEditColumn = $isNetworkAdmin || $isSchoolAdmin || $isAdmin ? 1 : 0;
$isAdminNadminPrincipal = $isNetworkAdmin || $isPrincipal || $isAdmin ? 1 : 0;
if($isLead >= 1)    
    $user['isLead'] = $isLead;

$assessmentListRowHelper = new assessmentListRowHelper($user);
$disableSelfReview = 0;
//echo $_COOKIE['ADH_LANG'];
//echo "<pre>";print_r($user);
?>

<div class="filterByAjax assessment-list" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
    <div class="clearfix hdrTitle">
        <div class="pull-left"><h1 class="page-title"><?php echo empty($_REQUEST['myAssessment']) ? "Manage " : ""; ?>MyReviews
             <select class="langSel" name="lang" id="lang">
                 <option value="all">All</option>
                <?php foreach($diagnosticsLanguage as $val) { ?>
                <option value="<?php echo $val['language_code'];?>" <?php echo (isset($_COOKIE['ADH_LANG']) && $_COOKIE['ADH_LANG'] == $val['language_id'])?"selected":'';?>><?php echo $val['language_words'];?></option>
                
                <?php } ?>
            </select>
            </h1></div>
       
        <div class="pull-right">
            <ul class="ratInd flotedInTab">
                <li><span class="sr">Self Review</span></li>
                <li><span class="er">External Review</span></li>
                <li><span class="pr">Post Review</span></li>
                <li><span class="fb">Assessor logs</span></li>
            </ul><?php
            if ((in_array("create_assessment", $user['capabilities']) && count($user['role_ids']) == 1 && current($user['role_ids']) != 8) || in_array("create_self_review", $user['capabilities'])) {
                
            ?>
            <ul class="mainNav">
                <li class="active"><a href="javascript:void(0);">Create Review <i class="fa fa-sort-desc"></i></a>
                    <ul>
                        <?php if (in_array("create_self_review", $user['capabilities'])) { ?>
                            
                           <li><a href="?controller=assessment&action=createSchoolSelfAssessment&amp;ispop=1" data-size="880" class="execUrl" id="addschoolselfRevAssBtn" <?php $disableSelfReview == 1 ? print 'disabled=disabled' : ''; ?>>Create Self Review</a></li>
                           <!--<li><a href="?controller=assessment&action=chooseReviewType&amp;ispop=1" data-size="880" class="execUrl" id="addschoolselfRevAssBtn" <?php $disableSelfReview == 1 ? print 'disabled=disabled' : ''; ?>>Create Self Review</a></li>-->
                        <?php } ?>
                        <?php
                        if (in_array("create_assessment", $user['capabilities'])) {
                            if (count($user['role_ids']) == 1 && current($user['role_ids']) != 8) {
                                ?>                                
                                <li><a href="?controller=assessment&action=createSchoolAssessment&amp;ispop=1" data-size="800" id="addschoolAssBtn">Create School Review</a></li>
                                <!-- <li><a href="?controller=assessment&action=createTeacherAssessment&amp;ispop=1" id="addTeacherAssBtn">Create Teacher Review</a></li> -->
                                <li><a href="?controller=assessment&action=createTeacherAssessment"  data-size="800" id="addTeacherAssBtn">Create Teacher Review</a></li>
                                <li><a href="?controller=assessment&action=createStudentAssessment"  data-size="800" id="addTeacherAssBtn">Create Student   Review</a></li>
                                <li><a href="?controller=assessment&action=createCollegeAssessment&amp;ispop=1" data-size="800" id="addschoolAssBtn">Create College Review</a></li>
                                <li><a href="?controller=assessment&action=uploadSchoolAssessment&amp;ispop=1" data-size="800" id="uploadAssBtn">Upload AQS data</a></li>
                            <?php }
                            ?>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
            <?php }
            
            ?>
        </div>
    </div>

    <div class="asmntTypeContainer">
        <?php
        $ajaxFilter = new ajaxFilter();
        if (!empty($_REQUEST['myAssessment'])) {
            $ajaxFilter->addHidden("myAssessment", 1);
            if ($isAdmin || $isNetworkAdmin || $isTAPAdmin || $isInternalReviewer || $isExternalReviewer) {
                if ($isAdmin || $isNetworkAdmin || $isTAPAdmin) {
                    $ajaxFilter->addTextBoxEtc("client_name", $filterParam["client_name_like"], "School/College", "style='width:9%;'");
                } else
                    $ajaxFilter->addTextBox("client_name", $filterParam["client_name_like"], "School/College");
            }
            if ($isAdmin || $isNetworkAdmin || $isTAPAdmin) {
                $ajaxFilter->addTextBoxEtc("name", $filterParam["name_like"], "Reviewer Name", "style='width:9%;'");
            } else
                $ajaxFilter->addTextBox("name", $filterParam["name_like"], "Reviewer Name");
        }else {
            if ($isAdmin || $isNetworkAdmin || $isTAPAdmin || $isInternalReviewer || $isExternalReviewer) {
                if ($isAdmin || $isNetworkAdmin || $isTAPAdmin) {
                    $ajaxFilter->addTextBoxEtc("client_name", $filterParam["client_name_like"], "School/College", "style='width:9%;'");
                } else
                    $ajaxFilter->addTextBox("client_name", $filterParam["client_name_like"], "School/College");
            }
            if ($isAdmin || $isNetworkAdmin || $isTAPAdmin) {
                $ajaxFilter->addTextBoxEtc("name", $filterParam["name_like"], "Reviewer Name", "style='width:9%;'");
            } else
                $ajaxFilter->addTextBox("name", $filterParam["name_like"], "Reviewer Name");

            if ($isAdmin || $isTAPAdmin) {
                $ajaxFilter->addDropDown("network_id", $networks, 'network_id', 'network_name', $filterParam["network_id"], "Network");
                $ajaxFilter->addDropDown("province_id", $provinces, 'province_id', 'province_name', $filterParam["province_id"], "Province");
            }
        }
        $ajaxFilter->addDropDown("diagnostic_id", $diagnostics, 'diagnostic_id', 'name', $filterParam["diagnostic_id"], "Diagnostic");
        if ($isAdmin || $isNetworkAdmin || $isTAPAdmin) {
            $ajaxFilter->addDateBox("fdate", ChangeFormat($filterParam["fdate_like"],"d-m-Y",""), "AQS Start Date", '', 'style="width:9%;"');
            $ajaxFilter->addDateBox("edate", ChangeFormat($filterParam["edate_like"],"d-m-Y",""), "AQS End Date", '', 'style="width:9%;"');
        } else {
            $ajaxFilter->addDateBox("fdate", ChangeFormat($filterParam["fdate_like"],"d-m-Y",""), "AQS Start Date", '');
            $ajaxFilter->addDateBox("edate", ChangeFormat($filterParam["edate_like"],"d-m-Y",""), "AQS End Date", '');
        }
        //$ajaxFilter->addTextbox("diagnostic_name",$filterParam["diagnostic_name_like"],"Diagnostic Name");
        $ajaxFilter->addDropDown("assessment_type_id", array(array("id" => "sch", "value" => "School"), array("id" => "schs", "value" => "School(Self-Review)"), array("id" => "tchr", "value" => "Teacher"), array("id" => "stu", "value" => "Student"), array("id" => "col", "value" => "College")), 'id', 'value', $filterParam["assessment_type_id"], "Reviews");
        if (isset($_REQUEST['uid']) && $_REQUEST['uid'] != '' && isset($_REQUEST['rid']) && $_REQUEST['rid'] != '') {
            $ajaxFilter->addHidden("uid", $_REQUEST['uid']);
            $ajaxFilter->addHidden("rid", $_REQUEST['rid']);
        }
        if (isset($_REQUEST['ref']) && $_REQUEST['ref'] != '') {
            $ajaxFilter->addHidden("ref", $_REQUEST['ref']);
        }

        $ajaxFilter->generateFilterBar(1);
        ?><script type="text/javascript">
            // function for change the end date according to from date on 28-07-2016 by Mohit Kumar
            $(function () {
                $('.fdate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false}).off('focus')
                        .click(function () {
                            $(this).data("DateTimePicker").show();
                        });
                $('.edate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false}).off('focus')
                        .click(function () {
                            $(this).data("DateTimePicker").show();
                        });
                $(".fdate").on("dp.change", function (e) {
                    $('.edate').data("DateTimePicker").setMinDate(e.date);
                    $('.edate').val('');
                });
                $(".edate").on("dp.change", function (e) {
                    $('.fdate').data("DateTimePicker").setMaxDate(e.date);
                });

                $(".fdate").on("blur", function (e) {
                    //alert($(this).val());
                    $('.edate').data("DateTimePicker").setMinDate($(this).val());
                    //$('.edate').val('');
                });
                $(".edate").on("click", function (e) {
                    $(this).val("");
                });
                $(".edate").on("blur", function (e) {
                    //alert($(this).val())
                    if ($(this).val() != "") {
                        $('.fdate').data("DateTimePicker").setMaxDate($(this).val());
                    }
                });


            });
        </script>
        <div class="tableHldr assessmentaqs">
            <table class="cmnTable">
                <thead>
                    <?php echo $assessmentListRowHelper->printHeaderRow($orderBy, $orderType); ?>
                </thead>
                <tbody>
                    <?php
                    if (count($assessmentList)) {

                        foreach ($assessmentList as $assessment)
                            echo $assessmentListRowHelper->printBodyRow($assessment);
                    } else {
                        echo $assessmentListRowHelper->printNoResultRow();
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <?php echo $this->generateAjaxPaging($pages, $cPage); ?>

        <div class="ajaxMsg"></div>


    </div>
</div>

<script>
    $(document).ready(function () {
        //console.log(isFilter)
        $('[disabled] .vtip').removeClass('vtip');
        $('[disabled] a,[disabled] span').css('cursor', 'not-allowed');
        $('[disabled] a,[disabled] i').attr('title', '');
        $('[disabled] a').attr('href', '');
        $('[disabled] a').on('click', function (e) {
            e.preventDefault();
        });
    });
</script>