<?php
if (isset($eUser['user_id'])) {

    $eUser['contract_value'] = !empty($eUser['contract_value']) ? explode(',', $eUser['contract_value']) : '';
    if (isset($_REQUEST['process'])) {
        $user_role = $auser['role_ids'];
    } else {
        $user_role = $user['role_ids'];
    }
    $userRoles = explode(',', $eUser['role_ids']);
    
    //echo '<pre>';
    //print_r($userRoles);
    //echo '</pre>';
    $isAdmin = 0;
    $isFacilitator = 0;
    $noExternal = 1;
    if(in_array(1, $user_role) || in_array(2, $user_role) ||  in_array(8, $user_role)){
       $isAdmin = 1; 
    }
    if(in_array(9, $userRoles) ){
        $isFacilitator = 1;
    }if(!in_array(4, $userRoles) ){
        $noExternal = 0;
    }
   
    $ref = isset($_REQUEST['process']) && array_key_exists('process', $_REQUEST) ? 'invite' : '';
    $disabled = $eUser['term_condition'] == 1 ? "style='display: none'" : "style='display:block' ";
    $activeClass = ($eUser['term_condition'] == 1) ? "active" : "";
    $inActiveClass = ($eUser['term_condition'] == 1) ? "" : "active";
    $submit = !empty($eUser['is_submit']) && $eUser['is_submit'] == 1 ? 'disabled' : '';
    $cell_country_code = '';
    $cell_number = '';
    if (isset($eUser['cell_number'])) {
        $number = explode(")", $eUser['cell_number']);
        if (isset($number[0]) && count($number) > 1) {
            $cell_country_code = explode("+", $number[0]);
            $cell_number = trim($number[1]);
        } else if (count($number) == 1) {
            $cell_number = trim($number[0]);
        } else if (isset($number[1])) {
            $cell_number = trim($number[1]);
        }
        //print_r($cell_country_code);
    }
    $wap_country_code = '';
    $whatsapp_number = '';
    // echo $eUser['whatsapp_num'];
    if (isset($eUser['whatsapp_num'])) {
        $number = explode(")", $eUser['whatsapp_num']);
        if (isset($number[0]) && count($number) > 1) {
            $wap_country_code = explode("+", $number[0]);
            $whatsapp_number = trim($number[1]);
        } else if (count($number) == 1) {
            $whatsapp_number = trim($number[0]);
        } else if (isset($number[1])) {
            $whatsapp_number = trim($number[1]);
        }
        //print_r($wap_country_code);
    }
    $sc_country_code = '';
    $school_contact_number = '';
    // echo $eUser['whatsapp_num'];
    if (isset($eUser['school_contact_number'])) {
        $number = explode(")", $eUser['school_contact_number']);

        if (isset($number[0]) && count($number) > 1) {
            $sc_country_code = explode("+", $number[0]);
            $school_contact_number = trim($number[1]);
        } else if (count($number) == 1) {
            $school_contact_number = trim($number[0]);
        } else if (isset($number[1])) {
            $school_contact_number = trim($number[1]);
        }
    }
    $ec_country_code = '';
    $emergency_home_contact_no = '';
    // echo $eUser['whatsapp_num'];
    if (isset($eUser['emergency_home_contact_no'])) {
        $number = explode(")", $eUser['emergency_home_contact_no']);

        if (isset($number[0]) && count($number) > 1) {
            $ec_country_code = explode("+", $number[0]);
            $emergency_home_contact_no = trim($number[1]);
        } else if (count($number) == 1) {
            $emergency_home_contact_no = trim($number[0]);
        } else if (isset($number[1])) {
            $emergency_home_contact_no = trim($number[1]);
        }
    }
    $ecc_country_code = '';
    $emergency_cell_no = '';
    // echo $eUser['whatsapp_num'];
    if (isset($eUser['emergency_cell_no'])) {
        $number = explode(")", $eUser['emergency_cell_no']);

        if (isset($number[0]) && count($number) > 1) {
            $ecc_country_code = explode("+", $number[0]);
            $emergency_cell_no = trim($number[1]);
        } else if (count($number) == 1) {
            $emergency_cell_no = trim($number[0]);
        } else if (isset($number[1])) {
            $emergency_cell_no = trim($number[1]);
        }
    }
    $dob = array();
    $dod_dd = $dob_yy = $dob_mm = '';
     if (isset($eUser['date_of_birth'])) {
         
         $dob = explode('-', $eUser['date_of_birth']);
     }
     if($isAdmin == 1) {
        $activeClass = "active";
        $inActiveClass = "";
     }
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
    <div class="filterByAjax user-list" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
        <form id="userProfileForm" class="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="table" value="<?php echo $eUser['table_name'] ?>" id="table"/>
            <input type="hidden" name="term_condition" value="<?php echo $eUser['term_condition'] ?>" id="term_condition"/>
            <input type="hidden" name="is_admin" value="<?php echo $isAdmin ?>" id="is_admin"/>
            <input type="hidden" name="is_facilitator" value="<?php echo $isFacilitator ?>" id="is_facilitator"/>
            <div class="row" id="assessmentForm">
                <div class="fl">
                    <h1 class="page-title">
                        <?php
                        $role_ids = explode(',', $eUser['role_ids']);

                        if (!isset($_GET['process'])) {
                             $args = array();
                             
                             if(isset($_REQUEST['source']) && $_REQUEST['source'] == 'user') {
                                 $args = array("controller" => "user", "action" => "user");
                             }else if (current($user['role_ids']) == 8 || current($user['role_ids']) == 1 || current($user['role_ids']) == 2) {
                                $args = array("controller" => "user", "action" => "accessors");
                            }
                                ?>
                                <a href="<?php
                                
                                echo createUrl($args);
                                ?>">
                             
                                    <i class="fa fa-chevron-circle-left vtip" title="Back"></i>

                                </a> 
                                <?php
                            
                        }
                        ?> 

                        <?php echo $eUser['name'] ?> Profile Details


                    </h1>
                </div>
                <h1 class="page-title">  
                    <?php
                    if ($ref == '') {
                        ?>
                        <a href="<?php echo "?controller=user&action=changePassword&id=" . $eUser['user_id'] . "&ispop=1"; ?>" class="btn btn-primary pull-right execUrl vtip" 
                           title="Click to update password." id="addUserBtn" style="margin-left:10px">Change Password</a>

                    <?php }
                    ?>
                    <a href="<?php echo "?controller=user&action=myJourney&id=" . $eUser['user_id'] . "&ispop=1&client_id=" . $_REQUEST['client_id'] . ""; ?>" class="btn btn-primary pull-right vtip" 
                       title="Click for my journey page." id="myjourneyBtn" style = "<?php echo $eUser['term_condition'] == 1 ? 'display:block' : 'display:none' ?>">My Journey</a>
                </h1>
                <div class="clr"></div>
            </div>

            <div id="aqsForm" class="assessorProfile dataChanged">
                <div class="ylwRibbonHldr">
                    <a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab4_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
                    <div class="collapse navbar-collapse tabitemsHldr" id="tab4_Toggle">
                        <ul class="yellowTab nav nav-tabs"> <?php if(isset($isAdmin) && $isAdmin != 1) { ?>
                            <li class="item <?php echo $inActiveClass ?> <?php echo!empty($eUser['term_condition']) && $eUser['term_condition'] == 1 ? 'completed' : ''; ?>" 
                                id="termscondition_tab" style=" display: <?php echo!empty($eUser['term_condition']) && $eUser['term_condition'] == 1 ? 'none' : 'block'; ?>">
                                <a href="#termscondition" data-toggle="tab" class="vtip" title="Terms and Conditions" style="background-color: #9e8323;color: #cdcda0">
                                    Terms and Conditions
                                </a>
                            </li>
                        <?php } ?>
                            <li class="item <?php echo $activeClass ?>" id="pd_personal_tab">
                                <a href="#pd_personal" data-toggle="tab" class="vtip" title="Personal Details">Personal Details</a>
                            </li>
                            <li class="item" id="emergencyContact_tab">
                                <a href="#emergencyContact" data-toggle="tab" class="vtip" title="Emergency Contact">Emergency Contact</a>
                            </li>
                            <li class="item" id="additionalInfo_tab">
                                <a href="#additionalInfo" data-toggle="tab" class="vtip" title="Additional Info">Additional Info</a>
                            </li>
                            <li class="item" id="biography_tab">
                                <a href="#biography" data-toggle="tab" class="vtip" title="Biography">Biography</a>
                            </li>
                            <li class="item <?php echo $inActiveClass ?> <?php echo!empty($eUser['term_condition']) && $eUser['term_condition'] == 1 ? 'completed' : ''; ?>" 
                                id="termscondition_tab_refer" style=" display: <?php echo!empty($eUser['term_condition']) && $eUser['term_condition'] == 1 ? 'block' : 'none'; ?>">
                                <a href="#termscondition" data-toggle="tab" class="vtip" title="Terms and Conditions" style="background-color: #9e8323;color: #cdcda0">
                                    Terms and Conditions
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>  
                <div class="subTabWorkspace pad26">
                    <div class="tab-content">                    
                        <div role="tabpane0" class="tab-pane fade in  <?php echo $inActiveClass ?>" id="termscondition" 
                             <?php // echo !empty($eUser['term_condition']) && $eUser['term_condition']==1?'disabled':'';?>>
                            <!--<h2 class="" style="font-size: 17px;">Terms and Conditions</h2>-->
                            <div class="boxBody">
                                <div class="transLayer">
                                    <div class="tc_wrapper">
                                        <div class="collapse navbar-collapse" id="tab_terms">
                                            <ul class="nav nav-tabs terms"> 
                                                <?php if($noExternal){ ?>
                                                <li class="item <?php echo($noExternal == 1)?'active':'';?>" id="tc_contract_tab">
                                                    <a href="#tc_contract" data-toggle="tab" class="vtip" title="TAP Contract">TAP Contract</a>
                                                </li>
                                                <?php } ?>
                                                <?php if($isFacilitator){ ?>
                                                 <li class="item <?php echo($noExternal == 1)?'':'active';?>" id="fc_contract_tab">
                                                    <a href="#fc_contract" data-toggle="tab" class="vtip" title="TAP Contract">Facilitator Contract</a>
                                                </li>
                                                <?php } ?>
                                                <li class="item" id="tc_agree_tab">
                                                    <a href="#tc_agreement" data-toggle="tab" class="vtip" title="Confidentiality Agreement">Confidentiality Agreement</a>
                                                </li>

                                            </ul>
                                        </div>
                                        <div class="tab-content">
                                            <div role="tabpane0" class="tab-pane fade <?php echo($noExternal == 1)?'in active':'';?>" id="tc_contract">
                                                <div class="conditionsData">
                                                    <p style="font-size: 16px; font-weight: bold;">Adhyayan Assessor Certification Agreement</p>
                                                    <p> You have embarked on an exciting and challenging learning journey to become a Certified Adhyayan Assessor, contributing to your network’s goal of achieving a ‘good’ school for every child. </p>
                                                    <p>
                                                        The Assessor Certification programme is self-paced and learner led, involving field work (participation in reviews), online courses and assessments to progress from Apprentice to Lead/Sr. Associate Assessor level. </p>

                                                    <p> In your professional development journey, you commit to:</p>
                                                    <ul><li style="font-size:13px;">Participate in at least 2 AQS external reviews per academic year fulfilling all associated commitments</li>
                                                        <li style="font-size:13px;">Complete all required online courses and assessments in order to develop to the next stage in the programme</li>
                                                        <li style="font-size:13px;">Actively contribute to support peer learning across the community</li>
                                                        <li style="font-size:13px;">Leading your own professional development journey</li>
                                                    </ul>

                                                    <p>
                                                        Adhyayan is committed to ensuring all developing Assessors have as rich an experience as possible during their development.Therefore, we recommend that you share your academic schedules with us to help identify ideal opportunities for AQS External Review participation. 
                                                    </p>
                                                    <h4>Adhyayan commitment:</h4>
                                                    <p>Adhyayan commits to providing the forum for Assessors’ learning journeys:</p>
                                                    <ul>
                                                        <li style="font-size: 13px;">Access to Adhyayan’s Online Portal</li>
                                                        <li style="font-size: 13px;">Opportunities to participate in field work during AQS External Reviews</li>                                        
                                                        <li style="font-size: 13px;">Mentoring to support you in your professional learning. </li>
                                                    </ul>
                                                    <p><h4>Confidential Information</h4></p>
                                                    <p>You agree that any information received by you during any furtherance of your obligations in accordance with this contract, which concerns the personal, financial or other affairs of the organisation will be treated by you in full confidence and will not be revealed to any other persons, firms or organisations. You shall not share the documents received by you throughout your training with any third parties, even after the expiry of the term of this Agreement.</p>
                                                    <p>All intellectual property created by you and/or in collaboration with Adhyayan or any of its professional partners from the offer of employment will remain the property of Adhyayan. If confidential information has to be copied due to its work requirements, the copies (including but not limited to files, discs, CDs, etc.) are exclusively owned by Adhyayan.</p>
                                                    <p><h4>Validity of Agreement</h4></p>
                                                    <p>The agreement shall be terminated if-</p>
                                                    <ol>
                                                        <li style="font-size: 13px;">You do not fulfil your agreed responsibilities as a developing Assessor participating in the AQS programme.</li>
                                                        <li style="font-size: 13px;">Your association with the school at which you are employed is terminated because you are in breach of contract.</li>
                                                        <li style="font-size: 13px;">You are in breach of the Assessor Code of Conduct (attached)
                                                            (<a href="<?php echo SITEURL . "public/pdf/Adhyayan_Code_of_Conduct.pdf" ?>" target="_blank" style="text-decoration: underline;color: blue !important;">Click Here to download</a>)
                                                        </li>
                                                        <li style="font-size: 13px;">There is a termination request received from your mentor or faculty member with substantiated reasonable evidence of non-performance of duties/non-submission of assignments.</li>
                                                    </ol>
                                                    <br/>
                                                    <p style="text-align: center"><h4>DECLARATION</h4></p>
                                                    
                                                    <p class="contract_value_text">
                                                        <input type="checkbox" name="contract_value[]" value="1" class="contract_value "
                                                        <?php echo!empty($eUser['contract_value']) && in_array(1, $eUser['contract_value']) ? 'checked' : '' ?>
                                                        <?php
                                                        if ($eUser['term_condition'] == 1) {
                                                            if (in_array(8, $user['role_ids'])) {
                                                                echo 'disabled';
                                                            } else {
                                                                if (!empty($eUser['contract_value'])) {
                                                                    echo 'disabled';
                                                                }
                                                            }
                                                        }
                                                        ?>/>  
                                                        I agree to the terms and conditions as set out in the above document. 
                                                    </p>
                                                    <p class="contract_value_text">
                                                        <input type="checkbox" name="contract_value[]" value="2" class="contract_value "
                                                        <?php echo!empty($eUser['contract_value']) && in_array(2, $eUser['contract_value']) ? 'checked' : '' ?>
                                                        <?php
                                                        if ($eUser['term_condition'] == 1) {
                                                            if (in_array(8, $user['role_ids'])) {
                                                                echo 'disabled';
                                                            } else {
                                                                if (!empty($eUser['contract_value'])) {
                                                                    echo 'disabled';
                                                                }
                                                            }
                                                        }
                                                        ?>/> 

                                                        I have read and agree to uphold the terms of the Assessor Code of Conduct.
                                                    </p>
                                                    <p class="contract_value_text">
                                                        <input type="checkbox" name="contract_value[]" value="3" class="contract_value "
                                                        <?php echo!empty($eUser['contract_value']) && in_array(3, $eUser['contract_value']) ? 'checked' : '' ?>
                                                        <?php
                                                        if ($eUser['term_condition'] == 1) {
                                                            if (in_array(8, $user['role_ids'])) {
                                                                echo 'disabled';
                                                            } else {
                                                                if (!empty($eUser['contract_value'])) {
                                                                    echo 'disabled';
                                                                }
                                                            }
                                                        }
                                                        ?>/> 

                                                        I will ensure I abide by Adhyayan's Safeguarding & Child Protection Policy.(<a href="<?php echo SITEURL . "public/pdf/Safeguarding-and-Child-Protection-Policy.pdf" ?>" target="_blank" style="text-decoration: underline;color: blue !important;">Click Here to download</a>)
                                                    </p>
                                                    <p class="contract_value_text">
                                                        <input type="checkbox" name="contract_value[]" value="4" class="contract_value "
                                                        <?php echo!empty($eUser['contract_value']) && in_array(4, $eUser['contract_value']) ? 'checked' : '' ?>
                                                        <?php
                                                        if ($eUser['term_condition'] == 1) {
                                                            if (in_array(8, $user['role_ids'])) {
                                                                echo 'disabled';
                                                            } else {
                                                                if (!empty($eUser['contract_value'])) {
                                                                    echo 'disabled';
                                                                }
                                                            }
                                                        }
                                                        ?>/> 

                                                        I have obtained permission from the school management to undertake Professional Development with Adhyayan.
                                                    </p>

                                                    <!--I will ensure I abide by Adhyayan's Safeguarding & Child Protection Policy.
                                                    --> </p>
                                                    <p class="contract_value_text">
                                                        <input type="checkbox" name="contract_value[]" value="5" class="contract_value "
                                                        <?php echo!empty($eUser['contract_value']) && in_array(5, $eUser['contract_value']) ? 'checked' : '' ?>
                                                        <?php
                                                        if ($eUser['term_condition'] == 1) {
                                                            if (in_array(8, $user['role_ids'])) {
                                                                echo 'disabled';
                                                            } else {
                                                                if (!empty($eUser['contract_value'])) {
                                                                    echo 'disabled';
                                                                }
                                                            }
                                                        }
                                                        ?>/> 

                                                        I consent to Adhyayan recording my Assesor journey and good practices from my school, and sharing this in their publications.
                                                    </p>
                                                </div>
                                            </div>
                                             <div role="tabpane0" class="tab-pane fade <?php echo($noExternal == 1)?'':'in active';?>" id="fc_contract">
                                                <div class="conditionsData">
                                                    <p style="font-size: 16px; font-weight: bold;">ASIST Facilitator Contract</p>
                                                    <p> 
We would like to welcome you to the Adhyayan School Improvement Support &Training (ASIST) Facilitator Network. You have been invited into our network to facilitate ASIST workshops that have been designed to support schools in their quality improvement journeys. </p>
                                                  
                                                    <p> As an ASIST Facilitator, you commit to:</p>
                                                    <ul><li style="font-size:13px;">Attend 2 ASIST Trainer Network seminars per year</li>
                                                        <li style="font-size:13px;">Liaise with the relevant Adhyayan team member regarding the workshops you facilitate in advance of the workshop date.</li>
                                                        <li style="font-size:13px;">Facilitate ASIST workshops as an Adhyayan representative, promoting the organisation’s values as outlined in Adhyayan’s Code of Conduct. </li>
                                                        <li style="font-size:13px;">You will be asked to contribute to the development of the workshop you will be facilitating. You agree to complying with the Adhyayan Workshop Framework and supporting the Adhyayan team with this.</li>
                                                        <li style="font-size:13px;">Provide a report using Adhyayan’s template, on the impact of the ASIST workshop you have facilitated.</li>
                                                    </ul>
                                                    
                                                    <p><h4>Remuneration</h4></p>
                                                    <p>
                                                        The remuneration you receive per workshop will be dependent on the number of participants, the content of the workshop, and your contribution to its creation. You will be informed of this amount by the responsible Adhyayan team member when you are contacted to facilitate the workshop. Payments will be made to you upon completion of this report and receipt by the Adhyayan team, which will be expected within 10 days of conducting the ASIST workshop.
                                                    </p>
                                                    
                                                    <p><h4>Tax Element</h4></p>
                                                    <p>TDS will be deducted as applicable.</p>
                                                    <p><h4>Expenses</h4></p>
                                                    <p>Where required, Adhyayan will provide you with food, accommodation and travel. </p>
                                                    <ul>
                                                        <li><span style="font-size: 14px;">Local travel:</span> For local travel, an expenses form provided by Adhyayan must be submitted to the Adhyayan team within 10 days of the workshop. Rickshaw/taxi fares within Mumbai will be reimbursed by Adhyayan upon the submission of an expenses form.</li>
                                                        <li><span style="font-size: 14px;">Outstation travel:</span> Where you are asked to travel outstation to conduct a workshop on behalf of Adhyayan, your travel will be arranged for by the Adhyayan core team. </li>                                        
                                                        <li><span style="font-size: 14px;">Food:</span> Your meals will be provided by the school when conducting an outstation workshop for Adhyayan. </li>
                                                    </ul>
                                                    <p><h4>Confidential Information</h4></p>
                                                    <p>You agree that any information received by you during any furtherance of your obligations in accordance with this contract, which concerns the personal, financial or other affairs of the organisation will be treated by you in full confidence and will not be revealed to any other persons, firms or organisations.</p>
                                                    <p>All intellectual property created by you and/or in collaboration with Adhyayan or any of its professional partners from the offer of employment will remain the property of Adhyayan.</p>
                                                    <p><h4>Protocol to be followed</h4></p>
                                                    <p>Please note that during the time you are representing Adhyayan although you are not an employee, you are duty bound to represent our value system as per the code of conduct attached. </p>
                                                    <p>As a representative of Adhyayan, you are expected to refrain from: </p>
                                                    <ul>
                                                        <li style="font-size: 13px;">Soliciting work as an external agency or consultant.</li>
                                                        <li style="font-size: 13px;">Draw comparisons with your school / organization (“this is how we do things in my school”). </li>                                        
                                                        <li style="font-size: 13px;">Suggest in any manner that the earlier consultant had provided “wrong” information – often the teachers in the school may present their version of what the earlier facilitator had said, which may be a complete misinterpretation. In the circumstances, it is best to ask the participants what they are doing and whether they have a rationale for it. Always recommend strongly that teachers should not ‘follow’ anyone but should operate from their own convictions. </li>
                                                    </ul>
                                                    <p><h4>Validity of Agreement</h4></p>
                                                    <p>The agreement shall be terminated if-</p>
                                                    <ol>
                                                        <li style="font-size: 13px;">You do not fulfil your agreed responsibilities as a developing Facilitator participating in the ASIST/Facilitator programme.</li>
                                                        <li style="font-size: 13px;">Your association with the school at which you are employed is terminated because you are in breach of contract.</li>
                                                        <li style="font-size: 13px;">You are in breach of the Facilitator Code of Conduct (attached)
                                                            (<a href="<?php echo SITEURL . "public/pdf/Adhyayan_Code_of_Conduct.pdf" ?>" target="_blank" style="text-decoration: underline;color: blue !important;">Click Here to download</a>)
                                                        </li>
                                                        <li style="font-size: 13px;">There is a termination request received from your mentor or faculty member with substantiated reasonable evidence of non-performance of duties/non-submission of assignments.</li>
                                                    </ol>
                                                    
                                                    <br/>
                                                    <p style="text-align: center"><h4>DECLARATION</h4></p>
                                                    
                                                    <p class="contract_value_text_fc">
                                                        <input type="checkbox" name="contract_value_fc[]" value="6" class="contract_value "
                                                        <?php echo!empty($eUser['contract_value']) && in_array(6, $eUser['contract_value']) ? 'checked' : '' ?>
                                                        <?php
                                                        if ($eUser['term_condition'] == 1) {
                                                            if (in_array(8, $user['role_ids'])) {
                                                                echo 'disabled';
                                                            } else {
                                                                if (!empty($eUser['contract_value'])) {
                                                                    echo 'disabled';
                                                                }
                                                            }
                                                        }
                                                        ?>/>  
                                                       I agree to the terms and conditions as set out in the above document.
                                                    </p>
                                                    <p class="contract_value_text_fc">
                                                        <input type="checkbox" name="contract_value_fc[]" value="7" class="contract_value "
                                                        <?php echo!empty($eUser['contract_value']) && in_array(7, $eUser['contract_value']) ? 'checked' : '' ?>
                                                        <?php
                                                        if ($eUser['term_condition'] == 1) {
                                                            if (in_array(8, $user['role_ids'])) {
                                                                echo 'disabled';
                                                            } else {
                                                                if (!empty($eUser['contract_value'])) {
                                                                    echo 'disabled';
                                                                }
                                                            }
                                                        }
                                                        ?>/> 

                                                        I have read and agree to uphold the terms of the Facilitator Code of Conduct.
                                                    </p>
                                                    <p class="contract_value_text_fc">
                                                        <input type="checkbox" name="contract_value_fc[]" value="8" class="contract_value "
                                                        <?php echo!empty($eUser['contract_value']) && in_array(8, $eUser['contract_value']) ? 'checked' : '' ?>
                                                        <?php
                                                        if ($eUser['term_condition'] == 1) {
                                                            if (in_array(8, $user['role_ids'])) {
                                                                echo 'disabled';
                                                            } else {
                                                                if (!empty($eUser['contract_value'])) {
                                                                    echo 'disabled';
                                                                }
                                                            }
                                                        }
                                                        ?>/> 

                                                        I will ensure I abide by Adhyayan's Safeguarding & Child Protection Policy.(<a href="<?php echo SITEURL . "public/pdf/Safeguarding-and-Child-Protection-Policy.pdf" ?>" target="_blank" style="text-decoration: underline;color: blue !important;">Click Here to download</a>)
                                                    </p>
                                                    <p class="contract_value_text_fc">
                                                        <input type="checkbox" name="contract_value_fc[]" value="9" class="contract_value "
                                                        <?php echo!empty($eUser['contract_value']) && in_array(9, $eUser['contract_value']) ? 'checked' : '' ?>
                                                        <?php
                                                        if ($eUser['term_condition'] == 1) {
                                                            if (in_array(8, $user['role_ids'])) {
                                                                echo 'disabled';
                                                            } else {
                                                                if (!empty($eUser['contract_value'])) {
                                                                    echo 'disabled';
                                                                }
                                                            }
                                                        }
                                                        ?>/> 

                                                        I have obtained permission from the school management to undertake Professional Development with Adhyayan.
                                                    </p>

                                                    <!--I will ensure I abide by Adhyayan's Safeguarding & Child Protection Policy.
                                                    --> </p>
                                                    <p class="contract_value_text_fc">
                                                        <input type="checkbox" name="contract_value_fc[]" value="10" class="contract_value "
                                                        <?php echo!empty($eUser['contract_value']) && in_array(10, $eUser['contract_value']) ? 'checked' : '' ?>
                                                        <?php
                                                        if ($eUser['term_condition'] == 1) {
                                                            if (in_array(8, $user['role_ids'])) {
                                                                echo 'disabled';
                                                            } else {
                                                                if (!empty($eUser['contract_value'])) {
                                                                    echo 'disabled';
                                                                }
                                                            }
                                                        }
                                                        ?>/> 

                                                         I consent to Adhyayan recording my Facilitator journey and good practices from my school and sharing this in their publications.
                                                    </p>
                                                </div>
                                            </div>
                                            <div role="tabpane1" class="tab-pane fade" id="tc_agreement">
                                                <div class="termsData">
                                                    <p>
                                                        This Confidentiality Agreement (the "Agreement") is dated the <b><?php echo date('d F, Y') ?></b>,
                                                        and is by and between <b>Adhyayan Quality Education Services Pvt. Ltd.</b> and <b><?php echo $eUser['client_name'] ?></b>
                                                        (the "Receiving Party").
                                                    </p>
                                                    <p>
                                                        WHEREAS Receiving Party has requested access to certain information and materials that
                                                        are nonpublic, confidential, and proprietary in nature ("Confidential Information") from Adhyayan; 
                                                        and Adhyayan is willing to disclose such nonpublic, confidential, and proprietary information and 
                                                        materials to Receiving Party only in exchange for commitments of confidentiality, as set forth below.
                                                    </p>
                                                    <p>
                                                        Confidential Information includes all information that Adhyayan considers confidential or 
                                                        proprietary information of Adhyayan or third-party sources, regardless of whether such information 
                                                        is marked as such by related information.
                                                    </p>
                                                    <p>
                                                        The Receiving Party covenants and agrees not to disclose or permit to be disclosed any
                                                        Confidential Information and that the Receiving Party will not appropriate, copy, reproduce, or in 
                                                        any fashion replicate any Confidential Information without the prior written consent of Adhyayan.
                                                        The Receiving Party agrees that any disclosure of Confidential Information in violation of this 
                                                        Agreement would cause immediate and substantial damage to Adhyayan and to any parties that 
                                                        provided Confidential Information to Adhyayan. The Receiving Party agrees to use all reasonable
                                                        effort to maintain the confidentiality of the Confidential Information and agrees not to disclose the
                                                        Confidential Information obtained from Adhyayan unless required to do so by law. The Receiving
                                                        Party agrees not to use any Confidential Information for its own benefit or that of a third party unless
                                                        authorized in advance in writing by Adhyayan. Confidential Information shall not include
                                                        information that enters the public domain through no fault of the Receiving Party or which the
                                                        Receiving Party rightfully obtains from a third party without comparable restrictions on disclosure or
                                                        use.
                                                    </p>
                                                    <p>
                                                        Any addition or modification to this Agreement must be made in writing and signed by the 
                                                        Parties. If any provisions of this Agreement are found to be unenforceable, the remainder shall be
                                                        enforced as fully as possible and the unenforceable provision(s) shall be deemed modified to the
                                                        limited extent required to permit enforcement of the Agreement as a whole. This Agreement may be
                                                        executed in any number of counterparts, all of which taken together will constitute one and the same
                                                        agreement, and the Parties hereto may execute this Agreement by signing such counterpart.
                                                    </p>
                                                    <p>
                                                        WHEREFORE the Parties acknowledge that they have read and understand this Agreement and
                                                        voluntarily accept the duties and obligations set forth herein.
                                                    </p>

                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>							
                            </div>	
                        </div>

                        <div role="tabpane0" class="tab-pane fade in <?php echo $activeClass ?> " id="pd_personal">
                            <input type="hidden" name="id" value="<?php echo $eUser['user_id'] ?>" id="id"/>
                            <input type="hidden" name="client_id" value="<?php echo $eUser['client_id'] ?>" id="client_id"/>
                            <div class="row mb30">
                                <div class="col-sm-6">
                                    <?php if (in_array("manage_all_users", $user['capabilities']) && in_array("edit_all_submitted_assessments", $user['capabilities'])) { ?>	

                                        <dl class="fldList">
                                            <input type="hidden" name="login_user_id" id="login_user_id" value="<?php echo current($user['role_ids']) ?>"/>
                                            <dt>School/College:</dt>
                                            <dd>

                                                <?php
                                                $span = $eUser['client_name'];
                                                ;
                                                $value = $eUser['client_id'];
                                                $labal = 'Change School/College';
                                                ?>
                                                <span id="selected_client_name"><?php echo $span; ?></span> &nbsp;
                                                <a href="?controller=client&action=clientList" data-postformid="for" class="btn btn-danger vtip execUrl mb0" title="Click to select a school." data-size="1050" id="selectClientBtn"><?php echo $labal ?></a>
                                                <input type="hidden" autocomplete="off" name="client_id" id="selected_client_id" value="<?php echo $value; ?>" />

                                            </dd>
                                        </dl>
                                    <?php } else {
                                        ?>
                                        <input type="hidden" value="<?php echo $eUser['client_id']; ?>" name="client_id" id="selected_client_id" />
                                        <?php }
                                    ?>	
                                    <!-- <dl class="fldList">
                                         <dt>School Name:</dt>
                                         <dd><?php //echo $eUser['client_name']  ?></dd>
                                     </dl>-->
                                    <?php
                                    $name = explode(' ', $eUser['name']);
                                    ?>
                                    <dl class="fldList">
                                        <dt>First Name:</dt>
                                        <dd>
                                            <input type="text" class="form-control"  value="<?php echo array_shift($name); ?>" name="first_name" id="first_name"
                                                   onkeypress="return isLetterKey(event)">
                                        </dd>
                                    </dl>
                                    <dl class="fldList">
                                        <dt>Last Name:</dt>
                                        <dd>
                                            <input type="text" class="form-control"  value="<?php echo implode(' ', $name); ?>" name="last_name" id="last_name"
                                                   onkeypress="return isLetterKey(event)">
                                        </dd>
                                    </dl>
                                    <dl class="fldList">
                                        <dt>Gender:</dt>
                                        <dd>
                                            <div class="clearfix advInfo">
                                                <div class="chkHldr">
                                                    <input type="radio" name="gender" id="gender" class="radioClass"  value="Male"
                                                           <?php echo!empty($eUser['gender']) && $eUser['gender'] == 'Male' ? 'checked' : '' ?>>
                                                    <label class="chkF radio"><span>Male</span></label>
                                                </div>
                                                <div class="chkHldr">
                                                    <input type="radio" name="gender" id="gender" class="radioClass"  value="Female"
                                                           <?php echo!empty($eUser['gender']) && $eUser['gender'] == 'Female' ? 'checked' : '' ?>>
                                                    <label class="chkF radio"><span>Female</span></label>
                                                </div>
                                            </div>
                                        </dd>
                                    </dl>
                                    <dl class="fldList">
                                        <dt>Date of Birth:</dt>
                                        <dd>

                                            <div class="inlContBox" id="user_dob">
                                                
                                              
                                                <div class="inlCBItm">
                                                    <div class="fld blk">
                                                        <div>
                                                             <select name="dob_dd" id="dob_dd" class="form-control" >
                                                                <option value="">DD</option>
                                                            <?php for($i=1;$i<=31;$i++) { ?>
                                                                <option value="<?php echo $i;?>" <?php echo (isset($dob[2]) && $dob[2] == $i)?'selected="selected"':'';?>><?php echo $i;?></option>
                                                            <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                  <div class="inlCBItm">
                                                    <div class="fld blk">
                                                        <div>
                                                             <select name="dob_mm" id="dob_mm" class="form-control" >
                                                                 <option value="">MM</option>
                                                            <?php for($i=1;$i<=12;$i++) {  ?>
                                                              <option value="<?php echo $i;?>" <?php echo (isset($dob[1]) && $dob[1] == $i)?'selected="selected"':'';?>><?php  echo $i;?></option>
                                                           <?php  } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="inlCBItm">
                                                    <div class="fld blk">
                                                        <select name="dob_yy" id="dob_yy" class="form-control" >
                                                            <option value="">YYYY</option>
                                                       <?php for($i=1950;$i<=date('Y')-1;$i++) { ?>
                                                            <option value="<?php echo $i;?>" <?php echo (isset($dob[0]) && $dob[0] == $i)?'selected="selected"':'';?>><?php echo $i;?></option>
                                                       <?php } ?>
                                                       </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!--<input type="text" class="form-control " placeholder="DD-MM-YYYY"   name="date_of_birth" id="date_of_birth"
                                                       value="<?php //echo!empty($eUser['date_of_birth']) && $eUser['date_of_birth'] != '0000-00-00' ? date("d-m-Y", strtotime($eUser['date_of_birth'])) : '' ?>">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>-->
                                            
                                        </dd>
                                    </dl>
                                    <dl class="fldList">
                                        <dt>Email address:</dt>
                                        <dd class="email">
                                            <?php
                                            if ($eUser['email'] != '') {
                                                // echo $eUser['email'];
                                                echo '<input type="text" class="form-control" readable  name="email" id="email" value="' . $eUser['email'] . '">';
                                            } else {
                                                echo '<input type="email" class="form-control" readable  name="email" id="email">';
                                            }
                                            ?>
                                        </dd>
                                    </dl>
                                    <?php
                                    if ($ref == 'invite') {
                                        ?>
                                        <dl class="fldList">
                                            <dt>Password:</dt>
                                            <dd><input type="password" class="form-control"  name="password" id="password"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Confirm Password:</dt>
                                            <dd><input type="password" class="form-control"  name="confirm_password" id="confirm_password"></dd>
                                        </dl>   
                                        <?php
                                    } else {
                                        ?>
                                        <dl class="fldList">
                                            <dt>Designation:</dt>
                                            <dd><input type="text" class="form-control"  name="designation" id="designation"
                                                       value="<?php echo!empty($eUser['designation']) ? $eUser['designation'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Work Experience:</dt>
                                            <dd>
                                                <div class="chkHldr" style="width: 100px;">
                                                    <input type="radio" name="work_experience" id="work_experience" class="radioClass"  value="1"
                                                           <?php echo!empty($eUser['work_experience']) && $eUser['work_experience'] == '1' ? 'checked' : '' ?>>
                                                    <label class="chkF radio"><span><1 year</span></label>
                                                </div>
                                                <div class="chkHldr" style="width: 100px;">
                                                    <input type="radio" name="work_experience" id="work_experience" class="radioClass"  value="3"
                                                           <?php echo!empty($eUser['work_experience']) && $eUser['work_experience'] == '3' ? 'checked' : '' ?>>
                                                    <label class="chkF radio"><span>1 - 3 years</span></label>
                                                </div>
                                                <div class="chkHldr" style="width: 90px;">
                                                    <input type="radio" name="work_experience" id="work_experience" class="radioClass"  value="4+"
                                                           <?php echo!empty($eUser['work_experience']) && $eUser['work_experience'] == '4+' ? 'checked' : '' ?>>
                                                    <label class="chkF radio"><span>4+ years</span></label>
                                                </div>
                                            </dd>
                                            <input type="hidden" class="form-control"  name="password" id="password">
                                            <input type="hidden" class="form-control"  name="confirm_password" id="confirm_password">
                                        </dl>  


                                        <?php
                                        if ($eUser['table_name'] == 'd_user') {
                                            $user_roles = explode(',', $eUser['role_ids']);
                                            if (in_array(1, $user ['role_ids'])) {
                                                $superRoleId = 1;
                                            } else {
                                                $superRoleId = 2;
                                            }
                                            //print_r($user_roles);

                                            if (in_array("manage_all_users", $user['capabilities']) && !in_array(8, $user ['role_ids'])) {
                                                ?>
                                                <dl class="fldList">
                                                    <dt>
                                                        User Role<span class="astric">*</span>:
                                                    </dt>
                                                    <dd>
                                                        <div class="clearfix">
                                                            <?php
                                                            $disabled = '';
                                                            //echo "kkk";
                                                            //print_r($user_roles);
                                                            foreach ($roles as $role) {
                                                                // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar
                                                                $disabled = '';
                                                                if (in_array(8, $user_roles)) {
                                                                    if ((!in_array('1', $user['role_ids']))) {
                                                                        $disabled = "disabled=''";
                                                                    }
                                                                } else {
                                                                    if ($role['role_id'] == 8 && (!in_array('1', $user['role_ids']))) {
                                                                        $disabled = "disabled=''";
                                                                    }
                                                                }
                                                                echo "<div class=\"chkHldr\"><input type=\"checkbox\" " . (in_array($role ['role_id'], $user_roles) ? 'checked="checked"' : "") . " class=\"user-roles\" name=\"roles[]\" value=\"" . $role ['role_id'] . "\" id='role_id_" . $role['role_id'] . "' onclick='checkboxEnableDisable(\"role_id_\"," . $role['role_id'] . ",\"user-roles\"," . $superRoleId . ")' " . $disabled . "><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";
                                                            }
                                                            ?>
                                                        </div>
                                                    </dd>
                                                </dl>
                                                <?php
                                            } else if (in_array("manage_own_users", $user ['capabilities']) && in_array(6, $user ['role_ids']) && !in_array(8, $user ['role_ids'])) {
                                                ?>
                                                <dl class="fldList">
                                                    <dt>
                                                        User Role<span class="astric">*</span>:
                                                    </dt>
                                                    <dd>
                                                        <div class="clearfix">
                                                            <?php
                                                            $disabled = '';
                                                            //echo "ddd";
                                                            // school principal is able to add internal reviewer and school admin only
                                                            foreach ($roles as $role) {
                                                                // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar
                                                                if (in_array(8, $user_roles)) {
                                                                    if ((!in_array('1', $user['role_ids']))) {
                                                                        $disabled = "disabled=''";
                                                                    }
                                                                } else {
                                                                    if ($role['role_id'] == 8 && (!in_array('1', $user['role_ids']))) {
                                                                        $disabled = "disabled=''";
                                                                    }
                                                                }
                                                                if (in_array(6, $user_roles) && $role ['role_id'] == 6) {
                                                                    echo in_array($role ['role_id'], array(3, 5, 6)) ? "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array($role ['role_id'], $user_roles) ? 'checked="checked"' : "") . ( $role ['role_id'] == 6 ? 'disabled="disabled"' : "") . " name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_" . $role['role_id'] . "' onclick='checkboxEnableDisable(\"role_id_\"," . $role['role_id'] . ",\"user-roles\"," . $superRoleId . ")' " . $disabled . "><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n" : '';
                                                                    echo "<input type='hidden' name=\"roles[]\" value='6' />";
                                                                } else {
                                                                    if (in_array($role ['role_id'], array(3, 5))) {
                                                                        echo "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array($role ['role_id'], $user_roles) ? 'checked="checked"' : "") . " name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_" . $role['role_id'] . "' onclick='checkboxEnableDisable(\"role_id_\"," . $role['role_id'] . ",\"user-roles\"," . $superRoleId . ")' " . $disabled . "><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";
                                                                    } else if (in_array($role ['role_id'], $user_roles)) {
                                                                        $disabled_etc = "disabled=''";
                                                                        echo "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array($role ['role_id'], $user_roles) ? 'checked="checked"' : "") . " name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_" . $role['role_id'] . "' onclick='checkboxEnableDisable(\"role_id_\"," . $role['role_id'] . ",\"user-roles\"," . $superRoleId . ")' " . $disabled_etc . "><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";
                                                                    }
                                                                    //echo in_array ( $role ['role_id'], array(3,5)) ? "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") ." name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n" : '';
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    </dd>
                                                </dl>
                <?php
            } else if (in_array("manage_own_users", $user ['capabilities']) && in_array(7, $user ['role_ids']) && !in_array(8, $user ['role_ids'])) {
                if ($eUser['user_id'] != $user['user_id']) {
                    ?>
                                                    <dl class="fldList">
                                                        <dt>
                                                            User Role<span class="astric">*</span>:
                                                        </dt>
                                                        <dd>
                                                            <div class="clearfix">
                    <?php
                    $disabled = '';
                    // school principal is able to add internal reviewer and school admin only
                    foreach ($roles as $role) {
                        // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar
                        if (in_array(8, $user_roles)) {
                            if ((!in_array('1', $user['role_ids']))) {
                                $disabled = "disabled=''";
                            }
                        } else {
                            if ($role['role_id'] == 8 && (!in_array('1', $user['role_ids']))) {
                                $disabled = "disabled=''";
                            }
                        }
                        if (in_array(7, $user_roles) && $role ['role_id'] == 7) {
                            echo in_array($role ['role_id'], array(5, 6, 7)) ? "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array($role ['role_id'], $user_roles) ? 'checked="checked"' : "") . ( $role ['role_id'] == 7 ? 'disabled="disabled"' : "") . " name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_" . $role['role_id'] . "' onclick='checkboxEnableDisable(\"role_id_\"," . $role['role_id'] . ",\"user-roles\"," . $superRoleId . ")' " . $disabled . "><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n" : '';
                            echo "<input type='hidden' name=\"roles[]\" value='7' />";
                        } else {
                            //echo in_array ( $role ['role_id'], array(3,5,6)) ? "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array ( $role ['role_id'], $eUser ['role_ids'] ) ? 'checked="checked"' : "") ." name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_".$role['role_id']."' onclick='checkboxEnableDisable(\"role_id_\",".$role['role_id'].",\"user-roles\",".$superRoleId.")' ".$disabled."><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n" : '';
                            if (in_array($role ['role_id'], array(3, 5, 6))) {
                                echo "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array($role ['role_id'], $user_roles) ? 'checked="checked"' : "") . " name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_" . $role['role_id'] . "' onclick='checkboxEnableDisable(\"role_id_\"," . $role['role_id'] . ",\"user-roles\"," . $superRoleId . ")' " . $disabled . "><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";
                            } else if (in_array($role ['role_id'], $user_roles)) {
                                $disabled_etc = "disabled=''";
                                echo "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array($role ['role_id'], $user_roles) ? 'checked="checked"' : "") . " name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_" . $role['role_id'] . "' onclick='checkboxEnableDisable(\"role_id_\"," . $role['role_id'] . ",\"user-roles\"," . $superRoleId . ")' " . $disabled_etc . "><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";
                            }
                        }
                    }
                    ?>
                                                            </div>
                                                        </dd>
                                                    </dl>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                <dl class="fldList">
                                                    <dt>
                                                        User Role<span class="astric">*</span>:
                                                    </dt>
                                                    <dd>
                                                        <div class="clearfix">
                <?php
                $disabled = '';
                // print_r($roles);
                // school principal is able to add internal reviewer and school admin only
                foreach ($roles as $role) {
                    // add id and onlick event into input field for enabling and disabling the checkboxes and call the onclick event on 12-05-2016 by Mohit Kumar


                    $disabled_etc = "disabled=''";
                    if (in_array($role ['role_id'], $user_roles)) {
                        echo '<input type="hidden" name="roles[]" value="' . $role ['role_id'] . '" class=" user-roles" >';
                    }
                    echo "<div class=\"chkHldr\" style='margin-top:8px;' ><input type=\"checkbox\" class=\"user-roles\"" . (in_array($role ['role_id'], $user_roles) ? 'checked="checked"' : "") . " name=\"roles[]\" autocomplete=\"off\" value=\"" . $role ['role_id'] . "\" id='role_id_" . $role['role_id'] . "' onclick='checkboxEnableDisable(\"role_id_\"," . $role['role_id'] . ",\"user-roles\"," . $superRoleId . ")' " . $disabled_etc . "><label class=\"chkF checkbox\"><span>" . $role ['role_name'] . "</span></label></div>\n";
                }
                ?>
                                                        </div>
                                                    </dd>
                                                </dl>
                                                        <?php
                                                        }
                                                    }
                                                    ?>
                                        <!--<dl class="fldList">
                                            <dt>User Role:</dt>
                                            <dd>
                                                <div class="clearfix advInfo">
                                                   
                                        <?php
                                        if ($eUser['table_name'] == 'd_user') {
                                            $roles = explode(',', $eUser['roles']);

                                            for ($i = 0; $i < count($roles); $i++) {
                                                ?>
                                                                    <div class="chkHldr">
                                                                        <input type="checkbox" name="roles[]" value="<?php echo $role_ids[$i] ?>" class=" user-roles" >
                                                                        <label class="chkF checkbox"><span style="margin-left: -27px;"><?php echo $roles[$i] ?></span></label>
                                                                    </div> 
                                                <?php
                                            }
                                        } else if ($eUser['table_name'] == 'd_aqs_team') {
                                            ?>
                                                            <div class="chkHldr">
                                                                <input type="checkbox" name="roles[]" value="4" class=" user-roles"  checked>
                                                                <label class="chkF checkbox"><span>External reviewer</span></label>                                                    
                                                            </div> 
            <?php
        }
        ?>
                                                </div>
                                            </dd>
                                        </dl>-->
        <?php
    }
    ?>


                                </div>
                                    <?php
                                    if ($ref == '') {
                                        ?>
                                    <div class="col-sm-6">
                                        <dl class="fldList">
                                            <dt>Address:</dt>
                                            <dd><input type="text" class="form-control"  name="address" id="address"
                                                       value="<?php echo!empty($eUser['address']) ? $eUser['address'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Town:</dt>

                                            <dd><input type="text" class="form-control"  name="town" id="town" onkeypress="return isLetterKey(event)"
                                                       value="<?php echo!empty($eUser['town']) ? $eUser['town'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>State:</dt>
                                            <dd>
                                                <select name="state_id" id="state_id" class="form-control" >
                                                    <option value="">--Select State--</option>
        <?php
        foreach ($stateList as $value) {
            ?>
                                                        <option value="<?php echo $value['state_id'] ?>"
            <?php echo!empty($eUser['state_id']) && $eUser['state_id'] == $value['state_id'] ? 'Selected' : '' ?>>
            <?php echo $value['state_name'] ?></option>
            <?php
        }
        ?>
                                                </select>
                                            </dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Pin code:</dt>
                                            <dd>
                                                <input type="text" class="form-control"  name="pincode" id="pincode" onkeypress="return isNumberKey(event)"
                                                       value="<?php echo!empty($eUser['pincode']) ? $eUser['pincode'] : '' ?>" maxlength="6">
                                            </dd>
                                        </dl>
                                        <dl class="fldList" style=" display: <?php echo ($isSelfReview == 1) ? 'none' : 'block'; ?>">
                                            <dt>Cell no:</dt>
                                            <dd>
                                                <div class="inlContBox ftySixty">
                                                    <div class="inlCBItm fty">
                                                        <div class="fld blk">
                                                            <select name="cell_country_code" id="cell_country_code" class="form-control" >
        <?php
        foreach ($countryCodeList as $value) {
            ?>
                                                                    <option value="<?php echo $value['phonecode'] ?>"
            <?php echo!empty($cell_country_code[1]) && $cell_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
            <?php echo "(+" . $value['phonecode'] . ") "; ?></option>
            <?php
        }
        ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="inlCBItm sixty">
                                                        <div class="fld">
                                                            <div>
                                                                <input type="text" class="form-control mask_ph "  name="cell_number" id="cell_number" value="<?php echo!empty($cell_number) ? str_replace("-", '', $cell_number) : '' ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>

                                        <dl class="fldList">
                                            <dt>WhatsApp Number:
                                                <i class="fa fa-info-circle vtip" title="If different to your Cell Number."></i>
                                            </dt>
                                            <dd>
                                                <div class="inlContBox ftySixty">
                                                    <div class="inlCBItm fty">
                                                        <div class="fld blk">
                                                            <select name="wap_country_code" id="wap_country_code" class="form-control" >
        <?php
        foreach ($countryCodeList as $value) {
            ?>
                                                                    <option value="<?php echo $value['phonecode'] ?>"
            <?php echo!empty($wap_country_code[1]) && $wap_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
            <?php echo "(+" . $value['phonecode'] . ") "; ?></option>
            <?php
        }
        ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="inlCBItm sixty">
                                                        <div class="fld">
                                                            <div>
                                                                <input type="text" class="form-control mask_ph "  name="whatsapp_num" id="whatsapp_num" value="<?php echo!empty($whatsapp_number) ? str_replace("-", '', $whatsapp_number) : '' ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>School contact no:</dt>
                                            <dd>
                                                <div class="inlContBox ftySixty">
                                                    <div class="inlCBItm fty">
                                                        <div class="fld blk">
                                                            <select name="sc_country_code" id="wap_country_code" class="form-control" >
        <?php
        foreach ($countryCodeList as $value) {
            ?>
                                                                    <option value="<?php echo $value['phonecode'] ?>"
            <?php echo!empty($sc_country_code[1]) && $sc_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
            <?php echo "(+" . $value['phonecode'] . ") "; ?></option>
            <?php
        }
        ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="inlCBItm sixty">
                                                        <div class="fld">
                                                            <div>
                                                                <input type="text" class="form-control mask_ph "  name="school_contact_number" id="school_contact_number" value="<?php echo!empty($school_contact_number) ? str_replace("-", '', $school_contact_number) : '' ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Personal PAN Number:</dt>
                                            <dd><input type="text" class="form-control"  name="personal_pan_number" id="personal_pan_number"
                                                       value="<?php echo!empty($eUser['personal_pan_number']) ? $eUser['personal_pan_number'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Aadhaar Card Number:</dt>
                                            <dd><input type="text" class="form-control"  name="aadhar_number" id="aadhar_number"
                                                       value="<?php echo!empty($eUser['aadhar_number']) ? $eUser['aadhar_number'] : '' ?>"></dd>
                                        </dl>
                                        <?php if($isAdmin) { ?>
                                        <dl class="fldList">
                                            <dt>Add /Update to Moodle:</dt>
                                            <dd>
                                                <div class="clearfix advInfo">
                                                    <div class="chkHldr">
                                                        <input type="radio" name="moodle_user" id="moodle_user" class="radioClass"  value="1"
        <?php echo!empty($eUser['moodle_user']) && $eUser['moodle_user'] == 1 ? 'checked' : '' ?>>
                                                        <label class="chkF radio"><span>Yes</span></label>
                                                    </div>
                                                    <div class="chkHldr">
                                                        <input type="radio" name="moodle_user" id="moodle_user" class="radioClass"  value="0"
        <?php echo empty($eUser['moodle_user']) ? 'checked' : '' ?>>
                                                        <label class="chkF radio"><span>No</span></label>
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                        <?php }else { ?>
                                        <input type="hidden" name="moodle_user" value="<?php echo isset($eUser['moodle_user'])?$eUser['moodle_user']:0 ?>" >  
                                        <?php } ?>
                                    </div>
    <?php }
    ?>
                            </div>
                                                           <?php
                                                           if ($ref == '') {
                                                               ?>
                                <div class="row mb30">
                                    <div class="col-sm-12">
                                        <div class="tableHldr teamsInfoHldr language_table team_table noShadow">
                                    <?php
                                    $language_id1 = array_shift($userlanguageList);
                                    ?>
                                            <a href="javascript:void(0)" class="languageAddRow" title="Click this to add more languages you know."><i class="fa fa-plus"></i></a>
                                            <table class='table customTbl'>
                                                <thead>
                                                    <tr>
                                                        <th style="width:10%;font-size: 13px;">Sr. No.</th>
                                                        <th style="width:25%;font-size: 13px;">Language</th>
                                                        <th colspan="3" style="font-size: 13px;">Proficiency</th>
                                                        <th style="width:5%;"></th>
                                                    </tr>	
                                                </thead>
                                                <tbody>
                                                    <tr class='language_row'>
                                                        <td class='s_no'>1</td>
                                                        <td>
                                                            <select class="form-control language" id="language_id1" name="languageData[language_id1][language_id]" 
                                                                    onchange="addNewLanguage('language_id', 1, this.value)">
                                                                <option value=""> - Select Language - </option> 
        <?php
        foreach ($languageList as $value) {
            ?>
                                                                    <option value="<?php echo $value['language_id'] ?>"
            <?php echo!empty($language_id1) && $language_id1['language_id'] == $value['language_id'] ? 'selected' : '' ?>>
            <?php echo $value['language_name'] ?></option>  
            <?php
        }
        ?>
                                                                <option value="other">Other</option>
                                                            </select>

                                                            <div id="other_language_div1" style="display: none;margin-top: 10px;">
                                                                <input type="text" name="other_language1" class="form-control other_language" id="other_language1" disabled placeholder="Enter new language"
                                                                       onblur="checkLanguageExist(this.value, 1)"/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="chkHldr">
                                                                <input type="checkbox" name="languageData[language_id1][language_speak]" class=""  
                                                                       value="speaking" id="language_speak1"
        <?php echo!empty($language_id1) && $language_id1['language_speak'] == 'speaking' ? 'checked' : '' ?>>
                                                                <label class="chkF checkbox"><span>Speaking</span></label>
                                                            </div>

                                                        </td>
                                                        <td>
                                                            <div class="chkHldr">
                                                                <input type="checkbox" name="languageData[language_id1][language_read]" class=""  
                                                                       value="reading" id="language_read1"
                                                                       <?php echo!empty($language_id1) && $language_id1['language_read'] == 'reading' ? 'checked' : '' ?>>
                                                                <label class="chkF checkbox"><span>Reading</span></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="chkHldr">
                                                                <input type="checkbox" name="languageData[language_id1][language_write]" class=""  
                                                                       value="writing" id="language_write1"
        <?php echo!empty($language_id1) && $language_id1['language_write'] == 'writing' ? 'checked' : '' ?>>
                                                                <label class="chkF checkbox"><span>Writing</span></label>
                                                            </div>
                                                        </td>
                                                        <td></td>	
                                                    </tr>
        <?php
        if (!empty($userlanguageList)) {
            $sn = 2;
            foreach ($userlanguageList as $key => $value) {
                $row = '<tr class="language_row">
                                                                        <td class="s_no">' . $sn . '</td>';

                $row .= '<td>
                                                                            <select class="form-control language" name="languageData[language_id' . $sn . '][language_id]" 
                                                                                    id="language_id' . $sn . '" onchange="addNewLanguage(' . "'language_id'" . ',' . $sn . ',this.value)">
                                                                                <option value=""> - Select Language - </option>';

                foreach ($languageList as $language) {
                    $row .="<option value=\"" . $language['language_id'] . "\" ";
                    $row .=!empty($language) && $language['language_id'] == $value['language_id'] ? 'Selected' : '';
                    $row .=">" . $language['language_name'] . "</option>\n";
                }
                $row .= ' <option value="other">Other</option>
                                                                            </select>
                                                                            <div id="other_language_div' . $sn . '" style="display: none;margin-top: 10px;">
                                                                                <input type="text" name="other_language' . $sn . '" class="form-control other_language" id="other_language' . $sn . '" disabled placeholder="Enter new language" onblur="checkLanguageExist(this.value,' . $sn . ')"/>
                                                                            </div>
                                                                        </td>';
                $row .= '<td>
                                                                            <div class="chkHldr">
                                                                                <input type="checkbox" name="languageData[language_id' . $sn . '][language_speak]" class="" value="speaking"';
                $row.=!empty($value) && $value['language_speak'] == 'speaking' ? 'checked' : '';
                $row.=' id="language_speak' . $sn . '">
                                                                                <label class="chkF checkbox"><span>Speaking</span></label>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="chkHldr">
                                                                                <input type="checkbox" name="languageData[language_id' . $sn . '][language_read]" class="" value="reading"';
                $row.=!empty($value) && $value['language_read'] == 'reading' ? 'checked' : '';
                $row.=' id="language_read' . $sn . '">
                                                                                <label class="chkF checkbox"><span>Reading</span></label>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="chkHldr">
                                                                                <input type="checkbox" name="languageData[language_id' . $sn . '][language_write]" class="" value="writing"';
                $row.=!empty($value) && $value['language_write'] == 'writing' ? 'checked' : '';
                $row.=' id="language_write' . $sn . '">
                                                                                <label class="chkF checkbox"><span>Writing</span></label>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <a href="javascript:void(0)" class="delete_language_row" ><i class="fa fa-times"></i></a>
                                                                        </td>
                                                                </tr>';
                $sn++;
                echo $row;
            }
        }
        ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>


                                <h2 class="">Payment Details <i class="fa fa-info-circle vtip" title='When Assessors reach Intern stage, they begin to receive
                                                                an honorarium for the number of days they participate in an AQS External Review.<br> 
                                                                The amount is dependent on the AQS schools aspiration tier and the Assessors role 
                                                                in the External Review.'></i></h2>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <dl class="fldList">
                                            <dt>Bank Name:</dt>
                                            <dd><input type="text" class="form-control"  name="bank_name" id="bank_name" onkeypress="return isLetterKey(event)"
                                                       value="<?php echo!empty($eUser['bank_name']) ? $eUser['bank_name'] : '' ?>"></dd>
                                        </dl> 
                                    </div>
                                    <div class="col-sm-6">
                                        <dl class="fldList">
                                            <dt>IFSC CODE:</dt>
                                            <dd><input type="text" class="form-control"  name="ifsc_code" id="ifsc_code"
                                                       value="<?php echo!empty($eUser['ifsc_code']) ? base64_decode($eUser['ifsc_code']) : '' ?>"></dd>
                                        </dl>  
                                    </div>
                                    <div class="col-sm-12">
                                        <dl class="fldList">
                                            <dt>Branch Address:</dt>
                                            <dd>
                                                <textarea id="branch_address" name="branch_address" class="form-control"><?php echo!empty($eUser['branch_address']) ? $eUser['branch_address'] : '' ?></textarea>
                                            </dd>
                                        </dl>
                                    </div>
                                    <div class="col-sm-6">
                                        <dl class="fldList">
                                            <dt>Account Name:</dt>
                                            <dd><input type="text" class="form-control"  name="account_name" id="account_name" onkeypress="return isLetterKey(event)"
                                                       value="<?php echo!empty($eUser['account_name']) ? $eUser['account_name'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Pan Card Name:</dt>
                                            <dd><input type="text" class="form-control"  name="pancard_name" id="pancard_name" onkeypress="return isLetterKey(event)"
                                                       value="<?php echo!empty($eUser['pancard_name']) ? $eUser['pancard_name'] : '' ?>"></dd>
                                        </dl>
                                    </div>
                                    <div class="col-sm-6">

                                        <dl class="fldList">
                                            <dt>Account Number:</dt>
                                            <dd><input type="text" class="form-control"  name="account_number" id="account_number"
                                                       value="<?php echo!empty($eUser['account_number']) ? base64_decode($eUser['account_number']) : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Pan Card Number:
                                                <i class="fa fa-info-circle vtip" title="If you have updated your school/organisation bank account details for payments, please add your school’s/organisation’s PAN number in this field. If you have updated your personal bank account details for payments, add the same PAN card number as mentioned in your personal details above."></i>
                                            </dt>
                                            <dd><input type="text" class="form-control"  name="pancard_number" id="pancard_number"
                                                       value="<?php echo!empty($eUser['pancard_number']) ? $eUser['pancard_number'] : '' ?>"></dd>
                                        </dl>

                                    </div>
                                    <!--<h2 class="">Upload Documents</h2>-->
                                    <div class="col-sm-12">
                                        <dl class="fldList">
                                            <dt>Upload copies of PAN card and a cancelled cheque (multiple files can be uploaded):</dt>
                                            <dd class="judgementS" style="background-color: transparent;">
                                                <div class="upldHldr">
                                                    <div class="fileUpload btn btn-primary mr0">
                                                        <i class="glyphicon glyphicon-folder-open"></i> <span>Attach File</span>  
                                                        <input type="file" autocomplete="off" title="" class="upload uploadBtn" id="profile">
                                                    </div>
                                                    <span style="margin-left: 10px;"><i>(Only given formats jpeg,png,gif,jpg,doc,docx,txt,xls,xlsx,pdf are allowed!)</i></span>
                                                    <div class="filesWrapper" style="margin-top: 10px;">                                               
        <?php
        // $file_name = explode('~',$eUser['file_name']);
        $upload_document = array_unique(explode(',', $eUser['upload_document']));
        if ($eUser['file_name'] != '' && $eUser['upload_document'] != '') {
            foreach ($eUser['file_name'] as $key => $file_name) {
                echo '<div class="filePrev uploaded vtip ext-' . diagnosticModel::getFileExt($file_name['file_name']) . '" id="file-' . $file_name['file_id'] . '" title="' . $file_name['file_name'] . '">'
                . '<span class="delete fa" id="doc"></span>'
                . '<div class="inner"><a href="' . UPLOAD_URL . '' . $file_name['file_name'] . '" target="_blank"> </a></div>'
                . '<input type="hidden" name="files[]" value="' . $file_name['file_id'] . '" id="files"></div>';
            }
        }
        ?>

                                                    </div>
                                                </div>                                        

                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                                    <?php }
                                                    ?>
                        </div>
    <?php
    if ($ref == '') {
        ?>
                            <div role="tabpane1" class="tab-pane fade" id="emergencyContact">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <dl class="fldList">
                                            <dt>First Name:</dt>
                                            <dd><input type="text" class="form-control"  name="emergency_firstname" id="emergency_firstname" onkeypress="return isLetterKey(event)"
                                                       value="<?php echo!empty($eUser['emergency_firstname']) ? $eUser['emergency_firstname'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Last Name:</dt>
                                            <dd><input type="text" class="form-control"  name="emergency_lastname" id="emergency_lastname" onkeypress="return isLetterKey(event)"
                                                       value="<?php echo!empty($eUser['emergency_lastname']) ? $eUser['emergency_lastname'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Relationship:</dt>
                                            <dd><input type="text" class="form-control"  name="emergency_relationship" id="emergency_relationship" onkeypress="return isLetterKey(event)"
                                                       value="<?php echo!empty($eUser['emergency_relationship']) ? $eUser['emergency_relationship'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Home contact no:</dt>
                                            <dd>
                                                <div class="inlContBox ftySixty">
                                                    <div class="inlCBItm fty">
                                                        <div class="fld blk">
                                                            <select name="ec_country_code" id="wap_country_code" class="form-control" >
        <?php
        foreach ($countryCodeList as $value) {
            ?>
                                                                    <option value="<?php echo $value['phonecode'] ?>"
            <?php echo!empty($ec_country_code[1]) && $ec_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
            <?php echo "(+" . $value['phonecode'] . ") "; ?></option>
            <?php
        }
        ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="inlCBItm sixty">
                                                        <div class="fld">
                                                            <div>
                                                                <input type="text" class="form-control mask_ph "  name="emergency_home_contact_no" id="emergency_home_contact_no" value="<?php echo!empty($emergency_home_contact_no) ? str_replace("-", '', $emergency_home_contact_no) : '' ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Cell no:</dt>
                                            <dd>
                                                <div class="inlContBox ftySixty">
                                                    <div class="inlCBItm fty">
                                                        <div class="fld blk">
                                                            <select name="ecc_country_code" id="wap_country_code" class="form-control" >
        <?php
        foreach ($countryCodeList as $value) {
            ?>
                                                                    <option value="<?php echo $value['phonecode'] ?>"
            <?php echo!empty($ecc_country_code[1]) && $ecc_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
            <?php echo "(+" . $value['phonecode'] . ") "; ?></option>
            <?php
        }
        ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="inlCBItm sixty">
                                                        <div class="fld">
                                                            <div>
                                                                <input type="text" class="form-control mask_ph "  name="emergency_cell_no" id="emergency_cell_no" value="<?php echo!empty($emergency_cell_no) ? str_replace("-", '', $emergency_cell_no) : '' ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                    </div>
                                    <div class="col-sm-6">
                                        <dl class="fldList">
                                            <dt>Email address:</dt>
                                            <dd><input type="text" class="form-control"  name="emergency_email" id="emergency_email"
                                                       value="<?php echo!empty($eUser['emergency_email']) ? $eUser['emergency_email'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Address:</dt>
                                            <dd><input type="text" class="form-control"  name="emergency_address" id="emergency_address"
                                                       value="<?php echo!empty($eUser['emergency_address']) ? $eUser['emergency_address'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Town:</dt>
                                            <dd><input type="text" class="form-control"  name="emergency_town" id="emergency_town" onkeypress="return isLetterKey(event)"
                                                       value="<?php echo!empty($eUser['emergency_town']) ? $eUser['emergency_town'] : '' ?>"></dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>State:</dt>
                                            <dd>
                                                <select name="emergency_state_id" id="emergency_state_id" class="form-control" >
                                                    <option value="">--Select State--</option>
        <?php
        foreach ($stateList as $value) {
            ?>
                                                        <option value="<?php echo $value['state_id'] ?>"
            <?php echo!empty($eUser['emergency_state_id']) && $eUser['emergency_state_id'] == $value['state_id'] ? 'Selected' : '' ?>>
            <?php echo $value['state_name'] ?></option>
            <?php
        }
        ?>
                                                </select>
                                            </dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Pin code:</dt>
                                            <dd>
                                                <input type="text" class="form-control"  name="emergency_pincode" id="emergency_pincode" maxlength="6"
                                                       value="<?php echo!empty($eUser['emergency_pincode']) ? $eUser['emergency_pincode'] : '' ?>" onkeypress="return isNumberKey(event)">
                                            </dd>
                                        </dl>

                                    </div>
                                </div>  
                            </div>
                            <div role="tabpane2" class="tab-pane fade" id="additionalInfo">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <dl class="fldList">
                                            <dt>Meal Preferences:</dt>
                                            <dd>
                                                <div class="clearfix advInfo">
                                                    <div class="chkHldr">
                                                        <input type="radio" name="meal_preferences" id="meal_preferences" class="radioClass"  value="Vegetarian"
        <?php echo!empty($eUser['meal_preferences']) && $eUser['meal_preferences'] == 'Vegetarian' ? 'checked' : '' ?>>
                                                        <label class="chkF radio"><span>Vegetarian</span></label>
                                                    </div>
                                                    <div class="chkHldr">
                                                        <input type="radio" name="meal_preferences" id="meal_preferences" class="radioClass"  value="Non-Vegetarian"
        <?php echo!empty($eUser['meal_preferences']) && $eUser['meal_preferences'] == 'Non-Vegetarian' ? 'checked' : '' ?>>
                                                        <label class="chkF radio"><span>Non-Vegetarian</span></label>
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                    </div>

                                    <div class="col-sm-12">
                                        <dl class="fldList">
                                            <dt>Medical Conditions <i class="fa fa-info-circle vtip" title='Medical information assists the Adhyayan team when
                                                                      organising for travel and accommodation for AQS External Reviews.<br/> We accommodate
                                                                      Assessors requirements wherever possible' style="color: #101010;"></i> :
                                            </dt>
                                            <dd>
                                                <div class="clearfix advInfo">
        <?php
        //$eUser['medical_conditions']=$eUser['medical_conditions']!=''?$eUser['medical_conditions']:'';

        if (isset($medicalOptionList) && count($medicalOptionList) >= 1) {
            $medical = array_column($medicalOptionList, 'medical_condition_id');
        } else {
            $medical = array();
        }
        //print_r($medical);
        foreach ($deseaseList as $value) {
            if ($value['desease_name'] == 'Other') {
                $width = '70px';
                $onclick = "onclick=openOtherTextBox('" . 'medical_' . $value['id'] . "','medical')";
            } else {
                $width = '290px';
                $onclick = '';
            }
            ?>
                                                        <div class="chkHldr" style="width:<?php echo $width ?>">
                                                            <input type="checkbox" name="medical_conditions[]" class="" value="<?php echo $value['id'] ?>"  id="medical_<?php echo $value['id'] ?>"
                                                        <?php echo!empty($medical) && in_array($value['id'], $medical) ? 'checked' : '' ?> <?php echo $onclick ?>>
                                                            <label class="chkF checkbox"><span><?php echo $value['desease_name'] ?></span></label>  
                                                        </div>  
                                                        <?php
                                                    }
                                                    if ($eUser['other_medical_text'] != '') {
                                                        $display1 = 'block';
                                                    } else {
                                                        $display1 = 'none';
                                                    }
                                                    ?>
                                                    <div style="width:210px;float: left;margin-bottom: 6px;margin-right: 6px;position: relative;display: <?php echo $display1 ?>;" 
                                                         id="other_medical_div">
                                                        <input type="text" class="form-control" name="other_medical_text" id="other_medical_text"
                                                               value="<?php echo!empty($eUser['other_medical_text']) ? $eUser['other_medical_text'] : '' ?>"
                                                               placeholder="Please specify if any" >
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                    </div>

                                    <div class="col-sm-6">
                                        <dl class="fldList">
                                            <dt>Are you ready to travel outstation?</dt>
                                            <dd>
                                                <div class="clearfix advInfo">
                                                    <div class="chkHldr">
                                                        <input type="radio" name="travel_outstation" id="travel_outstation" class="radioClass"  value="yes"
        <?php echo!empty($eUser['travel_outstation']) && $eUser['travel_outstation'] == 'yes' ? 'checked' : '' ?>>
                                                        <label class="chkF radio"><span>Yes</span></label>
                                                    </div>
                                                    <div class="chkHldr">
                                                        <input type="radio" name="travel_outstation" id="travel_outstation" class="radioClass"  value="no"
        <?php echo!empty($eUser['travel_outstation']) && $eUser['travel_outstation'] == 'no' ? 'checked' : '' ?>>
                                                        <label class="chkF radio"><span>No</span></label>
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Travel Sickness:</dt>
                                            <dd>
                                                <div class="clearfix advInfo">
                                                               <?php
                                                               $checked_sickness_yes = "";
                                                               $checked_sickness_no = "";
                                                               if (isset($eUser['travel_sickness']) && $eUser['travel_sickness'] == 'yes') {
                                                                   $checked_sickness_yes = "checked = " . '"' . "checked" . '"';
                                                               } else if (isset($eUser['travel_sickness']) && $eUser['travel_sickness'] == 'no') {
                                                                   $checked_sickness_no = "checked = " . '"' . "checked" . '"';
                                                               }
                                                               ?>
                                                    <div class="chkHldr">
                                                        <input type="radio" name="travel_sickness" id="travel_sickness_no" onclick=openOtherTextBox('travel_sickness_no','travel_sicknes') class="radioClass"  value="no" <?php echo $checked_sickness_no; ?> >
                                                        <label class="chkF radio"><span>No</span></label>
                                                    </div>
                                                    <div class="chkHldr">
                                                        <input type="radio" name="travel_sickness" id="travel_sickness_yes" onclick=openOtherTextBox('travel_sickness_yes','travel_sicknes') class="radioClass"  value="yes" <?php echo $checked_sickness_yes; ?> >
                                                        <label class="chkF radio"><span>Yes</span></label>
                                                    </div>
                                                    <?php
                                                     $display_sick = 'none';
                                                    if (isset($eUser['travel_sickness']) && $eUser['travel_sickness'] == 'yes') {
                                                        $display_sick = 'block';
                                                    }
                                                    ?>
                                                    <div style="width:210px;float: left;margin-bottom: 6px;margin-right: 6px;position: relative;display: <?php echo $display_sick ?>;" 
                                                         id="other_travel_sicknes_div">
                                                        <input type="text" class="form-control" name="travel_sickness_text" id="travel_sickness_text"
                                                               value="<?php echo isset($eUser['travel_sickness_text']) ? $eUser['travel_sickness_text'] : ""; ?>" placeholder="Please specifiy in detail" >
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                        <dl class="fldList">
                                            <dt>Accomodation Preference:</dt>
                                            <dd>
                                                <div class="fldsG">
                                                    <div class="fldRA" id="info_acc" style=" <?php echo (isset($eUser['accomod_pref']) && $eUser['accomod_pref'] != '') ? '' : 'display: none'; ?>"><i class="fa fa-info-circle vtip" title="We will try to accomodate your preference wherever it is possible, however, we cannot guarantee it as we work with a wide range of schools located in rural and remote locations where we strive to make the basic provision available." style="pointer-events: auto;"></i></div> 
                                                    <div class="grpLFld">
                                                        <select name="accomod_pref" id="accomod_pref" class="form-control" >
                                                            <option value="">--Select Option--</option>
                                                            <option value="shared" <?php echo (isset($eUser['accomod_pref']) && $eUser['accomod_pref'] == 'shared') ? "selected" : ""; ?> >Shared</option>
                                                            <option value="solo" <?php echo (isset($eUser['accomod_pref']) && $eUser['accomod_pref'] == 'solo') ? "selected" : ""; ?>>Solo</option>
                                                            <option value="no" <?php echo (isset($eUser['accomod_pref']) && $eUser['accomod_pref'] == 'no') ? "selected" : ""; ?>>No Preference</option>                                                        
                                                        </select>
                                                    </div>
                                                </div>


                                            </dd>

                                        </dl>
                                    </div>

                                </div> 
                            </div>
                            <div role="tabpane3" class="tab-pane fade" id="biography">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <dl class="fldList">
                                            <dt>Education qualifications and achievements:</dt>
                                            <dd><input type="text" class="form-control"  name="education" id="education" style="height: 40px;"
                                                       value="<?php echo!empty($eUser['education']) ? $eUser['education'] : '' ?>"></dd>
                                        </dl>
                                    </div>
                                    <div class="col-sm-6">
                                        <dl class="fldList">
                                            <dt>Workshops facilitated or research conducted:</dt>
                                            <dd><input type="text" class="form-control"  name="workshop" id="workshop" style="height: 40px;"
                                                       value="<?php echo!empty($eUser['workshop']) ? $eUser['workshop'] : '' ?>"></dd>
                                        </dl>
                                    </div>
                                    <div class="col-sm-12">
                                        <dl class="fldList">
                                            <dt>Hobbies and Interests :</dt>
                                            <dd>
                                                <div class="clearfix advInfo">
        <?php
        //$eUser['hobbies']=$eUser['hobbies']!=''?$eUser['hobbies']:'';
        //print_r($hobbies);
        if (isset($hobbies) && count($hobbies) >= 1) {
            $hobbies = array_column($hobbies, 'hobby_id');
        } else
            $hobbies = array();

        foreach ($hobbiesList as $value) {
            if ($value['name'] == 'Other') {
                $width = '70px';
                $onclick = "onclick=openOtherTextBox('" . 'hobbies_' . $value['id'] . "','hobbies')";
            } else {
                $width = '290px';
                $onclick = '';
            }
            ?>
                                                        <div class="chkHldr" style="width:<?php echo $width ?>">
                                                            <input type="checkbox" name="hobbies[]" class="" value="<?php echo $value['id'] ?>"  
                                                                   id="hobbies_<?php echo $value['id'] ?>" <?php echo $onclick ?>
                                                        <?php echo!empty($hobbies) && in_array($value['id'], $hobbies) ? 'checked' : '' ?>>
                                                            <label class="chkF checkbox"><span><?php echo $value['name'] ?></span></label>  
                                                        </div>  
                                                        <?php
                                                    }
                                                    if ($eUser['other_hobbies_text'] != '') {
                                                        $display = 'block';
                                                    } else {
                                                        $display = 'none';
                                                    }
                                                    ?>
                                                    <div style="width:210px;float: left;margin-bottom: 6px;margin-right: 6px;position: relative;display: <?php echo $display ?>" id="other_hobbies_div">
                                                        <input type="text" class="form-control"   placeholder="Please specify if any" name="other_hobbies_text" id="other_hobbies_text"
                                                               value="<?php echo!empty($eUser['other_hobbies_text']) ? $eUser['other_hobbies_text'] : '' ?>">
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                    </div>                            
                                    <div class="col-sm-12">
                                        <dl class="fldList">
        <?php
        $assessor_experience_yes = "";
        $assessor_experience_no = "";
        $assessor_experience_no = "checked = " . '"' . "checked" . '"';
        if (isset($eUser['assessor_experience']) && $eUser['assessor_experience'] == 'yes') {
            $assessor_experience_yes = "checked = " . '"' . "checked" . '"';
        }
        ?>
                                            <!--<dt>Describe your experience with the AQS review:</dt>
                                            <dd><textarea class="form-control word_count"  name="assessor_experience" id="assessor_experience"
                                                          placeholder="Write your experience undertaking the AQS Self Review or other review tools individually or with your organisation."><?php echo!empty($eUser['assessor_experience']) ? $eUser['assessor_experience'] : '' ?></textarea></dd>-->
                                            <dt>Have you been part of your school self-review?</dt>
                                            <dd><div class="chkHldr">
                                                    <input type="radio" name="assessor_experience" id="assessor_experience"  class="radioClass"  value="no" <?php echo $assessor_experience_no; ?> >
                                                    <label class="chkF radio"><span>No</span></label>
                                                </div>
                                                <div class="chkHldr">
                                                    <input type="radio" name="assessor_experience" id="assessor_experience" class="radioClass"  value="yes" <?php echo $assessor_experience_yes; ?> >
                                                    <label class="chkF radio"><span>Yes</span></label>
                                                </div></dd>

                                        </dl>
                                    </div>
                                    <div class="col-sm-12">
                                        <dl class="fldList">
                                            <dt>Brief description of work experience:</dt>
                                            <dd><textarea class="form-control"  name="experience_description" id="experience_description"
                                                          placeholder="Brief description of work experience"><?php echo!empty($eUser['experience_description']) ? $eUser['experience_description'] : '' ?></textarea></dd>
                                        </dl>
                                    </div>
                                    <div class="col-sm-12">
                                        <dl class="fldList">
                                            <dt>Upload Resume:</dt>
                                            <dd class="judgementSResumes" style="background-color: transparent;">
                                                <div class="upldHldr">
                                                    <div class="fileUpload btn btn-primary mr0">
                                                        <i class="glyphicon glyphicon-folder-open"></i> <span>Attach File</span>  
                                                        <input type="file" autocomplete="off" title="" class="upload uploadResumeBtn" id="profile_resume">
                                                    </div>
                                                    <span style="margin-left: 10px;"><i>(Only given formats doc,docx,txt are allowed!)</i></span>
                                                    <div class="filesWrapperResumes">                                               
        <?php
        $file_name = isset($eUser['resume_name']) ? $eUser['resume_name'] : '';
        //$upload_document = explode(',',$eUser['upload_document']);
        if ($file_name != '') {
            //foreach ($upload_document as $key => $value) {
            echo '<div class="filePrev uploaded vtip ext-' . diagnosticModel::getFileExt($file_name) . '" id="file-resume-' . $eUser['profile_resume'] . '" title="' . $file_name . '">'
            . '<span class="delete fa"></span>'
            . '<div class="inner"><a href="' . UPLOAD_URL . '' . $file_name . '" target="_blank"> </a></div>'
            . '<input type="hidden" name="profile_resume" value="' . $eUser['profile_resume'] . '" id="files"></div>';
            //}                                                
        }
        ?>

                                                    </div>
                                                </div>                                        

                                            </dd>
                                        </dl>
                                    </div>

                                </div>
                            </div>
    <?php }
    ?>
                        <!-- Ends: \ #Activity Status tabpanel -->

                    </div>
                </div>
    <?php
    if (isset($_REQUEST['process']) && $_REQUEST['process'] == 'invite') {
        ?>
                    <input type="hidden" class="" name="roles[]" value="4"/>    
                            <?php
                        }
                        if ($eUser['table_name'] == 'd_user' || (isset($_REQUEST['process']) && $_REQUEST['process'] == 'invite')) {
                            if ((in_array(4, $user_role) || in_array(9, $user_role) ) && $eUser['term_condition'] == 0) {
                                ?>
                        <div class="fr clearfix" id="agreeRow">																
                            <button class="btn btn-primary" id="agreeButton" type="button">I Agree</button>
                            <button class="btn btn-primary" id="disagreeButton" type="button">I Disagree</button>									
                        </div>
                        <?php
                    }
                    ?>
                    <div class="fr clearfix" id="termsRow" style="display: <?php echo ($isAdmin == 1 || $eUser['term_condition'] == 1 )? 'block' : 'none' ?>;" >
                        <button class="btn btn-primary" type="button" id="saveUserProfile" <?php echo ($isAdmin  || $eUser['term_condition'] == 1) ? 'disabled' : '' ?>>
                            <!--<i class="fa fa-save"></i>-->Save
                        </button>
        <?php
        if (!isset($_REQUEST['process'])) {
            ?>
                            <button class="btn btn-primary" type="button" id="submitUserProfile" <?php echo isset($eUser['is_submit']) && $eUser['is_submit'] == 1 ? 'style="display:none"' : ''; ?>
                        <?php echo ($isAdmin || $eUser['term_condition'] == 1) ? 'disabled' : '' ?>>
            <!--                        <i class="fa fa-input"></i>-->Submit
                            </button>
        <?php }
        ?>
                    </div>    
                        <?php
                    }
                    ?>

                <div class="clearfix"></div>
            </div>
            <!--<div id="validationErrors">dsfasdf</div>-->
            <div class="ajaxMsg" id="createresource"></div>
                    <?php
                    if (isset($eUser['is_submit']) && $eUser['is_submit'] == 1) {
                        ?>
                <input type="hidden" class="" name="submit_value" value="<?php echo $eUser['is_submit']; ?>" id="submit_value"/>    
                    <?php
                } else if (isset($_REQUEST['process']) && $_REQUEST['process'] == 'invite') {
                    ?>
                <input type="hidden" class="" name="submit_value" value="1" id="submit_value"/>    
        <?php
    }
    ?>

            <input type="hidden" class="" name="edit_request_from" value="user_profile" id="user_profile"/>
            <input type="hidden" name="client_id_old" id="selected_client_id_old" value="<?php echo $eUser['client_id']; ?>"	>
            <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
            <div id="validationErrors"></div>
        </form>
    </div>
    <script type="text/javascript">
        $("textarea.word_count").textareaCounter();
        $(document).ready(function () {
         $('#date_of_birth').mask('99-99-9999',{placeholder:"DD-MM-YYYY"});
       // $(.mask_ph).inputmask("9-a{1,3}9{1,3}");
        });
    </script>
    <?php
} else {
    echo '<h1>User does not exist</h1>';
}
?>