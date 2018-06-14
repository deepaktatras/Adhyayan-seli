<?php 
$isReadOnly=empty($isReadOnly)?0:1;
?>
<h1 class="page-title">
	<a href="<?php
						$args=array("controller"=>"assessment","action"=>"assessment");													
						$args["filter"]=1;
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i> Manage MyReviews 
					</a> &rarr; Post-Review
	-
	<?php echo $assessment['client_name'];
				
				?>
</h1>
<div class="clr"></div>
<div class="">
	<div class="ylwRibbonHldr">
		<div class="tabitemsHldr"></div>
	</div>
	<div class="subTabWorkspace pad26">
		<div class="form-stmnt">
			<form method="post" id="post_review_form" action="">
			<input type="hidden" name="assessment_id" id="id_assessment_id" value="<?php echo $assessment_id;?>">
			<input type="hidden" name="aqsdata_id"  value="<?php echo $aqsdata_id; ?>">
                        <input type="hidden" name="assessor_id" id="id_assessor_id" value="<?php echo $assessor_id; ?>">  
                        <input type="hidden" name="diagnostic_id" id="id_diagnostic_id" value="<?php echo $diagnostic_id; ?>">  
			<input type="hidden" name="lang_id" id="lang_id"  value="<?php echo $lang_id; ?>">
                        <div class="boxBody">
					<dl class="fldList">
						<dt>
							<?php echo $fields['decision_maker']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									<?php // print_r($currentData);?>
									<select class="form-control" id="postrev_decision_maker" name="decision_maker" required>
										<option value="">- Select Decision Maker -</option>
												<?php
												foreach ( $postReviewDecisionList as $decision_maker )
													echo "<option value=\"" . $decision_maker['decision_id'] . "\" ".(!empty($currentData) && $currentData['decision_maker']==$decision_maker['decision_id']?'selected=selected':'') .">" . $decision_maker['decision_user'] . "</option>\n";
												?>
											</select>
								</div>                                                            
							</div>
							<div class="row" id="postrev_decision_maker_text" style="<?php echo !empty($currentData['decision_maker']) && $currentData['decision_maker']==4?'block;':'display:none;'?>">
								<div class="col-sm-6">									
									<input type="text" class="form-control" name="decision_maker_other" value="<?php echo !empty($currentData['decision_maker_other'])?$currentData['decision_maker_other']:''; ?>" />
								</div>
							</div>
						</dd>
					</dl>
					<dl class="fldList">
						<dt>
							<?php echo $fields['management_engagement']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control desc" name="management_engagement" required>
										<option value="">- Select Engagement of Management -</option>
												<?php
												foreach ( $postReviewEngMgmtList as $engMgt )
													echo "<option value=\"" . $engMgt ['engagement_id'] . "\" ".(!empty($currentData) && $currentData['management_engagement']==$engMgt['engagement_id']? 'selected=selected':'' )." >" . $engMgt ['engagement_type'] . "</option>\n";
												?>
											</select>
								</div>
                                                            <div class="col-sm-6" id="management_engagement_desc">
                                                                    <?php
									foreach ( $postReviewEngMgmtList as $engMgt )   
                                                                            echo '<span id="'.$engMgt ['engagement_id'] .'" '.(!empty($currentData) && $currentData['management_engagement']==$engMgt['engagement_id']?' style="display:block"':' style="display:none;"').' >'.$engMgt['engagement_type_text'].'</span>';
                                                                    ?>
                                                            </div>
							</div>
						</dd>
					</dl>
					<dl class="fldList">
						<dt>
							<?php echo $fields['principal_involvement']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">					
									<select class="form-control" name="principal_involvement" required>
										<option value="">- Select Principal's involvement-</option>
												<?php
												foreach ( $postReviewInvolvement as $involvement )
													echo "<option value=\"" . $involvement['invlovement_id'] . "\" ".(!empty($currentData) && $currentData['principal_involvement']==$involvement['invlovement_id']?'selected=selected':'' ).">" . $involvement['status'] . "</option>\n";
												?>
											</select>
								</div>
							</div>
						</dd>
					</dl>
					<dl class="fldList">
						<dt>
							<?php echo $fields['principal_openness']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control" name="principal_openness" required>
										<option value="">- Select Principal's openness -</option>
												<?php
												foreach ( $postReviewOpenness as $openness )
													echo "<option value=\"" . $openness ['openness_id'] . "\" ".(!empty($currentData) && $currentData['principal_openness']==$openness ['openness_id']?'selected=selected':'' )." >" . $openness ['openness_type'] . "</option>\n";
												?>
											</select>
								</div>
							</div>
						</dd>
					</dl>
					<dl class="fldList">
						<dt>
							<?php echo $fields['action_management_decision']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control" name="action_management_decision" required>
										<option value="">- Select Action by management -</option>
												<?php
												foreach ( $postReviewActionList as $action )
													echo "<option value=\"" . $action['action_id'] . "\" ".(!empty($currentData) && $currentData['action_management_decision']==$action ['action_id']?'selected=selected':'' )." >" . $action['action_type'] . "</option>\n";
												?>
											</select>
								</div>
							</div>
						</dd>
					</dl>
					
					<dl class="fldList">
						<dt>
							<?php echo $fields['principal_tenure']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control" name="principal_tenure" required>
										<option value="">- Select tenure-</option>
												<?php
												foreach ( $postReviewPrinTenure as $prin )
													echo "<option value=\"" . $prin['tenure_id'] . "\" ".(!empty($currentData) && $currentData['principal_tenure']==$prin['tenure_id']?'selected=selected':'' ).">" . $prin['tenure'] . "</option>\n";
												?>
											</select>
								</div>
							</div>
						</dd>
					</dl>
					
					<dl class="fldList">
						<dt>
							<?php echo $fields['principal_vision']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control" name="principal_vision" required>
										<option value="">- Select vision -</option>
												<?php
												foreach ( $postReviewVision as $vision )
													echo "<option value=\"" . $vision ['vision_id'] . "\" ".(!empty($currentData) && $currentData['principal_vision']==$vision ['vision_id']?'selected=selected':'' )." >" . $vision ['vision_type'] . "</option>\n";
												?>
											</select>
								</div>
							</div>
						</dd>
					</dl>
                                        
                                         <dl class="fldList">
						<dt>
							<?php echo $fields['average_staff_tenure']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control" name="average_staff_tenure" required>
										<option value="">- Select tenure-</option>
												<?php
												foreach ( $postReviewStaffTenure as $tenure )
													echo "<option value=\"" . $tenure['avgstafftenure_id'] . "\" ".(!empty($currentData) && $currentData['average_staff_tenure']==$tenure['avgstafftenure_id']?'selected=selected':'' )." >" . $tenure['avg_tenure'] . "</option>\n";
												?>
											</select>
								</div>
							</div>
						</dd>
					</dl>	
										
					
					
					<dl class="fldList">
						<dt>
							<?php echo $fields['parent_teacher_association']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control desc" name="parent_teacher_association" required>
										<option value="">- Select Parent Teacher Association-</option>
												<?php
												foreach ( $postReviewParentTeacherAssoc as $assoc )
													echo "<option value=\"" . $assoc['association_id'] . "\" ".(!empty($currentData) && $currentData['parent_teacher_association']==$assoc['association_id']?'selected=selected':'' )." >" . ucfirst($assoc['status']) . "</option>\n";
												?>
											</select>
								</div>
                                                                <div class="col-sm-6" id="parent_teacher_association_desc">
                                                                    <?php
									foreach ( $postReviewParentTeacherAssoc as $assoc )   
                                                                            echo '<span id="'.$assoc ['association_id'] .'" '.(!empty($currentData) && $currentData['parent_teacher_association']==$assoc['association_id']?' style="display:block"':' style="display:none;"').' >'.$assoc['parent_association_text'].'</span>';
                                                                    ?>
                                                            </div>
							</div>
						</dd>
					</dl>
					<dl class="fldList">
						<dt>
							<?php echo $fields['alumni_association']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control desc" name="alumni_association" required>
										<option value="">- Select Alumni Association-</option>
												<?php
												foreach ( $postReviewAlumniAssoc as $assoc )
													echo "<option value=\"" . $assoc['association_id'] . "\" ".(!empty($currentData) && $currentData['alumni_association']==$assoc['association_id']?'selected=selected':'' )." >" . ucfirst($assoc['status']) . "</option>\n";
												?>
											</select>
								</div>
                                                                <div class="col-sm-6" id="alumni_association_desc">
                                                                    <?php
									foreach ( $postReviewAlumniAssoc as $assoc )   
                                                                            echo '<span id="'.$assoc ['association_id'] .'" '.(!empty($currentData) && $currentData['alumni_association']==$assoc['association_id']?' style="display:block"':' style="display:none;"').' >'.$assoc['alumni_association_text'].'</span>';
                                                                    ?>
                                                            </div>
							</div>
						</dd>
					</dl>
					
					<dl class="fldList">
						<dt>
							<?php echo $fields['student_body_activity']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>							
							<div class="row" id="student_body_drop" >
								<div class="col-sm-6">									
									<select class="form-control desc" id="postrev_sbody_select" name="student_body_activity">
										<option value="">- Select student leaders role -</option>
												<?php
												foreach ( $postReviewStudentBody as $sbody )
													echo "<option value=\"" . $sbody['student_body_id'] . "\" ".(!empty($currentData) && $currentData['student_body_activity']==$sbody['student_body_id']?'selected=selected':'' ).">" . $sbody['student_body_text'] . "</option>\n";
												?>
											</select>
								</div>
                                                                <div class="col-sm-6" id="student_body_activity_desc">
                                                                    <?php
									foreach ( $postReviewStudentBody as $sbody )   
                                                                            echo '<span id="'.$sbody ['student_body_id'] .'" '.(!empty($currentData) && $currentData['student_body_activity']==$sbody['student_body_id']?' style="display:block"':' style="display:none;"').' >'.$sbody['student_body_text_desc'].'</span>';
                                                                    ?>
                                                            </div>
							</div>
							<!--<div class="row" id="postrev_sbody_levels" style="<?php echo (!empty($currentData) && $currentData['student_body_activity']==2)?'display:block;':'display:none;'?>">
								<div class="col-sm-6">									
									<div class="clearfix chkBpxPane">
											<?php											
											foreach($schoolLevelList as $level)
												echo '<div class="chkHldr"><input autocomplete="off" type="checkbox" name="student_body_school_level[]" value="'.$level['school_level_id'].'" '.(!empty($currentData) && in_array($level['school_level_id'],explode(',',$currentData['student_body_school_level']))?'checked=checked':'' ).'><label class="chkF checkbox"><span>'.$level['school_level'].'</span></label></div>';
											?>
											</div> 
								</div>
							</div>-->
						</dd>
					</dl>
					
					<dl class="fldList">
						<dt>
							<?php echo $fields['middle_leaders']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>							
							<div class="row" id="middle_leaders_drop">
								<div class="col-sm-6">									
									<select class="form-control desc" name="middle_leaders_select" >
										<option value="">- Select number of middle leaders -</option>
												<?php
												foreach ( $postReviewMidLeaders as $leader )
													echo "<option value=\"" . $leader['midleaders_id'] . "\" ".(!empty($currentData) && $currentData['middle_leaders']==$leader['midleaders_id']?'selected=selected':'' ).">" . $leader['status'] . "</option>\n";
												?>
											</select>
								</div>
                                                            <div class="col-sm-6" id="middle_leaders_select_desc">
                                                                    <?php
									foreach ( $postReviewMidLeaders as $leader )   
                                                                            echo '<span id="'.$leader['midleaders_id'].'" '.(!empty($currentData) && $currentData['middle_leaders']==$leader['midleaders_id']?' style="display:block"':' style="display:none;"').' >'.$leader['midleaders_text'].'</span>';
                                                                    ?>
                                                            </div>
							</div>
						</dd>
					</dl>														
										
					<!--<dl class="fldList">
						<dt>
							<?php //echo $fields['average_number_students_class']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control" name="average_number_students_class" required>
										<option value="">- Select -</option>
												<?php
												/*foreach ( $postReviewAvgStudents as $avgStud )
													echo "<option value=\"" . $avgStud['student_id'] . "\" ".(!empty($currentData) && $currentData['average_number_students_class']==$avgStud['student_id']?'selected=selected':'' )." >" . $avgStud['student_count'] . "</option>\n";
												*/?>
											</select>
								</div>
							</div>
						</dd>
					</dl>-->


				<dl class="fldList">
						<dt>
							<?php echo $fields['ratio_students_class_size']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<select class="form-control" name="ratio_students_class_size" required>
										<option value="">- Select -</option>
												<?php
												foreach ( $postReviewRatioClass as $ratio )
													echo "<option value=\"" . $ratio ['classratio_id'] . "\" ".(!empty($currentData) && $currentData['ratio_students_class_size']==$ratio ['classratio_id']?'selected=selected':'' )." >" . ucfirst($ratio ['ratio']) . "</option>\n";
												?>
											</select>
								</div>
							</div>
						</dd>
					</dl>
                                    
                                    <dl class="fldList">
						<dt>
							<?php echo $fields['rte']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<div class="clearfix chkBpxPane radioRow">
										<div class="chkHldr" >
										<input autocomplete="off" id="rte_1" type="radio" value="1" name="rte" <?php echo (!empty($currentData) && $currentData['rte']==1?'checked=checked':(empty($currentData)?'checked=checked':'')) ?>><label class="chkF radio"><span>Applicable</span></label>
										</div>
										<div class="chkHldr" >
										<input autocomplete="off" id="rte_2" type="radio" value="0" name="rte" <?php echo (!empty($currentData) && $currentData['rte']==0?'checked=checked':'' ) ?>><label class="chkF radio"><span>Not Applicable</span></label>
										</div>
									</div>                                                                    
								</div>
							</div>
						</dd>
					</dl>
                                    <dl class="fldList">
						<dt>
							<?php echo $fields['student_count']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">									
									<input type="number" class="form-control" name="student_count" required min="1" max="99999" value="<?php echo (!empty($currentData)?$currentData['student_count']:'' ) ?>" />                                                                   
								</div>
							</div>
						</dd>
					</dl>
                                    
                                    <dl class="fldList">
						<dt>
							<?php echo $fields['average_number_students_class']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-12">		<div class="tableHldr noShadow">
							
							<table class="table postRevTbl">
                                                            <thead><tr><th>Levels</th>							
								<?php											
											foreach($schoolLevelList as $level)
											{
												
											?>
                                                                    
												<th><?php  echo $level['school_level'] ?></th>	
											
                                                                                        <?php } ?></tr></thead>
                                                            <tbody>
                                                            <tr class="prow">
                                                            <td>Avg. no. of students in single class</td>
                                                            <?php
                                                            //print_r($studentTeacherLevelList);
                                                            foreach($studentTeacherLevelList as $level){
                                                                //print_r($level);
                                                            ?>
                                                            <td><select class="form-control" name="average_students_class[<?php echo $level['school_level_id']; ?>]" required>
										<option value="">- Select -</option>
												<?php
												foreach ( $postReviewAvgStudents as $avgStud )
													echo "<option value=\"" . $avgStud['student_id'] . "\"  ".(!empty($level) && $level['student_id']==$avgStud['student_id']?'selected=selected':'' )." >" . $avgStud['student_count'] . "</option>\n";
												?>
											</select></td>
                                                            <?php
                                                            }
                                                            ?>
                                                            </tr>
                                                            <tr class="prow">
                                                            <td>Avg. no of teachers in single class</td>
                                                            <?php
                                                            foreach($studentTeacherLevelList as $level){
                                                            ?>
                                                            <td><select class="form-control" name="average_teachers_class[<?php echo $level['school_level_id']; ?>]" required>
										<option value="">- Select -</option>
												<?php
												foreach ( $postReviewAvgTeachers as $avgTeac )
													echo "<option value=\"" . $avgTeac['teacher_id'] . "\"  ".(!empty($level) && $level['teacher_id']==$avgTeac['teacher_id']?'selected=selected':'' )." >" . $avgTeac['average_teacher_class'] . "</option>\n";
												?>
											</select></td>
                                                            <?php
                                                            }
                                                            ?>
                                                            </tr>
                                                            </tbody>
                                                        </table></div>
								</div>
							</div>
						</dd>
				    </dl>
					
				<!--<dl class="fldList">
						<dt>
							<?php //echo $fields['number_teaching_staff']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">
											<?php
                                                                                       
											//foreach($schoolLevelList as $level)
											//{
											//	echo $level['school_level'];
											?>
									<div class="clr"></div>										
									<select class="form-control" name="number_teaching_staff[<?php //echo $level['school_level_id']; ?>]" required>
										<option value="">- Select -</option>
												<?php
												//foreach ( $postReviewTeachingStaffCount as $count )
												//	echo "<option value=\"" . $count ['staff_id'] . "\" ".(!empty($level) && $level['staff_id']==$count['staff_id']?'selected=selected':'' )." >" . $count ['staff_count'] . "</option>\n";
												?>
											</select>
											<?php //} ?>
								</div>
							</div>
						</dd>
					</dl>
				<dl class="fldList">
						<dt>
							<?php //echo $fields['number_non_teaching_staff_prep']['COLUMN_COMMENT']; ?><span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-6">	
										<?php //if($hasPrep){?>
										Pre primary<select class="form-control" name="number_non_teaching_staff_prep" required>
										<option value="">- Select -</option>
												<?php
												//foreach ( $postReviewNonTeachingStaffCount as $count )
												//	echo "<option value=\"" . $count ['staff_id'] . "\" ".(!empty($currentData) && $currentData['number_non_teaching_staff_prep']==$count ['staff_id']?'selected=selected':'' ).">" . $count ['staff_count'] . "</option>\n";
												?>											
									</select>
									<?php //} ?>
									<?php //if($hasNonPrep){?>
										Rest of the School<select class="form-control" name="number_non_teaching_staff_rest" required>
										<option value="">- Select -</option>
												<?php
												//foreach ( $postReviewNonTeachingStaffCount as $count )
												//	echo "<option value=\"" . $count ['staff_id'] . "\" ".(!empty($currentData) && $currentData['number_non_teaching_staff_rest']==$count ['staff_id']?'selected=selected':'' )." >" . $count ['staff_count'] . "</option>\n";
												?>											
									</select>
									<?php //} ?>
								</div>
							</div>
						</dd>
					</dl>-->
				<dl class="fldList">
						<dt>
							Action planning area chosen by the school<span class="astric">*</span>:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-12">									
									<div class="addBtnWrap">
						<a href="javascript:void(0)" class="filterRowAdd pRowAdd"><i class="fa fa-plus vtip" title="Click to add more rows."></i></a>	
						<div>
							<div class="tableHldr noShadow">
							
							<table class="table postRevTbl">
											<thead>
												<tr><th style="width:5%;">Sr. No.</th><th style="width:15%;">KPA</th><th style="width:15%;">KQ</th><th style="width:20%;">SQ</th><th  style="width:40%;">Action</th><th style="width:5%;"></th></tr>	
											</thead>
							<?php 
                                                            $sno=0;
                                                            if(empty($actionPlanningData)){
								$row = diagnosticModel::getPostReviewDiagnosticHTML(1,$assessment_id,$assessor_id,0,array(),$lang_id);
								echo $row;
                                                            }
                                                            else{
                                                                foreach($actionPlanningData as $key=>$data){                                                                    
                                                                    $row = diagnosticModel::getPostReviewDiagnosticHTML(++$sno,$assessment_id,$assessor_id,$sno>1?1:0,$data,$lang_id);
                                                                    echo $row;
                                                                }
                                                            }
							?>
							</table>
							</div>	
						</div>	
					</div>
								</div>
							</div>
						</dd>
					</dl>	
				
					<dl class="fldList">
						<dt>
							Any other important information or comments you would like to add about the school:
						</dt>
						<dd>
							<div class="row">
								<div class="col-sm-11">									
									<textarea  name="comments" id="id_comments" class="form-control"><?php echo $currentData['comments']; ?></textarea>
								</div>
							</div>
						</dd>
					</dl>
					
						
				</div>
				<?php if(!$isReadOnly){ ?>
                                <div class="clearfix">
                                    <div class="fr clearfix" style="margin-top:36px;">
                                        <input type="button" autocomplete="off"  id="savePostReview" class="fl nuibtn saveBtn" disabled="disabled" value="Save" />
                                        <?php if($currentData['status']!=1){ ?><input type="submit" autocomplete="off" id="submitPostReview" class="fl nuibtn submitBtn" value="Submit" disabled /><?php } ?>
                                    </div>
                                </div>
				<?php } ?>	
				<div class="ajaxMsg"></div>
				<input type="hidden" class="isAjaxRequest" name="isAjaxRequest"
					value="<?php echo $ajaxRequest; ?>" />
			</form>
		</div>
	</div>
</div>
