<h1 class="page-title">
		Edit Filter
</h1>
<div class="clr"></div>
<div class="">
	<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
	<div class="subTabWorkspace pad26">
		<div class="form-stmnt">
			<form method="post" id="edit_filter_form" action="">
			<input type="hidden" id="fisdashboard" class="isDashboard" value="<?php echo $isDashboard;?>">
			<input type="hidden" name="filter_id" value="<?php echo $filterData[0]['filter_id']; ?>" id="fId"> 
				<div class="boxBody">
					<dl class="fldList">
					<dt>Filter Name:</dt>
							<dd><div class="row"><div class="col-sm-6">								
								<input type="text" autocomplete="off" name="fliter_name" id="filter_id" class="form-control" maxlength="25" value="<?php echo $filterData[0]['filter_name']; ?>" required style="width:50%;"> 
					</div></div></dd>
					</dl>
					<div class="addBtnWrap">
					<?php if(!(strtolower($filterData[0]['filter_name'])=='default filter')){?>
						<a href="javascript:void(0)" class="filterRowAdd"><i class="fa fa-plus vtip" title="Click to add more rows."></i></a>	
					<?php } ?>	
						<div class="vertScrollArea">
							<div class="tableHldr noShadow filter_table">
							
							<table class="table customTbl">
											<thead>
												<tr><th style="width:5%">Sr. No.</th><th>Attribute</th><th>Operator</th><th style="width:40%">Value</th><th style="width:5%;"></th></tr>	
											</thead>
							<?php
							$i=0;
							foreach ($filterData as $param):
								$i++;
								$row = customreportModel::getFilterRow($i,$param,$isDashboard);
								echo $row;
							endforeach;	
							?>
							</table>
							</div>	
						</div>	
					</div>
					<?php if(!(strtolower($filterData[0]['filter_name'])=='default filter')){?>
					<div class='clearfix'>
						<div class='pull-right'>
							<input type="submit" title="Update Filter." value="Update" class="btn btn-primary vtip">
						</div>
					</div>	
					<?php } ?>
				</div>				
				<div class="ajaxMsg"></div>
				<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
			</form>		
							
		</div>
	</div>
</div>
<!-- Initialize the plugin: -->
<script type="text/javascript">
    $(document).ready(function() {
    	 $('.mulFilterValue').multiselect({  
         	numberDisplayed : 1,
         	onChange : function(){
         		$("#create_filter_form").find('.btn-group .multiselect').addClass('error');
             	}
           }); 
        $(".vertScrollArea").mCustomScrollbar({theme:"dark"});        
    });
</script>