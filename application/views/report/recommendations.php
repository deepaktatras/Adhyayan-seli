<h1 class="page-title">Adhyayan Recommendations - <?php echo $client_name ?></h1>
	<div class="clr"></div>
	<div>
		<div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
		</div>
		<div class="subTabWorkspace pad26">
			<div class="form-stmnt">
				<form method="post" id="teacher_recommendations_frm" action="">																			 
				<input type="hidden" name="group_assessment_id" value="<?php echo $group_assessment_id; ?>"> 
				<input type="hidden" name="diagnostic_id" value="<?php echo $diagnostic_id; ?>">
                                <input type="hidden" name="dept_id" value="<?php echo $dept_id; ?>"> 
					<div class="boxBody" >
						<h4><?php echo $DiagnosticName;?> Diagnostic</h4>
						<div class="vertScrollArea">					
						<div>									
							<?php if(!empty($kpas)){ 
									$i=0;									
									foreach($kpas as $key=>$kpa){
										$i++;
								?>
								<div class="addBtnWrap mt20"  style="width:98%;">
									<div class="teamsInfoHldr">
										<a href="javascript:void(0)" class="fltdAddRow ovwRecomm"
											data-type="kpa" data-id="<?php echo $kpa['kpa_instance_id']; ?>"><i class="fa fa-plus vtip" title="Click to add more rows."></i></a>
										<table class="table customTbl kpa <?php echo $kpa['kpa_instance_id']; ?>">
											<thead>
												<tr>
													<th style="width:8%">Sr. No.</th>
													<th style="width:82%;text-align:left;">KPA <?php echo $i. ' - '. $kpa['kpa_name']; ?> </th>
													<th style="width:10%"></th>
												</tr>
											</thead>
											<tbody>
												<?php 												
												$rcms = $reportObj->isExistingRecommendation($group_assessment_id,$diagnostic_id,'kpa',$kpa['kpa_instance_id'],$dept_id);												
							if($rcms==0){
								$row = reportModel::getRecommendationRow('kpa',$kpa['kpa_instance_id']);
								echo $row;
							}
							 else{							 	
								$j=0;		
								$recommendations = explode('~',$rcms['recommendations']);								
								foreach($recommendations as $recomm):
									$j++;
									$row = reportModel::getRecommendationRow('kpa',$kpa['kpa_instance_id'],$j,$recomm);
									echo $row;
								endforeach;
							} 
							?>						
											</tbody>
										</table>
									</div>
								</div>
							<?php } 
							
									}?>
									
						<?php if(!empty($kqs)){
									$i=0;									
									foreach($kqs as $key=>$kq){
										$i++;
								?>
								<div class="addBtnWrap mt20"  style="width:98%;">
									<div class="teamsInfoHldr">
										<a href="javascript:void(0)" class="fltdAddRow ovwRecomm"
											data-type="kq" data-id="<?php echo $kq['key_question_instance_id']; ?>"><i class="fa fa-plus vtip" title="Click to add more rows."></i></a>
										<table class="table customTbl kq <?php echo $kq['key_question_instance_id']; ?>">
											<thead>
												<tr>
													<th style="width:8%">Sr. No.</th>
													<th style="width:82%;text-align:left;">Key Question <?php echo $i. ' - '. $kq['key_question_text']; ?> </th>
													<th style="width:10%"></th>
												</tr>
											</thead>
											<tbody>
												<?php 												
												$rcms = $reportObj->isExistingRecommendation($group_assessment_id,$diagnostic_id,'key_question',$kq['key_question_instance_id'],$dept_id);												
							if($rcms==0){
								$row = reportModel::getRecommendationRow('kq',$kq['key_question_instance_id']);
								echo $row;
							}
							 else{							 	
								$j=0;		
								$recommendations = explode('~',$rcms['recommendations']);								
								foreach($recommendations as $recomm):
									$j++;
									$row = reportModel::getRecommendationRow('kq',$kq['key_question_instance_id'],$j,$recomm);
									echo $row;
								endforeach;
							} 
							?>						
											</tbody>
										</table>
									</div>
								</div>
							<?php } 
							
									}?>				
							<?php if(!empty($cqs)){
									$i=0;									
									foreach($cqs as $key=>$cq){
										$i++;
								?>
								<div class="addBtnWrap mt20"  style="width:98%;">
									<div class="teamsInfoHldr">
										<a href="javascript:void(0)" class="fltdAddRow ovwRecomm"
											data-type="cq" data-id="<?php echo $cq['core_question_instance_id']; ?>"><i class="fa fa-plus vtip" title="Click to add more rows."></i></a>
										<table class="table customTbl cq <?php echo $cq['core_question_instance_id']; ?>">
											<thead>
												<tr>
													<th style="width:8%">Sr. No.</th>
													<th style="width:82%;text-align:left;">Sub Question <?php echo $i. ' - '. $cq['core_question_text']; ?> </th>
													<th style="width:10%"></th>
												</tr>
											</thead>
											<tbody>
												<?php 												
												$rcms = $reportObj->isExistingRecommendation($group_assessment_id,$diagnostic_id,'core_question',$cq['core_question_instance_id'],$dept_id);												
							if($rcms==0){
								$row = reportModel::getRecommendationRow('cq',$cq['core_question_instance_id']);
								echo $row;
							}
							 else{							 	
								$j=0;		
								$recommendations = explode('~',$rcms['recommendations']);								
								foreach($recommendations as $recomm):
									$j++;
									$row = reportModel::getRecommendationRow('cq',$cq['core_question_instance_id'],$j,$recomm);
									echo $row;
								endforeach;
							} 
							?>						
											</tbody>
										</table>
									</div>
								</div>
							<?php } 
							
							}?>		
									<?php if(!empty($js)){
									$i=0;									
									foreach($js as $key=>$j){
										$i++;
								?>
								<div class="addBtnWrap mt20"  style="width:98%;">
									<div class="teamsInfoHldr">
										<a href="javascript:void(0)" class="fltdAddRow ovwRecomm"
											data-type="js" data-id="<?php echo $j['judgement_statement_instance_id']; ?>"><i class="fa fa-plus vtip" title="Click to add more rows."></i></a>
										<table class="table customTbl js <?php echo $j['judgement_statement_instance_id']; ?>">
											<thead>
												<tr>
													<th style="width:8%">Sr. No.</th>
													<th style="width:82%;text-align:left;">Judgement Statement <?php echo $i. ' - '. $j['judgement_statement_text']; ?> </th>
													<th style="width:10%"></th>
												</tr>
											</thead>
											<tbody>
												<?php 												
												$rcms = $reportObj->isExistingRecommendation($group_assessment_id,$diagnostic_id,'judgement_statement',$j['judgement_statement_instance_id'],$dept_id);												
							if($rcms==0){
								$row = reportModel::getRecommendationRow('js',$j['judgement_statement_instance_id']);
								echo $row;
							}
							 else{							 	
								$k=0;		
								$recommendations = explode('~',$rcms['recommendations']);								
								foreach($recommendations as $recomm):
									$k++;
									$row = reportModel::getRecommendationRow('js',$j['judgement_statement_instance_id'],$k,$recomm);
									echo $row;
								endforeach;
							} 
							?>						
											</tbody>
										</table>
									</div>
								</div>
							<?php } 
							
									}?>									
						</div>	
						</div>		
						<small><b>Note:-</b><ul><li style="font-weight:200"> To make any text bold enclose it in <?php echo htmlspecialchars("<b></b>");?>. Example:<?php echo htmlspecialchars("<b>Your text</b>: your recommendation");?> will render to <b>Your text</b>: your recommendation</li>
								 <li style="font-weight:200">To add a line break add <?php echo htmlspecialchars("<br/>");?>. Example:<?php echo htmlspecialchars("line1 <br/> line2");?> will render to:<br/> line1 <br/> line2</li>
							</ul>	
						</small>				
						<div class='text-right mt30'>
								<button type="submit" title="Click to add Recommendations" class="btn btn-primary vtip">Submit</button>																	
						</div>
						<div class='clearfix'></div>
						<div class="ajaxMsg"></div>
						<input type="hidden" class="isAjaxRequest" name="isAjaxRequest"
							value="<?php echo 1; ?>" />
					
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Initialize the plugin: -->
<script type="text/javascript">
    $(document).ready(function() {     
        $(".vertScrollArea").mCustomScrollbar({theme:"dark"});                	   
    });
</script>