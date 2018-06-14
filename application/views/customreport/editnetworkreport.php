<div class="filterByAjax filter-list" data-action="network"
	data-controller="customreport">
	<h1 class="page-title">
	<a href="<?php
						$args=array("controller"=>"customreport","action"=>"networkreportlist");												
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						Manage MyOverview Reports
					</a> &rarr;
	Edit Overview Report</h1>
	<div class="clr"></div>
	<div>
		<div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
		</div>
		<div class="subTabWorkspace pad26">
			<div class="form-stmnt">
				<form method="post" id="edit_network_report_form" action="">
					<input type="hidden" name="report_id" value="6"> 
					<input type="hidden" name="network_report_id"  value="<?php echo $network_report_id;?>"> 
					<input type="hidden" name="report_name"  value="<?php echo $report_name;?>"> 					
				
					<div class="boxBody">					
						<div id="schoolFltrSection" >
						<dl class="fldList">
							<dt>
								Name of the Report<span class="astric">*</span>:
							</dt>
							<dd>
								<div class="row">
									<div class="col-sm-6">									
										<input type='text' class='form-control' disabled='disabled' id='id_network_name' value="<?php echo $report_name;?>" required>
									</div>
								</div>
							</dd>
						</dl>						
                                                    <div class="fldList"><h5>Adhyayan Recommendations</h5>
									<!--<div class="teamsInfoHldr">
                                                                            
										<a href="javascript:void(0)" class="fltdAddRow exp"
											data-type="adhyayan"><i class="fa fa-plus vtip" title="Click to add more rows."></i></a>
										<table class="table customTbl">
											<thead>
												<tr>
													<th style="width:8%">Sr. No.</th>
													<th style="width:82%;text-align:left;">Adhyayan Recommendations</th>
													<th style="width:10%"></th>
												</tr>
											</thead>
											<tbody>-->
							<?php 
                                                       
							if(empty($experience)){
								$row = customreportModel::getExperienceRow();
								echo $row;
							}
							else{
								$i=0;										
								foreach($experience as $exp):
									$i++;
									$row = customreportModel::getExperienceRow($i,$exp);
									echo $row;
								endforeach;
							}
							?>
											<!--</tbody>
										</table>                                                                            
									</div>-->
								</div>
                                                    <!--<small><b>Note:-</b><ul><li style="font-weight:200"> To insert a page-break, open the source Source Code(Tools->Source Code) and type <?php echo htmlspecialchars("<p style='page-break-after: always;'>&nbsp;</p>");?> at a desired place. Click OK. Example:<?php echo htmlspecialchars("line 1<p style='page-break-after: always;'>&nbsp;</p>line 2");?> will break the line1 and line2 so that these lines appear on different pages.</li>
								 
							</ul>	
						</small>-->	
						<div class='text-right mt30'>
								<button type="submit" title="Click to update PDF Report." class="btn btn-primary vtip"><i class="fa fa-cog"></i>Update Report</button>									
								<a id='view-network-pdf'
						href="?controller=customreport&action=generateNetworkReportPDF" title="Click to view Report." target='_blank' class="btn btn-primary vtip" style="display: none;"
						 ><i class="fa fa-eye"></i>View Report</a>
						</div>
						<div class='clearfix'></div>
						<div class="ajaxMsg"></div>
						<input type="hidden" class="isAjaxRequest" name="isAjaxRequest"
							value="<?php echo 1; ?>" />
					</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
    tinymce.init({
  selector: '.recommendations-text',
  height: 300,
  statusbar:false,
  menubar: "tools",
  plugins: [
    'advlist autolink lists ',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime table contextmenu paste code',
    'textcolor',
    'code',
    'pagebreak',
  ],
  toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor | code | pagebreak',
  pagebreak_separator : '<p style="page-break-after: always;">&nbsp;</p>'
});
 $filterSelect = $('#create_network_report_form').find('select').selectize({
    sortField: {
        field: 'value',
        direction: 'asc'
    },
    dropdownParent: 'body'});
	$(".vertScrollArea").mCustomScrollbar({theme:"dark"});		
	$( "#sortableL, #sortableR" ).on('click', 'li', function (e) {
	    if (e.ctrlKey || e.metaKey) {
	        $(this).toggleClass("selected");
	    } else {
	        $(this).addClass("selected").siblings().removeClass('selected');
	    }
	}).sortable({
		 connectWith: ".connectedSortable",
		    delay: 150, //Needed to prevent accidental drag when trying to select
		    revert: 0,
		    helper: function (e, item) {
		        var helper = $('<li/>');
		        if (!item.hasClass('selected')) {
		            item.addClass('selected').siblings().removeClass('selected');
		        }
		      	var elements = item.parent().children('.selected').clone();
		       	item.data('multidrag', elements).siblings('.selected').remove();
		        return helper.append(elements);
		    },
		    stop: function (e, info) {
		        info.item.after(info.item.data('multidrag')).remove();
		        selectedClients();
		    }
	});
	
	
});

</script>
