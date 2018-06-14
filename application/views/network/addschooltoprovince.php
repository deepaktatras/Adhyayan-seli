				<h1 class="page-title">
					Add School to Province
				</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26">
						<div class="form-stmnt">
							<form method="post" id="add_school_to_province_form" action="">		
								<div class="boxBody">
									<dl class="fldList">
										<dt>Schools<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-6">
											<select class="form-control" multiple="multiple" name="client_ids[]" required>
											<?php
											foreach($clients as $client){
												echo '<option value="'.$client['client_id'].'">'.$client['client_name'].'</option>';
											}
											?>
											</select>
										</div></div></dd>
									</dl>
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" value="Add to Province" class="btn btn-primary">
												</div>
											</div>
										</dd>
									</dl>
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden"  name="province_id" value="<?php echo $province_id; ?>" />
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
							</form>
						</div>
					</div>
				</div>