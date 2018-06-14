				<h1 class="page-title">
				<?php if($isPop==0){?>
				<a href="<?php
						$args=array("controller"=>"network","action"=>"network");						
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
					Manage Networks
					</a> &rarr;				
				<?php } ?>				
					Add Province to network
				</h1>
				<div class="clr"></div>
				<div>
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26">
						<div class="form-stmnt">
							<form method="post" id="create_province_form" action="">
											
								<div class="boxBody">
                                                                    <div class="addFldHldr">
									<dl class="fldList">
										<dt>Network<span class="astric">*</span>:</dt>
                                                                                <dd class="inputHldr">
											<div class="row">
												<div class="col-sm-6 width-50-modal">
													<select class="form-control network-list-dropdown" name="network_id" required>
														<option value=""> - Select Network - </option>
														<?php
														foreach($networks as $network)
															echo "<option value=\"".$network['network_id']."\">".$network['network_name']."</option>\n";
														?>
													</select>
												</div>
												<div class="col-sm-3 width-50-modal text-right">
													<a href="?controller=network&action=createNetwork&ispop=1" class="btn btn-primary execUrl vtip" title="Click to add network." id="addNetworkBtn">Add New</a>
												</div>
											</div>
										</dd>
									</dl>
									<dl class="fldList provinceField">
										<dt>Province Name<span class="astric">*</span>:</dt>
										
										<dd class="the-basics province inputHldr"><input type="text" value="" class="form-control typeahead tt-query" name="province_name[]" required /></dd>
									</dl>
                                                                        <button type="button" class="btn btn-primary addnewprovince"><i class="fa fa-plus"></i></button>
                                                                    </div>
                                                                    
									<dl class="fldList btnHldr">
										<dd class="nobg inputHldr">
                                                                                    <div class="clearfix">
                                                                                        <div class="pull-left"><input type="submit" value="Submit" class="btn btn-primary"></div>
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
<script>
$(document).ready(function(){
		provinces=[];
		<?php foreach($provinces as $province){ ?>
			provinces.push('<?php echo $province['province_name']; ?>');
		<?php } ?>        	       
      $('.the-basics .typeahead').typeahead({
        hint: true,
        highlight: true,
        minLength: 3		
      },
      {
        name: 'provinces',
        source: substringMatcher(provinces),
		limit:30
      });
	  
	  $('.province .typeahead').bind('typeahead:select', function(ev, suggestion) {	
		$('.typeahead').typeahead('val','');
		 alert("This province already exists! Please type new province name.");		
		 
	  });
		$('.province .typeahead').bind('typeahead:render', function(ev, suggestion) {			
			console.log('render')		
		});						
}); 		
</script>				