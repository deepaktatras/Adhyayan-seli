<?php
$actions=array(array("action_id"=>1,"action_name"=>"New Diagnostic"),array("action_id"=>2,"action_name"=>"Existing Diagnostic in New language"));
?>
<!--<div class="modal-content">      -->
      <!--<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>-->
          <h4 class="page-title">Create/Translate New Diagnostic</h4>
      <!--</div>
     <!-- <div class="modal-body pad0">-->
          <div class="ylwRibbonHldr">
             <div class="tabitemsHldr">&nbsp;</div>
          </div>
          <div class="subTabWorkspace pad26">
              <div class="form-stmnt">
                <form method="post" id="diagnosticType" action="">
                    <input type="hidden" name="diagnostic_id_initial" id="diagnostic_id_initial" value="" >
                    <input type="hidden" name="equivalence_id" id="equivalence_id" value="" >
                    <input type="hidden" name="lang_id_original" id="lang_id_original" value="" >
                    <input type="hidden" name="diag_name" id="diag_name" value="" >
                    <input type="hidden" name="lang_name" id="lang_name" value="" >
                    <input type="hidden" name="kpa_recommendations_p" id="kpa_recommendations_p" value="" >
                    <input type="hidden" name="kq_recommendations_p" id="kq_recommendations_p" value="" >
                    <input type="hidden" name="cq_recommendations_p" id="cq_recommendations_p" value="" >
                    <input type="hidden" name="js_recommendations_p" id="js_recommendations_p" value="" >
                    <input type="hidden" name="diagnostic_id_parent" id="diagnostic_id_parent" value="" >
                    
                    <div class="boxBody">
                        <dl class="fldList">
                            <dt>Action<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control" name="assessment_action" id="assessment_action" required="">
										<option value=""> - Select Action - </option>
										<?php foreach($actions as $action): 
										
										?>
                                           <option value="<?php echo $action['action_id']; ?>"><?php echo $action['action_name']; ?></option>
										<?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
								
                            </dd>
			</dl>
                        
                        <dl class="fldList" style="display:none;" id="diagnostic_id_sh">
                            <dt>Diagnostic<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control" name="diagnostic_id_parent_show" id="diagnostic_id_parent_show" required="">
										<option value=""> - Select Diagnostic - </option>
										<?php foreach($diagnostics as $diagnostic): 
											
										?>
                                           <option value="<?php echo $diagnostic['equivalence_id']; ?>"><?php echo $diagnostic['name']; ?></option>
										<?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
								
                            </dd>
			</dl>
                        
                        <dl class="fldList" style="display:none;" id="diagnostic_id_sh_lang">
                            <dt>Available Diagnostic Languages<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control" name="diagnostic_id_parent_lang" id="diagnostic_id_parent_lang" required="">
										<option value=""> - Select Language - </option>
										
                                        </select>
                                    </div>
                                </div>
								
                            </dd>
			</dl>
                        
                        
                        <dl class="fldList" style="display:none;" id="language_id_sh">
                            <dt><span id="lag_label">Language</span><span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control" name="language_id" id="language_id" required="">
										<option value=""> - Select Language - </option>
										<?php foreach($diagnosticsLanguage as $language): 
											
										?>
                                           <option value="<?php echo $language['language_id']; ?>"><?php echo $language['language_name']; ?></option>
										<?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
								
                            </dd>
			</dl>
                        <dl class="fldList" style="display:none;" id="assessment_type_sh">
                            <dt>Type of Review<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control" name="assessment_type" id="assessment_type" required="">
										<option value=""> - Select Review Type - </option>
										<?php foreach($assessmentTypes as $assessment): 
											if($assessment['assessment_type_id']==3)
												continue;
										?>
                                           <option value="<?php echo $assessment['assessment_type_id']; ?>"><?php echo ucfirst($assessment['assessment_type_name']); ?></option>
										<?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
								
                            </dd>
			</dl>
						<dl class="fldList" id="teacherDiv" style="display:none;">							
							<dt>Type of Teacher<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control" name="teacher_type" id="teacher_type" required="">
										<option value=""> - Select Teacher Type - </option>
										<?php foreach($teacherCategories as $teacher): 
								
										?>
                                           <option value="<?php echo $teacher['teacher_category_id']; ?>"><?php echo $teacher['teacher_category']; ?></option>
										<?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>								
                            </dd>
                        </dl>

                        <dl class="fldList">
                            <dd class="nobg">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <br>
                                        <input type="button" id="choose_assessment" title="Click continue to proceed further with diagnostic creation process." value="Continue" class="btn vtip btn-primary">
                                    </div>
                                </div>
                            </dd>
                        </dl>
                    </div>
                    <div class="ajaxMsg"></div>
                    <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="1">
                </form>
            </div>
          </div>
          
     <!-- </div>-->
    <!--</div>--><!-- /.modal-content -->