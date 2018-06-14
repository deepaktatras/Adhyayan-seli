<?php
$disabled="";
if(in_array(8,$user['role_ids'])){
$disabled="disabled";
}
?>

<h1 class="page-title">
				<?php if($isPop==0){?>
					<a href="<?php
						$args=array("controller"=>"workshop","action"=>"allworkshop");						
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						Manage Workshop
                                        </a> &rarr;
				<?php }?>
						Add Workshop
				</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26">
						<div class="form-stmnt">
                                                    <form method="post" id="create_workshop_form" action=""  class="workshop_form" >
                                                            <div class="boxBody">
									<dl class="fldList">
										<dt>Title of Workshop<span class="astric">*</span>:</dt>
                                                                                <dd><div class="row"><div class="col-sm-8">
										<input type="text" value="<?php  ?>"
                                                                                       class="form-control" name="workshop_name"  required autocomplete="off"/>
										</div></div></dd>
									</dl>
                                                                        <dl class="fldList">
										<dt>Description/ Objective:</dt>
                                                                                <dd><div class="row"><div class="col-sm-8">
                                                                                            <textarea class="form-control" name="workshop_description"></textarea>            
										
										</div></div></dd>
									</dl>
                                                                    
                                                                       <dl class="fldList">
										<dt>Workshop Dates<span class="astric">*</span>:</dt>
                                                                                <dd><div class="row"><div class="col-sm-1 csdate" style="padding-top: 5px;white-space:nowrap;">Start Date:</div><div class="col-sm-3 fromdate">
										<div class="input-group fdate" id="date_picker">
                                                        <input type="text" class="form-control fdate" placeholder="DD-MM-YYYY" readonly  name="workshop_date_from" id="workshop_date_from"
                                                               value=""  required autocomplete="off"/>
                                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                        </div>
										</div>
                                                                                        <div class="col-sm-1 cedate" style="padding-top: 5px;white-space:nowrap;">End Date:</div><div class="col-sm-3 todate">
										<div class="input-group edate" id="date_picker">
                                                        <input type="text" class="form-control edate" placeholder="DD-MM-YYYY" readonly  name="workshop_date_to" id="workshop_date_to"
                                                               value=""  required autocomplete="off"/>
                                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                        </div>
										</div>
                                                                                    
                                                                                    </div></dd>
									</dl>
									
                                                                        <dl class="fldList">
										<dt>Venue<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-8"  style="padding-top: 5px;">
                                                                                <textarea class="form-control" name="workshop_location" required></textarea>
										
										</div></div></dd>
									</dl>
                                                                        <dl class="fldList">
										<dt>Name of the school:</dt>
										<dd><div class="row"><div class="col-sm-8"  style="padding-top: 5px;">
                                                                                            <select name="workshop_school" id="workshop_school" class="form-control" <?php echo $disabled ?> >
                                                                                                <option value=""> - Select School -</option> 
                                                                                                <option value="None of the mentioned">  None of the mentioned </option> 
                                                                                                <?php
												foreach($clients as $client){
                                                                                                       
													echo "<option value=\"".$client['client_id']."\" >".$client['client_name'].($client['city']!=""?", ".$client['city']:'')."</option>\n";
                                                                                                }
                                                                                                        ?>
                                                                                            </select>
                                                                                            <input type="text" value="" class="form-control" name="workshop_school_none" placeholder="Enter Name of the school" maxlength="250" id="workshop_school_none"  autocomplete="off"/>
										</div></div></dd>
									</dl>
                                                                        <dl class="fldList">
										<dt>Programme Name<span class="astric">*</span>:</dt>
										<dd><div class="row"><div class="col-sm-8"  style="padding-top: 5px;">
                                                                                            <select name="workshop_programme" class="form-control" required <?php echo $disabled ?> >
                                                                                                <option value=""> - Select Programme -</option> 
                                                                                                <?php
												foreach($programmes as $programme){
                                                                                                        if(in_array(8,$user['role_ids'])){
                                                                                                        echo "<option value=\"".$programme['programme_id']."\" ".($programme['programme_id']==1?"selected=selected":'').">".$programme['programme_name']."</option>\n";

                                                                                                        }else
													echo "<option value=\"".$programme['programme_id']."\" >".$programme['programme_name']."</option>\n";
                                                                                                }
                                                                                                        ?>
                                                                                            </select>
										</div></div></dd>
									</dl>
                                                                
                                                                        <dl class="fldList">
										<dt>Programme Type:</dt>
										<dd><div class="row"><div class="col-sm-8"  style="padding-top: 5px;">
                                                                                            <select name="prog_type_id" class="form-control" <?php echo $disabled ?> >
                                                                                                <option value=""> - Select Programme Type -</option> 
                                                                                                <?php
												foreach($programmestype as $programmetype){
                                                                                                        
													echo "<option value=\"".$programmetype['prog_type_id']."\" >".$programmetype['prog_type']."</option>\n";
                                                                                                }
                                                                                                        ?>
                                                                                            </select>
										</div></div></dd>
									</dl>
                                                                
                                                                            <dl class="fldList">
										<dt>Charges for the Workshop:</dt>
                                                                                <dd><div class="row"><div class="col-sm-8">
										<input type="text" value=""
                                                                                       class="form-control" name="workshop_charges" id="workshop_charges"  maxlength="20" autocomplete="off"/>
										</div></div></dd>
									    </dl>
                                                                
                                                                            <dl class="fldList">
										<dt>Other expenses:</dt>
										<dd><div class="row"><div class="col-sm-8"  style="padding-top: 5px;">
                                                                                <textarea class="form-control" name="workshop_payment_facilitator"></textarea>
										
										</div></div></dd>
									    </dl>
                                                                
                                                                            <dl class="fldList">
                                                                                <dt>Upload Document:</dt>
                                                                                <dd class="judgementS" style="background-color: transparent;">
                                                                                    <div class="panel-group" id="accordion">
                                                                                    <?php 
                                                                                    $i=1;
                                                                                    foreach($document_category as $key=>$val){
                                                                                    $class_show=$i==1?"collapse in":"collapse";
                                                                                    ?>
                                                                                        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $val['workshop_doc_cat_id'] ?>"><?php echo $val['workshop_doc_cat_name']; ?></a>
                    <span style="float:right" id="count_<?php echo $val['workshop_doc_cat_id'] ?>"  class="wrapclear"></span>
                </h4>
            </div>
                                                                                    <div id="collapse<?php echo $val['workshop_doc_cat_id'] ?>" class="panel-collapse <?php echo $class_show; ?>">
                <div class="panel-body">
                                                                                    <div class="upldHldr">
                                                                                        <div class="fileUpload btn btn-primary mr0">
                                                                                            <i class="glyphicon glyphicon-folder-open"></i> <span>Attach File</span>  
                                                                                            <input type="file" autocomplete="off" title="" class="upload uploadBtnWorkshop" reltype="<?php echo $val['workshop_doc_cat_id'] ?>" id="workshop_upload<?php echo $val['workshop_doc_cat_id'] ?>">
                                                                                        </div>
                                                                                        <span style="margin-left: 10px;"><i>(Only given formats jpeg,png,gif,jpg,avi,mp4,mov,doc,docx,txt,xls,xlsx,pdf,csv,xml,pptx,ppt,cdr,mp3,wav are allowed!)</i></span>
                                                                                        <div class="filesWrapper<?php echo $val['workshop_doc_cat_id'] ?> wrapclear" style="margin-top: 10px;">                                               
                                                                                            <?php
                                                                                            // $file_name = explode('~',$eUser['file_name']);
                                                                                            if(!empty($eUser)) {
                                                                                                $upload_document = array_unique(explode(',', $eUser['upload_document']));
                                                                                                if ($eUser['file_name'] != '' && $eUser['upload_document'] != '') {
                                                                                                    foreach ($eUser['file_name'] as $key => $file_name) {
                                                                                                        echo '<div class="filePrev uploaded vtip ext-' . diagnosticModel::getFileExt($file_name['file_name']) . '" id="file-' . $file_name['file_id'] . '" title="' . $file_name['file_name'] . '">'
                                                                                                        . '<span class="delete fa" id="doc"></span>'
                                                                                                        . '<div class="inner"><a href="' . UPLOAD_URL . '' . $file_name['file_name'] . '" target="_blank"> </a></div>'
                                                                                                        . '<input type="hidden" name="files[]" value="' . $file_name['file_id'] . '" id="files"></div>';
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                            ?>

                                                                                        </div>
                                                                                    </div>  
                </div></div></div>
                                                                                    
                                                                                    <?php
                                                                                    $i++;
                                                                                    }
                                                                                    ?>
                                                                                </div>

                                                                                </dd>
                                                                            </dl>
                                                                
									<div class="boxBody">
									<div class="clr" style="margin-top:5px;"></div>
									<p><b>Workshop team</b><span class="astric">*</span>:</p>
									<div class="tableHldr teamsInfoHldr school_team team_table noShadow">
                                                                        
                                                                            <a href="javascript:void(0)" class="extteamworkshopAddRow" title="Add New Co-Facilitator"><i class="fa fa-plus"></i></a>
                                                                            
									<table class='table customTbl'>
									<thead>
										<tr><th style="width:8%">Sr. No.</th><th style="width:32%">School</th><th style="width:20%">Role</th><th style="width:30%">Member</th><th style="width:30%">Payment to facilitator</th><th style="width:5%;"></th></tr>	
									</thead>
									<tbody>
										<tr class='team_row'><td class='s_no'>1</td>
                                                                                    <td><select class="form-control external_client_id" name="facilitator_client_id" id="facilitator_client_id" required>
												<option value=""> - Select School - </option>
												<?php
												foreach($clients as $client)
													echo "<option value=\"".$client['client_id']."\">".$client['client_name']."</option>\n";
												?>
											</select></td>
										<td>Leader</td>
											<td><select class="form-control external_assessor_id" name="facilitator_id" id="lead_assessor" required>
													<option value=""> - Select Member - </option>
                                                                                            </select>
                                                                                        </td>                                                                                                
                                                                                        <td>
                                                                                            <input class="form-control external_assessor_id" name="facilitator_payment" id="facilitator_payment" type="text">
                                                                                        </td>
											<td></td>	
										</tr>										
									</tbody>
									</table>
                                                                                  
									</div>
									</div>
									
								        <dl class="fldList">
										<dt></dt>
										<dd class="nobg">
											<div class="row">
												<div class="col-sm-6">
													<br>
													<input type="submit" title="Click to save a workshop."  value="Save Workshop" class="btn btn-primary vtip">
												</div>
											</div>
										</dd>
									</dl>
									
								</div>
								<div class="ajaxMsg"></div>
								<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
							</form>
						</div>
					</div>
				</div>
<script>
var date = new Date();
var currentMonth = date.getMonth();
var currentDate = date.getDate();
var currentYear = date.getFullYear();   
 //$('.fdate').datetimepicker({format: 'YYYY-MM-DD', minDate:new Date(currentYear, currentMonth, currentDate), pickTime: false});
 //$('.edate').datetimepicker({format: 'YYYY-MM-DD', minDate:new Date(currentYear, currentMonth, currentDate), pickTime: false});
$('.fdate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false});
$('.edate').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, pickTime: false});


                                                                    $(".fdate").on("dp.change", function (e) {
                                                                        $('.edate').data("DateTimePicker").setMinDate(e.date);
                                                                    });
                                                                    $(".edate").on("dp.change", function (e) {
                                                                        $('.fdate').data("DateTimePicker").setMaxDate(e.date);
                                                                    });
                                                                    
$("#workshop_school_none").hide();
$("#workshop_school").change(function(){
   
   if($(this).val()=="None of the mentioned"){
     $("#workshop_school_none").show();  
   }else{
     $("#workshop_school_none").hide();
     $("#workshop_school_none").val("");
   }
});

$("#workshop_charges").keypress(function(event){
   return isNumberKey1(event); 
});

</script>
<script type="text/javascript" src="<?php echo SITEURL;?>public/js/userprofile.js"></script>