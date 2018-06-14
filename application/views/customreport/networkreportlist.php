<div class="filterByAjax networkreportlist" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
    <div class="clearfix hdrTitle"><div class="pull-left"><h1 class="page-title">MyOverview Reports</h1></div>					
						<!--<a href="?controller=customreport&action=network" data-size="550" class="btn btn-primary pull-right" id="addschoolselfRevAssBtn" >Create Overview Report</a>--> 											
						 <div class="pull-right">
                                                     
            <ul class="mainNav">
                <li class="active"><a href="javascript:void(0);">Create Overview Report <i class="fa fa-sort-desc"></i></a>
                    <ul>
      <li><a href="?controller=customreport&action=network" id="addschoolselfRevAssBtn">School Overview Report</a> 											
						</li>
      <li><a href="?controller=customreport&action=studentnetwork"   id="addschoolselfRevAssBtn" >Student Overview Report</a> 											
						</li>
      
    </ul>
                </li>
            </ul>                                                 
                                                     
    <!--<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Create Overview Report
    <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="?controller=customreport&action=network" id="addschoolselfRevAssBtn" >School Overview Report</a> 											
						</li>
      <li><a href="?controller=customreport&action=studentnetwork"   id="addschoolselfRevAssBtn" >Student Overview Report</a> 											
						</li>
      
    </ul>-->
  </div>
                                        <div class="clr"></div>
					</div>
					<div class="nwListContainer">
					 <?php
                                         //print_r($aqsRounds);
						$ajaxFilter=new ajaxFilter();
                                                $ajaxFilter->addDropDown("assessment_type_id",$report_type,'assessment_type_id','assessment_type_name',$filterParam["assessment_type_id"],"Report Type");
						$ajaxFilter->addTextbox("report_name",$filterParam["report_name_like"],"Report Name");																																
                                                $ajaxFilter->addDropDown_etc("report_id",$reportType,'report_id','report_name',$filterParam["report_id"],"Student Report Type",'',$etc);
						$ajaxFilter->addDropDown_etc("network_id",$networks,'network_id','network_name',$filterParam["network_id"],"Network/ Organisation",'',$etc);
						$ajaxFilter->addDropDown_etc("province_id",$provinces,'province_id','province_name',$filterParam["province_id"],"Province/ Centre",'',''.$etc.' '.$etc1.'');
                                                $ajaxFilter->addDropDown_etc("client_id",$clients,'client_id','client_name',$filterParam["client_id"],"School/ Batch",'',''.$etc.' '.$etc2.'');
                                                $ajaxFilter->addDropDown_etc("round_id",$aqsRounds,'aqs_round','aqs_round',$filterParam["round_id"],"Round",'',$etc);
                                                $ajaxFilter->generateFilterBar(1);

						?>
					 <div class="tableHldr">
						 <table class="cmnTable">
							 <thead>
								 <tr><th data-value="report_name" class="sort <?php echo $orderBy=="report_name"?"sorted_".$orderType:""; ?>">Name</th>
                                                                     <th data-value="report_type" class="sort <?php echo $orderBy=="report_type"?"sorted_".$orderType:""; ?>">Overview Report type</th>
                                                                     <th data-value="create_date" class="sort <?php echo $orderBy=="create_date"?"sorted_".$orderType:""; ?>">Creation Date</th><th>Overview Report</th><th>Data Summary</th></tr>
							 </thead>
							 <tbody>
								 <?php 
							if(count($networkReportList)){
								 foreach($networkReportList as $nwrow){
								 	echo "<tr data-id='".$nwrow['network_report_id']."'><td>".$nwrow['report_name']."</td>
                                                                            <td>".$nwrow['assessment_type_name']."</td>
                                                                         <td>".ChangeFormat($nwrow['create_date'])."</td>";
                                                                                        if($nwrow['assessment_type_id']==1){ 
											echo"<td><a href='?controller=customreport&amp;action=generatenetworkreportpdf&amp;network_report_id=".$nwrow['network_report_id']."' target='_blank'><i title='Click to view report.' class='vtip glyphicon glyphicon-eye-open'></i></a> <a href='?controller=customreport&amp;action=editnetworkreport&amp;network_report_id=".$nwrow['network_report_id']."' target='_blank'><i title='Click to edit report' class='vtip glyphicon glyphicon-pencil'></i></a>
											</td>";
                                                                                        }else{
                                                                                        if($nwrow['report_id']==8)    
                                                                                        echo"<td><a href='?controller=report&action=student&report_id=".$nwrow['report_id']."&group_assessment_id=".$nwrow['group_assessment_id']."&diagnostic_id=".$nwrow['diagnostic_id']."'  target='_blank'><i title='Click to view report.' class='vtip glyphicon glyphicon-eye-open'></i></a></td>";    
                                                                                        
                                                                                        if($nwrow['report_id']==11)    
                                                                                        echo"<td><a href='?controller=report&action=studentCentre&report_id=".$nwrow['report_id']."&centre_id=".$nwrow['province_id']."&batch_id=".$nwrow['client_id']."&round_id=".$nwrow['round_id']."'  target='_blank'><i title='Click to view report.' class='vtip glyphicon glyphicon-eye-open'></i></a></td>";    
                                                                                        
                                                                                        if($nwrow['report_id']==12)    
                                                                                        echo"<td><a href='?controller=report&action=studentOrg&report_id=".$nwrow['report_id']."&org_id=".$nwrow['network_id']."&centre_id=".$nwrow['province_id']."&batch_id=".$nwrow['client_id']."&round_id=".$nwrow['round_id']."'  target='_blank'><i title='Click to view report.' class='vtip glyphicon glyphicon-eye-open'></i></a></td>";    
                           
                                                                                        }
                                                                                        
                                                                                        if($nwrow['assessment_type_id']==1){
											/*echo "<td>
											<a href='?controller=customreport&amp;action=generatedatasummary&amp;network_report_id=".$nwrow['network_report_id']."' target='_blank'><i title='Click to view data summary report.' class='vtip glyphicon glyphicon-eye-open'></i></a>
											</td>";*/
                                                                                         echo "<td>
											<a href='?controller=customreport&amp;action=generatedcustomdatasummary&amp;network_report_id=".$nwrow['network_report_id']."'  class='execUrl'><i title='Click to view data summary report.' class='vtip glyphicon glyphicon-eye-open'></i></a>
											</td>";
                                                                                        }else{
                                                                                        echo"<td>-</td>";    
                                                                                        }
                                                                                        
										echo"</tr>";
                                                                 }									
							}else{
								echo "<tr><td colspan='3'>No results found</td></tr>";
							}
								 ?>
							 </tbody>
						 </table>
					 </div>
					 
					 
					  <?php  echo $this->generateAjaxPaging($pages,$cPage); ?>
								
						<div class="ajaxMsg"></div>
					  </div>
				</div>