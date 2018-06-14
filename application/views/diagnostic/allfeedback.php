<?php
$isReadOnly = empty($isReadOnly) ? 0 : 1;
$disabled = isset($self_review) && $self_review == 1 ? "style='display: none'" : "style='display:block' ";
$activeClass = "active";
$inActiveClass = 1;
$sub_role = 0;
$is_team = isset($self_review) && $self_review == 1 ? '' : 'active in';
$singleCol = isset($assessmentTeam) && count($assessmentTeam)<5?'singleCol':'';
$oneTwoCol = isset($assessmentTeam) && count($assessmentTeam)<3?'oneTwoCol':'';
//echo count($assessmentTeam);
$numMembers = count($assessmentTeam)-1;
 $singleColPeerFeedback = isset($assessmentTeam) && $numMembers < 5 ? 'singleCol' : '';
$oneTwoColPeerFeedback = isset($assessmentTeam) && $numMembers < 3 ? 'oneTwoCol' : '';
//$viewSelfSubmit = 1;
//$viewPeerSubmit = 1;
/* if ($is_submit == 1 && $userRoleIds[0] != 8)
  $viewSelfSubmit = 0;
  else if ($selfFeedbackStatus == 1 && $userRoleIds[0] == 8)
  $viewSelfSubmit = 0;
  if ($is_submit_peer == 1 && $userRoleIds[0] != 8)
  $viewPeerSubmit = 0;
  else if ($peerFeedbackStatus == 1 && $userRoleIds[0] == 8)
  $viewPeerSubmit = 0; */
$answer = '';

$i = 1;

///echo "<pre>";print_r($assessmentTeam);
?>

<h1 class="page-title">
    <a href="<?php
    $args = array("controller" => "assessment", "action" => "assessment");
    $args["filter"] = 1;
    //$args["myAssessment"]=1;		
    echo createUrl($args);
    ?>">
        <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
        Manage MyReviews
    </a>  &rarr; <span>Assessor log & Peer Feedback</span> &rarr; <span><?php echo $schoolName; ?></span>
</h1>
<div class="clr"></div>

