<?php
$addDeleteBtn=$teacherAssessment['assessmentAssigned']==0?1:0;
?>
	<h1 class="page-title">
		<a href="<?php
			$args=array("controller"=>"assessment","action"=>"assessment");
			echo createUrl($args); 
			?>">
			<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
		</a>
		<?php echo $teacherAssessment['client_name']; ?> <big>&#8594;</big> Manage Validators/Students
	</h1>
	<form method="post" id="tchrAssessorsForm" onsubmit="return false;">
        <input type="hidden" name="r_type" id="r_type" value="student">
		<div class="subTabWorkspace pad26 tag_box_wrap">
                    <div>
			<div data-trigger="tchrAssessorsChanged" id="status_id_1-1" class="tag_boxes mb15 clearfix">
				<h2 class="mb10 small">External Validators</h2>
			<?php
			$cnt=0;
			$currentAssessorsBySchool=array();
			if(isset($assessors[1])){
				$dBtn=$addDeleteBtn && $isAdmin?1:0;
				foreach($assessors[1] as $eUser){
					if($eUser['added_by_admin']==1){
						//echo userModel::getExternalAssessorNodeHtml($eUser,$dBtn,1);
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
			</div>
			
			<div data-trigger="tchrAssessorsChanged" id="status_id_1-0" class="tag_boxes mb15 clearfix">
				<h2 class="mb10 small">Selected Internal Validators</h2>
			<?php
			foreach($currentAssessorsBySchool as $eUser){
				//echo userModel::getExternalAssessorNodeHtml($eUser,$addDeleteBtn,1);
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
					//echo userModel::getExternalAssessorNodeHtml($eUser,$addDeleteBtn,2);
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
					//echo userModel::getExternalAssessorNodeHtml($eUser,$addDeleteBtn,3);
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
                        <span class="collapseGA vtip fa fa-plus-circle" title="Add Internal Validators" id="collapseA"></span> Add Internal Validators (Optional)
                        <a title="Upload reviewer list" class="btn btn-primary pull-right execUrl vtip small-btn" href="?controller=assessment&amp;action=assessorListUpload&taid=<?php echo $teacherAssessment['group_assessment_id']; ?>" id="up_assessor" style="display:none;">Upload List</a>
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
                       </div>
                   </div>
                    <?php
                   // }
                    ?>
                    </div>
                    <div class="clearfix"></div>
                    <?php //if($teacherAssessment['assessmentAssigned']==0){ ?>
                    <div class="tag_boxes mb15 clearfix">
                    <div class="teachercolor tag_boxes mb15 clearfix">
                        <h2 class="mb20 small pl10"><span class="collapseGA vtip fa fa-minus-circle" title="Add/Edit/Remove Student" id="collapseT"></span> Add/Edit/Remove Student
				<?php //if($teacherAssessment['assessmentAssigned']==0){ ?><a title="Upload student list" class="btn btn-primary small-btn pull-right execUrl vtip " id="up_teacher" href="?controller=assessment&amp;action=assessorListUpload&type=student&taid=<?php echo $teacherAssessment['group_assessment_id']; ?>&amp;ispop=1">Upload List</a><?php //} ?>
			</h2>
                      <div class="teacher_show_hide">
                        <div data-trigger="tchrsForAssessmentChanged" class="tableHldr teamsInfoHldr teacher_team team_table">
				<?php //if($teacherAssessment['assessmentAssigned']==0){ ?><a href="javascript:void(0)" class="fltdAddRow" data-attach="<?php echo $teacherAssessment['group_assessment_id']; ?>" data-type="studentForAssessment"><i class="fa fa-plus"></i></a><?php //} ?>
				<table class="table customTbl">
					<thead>
						<tr>
							<th>Sr. No.</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Email</th>
							<!--<th>Date of Joining</th>
                                                        <th>Department</th>
							<th>Category</th>-->
							<th>Validator</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
                                                
						$i=0;
						$allApproved=1;
						global $t_assessor_list;
						foreach($teachers as $teacher){
							$i++;
							$nm=explode(" ",$teacher['name'],2);
							echo $assessmentModel->getStudentInStudentAssessmentHTMLRow($teacher['group_assessment_id'],$i,$nm[0],isset($nm[1])?$nm[1]:'',$teacher['email'],$teacher['doj'],$teacher['teacher_category_id'],$teacher['assessor_id'],$teacher['teacher_id'],1);
							if(isset($t_assessor_list[$teacher['assessor_id']]) && $t_assessor_list[$teacher['assessor_id']]['status_id']!=1)
								$allApproved=0;
						}
						echo count($teachers)>0 || $teacherAssessment['assessmentAssigned']>0?'':$teacherAssessment['assessmentAssigned']==0?$assessmentModel->getStudentInStudentAssessmentHTMLRow($teacherAssessment['group_assessment_id'],$i+1,'','','','',0,0,0,1):'';
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
		<?php //} ?>
		</div>
		
		<div class="fr clearfix">
			<?php if($teacherAssessment['assessmentAssigned']==0){ ?>
			<input type="button" autocomplete="off" data-ignorerejected="0" id="submitTchrsForAssessmnt" <?php echo count($teachers)>0 && $allApproved?"":'disabled="disabled"'; ?> class="fl nuibtn submitBtn" value="Submit" />
		        <?php } ?>
                        <?php if($isSchoolAdmin){ ?><!--<a id="addTeacherlink" href="?controller=assessment&action=addTeacherToTeacherAssessment&taid=<?php //echo $teacherAssessment['group_assessment_id']; ?>" class="fl nextbtn nuibtn">Next</a>--><?php } ?>
		</div>
		<div class="clr"></div>
		<div id="validationErrors"></div>
		<input type="hidden" id="taid" name="taid" value="<?php echo $teacherAssessment['group_assessment_id']; ?>" />
	</form>