			<?php if(isset($eNetwork['network_id'])){ ?>
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
				
						Update Network
				</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26">
						<div class="form-stmnt">
							<form method="post" id="update_network_form" action="">
								<div class="boxBody">
									<dl class="fldList">
										<dt>Network Name<span class="astric">*</span>:</dt>
										<dd class="the-basics network"><div class="row"><div class="col-sm-6"><input type="text" value="<?php echo $eNetwork['network_name']; ?>" class="form-control typeahead tt-query"  name="name" required /></div></div></dd>
									</dl>
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" value="Update Network" class="btn btn-primary">
												</div>
											</div>
										</dd>
									</dl>
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
								<input type="hidden" value="<?php echo $eNetwork['network_id']; ?>" name="id" />
							</form>
						</div>
						<br>
						<div class="tableHldr withAddRow">
							<a class="fltdAddRow execUrl" style="z-index:98;" href="?controller=network&action=addSchoolToNetwork&network_id=<?php echo $eNetwork['network_id']; ?>"><i class="fa fa-plus"></i></a>
									<table id="schoolsInNetwork" class="cmnTable">
										<thead>
											<tr>
												<th>School Name</th>
												<th>Address</th>
												<th class="pr20">Action</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($clients as $client){ 
												echo networkModel::getEditSchoolsInnetworkRowHtml($eNetwork['network_id'],$client);
											 } ?>
										</tbody>
									</table>
								</div>
						
					</div>
				</div>
			<?php }else{ ?>
			<h1>Network does not exist</h1>
			<?php } ?>
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
	  
	  $('.network .typeahead').bind('typeahead:select', function(ev, suggestion) {	
		$('.typeahead').typeahead('val','');
		 alert("This network already exists! Please type new network name.");		
		 
	  });		
}); 			
</script>			