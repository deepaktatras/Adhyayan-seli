<?php //echo"<pre>";print_r($details); ?>
<h1 class="page-title"><a href="<?php
						$args=array("controller"=>"actionplan","action"=>"actionplan1");													
						$args["filter"]=1;
                                                $args["assessment_id"]=$assessment_id;
						echo createUrl($args); 
						?>">
        <i class="fa fa-chevron-circle-left vtip" title="Back"></i></a> View Action Plan&rarr;				
				<?php echo $assessment_details['client_name']; ?></h1>
<?php
//print_r($details);



$date=(isset($details['from_date']) && $details['from_date']!="0000-00-00")?$details['from_date']:'';
$date_start_real=(isset($details['from_date']) && $details['from_date']!="0000-00-00")?$details['from_date']:'';
$date_end_real=(isset($details['to_date']) && $details['to_date']!="0000-00-00")?$details['to_date']:date("Y-m-d");

if($date_end_real>date("Y-m-d")){
$end_date=date("Y-m-d");
}else{
$end_date =$date_end_real;
}

$array_dates=array();
$ii=0;
if(!empty($date)){

    $ii=0;   
    while (strtotime($date) <= strtotime($end_date)) {
             $sdate=date ("Y-m-d",strtotime($date));
            
             $dateex = date ("Y-m-d", strtotime("".$details['frequency_days']."", strtotime($date)));
            
             $date = date ("Y-m-d", strtotime("-1 day", strtotime($dateex)));
            
             if($date>$date_end_real){
             $dateex = date ("Y-m-d", strtotime("+1 day", strtotime($date_end_real)));
             $date = date ("Y-m-d", strtotime("-1 day", strtotime($dateex)));  
             $array_dates[$ii]['fromDate']=$sdate;
             $array_dates[$ii]['endDate']=$date_end_real;
             $date = date ("Y-m-d", strtotime("+1 day", strtotime($date_end_real)));
             $ii++;
             break;
             }else{
            
             $array_dates[$ii]['fromDate']=$sdate;
             $array_dates[$ii]['endDate']=$date;
             $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
             }
       $ii++;
    }

}


    
if($ii==0){
if($date_start_real>date("Y-m-d")){
    $sdate=date ("Y-m-d",strtotime($date));
    $date = date ("Y-m-d", strtotime("".$details['frequency_days']."", strtotime($sdate)));
    $nextDate=date("d-m-Y",strtotime($date));
    $array_dates[$ii]['fromDate']=$sdate;
    $array_dates[$ii]['endDate']=$date;
    
    if($nextDate<$date_end_real){
    $date = $date_end_real;
    $nextDate=date("d-m-Y",strtotime($date));  
    $array_dates[$ii]['fromDate']=$sdate;
    $array_dates[$ii]['endDate']=$date;
    }
}else{    
$sdate=date ("Y-m-d",strtotime($date));   
//$date = date ("Y-m-d", strtotime("".$details['frequency_days']."", strtotime($date)));
//$datefinal = date ("Y-m-d", strtotime("-1 day", strtotime($date)));
$dateex = date ("Y-m-d", strtotime($end_date));
$array_dates[$ii]['fromDate']=$sdate;
$array_dates[$ii]['endDate']=$dateex;
$date = date ("Y-m-d", strtotime($dateex));
$nextDate=date("d-m-Y",strtotime($date));
}

}else{
$date = date ("Y-m-d", strtotime("-1 day", strtotime($dateex)));
$nextDate=date("d-m-Y",strtotime($date));
if($date<date("Y-m-d")){
$nextDate="Last Date is over";   
}
}



