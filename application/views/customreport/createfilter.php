<h1 class="page-title">
		Add Filter
</h1>
<div class="clr"></div>
<div class="">
	<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
	<div class="subTabWorkspace pad26">
		<div class="form-stmnt">
			<form method="post" id="create_filter_form" action="">
			<input type="hidden"  id="fisdashboard" class="isDashboard"  value="<?php echo $isDashboard;?>">
				<div class="boxBody">				
					<dl class="fldList">
					<dt>Filter Name:</dt>
							<dd><div class="row"><div class="col-sm-6">								
								<input type="text" autocomplete="off" name="fliter_name" id="filter_id" class="form-control" maxlength="25"  required style="width:50%;"> <span>(25 characters only)</span>
					</div></div></dd>
					</dl>
					<div class="addBtnWrap">
						<a href="javascript:void(0)" class="filterRowAdd"><i class="fa fa-plus vtip" title="Click to add more rows."></i></a>	
						<div class="vertScrollArea">
							<div class="tableHldr noShadow filter_table">
							
							<table class="table customTbl">
											<thead>
												<tr><th style="width:5%">Sr. No.</th><th>Attribute</th><th>Operator</th><th style="width:40%">Value</th><th style="width:5%;"></th></tr>	
											</thead>
							<?php 
								$row = customreportModel::getFilterRow(1,0,$isDashboard);
								echo $row;
							?>
							</table>
							</div>	
						</div>	
					</div>
					<div class='clearfix'>
						<div class='pull-right'>
							<input type="submit" title="Save Filter." value="Save" class="btn btn-primary vtip">
						</div>
					</div>	
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
        $(".vertScrollArea").mCustomScrollbar({theme:"dark"});
        $('.mulFilterValue').multiselect({  
        	numberDisplayed : 1,
        	onChange : function(){
        		$("#create_filter_form").find('.btn-group .multiselect').addClass('error');
            	}
          });         	   
    });
</script>