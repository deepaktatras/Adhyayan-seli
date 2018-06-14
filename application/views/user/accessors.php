<?php
//if(in_array(8,$user['role_ids'])){
//    echo '<pre>';
//    print_r($mailUser);
//    echo '</pre>';
    if(!empty($assessorsCount)){
?>
<!--    <script type="text/javascript" src="<?php echo SITEURL?>public/js/d3.v2.js"></script>-->
    <script type="text/javascript">
        var dataSet = [
                {legendLabel: "Lead Assessor", magnitude: <?php echo !empty($assessorsCount['Lead_Assessor'])?$assessorsCount['Lead_Assessor']:0?>, link: "?controller=user&action=accessors"},
                {legendLabel: "Sr Associate Assessor", magnitude: <?php echo !empty($assessorsCount['Sr_Associate_Assessor'])?$assessorsCount['Sr_Associate_Assessor']:0?>, link: "?controller=user&action=accessors"},
                {legendLabel: "Apprentice Assessor", magnitude: <?php echo !empty($assessorsCount['Apprentice_Assessor'])?$assessorsCount['Apprentice_Assessor']:0?>, link: "?controller=user&action=accessors"},
                {legendLabel: "Associate Assessor", magnitude: <?php echo !empty($assessorsCount['Associate_Assessor'])?$assessorsCount['Associate_Assessor']:0?>, link: "?controller=user&action=accessors"},
                {legendLabel: "Intern Assessor", magnitude: <?php echo !empty($assessorsCount['Intern_Assessor'])?$assessorsCount['Intern_Assessor']:0?>, link: "?controller=user&action=accessors"}];
    </script>
    <style>
        svg {
            font: 10px sans-serif;
            display: block;
        }
    </style>
<?php
    }
