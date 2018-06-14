<?php 
foreach($tdiagnostics as $t)
{
	//print_r($t);
	$gteacherCategory[$t['teacher_category_id']] = $t['diagnostic_id'];
}
//print_r($assessment);
?>
<h1 class="page-title">
<a href="<?php
						$args=array("controller"=>"assessment","action"=>"assessment");		
						$args["filter"]=1;						
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						Manage MyReviews
					</a> >
                                        <?php
                                        if($tab2==1){
                                        ?>
                                        Create Teacher Review
                                        <?php
                                        }else{
                                        ?>
				 Edit Review - <?php echo $assessment['client_name']; ?>
                                        <?php } ?>
</h1>

<div>
	<div class="ylwRibbonHldr">
		<a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab4_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
		<div class="collapse navbar-collapse tabitemsHldr" id="tab4_Toggle">
			<ul class="yellowTab nav nav-tabs">          
				<li class="item active"><a href="#editTeacherAssessment-step1" data-toggle="tab" class="vtip" title="Edit Teacher Review">Step 1</a></li>
				<li class="item"><a href="#editTeacherAssessment-step2" data-toggle="tab" class="vtip" title="Manage Validators/Teachers" id="step2">Step 2</a></li>				
			</ul>
		</div>
	</div>
	<div class="subTabWorkspace pad26">	
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade in active" id="editTeacherAssessment-step1">
			<form id="editTeacherAssessment" method="post" onsubmit="return false;">
				<h2>Edit Teacher Review</h2>
				<div class="boxBody"><input type="hidden" name="gaid" value="<?php echo $gaid; ?>">
									<dl class="fldList">
										<dt>School<span class="astric">*</span>:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<span id="selected_client_name"><?php echo $assessment['client_name']; ?></span> &nbsp;													
													<input type="hidden" value="<?php echo $assessment['client_id']; ?>" autocomplete="off" id="selected_client_id" name="client_id" autocomplete="off">
												</div>
											</div>
										</dd>
									</dl>
																		
									<dl class="fldList">
										<dt>School Admin<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control school_admin_id" autocomplete="off" name="school_admin_id" required>
												<option value=""> - Select School Admin/Principal - </option>
												<?php
													foreach($schoolAdmins as $val)
													print $assessment['admin_user_id']==$val['user_id'] ? '<option selected="selected" value="'.$val['user_id'].'">'.$val['name'].'</option>':'<option value="'.$val['user_id'].'">'.$val['name'].'</option>';	
												?>
											</select>
										</div></div></dd>
									</dl>
									
									<dl class="fldList">
										<dt>External Reviewers<span class="astric">*</span>:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<div id="external_reviewers_block" data-trigger="tchrAsmtChanged" class="currentSelection tag_boxes clearfix">
														<span class="notEmpty">Nothing selected yet</span>
														<?php
															if(!empty($eassessors[1]))	
															foreach($eassessors[1] as $eassessor){?>
															<div title="<?php echo $eassessor['email']; ?>" class="eAssessorNode clearfix eAssessorNode-<?php echo $eassessor['user_id']; ?>" data-id="<?php echo $eassessor['user_id']; ?>">
															<?php echo $eassessor['name']; ?>
															<input type="hidden" class="ajaxFilterAttach" name="eAssessor[<?php echo $eassessor['user_id']; ?>]" value="<?php echo $eassessor['user_id']; ?>">
															<?php if($assessment['assessments_count']==0){ ?>
															<span class="delete"><i class="fa fa-times"></i></span>
															<?php }else if(!in_array($eassessor['user_id'], $used_reviewers)) { ?>
                                                                                                                        <span class="delete"><i class="fa fa-times"></i></span>
                                                                                                                        <?php } ?>
															</div>
														<?php } ?>
													</div>
													<a data-size="950" data-postdata="#editTeacherAssessment .eAssessorNode input" class="btn btn-danger vtip execUrl" href="?controller=user&amp;action=externalAssessorList&amp;ispop=1" title="Click to select external reviewers of different schools.">Select Reviewers</a>
												</div>
											</div>
										</dd>
									</dl>
								
									<dl class="fldList">
										<dt>Assign Diagnostic<span class="astric">*</span>:<br/><span class="astric" style="font-size:12px;">Please select at least one diagnostic.</span></dt>
										<dd>
										<?php
										foreach($category_diagnostics as $teacherCategory){
										?>
											<div class="row mb10">
												<div class="col-sm-3">
													<h5><?php echo $teacherCategory['teacher_category']; ?></h5>
												</div>
												<div class="col-sm-3">
                                                                                                        <?php
                                                                                                        if($teacherCategory['teacher_disable']==1 && $assessment['assessments_count']>0){
                                                                                                        ?>
                                                                                                    <input type="hidden"  class="diagnostic_dd" autocomplete="off" name="teacher_cat[<?php echo $teacherCategory['teacher_category_id']; ?>]" value="<?php echo isset($gteacherCategory[$teacherCategory['teacher_category_id']])?$gteacherCategory[$teacherCategory['teacher_category_id']]:''; ?>">  
                                                                                                        <?php
                                                                                                            }
                                                                                                        ?>
													<select class="form-control diagnostic_dd" autocomplete="off" name="teacher_cat[<?php echo $teacherCategory['teacher_category_id']; ?>]"  <?php if($teacherCategory['teacher_disable']==1 && $assessment['assessments_count']>0) echo "disabled='disabled'"; ?>>
														<option value=""> - Select Diagnostic - </option>
														<?php
														foreach($teacherCategory['category_diagnostic'] as $diagnostic)
															print (isset($gteacherCategory[$teacherCategory['teacher_category_id']]) && $gteacherCategory[$teacherCategory['teacher_category_id']]==$diagnostic['diagnostic_id'])? "<option selected='selected' value=\"".$diagnostic['diagnostic_id']."\">".$diagnostic['translation_text']."</option>\n":"<option value=\"".$diagnostic['diagnostic_id']."\">".$diagnostic['translation_text']."</option>\n";
														?>
													</select>
												</div>
											</div>
										<?php
										}
										?>
										</dd>
									</dl>
                                                                        <dl class="fldList">
										<dt>Round<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control" autocomplete="off" name="student_round" >
														<option value=""> - Select Round - </option>
												<?php
                                                                                                foreach($aqsRounds as $key=>$val){
                                                                                                    ?>
                                                                                                                <option value="<?php echo $val['aqs_round'] ?>" <?php if($val['aqs_round']==$assessment['student_round']) echo"selected" ?>><?php echo $val['aqs_round'] ?></option>          
                                                                                                    <?php            
                                                                                                }
                                                                                                ?>
											</select>
										</div></div></dd>
									</dl>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />			
								<?php //if($assessment['assessments_count']==0){ ?> 
                                                                <div class="text-right" style="padding-top:5px;">
								<input type="button" data-ignorerejected="0" title="Click to update review" value="Update" class="btn btn-primary vtip" id="updateTeacherAssessment" autocomplete="off">
								</div>
								<?php //} ?>
								</div>
			</form>					
			</div>
			<div role="tabpanel" class="tab-pane fade in" id="editTeacherAssessment-step2">
				<h2>Manage Validators/Teachers</h2>
			<div class="pad26 tag_box_wrap">
			<form method="post" id="tchrAssessorsForm" onsubmit="return false;">
                        <input type="hidden" name="r_type" id="r_type" value="teacher">     
			<div data-trigger="tchrAssessorsChanged" id="status_id_1-1" class="tag_boxes mb15 clearfix">
				<h2 class="mb10 small">External Validators</h2>
			<?php
			$addDeleteBtn=$teacherAssessment['assessmentAssigned']==0?1:0;
			$cnt=0;
			$currentAssessorsBySchool=array();
			if(isset($assessors[1])){
				$dBtn=$addDeleteBtn && $isAdmin?1:0;
				foreach($assessors[1] as $eUser){
                                    
					if($eUser['added_by_admin']==1){
                                                if(!in_array($eUser['user_id'], $used_reviewers) && $isAdmin) {
                                                echo userModel::getExternalAssessorNodeHtml($eUser,1,1);
                                                }else{
						echo userModel::getExternalAssessorNodeHtml($eUser,$dBtn,1);
                                                }
						$cnt++;
					}else
						$currentAssessorsBySchool[]=$eUser;
				}
			}
			?>
				<span class="empty <?php echo $cnt>0?'notEmpty':''; ?>">List is empty</span>
				<input type="hidden" name="taid" id="taid" value="<?php echo $gaid; ?>">
			</div>
			
			<div data-trigger="tchrAssessorsChanged" id="status_id_1-0" class="tag_boxes mb15 clearfix">
				<h2 class="mb10 small">Selected Internal Validators</h2>
			<?php
			foreach($currentAssessorsBySchool as $eUser){
                                if(!in_array($eUser['user_id'], $used_reviewers)) {
                                echo userModel::getExternalAssessorNodeHtml($eUser,1,1);
                                }else{
				echo userModel::getExternalAssessorNodeHtml($eUser,$addDeleteBtn,1);
                                }
			}
			?>
				<span class="empty <?php echo count($currentAssessorsBySchool)>0?'notEmpty':''; ?>">List is empty</span>
			</div>
			
			<div data-trigger="tchrAssessorsChanged" id="status_id_2" class="tag_boxes mb15 clearfix">
				<h2 class="mb10 small">Internal Validators - Waiting for Approval 
				<?php //if($isAdmin && $teacherAssessment['assessmentAssigned']==0){ 
                                if($isAdmin){ 
                                ?>
					<button class="btn btn-primary pull-right small-btn approve_eAssessorNode">Approve</button>
					<button class="btn btn-primary pull-right small-btn mr10 reject_eAssessorNode">Reject</button>
					<button class="btn btn-primary pull-right small-btn mr10 selectAll_eAssessorNode">Select all</button>
				<?php } ?></h2>
			<?php
			$cnt=0;
			if(isset($assessors[2])){
				foreach($assessors[2] as $eUser){
                                        if(!in_array($eUser['user_id'], $used_reviewers)) {
                                        echo userModel::getExternalAssessorNodeHtml($eUser,1,2);
                                        }else{
					echo userModel::getExternalAssessorNodeHtml($eUser,$addDeleteBtn,2);
                                        }
					$cnt++;
				}
			}
			?>
				<span class="empty <?php echo $cnt>0?'notEmpty':''; ?>">List is empty</span>
			</div>
			
			<div data-trigger="tchrAssessorsChanged" id="status_id_3" class="tag_boxes mb15 clearfix">
				<h2 class="mb10 small">Rejected Internal Validators <?php //if($isAdmin && $teacherAssessment['assessmentAssigned']==0){ 
                                if($isAdmin){
                                ?><button class="btn btn-primary pull-right small-btn approve_eAssessorNode">Approve</button> <button class="btn btn-primary pull-right small-btn mr10 selectAll_eAssessorNode">Select all</button><?php } ?></h2>
			<?php
			$cnt=0;
			if(isset($assessors[3])){
				foreach($assessors[3] as $eUser){
                                        if(!in_array($eUser['user_id'], $used_reviewers)) {
                                        echo userModel::getExternalAssessorNodeHtml($eUser,1,3);
                                        }else{
					echo userModel::getExternalAssessorNodeHtml($eUser,$addDeleteBtn,3);
                                        }
					$cnt++;
				}
			}
			?>
				<span class="empty <?php echo $cnt>0?'notEmpty':''; ?>">List is empty</span>
			</div>
		<?php //if($teacherAssessment['assessmentAssigned']==0){ ?>
                        <div class="tag_boxes mb15 clearfix">    
			<h2 class="mb20 small pl10">
				<span class="collapseGA vtip fa fa-plus-circle" title="Add Internal Validators" id="collapseA"></span> Add External Validators (Optional)
				<a title="Upload reviewer list" class="btn btn-primary pull-right execUrl vtip small-btn" href="?controller=assessment&amp;action=assessorListUpload&taid=<?php echo $teacherAssessment['group_assessment_id']; ?>&amp;ispop=1"  id="up_assessor"  style="display:none;">Upload List</a>
			</h2>
			<div class="assessor_show_hide collapse">
			<div data-trigger="tchrAssessorsChanged" class="tableHldr teamsInfoHldr teacherAssessor_team team_table">
				<a href="javascript:void(0)" class="fltdAddRow" data-type="teacherAssessor"><i class="fa fa-plus"></i></a>
				<table class="table customTbl">
					<thead>
						<tr>
							<th>Sr. No.</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Email</th>
							<th>Date of Joining</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php echo assessmentModel::getTeacherAssessorHTMLRow(1,'','','','',1); ?>
					</tbody>
				</table>
			</div>
                            
                        <div style="padding-bottom:50px;">
			<?php //if($teacherAssessment['assessmentAssigned']==0){ ?>
			<button type="button" class="btn btn-primary pull-right vtip" data-ignorerejected="0"  disabled="disabled" title="Save Validators" id="saveTchrAssessorsForm">
