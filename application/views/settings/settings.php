				<h1 class="page-title">Manage Roles &amp; Capabilities</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26">
						<div class="form-stmnt">
							<form method="post" id="manage_roles_form" action="">
											
								<div class="boxBody">
								<?php foreach($roles as $role){
									$cCaps=$role['cap_ids']!=""?explode(",",$role['cap_ids']):array();
									?>
									<dl class="fldList">
										<dt><?php echo $role['role_name']; ?><span class="astric">*</span>:</dt>
										<dd>
											<div class="clearfix">
											<?php
											foreach($capabilities as $capability)
												echo '<div style="width:32.33%;margin-right:0;margin-left:1%" class="chkHldr"><input type="checkbox" class="user-roles" name="roles['.$role['role_id'].'][]" autocomplete="off" value="'.$capability['capability_id'].'" '.(in_array($capability['capability_id'],$cCaps)?'checked="checked"':"").'><label class="chkF checkbox"><span>'.$capability['slug'].'</span></label></div>'."\n";
											?>
											</div>
										</dd>
									</dl>
									<br>
								<?php } ?>
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" value="Update Roles" class="btn btn-primary">
													<input type="hidden" value="1" name="update" />
												</div>
											</div>
										</dd>
									</dl>
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
							</form>
						</div>
					</div>
				</div>