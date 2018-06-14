<h1 class="page-title">
<?php
if($isPop!=1){
?>
<a href="<?php
						$args=array("controller"=>"index","action"=>"index");	
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
					
				Manage</a> > 
<?php
}
//print_r($student_teacher);
$show_button=0;
?>
                                Export to Excel > Export Evidence data into Excel
</h1>
<!--<h1 class="page-title">
		Export Evidence data into Excel
	</h1>-->
	<div class="clr"></div>
	
	<div class="">
		<div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
		</div>
            
		<div class="subTabWorkspace pad26">
                    <div class="form-stmnt">
                    <?php
                    if($gaid>0){
                    ?>
                        <form enctype="multipart/form-data" method="post" id="create_evidence_data_form" action="?controller=exportExcel&action=evidenceData&gaid=<?php echo $gaid; ?>">
                   
                    <?php
                    }else{
                    ?>
                      <form enctype="multipart/form-data" method="post" id="create_evidence_data_form" action="?controller=exportExcel&action=evidenceData">
                   
                     <?php
                    }
                    if($gaid>0){
                        //echo"<pre>";
                        //print_r($group_assessment_details);
                        //echo"</pre>";
                        $external_validators=array();
                        foreach($group_assessment_details as $key=>$val){
                            if($val['data_by_role']['3']['status']==1){
                            $external_validators[]= $val['data_by_role']['4']['user_id'];
                            }
                        }
                     if(in_array($user['user_id'],$external_validators) || in_array("manage_all_users",$user['capabilities'])){   
                      $show_button=1;
                         ?>
                          
                    <input type="hidden" name="assessment_type"  value="<?php echo $group_assessment_details[0]['assessment_type_id'] ?>">
                    
                    <input type="hidden" name="school_related_to"  value="3"> 
                    <input type="hidden" name="report_view"  value="2">
                    <input type="hidden" name="gaid"  value="<?php echo $gaid ?>">
                    
                    <dl id="rec_teacher_student" class="fldList">
                            <dt>School/Centre:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <?php echo $group_assessment_details[0]['client_name']; ?>
                                    </div>
                                    
                                </div>
                            </dd>
                    </dl>
                    <br>
                    <dl id="rec_teacher_student" class="fldList">
                            <dt>Teacher/Student:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control province-list-dropdown" name="teacher_student[]" id="teacher_student" multiple="multiple">
                                        <?php
                                        foreach($student_teacher as $key=>$val){
                                        ?>
                                            <option value="<?php echo $val['user_id'] ?>" selected="selected"><?php echo $val['name'] ?></option>
                                        <?php
                                        }
                                        ?>
                                        </select>
                                    </div>
                                    
                                </div>
                            </dd>
                    </dl>
                    <script>
                        $(function () {
                               $('#teacher_student').multiselect({
                                enableFiltering: true,
                                includeSelectAllOption: true,
                                buttonWidth: '420px',
                                maxHeight: 210,
                                templates: {
                                    ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                                   },
                            });
                            $(".caret").css('float', 'right');
                    $(".caret").css('margin', '8px 0');
                      });
                      
                    </script>
                    <?php
                    }else{
                      echo"Not Authorised to view this page";
                    }
                    }else if(in_array("manage_all_users",$user['capabilities'])){
                        $show_button=1;
                    ?>
                    <dl id="schools_type"  class="fldList">
                            <dt>Assessment Type<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control" id="assessment_type" name="assessment_type">
                                            <option value="self">Self Review</option>
                                            <?php foreach($assessmentTypes as $types) { ?>
                                                <option value="<?php echo $types['assessment_type_id'];?>" ><?php echo $types['assessment_type_name'];?></option>
                                            <?php }?>
                                            
                                        </select>
                                    </div>
                                </div>    
                            </dd>
                    </dl>
                    <dl id="schools_type"  class="fldList">
                            <dt>Schools Related To<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control" id="school_related_to" name="school_related_to">
                                            <option value="1" selected="selected">Network</option>
                                            <option value="2">Non Network </option>
                                            <option value="3">All</option>
                                            
                                        </select>
                                    </div>
                                </div>    
                            </dd>
                    </dl>
                     <dl class="fldList">
                            <dt>Round:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control" id="round" name="round">
                                            <option value="">All Rounds</option>
                                           <?php
                                           foreach($aqsRounds as $key=>$val){
                                            ?>
                                            <option value="<?php echo $val['aqs_round'] ?>"><?php echo $val['aqs_round'] ?></option>
                                            <?php
                                           }
                                           ?>
                                            
                                        </select>
                                    </div>
                                </div>    
                            </dd>
                    </dl>
                    <dl id="networks"  class="fldList">
                            <dt>Network:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control" id="rec_network" name="network[]" multiple="multiple">
                                          
                                            <?php
                                            foreach ($networks as $network)
                                                echo "<option value=\"" . $network['network_id'] . "\">" . $network['network_name'] . "</option>\n";
                                                //echo "<option value=\"" . 'all' . "\">" . 'ALL' . "</option>\n";
                                            ?>
                                        </select>
                                    </div>
                            </dd>
                        </dl>
                        <dl id="provinces" style="display:none;" class="fldList">
                            <dt>Province:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control province-list-dropdown" name="province[]" id="rec_provinces" multiple="multiple">
                                            														
                                        </select>
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <dl id="rec_schools" style="display:none;" class="fldList">
                            <dt>Schools:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control province-list-dropdown" name="school[]" id="evd_school" multiple="multiple">
                                            														
                                        </select>
                                    </div>
                                    <!--<div class="col-sm-3 width-50-modal">
                                            <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                    </div>-->
                                </div>
                            </dd>
                        </dl>
                        
                        <dl id="rec_teacher_student" style="display:none;" class="fldList">
                            <dt>Teacher/Student:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control province-list-dropdown" name="teacher_student[]" id="teacher_student" multiple="multiple">
                                            														
                                        </select>
                                    </div>
                                    
                                </div>
                            </dd>
                        </dl>
                         
                         <dl id="rec_view" style="display:none;" class="fldList">
                            <dt>Report View:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control" id="report_view" name="report_view">
                                            <option value="1">Single Sheet View</option>
                                            <option value="2">Multiple Sheet View</option>
                                        </select>
                                    </div>
                                    
                                </div>
                            </dd>
                        </dl>
                        <?php
                        }else{
                            echo"Not Authorised to view this page";
                        }
                        ?>
                        <?php
                        if($show_button==1){
                        ?>
                        <dl class="fldList">
                            
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                        <div id="errors" style=" display: none;"></div>
                        <input type="submit" name="submitevidencedata" value="Download Evidence Data" class="btn btn-primary mt25 mb30">
			<!--<a class="btn btn-primary mt25 mb30" href="?controller=exportExcel&amp;action=downloadEvidenceDataExcel">Download Evidence Data</a>-->
                        </div>
                                    <!--<div class="col-sm-3 width-50-modal">
                                            <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                    </div>-->
                                </div>
                            </dd>
                        </dl>
			<br>
                        <div class="row"><div class="col-sm-1"></div>
                        <div class="ajaxMsg" id="createresource"></div>
			<small><b>Note:-</b> <i>Evidence Report will be generated 'only for reviews' whose evidence data is filled.</i></small>
                        
                        </div>
                        <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                        <?php
                        }
                        ?>
                     </form>
                    </div>
		</div>
	</div>
        <?php if(isset($error)){
        ?>
     <script>
        $(".ajaxMsg").show();
        $( ".ajaxMsg" ).addClass( "danger active" );
        $(".ajaxMsg").html('<?php echo $error; ?>');
        $(".ajaxMsg").delay(2000).fadeOut();
      </script>
        <?php
        } ?>