//echo"<pre>";
//print_r($array_dates);
$array_dates_f=array();
foreach($array_dates as $key=>$val){
if($val['endDate']<=date("Y-m-d")){
    $array_dates_f[]=$val;
}
}
//print_r($array_dates_f);
?>
                <div class="ylwRibbonHldr">                    
                    <div class="tabitemsHldr ribWrap"></div>
                    <div class="tab-pane-mand"><div class="wrapNote">
                            <?php if(count($array_dates_f)>0){ ?>
                            <select name="datesrange" id="datesrange">
                            <?php if(count($array_dates_f)>1){ ?> <option value="">Overall Report</option> <?php }?>
                            <?php
                            $tot=1;
                            foreach($array_dates_f as $key=>$val){
                                
                            ?>
                                <option value="<?php echo $val['fromDate'] ?>/<?php echo $val['endDate'] ?>" <?php if($tot==count($array_dates_f)) echo"selected='selected'"; ?>><?php echo date("d F Y",strtotime($val['fromDate'])); ?> to <?php echo date("d F Y",strtotime($val['endDate'])) ?></option>
                            <?php
                             $tot++;   
                            }
                            ?>
                            </select>
                            <button id="submitActionReport">View Report</button>
                            <?php } ?>
                        </div></div>
                </div> 
                <div class="subTabWorkspace">
                    <div class="actionPlanWrap">
                        <form name="statementActionPlan" action="" id="actionplanform2" method="post" enctype="multipart/form-data">
                           <input type="hidden" name="assessment_id" id="assessment_id" value="<?php echo $assessment_id ?>" > 
                           <input type="hidden" name="id_c" id="id_c" value="<?php echo $id_c ?>" >
                           <input type="hidden" name="h_assessor_action1_id" id="h_assessor_action1_id" value="<?php echo $h_assessor_action1_id  ?>" >
                           <input type="hidden" name="exportUrl" id="exportUrl" value="<?php echo DOWNLOAD_CHART_URL ?>" ?>
                           <div class="multBoxFldWrap clearfix">
                                <dl class="fldList">
                                    <dt>KPA Name</dt>
                                    <dd><div class="inputWrap"><input type="text" class="form-control" readonly value="<?php echo $details['translation_text'] ?>"></div></dd>
                                </dl>
                                <dl class="fldList">
                                    <dt>Next Reporting Date</dt>
                                    <dd><div class="inputWrap"><input type="text" class="form-control" readonly value="<?php echo ChangeFormat($nextDate,"d F Y",""); ?>"></div></dd>
                                </dl>
                                
                                
                                <dl class="fldList">
                                    <dt>Leader</dt>
                                    <dd><div class="inputWrap half"><input type="text" class="form-control" readonly value="<?php echo $details['name'] ?>"></div></dd>
                                </dl>
                                <dl class="fldList">

                                    <dt>Date of Review</dt>
                                    <dd><div class="inputWrap half"><input type="text" class="form-control" readonly value="<?php echo ChangeFormat($rating_date,"d F Y",""); ?>"></div></dd>
                                </dl>
                                <dl class="fldList fullWdth">
                                    <dt>Recommendation</dt>
                                    <dd><div class="inputWrap"><?php echo $details['text_data'] ?></div></dd>
                                </dl>
                                
                                <dl class="fldList fullWdth">
                                    <dd><table class="table customTbl fldSet statement">
                                            <thead>
                                                <!--<tr>
                                                    <th rowspan="2">Statement</th>
                                                    <th colspan="2">Evidence</th>
                                                    
                                                    
                                                </tr>-->	
                                                <tr>
                                                    <th>Statement</th>
                                                    <th>Evidence at the start of