Save Validators</button>
                        <?php //} ?>
                    </div>
                    </div></div>        
		<?php //} ?>
                <div class="clearfix"></div>
                <div class="tag_boxes mb15 clearfix">
                    <div class="teachercolor tag_boxes mb15 clearfix">
                        <h2 class="mb20 small pl10"><span class="collapseGA vtip fa fa-minus-circle" title="Add/Edit/Remove Teacher" id="collapseT"></span> Add/Edit/Remove Teacher
				<?php //if($teacherAssessment['assessmentAssigned']==0){ ?>
                            <a title="Upload teacher list" class="btn btn-primary small-btn pull-right execUrl vtip " id="up_teacher" href="?controller=assessment&amp;action=assessorListUpload&type=teacher&taid=<?php echo $teacherAssessment['group_assessment_id']; ?>&amp;ispop=1">Upload List</a>
                                <?php //} ?>
			</h2>
                      <div class="teacher_show_hide">
                        <div data-trigger="tchrsForAssessmentChanged" class="tableHldr teamsInfoHldr teacher_team team_table">
				<?php //if($teacherAssessment['assessmentAssigned']==0){ ?>
                            <a href="javascript:void(0)" class="fltdAddRow" data-attach="<?php echo $teacherAssessment['group_assessment_id']; ?>" data-type="teacherForAssessment"><i class="fa fa-plus"></i></a>
                                <?php //} ?>
				<table class="table customTbl">
					<thead>
						<tr>
							<th>Sr. No.</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Email</th>
							<th>Date of Joining</th>
                                                        <th>Department</th>
							<th>Category</th>
							<th>Validator</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
                                                //print_r($teachers);
						$i=0;
						$allApproved=1;
						global $t_assessor_list;
						foreach($teachers as $teacher){
							$i++;
							$nm=explode(" ",$teacher['name'],2);
							echo $assessmentModel->getTeacherInTeacherAssessmentHTMLRow($teacher['group_assessment_id'],$i,$nm[0],isset($nm[1])?$nm[1]:'',$teacher['email'],$teacher['doj'],$teacher['school_level_id'],$teacher['teacher_category_id'],$teacher['assessor_id'],$teacher['teacher_id'],1);
							if(isset($t_assessor_list[$teacher['assessor_id']]) && $t_assessor_list[$teacher['assessor_id']]['status_id']!=1)
								$allApproved=0;
						}
						echo count($teachers)>0 || $teacherAssessment['assessmentAssigned']>0?'':$teacherAssessment['assessmentAssigned']==0?$assessmentModel->getTeacherInTeacherAssessmentHTMLRow($teacherAssessment['group_assessment_id'],$i+1,'','','','',0,0,0,0,1):'';
						?>
					</tbody>
				</table>
                        
                        </div>
                      <div style="padding-bottom:50px;">       
                        <?php if($teacherAssessment['assessmentAssigned']==0){ ?>
                        <button type="button" class="btn btn-primary pull-right vtip" data-ignorerejected="0"  disabled="disabled" title="Save/ Update Teachers" id="saveTchrsForAssessmnt">Save Teachers</button>
		      
		       <?php
                       }else{
                        ?>
                        <button type="button" class="btn btn-lg btn-primary pull-right vtip" data-ignorerejected="0" title="Update Review" id="saveTchrsForAssessmnt">Update Review</button>
		      
                        <?php
                       }
                       ?></div> 
                      </div>
                           
                      
                    </div></div>
                
		</div>
                              
                                
		<div class="ajaxMsg"></div>
		<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
		<div class="fr clearfix">
			<?php  if($teacherAssessment['assessmentAssigned']==0){ ?><input type="button" autocomplete="off" data-ignorerejected="0" id="submitTchrsForAssessmnt" <?php echo count($teachers)>0 && $allApproved?"":'disabled="disabled"'; ?> class="fl nuibtn submitBtn" value="Submit" /><!--<input type="button" title="Click to update external reviewers" data-ignorerejected="0" value="Update" class="btn btn-primary vtip" id="saveTchrAssessorsForm" autocomplete="off">--><?php } ?>			
		</div>
                <div class="clr"></div>
		</form>
			</div>
		</div>
		
	</div>	
	
</div>
<?php
if($tab2==1){
?>
<script>
$('#step2').trigger('click');  
</script>
<?php
}
?>

<script>
    $('.teacher_team .date-Picker').on('dp.change', function(e){
    //alert("dsdgggdd");
    $("#saveTchrsForAssessmnt").removeAttr("disabled");
        $("#submitTchrsForAssessmnt").attr('disabled', "disabled");
        $("#vtip").remove();
    });
    
    $('.teacherAssessor_team .date-Picker').on('dp.change', function(e){
    //alert("dsdggg");
        $("#saveTchrAssessorsForm").removeAttr("disabled");
        $("#submitTchrsForAssessmnt").attr('disabled', "disabled");
        $("#vtip").remove();
    });
</script>