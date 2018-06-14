<?php /* <h4 class="page-title row"></h4> 
<?php 
$oldKN = array();
if(!empty($akns)){
$oldKN = array_filter($akns, function($var) {
	if ($var['type'] == '' || $var['type'] == NULL)
		return $var;
}) ;
}
$action_planning_status=isset($assessment['action_planning_status'])?$assessment['action_planning_status']:0;
$deletestatus=$action_planning_status==0?1:0;
//echo 'aa';print_r($akns);
//echo $instance_id;
//print_r($oldKN);
//die;
//print_r($assessment);
?>
<div class="ylwRibbonHldr">
        <div class="tabitemsHldr">&nbsp;</div>
    </div>
 <div class="subTabWorkspace pad26 sortable-form" role="document" id="<?php echo 'kr_'.$assessment_id.'_'.$type.'__instance_id_'.$instance_id; ?>">
 <div class="vertScrollArea">
     <?php  
     foreach($res_new as $mostly_statements){?>
        <span style="font-weight: bold;"> Judgement Statement:</span><span><?php echo $mostly_statements['judgement_statement_text1']; echo '<br>';?></span>
         <?php echo str_replace(array('*','Mostly:'),array('<br><span>-</span>',''),$mostly_statements['translation_text']); echo '<br>';echo '<br>';
        
         
     }
   
   
            ?>
 <form id="key_notes_frm">
 <input type="hidden" name='assessment_id' id="assessment_id" value="<?php echo $assessment_id?>" />
 <input type="hidden" name='level_name' id="id_level_name" value="<?php echo $type?>" />
 <input type="hidden" name='level_type' id="id_level_type" value="<?php echo $type.'_instance_id'?>" />
 <input type="hidden" name='instance_id' id='id_instance_id' value="<?php echo $instance_id; ?>" /> 
 <input type="hidden" name='qhref' id='id_qhref' value="<?php echo $sourceLink.$instance_id; ?>" /> 
 <input type="hidden" name="tab_type_kn" value="<?php echo $tab_type_kn; ?>" id="id_tab_type_kn">
  <input type="hidden" name='sourcetype' id='id_sourcetype' value="<?php echo $sourceLink; ?>" /> 
  <input type="hidden" name='external' id='external' value="<?php echo $external; ?>" /> 
  <input type="hidden" name='is_collaborative' id='is_collaborative' value="<?php echo $is_collaborative; ?>" /> 
  <input type="hidden" name='assessor_id' id='assessor_id' value="<?php echo $assessor_id; ?>" /> 
 
  
  
  <?php
                                if (!empty($oldKN)) {
                                    if (!$isReadOnly) {
                                        ?>
                                        <div class="clearfix addKeyN-wrap onlyBtn alnR8Btn"><button class="pull-right addKeyNote vtip" data-type=''  title="Click to add more Keynotes."><i class="fa fa-plus"></i></button></div>
                                        <?php
                                    }
                                    $akn_count = 0;
                                    if (isset($akns) && count($akns))
                                        foreach ($akns as $akn_id => $akn) {
                                            echo $diagnosticModel::getAssessorKeyNoteHtmlRow($instance_id, $akn_id, $akn['text_data'], '', $isReadOnly, (!$isReadOnly && $akn_count > 0 ? 1 : 0));
                                            $akn_count++;
                                        } else {
                                        $akn_id = $diagnosticModel->addAssessorKeyNote($assessment_id,$type.'_instance_id', $instance_id, '');
                                        echo $diagnosticModel::getAssessorKeyNoteHtmlRow($instance_id, $akn_id, '', '', $isReadOnly, 0);
                                    }
                                } else {
                                   ?>

                                    <div class="clearfix">
                                        <div class="pull-right"><?php
                        if (isset($isReadOnly) && !$isReadOnly) {
                                        ?>
                                                <div class="clearfix addKeyN-wrap alnR8Btn"><button class="pull-right addKeyNote vtip" data-type='celebrate' title="Click to add more Celebrate points."><i class="fa fa-plus"></i></button></div>
                                                <?php
                                            }
                                            ?></div>
                                        
                                    </div>

                                    <?php
                                     
                                    $celebrateKN = isset($akns) ? array_filter($akns, function($var) {                                    
                                                if ($var['type'] == 'celebrate')
                                                    return $var;                                               
                                            }) : '';
                                    $recommendationKN = isset($akns) ? array_filter($akns, function($var) {
                                                if ($var['type'] == 'recommendation')
                                                    return $var;
                                            }) : '';

                                    $akn_count = 0;
                                   // echo 'aaaadfdsfsdfdsf: '.count($recommendationKN);
                                    if (!empty($celebrateKN) && count($celebrateKN))
                                        foreach ($celebrateKN as $k => $akn) {
                                        	$akn_id = $akn['id'];                                        	
                                            echo $diagnosticModel::getAssessorKeyNoteHtmlRow($instance_id, $akn_id, $akn['text_data'], 'celebrate', $isReadOnly, (!$isReadOnly && $akn_count > 0 ? 1 : 0));
                                            $akn_count++;
                                        } 
                                    ?>

                                    <div class="clearfix">
                                        <div class="pull-right"><?php
                                            
                                    ?></div>
                                       
                                    </div>
                                    <?php
                                    $akn_count = 0;
                                    
                                
                                }
                                ?>
 	
</form> 	
</div>	
 </div>    
 <script type="text/javascript">
    $(document).ready(function() {     
        $(".vertScrollArea").mCustomScrollbar({theme:"dark"});
        $('#popup-diagnostic_keyrecommendations').find('.modal-dialog').addClass('modal-lg');
    });
</script>
*/?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<style type="text/css">
	body{background-color: #d1c382;font-family: 'Open Sans', sans-serif;}
	.navbar-brand{height: auto;}
	.navbar-brand img {
    height: 73px;
    width: auto;
	}
	h2{font-size: 16px; line-height: 24px; font-weight: 700; color: #000; padding-top: 20px; margin: 0;}
	.statementscrollarea {
	    font-size: 14px;
	    line-height: 24px;
	    font-weight: 400;
	    color: #000;
	    /*max-height: 450px;
	   	overflow: hidden;*/
	    overflow-y: auto;
	}
	@media(max-width: 767px){
		.navbar-brand img {
	    height: 50px;	
		}
	}
	
</style>
<body>

<header>
	<div class="container clearfix">
		<div class="navbar-brand logo"><a href="http://localhost/Adhyayan/"><img src="	public/images/logo.png" alt="Logo - Adhyayan"></a></div>
	</div>

</header>
<section>
	<div class="container clearfix">
		<h2>What "good" looks like statements</h2>
		<div class="statementscrollarea">
 	    <?php  
            foreach($res_new as $mostly_statements){?>
            <span style="font-weight: bold;"> Judgement Statement:</span><span><?php echo $mostly_statements['judgement_statement_text1']; echo '<br>';?></span>
                <?php echo str_replace(array('*','Mostly:'),array('<br><span>&#9656;</span>',''),$mostly_statements['translation_text']); echo '<br>';echo '<br>';
            } 
           ?>
        </div>
	</div>
</section>

</body>
</html>

