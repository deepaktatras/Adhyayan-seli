<?php
if(in_array(9,$user['role_ids'])){

}
$array_toshow=array (array("role_id" =>1,"role_name" => 'Leader'),array("role_id" =>2,"role_name" => 'Apprentice'),array("role_id" =>4,"role_name" => 'Intern'),array("role_id" =>5,"role_name" => 'Associate'),array("role_id" =>3,"role_name" => 'Attended By'));
//print_r($list_roles_allowed);
//echo $totalCount;
$list_roles_allowed=array_replace($list_roles_allowed,$array_toshow);
?>
<div class="filterByAjax user-list" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
						<h1 class="page-title">	
                                              <?php  if(isset($_REQUEST['role'])){
                                                    //if(current($user['role_ids'])==8){
                                                        ?>
                                                        <a href="<?php
                                                       // controller=user&action=userProfile&id=456&client_id=28
                                                        $args = array("controller" => "user", "action" => "myJourney","id"=>$_REQUEST['uid'],'client_id'=>$_REQUEST['client_id'],'refer'=>'myattendence');
                                                        echo createUrl($args);
                                                        ?>">
                                                            <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
                                                            My Attendance  

                                                        </a>   &rarr; <?php echo $_REQUEST['role'];?>
                                                        <?php
                                                    //}
                                                } else { ?>
                                                <a href="<?php
						$args=array("controller"=>"index","action"=>"index");												
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						
					</a>
								My Workshops
                                                <?php } ?>
							<span class="pull-right">Total: <?php echo $totalCount  ?></span>
								<div class="clr"></div>
                                                                
							</h1>
                                                        
							<div class="asmntTypeContainer">						
								<?php
								$ajaxFilter=new ajaxFilter();
								$ajaxFilter->addTextbox("workshop_name",$filterParam["workshop_like"],"Workshop Name");
							        $ajaxFilter->addTextbox("workshop_location",$filterParam["location_like"],"Address");
								$ajaxFilter->addDateBox("fdate", ChangeFormat($filterParam["fdate_like"],"d-m-Y",""), "From Date");
                                                                $ajaxFilter->addDateBox("edate", ChangeFormat($filterParam["edate_like"],"d-m-Y",""), "End Date");
                                                                $ajaxFilter->addDropDown("role_id",$list_roles_allowed,'role_id','role_name',$filterParam["role_id"],"Activity");
                                                                $ajaxFilter->addDropDown("programme_id",$list_programmes,'programme_id','programme_name',$filterParam["programme_id"],"Programme");
								
								$ajaxFilter->generateFilterBar(1);
								?>
                                                                <script type="text/javascript">
                                                                // function for change the end date according to from date on 28-07-2016 by Mohit Kumar
                                                                $(function() {
                                                                    $('.fdate').datetimepicker({format: 'DD-MM-YYYY', pickTime: false}).off('focus')
                                                                        .click(function () {
                                                                            $(this).data("DateTimePicker").show();
                                                                        });
                                                                    $('.edate').datetimepicker({format: 'DD-MM-YYYY', pickTime: false}).off('focus')
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
                                                                                            
                                                                                            <th data-value="user_role" class="sort <?php echo $orderBy=="user_role"?"sorted_".$orderType:""; ?>">Workshop Activity</th>
                                                                                         
                                                                                                
											</tr>
										</thead>
										<tbody>
											<?php 
                                                                                if(count($workshops))
											foreach($workshops as $workshopDetail){ ?>
                                                                                        <tr>
                                                                                            
                                                                                            <td><?php echo $workshopDetail['workshop_name'];?></td>
                                                                                            <td><?php echo ChangeFormat($workshopDetail['workshop_date_from']); ?> to <?php echo ChangeFormat($workshopDetail['workshop_date_to']); ?></td>
                                                                                            <td><?php echo $workshopDetail['workshop_location']; ?></td>
                                                                                             <td><?php echo $workshopDetail['programme_name']; ?></td>
                                                                                            <td><?php echo $workshopDetail['workshop_sub_role_name'];?></td>
                                                                                                                                                                       
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
<script>
//$( ".fieldsArea" ).append( "<span style='padding-left:20px;'> Total : <?php echo $totalCount  ?></span>" );
</script>