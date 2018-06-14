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
	Create Overview Report</h1>
	<div class="clr"></div>
	<div>
		<div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
		</div>
		<div class="subTabWorkspace pad26">
			<div class="form-stmnt">
				<form method="post" id="create_network_report_form" action="">
					<input type="hidden" name="report_id" value="6"> 
					<input type="hidden" name="clients" id="id_clients" value=""> 					
					<a class='execUrl' id='create-filter-pop'
						href="?controller=customreport&action=createfilter"
						data-width="800" style="display: none;">click</a> <a
						class='execUrl' id='edit-filter-pop'
						href="?controller=customreport&action=editfilter" data-width="800"
						style="display: none;">click</a>
					<div class="boxBody">
						
						<dl class="fldList">
							<dt>
								Filter<span class="astric">*</span>:
							</dt>
							<dd>
								<div class="row">
									<div class="col-sm-6">
										<select name="filter_name" id="create-filter-drop"
											data-placeholder="Search Filter">
											<option value=""></option>
											<option value="0">Create new filter</option>
                                                                    <?php
                                                                    
																																																																				foreach ( $filters as $filter )
																																																																					echo "<option value='" . $filter ['filter_id'] . "'>" . $filter ['filter_name'] . "</option>";
																																																																				?>											
                                                                 </select>
									</div>
									<input id="applyFilter" type="button" title="Click to apply the filter."
									value="Apply" class="btn btn-primary vtip"
									style="display: none;"> <input id="editFilter" type="button"
									title="Click to make changes in selected filter." value="Edit" class="btn btn-primary vtip"
									style="display: none;margin-left:5px;">
								</div>
							</dd>
						</dl>
						
						<div id="schoolFltrSection" style="display: none;">
						<dl class="fldList">
							<dt>
								Name of the Report<span class="astric">*</span>:
							</dt>
							<dd>
								<div class="row">
									<div class="col-sm-6">									
										<input type='text' class='form-control' name='report_name' id='id_network_name' required>
									</div>
								</div>
							</dd>
						</dl>
						<div class='queryBoxWrapper' style="padding:20px 0 35px;">
							<!--<dl class="text-center fldList">
								<dd class="the-basics">
									<input id="searchbox" type="text"
										class="form-control typeahead tt-query"
										placeholder="Search .." autocomplete="off" spellcheck="false"> <a href="javascript:void(0);" id="clrBtn"
										class="vtip" title="Clear"><i class="fa fa-times-circle"></i></a>
								</dd>
							</dl>-->
							<div class="row">
								<div class="col-md-5">
								<h3 class="red">Filtered Schools<span id="num-filtered-schools"></span>
                                                                                	<em style="font-size:10px;font-weight:normal;padding-left:8px;">Press Ctr+click to select multiple schools.</em>
                                                                                </h3>
									<div class="leftQuestHldr">
                                                                                
										<div class="vertScrollArea" id='fltrSchools'>
											<ul id="sortableL" class="connectedSortable">
											</ul>
										</div>										
										
									</div>
									<div class="text-right mt30">
		                                   <input id='select-all-sortable' type="button" title="Click to select all schools." value="Select all" class="btn btn-primary vtip">
		                                   <input id='deselect-all-sortable' type="button" title="Click to deselect all schools." value="Deselect all" class="btn btn-primary vtip">
									</div>
								</div>
								<div class="col-md-2">
									<div class="sortInfoIcon text-center">
										<i class="fa fa-arrows-h"></i>										
									</div>
								</div>
								<div class="col-md-5">
									<h3 class="red">Selected Schools<span id="num-selected-schools"></span></h3>
									<div class="rightConfirmedQueryBox ">
                                                                            <div class="vertScrollArea" id='selectedSchools'>
										<ul id="sortableR" class="connectedSortable">

										</ul>
                                                                            </div>
									</div>
								</div>
							</div>
						</div>
						<dl class="fldList w120p">
							<dt>
								Review duration:
							</dt>
							<dd>
								<div class="row">
									<div class="col-sm-3">
										<input type="text" class="form-control" placeholder="From" id="frm-date"  disabled>
									</div>
									<div class="col-sm-3">
										<input type="text" class="form-control" placeholder="To" id="to-date"  disabled>
									</div>
								</div>
							</dd>
						</dl>
						<div class="clearfix chkBpxPane">
							<div class="chkHldr ml0 mt20" style="width: 300px;">
								<input autocomplete="off" id="self_review_only"
									type="checkbox" value="1" name="include_self_review"><label
									class="chkF checkbox"><span><b>Include 7<sup>th</sup> KPA Analysis</b></span></label>
							</div>							
						</div>
						<div class="clearfix chkBpxPane" id="validatedRow" style="display:none;">
							<div class="chkHldr m10 mt20"><input type="radio" autocomplete="off" name="is_validated" value="1"><label class="chkF radio"><span>Validated</span></label></div>
							<div class="chkHldr m10 mt20"><input type="radio" autocomplete="off" name="is_validated" value="0"><label class="chkF radio"><span>Not validated </span></label></div>
						</div>
						

						
								<div class="addBtnWrap mt20"><h5>Adhyayan Recommendations</h5>
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
								$row = customreportModel::getExperienceRow();
								echo $row;													
							?>
											<!--</tbody>
										</table>                                                                            
									</div>-->
								</div>
						

						<div class='text-right mt30'>
								<button type="submit" title="Click to generate PDF Report." class="btn btn-primary vtip"><i class="fa fa-cog"></i>Generate Report</button>									
								<a id='view-network-pdf'
						href="?controller=customreport&action=generateNetworkReportPDF" title="Click to view Report." target='_blank' class="btn btn-primary vtip" style="display: none;"
						 ><i class="fa fa-eye"></i>View Report</a>
						 <a id='view-ds-pdf'
						href="?controller=customreport&action=generateDataSummary" title="Click to view Data Summary Report." target='_blank' class="btn btn-primary vtip" style="display: none;"
						 ><i class="fa fa-eye"></i>View Data-summary Report</a>
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
