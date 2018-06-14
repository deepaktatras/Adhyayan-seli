<?php
if(in_array(9,$user['role_ids'])){

}
$array_toshow=array (array("sub_role_id" =>5,"sub_role_name" => 'LED By'),array("sub_role_id" =>6,"sub_role_name" => 'Co-Facilitator By'),array("sub_role_id" =>7,"sub_role_name" => 'Attended By'));
//print_r($list_roles_allowed);
//echo $totalCount;
$list_roles_allowed=array_replace($list_roles_allowed,$array_toshow);
?>
<div class="filterByAjax workshop-list" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
							<h1 class="page-title"><a href="<?php
						$args=array("controller"=>"index","action"=>"index");												
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						
					</a>
								Workshops
							<!--<span class="pull-right"><a id="addUserBtn" class="btn btn-primary pull-right execUrl vtip fixonmodal" data-size="800" title="Click to add workshop details." href="?controller=workshop&action=createWorkshop&ispop=1">Add workshop</a>
</span>--><span class="pull-right"><a id="addUserBtn" class="btn btn-primary pull-right vtip fixonmodal" data-size="800" title="Click to add workshop details." href="?controller=workshop&action=createWorkshop">Add workshop</a>
</span>
								<div class="clr"></div>
                                                                
							</h1>
                                                        
							<div class="asmntTypeContainer">						
								<?php
								$ajaxFilter=new ajaxFilter();
								$ajaxFilter->addTextbox("workshop_name",$filterParam["workshop_like"],"Workshop Name");
							        $ajaxFilter->addTextbox("workshop_location",$filterParam["location_like"],"Address");
								$ajaxFilter->addDateBox("fdate", ChangeFormat($filterParam["fdate_like"],"d-m-Y",""), "Start Date");
                                                                $ajaxFilter->addDateBox("edate", ChangeFormat($filterParam["edate_like"],"d-m-Y",""), "End Date");
                                                                //$ajaxFilter->addDropDown("sub_role_id",$list_roles_allowed,'sub_role_id','sub_role_name',$filterParam["sub_role_id"],"Activity");
                                                                //$ajaxFilter->addTextbox("workshop_led",$filterParam["workshop_led_like"],"LED By");
                                                                if(!in_array(8,$user['role_ids'])){
                                                                $ajaxFilter->addDropDown("programme_id",$list_programmes,'programme_id','programme_name',$filterParam["programme_id"],"Programme");
                                                                }
								$ajaxFilter->generateFilterBar(1);
								?>
                                                                <script type="text/javascript">
                                                                // function for change the end date according to from date on 28-07-2016 by Mohit Kumar
                                                                $(function() {
                                                                    $('.fdate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false}).off('focus')
                                                                        .click(function () {
                                                                            $(this).data("DateTimePicker").show();
                                                                        });
                                                                    $('.edate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false}).off('focus')
                                                                        .click(function () {
                                                                            $(this).data("DateTimePicker").show();
                                                                        });
                                                                    $(".fdate").on("dp.change", function (e) {
                                                                        $('.edate').data("DateTimePicker").setMinDate(e.date);
                                                                        $('.edate').val('');
                                                                    });
                                                                    $(".edate").on("dp.change", function (e) {
                                                                        $('.fdate').data("DateTimePicker").setMaxDate(e.date);
                                                                    });
                                                                    
                                                                    $(".fdate").on("blur", function (e) {
                                                                        //alert($(this).val());
                                                                        $('.edate').data("DateTimePicker").setMinDate($(this).val());
                                                                        //$('.edate').val('');
                                                                    });
                                                                    $(".edate").on("click", function (e) {
                                                                    $(this).val("");
                                                                    });
                                                                    $(".edate").on("blur", function (e) {
                                                                        //alert($(this).val())
                                                                        if($(this).val()!=""){
                                                                        $('.fdate').data("DateTimePicker").setMaxDate($(this).val());
                                                                    }
                                                                    });
                                                                    
                                                                    
                                                                });
                                                                </script>       
								<form name="frm" action="" method="post">
								<div class="tableHldr">
                                                                    
									<table class="cmnTable">
										<thead>
											<tr>
                                                                                            
                                                                                            <th data-value="workshop_name" class="sort <?php echo $orderBy=="workshop_name"?"sorted_".$orderType:""; ?>">Workshop name</th>
                                                                                            <th data-value="workshop_date_from" class="sort <?php echo $orderBy=="workshop_date_from"?"sorted_".$orderType:""; ?>">Conducted on</th>
                                                                                            <th data-value="workshop_location" class="sort <?php echo $orderBy=="workshop_location"?"sorted_".$orderType:""; ?>">Address</th>
                                                                                            <th data-value="programme_name" class="sort <?php echo $orderBy=="programme_name"?"sorted_".$orderType:""; ?>">Programme</th>
                                                                                            
                                                                                            <th data-value="workshop_led" class="sort <?php echo $orderBy=="workshop_led"?"sorted_".$orderType:""; ?>">Workshop Leader</th>
                                                                                            <th data-value="workshop_cofaciliated" class="sort <?php echo $orderBy=="workshop_cofaciliated"?"sorted_".$orderType:""; ?>">Co-facilitated By</th>
                                                                                            <th data-value="workshop_attende" class="sort <?php echo $orderBy=="workshop_attende"?"sorted_".$orderType:""; ?>">No. of Expected Attendees</th>
                                                                                            <th data-value="workshop_actual" class="sort <?php echo $orderBy=="workshop_actual"?"sorted_".$orderType:""; ?>">No. of Actual Attendees</th>
                                                                                            
                                                                                            <th>Edit/View</th>
                                                                                            <th>Download</th>
											</tr>
										</thead>
										<tbody>
											<?php 
                                                                                if(count($workshops))
											foreach($workshops as $workshopDetail){ ?>
                                                                                        <tr>
                                                                                            
                                                                                            <td><?php echo $workshopDetail['workshop_name'];?></td>
                                                                                            <td><?php echo ChangeFormat($workshopDetail['workshop_date_from']); ?> <br>to <br><?php echo ChangeFormat($workshopDetail['workshop_date_to']); ?></td>
                                                                                            <td><?php echo $workshopDetail['workshop_location']; ?></td>
                                                                                            <td><?php echo $workshopDetail['programme_name']; ?></td>
                                                                                            <td><?php echo $workshopDetail['lead_by']; ?></td>
                                                                                            <td><?php echo $workshopDetail['co_faciliated_by']; ?></td>
                                                                                            <td><?php echo $tot_attende=empty($workshopDetail['tot_attende'])?'-':$workshopDetail['tot_attende']; ?></td>
                                                                                            <td><?php echo $tot_attended_actual=empty($workshopDetail['tot_attended_actual'])?'-':$workshopDetail['tot_attended_actual']; ?></td>
                                                                                           
                                                                                            <td><a href="?ispop=0&amp;controller=workshop&amp;action=editworkshop&amp;wid=<?php echo $workshopDetail['workshop_id']  ?>"  data-size="800"><i title="Edit" class="vtip glyphicon glyphicon-pencil"></i></a></td>
                                                                                            <td><?php echo $show=empty($workshopDetail['tot_attende'])?'-':'<a href="?controller=workshop&amp;action=downloadAttendees&amp;wid='.$workshopDetail['workshop_id'].'"><i title="Download Attendance List" class="vtip glyphicon glyphicon-download-alt"></i></a>'; ?></td>
                                                                                        </tr>
											<?php }
										else{
												?>
												<tr>
													<td colspan="5">No Workshop found</td>
												</tr>
												<?php
											}
											?>
										</tbody>
									</table>
                                                                    
								</div>
                                                                </form>
								<?php echo $this->generateAjaxPaging($pages,$cPage); ?>
								
								<div class="ajaxMsg"></div>
							</div>
						</div>