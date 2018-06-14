<h1 class="page-title">
	<a href="<?php
						$args=array("controller"=>"customreport","action"=>"networkreportlist");												
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
						Manage MyOverview Reports
					</a> &rarr;
	Create Student Overview Report</h1>
	<div class="clr"></div>
	
	<div class="">
		<div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
		</div>
            
		<div class="subTabWorkspace pad26">
                    <div class="form-stmnt">
<form enctype="multipart/form-data" method="post" id="create_student_data_form" action="#">
                    <dl id="schools_type"  class="fldList">
                            <dt>Report Type<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control" id="report_type" name="report_type" required="required">
                                            <option value="">Report Type</option>
                                            <?php
                                                 foreach ($reportType as $report){
                                                 //if($report['report_id']==12) continue;    
                                                 echo "<option value=\"" . $report['report_id'] . "\">" . $report['report_name'] . "</option>\n";
                                                 }
                                            ?>
                                            
                                        </select>
                                    </div>
                                </div>    
                            </dd>
                    </dl>
                    <dl id="networks"  class="fldList">
                            <dt>Network/ Organisation<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control" id="rec_network" name="network"  required="required">
                                            <option value="">Select Network/ Organisation</option> 
                                            <?php
                                            foreach ($networks as $network)
                                                echo "<option value=\"" . $network['network_id'] . "\">" . $network['network_name'] . "</option>\n";
                                                //echo "<option value=\"" . 'all' . "\">" . 'ALL' . "</option>\n";
                                            ?>
                                        </select>
                                    </div>
                            </dd>
                        </dl>
                        <dl id="provinces"  class="fldList">
                            <dt>Province/ Centre<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control province-list-dropdown" name="province[]" id="rec_provinces"  required="required">
                                        <option value="">Select Province/Centre</option>												
                                        </select>
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <dl id="rec_schools"  class="fldList">
                            <dt>School/ Batch<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control province-list-dropdown" name="school[]" id="evd_school"  required="required">
                                        <option value="">Select School/ Batch</option> 														
                                        </select>
                                    </div>
                                    <!--<div class="col-sm-3 width-50-modal">
                                            <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                    </div>-->
                                </div>
                            </dd>
                        </dl>
                        
                        <dl id="rec_rounds" class="fldList">
                            <dt>Round<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control round-list-dropdown" name="round" id="evd_round"  required="required">
                                        <option value="">Select Round</option>
                                        <?php
                                                                                                                foreach ($aqsRounds as $aqsRound){
                                                                                                                    if($aqsRound['aqs_round']==1 || $aqsRound['aqs_round']==2 ){
                                                                                                                    echo "<option value=\"" . $aqsRound['aqs_round'] . "\">" . $aqsRound['aqs_round'] . "</option>\n";
                                                                                                                    }
                                                                                                                }
                                                                                                                ?>                                        
                                        </select>
                                    </div>
                                    <!--<div class="col-sm-3 width-50-modal">
                                            <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                    </div>-->
                                </div>
                            </dd>
                        </dl>
    
    
                        <dl id="rec_report_name" class="fldList" style="display: none">
                            <dt>Report Name<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <input type="text" name="report_name" class="form-control" id="report_name" value="" maxlength="60">
                                    </div>
                                    <!--<div class="col-sm-3 width-50-modal">
                                            <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                    </div>-->
                                </div>
                            </dd>
                        </dl>
                          
                         <dl class="fldList">
                            
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                        <div id="errors" style=" display: none;"></div>
                        <input type="submit" name="submitevidencedata" value="Generate Report" class="btn btn-primary mt25 mb30">
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
                        <div class="ajaxMsg"></div>
			
                        </div>
                        <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                     </form>
                    </div>
		</div>
	</div>