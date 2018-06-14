<?php
//$json ='{"d_aqs_additional_questions":{"id":"school_community_id","label":"School communities predominantly:","type":"checkbox"}}';
//$json=json_encode($json);
$form_elements['d_aqs_additional_questions'][] = array("id"=>"school_community_id","attr"=>"school_community_text","label"=>"School communities predominantly:","type"=>"checkbox");
$form_elements['d_aqs_additional_questions'][] = array("id"=>"review_medium_instrn_id","attr"=>"d_review_medium_text","label"=>"Medium of Instruction:","type"=>"checkbox");
$form_elements['d_aqs_additional_questions'][] = array("id"=>"quintile","label"=>"Quintile (socio-economic class):","type"=>"text");
$form_elements['d_aqs_additional_questions'][] = array("id"=>"total_teachers","label"=>"Total number of teachers:","type"=>"number");
$form_elements['d_aqs_additional_questions'][] = array("id"=>"support_staff_arr","label"=>"Total number of support staff:","type"=>"nested",
		"elements"=>array(
				array("id"=>"total_support_staff_admin","label"=>"Admin","type"=>"number"),
				array("id"=>"total_support_staff_finance","label"=>"Finance","type"=>"number"),
				array("id"=>"total_support_staff_maintenance","label"=>"Caretaking/Maintenance","type"=>"number")
		));
$form_elements['d_aqs_additional_questions'][] = array("id"=>"teacher_learner_ratio","label"=>"Teacher/Learner ratio:","type"=>"text");
$form_elements['d_aqs_additional_questions'][] = array("id"=>"total_teachers","label"=>"Total number of teachers:","type"=>"number");
$form_elements['d_aqs_additional_questions'][] = array("id"=>"sections_classes","label"=>"Number of sections per standard and the total number of classes in the school:","type"=>"text");
$form_elements['d_aqs_additional_questions'][] = array("id"=>"learners_largest_class","label"=>"Number of learners in the largest class:","type"=>"number");
$form_elements['d_aqs_additional_questions'][] = array("id"=>"learners_smallest_class","label"=>"Number of learners in the smallest class:","type"=>"number");
$form_elements['d_aqs_additional_questions'][] = array("id"=>"deputy_principal_data","label"=>"Name of Deputy Principal and direct email:","type"=>"row",
		"elements"=>array(
				array("id"=>"deputy_principal_name","type"=>"text","class"=>''),
				array("id"=>"deputy_principal_email","type"=>"email","class"=>"haveIcon", "icon"=>"fa fa-envelope")
		
		));
$form_elements['d_aqs_additional_questions'][] = array("id"=>"management_commitee_chairman_data","label"=>"Name of School Management Committee Chair and email:","type"=>"row",
		"elements"=>array(
				array("id"=>"management_commitee_chairman","type"=>"text","class"=>''),
				array("id"=>"management_commitee_chairman_email","type"=>"email","class"=>"haveIcon", "icon"=>"fa fa-envelope")

		));
$form_elements['d_aqs_additional_questions'][] = array("id"=>"contact_provincial_data","label"=>"Name of main contact with provincial administration and email, if applicable:","type"=>"row",
		"elements"=>array(
				array("id"=>"contact_provincial_admin","type"=>"text","class"=>''),
				array("id"=>"contact_provincial_email","type"=>"email","class"=>"haveIcon", "icon"=>"fa fa-envelope")

		));
