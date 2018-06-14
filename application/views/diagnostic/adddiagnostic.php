<form id="add_diagnostic_form">
		<div class="row">
			<h1 class="page-title"><?php if($isPop==0){?>
					<a href="<?php
						$args=array("controller"=>"diagnostic","action"=>"diagnostic");	
                                                $args["filter"] = 1;
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						Manage Diagnostics
					</a> &rarr;
				<?php } ?>Create Diagnostic</h1>
			<div class="col-md-4">			
			<h1 class="related">			
			<span class="editHdng"><i class="fa fa-pencil-square-o vtip" title="You can enter/edit diagnostic title"></i></span>
			<input type="text" id="diagnostic_name" name="diagnostic_name" value="<?php echo $diagnosticName; ?>" placeholder="Enter Diagnostic title" readonly="readonly">
			<input type="hidden" autocomplete="off" name="kpas_id" id="selected_kpa" value="" />
                        <input type="hidden" autocomplete="off" name="diagnostic_id" id="diagnostic_id" value="<?php echo $diagnosticId; ?>" />
			<input type="hidden" autocomplete="off" name="assessmentId" id="assessmentId" value="<?php echo $assessmentId; ?>" />
                        <input type="hidden" autocomplete="off" name="langId" id="langId" value="<?php echo isset($langId)?$langId:''; ?>" />
			<input type="hidden" autocomplete="off" name="teacherCategoryId" id="teacherCategoryId" value="<?php echo isset($teacherCategory['teacher_cat_id'])?$teacherCategory['teacher_cat_id']:''; ?>" />
			<input type="hidden" autocomplete="off" name="parent_diagnostic_id" id="parent_diagnostic_id" value="<?php echo isset($parentId)?$parentId:''; ?>" />
                        <input type="hidden" autocomplete="off" name="equivalenceId" id="equivalenceId" value="<?php echo isset($equivalenceId)?$equivalenceId:''; ?>" />
                        <input type="hidden" autocomplete="off" name="langIdOriginal" id="langIdOriginal" value="<?php echo isset($langIdOriginal)?$langIdOriginal:''; ?>" />
                        
                        </h1>
			</div>
			<div class="col-md-8 text-right hdngRight">			
			<?php if($assessmentType!=''){?>
                        <h2 id="language_name">Language: <?php echo $languageName ?></h2>    
			<h2 id="assesmentVal">Review Type: <strong><?php echo ucfirst($assessmentType[0]['assessment_type_name']); ?>
			<?php print $teacherCategory['teacher_category']? " (".$teacherCategory['teacher_category'].")":''; ?></strong>
			</h2>

			<?php }
			else {
			?>
                        <h2 id="language_name"></h2>
			<h2 id="assesmentVal"></h2>
			<a href="?controller=diagnostic&amp;action=assessmenttype" style="margin-top:-26px;" class="valcheck btn btn-primary execUrl mr30" id="chooseAssmt">Choose Review Type</a>
			
			<?php 
			}
			?>
			</div>			
			
		</div>
		<div class="clearfix recmLbl">
			<div class="pull-right">
				<h2>Assessor Recommendations Level:</h2>
				<div class="recmLblDD">
					<select name="recommendations_levels" id="id_recommendations_levels" class="form-control mulselect" multiple="multiple" style="visibility: hidden;">
						<option value="kpa_recommendations" <?php echo (isset($kpaRecommendations) && $kpaRecommendations==1)?'selected="selected"':'';?>>KPA</option>
						<option value="kq_recommendations" <?php echo (isset($kqRecommendations) && $kqRecommendations==1)?'selected="selected"':'';?>>Key Question</option>
						<option value="cq_recommendations" <?php echo (isset($cqRecommendations) && $cqRecommendations==1)?'selected="selected"':'';?>>Sub Question</option>
						<option value="js_recommendations" <?php echo (isset($jsRecommendations) && $jsRecommendations==1)?'selected="selected"':'';?>>Judgement Statement</option>
					</select>
			    </div>
			</div>
		</div>
					
			   <div class="whitePanel" id="allContent">
			   <?php if($diagnosticId>0){ include('diagnostickpatabs.php'); } 
						else{
			   ?>
                    <div class="tab1Hldr" style="display:none;" >                        
                        <div class="tabitemsHldr">
                            <ul class="redTab nav nav-tabs">
                                <!-- <li class="item active"><a href="#kpa1" data-toggle="tab" class="vtip" title="Leadership &amp; Management">KPA 1</a></li>-->
                            </ul>
                            <div class="flotedInTab add" >
                                <a href="?controller=diagnostic&action=addmoreform&type=kpa&assessmentId=" data-postformid="for" class="vtip execUrl" title="Click to add KPAs" data-toggle="modal" data-target="#addMoreKPA" data-validator="isDiagnosticName" id="addmorekpa" title="KPAs" data-size="800"><i class="fa fa-plus-circle"></i> Add KPA</a>                              	
								<!--<a href="?controller=diagnostic&action=addmoreform&type=kpa&assessmentId=<?php echo $assessmentId; ?>" class="vtip execUrl" title="Click to select a school." >KPA(s): Add More</a>-->								
						   </div>
                        </div>
                    </div>
                    <!-- Tab panes -->                    
						<?php } ?>  
                </div>

                <!-- Page Progress & Buttons bar  --> 
                
				<div class="ajaxMsg"></div>
                <div class="clearfix">                    
                    <div class="fr clearfix"> 
					<?php if(!$isDiagnosticPublished){ ?>	
						<input type="button" data-ignorerejected="0" value="Save" class="fl nuibtn saveBtn" id="saveDiagBtn" autocomplete="off" disabled="disabled">
                       <input type="submit" class="fl nuibtn submitBtn" value="Submit">
					<?php } ?> 
                    </div>
                </div>
</form>				
                <!-- End: \ Page Progress & Buttons bar  --> 
				