//}
?>
<div class="filterByAjax user-list" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
							<h1 class="page-title">
								<?php
                                                                    echo 'Assessors';
                                                                
                                                                ?>
                                                            
                                                            <div class="clr"></div>
							</h1>
                                                        <?php  
                                                        if(!empty($assessorsCount)){
                                                            ?>
                                                            <!--<div style="width: 500px;height: 350px;margin: 0 0 10px 100px;" >-->
                                    <!--                             <div id="chart_div" style="width:500px; height:300px;"></div>-->
                                                                <div class="div_RootBody" id="pie_chart_1" style="width: 500px;min-height: 280px;margin: 0 0 10px 100px;">
                                                                    <h3 class="h3_Body">Assessors Activity Status</h3>
                                                                    <div class="chart"></div>
                                                                </div>
                                                            <!--</div>-->
                                                            <?php
                                                        }?>
							<div class="asmntTypeContainer">
                                                            <!--<a href="?controller=user&action=createUser&ispop=1" class="btn btn-primary pull-right vtip floatedFltrBtn" title="Click to add a new user." id="addUserBtn"><i class="fa fa-plus"></i>Add New</a>-->
								<?php
								$ajaxFilter=new ajaxFilter();
								$ajaxFilter->addTextbox("name",$filterParam["name_like"],"Name");
								$ajaxFilter->addTextbox("email",$filterParam["email_like"],"Email");
								if(in_array("manage_all_users",$user['capabilities']) || in_array("manage_own_network_users",$user['capabilities']))
									$ajaxFilter->addTextbox("client",$filterParam["client_like"],"School name");
								if(in_array("manage_all_users",$user['capabilities'])){
                                                                    
                                                                
                                                                        $array = array(
                                                                            array(
                                                                                'id' => 'd_AQS_team',
                                                                                'value' => 'Not Email'
                                                                            ),
                                                                            array(
                                                                                'id' => 'd_user',
                                                                                'value' => 'Email'
                                                                            )
                                                                        );
//                                                                        $filterParam["table_name"]='';
//                                                                        $filterParam["sub_role"]='';
                                                                        $ajaxFilter->addDropDown("table_name",$array,'id','value',$filterParam["table_name"],"User List");
//                                                                        $ajaxFilter->addDropDown("sub_role",$userSubRoleList,'sub_role_id','sub_role_name',$filterParam["sub_role"],"User Sub Role");
                                                                    }                                                                    
                                                                if(isset($_REQUEST['ref']) && $_REQUEST['ref']!=''){
                                                                    $ajaxFilter->addHidden("ref",$_REQUEST['ref']);
                                                                }
								$ajaxFilter->generateFilterBar(1);
								?>
								<form name="frm" action="" method="post">
								<div class="tableHldr">
                                                                    
									<table class="cmnTable">
										<thead>
											<tr>
                                                                                                <th>
                                                                                                    <input type="checkbox" name="tickall" id="tickall" onclick="checkall(this, delid);" />
                                                                                                </th>
                                                                                                
                                                                                            <th data-value="name" class="sort <?php echo $orderBy=="name"?"sorted_".$orderType:""; ?>">Name</th>
                                                                                            <th data-value="client_name" class="sort <?php echo $orderBy=="client_name"?"sorted_".$orderType:""; ?>">School Name</th>
                                                                                            <th data-value="email" class="sort <?php echo $orderBy=="email"?"sorted_".$orderType:""; ?>">Email</th>
                                                                                            <th data-value="user_role" class="sort <?php echo $orderBy=="user_role"?"sorted_".$orderType:""; ?>">User Role</th>
                                                                                            <th>Action</th>  
                                                                                                
											</tr>
										</thead>
										<tbody>
											<?php 
                                                                                if(count($users))
											foreach($users as $userDetail){ ?>
                                                                                        <tr>
                                                                                            
                                                                                            <td>
                                                                                                <input type="checkbox"  id="delid"  name="deleteMe[]" onclick="javascript:uncheck();" value="<?php echo $userDetail['user_id'] ?>"/>
                                                                                            </td>
                                                                                            <td class="tdUserClass">
                                                                                                <?php 
                                                                                                if($userDetail['table_name']=='d_user'){
                                                                                                    $client_id = "&client_id=".$userDetail['client_id'];
                                                                                                    ?>
                                                                                                    <a href="<?php echo "?controller=user&action=userProfile&id=".$userDetail['user_id'].$client_id; ?>"
                                                                                                       style="background-color: transparent;color: blue;text-decoration: underline;padding: 0px;" title="Click here to view profile">
                                                                                                        <?php echo $userDetail['name']; ?>
                                                                                                    </a>    
                                                                                                    <?php
                                                                                                } else {
                                                                                                    echo $userDetail['name'];
                                                                                                }
                                                                                                
                                                                                                ?>
                                                                                                
                                                                                                                                                                                               
                                                                                            </td>
                                                                                            <td class="tdUserClass"><?php echo $userDetail['client_name']; ?></td>
                                                                                            <td class="tdUserClass"><?php echo $userDetail['email']; ?></td>
                                                                                            <td class="tdUserClass"><?php echo rtrim(ucfirst(strtolower(str_replace(",","<br>",$userDetail['roles']))),'s'); ?></td>
                                                                                            <td>
                                                                                            <?php
                                                                                           // if(current($user['role_ids'])==8){
//                                                                                                if(!empty($mailUser) && in_array($userDetail['email'], $mailUser)){
//                                                                                                    echo 'Mail has been sent!';
//                                                                                                } else {
                                                                                                    if($userDetail['table_name']=='d_user'){
                                                                                                        if(!empty($tapAssessorUser) && in_array($userDetail['user_id'], $tapAssessorUser)){
                                                                                                            $message = "Mail has been sent!";
                                                                                                            $label = '<i class="fa fa-envelope" title="Resend Invite" style="font-size:18px;color:#600109;"></i>';
                                                                                                            $style = ' style="background-color: unset;"';
                                                                                                        } else {
                                                                                                            $message = "";
                                                                                                            $label = "Send & Invite";
                                                                                                            $style = '';
                                                                                                        }
                                                                                                        echo $message;
                                                                                                        ?>
                                                                                                        &nbsp;
                                                                                                        <a href="?controller=user&action=message&table=<?php echo $userDetail['table_name']."&id=".$userDetail['user_id']; ?>" 
                                                                                                           class="execUrl" title="Resend Invite" <?php echo $style?>>
                                                                                                            <?php echo $label?>
                                                                                                        </a>
                                                                                                        <?php
                                                                                                    } else {
                                                                                                        if(!empty($mailUser) && in_array($userDetail['user_id'], $mailUser)){
                                                                                                            $message = "Mail has been sent!";
                                                                                                            $label = '<i class="fa fa-envelope" title="Resend Approval" style="font-size:18px;color:#600109;"></i>';
                                                                                                            $style = ' style="background-color: unset;"';
                                                                                                        } else {
                                                                                                            $message = "";
                                                                                                            $label = "Approval Required";
                                                                                                            $style = '';
                                                                                                        }
                                                                                                        echo $message;
                                                                                                        ?>
                                                                                                        &nbsp;
                                                                                                        <a href="<?php echo "?controller=user&action=sendSignUpEmail&table=".$userDetail['table_name']."&id=".$userDetail['user_id']; ?>" 
                                                                                                           class="execUrl" title="Resend Approval" <?php echo $style?>>
                                                                                                            <?php echo $label?>
                                                                                                        </a>
                                                                                                        <?php
                                                                                                    }
//                                                                                                }
                                                                                                
                                                                                                
                                                                                            
                                                                                            ?>
                                                                                            </td>                                                                                            
											</tr>
											<?php }
										else{
												?>
												<tr>
													<td colspan="6">No user found</td>
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
   
        <script type="text/javascript">
            drawPie("Pie1", dataSet, "#pie_chart_1 .chart", "colorScale20", 10, 100, 5, 0);
        </script>
    