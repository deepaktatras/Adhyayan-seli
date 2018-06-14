<?php
	$isReadOnly=empty($isReadOnly)?0:1;
	$readOnlyText=$isReadOnly?'readonly="readonly"':"";
	$disabledText=$isReadOnly?'disabled="disabled"':"";
?>
<span id="load_edit">
				<h1 class="page-title">
				<?php if($isPop==0){?>
					<a href="<?php
						$args=array("controller"=>"assessment","action"=>"assessment");						
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						Manage MyReviews
					</a>
					>
				<?php }?>
                                               
					<?php echo $userType=="self"?"My Profile Page":" ".$can_name."'s Profile Page"; ?>
                                               
				</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabmitemsHldr"></div>
                                                                             
                                        </div>
                                                
					</div>
					<div class="subTabWorkspace pad26">
                                            <div>
							<form method="post" id="create_student_profile" action="">
                                                            
								<div class="boxBody">
                                                                    <?php 
                                                                    //echo "<pre>";print_r($form_attributes);
                                                                    $array_not_mandatory=array("1","2","3","8","9","10","11","12","13","14","16","21","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","46","47");
                                                                    foreach($form_attributes  as $attributes){
                                                                        if($attributes['field_name']=="is_submit" || $attributes['field_name']=="email") continue;
                                                                        $id = '';
                                                                        $class = '';
                                                                        $dispaly = '';
                                                                        if($attributes['field_type'] != 'radio') {
                                                                            $id =  !empty($attributes['class'])?$attributes['class']:'';
                                                                        }
                                                                        if($attributes['value_type'] == 3) {
                                                                            $class =  "mask_ph";
                                                                            $st_country_code = '';
                                                                            $cell_number = '';
                                                                            if (isset($attributes['value'])) {
                                                                                $number = explode(")", $attributes['value']);
                                                                                //print_r($number);
                                                                                if (isset($number[0]) && count($number) > 1) {
                                                                                    $st_country_code = explode("+", $number[0]);
                                                                                    $cell_number = trim($number[1]);
                                                                                } else if (count($number) == 1) {
                                                                                    $cell_number = trim($number[0]);
                                                                                } else if (isset($number[1])) {
                                                                                    $cell_number = trim($number[1]);
                                                                                }
                                                                                //echo $cell_number;
                                                                                //print_r($cell_country_code);
                                                                            }
                                                                           // $id = 'cell_number';
                                                                        }
                                                                        if($attributes['value']!='') {
                                                                            $dispaly = 'display:block';
                                                                        }else if($attributes['visibility']!=1) {
                                                                             $dispaly = 'display:none';
                                                                        }
                                                                        if($attributes['field_id']==5 && empty($attributes['value'])){
                                                                        $attributes['value']=$can_name;    
                                                                        }
                                                                        
                                                                        if($attributes['field_id']==1 && empty($attributes['value'])){
                                                                        $attributes['value']=$batch_code;    
                                                                        }
                                                                        
                                                                        ?>
                                                                    <div class="<?php echo $id;?>" style="<?php echo $dispaly ?>">
                                                                    <dl class="fldList"  >
										<dt><?php echo $attributes['field_label'];?><?php echo !in_array($attributes['field_id'],$array_not_mandatory)?'<span class="astric">*</span>':'' ?>:</dt>
										<dd>
											<div class="row">
												<div class="col-sm-6">
													<span id="selected_client_name"></span>
                                                                                                        <?php if($attributes['field_type'] == 'text' ) { 
                                                                                                              if($attributes['value_type'] == 3) { ?>
                                                                                                                    <div class="inlContBox ftySixty">
                                                                                                                                <div class="inlCBItm fty">
                                                                                                                                    <div class="fld blk">
                                                                                                                                        <div>
                                                                                                                                            <select name="st_country_code_<?php echo $attributes['field_id'];?>" id="st_country_code_<?php echo $attributes['field_id'];?>" class="form-control" >
                                                                                                                                                <?php
                                                                                                                                                    
                                                                                                                                                foreach ($countryCodeList as $value) {
                                                                                                                                                    ?>
                                                                                                                                                    <option value="<?php echo $value['phonecode'] ?>"
                                                                                                                                                            <?php echo !empty($st_country_code[1]) && $st_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
                                                                                                                                                        <?php echo "(+".$value['phonecode'] .") " ?></option>
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
                                                                                                                                            <input <?php if(!in_array($attributes['field_id'],$array_not_mandatory)) echo "required"; ?> type="text" class="form-control aqs_ph <?php if(!in_array($attributes['field_id'],$array_not_mandatory)) echo "required"; ?>" <?php echo $readOnlyText; ?> value="<?php echo  isset($attributes['value'])?$cell_number:''; ?>" name="<?php echo $attributes['field_id'];?>" autocomplete="off" <?php echo $readOnlyText; ?>>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                    </div>
                                                                                                                  
                                                                                                             <?php }else {
                                                                                                            ?>
                                                                                                            <input type="text" value="<?php echo isset($attributes['value'])?$attributes['value']:'';?>" class="form-control <?php echo $class;?>" autocomplete="off" id="" name="<?php echo $attributes['field_id'];?>"  autocomplete="off" <?php echo $readOnlyText; ?>>
                                                                                                        <?php } 
                                                                                                        }
                                                                                                        ?>
                                                                                                        <?php if($attributes['field_type'] == 'text_area' ) { ?>
                                                                                                            <textarea name="<?php echo $attributes['field_id'];?>" class="form-control"  <?php echo $disabledText; ?> ><?php echo isset($attributes['value'])?$attributes['value']:'';?></textarea>
                                                                                                        <?php } ?>
                                                                                                            <?php if($attributes['field_type'] == 'dob' ) { ?>
                                                                                                            <div class="input-group date" id="date_picker">
                                                                                                            <input type="text" value= "<?php echo isset($attributes['value'])?$attributes['value']:'';?>" class="form-control date" placeholder="DD-MM-YYYY" readonly  name="<?php echo $attributes['field_id'];?>" id="date_of_birth"
                                                                                                                    <?php echo $readOnlyText; ?>>
                                                                                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                                                                            </div>
                                                                                                        <?php } ?>
                                                                                                           <?php if($attributes['field_type'] == 'hidden' ) { ?>
                                                                                                            <input type="hidden"    name="<?php echo $attributes['field_id'];?>" value="<?php echo isset($attributes['value'])?$attributes['value']:'';?>">
                                                                                                         <?php } ?>
                                                                                                            <?php if($attributes['field_type'] == 'date' ) { ?>
                                                                                                            <div class="input-group date" id="date_picker">
                                                                                                            <input type="text" class="form-control date" placeholder="DD-MM-YYYY" readonly  name="<?php echo $attributes['field_id'];?>" id="date_of_birth"
                                                                                                                   value="<?php echo isset($attributes['value'])?$attributes['value']:'';?>"  <?php echo $readOnlyText; ?>>
                                                                                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                                                                            </div>
                                                                                                         <?php } ?>
                                                                                                            <?php if($attributes['field_type'] == 'state' ) { ?>
                                                                                                            <select id="state_id" name="<?php echo $attributes['field_id'];?>" class="form-control"  <?php echo $disabledText; ?>>
                                                                                                             <option value=""> - Select State - </option>    
                                                                                                             <?php   foreach($states as $state) { ?>
                                                                                                                         <?php
                                                                                                                         if(isset($attributes['value']) && $attributes['value']==$state['state_id']){
                                                                                                                         ?>
                                                                                                                         <option value="<?php echo $state['state_id'];?>" selected><?php echo $state['state_name'];?></option> 
                                                                                                                         <?php
                                                                                                                         }else{
                                                                                                                         ?>
                                                                                                                        <option value="<?php echo $state['state_id'];?>"><?php echo $state['state_name'];?></option>
                                                                                                                        <?php
                                                                                                                         }
                                                                                                                        ?>
                                                                                                               <?php 
                                                                                                               
                                                                                                                         } 
                                                                                                               ?>
                                                                                                            </select>
                                                                                                            <?php } ?>
                                                                                                            <?php if($attributes['field_type'] == 'city' ) { ?>
                                                                                                            <select id="city_id" name="<?php echo $attributes['field_id'];?>"  class="form-control"  <?php echo $disabledText; ?>>
                                                                                                            <option value=""> - Select City - </option>
                                                                                                            <?php
                                                                                                            foreach($cities as $city_value){
                                                                                                                
                                                                                                             
                                                                                                                         if(isset($attributes['value']) && $attributes['value']==$city_value['city_id']){
                                                                                                                         ?>
                                                                                                                         <option value="<?php echo $city_value['city_id'];?>" selected><?php echo $city_value['city_name'];?></option> 
                                                                                                                         <?php
                                                                                                                         }else{
                                                                                                                         ?>
                                                                                                                        <option value="<?php echo $city_value['city_id'];?>"><?php echo $city_value['city_name'];?></option>
                                                                                                                        <?php
                                                                                                                         }
                                                                                                           
                                                                                                                
                                                                                                            }
                                                                                                            ?>
                                                                                                            </select>
                                                                                                            <?php } ?>
                                                                                                        <?php if($attributes['field_type'] == 'radio'  && $attributes['field_name'] == 'gender' ) { ?>
                                                                                                            <div class="chkHldr">
                                                                                                            <input  autocomplete="off" name="<?php echo $attributes['field_id'];?>" value="1" type="radio" <?php echo (isset($attributes['value']) && $attributes['value'] == 1)?'checked="checked"':'';?>   <?php echo $disabledText; ?>>
                                                                                                            <label class="chkF radio">
                                                                                                            <span>Male</span>
                                                                                                            </label>
                                                                                                            </div>
                                                                                                            <div class="chkHldr">
                                                                                                                <input  autocomplete="off" name="<?php echo $attributes['field_id'];?>" value="2" type="radio" <?php echo (isset($attributes['value']) && $attributes['value']== 2)?'checked="checked"':'';?>  <?php echo $disabledText; ?>>
                                                                                                                <label class="chkF radio">
                                                                                                                <span>Female</span>
                                                                                                                </label>
                                                                                                            </div>
                                                                                                        <?php }else  if($attributes['field_type'] == 'radio'  ) { ?>
                                                                                                            <div class="chkHldr">
                                                                                                                <input  autocomplete="off" name="<?php echo $attributes['field_id'];?>" value="1" type="radio" id="<?php echo !empty($attributes['class'])?$attributes['class']:'';?>" <?php echo (isset($attributes['value']) && $attributes['value']== 1)?'checked="checked"':'';?>  <?php echo $disabledText; ?>>
                                                                                                            <label class="chkF radio">
                                                                                                            <span>YES</span>
                                                                                                            </label>
                                                                                                            </div>
                                                                                                            <div class="chkHldr">
                                                                                                                <input  autocomplete="off" name="<?php echo $attributes['field_id'];?>" value="2" type="radio" id="<?php echo !empty($attributes['class'])?$attributes['class']:'';?>" <?php echo (isset($attributes['value']) && $attributes['value']== 2)?'checked="checked"':'';?>  <?php echo $disabledText; ?>>
                                                                                                                <label class="chkF radio">
                                                                                                                <span>NO</span>
                                                                                                                </label>
                                                                                                            </div>
                                                                                                        <?php } ?>
												</div>
											</div>
										</dd>
									</dl>
                                                                    </div>
                                                                     <?php   
                                                                    }
                                                                    ?>
								
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                                                                <div class="text-right" style="padding-top:5px;">
                                                                    <?php if(!$isReadOnly){ ?>
                                                                    <?php if((!isset($student_array['is_submit']) || (isset($student_array['is_submit']) && $student_array['is_submit']!=1))) { ?>
                                                                        <input type="submit" title="Click to create a student profile page." value="Save" name="save" id="save_student_profile" class="btn vtip btn-primary">
                                                                    <?php } ?>
                                                                        <input type="submit" title="Click to create a student profile page." value="Submit" name="submit" id="submit" class="btn vtip btn-primary">
                                                                   <?php
                                                                    }
                                                                   ?>
                                                                </div>
                                                                <div id="validationErrors"></div>
                                                                <input type="hidden" value="<?php echo $assessment_id; ?>" name="assessment_id">
							</form>
						</div>
					</div>
				</div></span>
<script type="text/javascript">
       // $("textarea.word_count").textareaCounter();
       $('.date').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false , maxDate: new Date, pickTime: false});
        $(document).ready(function(){
            $('.mask_ph').mask("(+99) 999-9999-999");
        });
    </script>