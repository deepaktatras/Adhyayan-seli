			<?php
			//print_r($provinceData);
			if(!empty($provinceData)){ ?>
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
				
						Update Province
				</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26">
						<div class="form-stmnt">
							<form method="post" id="update_network_province_form" action="">
								<div class="boxBody">
									<dl class="fldList">
										<dt>Network Name :</dt>
										<dd><div class="col-sm-6"><input type="text" value="<?php echo $provinceData['network_name']; ?>" class="form-control" disabled name="network" required /></div></dd>
									</dl>
									<dl class="fldList">
										<dt>Province Name<span class="astric">*</span>:</dt>
										<dd class="the-basics province"><div class="col-sm-6"><input type="text" class="form-control typeahead tt-query" value="<?php echo $provinceData['province_name']; ?>" name="name" required /></div></dd>
									</dl>
									<dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" value="Update Province" class="btn btn-primary">
												</div>
											</div>
										</dd>
									</dl>
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
								<input type="hidden" value="<?php echo $provinceData['province_id']; ?>" name="id" />
							</form>
						</div>
						<br>
						<div class="tableHldr withAddRow">
							<a class="fltdAddRow execUrl" style="z-index:98;" href="?controller=network&action=addSchoolToProvince&network_id=<?php echo $provinceData['network_id']; ?>&province_id=<?php echo $provinceData['province_id']; ?>"><i class="fa fa-plus"></i></a>
									<table id="schoolsInProvince" class="cmnTable">
										<thead>
											<tr>
												<th>School Name</th>
												<th>Address</th>
												<th class="pr20">Action</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($clients as $client){ 
												echo networkModel::getEditSchoolsInnetworkProvinceRowHtml($provinceData['province_id'],$client);
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
	var provinces=[];
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
			//console.log('render')		
		});	
	 
}); 			
</script>						