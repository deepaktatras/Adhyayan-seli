<h1 class="page-title">
<a href="<?php
						$args=array("controller"=>"index","action"=>"index");	
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
					
				Manage</a> > Export to Excel > Export Summary Report for Student Reviews into Excel
</h1>
<!--<h1 class="page-title">
		Export Evidence data into Excel
	</h1>-->
	<div class="clr"></div>
	
	<div class="">
		<div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
		</div>
            
		<div class="subTabWorkspace pad26">
                    <div class="form-stmnt">
<form enctype="multipart/form-data" method="post" id="create_evidence_data_form" action="?controller=exportExcel&action=overallsummary">
                    
                    <dl id="networks"  class="fldList">
                            <dt>Organisation:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-8 width-50-modal">
                                        <select class="form-control" id="rec_network" name="network" required="required">
                                            <option value="">Select Organisation</option> 
                                            <?php
                                            foreach ($networks as $network){
                                                $selected=($network_s==$network['network_id'])?"Selected='Selected'":"";
                                                echo "<option value=\"" . $network['network_id'] . "\" ".$selected.">" . $network['network_name'] . "</option>\n";
                                            }
                                            ?>
                                        </select>
                                    </div>
                            </dd>
                        </dl>
                        <dl class="fldList">
										<dt>Dates:</dt>
                                                                                <dd><div class="row"><div class="col-sm-1 csdate" style="padding-top: 5px;white-space:nowrap;">From Date:</div><div class="col-sm-3 fromdate">
										<div class="input-group fdate" id="date_picker">
                                                        <input type="text" class="form-control fdate" placeholder="YYYY-MM-DD" readonly  name="report_date_from" id="report_date_from"
                                                               value="<?php echo $report_date_from ?>"  required autocomplete="off"/>
                                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                        </div>
										</div>
                                                                                        <div class="col-sm-1 cedate"  style="padding-top: 5px;white-space:nowrap;">To Date:</div><div class="col-sm-3 todate">
										<div class="input-group edate" id="date_picker">
                                                        <input type="text" class="form-control edate" placeholder="YYYY-MM-DD" readonly  name="report_date_to" id="report_date_to"
                                                               value="<?php echo $report_date_to ?>"  required autocomplete="off"/>
                                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                        </div>
										</div>
                                                                                    
                                                                                    </div>
                                                                                </dd>
									</dl>
                        <br>
                        <div class="row"><div class="col-sm-2"></div>
                        
			<small><b>Note:-</b> <i>If dates not selected, all student reviews data against selected organisation will be exported.</i></small>
                        
                        </div>
                         <dl class="fldList">
                            
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                        <div id="errors" style=" display: none;"></div>
                        <input type="submit" name="submitevidencedata" value="Download Data" class="btn btn-primary mt25 mb30">
			<!--<a class="btn btn-primary mt25 mb30" href="?controller=exportExcel&amp;action=downloadEvidenceDataExcel">Download Evidence Data</a>-->
                        </div>
                                    <!--<div class="col-sm-3 width-50-modal">
                                            <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                    </div>-->
                                </div>
                            </dd>
                        </dl>
			<br>
                        <div class="row"><div class="col-sm-1"></div>
                        <div class="ajaxMsg" id="createresource"></div>
			
                        </div>
                        <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                     </form>
                    </div>
		</div>
	</div>
        <?php if(isset($error)){
        ?>
     <script>
        $(".ajaxMsg").show();
        $( ".ajaxMsg" ).addClass( "danger active" );
        $(".ajaxMsg").html('<?php echo $error; ?>');
        $(".ajaxMsg").delay(2000).fadeOut();
        

                                                                    
      </script>
        <?php
        } ?>
      <script>
var date = new Date();
var currentMonth = date.getMonth();
var currentDate = date.getDate();
var currentYear = date.getFullYear();   
 //$('.fdate').datetimepicker({format: 'YYYY-MM-DD', minDate:new Date(currentYear, currentMonth, currentDate), pickTime: false});
 //$('.edate').datetimepicker({format: 'YYYY-MM-DD', minDate:new Date(currentYear, currentMonth, currentDate), pickTime: false});
$('.fdate').datetimepicker({format: 'YYYY-MM-DD', pickTime: false});
$('.edate').datetimepicker({format: 'YYYY-MM-DD', pickTime: false});


                                                                    $(".fdate").on("dp.change", function (e) {
                                                                        $('.edate').data("DateTimePicker").setMinDate(e.date);
                                                                    });
                                                                    $(".edate").on("dp.change", function (e) {
                                                                        $('.fdate').data("DateTimePicker").setMaxDate(e.date);
                                                                    });
    </script>