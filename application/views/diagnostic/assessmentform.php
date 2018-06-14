<?php $assessmentFilled=1; //echo "<pre>";print_r($diagnosticLabels);die;
$url=createUrl(array("controller"=>"diagnostic","action"=>"assessmentForm","assessment_id"=>isset($_REQUEST['assessment_id'])?$_REQUEST['assessment_id']:'',"assessor_id"=>isset($_REQUEST['assessor_id'])?$_REQUEST['assessor_id']:''));
                                        
?>          <?php
                if($user['is_guest']==1 && $assessment['diagnostic_id']==72){
                 ?>
<div style="text-align:justify;font-weight:bold;font-size: 18px;padding-bottom: 5px;" class="text-danger"><span class="fa fa-info-circle fa-1x"></span> <?php echo MESSAGE_GUEST; ?></div>           
		<?php
                  }
                ?>
			<h1 class="page-title">
				<a href="<?php
					$args=array("controller"=>"assessment","action"=>"assessment");
					if($assessment['user_id']==$user['user_id'])
						$args["myAssessment"]=1;
					$args["filter"]=1;
					echo createUrl($args); 
					?>">
					<i class="fa fa-chevron-circle-left vtip" title="Back"></i>Manage MyReviews
					</a> &rarr;				
				<?php echo $assessment['client_name'];
				echo $assessment['assessment_type_id']==2?'<big>&#8594;</big>'.$assessment['user_names'][0]:'';
				if($isAdmin){
					echo $assessment['role']==3?' - Self-Review':' - External-Review';
				} ?>
                                 <select class="langSel" name="lang" id="lang-n" data-url="<?php echo $url; ?>">
                                    <?php foreach($diagnosticLanguages as $val) { ?>
                                    <option value="<?php echo $val['language_id'];?>" <?php echo (isset($prefferedLanguage) && $prefferedLanguage == $val['language_id'])?"selected":'';?>><?php echo $val['language_words'];?></option>
                                    <!--<option value="hi">हिंदी</option>-->
                                    <?php } ?>
                                </select> 
                                <?php
                                //$isReplicateReview=($assessment['assessment_type_id']==4 && $assessment['role']==4 && $self_review['role']==3 && $self_review['status']==1 && $self_review['percComplete']>=100 && $assessment['percComplete']==0)?1:0;
                                //$isReplicateReview=(($assessment['assessment_type_id']==1 || $assessment['assessment_type_id']==4 || $assessment['assessment_type_id']==5) && $assessment['role']==4 && $self_review['role']==3 && $self_review['status']==1 && $self_review['percComplete']>=100)?1:0;
                                $isReplicateReview=(($assessment['assessment_type_id']==4 || $assessment['assessment_type_id']==5) && $assessment['role']==4 && $self_review['role']==3 && $self_review['status']==1 && $self_review['percComplete']>=100)?1:0;
                     
                             //print_r($assessment);
                                ?>
			</h1>
			<form id="assessmentWrapper" method="post" class="<?php echo $assessment['role']==3 && !$isAdmin?"internal":"external"; ?>-assessment page-loading-class" onsubmit="return false;">
				<div id="assessmentForm" data-id="<?php echo $assessment_id; ?>" class="whitePanel page-loading">
					<div class="tab1Hldr mb10">
						<a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab1_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
						<div class="collapse navbar-collapse tabitemsHldr" id="tab1_Toggle">
							<ul class="redTab nav nav-tabs">
								<?php
								$i=0;
                                                                $kpa7Id = 0;
								foreach($kpas as $kpa_id=>$kpa){									
									$i++;
                                                                        if($i == 7){
                                                                         $kpa7Id = $kpa_id;
                                                                        }
									$isKNComplete=true;
									if($assessment['role']==4 && $akpans!=0){
												if(isset($akpans[$kpa_id]) && count($akpans[$kpa_id])){
													foreach($akpans[$kpa_id] as $akn_id=>$akn)
														if(empty($akn['text_data'])){
															$isKNComplete=false;
															$assessmentFilled=0;
															break;
														}
												}else{
													$isKNComplete=false;
													$assessmentFilled=0;
												}
									}
								/*?>
									<li class="item mainTab" data-type="kpa" data-kpaid="<?php echo $kpa_id; ?>"><a href="#kpa<?php echo $kpa_id; ?>" class="vtip" title="<?php echo htmlspecialchars($kpa['kpa_name']); ?>">KPA <?php echo $i; ?></a></li>
								<?php*/
									?>
									<li class="item<?php echo $i==1?" active":""; ?> <?php echo $kpa['numericRating']>0 && $isKNComplete?"completed":""; ?>"><a href="#kpa<?php echo $kpa_id; ?>" data-toggle="tab" class="vtip" title="<?php echo htmlspecialchars($kpa['kpa_name']); ?>"><?php echo $diagnosticLabels['KPA']." ". $kpa['kpa_no']; ?></a>
										<?php if($assessment['role']==4 && $akpans!=0){ ?><input name="aknotes[]" type="hidden" class='kpa key-notes-val' id='kpa_<?php echo $kpa_id; ?>' value="<?php $isKNComplete ? print 1: print 0; ?>"><?php }?>
									</li>									
									<?php
								}
                                                                ?>
                                                                 <?php if($isReplicateReview){ ?>        
                                                                
                                                                <div style="text-align:left;padding-top: 10px;float:right;color:#FFFFFF;" ><input type="checkbox" name="replicate_self_review" id="replicate_self_review" value="<?php echo $assessment_id; ?>"  <?php echo $assessment['is_replicated']==1?'checked="checked"':'' ?>  <?php echo (($assessment['is_replicated']==1) || ($assessment['percComplete']>=100 && $assessment['status']==1))?'disabled="disabled"':'' ?> >Replicate Self Review Ratings</div>
                <?php } ?>
							</ul>
                                                    
						</div>
					</div>
					<!-- Tab panes -->                    
					<div id="theMainTabWarp" class="tab-content mainTabCont">
						
						
						<?php
							$activeKpa=1;
							include("common/kpatabs.php");
						?>
					</div>
				</div>
				
				<!-- Page Progress & Buttons bar  --> 
				<div class="clearfix page-loading">
					<div class="fl page-progress">
						<div id="percProg" style="width:<?php echo $assessment['percComplete']; ?>%;" class="percent"></div>
					</div>
					<div class="fr clearfix">
						<?php if(!$isReadOnly){ 
							if($isAdmin && $assessment['role']==4){ ?>
							<div style="width: auto;" class="chkHldr bigger pt10"><input type="checkbox" id="aKeyNotesAccepted" name="approveKeyNotes" value="1" <?php echo $assessment['isAssessorKeyNotesApproved']==1?'checked="checked"':''; ?>><label class="chkF checkbox"><span>Approve Assessor Key Recommendations</span></label></div>
							<?php } ?>
						
						<a class="fl nuibtn previewBtn vtip execUrl" title="Last saved data will be previewed" data-modalclass="modal-lg aPreview" href="?controller=diagnostic&action=assessmentPreview&external=<?php echo $external;?>&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessor_id; ?>&lang_id=<?php echo $prefferedLanguage;?>"><?php echo $diagnosticLabels['Preview'];?></a>
                                                        <input type="button" autocomplete="off" <?php echo isset($_GET['save_enable'])?"":'disabled="disabled"'?> id="saveAssessment" class="fl nuibtn saveBtn" value="<?php echo $diagnosticLabels['Save'];?>" />
                                              
						<?php 
                                                if($isFilledStatus == 1 && $is_collaborative && $external == 0 && $submitStatus == 0){  ?>
                                                    <input type="button" autocomplete="off" id="submitAssessment" <?php echo $assessmentFilled==0?'disabled="disabled"':''; ?> class="fl nuibtn submitBtn" value="Submit" />
                                                <?php }else if($isFilledStatus == 0  && $external == 1 && $is_collaborative && $submitStatus == 0){  ?>
                                                    <input type="button" autocomplete="off" id="submitAssessment" <?php echo $assessmentFilled==0?'disabled="disabled"':''; ?> class="fl nuibtn submitBtn" value="Submit" />
                                                <?php }else {
                                                    
                                                if($assessment['status']==0 && $is_collaborative==0){ ?><input type="button" autocomplete="off" id="submitAssessment" <?php echo $assessmentFilled==1?"":'disabled="disabled"'; ?> class="fl nuibtn submitBtn" value="Submit" /><?php } ?>
						<?php } 
                                                }
                                                ?>
					</div>
				</div>
				<!-- End: \ Page Progress & Buttons bar  --> 
				<input type="hidden" name="assessment_id" value="<?php echo $assessment_id; ?>" />
				<input type="hidden" name="assessor_id" value="<?php echo $assessor_id; ?>" />
				<input type="hidden" name="external" value="<?php echo $external; ?>" />
				<input type="hidden" name="is_collaborative" value="<?php echo $is_collaborative; ?>" />
				<input type="hidden" name="isLeadAssessorKpa" value="<?php echo $isLeadAssessorKpa; ?>" />
				<input type="hidden" name="isLeadAssessor" value="<?php echo $isLeadAssessor; ?>" />
				<input type="hidden" name="isRevCompleteNtSubmitted" value="<?php echo $isRevCompleteNtSubmitted; ?>" />
			</form>			
			<script>
                            isRevCompleteNtSubmitted = 0;
			<?php 
                            if($isLeadAssessor == 0 && $assessment['status']==0 && intval($assessment['percComplete'])=='100'){ ?>
						isRevCompleteNtSubmitted = 1;
                        <?php }else if($isLeadAssessorKpa == 1  && $assessment['status']==0 && intval($assessment['percComplete'])=='100'){ ?>
                            isRevCompleteNtSubmitted = 1;
                            
                            
                       <?php }else if($isFilledStatus == 1 && $isLeadAssessor == 1 &&  $assessment['status']==0 && intval($assessment['percComplete'])=='100'){
                        ?>
                            isRevCompleteNtSubmitted = 1;
                            <?php } ?>
                         var scoreToText={ "scheme-1":{0:"",1:"<?php echo $diagnosticLabels['Needs_Attention'];?>",2:"<?php echo $diagnosticLabels['Variable'];?>",3:"<?php echo $diagnosticLabels['Good'];?>",4:"<?php echo $diagnosticLabels['Outstanding'];?>"},"scheme-2":{0:"",1:"Foundation",2:"Emerging",3:"Developing",4:"Proficient",5:"Exceptional",6:""},"scheme-4":{0:"",1:"Foundation",2:"Emerging",3:"Developing",4:"Proficient",5:"Exceptional",6:""},"scheme-5":{0:"",1:"Needs Attention",2:"Variable",3:"Good",4:"Outstanding"}},isFileUploading=false;

			
			</script>