<?php

	$isReadOnly=empty($isReadOnly)?0:1;
	$readOnlyText=$isReadOnly?'readonly="readonly"':"";
	$disabledText=$isReadOnly?'disabled="disabled"':"";
        $tr_country_code = '';
$cell_number = '';
if (isset($tchrInfo['mobile']['value'])) {
    $number = explode(")", $tchrInfo['mobile']['value']);
    if (isset($number[0]) && count($number) > 1) {
        $tr_country_code = explode("+", $number[0]);
        $cell_number = trim($number[1]);
    } else if (count($number) == 1) {
        $cell_number = trim($number[0]);
    } else if (isset($number[1])) {
        $cell_number = trim($number[1]);
    }
    //print_r($cell_country_code);
}
?>

	<h1 class="page-title">
		<a href="?controller=assessment&action=assessment">
			<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
		</a>
		Teacher information
		<big>&#8594;</big>
		<?php echo $groupAssmt['user_names'][0]; ?>
	</h1>
	<form method="post" id="teacherInfoForm" onsubmit="return false;">
		<div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
                        <div class="tab-pane-mand"><div class="wrapNote"><span>Fields marked with * are mandatory.</span></div></div>
		</div>
		<div class="subTabWorkspace wideLabel pt10">
			<div class="boxBody">
				<dl class="fldList">
					<dt>Your name<span class="astric">*</span>:</dt>
					<dd>
						<div class="row">
							<div class="col-sm-6"><input required type="text" class="form-control required" <?php echo $readOnlyText; ?> value="<?php echo empty($tchrInfo['name']['value'])?$groupAssmt['user_names'][0]:$tchrInfo['name']['value']; ?>" name="tchrInfo[name]" autocomplete="off"></div>
						</div>
					</dd>
				</dl>
			</div>
			
			<div class="boxBody">
				<dl class="fldList">
					<dt>Your designation<span class="astric">*</span>:</dt>
					<dd>
						<div class="row">
							<div class="col-sm-6"><input required type="text" class="form-control required" <?php echo $readOnlyText; ?> value="<?php echo empty($tchrInfo['designation']['value'])?'':$tchrInfo['designation']['value']; ?>" name="tchrInfo[designation]" autocomplete="off"></div>
						</div>
					</dd>
				</dl>					
			</div>
			
			<div class="boxBody">
				<dl class="fldList">
					<dt>Your mobile no.<span class="astric">*</span>:</dt>
					<dd>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="inlContBox ftySixty">
                                <div class="inlCBItm fty">
                                    <div class="fld blk">
                                        <div>
                                            <select name="tr_country_code" id="tr_country_code" class="form-control" >
                                                <?php
                                                foreach ($countryCodeList as $value) {
                                                    ?>
                                                    <option value="<?php echo $value['phonecode'] ?>"
                                                            <?php echo!empty($tr_country_code[1]) && $tr_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
                                                        <?php echo "(+" . $value['phonecode'] . ") " ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="inlCBItm sixty">
                                    <div class="fld">
                                        <div>
                                            <input required type="text" class="form-control required aqs_ph" <?php echo $readOnlyText; ?> value="<?php echo empty($tchrInfo['mobile']['value']) ? '' : $cell_number; ?>" name="tchrInfo[mobile]" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </dd>

				</dl>					
			</div>
			
			<div class="boxBody">
				<dl class="fldList">
					<dt>Your educational qualification<span class="astric">*</span>:</dt>
					<dd>
						<div class="row">
							<div class="col-sm-6"><input required type="text" class="form-control required" <?php echo $readOnlyText; ?> value="<?php echo empty($tchrInfo['qualification']['value'])?'':$tchrInfo['qualification']['value']; ?>" name="tchrInfo[qualification]" autocomplete="off"></div>
						</div>
					</dd>
				</dl>					
			</div>
			
			<div class="boxBody">
				<dl class="fldList">
					<dt>Total years of teaching experience in the current school<span class="astric">*</span>:</dt>
					<dd>
						<div class="row">
							<div class="col-sm-6">
								<select required autocomplete="off" <?php echo $disabledText; ?> name="tchrInfo[experience]" class="selectpicker required show-tick form-control" >
									<option value=''> - Select total number of years - </option>
								<?php
								$exp=isset($tchrInfo['experience']['value'])?$tchrInfo['experience']['value']:'-1';
								for($i=0;$i<71;$i++){
									?>
									<option <?php echo $i==$exp?'selected="selected"':''; ?> value="<?php echo $i; ?>"><?php echo $i==0?'Less than 1':$i; ?></option>
									<?php
								}
								?>
								</select>
							</div>
						</div>
					</dd>
				</dl>					
			</div>
                    
                    
                       <div class="boxBody">
				<dl class="fldList">
					<dt>Total years of teaching experience other than current school<span class="astric">*</span>:</dt>
					<dd>
						<div class="row">
							<div class="col-sm-6">
								<select required autocomplete="off" <?php echo $disabledText; ?> name="tchrInfo[other_experience]" class="selectpicker required show-tick form-control" >
									<option value=''> - Select total number of years - </option>
								<?php
								$exp=isset($tchrInfo['other_experience']['value'])?$tchrInfo['other_experience']['value']:'-1';
								for($i=0;$i<71;$i++){
									?>
									<option <?php echo $i==$exp?'selected="selected"':''; ?> value="<?php echo $i; ?>"><?php echo $i==0?'Less than 1':$i; ?></option>
									<?php
								}
								?>
								</select>
							</div>
						</div>
					</dd>
				</dl>					
			</div>
			
			<div class="boxBody">
				<dl class="fldList">
					<dt>School joining year<span class="astric">*</span>:</dt>
					<dd>
						<div class="row">
							<div class="col-sm-6">
								<select required autocomplete="off" <?php echo $disabledText; ?> name="tchrInfo[joinning_year]" class="selectpicker required show-tick form-control" >
									<option value=''> - Select year of joining - </option>
								<?php
								$jy=empty($tchrInfo['joinning_year']['value'])?'':$tchrInfo['joinning_year']['value'];
								if(empty($jy) && !empty($tchrInfo['doj']['value'])){
									$doj=explode('-',$tchrInfo['doj']['value']);
									$jy=count($doj)==3?$doj[2]:'';
								}
								$cy=date("Y");
								for($i=$cy;$i>($cy-60);$i--){
									?>
									<option <?php echo $i==$jy?'selected="selected"':''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php
								}
								?>
								</select>
							</div>
						</div>
					</dd>
				</dl>					
			</div>
			
			<div class="boxBody">
				<dl class="fldList">
					<dt>Position when joined the school<span class="astric">*</span>:</dt>
					<dd>
						<div class="row">
							<div class="col-sm-6"><input required type="text" class="form-control required" <?php echo $readOnlyText; ?> value="<?php echo empty($tchrInfo['position_when_joined']['value'])?'':$tchrInfo['position_when_joined']['value']; ?>" name="tchrInfo[position_when_joined]" autocomplete="off"></div>
						</div>
					</dd>
				</dl>					
			</div>
		
		
			<div class="boxBody">
				<dl class="fldList">
					<dt>No. of promotions since joining<span class="astric">*</span>:</dt>
					<dd>
						<div class="row">
							<div class="col-sm-6">
								<select required autocomplete="off" <?php echo $disabledText; ?> name="tchrInfo[no_of_promotions]" class="selectpicker required show-tick form-control" >
									<option value=''> - Select no. of promotions since joining - </option>
								<?php
								$prom=isset($tchrInfo['no_of_promotions']['value'])?$tchrInfo['no_of_promotions']['value']:'-1';
								for($i=0;$i<31;$i++){
									?>
									<option <?php echo $i==$prom?'selected="selected"':''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php
								}
								?>
								</select>
							</div>
						</div>
					</dd>
				</dl>					
			</div>
			
			<div class="boxBody">
				<dl class="fldList">
					<dt>No. of subjects taught<span class="astric">*</span>:</dt>
					<dd>
						<div class="row">
							<div class="col-sm-6">
								<select autocomplete="off" required <?php echo $disabledText; ?> name="tchrInfo[no_of_subjects_taught]" class="selectpicker required show-tick form-control" >
									<option value=''> - Select no. of subjects taught - </option>
								<?php
								$subs=isset($tchrInfo['no_of_subjects_taught']['value'])?$tchrInfo['no_of_subjects_taught']['value']:'-1';
								for($i=0;$i<16;$i++){
									?>
									<option <?php echo $i==$subs?'selected="selected"':''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php
								}
								?>
								</select>
							</div>
						</div>
					</dd>
				</dl>					
			</div>
			
			<div class="boxBody">
				<dl class="fldList">
					<dt>No. of classes taught per week<span class="astric">*</span>:</dt>
					<dd>
						<div class="row">
							<div class="col-sm-6">
								<select required autocomplete="off" <?php echo $disabledText; ?> name="tchrInfo[no_of_classes_per_week]" class="selectpicker required show-tick form-control" >
									<option value=''> - Select no. of classes taught per week - </option>
								<?php
								$cls=isset($tchrInfo['no_of_classes_per_week']['value'])?$tchrInfo['no_of_classes_per_week']['value']:'-1';
								for($i=0;$i<41;$i++){
									?>
									<option <?php echo $i==$cls?'selected="selected"':''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php
								}
								?>
								</select>
							</div>
						</div>
					</dd>
				</dl>					
			</div>
			
			<div class="boxBody">
				<dl class="fldList">
					<dt>Other role in the school:</dt>
					<dd class="posRel">
						<?php if(!$isReadOnly){ ?><a class="addDynamicRow"><i class="fa fa-plus"></i></a><?php } ?>
				<?php 
					if(empty($tchrInfo['other_roles']['value']) || !is_array($tchrInfo['other_roles']['value']) || count($tchrInfo['other_roles']['value'])==0){
						?>
						<div class="row">
							<div class="col-sm-6"><input required type="text" class="form-control" <?php echo $readOnlyText; ?> value="" name="tchrInfo[other_roles][]" autocomplete="off"></div>
						</div>
						<?php
					}else{
						$i=0;
						foreach($tchrInfo['other_roles']['value'] as $otherRole){
							$i++;
						?>
						<div class="row<?php echo $i>1?' pt20':''; ?>">
							<div class="col-sm-6 posRel">
								<input required type="text" class="form-control" value="<?php echo $otherRole; ?>" <?php echo $readOnlyText; ?> name="tchrInfo[other_roles][]" autocomplete="off">
								<?php if($i>1 && !$isReadOnly){ ?><a class="removeDynamicRow"><i class="fa fa-minus"></i></a><?php } ?>
							</div>
						</div>
						<?php
						}
					}
				?>
					</dd>
				</dl>					
			</div>
			
			<div class="boxBody">
				<dl class="fldList">
					<dt>Your Supervisor's / Principal's name <span class="astric">*</span>:</dt>
					<dd class="posRel">
						<?php if(!$isReadOnly){ ?><a class="addDynamicRow"><i class="fa fa-plus"></i></a><?php } ?>
				<?php 
					if(empty($tchrInfo['supervisors']['value']) || !is_array($tchrInfo['supervisors']['value']) || count($tchrInfo['supervisors']['value'])==0){
						?>
						<div class="row">
							<div class="col-sm-6"><input required type="text" class="form-control required" <?php echo $readOnlyText; ?> value="" name="tchrInfo[supervisors][]" autocomplete="off"></div>
						</div>
						<?php
					}else{
						$i=0;
						foreach($tchrInfo['supervisors']['value'] as $supervisor){
							$i++;
						?>
						<div class="row<?php echo $i>1?' pt20':''; ?>">
							<div class="col-sm-6 posRel">
								<input type="text" required class="form-control required" value="<?php echo $supervisor; ?>" <?php echo $readOnlyText; ?> name="tchrInfo[supervisors][]" autocomplete="off">
								<?php if($i>1 && !$isReadOnly){ ?><a class="removeDynamicRow"><i class="fa fa-minus"></i></a><?php } ?>
							</div>
						</div>
						<?php
						}
					}
				?>
					</dd>
				</dl>					
			</div>
		</div>
		
		<div class="fr clearfix">
			<?php if(!$isReadOnly){ ?>
			<input type="button" autocomplete="off"  id="saveTchrInfoForm" class="fl nuibtn saveBtn" disabled="disabled" value="Save" />
			<?php if($tchrInfo['isTeacherInfoFilled']['value']!=1){ ?><input type="button" autocomplete="off" id="submitTchrInfoForm" <?php echo 0?"":'disabled="disabled"'; ?> class="fl nuibtn submitBtn" value="Submit" /><?php } ?>
			<?php } ?>
		</div>
		
		<div class="clearfix"></div>
		<input type="hidden" value="<?php echo $assessment_id; ?>" name="assessment_id">
	</form>