<div class="panel-group" id="userFeedsAccordion" role="tablist" aria-multiselectable="true">
    <?php
    foreach ($assessmentTeam as $team) {
        $collapse = 'collapse';
        if ($i > 1) {
            $collapse = 'expand';
        }
        ?>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" class="<?php echo ($i > 1) ? 'collapsed' : ''; ?>" data-parent="#userFeedsAccordion" href="#collapse-<?php echo $team['user_id']; ?>">
                        <?php echo $team['name'] . " <em>(" . $team['sub_role_name'] . ")</em>"; ?>
                    </a>
                </h4>
            </div>
            <div id="collapse-<?php echo $team['user_id']; ?>" class="panel-collapse <?php echo ($i > 1) ? 'collapse ' : 'collapse in'; ?> " role="tabpanel">
                <div class="panel-body">                          
                    <div class="userFeedsContent">
                        <div class="boxBody">
                            <div class="transLayer">
                                <div class="tc_wrapper">
                                    <ul class="nav nav-tabs terms">          
                                        <li class="item <?php echo $activeClass ?>" id="selfFeedback_tab_<?php echo $team['user_id']; ?>">
                                            <a href="#selfFeedback_<?php echo $team['user_id']; ?>" data-toggle="tab" class="vtip" title="Assessor Log">Assessor Log</a>
                                        </li>
                                        <li class="item " <?php echo $disabled; ?> id="peerFeedback_tab">
                                            <a href="#peerFeedback_<?php echo $team['user_id']; ?>" data-toggle="tab" class="vtip" title=" Feedback For Peers"> Feedback For Peers</a>
                                        </li>
                                        <li class="item " <?php echo isset($goals[$team['user_id']])?'':'style = "display:none"'; ?> id="peerFeedbackGoals_tab">
                                            <a href="#peerFeedbackGoals_<?php echo $team['user_id']; ?>" data-toggle="tab" class="vtip" title="assessor goals">Goals</a>
                                        </li>
                                    </ul>                                
                                </div>
                                <div class="tab-content">                                
                                    <div role="tabpane1" class="tab-pane fade active in" id="selfFeedback_<?php echo $team['user_id']; ?>" >
                                        <div class="wrapNote"><span>All fields are mandatory.</span></div>
                                        <form method="post" id="post_self_feedback_form_<?php echo $team['user_id']; ?>">
                                            <?php
                                            if (!empty($selfFeedbackQuestion[$team['user_id']])) {
                                                //echo "<pre>";print_r($selfFeedbackQuestion[$team['user_id']]);
                                                $qustn_indx = 1;
                                                foreach ($selfFeedbackQuestion[$team['user_id']] as $data) {
                                                    $sub_role = $data['user_sub_role'];
                                                    $answer = '';
                                                    if(isset($selfFeedbackStatus[$team['user_id']]))
                                                     $answer = !empty($peerFeedbackAnswers[$team['user_id']][$team['user_id']][$data['q_id']]) ? $peerFeedbackAnswers[$team['user_id']][$team['user_id']][$data['q_id']] : array();
                                                    // print_r($answer['answer']);
                                                    // echo $answer[$data['q_id']];
                                                    ?>
                                                    <div class="mb10">
                                                        <label><?php echo $qustn_indx . "." . $data['question_name']; ?> </label>
                                                        <?php if ($data['field_type'] == 'area') { ?>
                                                            <textarea rows="5" cols="8" class="form-control custom selffeed"  name="q_id[<?php echo $data['q_id']; ?>]"><?php echo!empty($answer['answer']) ? $answer['answer'] : ''; ?></textarea>
                                                        <?php } ?>    
                                                    </div>
                                                    <?php
                                                    $qustn_indx++;
                                                }
                                            }
                                            ?>
                                            <input type="hidden" name="assessment_id" value="<?php echo $assessment_id; ?> ">
                                            <input type="hidden" name="sub_role" value="<?php echo $team['user_sub_role']; ?> ">
                                            <input type="hidden" name="user_id" value="<?php echo $team['user_id']; ?> ">
                                            <div class="clearfix">
                                                <?php
                                                // $is_submit =  in_array($team['user_id'], array_column($selfFeedbackStatus, 'user_id'));
                                                $is_submit = 0;
                                                if (!empty($selfFeedbackStatus[$team['user_id']])) {

                                                    if ($selfFeedbackStatus[$team['user_id']]['is_submit'] && $selfFeedbackStatus[$team['user_id']]['feedback_status']) {
                                                        $is_submit = 1;
                                                    } else
                                                        $is_submit = 0;
                                                }
                                                if(!isset($selfFeedbackStatus[$team['user_id']])) {
                                                    $is_submit = 1;
                                                }
                                                ?>
                                                <div id="selffeedbackbutton_<?php echo $team['user_id']; ?>" class="fr clearfix" style="margin-top:36px; display: <?php echo isset($is_submit) && $is_submit == 1 ? 'none' : 'block'; ?>">
                                                    <button class="fl nuibtn saveBtn saveApprove" type="button"  id="saveSelfFeedback_<?php echo $team['user_id']; ?>">Save</button>
                                                    <button class="fl nuibtn approveBtn submitApprove " type="button" id="submitSelfFeedback_<?php echo $team['user_id']; ?>" alt="<?php echo $team['user_id']; ?>" value="">Approve</button>
                                                    <!--<input type="button" autocomplete="off" id="submitSelfFeedback_<?php //echo $team['user_id']; ?>" alt="<?php //echo $team['user_id']; ?>"   class="fl nuibtn submitBtn" value="" />-->
                                                </div>
                                            </div>

                                            <input type="hidden" class="isAjaxRequest" name="isAjaxRequest"
                                                   value="<?php echo $ajaxRequest; ?>" />
                                            <div class="ajaxMsg mt10" id="createresource_<?php echo $team['user_id']; ?>"></div>
                                        </form>
                                    </div>


                                    <!--peer feedback start-->
                                    <div role="tabpane1" class="tab-pane fade " id="peerFeedback_<?php echo $team['user_id']; ?>" >
                                        <div class="wrapNote"><span>All fields are mandatory.</span></div>
                                        <form method="post" id="post_feedback_form_<?php echo $team['user_id']; ?>" action="">
                                            <input type="hidden" name="assessment_id" id="id_assessment_id" value="<?php echo $assessment_id; ?>">
                                            <input type="hidden" name="sub_role" value="<?php echo $team['user_sub_role']; ?> ">
                                            <input type="hidden" name="user_id" value="<?php echo $team['user_id']; ?> ">
                                            <div class="assesorfeed customFeedScl">
                                                <div class="leftTitleWrap">
                                                    <dl class="fldList ftitle brdW contTable">
                                                        <dt>
                                                            <h2 class="qst">Assessors:</h2>
                                                        </dt>
                                                    </dl>
                                                          <?php 
                                                          $qustn_indx_peer = 0;
                                                          foreach ($allFeedbackQuestion[$team['user_id']] as $data) { ?>
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
                                                                            </dt></dl>
                                                                        <?php }
                                                                }
                                                                if (isset($data['child_question']) && count($data['child_question']) >= 1) { ?>

                                                                        <?php
                                                                        $qustn_indx_peer = 0;
                                                                        foreach ($data['child_question'] as $childQuestion) {
                                                                            $qustn_indx_peer++;
                                                                            ?>

                                                                            <dl class="fldList brdW contTable <?php echo ($data['field_type'] == 'area') ? 'area' : ''; ?> ">
                                                                                <dt><?php echo $qustn_indx_peer . ". &nbsp;" . $childQuestion['question_name']; ?></dt>
                                                                            </dl>
                                                                        <?php 
                                                                        }
                                                                }
                                                          }
                                                        ?>
                                                    
                                                   
                                                </div>
                                                <div class="rightTableWrap">
                                                    <div class="tableWrap">
                                                        <dl class="fldList brdW contTable">
                                                            <dd class="noPad">
                                                                <table class="table customTbl <?php echo $singleColPeerFeedback . ' ' . $oneTwoColPeerFeedback; ?>">
                                                                    <thead>
                                                                        <tr>
                                                                            <?php
                                                                            foreach ($assessmentTeam as $peer) {
                                                                                if ($team['user_id'] != $peer['user_id']) {
                                                                                    ?>
                                                                            <th title="<?php echo $peer['name']; ?>" class="vtip"><?php echo substr($peer['name'],0,15); echo strlen($peer['name'])>15?'..':'' ?></th>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    </tbody>
                                                                </table>
                                                            </dd>
                                                        </dl>
                                                        <?php foreach ($allFeedbackQuestion[$team['user_id']] as $data) { ?>
                                                            <?php
                                                            if ($data['user_type'] == 'peer') {
                                                                //$qustn_indx_peer = 1;
                                                                ?>
                                                                <dl class="fldList brdW contTable">
                                                                    <?php
                                                                    if (!isset($data['child_question'])) {
                                                                        ?>
                                                                       
                                                                    <?php } ?>
                                                                    <dd class="noPad">
                                                                        <table class="table customTbl <?php echo $singleColPeerFeedback . ' ' . $oneTwoColPeerFeedback; ?>">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <?php
                                                                                    $peerAnswer = '';
                                                                                    foreach ($assessmentTeam as $peer) {

                                                                                        if ($team['user_id'] != $peer['user_id']) {
                                                                                            //}
                                                                                            $answer = '';
                                                                                             if(isset($peerFeedbackStatus[$team['user_id']]))
                                                                                             $answer = !empty($peerFeedbackAnswers[$team['user_id']][$peer['user_id']][$data['q_id']]) ? $peerFeedbackAnswers[$team['user_id']][$peer['user_id']][$data['q_id']] : array();
                                                                                            //$answer = isset($peerFeedbackAnswers[$peer['user_id']][$data['q_id']]) ? $peerFeedbackAnswers[$peer['user_id']][$data['q_id']] : '';
                                                                                            //print_r($answer);die;
                                                                                            if ($data['field_type'] == 'area') {
                                                                                                ?>
                                                                                                <td>
                                                                                                    <textarea rows="4" cols="6" name="user[<?php echo $peer['user_id']; ?>][<?php echo $data['q_id']; ?>]" class="form-control" placeholder="Your answer"><?php echo isset($answer['answer']) ? $answer['answer'] : ''; ?></textarea>
                                                                                                    <input type="hidden" value="<?php echo $peer['name']; ?>" name="user[<?php echo $peer['user_id']; ?>][name]">
                                                                                                    <input type="hidden" value="<?php echo $peer['email']; ?>" name="user[<?php echo $peer['user_id']; ?>][email]">
                                                                                                </td>
                                                                                                <?php
                                                                                            }
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
                                                                    $qustn_indx_peer = 0;
                                                                    foreach ($data['child_question'] as $childQuestion) {
                                                                        $qustn_indx_peer++;
                                                                        ?>

                                                                        <dl class="fldList brdW contTable">
                                                                            <dd class="noPad">
                                                                                <table class="table customTbl <?php echo $singleColPeerFeedback . ' ' . $oneTwoColPeerFeedback; ?>">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <?php
                                                                                            foreach ($assessmentTeam as $peer) {
                                                                                                if ($team['user_id'] != $peer['user_id']) {
                                                                                                    // $answer = isset($peerFeedbackAnswers[$peer['user_id']][$childQuestion['q_id']]) ? $peerFeedbackAnswers[$peer['user_id']][$childQuestion['q_id']] : '';
                                                                                                    $answer = '';
                                                                                                    if(isset($peerFeedbackStatus[$team['user_id']]))
                                                                                                    $answer = !empty($peerFeedbackAnswers[$team['user_id']][$peer['user_id']][$childQuestion['q_id']]) ? $peerFeedbackAnswers[$team['user_id']][$peer['user_id']][$childQuestion['q_id']] : array();
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
                                                                                                }
                                                                                            }
                                                                                            ?>

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
                                                    <?php
                                                    //print_r($peerFeedbackStatus);
                                                    $is_submit_peer = 0;
                                                    if (!empty($peerFeedbackStatus[$team['user_id']])) {

                                                        if ($peerFeedbackStatus[$team['user_id']]['is_submit'] && $peerFeedbackStatus[$team['user_id']]['feedback_status']) {
                                                            $is_submit_peer = 1;
                                                        } else
                                                            $is_submit_peer = 0;
                                                    }
                                                    
                                                    if(!isset($peerFeedbackStatus[$team['user_id']])) {
                                                        $is_submit_peer = 1;
                                                    }
                                                    // $is_submit_peer =  in_array($team['user_id'], array_column($peerFeedbackStatus, 'user_id'));
                                                    ?>
                                                    <div id="peerfeedbackbutton_<?php echo $team['user_id']; ?>" class="fr clearfix" style="margin-top:36px; display: <?php echo isset($is_submit_peer) && $is_submit_peer == 1 ? 'none' : 'block'; ?>">
                                                        <button class="fl nuibtn saveBtn saveApprove" type="button" id="savePostFeedback_<?php echo $team['user_id']; ?>" >Save</button>
                                                         <button class="fl nuibtn approveBtn submitApprove" type="button" id="submitPostFeedback_<?php echo $team['user_id']; ?>" alt="<?php echo $team['user_id']; ?>" value="">Approve</button>
                                                        <!--<input type="button" autocomplete="off" id="submitPostFeedback_<?php //echo $team['user_id']; ?>" class="fl nuibtn submitBtn" value="Submit">-->
                                                    </div>
                                                </div>
                                                <input type="hidden" class="isAjaxRequest" name="isAjaxRequest"	value="<?php echo $ajaxRequest; ?>" />
                                                <input type="hidden"  name="school_name"	value="<?php echo $schoolName; ?>" />
                                                <div class="ajaxMsg mt10" id="createresource_<?php echo $team['user_id']; ?>"></div>
                                            </div>
                                        </form>
                                    </div>
                            <div role="tabpane1" class="tab-pane fade " id="peerFeedbackGoals_<?php echo $team['user_id']; ?>" >
                                        <form method="post" id="post_feedback_form_<?php echo $team['user_id']; ?>" action="">
                                           
                                                
                                                    
                                                        <dl class="fldList goal"> <dt>
                                                            Goals
                                                            </dt> 
                                                        
                                                            <dd class="noPad">
                                                                <textarea name="goals" class="form-control"><?php echo $goals[$team['user_id']];?></textarea>
                                                            </dd>
                                                        </dl>
                                                       

                                                        <!-- <div class="clearfix">
                                                            <a class="fr nuibtn viewBtn" href="#">View</a>
                                                        </div> -->
                                        </form>
                                    </div>

                                    <!--peer feedback end-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $i++;
    }
    ?>
</div>






