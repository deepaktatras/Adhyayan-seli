<?php
include '../config/config.php';
include '../library/db.class.php';

$objDB = new db(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
$newDBName = "adhyayan".  str_replace(',', '', $_GET['client_id']);
if(isset($_GET['client_id']) && isset($_GET['assessment_id'])){
    $SQL0="SELECT MAX(score_id)+1 as id  from ".DB_NAME.".f_score;";
    $last_insert_id = $objDB->get_row($SQL0);
    $last_insert_id = $last_insert_id['id'];
//    print_r($last_insert_id);
//    die;
    $SQL = "CREATE DATABASE IF NOT EXISTS ".$newDBName.";";
    $objDB->query($SQL);
    $SQL1=""
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".preferred_language SELECT * FROM ".DB_NAME.".preferred_language;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".z_history LIKE ".DB_NAME.".z_history;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".z_sync_status LIKE ".DB_NAME.".z_sync_status;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_ngo LIKE ".DB_NAME.".d_ngo;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_post_review LIKE ".DB_NAME.".d_post_review;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_teacher_data LIKE ".DB_NAME.".d_teacher_data;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_user_enc LIKE ".DB_NAME.".d_user_enc;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_group_assessment LIKE ".DB_NAME.".d_group_assessment;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_alerts LIKE ".DB_NAME.".d_alerts;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_aqs_team_invite_user LIKE ".DB_NAME.".h_aqs_team_invite_user;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".z_memory_stat LIKE ".DB_NAME.".z_memory_stat;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_assessment_type LIKE ".DB_NAME.".d_assessment_type;
                INSERT INTO ".$newDBName.".d_assessment_type SELECT * FROM ".DB_NAME.".d_assessment_type;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_award LIKE ".DB_NAME.".d_award;
                INSERT INTO ".$newDBName.".d_award SELECT * FROM ".DB_NAME.".d_award;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_award_scheme LIKE ".DB_NAME.".d_award_scheme;
                INSERT INTO ".$newDBName.".d_award_scheme SELECT * FROM ".DB_NAME.".d_award_scheme;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_board SELECT * FROM ".DB_NAME.".d_board;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_cluster LIKE ".DB_NAME.".d_cluster;
                INSERT INTO ".$newDBName.".d_cluster SELECT * FROM ".DB_NAME.".d_cluster;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_core_question LIKE ".DB_NAME.".d_core_question;
                INSERT INTO ".$newDBName.".d_core_question SELECT * FROM ".DB_NAME.".d_core_question;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_diagnostic LIKE ".DB_NAME.".d_diagnostic;
                INSERT INTO ".$newDBName.".d_diagnostic SELECT * FROM ".DB_NAME.".d_diagnostic;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_diagnostic_limit_matrix LIKE ".DB_NAME.".d_diagnostic_limit_matrix;
                INSERT INTO ".$newDBName.".d_diagnostic_limit_matrix SELECT * FROM ".DB_NAME.".d_diagnostic_limit_matrix;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_fees LIKE ".DB_NAME.".d_fees;
                INSERT INTO ".$newDBName.".d_fees SELECT * FROM ".DB_NAME.".d_fees;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_judgement_statement LIKE ".DB_NAME.".d_judgement_statement;
                INSERT INTO ".$newDBName.".d_judgement_statement SELECT * FROM ".DB_NAME.".d_judgement_statement;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_key_question LIKE ".DB_NAME.".d_key_question;
                INSERT INTO ".$newDBName.".d_key_question SELECT * FROM ".DB_NAME.".d_key_question;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_kpa LIKE ".DB_NAME.".d_kpa;
                INSERT INTO ".$newDBName.".d_kpa SELECT * FROM ".DB_NAME.".d_kpa;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_language LIKE ".DB_NAME.".d_language;
                INSERT INTO ".$newDBName.".d_language SELECT * FROM ".DB_NAME.".d_language;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_payment_mode LIKE ".DB_NAME.".d_payment_mode;
                INSERT INTO ".$newDBName.".d_payment_mode SELECT * FROM ".DB_NAME.".d_payment_mode;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_product LIKE ".DB_NAME.".d_product;
                INSERT INTO ".$newDBName.".d_product SELECT * FROM ".DB_NAME.".d_product;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_rating LIKE ".DB_NAME.".d_rating;
                INSERT INTO ".$newDBName.".d_rating SELECT * FROM ".DB_NAME.".d_rating;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_recommendation LIKE ".DB_NAME.".d_recommendation;
                INSERT INTO ".$newDBName.".d_recommendation SELECT * FROM ".DB_NAME.".d_recommendation;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_referrer SELECT * FROM ".DB_NAME.".d_referrer;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_reports LIKE ".DB_NAME.".d_reports;
                INSERT INTO ".$newDBName.".d_reports SELECT * FROM ".DB_NAME.".d_reports;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_action LIKE ".DB_NAME.".d_review_action;
                INSERT INTO ".$newDBName.".d_review_action SELECT * FROM ".DB_NAME.".d_review_action;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_association LIKE ".DB_NAME.".d_review_association;
                INSERT INTO ".$newDBName.".d_review_association SELECT * FROM ".DB_NAME.".d_review_association;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_avgstafftenure LIKE ".DB_NAME.".d_review_avgstafftenure;
                INSERT INTO ".$newDBName.".d_review_avgstafftenure SELECT * FROM ".DB_NAME.".d_review_avgstafftenure;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_classratio LIKE ".DB_NAME.".d_review_classratio;
                INSERT INTO ".$newDBName.".d_review_classratio SELECT * FROM ".DB_NAME.".d_review_classratio;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_classroom LIKE ".DB_NAME.".d_review_classroom;
                INSERT INTO ".$newDBName.".d_review_classroom SELECT * FROM ".DB_NAME.".d_review_classroom;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_decision LIKE ".DB_NAME.".d_review_decision;
                INSERT INTO ".$newDBName.".d_review_decision SELECT * FROM ".DB_NAME.".d_review_decision;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_engagement LIKE ".DB_NAME.".d_review_engagement;
                INSERT INTO ".$newDBName.".d_review_engagement SELECT * FROM ".DB_NAME.".d_review_engagement;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_involvement LIKE ".DB_NAME.".d_review_involvement;
                INSERT INTO ".$newDBName.".d_review_involvement SELECT * FROM ".DB_NAME.".d_review_involvement;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_medium_instrn LIKE ".DB_NAME.".d_review_medium_instrn;
                INSERT INTO ".$newDBName.".d_review_medium_instrn SELECT * FROM ".DB_NAME.".d_review_medium_instrn;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_midleaders LIKE ".DB_NAME.".d_review_midleaders;
                INSERT INTO ".$newDBName.".d_review_midleaders SELECT * FROM ".DB_NAME.".d_review_midleaders;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_openness LIKE ".DB_NAME.".d_review_openness;
                INSERT INTO ".$newDBName.".d_review_openness SELECT * FROM ".DB_NAME.".d_review_openness;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_principal_tenure LIKE ".DB_NAME.".d_review_principal_tenure;
                INSERT INTO ".$newDBName.".d_review_principal_tenure SELECT * FROM ".DB_NAME.".d_review_principal_tenure;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_staff LIKE ".DB_NAME.".d_review_staff;
                INSERT INTO ".$newDBName.".d_review_staff SELECT * FROM ".DB_NAME.".d_review_staff;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_students LIKE ".DB_NAME.".d_review_students;
                INSERT INTO ".$newDBName.".d_review_students SELECT * FROM ".DB_NAME.".d_review_students;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_review_vision LIKE ".DB_NAME.".d_review_vision;
                INSERT INTO ".$newDBName.".d_review_vision SELECT * FROM ".DB_NAME.".d_review_vision;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_school_class SELECT * FROM ".DB_NAME.".d_school_class;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_school_it_support LIKE ".DB_NAME.".d_school_it_support;
                INSERT INTO ".$newDBName.".d_school_it_support SELECT * FROM ".DB_NAME.".d_school_it_support;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_school_level LIKE ".DB_NAME.".d_school_level;
                INSERT INTO ".$newDBName.".d_school_level SELECT * FROM ".DB_NAME.".d_school_level;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_school_location LIKE ".DB_NAME.".d_school_location;
                INSERT INTO ".$newDBName.".d_school_location SELECT * FROM ".DB_NAME.".d_school_location;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_school_type SELECT * FROM ".DB_NAME.".d_school_type;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_states LIKE ".DB_NAME.".d_states;
                INSERT INTO ".$newDBName.".d_states SELECT * FROM ".DB_NAME.".d_states;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_status SELECT * FROM ".DB_NAME.".d_status;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_student_body LIKE ".DB_NAME.".d_student_body;
                INSERT INTO ".$newDBName.".d_student_body SELECT * FROM ".DB_NAME.".d_student_body;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_student_type SELECT * FROM ".DB_NAME.".d_student_type;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_sub_assessment_type LIKE ".DB_NAME.".d_sub_assessment_type;
                INSERT INTO ".$newDBName.".d_sub_assessment_type SELECT * FROM ".DB_NAME.".d_sub_assessment_type;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_teacher_attribute LIKE ".DB_NAME.".d_teacher_attribute;
                INSERT INTO ".$newDBName.".d_teacher_attribute SELECT * FROM ".DB_NAME.".d_teacher_attribute;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_teacher_category LIKE ".DB_NAME.".d_teacher_category;
                INSERT INTO ".$newDBName.".d_teacher_category SELECT * FROM ".DB_NAME.".d_teacher_category;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_thought LIKE ".DB_NAME.".d_thought;
                INSERT INTO ".$newDBName.".d_thought SELECT * FROM ".DB_NAME.".d_thought;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_tier LIKE ".DB_NAME.".d_tier;
                INSERT INTO ".$newDBName.".d_tier SELECT * FROM ".DB_NAME.".d_tier;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_user_capability LIKE ".DB_NAME.".d_user_capability;
                INSERT INTO ".$newDBName.".d_user_capability SELECT * FROM ".DB_NAME.".d_user_capability;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_user_role LIKE ".DB_NAME.".d_user_role;
                INSERT INTO ".$newDBName.".d_user_role SELECT * FROM ".DB_NAME.".d_user_role;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_user_sub_role LIKE ".DB_NAME.".d_user_sub_role;
                INSERT INTO ".$newDBName.".d_user_sub_role SELECT * FROM ".DB_NAME.".d_user_sub_role;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_workshop LIKE ".DB_NAME.".d_workshop;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_award_scheme LIKE ".DB_NAME.".h_award_scheme;
                INSERT INTO ".$newDBName.".h_award_scheme SELECT * FROM ".DB_NAME.".h_award_scheme;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_cq_js_instance LIKE ".DB_NAME.".h_cq_js_instance;
                INSERT INTO ".$newDBName.".h_cq_js_instance SELECT * FROM ".DB_NAME.".h_cq_js_instance;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_diagnostic_rating_scheme LIKE ".DB_NAME.".h_diagnostic_rating_scheme;
                INSERT INTO ".$newDBName.".h_diagnostic_rating_scheme SELECT * FROM ".DB_NAME.".h_diagnostic_rating_scheme;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_diagnostic_teacher_cat LIKE ".DB_NAME.".h_diagnostic_teacher_cat;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_group_assessment_diagnostic LIKE ".DB_NAME.".h_group_assessment_diagnostic;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_group_assessment_external_assessor LIKE ".DB_NAME.".h_group_assessment_external_assessor;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_group_assessment_report LIKE ".DB_NAME.".h_group_assessment_report;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_group_assessment_teacher LIKE ".DB_NAME.".h_group_assessment_teacher;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_jstatement_recommendation LIKE ".DB_NAME.".h_jstatement_recommendation;
                INSERT INTO ".$newDBName.".h_jstatement_recommendation SELECT * from ".DB_NAME.".h_jstatement_recommendation;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_kpa_diagnostic LIKE ".DB_NAME.".h_kpa_diagnostic;
                INSERT INTO ".$newDBName.".h_kpa_diagnostic SELECT * FROM ".DB_NAME.".h_kpa_diagnostic;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_kpa_kq LIKE ".DB_NAME.".h_kpa_kq;
                INSERT INTO ".$newDBName.".h_kpa_kq SELECT * FROM ".DB_NAME.".h_kpa_kq;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_kq_cq LIKE ".DB_NAME.".h_kq_cq;
                INSERT INTO ".$newDBName.".h_kq_cq SELECT * FROM ".DB_NAME.".h_kq_cq;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_nonteaching_staff_school_level LIKE ".DB_NAME.".h_nonteaching_staff_school_level;
                INSERT INTO ".$newDBName.".h_nonteaching_staff_school_level SELECT * FROM ".DB_NAME.".h_nonteaching_staff_school_level;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_studbody_school_level LIKE ".DB_NAME.".h_studbody_school_level;
                INSERT INTO ".$newDBName.".h_studbody_school_level SELECT * FROM ".DB_NAME.".h_studbody_school_level;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_teaching_staff_school_level LIKE ".DB_NAME.".h_teaching_staff_school_level;
                INSERT INTO ".$newDBName.".h_teaching_staff_school_level SELECT * FROM ".DB_NAME.".h_teaching_staff_school_level;"
        
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".session_token LIKE ".DB_NAME.".session_token;
                INSERT INTO ".$newDBName.".session_token SELECT * FROM ".DB_NAME.".session_token;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_medical LIKE ".DB_NAME.".d_medical;
                INSERT INTO ".$newDBName.".d_medical SELECT * FROM ".DB_NAME.".d_medical;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_filter LIKE ".DB_NAME.".d_filter;
                INSERT INTO ".$newDBName.".d_filter SELECT * FROM ".DB_NAME.".d_filter;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_filter_attr LIKE ".DB_NAME.".d_filter_attr;
                INSERT INTO ".$newDBName.".d_filter_attr SELECT * FROM ".DB_NAME.".d_filter_attr;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_filter_operator LIKE ".DB_NAME.".d_filter_operator;
                INSERT INTO ".$newDBName.".d_filter_operator SELECT * FROM ".DB_NAME.".d_filter_operator;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_school_region LIKE ".DB_NAME.".d_school_region;
                INSERT INTO ".$newDBName.".d_school_region SELECT * FROM ".DB_NAME.".d_school_region;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_school_strength LIKE ".DB_NAME.".d_school_strength;
                INSERT INTO ".$newDBName.".d_school_strength SELECT * FROM ".DB_NAME.".d_school_strength;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_filter_attr LIKE ".DB_NAME.".h_filter_attr;
                INSERT INTO ".$newDBName.".h_filter_attr SELECT * FROM ".DB_NAME.".h_filter_attr;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_filter_attr_operator LIKE ".DB_NAME.".h_filter_attr_operator;
                INSERT INTO ".$newDBName.".h_filter_attr_operator SELECT * FROM ".DB_NAME.".h_filter_attr_operator;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_filter_multiple_vals LIKE ".DB_NAME.".h_filter_multiple_vals;
                INSERT INTO ".$newDBName.".h_filter_multiple_vals SELECT * FROM ".DB_NAME.".h_filter_multiple_vals;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_network_report LIKE ".DB_NAME.".h_network_report;
                INSERT INTO ".$newDBName.".h_network_report SELECT * FROM ".DB_NAME.".h_network_report;"
        . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_hobbies LIKE ".DB_NAME.".d_hobbies;
                INSERT INTO ".$newDBName.".d_hobbies SELECT * FROM ".DB_NAME.".d_hobbies;"
        ;
//    echo $SQL1;
//    die;
    if($objDB->query($SQL1)){
//    if($objDB){
        $SQL2=" "
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_client LIKE ".DB_NAME.".d_client;            
                    INSERT INTO ".$newDBName.".d_client SELECT * FROM ".DB_NAME.".d_client where client_id IN (".$_GET['client_id'].") Or  
                    client_id IN (Select client_id from ".DB_NAME.".d_user where user_id IN ( Select user_id from ".DB_NAME.".h_assessment_user where
                    assessment_id IN (".$_GET['assessment_id'].") and role='4'  UNION ALL SELECT user_id from ".DB_NAME.".h_assessment_external_team 
                    WHERE assessment_id IN (".$_GET['assessment_id'].") and user_role='4' UNION All SELECT t1.user_id FROM ".DB_NAME.".`d_user` t1 
                    Left Join ".DB_NAME.".h_user_user_role t2 ON (t1.user_id=t2.user_id) WHERE t2.role_id IN (1,2,8)) );"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_client_network LIKE ".DB_NAME.".h_client_network;
                    INSERT INTO ".$newDBName.".h_client_network SELECT * FROM ".DB_NAME.".h_client_network Where client_id IN (".$_GET['client_id'].");"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_network LIKE ".DB_NAME.".d_network;
                    INSERT INTO ".$newDBName.".d_network SELECT * FROM ".DB_NAME.".d_network Where network_id IN (Select network_id 
                    from ".$newDBName.".h_client_network);"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_assessment LIKE ".DB_NAME.".d_assessment;
                    INSERT INTO ".$newDBName.".d_assessment SELECT * FROM ".DB_NAME.".d_assessment WHERE assessment_id IN (".$_GET['assessment_id'].");"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_aqs_data LIKE ".DB_NAME.".d_aqs_data;
                    INSERT INTO ".$newDBName.".d_aqs_data SELECT * FROM ".DB_NAME.".d_aqs_data WHERE id IN (Select `aqsdata_id` 
                    from ".DB_NAME.".d_assessment where `aqsdata_id`!='' and client_id in (".$_GET['client_id'].") and 
                    assessment_id IN (".$_GET['assessment_id'].") );"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_aqs_team LIKE ".DB_NAME.".d_aqs_team;
                    INSERT INTO ".$newDBName.".d_aqs_team SELECT * from ".DB_NAME.".d_aqs_team where AQS_data_id IN (Select `aqsdata_id` 
                    from ".DB_NAME.".d_assessment where `aqsdata_id`!='' and client_id in (".$_GET['client_id'].") and 
                    assessment_id IN (".$_GET['assessment_id'].") );"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_user LIKE ".DB_NAME.".d_user;
                    INSERT INTO ".$newDBName.".d_user SELECT t1.* FROM ".DB_NAME.".d_user t1 LEFT JOIN ".DB_NAME.".h_user_user_role t2 ON 
                    (t1.user_id = t2.user_id) LEFT JOIN ".DB_NAME.".h_assessment_user t3 ON (t1.user_id = t3.user_id) WHERE 
                    t2.role_id IN(5,6,3,4,2,1,8) AND t1.user_id IN (SELECT user_id FROM ".DB_NAME.".d_user WHERE client_id IN(".$_GET['client_id'].") 
                    UNION SELECT user_id FROM ".DB_NAME.".h_assessment_user WHERE assessment_id IN(".$_GET['assessment_id'].") 
                    UNION SELECT user_id from ".DB_NAME.".h_assessment_external_team WHERE assessment_id IN (".$_GET['assessment_id'].") and 
                    user_role='4' UNION SELECT t1.user_id FROM ".DB_NAME.".`d_user` t1 Left Join ".DB_NAME.".h_user_user_role t2 ON 
                    (t1.user_id=t2.user_id) WHERE t2.role_id IN (1,2,8)) GROUP BY t1.user_id;"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_user_user_role LIKE ".DB_NAME.".h_user_user_role;
                    INSERT INTO ".$newDBName.".h_user_user_role SELECT * FROM ".DB_NAME.".h_user_user_role Where user_id In (
                    Select user_id from ".$newDBName.".d_user);"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_assessment_user LIKE ".DB_NAME.".h_assessment_user;
                    INSERT INTO ".$newDBName.".h_assessment_user SELECT * from ".DB_NAME.".h_assessment_user where 
                    `assessment_id` IN (".$_GET['assessment_id'].");"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_assessment_external_team LIKE ".DB_NAME.".h_assessment_external_team;
                    INSERT INTO ".$newDBName.".h_assessment_external_team SELECT * from ".DB_NAME.".h_assessment_external_team where 
                    `assessment_id` IN (".$_GET['assessment_id'].");"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_aqsdata_itsupport LIKE ".DB_NAME.".h_aqsdata_itsupport;
                    INSERT INTO ".$newDBName.".h_aqsdata_itsupport SELECT * from ".DB_NAME.".h_aqsdata_itsupport where aqs_id IN (
                    Select`aqsdata_id` from ".DB_NAME.".d_assessment where `aqsdata_id`!='' and  client_id in (".$_GET['client_id'].") and 
                    assessment_id IN (".$_GET['assessment_id']."));"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_aqs_ngo LIKE ".DB_NAME.".h_aqs_ngo;
                    INSERT INTO ".$newDBName.".h_aqs_ngo SELECT * from ".DB_NAME.".h_aqs_ngo where AQS_data_id IN (Select`aqsdata_id` 
                    from ".DB_NAME.".d_assessment where `aqsdata_id`!='' and  client_id in (".$_GET['client_id'].") and 
                    assessment_id IN (".$_GET['assessment_id']."));"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_aqs_school_level LIKE ".DB_NAME.".h_aqs_school_level;
                    INSERT INTO ".$newDBName.".h_aqs_school_level SELECT * from ".DB_NAME.".h_aqs_school_level where AQS_data_id IN (
                    Select`aqsdata_id` from ".DB_NAME.".d_assessment where `aqsdata_id`!='' and  client_id in (".$_GET['client_id'].") and 
                    assessment_id IN (".$_GET['assessment_id']."));"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_aqs_workshop LIKE ".DB_NAME.".h_aqs_workshop;"    
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_assessment_ass_group LIKE ".DB_NAME.".h_assessment_ass_group;"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_assessment_report LIKE ".DB_NAME.".h_assessment_report;
                    INSERT INTO ".$newDBName.".h_assessment_report SELECT * from ".DB_NAME.".h_assessment_report where 
                    `assessment_id` IN (".$_GET['assessment_id'].");"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_client_cluster LIKE ".DB_NAME.".h_client_cluster;
                    INSERT INTO ".$newDBName.".h_client_cluster SELECT * FROM ".DB_NAME.".h_client_cluster where client_id IN (".$_GET['client_id'].");"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_client_product LIKE ".DB_NAME.".h_client_product;
                    INSERT INTO ".$newDBName.".h_client_product SELECT * FROM ".DB_NAME.".h_client_product where client_id IN (".$_GET['client_id'].");"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_cq_score LIKE ".DB_NAME.".h_cq_score;
                    INSERT INTO ".$newDBName.".h_cq_score SELECT * from ".DB_NAME.".h_cq_score Where `assessment_id` IN (".$_GET['assessment_id'].");"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_kpa_instance_score LIKE ".DB_NAME.".h_kpa_instance_score;
                    INSERT INTO ".$newDBName.".h_kpa_instance_score SELECT * from ".DB_NAME.".h_kpa_instance_score Where `assessment_id` IN (".$_GET['assessment_id'].");"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_kq_instance_score LIKE ".DB_NAME.".h_kq_instance_score;
                    INSERT INTO ".$newDBName.".h_kq_instance_score SELECT * from ".DB_NAME.".h_kq_instance_score Where `assessment_id` IN (".$_GET['assessment_id'].");"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".f_score LIKE ".DB_NAME.".f_score;
                    INSERT INTO ".$newDBName.".f_score SELECT * from ".DB_NAME.".f_score Where `assessment_id` IN (".$_GET['assessment_id'].");
                    ALTER TABLE ".$newDBName.".f_score AUTO_INCREMENT = ".$last_insert_id.";
            "
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_score_file LIKE ".DB_NAME.".h_score_file;
                    INSERT INTO ".$newDBName.".h_score_file SELECT * from ".DB_NAME.".h_score_file where `score_id` IN (
                        SELECT score_id from ".DB_NAME.".f_score WHERE assessment_id IN (".$_GET['assessment_id']."));"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".d_file LIKE ".DB_NAME.".d_file;
                    INSERT INTO ".$newDBName.".d_file SELECT * from ".DB_NAME.".d_file where 
                    file_id IN (Select`file_id` from ".$newDBName.".h_score_file);"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_sub_assessment_request LIKE ".DB_NAME.".h_sub_assessment_request;"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_transaction_assessment LIKE ".DB_NAME.".h_transaction_assessment;"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_user_role_user_capability LIKE ".DB_NAME.".h_user_role_user_capability;
                    INSERT INTO ".$newDBName.".h_user_role_user_capability SELECT * FROM ".DB_NAME.".h_user_role_user_capability;"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_user_teacher_attr LIKE ".DB_NAME.".h_user_teacher_attr;"
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".assessor_key_notes LIKE ".DB_NAME.".assessor_key_notes;
                    INSERT INTO ".$newDBName.".assessor_key_notes SELECT * from ".DB_NAME.".assessor_key_notes where 
                    `assessment_id` IN (".$_GET['assessment_id'].");"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_tap_user_assessment LIKE ".DB_NAME.".h_tap_user_assessment;
                    INSERT INTO ".$newDBName.".h_tap_user_assessment SELECT * FROM ".DB_NAME.".h_tap_user_assessment WHERE user_id IN (
                    Select user_id from ".$newDBName.".d_user);"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_user_profile LIKE ".DB_NAME.".h_user_profile;
                    INSERT INTO ".$newDBName.".h_user_profile SELECT * FROM ".DB_NAME.".h_user_profile WHERE user_id IN (
                    Select user_id from ".$newDBName.".d_user);"
                
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_user_language LIKE ".DB_NAME.".h_user_language;
                    INSERT INTO ".$newDBName.".h_user_language SELECT * FROM ".DB_NAME.".h_user_language WHERE user_id IN (
                    Select user_id from ".$newDBName.".d_user);"
            
            . " CREATE TABLE IF NOT EXISTS ".$newDBName.".h_network_report_clients LIKE ".DB_NAME.".h_network_report_clients;
                    INSERT INTO ".$newDBName.".h_network_report_clients SELECT * FROM ".DB_NAME.".h_network_report_clients Where
                    client_id IN (".$_GET['client_id'].");";
//        echo $SQL2;
//        die;
        if($objDB->query($SQL2)){
            echo 'database dump is created!';   
        } else {
            echo 'false';
        }        
    } else {
        echo 'false';
    }
}

ini_set("memory_limit",-1);

//backup_database(DB_HOST, DB_USER, DB_PASSWORD, "".$newDBName );

/* backup the db OR just a table */
echo '<pre>';
function backup_database($host, $user, $pass, $name, $tables = '*') {

    $objDBNew = new db($host, $name, $user, $pass);

    //get all of the tables
    if ($tables == '*') {
        $tables = array();
        $row = $objDBNew->get_results('SHOW TABLES');
        
        foreach ($row as $value) {
            $tables[] = $value['Tables_in_'.$name];
        }
        
    } else {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
    }
    
    $return='';
    //cycle through
    foreach ($tables as $table) {
        $result = ($objDBNew->get_results('SELECT * FROM ' . $table));
        $num_fields = count($result);
//        $result = mysqli_query($db,'SELECT * FROM ' . $table);
//        $num_fields = mysqli_num_fields($result);
        
        //$return.= 'DROP TABLE ' . $table . ';';
        $row2 = $objDBNew->get_row('SHOW CREATE TABLE ' . $table);
//        $row2 = mysqli_fetch_row(mysqli_query($db,'SHOW CREATE TABLE ' . $table));
        $return.= "\n\n" . $row2['Create Table'] . ";\n\n";
        
//        for ($i = 0; $i < $num_fields; $i++) {
//            while ($row = mysqli_fetch_row($result)) {
            foreach ($result as $key => $row1) {
                    
                $i=0;
                $return.= 'INSERT INTO ' . $table . ' VALUES(';
//                for ($j = 0; $j < $num_fields; $j++) {
                foreach ($row1 as  $row) {


    
                    $row = addslashes($row);
                    
                    //$row[$j] = ereg_replace("\n", "\\n", $row[$j]);
                   
                    if (isset($row)) {
                        $return.= '"' . $row . '"';
                    } else {
                        $return.= '""';
                    }
                    if ($i < ($num_fields - 1)) {
                        $return.= ',';
                    }
                    $i++;
                }
                $return.= ");\n";
                
            }
           
//        }
        $return.="\n\n\n";
    }
//     echo '<pre>';
//        print_r($return);
//        die;
    $filename = 'db-backup-' . time() . '-' . (md5(implode(',', $tables))) . '.sql';
    $file_names = $filename;
    //save file
    $handle = fopen($filename, 'w+');
    fwrite($handle, $return);
    fclose($handle);
    

    //Archive name
    $archive_file_name="".$newDBName.'.zip';

    //Download Files path
    $file_path=$_SERVER['DOCUMENT_ROOT'].'/';


    zipFilesAndDownload($file_names,$archive_file_name,$file_path);
}



function zipFilesAndDownload($file_names,$archive_file_name,$file_path)
{
    $file = $file_names;

    # create new zip opbject
    $zip = new ZipArchive();

    # create a temp file & open it
    $tmp_file = $file_path.$file_names;
    $zip->open($tmp_file, ZipArchive::CREATE);

    
        # download file
        $download_file = file_get_contents($file);

        #add it to the zip
        $zip->addFromString(basename($file),$download_file);

   

    # close zip
    $zip->close();

    # send the file to the browser as a download
    header('Content-disposition: attachment; filename='.$archive_file_name);
    header('Content-type: application/zip');
    readfile($tmp_file);
    unlink($file);
//    unlink($tmp_file);
    
}

