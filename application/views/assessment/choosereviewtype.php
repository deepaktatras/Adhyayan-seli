<h1 class="page-title">
Information<!-- Choose Review Type -->
</h1>
<div class="clr"></div>
<div class="">
	<div class="ylwRibbonHldr">
		<div class="tabitemsHldr"></div>
	</div>
</div>
<a href="?controller=assessment&action=createSchoolSelfAssessment" data-size="800" class="btn btn-primary pull-right execUrl" id="showschoolselfrev" style="display:none;">Self-Review</a>
<div class="subTabWorkspace pad26">
						<div class="form-stmnt">						
							<form method="post" id="choose_review_type_form" action="" >
								<div class="boxBody">
									<dl class="fldList">
										<dt>Review Type<span class="astric">*</span>:</dt>										
										<dd><div class="row"><div class="col-sm-6">											
											<div class="chkBpxPane">
											<?php 
											foreach($reviewTypes as $key=>$type)
												echo '<div class="chkHldr" style="width:auto;float:none;"><input autocomplete="off" type="radio" name="reviewtype" required '.($key==0?'checked=checked':'').' value="'.$type['sub_assessment_type_id'].'"><label class="chkF radio"><span>'.ucfirst($type['sub_assessment_type_name']).'</span></label></div>';
											?>																																			
											</div>
											
										</div></div></dd>
									</dl>
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" title="Submit review type."  value="Submit" class="btn btn-primary vtip">
												</div>
											</div>
										</dd>
									</dl>
									
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
								</div>	
							</form>
						</div>
</div>									