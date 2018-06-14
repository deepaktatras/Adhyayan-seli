<?php 
//print_r($data);
$filters_json = json_encode($filters);
if(!empty($data)){
$awardsMatrix = array();
	$i=0;
	foreach($data as $row){
		$awardsMatrix[$i]['award']=($row['standard_name']?$row['standard_name']." ":'').$row['award_name'];
		$awardsMatrix[$i]['default']=$row['num'];	
		$i++;
	}	
}
?>
<a class='execUrl' id='create-filter-pop'
						href="?controller=customreport&action=createfilter&isDashboard=2"
						data-width="800" style="display: none;">click</a> <a
						class='execUrl' id='edit-filter-pop'
						href="?controller=customreport&action=editfilter&isDashboard=2" data-width="800"
						style="display: none;">click</a>
<style>
.award rect{
   animation-name: pullUp;
	-webkit-animation-name: pullUp;	

	animation-duration: 1.3s;	
	-webkit-animation-duration: 1.3s;

	animation-timing-function: ease-out;	
	-webkit-animation-timing-function: ease-out;	

	transform-origin: 50% 100%;
	-ms-transform-origin: 50% 100%;
	-webkit-transform-origin: 50% 100%; 	
}
@keyframes pullUp {
	0% {
		transform: scaleY(0.1);
	}
	40% {
		transform: scaleY(1.02);
	}
	60% {
		transform: scaleY(0.98);
	}
	80% {
		transform: scaleY(1.01);
	}
	100% {
		transform: scaleY(0.98);
	}				
	80% {
		transform: scaleY(1.01);
	}
	100% {
		transform: scaleY(1);
	}							
}

@-webkit-keyframes pullUp {
	0% {
		-webkit-transform: scaleY(0.1);
	}
	40% {
		-webkit-transform: scaleY(1.02);
	}
	60% {
		-webkit-transform: scaleY(0.98);
	}
	80% {
		-webkit-transform: scaleY(1.01);
	}
	100% {
		-webkit-transform: scaleY(0.98);
	}				
	80% {
		-webkit-transform: scaleY(1.01);
	}
	100% {
		-webkit-transform: scaleY(1);
	}		
}
 rect#userAwarda { 
     -webkit-animation-name: userPositionAnim;  /*Chrome, Safari, Opera */ 
     -webkit-animation-duration: 4s;  /*Chrome, Safari, Opera */ 
     -webkit-animation-iteration-count: infinite;  /*Chrome, Safari, Opera */   
     animation-name: userPositionAnim; 
     animation-duration: 4s; 
     animation-iteration-count: infinite; 
     stroke: #000; 
     stroke-width: 1; 
     stroke-linejoin: bevel; 
 } 

 @-webkit-keyframes userPositionAnim { 
     0% {opacity:0.5;} 
     25% {opacity:0.75;} 
     50% {opacity:1;} 
     75% {opacity:0.75;} 
     100% {opacity:0.5;}  
 } 
 @keyframes userPositionAnim { 
     0%  {opacity:0.5;} 
     25% {opacity:0.75;} 
     50% {opacity:1;} 
     75% {opacity:0.75;} 
     100% {opacity:0.5;}  
 }  
