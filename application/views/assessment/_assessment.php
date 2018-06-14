<?php
$isNetworkAdmin=(in_array("view_own_network_assessment",$user['capabilities']) && $user['network_id']>0)?1:0;
$isSchoolAdmin=in_array("view_own_institute_assessment",$user['capabilities'])?1:0;
$isAdmin=in_array("view_all_assessments",$user['capabilities'])?1:0;
$canEditAfterSubmit=in_array("edit_all_submitted_assessments",$user['capabilities'])?1:0;
$isPrincipal=in_array(6,$user['role_ids'])?1:0;
$addEditColumn=$isNetworkAdmin || $isSchoolAdmin || $isAdmin?1:0;
$isAdminNadminPrincipal=$isNetworkAdmin || $isPrincipal || $isAdmin?1:0;

$assessmentListRowHelper=new assessmentListRowHelper($user);
?>
				<div class="filterByAjax assessment-list" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
					<h1 class="page-title"><?php echo empty($_REQUEST['myAssessment'])?"Manage ":""; ?>MyReview
					<?php if(in_array("create_assessment",$user['capabilities'])){ ?>
						<a href="?controller=assessment&action=createSchoolAssessment" class="btn btn-primary pull-right execUrl" id="addschoolAssBtn">Create School Review</a> 
						<a href="?controller=assessment&action=createTeacherAssessment" class="btn btn-primary pull-right execUrl mr30" id="addTeacherAssBtn">Create Teacher Review</a>
					<?php } ?>
						<div class="clr"></div>
					</h1>
					<div class="asmntTypeContainer">
					 <?php
						$ajaxFilter=new ajaxFilter();
						if(!empty($_REQUEST['myAssessment'])){
							$ajaxFilter->addHidden("myAssessment",1);
						}else{
							if($isAdmin || $isNetworkAdmin)
								$ajaxFilter->addTextbox("client_name",$filterParam["client_name_like"],"School Name");
							if($isAdmin)
								$ajaxFilter->addDropDown("network_id",$networks,'network_id','network_name',$filterParam["network_id"],"Network");
						}
						$ajaxFilter->addDropDown("status",array(array("id"=>"iFilled","value"=>"Internal Filled"),array("id"=>"eFilled","value"=>"External Filled"),array("id"=>"iNotFilled","value"=>"Internal Not Filled"),array("id"=>"eNotFilled","value"=>"External Not Filled"),array("id"=>"bFilled","value"=>"Both Filled"),array("id"=>"bNotFilled","value"=>"Both Not Filled")),'id','value',$filterParam["status"],"Status");
						$ajaxFilter->generateFilterBar(1);
						?>
					 <div class="tableHldr">
						 <table class="cmnTable">
							 <thead>
								 <tr>
									<th colspan="2" data-value="client_name" class="sort <?php echo $orderBy=="client_name"?"sorted_".$orderType:""; ?>">School Name</th>
									<th data-value="assessment_type" class="sort <?php echo $orderBy=="assessment_type"?"sorted_".$orderType:""; ?>">Review Type</th>
									<th data-value="create_date" class="sort <?php echo $orderBy=="create_date"?"sorted_".$orderType:""; ?>">Date of Review</th>
									<th>School Profile Status</th>
									<th>Self-Review Status (%)</th>
									<th>External Review Status (%)</th>
									<?php if($isAdmin){?>
									<th>Reports</th>
									<?php }
									if($addEditColumn){?>
									<th>Edit/View</th>
									<?php } ?>
								 </tr>
							 </thead>
							 <tbody>
								 <tr>
								 <?php
								
							if(count($assessmentList)){
								 foreach($assessmentList as $assessment){
									$isReportPublished=empty($assessment['report_data']) || $assessment['report_data'][0]['isPublished']!=1?false:true;
								?>
								<tr class="ass_type_<?php echo $assessment['assessment_type_id']; ?>" data-gaid="<?php echo $assessment['group_assessment_id']; ?>">
									<td><?php echo $assessment['assessment_type_id']==2 && $assessment['assessments_count']>0?'<span class="collapseGA fa fa-plus-circle"></span>':''; ?></td>
									<td><?php echo $assessment['client_name']; ?></td>
									<td><?php echo $assessment['assessment_type_name']; ?></td>
									<td><?php echo substr($assessment['create_date'],0,10); ?></td>
									<td><a title="Click to view/edit School Profile" class="vtip" href="<?php echo createUrl(array("controller"=>"diagnostic","action"=>"aqsForm","assessment_id"=>$assessment['assessment_id'])); ?>"><?php echo $assessment['aqs_status']==1?"Filled":"Not filled"; ?></a></td>
								<?php
								if($assessment['assessment_type_id']==1){
									$roles=array(3,4);
									foreach($roles as $rid){
								?>
									<td><span class="vtip" title="<?php echo $assessment['data_by_role'][$rid]['user_name']; ?>"><?php echo $assessment['data_by_role'][$rid]['percComplete']."%"; ?></span>
									<?php
										if($assessment['aqs_status']!=1){
											
										}else if($assessment['data_by_role'][$rid]['status']==1){
											echo '<span class="assComplete vtip" title="'.$assessment['data_by_role'][$rid]['ratingInputDate'].'"></span>';
																						
											if($user['user_id']==$assessment['data_by_role'][$rid]['user_id'] || $isAdmin || ($rid==3 && (($isSchoolAdmin && $assessment['client_id']==$user['client_id']) || ($isNetworkAdmin && $assessment['network_id']==$user['network_id'])) )){
												$editViewText=$canEditAfterSubmit && !$isReportPublished ?'Edit':'View';
												echo '<br><a data-modalclass="modal-lg aPreview" title="Click to view the snapshot of ratings for KPAs." href="'.createUrl(array("controller"=>"diagnostic","action"=>"assessmentPreview","assessment_id"=>$assessment['assessment_id'],"assessor_id"=>$assessment['data_by_role'][$rid]['user_id'])).'" class="linkBtn vtip execUrl">Preview</a> <a title="Click to '.$editViewText.' ratings." href="'.createUrl(array("controller"=>"diagnostic","action"=>"assessmentForm","assessment_id"=>$assessment['assessment_id'],"assessor_id"=>$assessment['data_by_role'][$rid]['user_id'])).'" class="linkBtn vtip">'.$editViewText.'</a>';
											}
										}else if($user['user_id']==$assessment['data_by_role'][$rid]['user_id']){
											echo '<br><a href="'.createUrl(array("controller"=>"diagnostic","action"=>"assessmentForm","assessment_id"=>$assessment['assessment_id'],"assessor_id"=>$user['user_id'])).'" class="linkBtn">Take Review</a>';
										}
										?>
									</td>
								<?php 
									}
								}else{
								?>
									<td></td><td></td>
								<?php
								}
									 if($isAdmin){
									 ?>
									 <td>
										<?php if($assessment['aqs_status']==1 && isset($assessment['data_by_role']) && $assessment['data_by_role'][4]['status']==1){ ?>
										<a class="execUrl manageReportBtn vtip iconBtn" data-size="700" href="<?php echo createUrl(array("controller"=>"assessment","action"=>"reportList","assessment_id"=>$assessment['assessment_id'])); ?>" title="Print/View Report"><i class="fa fa-print"></i></a>
										<?php } ?>
									</td>
									 <?php }
									 
									if($addEditColumn){?>
									<td>
									<?php if($assessment['assessment_type_id']==2 && ($isAdminNadminPrincipal || $assessment['admin_user_id']==$user['user_id'])){ ?>
										<a href="?controller=assessment&amp;action=createTeacherAssessor&amp;taid=<?php echo $assessment['group_assessment_id']; ?>">
										<?php echo $assessment['assessments_count']==0?'<i title="Edit" class="vtip glyphicon glyphicon-pencil"></i>':'<i title="View" class="vtip glyphicon glyphicon-eye-open"></i>'; ?>
										</a>
									<?php } ?>
									</td>
									<?php } ?>
								 </tr>
								<?php
								 }
							}else{
								?>
								<tr>
									<td colspan="<?php $n=6; if($isAdmin){$n+=2;}else if($addEditColumn){$n++;} echo $n; ?>">No Review found</td>
								</tr>
								<?php
							}
								 ?>
							 </tbody>
						 </table>
					 </div>
					 
					 
					  <?php echo $this->generateAjaxPaging($pages,$cPage); ?>
								
						<div class="ajaxMsg"></div>
					 
					 
					</div>
				</div>