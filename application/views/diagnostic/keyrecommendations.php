<h4 class="page-title row">Key Recommendations</h4> 
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
                                            echo $diagnosticModel::getAssessorKeyNoteHtmlRow($kpa7,$instance_id, $akn_id, $akn['text_data'], '', $isReadOnly, (!$isReadOnly && $akn_count > 0 ? 1 : 0));
                                            $akn_count++;
                                        } else {
                                        $akn_id = $diagnosticModel->addAssessorKeyNote($assessment_id,$type.'_instance_id', $instance_id, '');
                                        echo $diagnosticModel::getAssessorKeyNoteHtmlRow($kpa7,$instance_id, $akn_id, '', '', $isReadOnly, 0);
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
                                        <h4><i class="fa fa-question-circle"></i>Celebrate</h4>
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
                                            echo $diagnosticModel::getAssessorKeyNoteHtmlRow($kpa7,$instance_id, $akn_id, $akn['text_data'], 'celebrate', $isReadOnly, (!$isReadOnly && $akn_count > 0 ? 1 : 0));
                                            $akn_count++;
                                        } else {
                                        $akn_id = $diagnosticModel->addAssessorKeyNote($assessment_id, $type.'_instance_id',$instance_id, '', "celebrate");
                                        echo $diagnosticModel::getAssessorKeyNoteHtmlRow($kpa7,$instance_id, $akn_id, '', 'celebrate', $isReadOnly, 0);
                                    }
                                    ?>

                                    <div class="clearfix">
                                        <div class="pull-right"><?php
                                            if (!$isReadOnly) {
                                                ?>
                                                <div class="clearfix addKeyN-wrap alnR8Btn"><button class="pull-right addKeyNote vtip" data-type='recommendation' title="Click to add more Recommendations."><i class="fa fa-plus"></i></button></div>
                                        <?php
                                    }
                                    ?></div>
                                        <h4><i class="fa fa-question-circle"></i>Recommendations</h4>
                                    </div>
                                    <?php
                                    $akn_count = 0;
                                    if($type=="kpa" && $assessment['assessment_type_id']==1){
                                      $getJSforKPA=$diagnosticModel->getJSforKPA($instance_id,$lang_id);
                                      //print_r($getJSforKPA);
                                      
                                      if (!empty($recommendationKN) && count($recommendationKN))
                                        foreach ($recommendationKN as $k => $akn) {
                                        	$akn_id = $akn['id'];
                                            echo $diagnosticModel::getAssessorKeyNoteHtmlRow($kpa7,$instance_id, $akn_id, $akn['text_data'], 'recommendation', $isReadOnly, (!$isReadOnly && $akn_count > 0 && $deletestatus==1 ? 1 : 0),$type,$getJSforKPA,explode(",",$akn['rec_judgement_instance_id']));
                                            $akn_count++;
                                        } else {
                                        $akn_id = $diagnosticModel->addAssessorKeyNote($assessment_id, $type.'_instance_id', $instance_id, '', "recommendation");
                                        echo $diagnosticModel::getAssessorKeyNoteHtmlRow($kpa7,$instance_id, $akn_id, '', 'recommendation', $isReadOnly, 0,$type,$getJSforKPA);
                                    }  
                                        
                                    }else{
                                    if (!empty($recommendationKN) && count($recommendationKN))
                                        foreach ($recommendationKN as $k => $akn) {
                                        	$akn_id = $akn['id'];
                                            echo $diagnosticModel::getAssessorKeyNoteHtmlRow($kpa7,$instance_id, $akn_id, $akn['text_data'], 'recommendation', $isReadOnly, (!$isReadOnly && $akn_count > 0 ? 1 : 0));
                                            $akn_count++;
                                        } else {
                                        $akn_id = $diagnosticModel->addAssessorKeyNote($assessment_id, $type.'_instance_id', $instance_id, '', "recommendation");
                                        echo $diagnosticModel::getAssessorKeyNoteHtmlRow($kpa7,$instance_id, $akn_id, '', 'recommendation', $isReadOnly, 0);
                                    }
                                }
                                
                                }
                                ?>
        <?php if(!$isReadOnly){?>                        
 		<div class="text-right mb10 mt10">	
 			<button type="submit" class="btn btn-primary" id="btn_kr_save"><i class="fa fa-floppy-o"></i>Save</button>
 		</div>	
 		<?php }?>
</form> 	
</div>	
 </div>    
 <script type="text/javascript">
    $(document).ready(function() {     
        $(".vertScrollArea").mCustomScrollbar({theme:"dark"});
        $('#popup-diagnostic_keyrecommendations').find('.modal-dialog').addClass('modal-lg');
    });
</script>