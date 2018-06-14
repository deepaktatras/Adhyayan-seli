<?php
if (isset($eUser['user_id'])) {
  
   // echo '</pre>';
    $eUser['contract_value'] = !empty($eUser['contract_value'])? explode(',', $eUser['contract_value']):'';
    if(isset($_REQUEST['process'])){
        $user_role = $auser['role_ids'];
    } else {
        $user_role = $user['role_ids'];
    }
    $is_active = isset($_REQUEST['refer']) &&  $_REQUEST['refer'] == 'myattendence'?1:0;
    //echo '<pre>';
     //print_r($user_role);
    $ref = isset($_REQUEST['process']) && array_key_exists('process', $_REQUEST) ?'invite':'';
    $disabled = isset($eUser['term_condition']) && $eUser['term_condition']==1?"style='display: none'":"style='display:block' ";
    //$activeClass = isset($eUser['term_condition']) && $eUser['term_condition']==1?"active":"";
    $inActiveClass = 1;
    $submit = !empty($eUser['is_submit']) && $eUser['is_submit']==1?'disabled':'';
   // echo "$('#reviewDtls').removeClass('completed')";
    $assessment_type=array("1"=>"School Review",
                           "5"=>"College Review",
                           "2"=>"Teacher Review",
                           "4"=>"Student Review"
                          );
   
    ?>
<style>
    p{
        font-size: 13px;
        margin-top: 5px;
    }
    .errorRed{
        border: 1px solid red !important;
    }
</style>
    <div class="filterByAjax user-list"  data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
    <form id="userAssessmentForm"  method="post" enctype="multipart/form-data">
        <input type="hidden" name="table" value="<?php echo $eUser['table_name']?>" id="table"/>
        <input type="hidden" name="term_condition" value="<?php echo $eUser['term_condition']?>" id="term_condition"/>
        <div class="row" id="assessmentForm">
            <div class="fl">
                <h1 class="page-title">
                    <?php
                   // print_r($eUser['role_ids']);
                    $role_ids = explode(',', $eUser['role_ids']);
                    $urlParams = array();
                    foreach($_REQUEST as $key=>$val) {
                        $urlParams[$key] = $val;
                        
                    }
                    $urlParams['action'] = 'userProfile';
                    $userId = $_REQUEST['id'];
                    $clientId = $_REQUEST['client_id'];
                    if(!isset($_GET['process'])){
                        //if(current($user['role_ids'])==8){
                            ?>
                            <a href="<?php
                           // controller=user&action=userProfile&id=456&client_id=28
                            $args = array("controller" => "user", "action" => "userProfile","id"=>$userId,""=>'client_id',""=>$clientId);
                            echo createUrl($urlParams);
                            ?>">
                                <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
                                Manage <?php
                                        if(current($user['role_ids'])==8){
                                            echo 'Assessors';
                                        } else {
                                            echo 'Users';
                                        }
                                        ?>
                                
                            </a> &rarr; <?php echo $eUser['name'] ?> Profile Details &rarr; My Journey 
                            <?php
                        //}
                    }
                  ?>
                </h1>
            </div>
         <!--   <h1 class="page-title">
Add Resource
<a id="addschoolAssBtn" class="btn btn-primary pull-right " href="?controller=resource&action=listResourceDirectory&ispop=1" data-size="800" style="margin-left:10px">View All Directory</a>
<a id="addschoolDirBtn" class="btn btn-primary pull-right " href="?controller=resource&action=createResourceDirectory&ispop=1" data-size="800">Add New Directory</a>
</h1>-->
            <h1 class="page-title">            
            <?php
            if($ref==''){
            ?>
            <a href="<?php echo "?controller=user&action=changePassword&id=".$eUser['user_id']."&ispop=1"; ?>" class="btn btn-primary pull-right execUrl vtip" 
                title="Click to update password." id="addUserBtn" style="margin-left:10px">Change Password</a>
            <?php
            }?>
           <!-- <a href="<?php echo "?controller=user&action=myJourney&id=".$eUser['user_id']."&ispop=1"; ?>" class="btn btn-primary pull-right vtip" 
                title="Click for my journey page." id="myjourneyBtn">My Journey</a>-->
            </h1>
                     <div class="clr"></div>

        </div>

        <div id="aqsForm" class="assessorProfile dataChanged">
            <div class="ylwRibbonHldr">
                <a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab4_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
                <div class="collapse navbar-collapse tabitemsHldr" id="tab4_Toggle">
                    <ul class="yellowTab nav nav-tabs"> 
                        
                        <li class="item active" id="activityStatus_tab">
                            <a href="#activityStatus" data-toggle="tab" class="vtip" title="Activity Status">Activity Status</a>
                        </li>
                        <li class="item" id="reviewDtls_tab">
                            <a href="#reviewDtls" data-toggle="tab" class="vtip" title="Review & Feedback">Review & Feedback</a>
                        </li>
                        
                       <!-- <li class="item" id="goalSetting_tab">
                            <a href="#goalSetting" data-toggle="tab" class="vtip" title="Goal setting">Goal setting</a>
                        </li>-->
                        
                    </ul>
                </div>
            </div>  
            <div class="subTabWorkspace pad26">
                <div class="tab-content"> 
                    
                     <!-- Start Activity Status-->
                     <div role="tabpane2" class="tab-pane fade in active" id="activityStatus">
                        <i style="font-size: 11px; font-weight: bold;">Note: All fields are mandatory. </i><br/>
                        <div class="boxBody" style="margin-top: 10px;">
                            <div class="tc_wrapper introductoryPanel">
                            <div class="collapse navbar-collapse tabitemsHldr" id="tab_terms">
                                 <ul class="nav nav-tabs terms"> 
                                    
                                    <?php
                                    if($ref==''){
                                    ?>
                                    <li class="item <?php echo $is_active == 1?'':'active';?>" id="pd_myreview_tab">
                                          <a href="#myreview" data-toggle="tab" class="vtip" title="My Reviews">My Reviews</a>
                                    </li>
                                    <li class="item <?php echo $is_active == 1?'active':'';?>" id="myprogress_tab">
                                        <a href="#myattendence" data-toggle="tab" class="vtip" title="My Attendance">My Attendance</a>
                                    </li>
                                    <!--<li class="item" id="nextstep_tab">
                                        <a href="#nextstep" data-toggle="tab" class="vtip" title="My Next steps">My Next steps</a>
                                    </li>
                                    <li class="item" id="attendence_tab">
                                        <a href="#attendence" data-toggle="tab" class="vtip" title="My Attendance">My Attendance</a>
                                    </li>-->
                                    <?php
                                    }?>
                                </ul>
                            </div>
                            <div class="tab-content">
                               
                                <?php
                                if ($ref == '') {
                                        ?>
                                <div role="tabpane1" class="tab-pane fade <?php echo $is_active == 1?'':'active in';?>" id="myreview" >
                                            <h2 class="" style="font-size: 17px;">Please see the distribution of reviews by user's role.</h2>
                                            <div class="asmntTypeContainer">
                                                <div class="tableHldr">
                                                    <table class="cmnTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Assessor Role</th>
                                                                <?php
                                                                foreach($assessment_type as $key=>$val){
                                                                ?>
                                                                <th><?php echo $val ?></th>
                                                                <?php
                                                                $$key=0;
                                                                }
                                                                ?>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        //print_r($userCount);
                                                        $sum = 0;
                                                        if (!empty($userCount)) {
                                                            $i = 1;
                                                            foreach ($userCount as $dt) {
                                                                //$sum = $sum + $dt['num'];
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $dt['sub_role_name']; ?></td>
                                                                    
                                                                            <?php
                                                                      
                                                                            $tot_internal=0;
                                                                            foreach($assessment_type as $key=>$val){
                                                                            ?>
                                                                            <td class="count"><?php 
                                                                            if($dt[$val]>0){
                                                                            ?>
                                                                            <a href="<?php echo createUrl(array("controller" => "assessment", "action" => "assessment", "uid" => $eUser['user_id'], 'rid' => $dt['sub_role_id'], 'aid' => $key)); ?>"
                                                                               title="Click here to Reviews">
                                                                                   <?php echo $dt[$val]; ?>
                                                                            </a>    
                                                                            <?php
                                                                            }else{
                                                                            echo $dt[$val];
                                                                            }
                                                                                    
                                                                            ?></td>
                                                                            <?php
                                                                            $tot_internal=$tot_internal+$dt[$val];
                                                                            $$key=$$key+$dt[$val];
                                                                            }
                                                                            ?>
                                                                            <td class="count"><?php echo $tot_internal; ?></td>
                                                                            <!--<td>
                                                                            <a href="<?php //echo createUrl(array("controller" => "assessment", "action" => "assessment", "uid" => $eUser['user_id'], 'rid' => $dt['sub_role_id'])); ?>"
                                                                               title="Click here to Reviews">
                                                                                   <?php //echo $dt['num']; ?>
                                                                            </a> 
                                                                        </td>-->
                                                                         

                                                                    
                                                                </tr>   
                                                                <?php
                                                                $sum=$sum+$tot_internal;
                                                                $i++;
                                                            }
                                                        }
                                                        ?>
                                                        <tr class="total">
                                                            <td><b>Total Reviews</b></td>
                                                            <?php
                                                            foreach($assessment_type as $key=>$val){
                                                            ?>
                                                            <td class="count"><span class="sum" style="font-size: 14px;"><?php echo $$key; ?></span></td>
                                                            <?php
                                                            }
                                                            ?>
                                                            <td class="count"><span class="sum" style="font-size: 14px;"><?php echo $sum; ?></span></td>
                                                        </tr>


                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                
                                <div role="tabpane1" class="tab-pane <?php echo $is_active == 1?'active':'';?>" id="myattendence" >
                                            <h2 class="" style="font-size: 17px;">Please see the distribution of reviews by user's role.</h2>
                                            <div class="asmntTypeContainer">
                                                <div class="tableHldr">
                                                    <table class="cmnTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Workshops attended As</th>
                                                                <th>No. of Workshops</th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        $sum = 0;
                                                        if (!empty($workshopCount)) {
                                                            $i = 1;
                                                            foreach ($workshopCount as $dt) {
                                                                $sum = $sum + $dt['num'];
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $dt['workshop_sub_role_name']; ?></td>
                                                                    <td class="count">
                                                                        <?php
                                                                        if ($dt['num'] > 0) {
                                                                            ?>
                                                                            <a href="<?php echo createUrl(array("controller" => "workshop", "action" => "myworkshop",'client_id'=>$clientId, "uid" => $eUser['user_id'],'role'=>$dt['workshop_sub_role_name'], 'role_id' => $dt['workshops_user_role_id'])); ?>"
                                                                               title="Click here to Reviews">
                                                                                   <?php echo $dt['num']; ?>
                                                                            </a>    
                                                                            <?php
                                                                        } else {
                                                                            echo $dt['num'];
                                                                        }
                                                                        ?>

                                                                    </td>
                                                                </tr>   
                                                                <?php
                                                                $i++;
                                                            }
                                                        }
                                                        ?>
                                                        <tr class="total">
                                                            <td><b>Total Workshop Attended</b></td>
                                                            <td class="count"><span class="sum" style="font-size: 14px;"><?php echo $sum; ?></span></td>
                                                        </tr>


                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                       
        <?php }
    ?>
                            </div>
                        </div>                    
                        </div>
                    </div> 
                    
                    <!-- End of Activity Status -->
                    
                    <!-- Start Review and Feedback-->
                     <div role="tabpane2" class="tab-pane fade in" id="reviewDtls">
                        <div class="boxBody" style="margin-top: 10px;">
                            <div class="tc_wrapper introductoryPanel">
                            <div class="collapse navbar-collapse tabitemsHldr" id="tab_terms">
                                 <ul class="nav nav-tabs terms"> 
                                    
                                    <?php
                                    if($ref==''){
                                    ?>
                                    <li class="item active" id="pd_introductoryAssessment_tab">
                                          <a href="#introductoryAssessment" data-toggle="tab" class="vtip" title="Introductory Assessment ">Introductory Assessment</a>
                                    </li>
                                    <?php //if(isset($introductoryAssessment['score']) ) { ?>
                                    <li>
                                        <div class="statistic">Total Score : <span class="value"><span id="score"><?php echo (isset($introductoryAssessment['score']))?$introductoryAssessment['score']:0;?></span>/14</span></div>
                                    </li>
                                    <?php //} ?>
                                   <!-- <li class="item" id="nextstep_tab">
                                        <a href="#nextstep" data-toggle="tab" class="vtip" title="Peer Feedback">Peer Feedback</a>
                                    </li>
                                    <li class="item" id="attendence_tab">
                                        <a href="#attendence" data-toggle="tab" class="vtip" title="Adhyayan Feedback">Adhyayan Feedback</a>
                                    </li>-->
                                    <?php
                                    }?>
                                </ul>
                            </div>
                            <div class="tab-content">
                               
                                <?php
                                if ($ref == '') {
                                        ?>
                                
                                <div role="tabpane3" class="tab-pane active" id="introductoryAssessment">
                        <i class="noteTxt">Note: All questions are mandatory. </i><br/>
                        <div class="row mt15 introductory">                            
                            <div class="col-sm-12">
                                
                                
                                    <?php 
                                    $questionNum = 1;
                                    foreach($assessmentQuestionList as $question) { 
                                       ?>
                                       <dl class="fldList"><dt>
                                        <?php if($question['rank'] == 1) { ?>
                                        <h2>An Adhyayan Assessor</h2>
                                            
                                      <?php  }else  if($question['rank'] == 2) { ?>
                                        <h2>Evidence vs. Supposition</h2>
                                        <span>Identify the difference between evidence and supposition in order to make evidence based
                                            judgements of school performance.<br></span>
                                            
                                      <?php  }else  if($question['rank'] == 3) { ?>
                                        <h2>Your learning journey</h2>
                                      <?php  }
                                            
                                        ?>
                                        
                                        <?php echo $questionNum.".". $question['question'];?>
                                        </dt>
                                        
                                            <?php if($question['field_name']!='') { ?>
                                            <input  type="hidden"   name="<?php echo $question['field_name'];?>[]" value="<?php echo $question['q_id'];?>"> 
                                            <?php } ?>
                                            
                                            <?php if ($question['value_field'] == 't_area') { ?>
                                            <dd class="subDefTxt"><textarea class="form-control"  name="<?php echo $question['field_name'];?>[]" id="aqs_improvement_text"><?php echo isset($introductoryAnswer[$question['field_name']])?$introductoryAnswer[$question['field_name']]:'' ?></textarea></dd>
                                            <?php } else if ($question['value_field'] == 'text') { ?>
                                            <dd class="subDefTxt"><input  type="text" class="form-control"  name="<?php echo $question['field_name'];?>[]" id="aqs_improvement_text" value="<?php echo isset($introductoryAnswer[$question['field_name']])?$introductoryAnswer[$question['field_name']]:'' ?>"></dd>
                                            <?php } ?>
                                        <?php 
                                        
                                        if($question['options'] !='' ) {
                                            
                                            ?>
                                        <dd>
                                            <!--<div class="clearfix">
                                                <div class="chkHldr"><input type="checkbox" class="user-roles" name="roles[]" autocomplete="off" value="1" id="role_id_1" onclick="checkboxEnableDisable(&quot;role_id_&quot;,1,&quot;user-roles&quot;,1)"><label class="chkF checkbox"><span>Super Admin</span></label></div>
                                            </div>--><div class="clearfix autoHe8">
                                                            
                                                            <?php 
                                                             if (isset($question['option_field']) && $question['option_field'] == 'checkbox') {
                                                                 
                                                                    $optionValue = explode("/",$question['options']);
                                                                    $introductoryAnswerArray = array_column($introductoryAnswerOption, 'option_id');
                                                                    //print_r($introductoryAnswerOption);
                                                                    ?>
                                                                    <p class="mb10">  
                                                                        Please select one or more answers<br>
                                                                        <em>Check all that apply.</em>                                                                    
                                                                    </p>
                                                                    <?php
                                                                    foreach($optionValue as $value) {
                                                                         $value = explode("-",$value);
                                                                         $value[1] = isset($value[1])?$value[1]:'';
                                                                         if($value[1]) {
                                                                        ?><div class="chkHldr">
                                                                                <input type="checkbox" name="<?php echo $question['field_name'];?>[]"  value="<?php echo $value[1];?>" <?php echo in_array($value[1],$introductoryAnswerArray)?'checked="checked"':'';?> ><label class="chkF checkbox"><span><?php echo $value[0];?></span></label> </div>

                                                                   <?php  
                                                                         }
                                                                    }
                                                                   ?>
                                                            <?php
                                                            } else if (isset($childQuestion['value_field']) && $childQuestion['value_field'] == 't_area') {
                                                                ?>
                                                                <textarea class="form-control" name="<?php echo $question['field_name'];?>[]" id="aqs_improvement_text"><?php echo isset($introductoryAnswer[$question['field_name']])?$introductoryAnswer[$question['field_name']]:'' ?></textarea>
                                                            <?php } else if (isset($childQuestion['value_field']) && $childQuestion['value_field'] == 'text') { ?>
                                                                <input  type="text" class="form-control"  name="<?php echo $question['field_name'];?>[]" id="aqs_improvement_text" style="margin-left: 10px; width: 99%;" value="<?php echo isset($introductoryAnswer[$question['field_name']])?$introductoryAnswer[$question['field_name']]:'' ?>">                                                      
                                                            <?php } else if (isset($question['option_field']) && $question['option_field'] == 'radio') { 
                                                                    $optionValue = explode("/",$question['options']); ?>
                                                               <?php     foreach($optionValue as $value) { 
                                                                            $value = explode("-",$value);
                                                                        ?>
                                                                        <div class="chkHldr" style="width: 90px;margin-left:20px;"><input type="radio" name="<?php echo $question['field_name'];?>[]" value="<?php echo $value[0];?>" <?php echo $introductoryAnswer[$question['field_name']] == $value[0]?'checked="checked"':'' ;?> ><label class="chkF radio"><span><?php echo $value[0];?></span>
                                                                            </label></div>

                                                                   <?php  }
                                                            } ?>
                                                                
                                                                </div>
                                        </dd>
                                          <?php   
                                        }else if($question['options'] =='' &&  (isset($question['child_question']) && count($question['child_question'])>=1 )) { 
                                            
                                            $childQuestionNum = 'A';
                                            foreach($question['child_question'] as $childQuestion) { ?>
                                                    
                                                <dt>
                                                <?php echo strtolower($childQuestionNum).". ". $childQuestion['question'];?>
                                                </dt>
                                                
                                                    <?php if($childQuestion['field_name']!='') { ?>
                                                        <input type="hidden" name="<?php echo $childQuestion['field_name'];?>[]"  value="<?php echo $childQuestion['q_id'];?>" >
                                                    <?php } ?> 
                                                        <dd class="subDefTxt">
                                                    <div class="clearfix autoHe8">
                                                        <?php if (isset($childQuestion['value_field']) && $childQuestion['value_field'] == 't_area') { ?>
                                                            <textarea class="form-control"  name="<?php echo $childQuestion['field_name'];?>[]" id="aqs_improvement_text"><?php echo isset($introductoryAnswer[$childQuestion['field_name']])?$introductoryAnswer[$childQuestion['field_name']]:'' ?></textarea>
                                                        <?php } else if (isset($childQuestion['value_field']) && $childQuestion['value_field'] == 'text') { ?>
                                                            <input  type="text" class="form-control"  name="<?php echo $childQuestion['field_name'];?>[]" id="aqs_improvement_text" value="<?php echo isset($introductoryAnswer[$childQuestion['field_name']])?$introductoryAnswer[$childQuestion['field_name']]:'' ?>">                                                      
                                                        <?php } else if (isset($childQuestion['option_field']) && $childQuestion['option_field'] == 'checkbox') { 
                                                                $optionValue = explode("/",$childQuestion['options']); 
                                                                $introductoryAnswerArray = array_column($introductoryAnswerOption, 'option_id');
                                                                ?>
                                                                        <p class="mb10">  
                                                                        Please select one or more answers<br>
                                                                        <em>Check all that apply.</em>                                                                    
                                                                    </p>
                                                                <?php 
                                                                foreach($optionValue as $value) { 
                                                                 $value = explode("-",$value);?>
                                                                <div class="chkHldr"><input type="checkbox" name="<?php echo $childQuestion['field_name'];?>[]"  value="<?php echo $value[1];?>" <?php echo in_array($value[1],$introductoryAnswerArray)?'checked="checked"':'';?> <?php echo ($value[0] == 'Others')?' onclick="getOtherText();"':'';?> ><label class="chkF checkbox"><span><?php echo $value[0];?></span></label>
                                                               
                                                                </div>
                                                                     <?php if($value[0] == 'Others') { ?>
                                                                    <div id="school_rating_txt" style=" <?php echo in_array($value[1],$introductoryAnswerArray)?'display:block"':'display:none';?>"><input  type="text" class="form-control"  name="school_rating_txt" id="school_rating_others" value="<?php echo isset($introductoryAssessment['school_rating_txt'])?$introductoryAssessment['school_rating_txt']:'' ?>"> </div>
                                                               <?php  }?>
                                                                    
                                                               <?php  }
                                                        } else if (isset($childQuestion['option_field']) && $childQuestion['option_field'] == 'radio') { 
                                                                $optionValue = explode("/",$childQuestion['options']); ?>
                                                             <?php   foreach($optionValue as $value) { 
                                                                    $value = explode("-",$value);
                                                                    ?>
                                                                <div class="chkHldr" style="width: 90px;margin-left:20px;">  <input type="radio" name="<?php echo $childQuestion['field_name'];?>[]"  value="<?php echo $value[0];?>" <?php echo $introductoryAnswer[$childQuestion['field_name']] == $value[0]?'checked="checked"':'' ;?>><label class="chkF radio" ><span><?php echo $value[0];?></span>
                                                                      </label></div>
                                                                    
                                                               <?php  } 
                                                               
                                                        }
                                                               ?>
                                                    </div>
                                                </dd>
                                                <?php 
                                                if($childQuestion['options'] !='') { ?>
                                                    
                                                    <dd>
                                                        <?php //echo $childQuestion['options'];?>
                                                    </dd>
                                                <?php 
                                                
                                                }
                                                $childQuestionNum++;
                                            }
                                                
                                            
                                            }
                                            
                                        
                                    ?>
                                        </dl>
                                           <?php 
                                           $questionNum++;
                                        }
                                    
                                    ?>
                                </dl>
                                   
                            </div>
                            
                               
                        </div>
                        <div class="clearfix">
                          <div class="fr clearfix padT10" id="termsRow"  >
                <?php
                   if( (in_array(3,$user_role) || in_array(4,$user_role))  && !isset($introductoryAssessment['is_submit'])   ){
                    ?>  
                    
                    <button class="btn btn-primary disabled" type="button" id="saveIntroductoryAssessment">
                        <!--<i class="fa fa-save"></i>-->Save
                    </button>
                     <button class="btn btn-primary disabled" type="button" id="submitIntroductoryAssessment">
                        <!--<i class="fa fa-input"></i>-->Submit
                    </button>
                    <?php
                    } else if( (in_array(3,$user_role) || in_array(4,$user_role))  && (isset($introductoryAssessment['is_submit']) && $introductoryAssessment['is_submit']!=1   )){
                    ?>  
                    
                    <button class="btn btn-primary disabled" type="button" id="saveIntroductoryAssessment">
                        <!--<i class="fa fa-save"></i>-->Save
                    </button>
                    <button class="btn btn-primary disabled" type="button" id="submitIntroductoryAssessment">
                        <!--<i class="fa fa-input"></i>-->Submit
                    </button>
                    <?php
                    } 
                    ?>
                    <input type="hidden" name="user_id" value="<?php echo $eUser['user_id'];?>">
                            </div>
                        </div>
                    </div>
                                       
                                     
        <?php }
    ?>
                            </div>
                        </div>                    
                        </div>
                    </div> 
                    
                    <!-- End Start Review and Feedback -->
                    
                    
                </div>
            </div>
            <?php
            if(isset($_REQUEST['process']) && $_REQUEST['process']=='invite'){
                ?>
                <input type="hidden" class="" name="roles[]" value="4"/>    
                <?php
            }
           // if($eUser['table_name']=='d_user' || (isset($_REQUEST['process']) && $_REQUEST['process']=='invite')){
               /* if(in_array(4, $user_role) && $eUser['term_condition']==0){
                    ?>
                    <div class="fr clearfix" id="agreeRow">																
                        <button class="btn btn-primary" id="agreeButton" type="button">I Agree</button>
                        <button class="btn btn-primary" id="disagreeButton" type="button">I Disagree</button>									
                    </div>
                    <?php
                }*/
                
                
           // }
            ?>
            
            
        </div>
        <!--<div id="validationErrors">dsfasdf</div>-->
        <div class="ajaxMsg" id="createresource"></div>
        <?php
        if(isset($introductoryAssessment['is_submit']) && $introductoryAssessment['is_submit']==1){
            ?>
            <input type="hidden" class="" name="submit_value" value="<?php echo $introductoryAssessment['is_submit']; ?>" id="submit_value"/>    
            <?php
        } else {
            ?>
            <input type="hidden" class="" name="submit_value" value="0" id="submit_value"/>    
            <?php
        }
        ?>
        <input type="hidden" class="" name="process" value="<?php echo $ref; ?>" id="process"/>
        <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
        <div id="validationErrors"></div>
    </form>
    </div>
    <script type="text/javascript">
        $("textarea.word_count").textareaCounter();
        $(document).ready(function(){
            $('.mask_ph').mask("(+99) 999-9999-999");
        });
    </script>
    <?php
} else {
    echo '<h1>User does not exist</h1>';
}
?>