the action plan</th>
                                                    <th>Evidence Files</th>
                                                    
                                                </tr>
                                            </thead>
                                            
                                            <?php
                                            foreach($details['js_evidences'] as $val){
                                            ?>
                                            <tr>
                                                <td style="text-align: left"><?php echo $val['translation_text'] ?></td>
                                                    <td style="text-align: left"><?php echo $val['evidence_text'] ?></td>
                                                    <td style="text-align: left"><?php 
                                                    if(!empty($val['files'])){
                                                    $files= explode("-@$#@$-", $val['files']);
                                                    
                                                    foreach($files as $keyf=>$valf){
                                                       echo '<div  class="filePrev uploaded  ext-' . diagnosticModel::getFileExt($valf) . '"  title="' . $valf . '"><div class="inner"><a href="' . UPLOAD_URL . '' . $valf . '" target="_blank"> </a></div></div>';
                                                                      
                                                    }
                                                    }
                                                    ?></td>
                                                    
                                                </tr>
                                            <?php
                                                
                                            }
                                            ?>
                                </table></dd>
                                </dl>
                                <dl class="fldList fullWdth">
                                    <dt>Team</dt>
                                    <dd><div class="tableHldr teamsInfoHldr team_action2 noShadow ">
                                        <a href="javascript:void(0)" title="Add New Team Member" class="impactteam team-add"><i class="fa fa-plus"></i></a>
                                        <table class="table customTbl fldSet" id="team2">
                                            <thead>

                                                <tr>
                                                    <th style="width:5%">S.No</th>
                                                    <th>Designation</th>
                                                    <th>Name</th>
                                                    <th style="width:4%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if(count($teamDetails)>0){
                                                $inc=1;    
                                                foreach($teamDetails as $key=>$val){    
                                                echo assessmentModel::getActionTeamHTMLRow ($inc,$val);
                                                $inc++;
                                                }
                                                
                                                }else{
                                                echo assessmentModel::getActionTeamHTMLRow (1);
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        </div>
                                    </dd>
                                </dl>
                                
                                <!--<dl class="fldList">
                                    <dt>Statement</dt>
                                    <dd><div class="inputWrap"><input type="text" class="form-control" readonly value="Name of Statement"></div></dd>
                                </dl>-->
                                <!--<dl class="fldList fullWdth">
                                    <dt>Evidence</dt>
                                    <dd>
                                       <input type="text" class="form-control" value=""> 
                                    </dd>
                                </dl>
                                <dl class="fldList fullWdth">
                                    <dt>Team</dt>
                                    <dd>
                                        <div class="inputWrap row">                                                
                                            <div class="col-sm-3">
                                                <select class="selectpicker">
                                                    <option>Designation 1</option>
                                                    <option>Designation 2</option>
                                                    <option>Designation 3</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <select class="selectpicker">
                                                    <option>Team 1</option>
                                                    <option>Team 2</option>
                                                    <option>Team 3</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" value="">
                                            </div>
                                        </div>
                                    </dd>
                                </dl>-->
                                
                            </div>

                            <h4>Your journey towards improvement</h4>
                            <div id="container" style="min-width: 310px; height: 180px; max-width: 90%; margin: 0 auto;border:1px solid #000000"></div>
                            <!--<div class="timelinebar">
                                <div class="tlPoint start vtip" title="Description of timeline"><label>Start Day</label></div>
                                <div class="tlPoint tenP vtip" title="Description of timeline"><label>Week 1st</label></div>
                                <div class="tlPoint twenP vtip" title="Description of timeline"><label>Week 2nd</label></div>
                                <div class="tlPoint thrdP vtip" title="Description of timeline"><label>Week 3rd</label></div>
                                <div class="tlPoint fourtP vtip" title="Description of timeline"><label>Week 4th</label></div>
                                <div class="tlPoint fiveP vtip" title="Description of timeline"><label>Week 5th</label></div>
                                <div class="tlPoint sixP vtip" title="Description of timeline"><label>Week 6th</label></div>
                                <div class="tlPoint sevenP vtip" title="Description of timeline"><label>Week 7th</label></div>
                                <div class="tlPoint eightP vtip" title="Description of timeline"><label>Week 8th</label></div>
                                <div class="tlPoint nineP vtip" title="Description of timeline"><label>Week 9th</label></div>
                                <div class="tlPoint hundP vtip" title="Description of timeline"><label>End Day</label></div>
                            </div>-->

                            <div style="height: 70px; max-width: 90%; margin: 0 auto;font-weight:bold"><span style="color:rgba(3, 168, 3,1);">C:</span><span>Activities completed</span> &nbsp;&nbsp;<br> <span style="color:rgba(255,140,0,1);">P:</span><span>Activities planned or in progress</span> &nbsp;&nbsp;<br> <span style="color:rgba(255,0,0,1)">Ex:</span><span>Activities expired due to lack of action</span></div>
                            
                            <section class="contSec">
                                <h4 class="clearfix exPndBx" data-toggle="collapse" data-target="#collapseActivities">Activities <i class="fr fa fa-minus-circle" id="collapseAct" data-toggle="collapse" data-target="#collapseActivities"></i></h4>
                                <div class="collapse" id="collapseActivities">
                                    <!--<div class="horrzScrollBox activity_action2">-->
                                    <div class="activity_action2">
                                        <table class="table customTbl fldSet"  id="activityStmnt">
                                            <thead>
                                                <tr>
                                                    <th style="width:3%"></th>
                                                    <th>Who are involved in
the activity?</th>
                                                    <th>Type of Activity</th>
                                                    <th>Activity Details</th>
                                                    <th>Status</th>
                                                    <th>Planned Date</th>
                                                    <th>Date of Completion</th>
                                                    <th>Comments</th>
                                                    <th style="width:4%"></th>
                                                </tr>	
                                            </thead>
                                            <tbody>
                                              <?php
                                                if(count($activityDetails)>0){
                                                $inc=1;    
                                                foreach($activityDetails as $key=>$val){    
                                                echo assessmentModel::getActionActivityHTMLRow ($inc,$val);
                                                $inc++;
                                                }
                                                
                                                }else{
                                                echo assessmentModel::getActionActivityHTMLRow (1);
                                                }
                                              ?>  
                                            </tbody>
                                        </table>
                                        <div><a href="Javascript:void(0);" class="btn btn-pimary activity-add">Add New <i class="fa fa-plus"></i></a></div>
                                    </div>
                                </div>
                            </section>
                            <?php 
                            if(!empty($impactStatements)) {
                                
                                //echo "<pre>";print_r($statementData);
                                $aqsDataModel = new aqsDataModel();
                                $designations=$aqsDataModel->getDesignations();
                                $classes=$aqsDataModel->getSchoolClassList();?>
                                <h4>Monitoring of the progress made</h4>
                              <?php  foreach($impactStatements as $statements) { ?>
                           
                                    <section class="contSec">
                                     <h4 class="clearfix exPndBx"><b style=" color: black;">Goal for <?php echo $statements['designation'];?> </b>- &nbsp;<span><?php echo ucfirst($statements['impact_statement']);?></span><i class="fr fa fa-minus-circle collapse-impact" data-toggle="collapse" data-target="#collapseStatement-<?php echo $statements['assessor_action1_impact_id'];?>"></i></h4>
                                        <div class="collapse" id="collapseStatement-<?php echo $statements['assessor_action1_impact_id'];?>">                                    
                                        <!--<div class="horrzScrollBox impactstatements">-->
                                        <div class="impactstatements">
                                            <table class="table customTbl fldSet " id="impactStmnt-<?php echo $statements['assessor_action1_impact_id'];?>">
                                                <thead>
                                                    <tr>
                                                        <th>S.No</th>
                                                        <th class="date">Date</th>
                                                        <th class="method">Activity Method</th>
                                                        <th class="cmnts">Comments</th>
                                                        <th class="evd">Evidence</th>
                                                        <th></th>
                                                    </tr>	
                                                </thead>
                                                <tbody>
                                                    <?php   
                                                    if(!empty($statementData[$statements['assessor_action1_impact_id']])) {
                                                        
                                                        echo assessmentModel::getActionImpactStmntDataRow(1,$val=array(),$statements['assessor_action1_impact_id'],$designations,$classes,$statementData,$methods); 
                                                    }else{
                                                        echo assessmentModel::getActionImpactStmntHTMLRow(1,$val=array(),$statements['assessor_action1_impact_id'],$designations,$classes,'',$methods);  
                                                    } ?>
                                                    
                                                </tbody>
                                            </table>
                                            <div> <td colspan="5" style="text-align:left;"><a href="Javascript:void(0);" class="btn btn-pimary impact-add" rel="<?php echo $statements['assessor_action1_impact_id'];?>">Add New <i class="fa fa-plus"></i></a></td></div>
                                        </div>
                                    </div>
                                    </section>
                            
                            <?php } 
                            }
                            ?>
                         <div class="ajaxMsg"></div>
                         <?php if(isset($details['action_status']) && $details['action_status']!=2) {  ?>
                            <div class="clearfix">
                                <button class="btn btn-primary saverow fr" data-item-id="0" value="0" id="submitAction2" style=" margin-left: 8px;">Finish Action Planning</button>
                                <button class="btn btn-primary saverow fr" data-item-id="0" value="0" id="saveAction2" >Save</button>
                            </div>
                        <?php  } ?>
                          
                         <input type="hidden" name="from_date" id="from_date" value="<?php echo isset($details['from_date'])?$details['from_date']:''; ?>">
                         <input type="hidden" name="to_date" id="to_date" value="<?php echo isset($details['to_date'])?$details['to_date']:''; ?>">
                        </form>
                    </div>
                </div>

<script type="text/javascript">
            (function ($) {
                //$('.date').datetimepicker({format: 'MM/DD/YYYY', pickTime: false});
                var startDate = new Date("<?php echo date("Y/m/d",strtotime($details['from_date'])); ?>");
                var endDate = new Date("<?php echo date("Y/m/d",strtotime($details['to_date'])); ?>");
                //alert(startDate);
                $('.datePicker').datetimepicker({format: 'DD-MM-YYYY',useCurrent: false,minDate:startDate,maxDate:endDate,pickTime: false});
                $('.collapse').collapse();

                $(document).on("click", "#collapseAct", function () {
        if ($(this).hasClass('fa-minus-circle')) {
        //$(".assessor_show_hide").slideToggle();    
        $(this).removeClass('fa-minus-circle').addClass("fa-plus-circle");
        //$("#up_assessor").hide();
        }else if($(this).hasClass('fa-plus-circle')){
        //$(".assessor_show_hide").slideToggle();      
        $(this).removeClass('fa-plus-circle').addClass("fa-minus-circle");
        //$("#up_assessor").show();
        }
    });        
            })(jQuery);
        </script>
        
        <script>
            $(function () {
     
        var id_c =$("#id_c").val();
        var assessment_id =$("#assessment_id").val();
        var querystring="&id_c="+id_c+"&assessment_id="+assessment_id+"";
        apiCall($("#actionplanform2"), "actionplandata", "token=" + getToken() +""+querystring, function (s, data) {
                
          
            //alert(data.xaxis);
            chartdata.xAxis = data.xaxis;
            chartdata.series =data.data; 
            
            /*chartdata.series = [{
        name: 'Research-C',
        color: 'rgba([0],[128],[0], [0.5])',
        data: [[Date.UTC(2015, 11, 01),0],[Date.UTC(2015, 11, 02),''], [Date.UTC(2015, 11, 03),0], [Date.UTC(2015, 11, 04),''], [Date.UTC(2015, 11, 05),0]],
        marker: {
        symbol: 'triangle'
    }

    }
];*/
            
            $('#container').highcharts(chartdata);

           //var yVis = false;
           //$('#container').highcharts().yAxis[0].update({
            //visible: yVis
           //});
    
           
   
                
            }, function (s, msg) {
                alert(msg);
            });
            
            
     
     

    });
            </script>