<?php
// print_r($data);
$filters_json = json_encode ( $filters );
if (! empty ( $data )) {
	$awardsMatrix = array ();
	$i = 0;
	foreach ( $data as $row ) {
		$awardsMatrix [$i] ['award'] = ($row ['standard_name'] ? $row ['standard_name'] . " " : '') . $row ['award_name'];
		$awardsMatrix [$i] ['default'] = $row ['num'];
		$i ++;
	}
}
?>
<a class='execUrl' id='create-filter-pop'
	href="?controller=customreport&action=createfilter&isDashboard=1"
	data-width="800" style="display: none;">click</a>
<a class='execUrl' id='edit-filter-pop'
	href="?controller=customreport&action=editfilter&isDashboard=1"
	data-width="800" style="display: none;">click</a>
<div class="filterByAjax filter-list"
	data-action="<?php echo $this->_action;?>"
	data-controller="<?php echo $this->_controller;?>">
	<h1 class="page-title">
		My Dashboard
		<div class="clr"></div>
	</h1>
	<div>
		<form method="post" id="admin_dashboard_frm" action="">
			<div class="ylwRibbonHldr">
				<div class="tabitemsHldr"></div>
			</div>
			<div class="subTabWorkspace pad26">
				<div class="form-stmnt myDashboard">

					<div class="row">
						<div class="col-md-12">
							<div class="clearfix">
								<dl class="fldList">
									<dt>
										<label>Filter(Optional):</label>
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
											<input id="editFilter" type="button"
												title="Click to edit the selected filter." value="Edit"
												class="btn btn-primary vtip"
												style="margin-left: 5px; display: none;"> <input
												id="applyFilterHidden" type="button"
												title="Click to apply the selected filter." value="Apply"
												class="btn btn-primary vtip"
												style="margin-left: 5px; display: none;">
												<input
												 type="reset"
												title="Click to clear the filter." value="Clear"
												class="btn btn-primary vtip clearFilter"
												style="margin-left: 5px;">
										</div>

									</dd>
								</dl>
							</div>
						</div>
						<div class="col-md-7" id="filters-tag" style="display: none;">
							<div class="clearfix">
								<div class="fr">
									<input type="hidden" class="selectedfilters"
										name="selectedfilters" />
									<div class="currentSelection tag_boxes clearfix"
										data-trigger="selFiltersUpdate">
										<span class="empty">None Selected</span>
									</div>
									<div class="padB10 inline">
										<a class="btn btn-primary execUrl vtip"
											title="Click to choose and apply your filter."
											href="?controller=customreport&action=dashboardfilters"
											id="sel-filters-link" data-size="680">Choose and apply filter</a>
									</div>
								</div>
							</div>
						</div>
					</div>


				</div>
				<div class="row sortlistcnt" id="varList">
					<div class="col-md-12">
						<!-- <h4>Rows and columns</h4> -->
						<div class='queryBoxWrapper' style="padding-top:20px;">
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
									<h3 class="red">
										List of all variables<span id="num-filtered-vars"></span> <em
											style="font-size: 10px; font-weight: normal; padding-left: 8px;">Press
											Ctr+click to select multiple items.</em>
									</h3>
									<div class="leftQuestHldr">

										<div class="vertScrollArea">
											<ul id="sortableL" class="connectedSortable">
											</ul>
										</div>

									</div>
									<div class="text-right mt30">
										<input id='select-all-sortable' type="button"
											title="Click to select all variables." value="Select all"
											class="btn btn-primary vtip"> <input
											id='deselect-all-sortable' type="button"
											title="Click to deselect all variables." value="Deselect all"
											class="btn btn-primary vtip">
									</div>
								</div>
								<div class="col-md-2">
									<div class="sortInfoIcon text-center">
										<i class="fa fa-arrows-h" style="padding-top:156px;"></i>
									</div>
								</div>
								<div class="col-md-5">
									<h3 class="red">
										Selected Variables<span id="num-selected-vars"></span>
									</h3>
									<h5 class="red">Columns - Select one variable only</h5>
									<div class="rightConfirmedQueryBox ">
										<div class="vertScrollArea">
											<ul id="sortableRcol" class="connectedSortable">
											</ul>
										</div>
									</div>
									<h5 class="red">Rows - Select one variable only</h5>
									<div class="rightConfirmedQueryBox ">
										<div class="vertScrollArea">
											<ul id="sortableRrow" class="connectedSortable">

											</ul>
										</div>
									</div>
									<div class=" mt30">										
										<div class="chkHldr"><input type="radio" autocomplete="off" name="count_criteria" value="client_id" checked="checked"><label class="chkF radio"><span>Count of Schools </span></label></div>
										<div class="chkHldr"><input type="radio" autocomplete="off" name="count_criteria" value="assessment_id" ><label class="chkF radio"><span>Count of Reviews</span></label></div>									
									</div>
									<div class="text-right mt30">	
									<small><b>Note:-</b> <i>Disable pop-up blocker to view data</i></small>
									</div>
									<div class="text-right mt30">
										<!-- <input type="submit" value="Generate data" id="sbt-btn"
											class="btn vtip btn-primary" title="Click to generate data">
 											-->
										<a id="showdata" style="display:none;" target='_blank'
											href="?controller=customreport&action=ngdataview">Show Data</a>
											
										<a id="showButton" id="showButton" href="javascript:void(0);"
											class="btn vtip btn-primary disabled" title="Click to generate and view data.">
											Generate and View data
										</a>	
									</div>									
								</div>								
							</div>
						</div>
						<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
jQuery(document).ready(function($) {
// var $filterSelect = $('#admin_dashboard_frm').find('select').selectize({
 var $filterSelect = $('select').selectize({	 
    sortField: {
        field: 'value',
        direction: 'asc'
    },
    dropdownParent: 'body'});
	$(".vertScrollArea").mCustomScrollbar({theme:"dark"});		
	$( "#sortableL, #sortableRrow, #sortableRcol" ).on('click', 'li', function (e) {
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
		    }
	});	
	$(document).find("#admin_dashboard_frm #applyFilterHidden").trigger('click');
	$(document).on("click",".clearFilter",function(){		
		var control = $filterSelect[0].selectize;
		 control.clear(true);
		 $(document).find('#applyFilterHidden,#editFilter').hide();
	});
});
</script>