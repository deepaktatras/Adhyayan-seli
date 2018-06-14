						<div class="filterByAjax network-list" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
							<h1 class="page-title">
								Networks
								<a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary pull-right vtip execUrl" title="Click to add province or network." id="addProvinceBtn">Add New</a>
								<!--  <a href="?controller=network&action=createNetwork&amp;ispop=1" class="btn btn-primary pull-right vtip execUrl" title="Click to add network." id="addNetworkBtn">Add New Network</a>-->
								<div class="clr"></div>
							</h1>
							<div class="asmntTypeContainer">			
								<?php
								$ajaxFilter=new ajaxFilter();
								$ajaxFilter->addTextbox("name",$filterParam["name_like"],"Network Name");
								$ajaxFilter->addTextbox("province",$filterParam["province_like"],"Province Name");
								$ajaxFilter->generateFilterBar(1);
								?>
								
								<div class="tableHldr">
									<table class="cmnTable">
										<thead>
											<tr>
												<th data-value="name" class="sort <?php echo $orderBy=="name"?"sorted_".$orderType:""; ?>">Network Name</th>
												<th data-value="province" class="sort <?php echo $orderBy=="province"?"sorted_".$orderType:""; ?>">Province Name</th>
												<th data-value="noOfClients" class="sort <?php echo $orderBy=="noOfClients"?"sorted_".$orderType:""; ?>">Schools in network</th>
												<th data-value="clientInProvince" class="sort <?php echo $orderBy=="clientInProvince"?"sorted_".$orderType:""; ?>">Schools in province</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											if(count($networks))
												foreach($networks as $network){ ?>
											<tr <?php echo !empty($network['province_name'])?'class="provinceRow"':''; ?>>
												<td><?php echo $network['network_name']; ?></td>
												<td><?php echo $network['province_name']; ?></td>
												<td id="clientCountFor-<?php echo $network['network_id']; ?>"><?php echo $network['noOfClients']; ?></td>
												<td id="clientInProvinceCountFor-<?php echo $network['province_id']; ?>"><?php echo $network['clientInProvince']; ?></td>
												<td><?php if(!($network['province_id']>0)){?><a href="?controller=network&action=editNetwork&id=<?php echo $network['network_id']; ?>&amp;ispop=1" class="execUrl"><i class="vtip glyphicon glyphicon-pencil" title="Update Network"></i></a><?php } ?>
												<?php if($network['province_id']>0 && $network['noOfClients']>0){?><a href="?controller=network&action=editNetworkProvince&pid=<?php echo $network['province_id']; ?>&amp;ispop=1" class="execUrl"><i class="vtip glyphicon glyphicon-pencil" title="Update Province"></i></a><?php } ?>
												</td>
											</tr>
											<?php }
											else{
												?>
												<tr>
													<td colspan="6">No network found</td>
												</tr>
												<?php
											} ?>
										</tbody>
									</table>
								</div>
								
								<?php echo $this->generateAjaxPaging($pages,$cPage); ?>
								
								<div class="ajaxMsg"></div>
							</div>
						</div>