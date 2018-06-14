				<h1 class="page-title">
				Generate Data Summary Report
				</h1>
				<div class="clr"></div>
                                
                                <div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
                                <div class="subTabWorkspace pad26">
                                    <div class="form-stmnt">
                                        <form method="GET" id="generatecustomdatasummary" action="?" target="_blank">
                                       <input type="hidden" name="controller" value="customreport">
                                       <input type="hidden" name="action" value="generatedatasummary">
                                       <input type="hidden" name="network_report_id" value="<?php echo $network_report_id ?>">
                                       <input type="hidden" name="custom_report" value="1">
                                       <div class="boxBody">
                                          
                                      <dl class="fldList">
                                          <?php //print_r($network_report); ?>
                                          <div style="padding-bottom: 5px;"><span style="font-weight: bold;">Report Name:</span> <?php echo isset($network_report['report_name'])?$network_report['report_name']:''; ?></div>
                                          <span style="font-weight: bold;">Include the following in Report:</span><br>
                                          <input type="checkbox" name="report_point3" value="1" checked="checked"> Post review form data<br>
                                          <input type="checkbox" name="report_point5" value="1" checked="checked"> Other information/Comments about the school
                                      </dl>
                                         <dl class="fldList">
										
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" title="Click to create a review."  value="Generate Report" class="btn btn-primary vtip">
												</div>
											</div>
										</dd>
									</dl>
                                     </div>
                                        </form>
                                    </div>
                                </div>
                                </div>