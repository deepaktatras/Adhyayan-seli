		<div class="filterByAjax" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>" data-querystring="type=<?php echo $type; ?>" data-frm="<?php echo $frm ?>" data-type="<?php echo $type;?>">
			<h1 class="page-title"><?php echo $title;?></h1>		
			<div class="ylwRibbonHldr">
				<div class="tabitemsHldr"></div>
			</div>
			<div class="subTabWorkspace pad26">
				<div class="row">
					<div class="col-sm-9">
						<div class="asmntTypeContainer">
							<?php
							$ajaxFilter=new ajaxFilter();
							$ajaxFilter->addTextbox($name,$filterParam[$name."_like"],"Text");
							//$ajaxFilter->addTextbox($id,$filterParam["client_like"],"School name");
							//$ajaxFilter->addDropDown("network_id",$networks,'network_id','network_name',$filterParam["network_id"],"Network");
							$ajaxFilter->generateFilterBar(1);
							?>
							
							<div class="tableHldr">
								<table class="cmnTable">
									<thead>
										<tr>
											<th data-value="<?php echo $name;?>" class="sort <?php echo $orderBy=="$name"?"sorted_".$orderType:""; ?>">Name</th>
										</tr>
									</thead>
									<tbody>
										<?php 												
									if(count($questions))
										foreach($questions as $key=>$val){
									?>
										<tr class="questionsList <?php echo in_array($val[$id],$currentSelectionIdsArr)?'selected':''; ?>" id="id-question-<?php echo $val[$id]; ?>" data-id="<?php echo $val[$id]; ?>" data-parent-sno="<?php echo $sno; ?>">
											<td><span class="questionName" title="<?php echo $val[$name]; ?>"><?php echo $val[$name]; ?></span></td>																						
										</tr>
										<?php }
									else{
											?>
											<tr>
												<td colspan="">No data found</td>
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
											<td class="currentSelection sno_<?php echo $sno;?>">
											<?php
											//print_r($currentSelection);
											if(count($currentSelection)){
												foreach($currentSelection as $q){
													echo customreportModel::getQuestionsListNode($sno,$q,$type);
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