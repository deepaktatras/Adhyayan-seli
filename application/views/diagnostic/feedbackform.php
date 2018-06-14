<?php
$isReadOnly = empty($isReadOnly) ? 0 : 1;
$disabled = isset($self_review) && $self_review == 1 ? "style='display: none'" : "style='display:block' ";
$disabledReceivedFeedback = isset($accessorFeedbackDisabled) && $accessorFeedbackDisabled == 1 ? "style='display: none'" : "style='display:block' ";
$activeClass = "active";
$inActiveClass = 1;
$sub_role = 0;
$is_team = isset($self_review) && $self_review == 1 ? '' : 'active in';
$viewSelfSubmit = 1;
$viewPeerSubmit = 1;
if (isset($is_submit) && $is_submit == 1)
    $viewSelfSubmit = 0;

if (isset($is_submit_peer) && $is_submit_peer == 1)
    $viewPeerSubmit = 0;

$answer = '';
$qustn_indx_peer = 1;
$singleCol = isset($assessmentTeam) && count($assessmentTeam) < 5 ? 'singleCol' : '';
$oneTwoCol = isset($assessmentTeam) && count($assessmentTeam) < 3 ? 'oneTwoCol' : '';
$singleColPeerFeedback = isset($feedbackAssessmentMembers) && count($feedbackAssessmentMembers) < 5 ? 'singleCol' : '';
$oneTwoColPeerFeedback = isset($feedbackAssessmentMembers) && count($feedbackAssessmentMembers) < 3 ? 'oneTwoCol' : '';
$goals = isset($goals['goal']) ? $goals['goal'] : '';
//echo "<pre>";print_r($allFeedbackQuestion);
?>
<style>form#post_feedback_form{margin:0;}.padT15{padding-top:15px;}</style>

<h1 class="page-title">
    <a href="<?php
    $args = array("controller" => "assessment", "action" => "assessment");
    $args["filter"] = 1;
    //$args["myAssessment"]=1;		
    echo createUrl($args);
    ?>">
        <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
        Manage MyReviews
    </a>  &rarr; <span>Assessor log &amp; Peer Feedback</span> &rarr; <span><?php echo $schoolName; ?></span> &rarr; <span><?php echo $assessorDetails['name'] . " <em>(" . $assessorDetails['sub_role_name'] . ")</em>"; ?></span>
