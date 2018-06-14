<?php
$addDeleteBtn=$teacherAssessment['assessmentAssigned']==0?1:0;
?>
	<h1 class="page-title">
		<a href="?controller=assessment&action=createTeacherAssessor&taid=<?php echo $teacherAssessment['group_assessment_id']; ?>">
			<i class="fa fa-chevron-circle-left vtip"  title="Back"></i>
		</a>
		<?php echo $teacherAssessment['client_name']; ?> <big>&#8594;</big> Manage External Reviewers <big>&#8594;</big> Manage Teachers
	</h1>
	<form method="post" id="addTchrToAssmntForm" onsubmit="return false;">
		<div class="subTabWorkspace pad26 tag_box_wrap">
			
			<div data-trigger="tchrAssessorsChanged" id="status_id_1-1" class="tag_boxes mb15 clearfix">
				<h2 class="mb10 small">Adhyayan selected External Reviewers</h2>
			<?php
			$cnt=0;
			$currentAssessorsBySchool=array();
			if(isset($assessors[1])){
				foreach($assessors[1] as $eUser){
					if($eUser['added_by_admin']==1){
						echo userModel::getExternalAssessorNodeHtml($eUser,0,1);
						$cnt++;
					}else
						$currentAssessorsBySchool[]=$eUser;
				}
			}
			?>
				<span class="empty <?php echo $cnt>0?'notEmpty':''; ?>">List is empty</span>
			</div>
			
			<div data-trigger="tchrAssessorsChanged" id="status_id_1-0" class="tag_boxes mb15 clearfix">
				<h2 class="mb10 small">School's selected External Reviewers</h2>
			<?php
			foreach($currentAssessorsBySchool as $eUser){
				echo userModel::getExternalAssessorNodeHtml($eUser,0,1);
			}
			?>
				<span class="empty <?php echo count($currentAssessorsBySchool)>0?'notEmpty':''; ?>">List is empty</span>
			</div>
			
			<div data-trigger="tchrsForAssessmentChanged" id="status_id_2" class="tag_boxes mb15 clearfix">
				<h2 class="mb10 small">School's selected External Reviewers - Waiting for Approval</h2>
			<?php
			$cnt=0;
			if(isset($assessors[2])){
				foreach($assessors[2] as $eUser){
					echo userModel::getExternalAssessorNodeHtml($eUser,0,2);
					$cnt++;
				}
			}
			?>
				<span class="empty <?php echo $cnt>0?'notEmpty':''; ?>">List is empty</span>
			</div>
			
			<div data-trigger="tchrsForAssessmentChanged" id="status_id_3" class="tag_boxes mb15 clearfix">
				<h2 class="mb10 small">Rejected School's External Reviewers</h2>
			<?php
			$cnt=0;
			if(isset($assessors[3])){
				foreach($assessors[3] as $eUser){
					echo userModel::getExternalAssessorNodeHtml($eUser,0,3);
					$cnt++;
				}
			}
			?>
				<span class="empty <?php echo $cnt>0?'notEmpty':''; ?>">List is empty</span>
			</div>
			
			<h2 class="mb20 small pl10">
				Add/Edit/Remove Teacher
				<?php if($teacherAssessment['assessmentAssigned']==0){ ?><a title="Upload teacher list" class="btn btn-primary small-btn pull-right execUrl vtip" href="?controller=assessment&amp;action=assessorListUpload&type=teacher&taid=<?php echo $teacherAssessment['group_assessment_id']; ?>&amp;ispop=1">Upload List</a><?php } ?>
			</h2>
			
			
			<div data-trigger="tchrsForAssessmentChanged" class="tableHldr teamsInfoHldr teacherAssessor_team team_table">
				<?php if($teacherAssessment['assessmentAssigned']==0){ ?><a href="javascript:void(0)" class="fltdAddRow" data-attach="<?php echo $teacherAssessment['group_assessment_id']; ?>" data-type="teacherForAssessment"><i class="fa fa-plus"></i></a><?php } ?>
				<table class="table customTbl">
					<thead>
						<tr>
							<th>Sr. No.</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Email</th>
							<th>Date of Joining</th>
							<th>Category</th>
							<th>Reviewer</th>
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
							echo $assessmentModel->getTeacherInTeacherAssessmentHTMLRow($teacher['group_assessment_id'],$i,$nm[0],isset($nm[1])?$nm[1]:'',$teacher['email'],$teacher['doj'],$teacher['teacher_category_id'],$teacher['assessor_id'],$teacher['teacher_id'],$addDeleteBtn,$addDeleteBtn);
							if(isset($t_assessor_list[$teacher['assessor_id']]) && $t_assessor_list[$teacher['assessor_id']]['status_id']!=1)
								$allApproved=0;
						}
						echo count($teachers)>0 || $teacherAssessment['assessmentAssigned']>0?'':$teacherAssessment['assessmentAssigned']==0?$assessmentModel->getTeacherInTeacherAssessmentHTMLRow($teacherAssessment['group_assessment_id'],$i+1,'','','','',0,0,0,1):'';
						?>
					</tbody>
				</table>
			</div>
			
		</div>
		
		<div class="fr clearfix">
		<?php if($teacherAssessment['assessmentAssigned']==0){ ?>
			<input type="button" value="Save" data-ignorerejected="0" disabled="disabled" class="fl nuibtn saveBtn" id="saveTchrsForAssessmnt" autocomplete="off">
			
			<input type="button" autocomplete="off" data-ignorerejected="0" id="submitTchrsForAssessmnt" <?php echo count($teachers)>0 && $allApproved?"":'disabled="disabled"'; ?> class="fl nuibtn submitBtn" value="Submit" />
		<?php } ?>
		</div>
		<div class="clr"></div>
		<div id="validationErrors"></div>
		<input type="hidden" name="taid" value="<?php echo $teacherAssessment['group_assessment_id']; ?>" />
	</form>