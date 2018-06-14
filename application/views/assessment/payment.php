<h1 class="page-title">
Choose Product Package
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
							<form method="post" id="choose_product_form" action="">
								<div class="boxBody">
								<input type="hidden" name="assessment_id" value="<?php echo $_GET['assessment_id'];?>" >
									<dl class="fldList">
										<dt>Product<span class="astric">*</span>:</dt>										
										<dd>											
											<div class="chkBpxPane">
											<?php 
											setlocale(LC_MONETARY, 'en_IN');
											foreach($products as $product)
												echo '<div class="chkHldr" style="width:auto;float:none;">
													<input autocomplete="off" type="radio" name="product" required  value="'.$product['product_id'].'"><label class="chkF radio topMargin"><span><b>'.ucfirst($product['product_name']).'</b><span class="highLbl">INR '.number_format($product['amount'],2).'</span><br/> Validated reviews: '.$product['validated_reviews'].', Self-Reviews: '.$product['self_reviews'].'<br/> Valid for '.$product['validity'].' years, Assitance: '.$product['assist'].'</span></label>
												</div><br/>';
											?>																																			
											</div>
											
										</dd>
									</dl>
									<dl class="fldList">
										<dt>Mode of Payment<span class="astric">*</span>:</dt>										
										<dd>											
											<div class="chkBpxPane">
											<?php 											
											foreach($paymentModes as $pmode)
												echo '<div class="chkHldr">
													<input autocomplete="off" type="radio" name="payment_mode_id" required  value="'.$pmode['payment_mode_id'].'"><label class="chkF radio"><span><b>'.ucfirst($pmode['payment_mode_text']).'</b></label>
												</div>';
											?>																																			
											</div>
											
										</dd>
									</dl>
									<dl class="fldList">
										<dd><div style="align:center;margin-top:30px;margin-left:25px;"><input type="submit" title="Click to buy package"  value="Submit" class="btn btn-primary vtip"></div></dd>
									</dl>
									
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
								</div>	
							</form>
						</div>
</div>									