</h1>
<div class="clr"></div>
<div class="feedForm">
    <div class="ylwRibbonHldr">
        <a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab4_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
        <div class="collapse navbar-collapse tabitemsHldr" id="tab4_Toggle">
            <ul class="yellowTab nav nav-tabs"> 

                <li class="item <?php  echo empty($destination)? $activeClass:''; ?>" id="selfFeedback_tab">
                    <a href="#selfFeedback" data-toggle="tab" class="vtip" title="Post-Review Reflections">Post-Review Reflections</a>
                </li>
                <li class="item <?php  echo (!empty($destination) && $destination='received_feedback') ? $activeClass:''; ?> "  id="peerFeedback_tab">
                    <a href="#peerFeedback" data-toggle="tab" class="vtip" title="Feedback Received">Feedback Received</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="subTabWorkspace pad26">            
        <div class="form-stmnt">			
            <div class="boxBody">
                <div class="tab-content">
                    <div role="tabpane1" class="tab-pane fade <?php echo (!empty($destination) && $destination='received_feedback') ?'active in':'' ?>" id="peerFeedback" >

                        <div class="boxBody">
                            <div class="transLayer">
                                <?php if (isset($accessorFeedbackDisabled) && $accessorFeedbackDisabled == 1) { ?>
                                    <div>
                                        <h3><p>You haven't received any feedback yet</p> </h3>
                                    </div>
                                <?php } else { ?>
                                    <form method="post" id="post_feedback_form" action="">
                                        <input type="hidden" name="assessment_id" id="assessment_id" value="<?php echo $assessment_id; ?>">
                                        <input type="hidden" name="sub_role" value="<?php echo $sub_role; ?> ">
                                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?> ">
                                        <div class="assesorfeed customFeedScl clearfix">
                                            <div class="leftTitleWrap">
                                                <dl class="fldList ftitle brdW contTable">
                                                    <dt>
                                                        <h2 class="qst">Members:</h2>
                                                    </dt>
                                                </dl>
                                                <?php foreach ($allFeedbackQuestion as $data) {
                                                    ?>
                                                    <?php
                                                    if ($data['user_type'] == 'peer') {
                                                        //$qustn_indx_peer = 1;
                                                        ?>

                                                        <?php
                                                        if (!isset($data['child_question'])) {
                                                            $qustn_indx_peer++;
                                                            ?>
                                                            <dl class="fldList brdW contTable <?php echo ($data['field_type'] == 'area') ? 'area' : ''; ?> "><dt>
                                                                    <?php echo $qustn_indx_peer . ". &nbsp;" . $data['question_name']; ?> 
                                                                </dt>
                                                            </dl>
                                                        <?php } ?>

                                                        <?php
                                                    }
                                                    if (isset($data['child_question']) && count($data['child_question']) >= 1) {
                                                        ?>

                                                        <?php
                                                        $qustn_indx_peer = 0;
                                                        foreach ($data['child_question'] as $childQuestion) {
                                                            $qustn_indx_peer++;
                                                            ?>
                                                            <dl class="fldList brdW contTable <?php echo ($data['field_type'] == 'area') ? 'area' : ''; ?> "><dt><?php echo $qustn_indx_peer . ". &nbsp;" . $childQuestion['question_name']; ?></dt></dl>
                                                        <?php } ?>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="rightTableWrap">
                                                <div class="tableWrap">
                                                    <dl class="fldList brdW contTable">
                                                        <dt>
                                                            <h2 class="qst">Assessors <em>(Role)</em>:</h2>
                                                        </dt>
                                                        <dd class="noPad">
                                                            <table class="table customTbl <?php echo $singleColPeerFeedback . ' ' . $oneTwoColPeerFeedback; ?>">
                                                                <thead>
                                                                    <tr>
                                                                        <?php
                                                                        $i = 0;
                                                                        foreach ($feedbackAssessmentMembers as $team) {
                                                                            $i++;
                                                                            ?>
                                                                            <th><?php echo "Member " . $i; //echo $team['name'] . " <em>(" . $team['sub_role_name'] . ")</em>";   ?></th>
                                                                        <?php } ?>
                                                                    </tr>
                                                                </thead>
                                                            </table>
                                                        </dd>
                                                    </dl>
                                                    <?php
                                                    // foreach ($assessmentTeam as $team) {
                                                    foreach ($allFeedbackQuestion as $data) {
                                                        ?>
                                                        <?php
                                                        if ($data['user_type'] == 'peer') {
                                                            //$qustn_indx_peer = 1;
                                                            ?>
                                                            <dl class="fldList brdW contTable">

                                                                <dd class="noPad">
                                                                    <table class="table customTbl <?php echo $singleColPeerFeedback . ' ' . $oneTwoColPeerFeedback; ?>">
                                                                        <tbody>
                                                                            <tr>
                                                                                <?php
                                                                                $peerAnswer = '';
                                                                                foreach ($feedbackAssessmentMembers as $peer) {

                                                                                    //if($team['user_id'] != $peer['user_id']) {
                                                                                    //}
                                                                                    $answer = !empty($peerReceivedFeedbackAnswers[$peer['user_id']][$data['q_id']]) ? $peerReceivedFeedbackAnswers[$peer['user_id']][$data['q_id']] : array();
                                                                                    //$answer = isset($peerFeedbackAnswers[$peer['user_id']][$data['q_id']]) ? $peerFeedbackAnswers[$peer['user_id']][$data['q_id']] : '';
                                                                                    //print_r($answer);die;
                                                                                    if ($data['field_type'] == 'area') {
                                                                                        ?>
                                                                                        <td>
                                                                                            <textarea rows="4" cols="6" name="user[<?php echo $peer['user_id']; ?>][<?php echo $data['q_id']; ?>]" class="form-control" placeholder="Your answer"><?php echo isset($answer['answer']) ? $answer['answer'] : ''; ?></textarea>
                                                                                            <input type="hidden" value="<?php echo $peer['name']; ?>" name="user[<?php echo $peer['user_id']; ?>][name]">
                                                                                        </td>
                                                                                        <?php
                                                                                    }
                                                                                    // }
                                                                                }
                                                                                ?>

                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </dd>
                                                            </dl>

                                                            <?php if (isset($data['child_question']) && count($data['child_question']) >= 1) { ?>
                                                                <dl class="fldList brdW contTable">
                                                                    <!-- <dt>
                                                                    <?php //echo $data['question_name'];    ?> <span class="astric">*</span>: 
                                                                     </dt> -->

                                                                    <?php
                                                                    foreach ($data['child_question'] as $childQuestion) {
                                                                        ?>

                                                                        <dd class="noPad">
                                                                            <table class="table customTbl <?php echo $singleColPeerFeedback . ' ' . $oneTwoColPeerFeedback; ?>">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <?php
                                                                                        foreach ($feedbackAssessmentMembers as $peer) {
                                                                                            //if($team['user_id'] != $peer['user_id']) {
                                                                                            // $answer = isset($peerFeedbackAnswers[$peer['user_id']][$childQuestion['q_id']]) ? $peerFeedbackAnswers[$peer['user_id']][$childQuestion['q_id']] : '';
                                                                                            $answer = !empty($peerReceivedFeedbackAnswers[$peer['user_id']][$childQuestion['q_id']]) ? $peerReceivedFeedbackAnswers[$peer['user_id']][$childQuestion['q_id']] : array();
                                                                                            // echo"ffff". $peer['user_id'];
                                                                                            //print_r($answer['answer']);
                                                                                            ?>
                                                                                            <td>
                                                                                                <select class="form-control" name="user[<?php echo $peer['user_id']; ?>][<?php echo $childQuestion['q_id']; ?>]">
                                                                                                    <option value="">-Select-</option>
                                                                                                    <option value="Always" <?php echo isset($answer['answer']) && $answer['answer'] == 'Always' ? 'selected="selected"' : ''; ?> >Always</option>
                                                                                                    <option value="Mostly" <?php echo isset($answer['answer']) && $answer['answer'] == 'Mostly' ? 'selected="selected"' : ''; ?>>Mostly</option>
                                                                                                    <option value="Sometimes" <?php echo isset($answer['answer']) && $answer['answer'] == 'Sometimes' ? 'selected="selected"' : ''; ?> >Sometimes</option>
                                                                                                    <option value="Rarely" <?php echo isset($answer['answer']) && $answer['answer'] == 'Rarely' ? 'selected="selected"' : ''; ?> >Rarely</option>
                                                                                                </select>
                                                                                            </td>
                                                                                            <?php
                                                                                            //}
                                                                                        }
                                                                                        ?>

                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </dd>
                                                                    <?php } ?>
                                                                </dl>

                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    // }
                                                    ?>

                                                    <!-- <div class="clearfix">
                                                        <a class="fr nuibtn viewBtn" href="#">View</a>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                      <form id="post_feedback_goal_form">
                                        <input type="hidden" name="assessment_id" id="assessment_id" value="<?php echo $assessment_id; ?>">
                                        <input type="hidden" name="sub_role" value="<?php echo $sub_role; ?> ">
                                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?> ">
                                        <div class="goals clearfix" >
                                            
                                        <dl class="fldList ftitle brdW contTable">
                                                    <dt>
                                                        <h2 class="qst">Goals for the next review</h2>
                                                    </dt>
                                                    <dd>
                                                        <textarea rows="4" cols="6" name="feedback_goal" class="form-control"><?php echo $goals; ?></textarea>
                                                    </dd>
                                                </dl>
                                          <div class="padT15">
                                                    <dl class="fldList brdW contTable" style="display:<?php echo !empty($goals) ? 'none' : ''; ?>">
                                                        <dd>
                                                            <input type="button" autocomplete="off" id="submitFeedbackGoal"  class="fl nuibtn submitBtn" value="Submit" />
                                                        </dd>
                                                    </dl>
                                                    <div class="ajaxMsg mt10" id="createresource"></div>
                                                </div>
                                        </div>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div role="tabpane1" class="tab-pane fade <?php echo (!empty($destination) && $destination='received_feedback') ?'':'active in' ?>" id="selfFeedback" >
                        <div class="wrapNote"><span>All fields are mandatory.</span></div>
                        <div class="boxBody">
                            <div class="transLayer">
                                <div class="tc_wrapper">
                                    <ul class="nav nav-tabs terms">          
                                        <li class="item active"><a href="#myFeedback_assLog" data-toggle="tab" class="vtip" title="Assessor Log">Assessor Log</a></li>
                                        <li class="item" <?php echo $disabled; ?> ><a href="#myFeedback_peerFeed" data-toggle="tab" class="vtip" title="Feedback For Peers">Feedback For Peers</a></li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade active in" id="myFeedback_assLog">
                                        <form method="post" id="post_self_feedback_form">
                                            <?php
                                            $qustn_indx = 1;
                                            foreach ($selfFeedbackQuestion as $data) {
                                                $sub_role = $data['user_sub_role'];
                                                ?>
                                                <div class="mb10">
                                                    <label><?php echo $qustn_indx . "." . $data['question_name']; ?> </label>
                                                    <?php if ($data['field_type'] == 'area') { ?>
                                                        <textarea rows="5" cols="8" class="form-control custom selffeed"  name="q_id[<?php echo $data['q_id']; ?>]"><?php echo isset($data['answer']) ? $data['answer'] : ''; ?></textarea>
                                                    <?php } ?>    
                                                </div>
                                                <?php
                                                $qustn_indx++;
                                            }
                                            ?>
                                            <input type="hidden" name="assessment_id" value="<?php echo $assessment_id; ?> ">
                                            <input type="hidden" name="sub_role" value="<?php echo $sub_role; ?> ">
                                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?> ">
                                            <input type="hidden"  name="userEmail" value="<?php echo $userEmail; ?>" />
                                            <input type="hidden"  name="userName" value="<?php echo $assessorDetails['name']; ?>" />
                                            <input type="hidden"  name="school_name" value="<?php echo $schoolName; ?>" />
                                            <div class="clearfix">
                                                <div id="selffeedbackbutton" class="fr clearfix" style="margin-top:36px; display: <?php echo isset($viewSelfSubmit) && $viewSelfSubmit == 1 ? 'block' : 'none'; ?>">
                                                    <button class="fl nuibtn saveBtn" type="button" id="saveSelfFeedback">Save</button>
                                                    <input type="button" autocomplete="off" id="submitSelfFeedback"  class="fl nuibtn submitBtn" value="Submit" />
                                                </div>
                                            </div>

                                            <input type="hidden" class="isAjaxRequest" name="isAjaxRequest"
                                                   value="<?php echo $ajaxRequest; ?>" />
                                            <div class="ajaxMsg mt10" id="createresource"></div>
                                        </form>                                        
                                    </div>
                                    <div class="tab-pane fade" id="myFeedback_peerFeed">
                                        <form method="post" id="post_feedback_form_myFeedback" action="">
                                            <input type="hidden" name="assessment_id" id="assessment_id_myFeedback" value="<?php echo $assessment_id; ?>">
                                            <input type="hidden" name="sub_role" value="<?php echo $sub_role; ?> ">
                                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?> ">
                                            <div class="assesorfeed customFeedScl">
                                                <div class="leftTitleWrap">
                                                    <dl class="fldList ftitle brdW contTable">
                                                        <dt>
                                                            <h2 class="qst">Assessors <em>(Role)</em>:</h2>
                                                        </dt>
                                                    </dl>
                                                    <?php foreach ($assessorsFeedbackQuestions as $data) { ?>
                                                        <?php if ($data['user_type'] == 'peer') { ?>
                                                            <dl class="fldList brdW contTable <?php echo ($data['field_type'] == 'area') ? 'area' : ''; ?> ">
                                                                <?php
                                                                if (!isset($data['child_question'])) {
                                                                    $qustn_indx_peer++;
                                                                    ?>
                                                                    <dt>
                                                                        <?php echo $qustn_indx_peer . ". &nbsp;" . $data['question_name']; ?> 
                                                                    </dt>
                                                                <?php } ?>
                                                            </dl>
                                                            <?php if (isset($data['child_question']) && count($data['child_question']) >= 1) { ?>
                                                                <?php
                                                                $qustn_indx_peer = 0;
                                                                //print_r($data['child_question']);
                                                                foreach ($data['child_question'] as $childQuestion) {
                                                                    $qustn_indx_peer++;
                                                                    // echo $qustn_indx_peer;
                                                                    ?>
                                                                    <dl class="fldList brdW contTable">
                                                                        <dt><?php echo $qustn_indx_peer . ". &nbsp;" . $childQuestion['question_name']; ?></dt>

                                                                    </dl>
                                                                <?php } ?>

                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <div class="rightTableWrap">
                                                    <div class="tableWrap">
                                                        <dl class="fldList brdW contTable">
                                                            <dt>
                                                                <h2 class="qst">Assessors <em>(Role)</em>:</h2>
                                                            </dt>
                                                            <dd class="noPad">
                                                                <table class="table customTbl <?php echo $singleCol . ' ' . $oneTwoCol; ?>">
                                                                    <thead>
                                                                        <tr>
                                                                            <?php foreach ($assessmentTeam as $team) { 
                                                                                $name= $team['name'] . " <em>(" . $team['sub_role_name'] . ")</em>";
                                                                                ?>
                                                                            <th title="<?php echo $name; ?>" class="vtip"><?php echo substr($team['name'],0,15); echo strlen($team['name'])>15?'..':'' ?></th>
                                                                            <?php } ?>
                                                                        </tr>
                                                                    </thead>
                                                                </table>
                                                            </dd>
                                                        </dl>
                                                        <?php foreach ($assessorsFeedbackQuestions as $data) { ?>
                                                            <?php if ($data['user_type'] == 'peer') { ?>
                                                                <dl class="fldList brdW contTable ">

                                                                    <dd class="noPad">
                                                                        <table class="table customTbl <?php echo $singleCol . ' ' . $oneTwoCol; ?>">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <?php
                                                                                    $peerAnswer = '';
                                                                                    foreach ($assessmentTeam as $team) {

                                                                                        //if($data['peer'] == $team['user_id']) {
                                                                                        //}
                                                                                        $answer = isset($peerFeedbackAnswers[$team['user_id']][$data['q_id']]) ? $peerFeedbackAnswers[$team['user_id']][$data['q_id']] : '';
                                                                                        //print_r($answer);die;
                                                                                        if ($data['field_type'] == 'area') {
                                                                                            ?>
                                                                                            <td>
                                                                                                <textarea rows="4" cols="6" name="user[<?php echo $team['user_id']; ?>][<?php echo $data['q_id']; ?>]" class="form-control" placeholder="Your answer"><?php echo isset($answer['answer']) ? $answer['answer'] : ''; ?></textarea>
                                                                                                <input type="hidden" value="<?php echo $team['name']; ?>" name="user[<?php echo $team['user_id']; ?>][name]">
                                                                                            </td>
                                                                                            <?php
                                                                                        }
                                                                                    }
                                                                                    ?>

                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </dd>
                                                                </dl>

                                                                <?php if (isset($data['child_question']) && count($data['child_question']) >= 1) { ?>
                                                                    <?php
                                                                    //print_r($data['child_question']);
                                                                    foreach ($data['child_question'] as $childQuestion) {
                                                                        // echo $qustn_indx_peer;
                                                                        ?>
                                                                        <dl class="fldList brdW contTable">
                                                                            <dd class="noPad">
                                                                                <table class="table customTbl <?php echo $singleCol . ' ' . $oneTwoCol; ?>">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <?php
                                                                                            foreach ($assessmentTeam as $team) {
                                                                                                $answer = isset($peerFeedbackAnswers[$team['user_id']][$childQuestion['q_id']]) ? $peerFeedbackAnswers[$team['user_id']][$childQuestion['q_id']] : '';
                                                                                                ?>
                                                                                                <td>
                                                                                                    <select class="form-control" name="user[<?php echo $team['user_id']; ?>][<?php echo $childQuestion['q_id']; ?>]">
                                                                                                        <option value="">-Select-</option>
                                                                                                        <option value="Always" <?php echo isset($answer['answer']) && $answer['answer'] == 'Always' ? 'selected="selected"' : ''; ?> >Always</option>
                                                                                                        <option value="Mostly" <?php echo isset($answer['answer']) && $answer['answer'] == 'Mostly' ? 'selected="selected"' : ''; ?>>Mostly</option>
                                                                                                        <option value="Sometimes" <?php echo isset($answer['answer']) && $answer['answer'] == 'Sometimes' ? 'selected="selected"' : ''; ?> >Sometimes</option>
                                                                                                        <option value="Rarely" <?php echo isset($answer['answer']) && $answer['answer'] == 'Rarely' ? 'selected="selected"' : ''; ?> >Rarely</option>
                                                                                                    </select>
                                                                                                </td>
                                                                                            <?php } ?>

                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </dd>
                                                                        </dl>
                                                                    <?php } ?>

                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>

                                                        <!-- <div class="clearfix">
                                                            <a class="fr nuibtn viewBtn" href="#">View</a>
                                                        </div> -->
                                                    </div>
                                                </div>

                                                <div class="clearfix">
                                                    <div id="peerfeedbackbutton_myFeedback" class="fr clearfix" style="margin-top:36px; display: <?php echo isset($viewPeerSubmit) && $viewPeerSubmit == 1 ? 'block' : 'none'; ?>">
                                                        <button class="fl nuibtn saveBtn" type="button" id="savePostFeedback" >Save</button>
                                                        <input type="button" autocomplete="off" id="submitPostFeedback" class="fl nuibtn submitBtn" value="Submit">
                                                    </div>
                                                </div>
                                                <input type="hidden" class="isAjaxRequest" name="isAjaxRequest_myFeedback" value="<?php echo $ajaxRequest; ?>" />
                                                <input type="hidden"  name="userEmail" value="<?php echo $userEmail; ?>" />
                                                <input type="hidden"  name="userName" value="<?php echo $assessorDetails['name']; ?>" />
                                                <input type="hidden"  name="school_name" value="<?php echo $schoolName; ?>" />
                                                <div class="ajaxMsg mt10" id="createresource"></div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>                            
    </div>
</div>
</div>
</div>