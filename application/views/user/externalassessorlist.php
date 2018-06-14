		<div class="filterByAjax externalAssessorList" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
			<h1 class="page-title">
				External Reviewers
			</h1>
			
			<div class="ylwRibbonHldr">
				<div class="tabitemsHldr"></div>
			</div>
			<div class="subTabWorkspace pad26">
				<div class="row">
					<div class="col-sm-9">
						<div class="asmntTypeContainer">
							<?php
							$ajaxFilter=new ajaxFilter();
							$ajaxFilter->addTextbox("name",$filterParam["name_like"],"Name");
							$ajaxFilter->addTextbox("client",$filterParam["client_like"],"School name");
							$ajaxFilter->addDropDown("network_id",$networks,'network_id','network_name',$filterParam["network_id"],"Network");
							$ajaxFilter->generateFilterBar(1);
							?>
							
							<div class="tableHldr">
								<table class="cmnTable">
									<thead>
										<tr>
											<th data-value="name" class="sort <?php echo $orderBy=="name"?"sorted_".$orderType:""; ?>">Name</th>
											<th data-value="client_name" class="sort <?php echo $orderBy=="client_name"?"sorted_".$orderType:""; ?>">School Name</th>
											<th data-value="network_name" class="sort <?php echo $orderBy=="network_name"?"sorted_".$orderType:""; ?>">Network Name</th>
										</tr>
									</thead>
									<tbody>
										<?php 
									if(count($users))
										foreach($users as $user){ ?>
										<tr class="eAssessorRow <?php echo in_array($user['user_id'],$currentSelectionIds)?'selected':''; ?>" id="ex-user-<?php echo $user['user_id']; ?>" data-id="<?php echo $user['user_id']; ?>">
											<td><span class="eAssessorName" title="<?php echo $user['email']; ?>"><?php echo $user['name']; ?></span></td>
											<td><?php echo $user['client_name']; ?></td>
											<td><?php echo $user['network_name']; ?></td>
										</tr>
										<?php }
									else{
											?>
											<tr>
												<td colspan="3">No External Reviewer found</td>
											</tr>
											<?php
										}
										?>
									</tbody>
								</table>
							</div>
							<div class="row">
								<div class="col-sm-9 text-left mb0"><?php echo $this->generateAjaxPaging($pages,$cPage); ?></div>
								<div class="col-sm-3 text-right"><button class="btn btn-primary mt20" data-dismiss="modal">Done</button></div>
							</div>
							
						</div>
					</div>
					<div class="col-sm-3">
						<div class="mb10 text-right"><a href="?controller=user&action=createUser" class="btn btn-block btn-primary execUrl vtip" title="Click to add a new user." id="addUserBtn">Add New</a></div>
						<div class="asmntTypeContainer">
							<div class="tableHldr">
								<table class="cmnTable">
									<thead>
										<tr>
											<th>Current Selections</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="currentSelection">
											<?php
											if(count($currentSelection)){
												foreach($currentSelection as $eUser){
													echo userModel::getExternalAssessorNodeHtml($eUser);
												}
											}else{
												echo '<span class="empty">Nothing selected yet</span>';
											}
											?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="ajaxMsg"></div>
			</div>
		</div>