</style>
<div class="filterByAjax filter-list" data-action="dashboard"
	data-controller="customreport">
	<h1 class="page-title">My Dashboard
	<!-- <a href="?controller=customreport&amp;action=createfilter&amp;ispop=1&amp;isDashboard=1" data-size="800" class="btn btn-primary pull-right execUrl" >Create Filter</a> -->	
	<div class="clr"></div>
	</h1>
	<div>
		<div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
		</div>
		<div class="subTabWorkspace pad26">
			<div class="form-stmnt myDashboard">
                            <form method="post" id="school_dashboard_frm" action="">				
				<div class="row">										
                                    <div class="col-md-5">
                                        <div class="clearfix">
                                            <dl class="fldList">
                                                <dt><label>Filter:</label></dt>
                                                <dd>
                                                    <select name="filter_name" id="create-filter-drop" data-placeholder="Search Filter">
                                                        <option value=""></option>
                                                        <option value="0">Create new filter</option>
                                                        <?php 
                                                        foreach ( $filters as $filter )
                                                                echo "<option value='" . $filter ['filter_id'] . "' ".($filter_id==$filter ['filter_id']?'selected=selected':'').">" . $filter ['filter_name'] . "</option>";
                                                        ?>																																							
                                                    </select>
                                                </dd>
                                            </dl>  
                                            <div class="pull-left">
                                                <input id="editFilter" type="button" title="Click to edit the selected filter." value="Edit" class="btn btn-primary vtip" style="margin-left:5px;display:none;">
                                            </div>
                                        </div>    
                                    </div>                                    
                                    <div class="col-md-7" id="filters-tag">
                                        <div class="clearfix">
                                            <div class="fr">
                                                <input type="hidden" class="selectedfilters" name="selectedfilters" />
                                                <div class="currentSelection tag_boxes clearfix" data-trigger="selFiltersUpdate">
                                                   <span class="empty">None Selected</span>
                                                </div>	
                                                <div class="padB10 inline"><a class="btn btn-primary execUrl vtip" title="Click to choose and apply your filter." href="?controller=customreport&action=dashboardfilters" id="sel-filters-link" data-size="680">Choose and apply filter</a></div>
                                            </div>
                                        </div>    
                                    </div>	
				</div>						
				</form>				

			</div>
			<div class="row">
                            <div class="col-md-12 graphBorder">
                                <h4>Distribution of awards</h4>
                                <div class="graphBorderInner">
                                    <div id="awardGraph"></div>
                                </div>
                            </div>				
			</div>
			<div class="row">				
                            <div class="col-md-12 graphBorder">
                                <h4>KPA wise performance</h4>
                                <div class="graphBorderInner">
                                    <div id="kpaGraph"></div>
                                </div>
                            </div>
			</div>
			
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	var filters = <?php echo $filters_json?>;
	var i=0;
	console.log(filters);
	 $filterSelect = $('#school_dashboard_frm').find('#create-filter-drop').selectize({
	    sortField: {
	        field: 'value',
	        direction: 'asc'
	    },
	    dropdownParent: 'body'});
	 $(".vertScrollArea").mCustomScrollbar({theme:"dark"});	 
	 var graphData = <?php echo !empty($awardsMatrix)?json_encode($awardsMatrix):'0'?>;
	 var kpaData = <?php echo !empty($kpaMatrix)?json_encode($kpaMatrix):'0';?>;
	 var userKPAData = <?php echo !empty($lastKPAratings)?json_encode($lastKPAratings):'0';?>;
	 var maxScaleY = <?php echo $maxScaleY?>;
	 //console.log(userKPAData);
	 var userData = {"scheme1Award":'<?php echo !empty($lastReviewData['scheme1Award'])?$lastReviewData['scheme1Award']:'' ?>',"scheme2Award":'<?php echo !empty($lastReviewData['scheme2Award'])?$lastReviewData['scheme2Award']:''?>'}; 
	 sessionStorage.setItem("userKPAData", JSON.stringify(userKPAData));	
	 sessionStorage.setItem("scheme1Award", userData.scheme1Award);
	 sessionStorage.setItem("scheme2Award", userData.scheme2Award);
	 if(graphData!==0){
	 	loadGraph(sessionStorage.getItem("scheme1Award"),sessionStorage.getItem("scheme2Award"),graphData);
	 	loadSubGraphs('kpaGraph',kpaData,userKPAData,maxScaleY);
	 }
	 if(!filters.length)
		 $(this).find("#filters-tag").css("display","none");
	 else
		 $(this).find("#filters-tag").css("display","block");
	 if(sessionStorage["selfilters"]!=undefined && sessionStorage["selfilters"].length>0)
		{
		 var cont=$(document).find(".currentSelection");
		 var selfilters = JSON.parse(sessionStorage["selfilters"]);
		 var vals3="";
		 selfilters.length>0? cont.html('<span class="empty notEmpty">None Selected</span>'):'';
		  for(i=0;i<filters.length;i++)		  
			  if($.inArray(+filters[i]['filter_id'],selfilters)>=0){				  
			 		 cont.append('<div title="'+filters[i]['filter_name']+'" class="selFilterNode selFilterNode-'+filters[i]['filter_id']+' clearfix fltr-'+filters[i]['filter_id']+'" data-id="'+filters[i]['filter_id']+'">'+filters[i]['filter_name']+'<input type="hidden" class="ajaxFilterAttach" name="sel-filter['+filters[i]['filter_id']+']" value="'+filters[i]['filter_name']+'"/><span class="delete"><i class="fa fa-times"></i></span></div>').find(".empty").addClass('notEmpty');			 			 
			 		vals3 = vals3+","+filters[i]['filter_id'];
				} 
		  $(document).find('.selectedfilters').first().val(vals3.slice(1));
		  var p=$(document).find(".currentSelection").first();
		  var trgr=p.data('trigger');
			if(trgr!=undefined && trgr!=null && trgr!=""){
				$("body").trigger(trgr);
			}

		}
});	    
</script>  