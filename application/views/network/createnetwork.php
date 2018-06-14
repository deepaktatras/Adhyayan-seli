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
					Add Network
				</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26">
						<div class="form-stmnt">
							<form method="post" id="create_network_form" action="">
											
								<div class="boxBody">
									<dl class="fldList">
										<dt>Network Name<span class="astric">*</span>:</dt>
										<dd class="the-basics network"><div class="row"><div class="col-sm-6"><input type="text" value="" class="form-control typeahead tt-query" name="name" required /></div></div></dd>
									</dl>
									<!-- <dl class="fldList provinceField">
										<dt>Province Name:</dt>
										
										<dd class="the-basics province"><div class="row"><div class="col-sm-6"><input type="text" value="" class="form-control typeahead tt-query" name="province_name[]" /></div></div></dd>
									</dl>
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row" style="float:right;">
												<div class="col-sm-6" >													
													<input type="button" value="Add Province Field" class="btn btn-primary addnewprovince">
												</div>
											</div>
										</dd>
									</dl> -->
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" value="Add Network" class="btn btn-primary">
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
<script>
$(document).ready(function(){
	networks=[];
	<?php foreach($networks as $network){ ?>
			networks.push('<?php echo $network['network_name']; ?>');
		<?php } ?>			  
	  $('.the-basics.network .typeahead').typeahead({
        hint: true,
        highlight: true,
        minLength: 3		
      },
      {
        name: 'networks',
        source: substringMatcher(networks),
		limit:30
      });
	  $('.network .typeahead').bind('typeahead:select', function(ev, suggestion) {	
		$('.network .typeahead').typeahead('val','');
		 alert("This network already exists! Please type a new network name.");		
		 
	  });
	  /* $('.the-basics.province .typeahead').typeahead({
        hint: true,
        highlight: true,
        minLength: 3		
      },
      {
        name: 'provinces',
        source: substringMatcher(networks),
		limit:30
      });
	  $('.province .typeahead').bind('typeahead:select', function(ev, suggestion) {	
		$('.province .typeahead').typeahead('val','');
		 alert("This Province already exists! Please type a new province name.");		
		 
	  }); */	 
}); 		
</script>