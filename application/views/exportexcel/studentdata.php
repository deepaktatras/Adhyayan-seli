<h1 class="page-title">
<a href="<?php
						$args=array("controller"=>"index","action"=>"index");	
						echo createUrl($args); 
						?>">
						<i class="fa fa-chevron-circle-left vtip" title="Back"></i>
					
				Manage</a> > Export to Excel > Export Student data into Excel
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
<form enctype="multipart/form-data" method="post" id="create_evidence_data_form" action="?controller=exportExcel&action=studentdata">
                    <dl id="schools_type"  class="fldList">
                            <dt>Schools Related To<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control" id="school_related_to" name="school_related_to">
                                            <option value="1" selected="selected">Network</option>
                                            
                                            
                                        </select>
                                    </div>
                                </div>    
                            </dd>
                    </dl>
                    <dl id="networks"  class="fldList">
                            <dt>Organisation:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control" id="rec_network" name="network">
                                            <option value="">Select Organisation</option> 
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
                            <dt>Centre:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control province-list-dropdown" name="province" id="rec_provinces">
                                        <option value="">Select Centre</option>  														
                                        </select>
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <dl id="rec_schools"  class="fldList">
                            <dt>Batch:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control province-list-dropdown" name="school" id="evd_school">
                                        <option value="">Select Batch</option>      														
                                        </select>
                                    </div>
                                    <!--<div class="col-sm-3 width-50-modal">
                                            <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                    </div>-->
                                </div>
                            </dd>
                        </dl>
                        
                        <dl id="rec_rounds" class="fldList">
                            <dt>Round:</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6 width-50-modal">
                                        <select class="form-control round-list-dropdown" name="round" id="evd_round">
                                        <option value="">Select Round</option>
                                        <?php
                                                                                                                foreach ($aqsRounds as $aqsRound){
                                                                                                                    
                                                                                                                    echo "<option value=\"" . $aqsRound['aqs_round'] . "\">" . $aqsRound['aqs_round'] . "</option>\n";
                                                                                                                    
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