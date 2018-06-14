<span id="load_edit">				
<a id="addUserBtn" class="btn btn-primary pull-right execUrl vtip fixonmodal" title="Click to add a new user." href="?controller=user&amp;action=createUser&amp;ispop=1">Add New</a>
				<h1 class="page-title">
				<?php if($isPop==0){?>
					<a href="<?php
						$args=array("controller"=>"assessment","action"=>"assessment");						
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						Manage MyReviews
					</a>
					>
				<?php }?>
						Create Teacher Review
				</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabmitemsHldr"></div>
                                                <?php
                                                if($isPop==0){ ?>
                                                <a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab4_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
		<div class="collapse navbar-collapse tabitemsHldr" id="tab4_Toggle">
			<ul class="yellowTab nav nav-tabs">          
				<li class="item active"><a href="Javascript:void(0);" data-toggle="tab" class="vtip" title="Create/ Edit Teacher Review">Step 1</a></li>
				<li class="item"><a href="Javascript:void(0);" data-toggle="tab" class="vtip" title="Manage Validators/Teachers" disabled="disabled">Step 2</a></li>				
			</ul>
                       
                                                <?php } ?>                                
		</div>
                                                
					</div>
					<div class="subTabWorkspace pad26">
                                            <div>
							<form method="post" id="create_teacher_assessment_form" action="">
                                                            
								<div class="boxBody">
									<dl class="fldList">
										<dt>School<span class="astric">*</span>:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<span id="selected_client_name"></span> &nbsp;
													<a id="selectClientBtn" data-size="1050" data-postformid="for" title="Click to select a school." class="btn btn-danger vtip execUrl" href="?controller=client&amp;action=clientList&amp;ispop=1">Select School</a>
													<input type="hidden" value="" autocomplete="off" id="selected_client_id" name="client_id" autocomplete="off">
												</div>
											</div>
										</dd>
									</dl>
																		
									<dl class="fldList">
										<dt>Principal/Admin<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control school_admin_id" autocomplete="off" name="school_admin_id" required>
												<option value=""> - Select School Principal/Admin - </option>
											</select>
										</div></div></dd>
									</dl>
									
									<dl class="fldList">
										<dt>External Reviewers :</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<div id="external_reviewers_block" class="currentSelection tag_boxes clearfix">
														<span class="empty">Nothing selected yet</span>
													</div>
													<a data-size="950" data-postdata="#create_teacher_assessment_form .eAssessorNode input" class="btn btn-danger vtip execUrl" href="?controller=user&amp;action=externalAssessorList&amp;ispop=1" title="Click to select external reviewers of different schools.">Select Reviewers</a>
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
											<div class="row">
												<div class="col-sm-2">
													<h5><?php echo $teacherCategory['teacher_category']; ?></h5>
												</div>
												<div class="col-sm-3">
													<select class="form-control diagnostic_dd" autocomplete="off" name="teacher_cat[<?php echo $teacherCategory['teacher_category_id']; ?>]" >
														<option value=""> - Select Diagnostic - </option>
														<?php
														foreach($teacherCategory['category_diagnostic'] as $diagnostic)
															echo "<option value=\"".$diagnostic['diagnostic_id']."\">".$diagnostic['translation_text']."</option>\n";
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
                                                                                                                <option value="<?php echo $val['aqs_round'] ?>"><?php echo $val['aqs_round'] ?></option>          
                                                                                                    <?php            
                                                                                                }
                                                                                                ?>
											</select>
										</div></div></dd>
									</dl>
									
									<!--<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" title="Click to create a review." value="Create Review" class="btn vtip btn-primary">
												</div>
											</div>
										</dd>
									</dl>-->
									
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                                                                <div class="text-right" style="padding-top:5px;">
								<input type="submit" title="Click to create a review." value="Create Review" class="btn vtip btn-primary">
                                                                </div>
							</form>
						</div>
					</div>
				</div></span>