<?php
$isSchoolAssessment=$group_assessment_id==0?true:false;
$isSubAssessment=$assessment_id>0 && $group_assessment_id>0?true:false;
$isGroupAssessment=$assessment_id==0?true:false;
$isSelfReview =!empty($assessment['subAssessmentType']) && $assessment['subAssessmentType']==1?1:'0';
$allow_publish=1;
$isAdmin = 0;
if(!empty($user))
$isAdmin=in_array("create_assessment",$user['capabilities']);
//print_r($assessment);
if($isSubAssessment)
{
	$teacherExtPerc = explode(',',$assessment['perc']);
	$teacherExtPerc = $teacherExtPerc[1];
        $teacherIntPerc = $teacherExtPerc[0];
}
//print_r($school_cat);
?>
	<h1 class="page-title">
		Reports for <?php echo $isSubAssessment?$assessment['usernameByRole'][3].' - ':''; echo $assessment['client_name']; ?>
	</h1>
	<div class="clr"></div>
	<div id="reportsListWrapper" data-assessmentorgroupassessmentid="<?php echo $isGroupAssessment?$group_assessment_id:$assessment_id; ?>" data-assesmenttypeid="<?php echo $assessment['assessment_type_id']; ?>" class="">
		<div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
		</div>
		<div class="subTabWorkspace pad26">	
		<?php
		if(count($reports)==0){
		?>
			<h3>No reports found</h3>
		<?php
		}/*else if($isSchoolAssessment && ( ($assessment['st/*atusByRole'][3]!=1 && $assessment['subAssessmentType']==1)&&($assessment['statusByRole'][4]!=1))){ ?>
			<h3>External review form still not submitted</h3>
		<?php		
		}*/else if($isSchoolAssessment && ($assessment['aqs_status']!=1 || ($assessment['statusByRole'][3]!=1 && $assessment['subAssessmentType']==1)&&($assessment['aqs_status']!=1 || $assessment['statusByRole'][4]!=1))){ ?>
			<h3>School Profile or External review form still not submitted</h3>
		<?php		
		}/*else if($isSchoolAssessment && ($assessment['aqs_status']!=1 || $assessment['statusByRole'][4]!=1)){ ?>
			<h3>School Profile or External review form still not submitted</h3>
		<?php
		}else if($assessment_id==0 && $assessment['aqs_status']==1 && $assessment['data_by_role'][4]==1){ ?>
			<h3>AQS form or External review form still not submitted</h3>
		<?php
		}*/else if($isSubAssessment && (($assessment['statusByRole'][4]!=1 || $teacherExtPerc!='100') &&  $assessment['isTchrInfoFilled']!=1)){ ?>
			<h3>Teacher/Student info form or External review form still not submitted</h3>
		<?php
		}/*else if($assessment['aqs_status']!=1 && $assessment['assessment_type_id']==4){ ?>
			<h3>AQS/CRR form still not submitted</h3>
		<?php
		}*/else if($assessment['assessment_type_id']==4 && isset($assessment['isTchrInfoFilled']) && $assessment['isTchrInfoFilled']!=1){ ?>
			<h3>Student info form still not submitted</h3>
		<?php
		}else{
			//$isPublished=$isGroupAssessment?(isset($reports[0]['report_data'][0])?$reports[0]['report_data'][0]['isPublished']:0):$reports[0]['isPublished'];
			$isPublished=$reports[0]['isPublished'];
		?>
                        
                <?php
		if($isSchoolAssessment && ($assessment['aqs_status']!=1 || ($assessment['statusByRole'][3]!=1 && $assessment['subAssessmentType']==1)&&($assessment['aqs_status']!=1 || $assessment['statusByRole'][4]!=1))){
		$allow_publish=0;
                }
                ?>
		<?php if($isPublished!=1){ ?>
			<div class="row report_row pb10">
				<div class="col-sm-3">
					<b>Report Validity: </b>
				</div>
				<div class="col-sm-2">
					<select class="valid_years">
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select> <b>Years</b>
				</div>
				<div class="col-sm-2">
					<select class="valid_months">
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
					</select> <b>Months</b>
				</div>
                            <?php
                            if($isSchoolAssessment && $assessment_id && $assessment['assessment_type_id']==1){
                            ?>
                            <div class="col-sm-2">
                                <b>Language:</b>
                            </div>
                            <div class="col-sm-3">
					<select name="lang" id="lang_id">
                                                <option value="">Select Language</option>
                                           
                                        <?php foreach($diagnosticsLanguage as $data) { ?>
                                            <option value="<?php echo $data['language_id'];?>"><?php echo $data['language_words'];?></option>
                                            
                                       <?php } ?>
                                          </select>
				</div>
			    
                        <?php
                        }
                        ?>
                        
			</div>
		       <?php } ?>
     
                         
                       <?php
                        if($isPublished==1 && $assessment['assessment_type_id']==1){
                         ?>
                        <div class="row report_row pb10">
                        <div class="col-sm-2">
                                <b>Language:</b>
                            </div>
                            <div class="col-sm-3">
					<select name="lang" id="lang_id">
                                                <option value="">Select Language</option>
                                           
                                        <?php foreach($diagnosticsLanguage as $data) { ?>
                                            <option value="<?php echo $data['language_id'];?>"><?php echo $data['language_words'];?></option>
                                            
                                       <?php } ?>
                                          </select>
				</div>
                        </div>
                         <?php 
                        }
                        ?>
			<div id="reportsList" class="form-stmnt">
			<?php 	
			if(!$isSchoolAssessment && !$assessment_id)//teacher review group tab
			{
                               if($assessment['assessment_type_id']=="4"){
                                   $col_top="7";
                                   $col_1="5";
                               }else{
                                   $col_top="7";
                                   $col_1="5";
                               }
                               
                               
                                
                                                
                            
				foreach($reportsType as $rType){
                                    
                                if($isGroupAssessment && $rType['report_id']==9 && $isAdmin==1){
                                $url_R=createUrl(array("controller"=>"exportExcel","action"=>"studentData","report_id"=>$rType['report_id'],"group_assessment_id"=>$groupAssessmentId,"school"=>$assessment['client_id'],"round"=>$assessment['student_round']));
                                ?>
                                 <div class="row report_row pb10 pt10" id="report_row_<?php echo $rType['report_id']; ?>">
					
                                            
                                                <div class="col-sm-7">
                                                    
                                                    <div class="col-sm-5"> Batch Summary Report</div>
                                                    <div class="col-sm-3">&nbsp;</div>
                                                
                                                </div>
                                                
                                     <div class="col-sm-3"><a href="<?php echo $url_R ?>" target="_blank" class="btn btn-secondary form-control" style="background-color:rgb(110, 91, 1);" > Generate Report</a></div>
                                                <div class="col-sm-2">&nbsp;</div>
                                           
                                        
                                 </div>
                                     <?php
                                 }
                                    
				if($rType['report_id']==7 || $rType['report_id']==10 ||$rType['report_id']==4)
					$url=createUrl(array("controller"=>"report","action"=>"teacher","report_id"=>$rType['report_id'],"group_assessment_id"=>$groupAssessmentId));
				else if($rType['report_id']==8){
                                        $url=createUrl(array("controller"=>"report","action"=>"student","report_id"=>$rType['report_id'],"group_assessment_id"=>$groupAssessmentId));
                                        continue;
                                }
                                else
					$url=createUrl(array("controller"=>"report","action"=>"report","report_id"=>$rType['report_id'],"group_assessment_id"=>$groupAssessmentId));
                                        $url_all=createUrl(array("controller"=>"report","action"=>"reportall","report_id"=>$rType['report_id'],"group_assessment_id"=>$groupAssessmentId));
				        //$url_compar_round=createUrl(array("controller"=>"report","action"=>"report","assessment_id"=>$aid,"report_id"=>$report['report_id'],"group_assessment_id"=>$report['group_assessment_id'],"diagnostic_id"=>$assessment['diagnostic_id'],"client_id"=>$assessment['client_id']));
                                        if($rType['report_id']==10 || $rType['report_id']==11 || $rType['report_id']==12) continue;
					?>
				<div class="row report_row pb10 pt10" id="report_row_<?php echo $rType['report_id']; ?>">
					<div class="col-sm-<?php echo $col_top ?>">
						<?php print '<div class="col-sm-'.$col_1.'">'.$rType['report_name']."</div>";
							//for teacher overview report show diagnostic drop down
							if($rType['report_id']==4){								
								echo '<div class="col-sm-7" >'."<label class='catlabel'>Teacher Type</label><select class='tchrdrop' id='report_id_4' data-groupassessment_id='".$groupAssessmentId."'>";								
								foreach($diagnosticsForGroup as $key=>$diag):
									echo "<option value=$key>".$diag."</option>";
								endforeach;		
								echo "</select>";
                                                                echo"<label class='catlabel'>Department</label><select class='tchrdrop' id='report_id_cat_4'><option value=''>All Departments</option>";
                                                                foreach($school_cat as $key=>$val){
                                                                echo "<option value=$key>".$val."</option>";    
                                                                }
                                                                echo"</select>";
                                                                echo "</div>";					
							}else if($rType['report_id']==8){								
								echo '<div class="col-sm-3" >'."<select class='tchrdrop' id='report_id_8' style='display:none;'>";								
								foreach($diagnosticsForGroup as $key=>$diag):
									echo "<option value=$key>".$diag."</option>";
								endforeach;		
								echo "</select></div>";					
							}
							elseif($rType['report_id']==5){		
								//print_r($reportsIndividual);
								echo '<div class="col-sm-7">'."<label class='catlabel'>&nbsp;</label><select class='tchrdrop'  id='report_id_5'>";
								foreach($reportsIndividual as $key=>$tchr):
							        echo "<option value='".$tchr['assessment_id']."'>".$tchr['user_names'][0]."</option>";									
								endforeach;
								echo "</select>".'</div>';
							}elseif($rType['report_id']==9){		
								//print_r($reportsIndividual);
								echo '<div class="col-sm-3">'."<select class='tchrdrop'  id='report_id_9'>";
								foreach($reportsIndividual as $key=>$tchr):
									echo "<option value='".$tchr['assessment_id']."'>".$tchr['user_names'][0]."</option>";									
								endforeach;
								echo "</select>".'</div>';
							}
							elseif($rType['report_id']==7){								
								echo '<div class="col-sm-7">'."<label class='catlabel'>&nbsp;</label><select class='tchrdrop'  id='report_id_7'>";
								foreach($reportsSingleTeacher as $key=>$tchr):
								echo "<option value='".$tchr['assessment_id']."'>".$tchr['user_names'][0]."</option>";
								endforeach;
								echo "</select>".'</div>';
							}elseif($rType['report_id']==10){								
								echo '<div class="col-sm-3">'."<select class='tchrdrop'  id='report_id_10'>";
								foreach($reportsSingleTeacher as $key=>$tchr):
								echo "<option value='".$tchr['assessment_id']."'>".$tchr['user_names'][0]."</option>";
								endforeach;
								echo "</select>".'</div>';
							}
						?>
					</div>
				<?php if($isPublished==1){ ?>
						<div class="col-sm-3">						
							<input type="button" class="btn btn-primary view_report tchr-report" data-url="<?php echo $url; ?>" data-diagnosticId="<?php echo 0 ?>" data-assessmentId="<?php echo 0 ?>" data-reportid="<?php echo $rType['report_id']; ?>" value="View Report" />
						</div>
				<?php }else{ 
							$firstDiag = (array_keys($diagnosticsForGroup));
							$firstDiag = isset($firstDiag[0])?$firstDiag[0]:0;							
					?>
						
						<div class="col-sm-3">
							<?php if($rType['report_id']==4 && in_array("edit_all_submitted_assessments",$user['capabilities'])){?><a class="execUrl btn btn-primary tchr-recomm form-control" style="padding:3px 0px;" data-size="850" href="<?php echo createUrl(array("controller"=>"report","action"=>"recommendations","assessment_id"=>0,"report_id"=>$rType['report_id'],"group_assessment_id"=>$groupAssessmentId,"diagnostic_id"=>$firstDiag,"dept_id"=>"")); ?>">Recommendations</a><?php } ?>							
							<input type="button" data-url="<?php echo $url; ?>" data-diagnosticId="<?php echo 0 ?>" data-assessmentId="<?php echo 0 ?>" class="btn btn-secondary form-control generate_report tchr-report" data-reportid="<?php echo $rType['report_id']; ?>" value="Generate Report" />
						
                                                </div>
						
				<?php }
				?>
                                    <?php //echo $url_all;
                                    if($isGroupAssessment && $rType['report_id']==9){
                                    if($isPublished==1){
                                    ?>
                                    <div class="col-sm-2"><input type="button" class="btn btn-primary view_report tchr-report" data-url="<?php echo $url_all; ?>" data-diagnosticId="<?php echo 0 ?>" data-assessmentId="<?php echo 0 ?>" data-reportid="<?php echo $rType['report_id']; ?>" value="Download All" /></div>
                                    <?php
                                    }else{
                                    ?>
                                    <div class="col-sm-2"><input type="button" class="btn btn-primary tchr-report generate_report" data-url="<?php echo $url_all; ?>" data-diagnosticId="<?php echo 0 ?>" data-assessmentId="<?php echo 0 ?>" data-reportid="<?php echo $rType['report_id']; ?>" value="Download All" /></div>    
                                    <?php }
                                    }?>
                                    
                                    <?php
                                    if($isGroupAssessment && $rType['report_id']==5){
                                    if($isPublished==1){
                                    ?>
                                    <div class="col-sm-2"><input type="button" class="btn btn-primary view_report tchr-report" data-url="<?php echo $url_all; ?>" data-diagnosticId="<?php echo 0 ?>" data-assessmentId="<?php echo 0 ?>" data-reportid="<?php echo $rType['report_id']; ?>" value="Download All" /></div>
                                    <?php
                                    }else{
                                    ?>
                                    <div class="col-sm-2"><input type="button" class="btn btn-primary tchr-report generate_report" data-url="<?php echo $url_all; ?>" data-diagnosticId="<?php echo 0 ?>" data-assessmentId="<?php echo 0 ?>" data-reportid="<?php echo $rType['report_id']; ?>" value="Download All" /></div>    
                                    <?php }
                                    }?>
                                    
                                    <?php
                                    if($isGroupAssessment && $rType['report_id']==7){
                                    if($isPublished==1){
                                    ?>
                                    <div class="col-sm-2"><input type="button" class="btn btn-primary view_report tchr-report" data-url="<?php echo $url_all; ?>" data-diagnosticId="<?php echo 0 ?>" data-assessmentId="<?php echo 0 ?>" data-reportid="<?php echo $rType['report_id']; ?>" value="Download All" /></div>
                                    <?php
                                    }else{
                                    ?>
                                    <div class="col-sm-2"><input type="button" class="btn btn-primary tchr-report generate_report" data-url="<?php echo $url_all; ?>" data-diagnosticId="<?php echo 0 ?>" data-assessmentId="<?php echo 0 ?>" data-reportid="<?php echo $rType['report_id']; ?>" value="Download All" /></div>    
                                    <?php }
                                    }?>
                                    
                                </div>
				<?php 
				}
				?>
				
				<?php 
				//$diagnosticsForGroup
			}
			else	
			foreach($reports as $report){ //print_r($reports);
                                if($report['report_id']==8 || $report['report_id']==10) continue;
				$aid=$assessment_id==0?$report['assessment_id']:$assessment_id;
				$subAssessmentType = !empty($assessment['subAssessmentType'])?$assessment['subAssessmentType']:0;
				if(($isSchoolAssessment && $subAssessmentType == 1 && $report['report_id']!=1)||($isSchoolAssessment && $report['report_id']==3 && $numKpas<2) || ($isSchoolAssessment && !empty($user) && in_array("take_external_assessment",$user['capabilities']) && !(in_array("view_published_own_school_reports",$user['capabilities'])) && in_array($report['report_id'],array(3))) )//||($isSchoolAssessment && $report['report_id']!=1 && !empty($user) && in_array("take_external_assessment",$user['capabilities']))) //show aqs report card only in case of self review
					continue ;
				$dId=empty($report['diagnostic_id'])?0:$report['diagnostic_id'];
				if($report['report_id']==7 || $report['report_id']==10 || $report['report_id']==4){
					$url=createUrl(array("controller"=>"report","action"=>"teacher","assessment_id"=>$aid,"report_id"=>$report['report_id'],"group_assessment_id"=>$report['group_assessment_id'],"diagnostic_id"=>$dId));
				
                                }else{	
					$url=createUrl(array("controller"=>"report","action"=>"report","assessment_id"=>$aid,"report_id"=>$report['report_id'],"group_assessment_id"=>$report['group_assessment_id'],"diagnostic_id"=>$dId));
                                        $url_compar_round=createUrl(array("controller"=>"report","action"=>"reportRound2","assessment_id"=>$aid,"report_id"=>$report['report_id'],"group_assessment_id"=>$report['group_assessment_id'],"diagnostic_id"=>$assessment['diagnostic_id'],"client_id"=>$assessment['client_id']));
                                }
				?>
				<div class="row report_row pb10 pt10" id="report_row_<?php echo $report['report_id']; ?>">
				
                                   
                                    <?php if($report['report_id']==1) {
                                    ?>
                                    <div class="col-sm-10">
						<?php print strtolower($report['report_name'])=='aqs report'?'AQS Report Card':$report['report_name'].($assessment_id==0 && $report['group_assessment_id']==0?' - '.$report['user_names'][0]:''); ?>
				    </div>
                                    <?php
                                    }else{
                                    ?>
                                    
                                    <div class="col-sm-10">
						<?php print strtolower($report['report_name'])=='aqs report'?'AQS Report Card':$report['report_name'].($assessment_id==0 && $report['group_assessment_id']==0?' - '.$report['user_names'][0]:''); ?>
				    </div>
                                    <?php
                                    }
                                    ?>
				<?php if($isPublished==1){ ?>
						<div class="col-sm-2">
                                                    <!--<input type="button" rel="<?php echo $url; ?>" id="aqsReportView" class="btn btn-primary" data-reportid="<?php echo $report['report_id']; ?>" value="View Report" />-->
                                                    <a target="_blank" class="btn btn-primary" id="aqsReportView" rel="<?php echo $url; ?>" data-reportid="<?php echo $report['report_id']; ?>" href="#">View Report</a>
						</div>
				<?php }else{ 
                                    
                                    ?>
						
						<div class="col-sm-2">
                                                    
							<input type="button" data-url="<?php echo $url; ?>" class="btn btn-secondary form-control generate_report school-report" data-reportid="<?php echo $report['report_id']; ?>" value="Generate Report" />
						</div>
						
				<?php } ?>
               
				</div>
                            
                            <?php if($report['report_id']==1 && $assessment['aqs_round']==2 && $numRowsCount==2) {?> 
                              
                            <div class="row report_row pb10 pt10">
				<div class="col-sm-10">
					AQS Comparative report Round 2
				</div> 
                                <?php if($isPublished==1){?>
                                <div class="col-sm-2">
                                                    <!--<input type="button" rel="<?php echo $url; ?>" id="aqsReportView" class="btn btn-primary" data-reportid="<?php echo $report['report_id']; ?>" value="View Report" />-->
                                                    <a target="_blank" class="btn btn-primary" id="aqsReportView" rel="<?php echo $url_compar_round; ?>" data-reportid="<?php echo $report['report_id']; ?>" href="#">View Report</a>
						</div>
                                <?php }else{ ?>
                               <div class="col-sm-2">
                                                    
							<input type="button" data-url="<?php echo $url_compar_round; ?>" class="btn btn-secondary form-control generate_report school-report" data-reportid="<?php echo $report['report_id']; ?>" value="Generate Report" />
						</div>
						
                        <?php }?>
			</div>
                                    
                        <?php  }?> 
                            
                            
                           	<?php
			} 
			?>   <?php //print_r($report);?>
                               
                            
       
			</div>
                         
                <?php
                if($assessment['aqs_status']!=1 && $allow_publish==0){
                echo"<br><div style='width:100%;text-align:left;'>Note: AQS Profile Data is Incomplete.</div>";
                }
                ?>
		<?php if( !$isSelfReview && !empty($user) && in_array("generate_submitted_asmt_reports",$user['capabilities'])){ ?>	
		<?php if($isPublished!=1 && ($assessment_id>0 || ($assessment['allStatusFilled'] && $assessment['allTchrInfoFilled']))){ ?>
			<?php if($allow_publish && ($isSchoolAssessment || $isGroupAssessment)){ ?>
			<div class="row report_row pb10 pt10">
				<div class="col-sm-10">
					<?php if($isSchoolAssessment && $assessment['isAssessorKeyNotesApproved']!=1 && $numKpas>1){ ?>
					<div style="width: auto;" class="chkHldr"><input type="checkbox" id="keyNotesAccepted"><label class="chkF checkbox"><span>I approve all the assessor key recommendations</span></label></div>
					<?php }else{ ?>
					&nbsp;
					<?php } ?>
				</div>
				<div class="col-sm-2">
					<input type="button" class="btn btn-primary form-control publish_report vtip" title="Click to publish all reports." value="Publish" />
				</div>
			</div>
			<div class="row pt10">
				<div class="col-sm-12">
				<b>NOTE:-</b> <i>Once you publish the reports you would not be able to edit anything (including review and assessor keynotes).</i>
				</div>
			</div>
			<?php } ?>
		<?php }else if($isPublished==1){ ?>
			<div class="row pt20">
				<div class="col-sm-9">
					<b>Published on:</b> <?php echo substr($reports[0]['publishDate'],0,7); ?>
				</div>
				<div class="col-sm-3">
					<b>Valid till:</b> <?php echo substr($reports[0]['valid_until'],0,7); ?>
				</div>
			</div>
		<?php } ?>
			
		<?php } ?>	
		<?php } ?>
		</div>
	</div>
      <script>
        
       var groupassessment_id = $("#report_id_4").data("groupassessment_id"); 
       var diagnostic_id=$("#report_id_4").val();
       //alert(groupassessment_id);
       //alert(diagnostic_id);
       
       postData = "groupassessment_id="+groupassessment_id+"&diagnostic_id="+diagnostic_id+"&token=" + getToken();
       apiCall(this, "updateDepartment", postData,
                function (s, data) {
                    //alert("Yes");
                var aDd = $("#report_id_cat_4");
                aDd.find("option").next().remove();
                addOptions(aDd, data.department, 'department_id', 'department');
                
                }, showErrorMsgInMsgBox);
       
    </script>