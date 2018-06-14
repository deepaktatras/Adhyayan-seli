<h4 class="page-title row">Clone Diagnostic</h4>
<div class="ylwRibbonHldr">
	<div class="tabitemsHldr">&nbsp;</div>
</div>
<div class="subTabWorkspace pad26 sortable-form" role="document">
	<form id="clone_diagnostic_frm">
	<input type="hidden" name="diagnostic_id" id="id_diagnostic_id" value="<?php echo $diagnosticId; ?>" />
        <input type="hidden" name="langId" id="langId" value="<?php echo $langId; ?>" />
		<div class="boxBody">
			<dl class="fldList">
				<dt>Diagnostic Template :</dt>
				<dd>
					<div class="row">
						<div class="col-sm-6"><?php echo $diagnosticName; ?></div>
					</div>
				</dd>
			</dl>
			<dl class="fldList">
				<dt>Review Type :</dt>
				<dd>
					<div class="row">
						<div class="col-sm-6"><?php echo $diagnosticType; ?></div>
					</div>
				</dd>
			</dl>
			<dl class="fldList">
				<dt>
					Diagnostic Name <span class="astric">*</span>:
				</dt>
				<dd>
					<div class="row">
						<div class="col-sm-6">
							<input class="form-control" title="Enter Diagnostic Name" type="text"
								class="form-control vtip" value="" name="cloned_diagnostic_name" id="id_cloned_diagnostic_name"
								required />
						</div>
					</div>
				</dd>
			</dl>
			<dl class="fldList">
			<dt></dt>			
			<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" title="Click to copy the diagnostic" value="Submit" class="btn vtip btn-primary">
												</div>
											</div>
										</dd>
			</dl>							
			
			<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
		</div>
	</form>
</div>
