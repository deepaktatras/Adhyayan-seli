<?php

 ?>
 <div class="filterByAjax filter-list-sort" data-action="dashboardfilters" data-controller="customreport">
 <h4 class="page-title">Choose Filters</h4>     
      <!--<div class="modal-body pad0">-->
          <div class="ylwRibbonHldr">
             <div class="tabitemsHldr"><a href="?controller=customreport&amp;action=createfilter&amp;isDashboard=1" class="btn btn-primary pull-right execUrl vtip fixonmodal" title="Click to add a new filter" style="top:3px;right:24px;">Add New</a></div>
          </div>
          <div class="subTabWorkspace pad26 sortable-form" role="document" >
              <form id="fltrselectform" enctype="multipart/form-data" method="post" action="#">              
                <div class="queryBoxWrapper">                   				
                    <div class="row">
                        <div class="col-md-6"> 
                            <div class="leftQuestHldr">
								<h3>All filters</h3>
								<div class="vertScrollArea" style="height:200px;">
									<ul id="f-sortableL" class="connectedSortable" >
									<?php		
									//print_r($filters);
									$i=0;
									foreach($filters as $key=>$val): 										
									//$val = array_values($val);
									//if(isset($val[2]) && ($val[2]==$diagnosticId))
										//continue;		
									$i++;	
									echo '<li class="vtip" data-id="'.$val['filter_id'].'" title="'.$val['filter_name'].'"><span class="vtip" title="'.$val['filter_name'].'">'.$val['filter_name'].'</span><a href="?controller=customreport&action=editfilter&isDashboard=1&fid='.$val['filter_id'].'" class="execUrl edit" data-size="800" class="edit"><i class="fa fa-pencil vtip" title="Edit Filter"></i></a></li>';
									endforeach;
									?>                                                                 
									</ul>
								</div>																
							</div>
                        </div>
                        <div class="col-md-1">
                            <div class="sortInfoIcon text-center"><i class="fa fa-arrows-h"></i></div>
                        </div>
                        <div class="col-md-5">
							
                            <div class="rightConfirmedQueryBox ">
                                <h3>Selected filters</h3>
                              <div class="vertScrollArea" style="height:200px;">	
                                <ul id="f-sortableR" class="connectedSortable">
                                 <?php										
								foreach($filters as $key=>$val): 	
									//$val = array_values($val);
									echo '<li class="vtip" data-id="'.$val['filter_id'].'" title="'.$val['filter_name'].'"><span class="vtip" title="'.$val['filter_name'].'">'.$val['filter_name'].'</span><a href="?controller=customreport&action=editfilter&isDashboard=1&fid='.$val['filter_id'].'" class="execUrl edit" data-size="800" class="edit"><i class="fa fa-pencil vtip" title="Edit Filter"></i></a></li>';
								endforeach;
								?> 
                                </ul>
                               </div> 
                            </div>
                            <div class="text-right mb10">                               
                                <button type="button" class="btn btn-primary vtip" id="btn-fltr-apply" title="Click to apply the selected filters.">Apply</button>							   	
						   </div>
							<div class="ajaxMsg"></div>
                        </div>
                    </div>                                            
				
			   </div>
            </form>
          </div>
 </div>         
     <!-- </div>-->
  <!-- Diagnostic addition script -->        
    <script>	
    $(function() {			
		$(".vertScrollArea").mCustomScrollbar({theme:"dark"});		
      $( "#f-sortableL, #f-sortableR" ).sortable({
        connectWith: ".connectedSortable"
      }).disableSelection();	
     // console.log(sessionStorage.getItem("selfilters"));
      //$("#f-sortableR").
     var fltrs = JSON.parse( sessionStorage.getItem("selfilters"));     
    	 $( "#f-sortableL").find("li").each(function(i,v){
			console.log($(v).data('id'))
			if($.inArray($(v).data('id'),fltrs)>=0)
				$(this).remove();
         });
    	 $( "#f-sortableR").find("li").each(function(i,v){
 			console.log($(v).data('id'))
 			if($.inArray($(v).data('id'),fltrs)<0)
 				$(this).remove();
          });  
    	 var boxHeight=0;
    	 var frm="#fltrselectform";
		 boxHeight = $(frm).find('.leftQuestHldr .vertScrollArea').height();
			$(frm).find('.rightConfirmedQueryBox .vertScrollArea').height(boxHeight);
			$(frm).find('.leftQuestHldr .vertScrollArea').height(boxHeight);
			$(frm).find('.connectedSortable.ui-sortable').css('min-height',boxHeight+'px');
			$(frm).find('.sortInfoIcon').height($(frm).find('.leftQuestHldr').height());	
	      
    });
    </script>
