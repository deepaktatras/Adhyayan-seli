<?php 
if($report_id!=3 && $report_id!=4){ ?><a target='_blank' style="color: #ccc;float:right;padding-right:10px;text-decoration:underline;position:fixed;margin-top:20px;margin-left:10px;" href='?controller=pdf&action=pdf&assessment_id=<?php echo $assessment_id; ?>&report_id=<?php echo $report_id; ?>&group_assessment_id=<?php $group_assessment_id; ?>&diagnostic_id=<?php echo $diagnostic_id; ?>&client_id=<?php echo $client_id; ?>&years=<?php echo $pYears; ?>&months=<?php echo $pMonths; ?>&lang_id=<?php echo $lang_id; ?>'>Click to get PDF</a><?php } ?>

<?php //print_r($assessment);
//print_r($user);
if(( ($assessment['assessment_type_id']==1 && $assessment_id>0 && $assessment['statusByRole'][3]==1 && $assessment['subAssessmentType']==1)||($assessment_id>0 && ($assessment['statusByRole'][4]==1 && in_array('edit_all_submitted_assessments',$user['capabilities'])||($assessment['statusByRole'][4]!=1 && $assessment['userIdByRole'][4]==$user['user_id']))) || $group_assessment_id>0  ) && $assessment['report_published']!=1 && $assessment['aqs_status']==1 &&  ($assessment['assessment_type_id']==1 && $assessment['subAssessmentType']!=1)){
?>
<div id="quickLinks">
	<div class="pRel">
		<a href="javascript:void(0)" class="closeIt">&#10005</a>
		<ul>
		<?php
		if($assessment_id>0 ){	
			if( (in_array('edit_all_submitted_assessments',$user['capabilities']) || ($assessment['userIdByRole'][4]==$user['user_id'] && $assessment['statusByRole'][4]!=1))){
			?>
			<li><a class="editLinks" href="?controller=diagnostic&action=assessmentForm&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessment['userIdByRole'][4]; ?>">Edit External Assessment</a></li>
		<?php 		
			}
		if( (in_array('edit_all_submitted_assessments',$user['capabilities']) || $assessment['userIdByRole'][3]==$user['user_id']) && $assessment['statusByRole'][3]==1){ ?>
			<li><a class="editLinks" href="?controller=diagnostic&action=assessmentForm&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessment['userIdByRole'][3]; ?>">Edit Internal Assessment</a></li>
		<?php }
			if($assessment['assessment_type_id']==2 && $assessment['isTchrInfoFilled']==1){
				?>
				<li><a class="editLinks" href="?controller=diagnostic&action=teacherInfoForm&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessment['userIdByRole'][3]; ?>">Edit Teacher Info</a></li>
				<?php
			}
		}
		if( in_array('edit_all_submitted_assessments',$user['capabilities'])){
		?>
			<li><a class="editLinks" href="?controller=diagnostic&action=aqsForm&assmntId_or_grpAssmntId=<?php echo $assessment['group_assessment_id']>0?$assessment['group_assessment_id']:$assessment['assessment_id']; ?>&assessment_type_id=<?php echo $assessment['assessment_type_id']; ?>">Edit AQS Information</a></li>
		</ul>
		<?php 
		}
		?>
	</div>
</div>
<?php
}
?>
	<div id="total_wrapper">
		<div id="reportContainer"></div>
	</div>
	<div id="ajaxLoading"></div>
	<script type="text/javascript">
		$(document).ready(function(){
			if($('#quickLinks').length){
				$('body').addClass("haveQuickLinks");
			}
			$(document).on("click","#quickLinks .closeIt",function(){$('body').removeClass("haveQuickLinks"); return false;});
			$(document).on("click","#quickLinks .editLinks",function(){ 
				if(window.opener!=undefined && window.opener!=null && window.opener.location!=undefined && window.opener.location.href!=undefined){
					window.opener.location.href=$(this).attr("href");
					window.opener.focus(); 
					window.close();
					return false;
				}
			});
                        
			apiCall(document,"getReportRound2Data",{"token":getToken(),"report_id":'<?php echo $_GET['report_id']; ?>',"assessment_id":'<?php echo $assessment_id; ?>','group_assessment_id':'<?php echo $group_assessment_id; ?>','diagnostic_id':'<?php echo $diagnostic_id; ?>','years':'<?php echo empty($_GET['years'])?0:$_GET['years']; ?>','months':'<?php echo empty($_GET['months'])?0:$_GET['months']; ?>','lang_id':'<?php echo $lang_id;?>'},function(s,response){
				if(response.reportData==undefined || response.reportData==null){
					alert("Report data missing.");
				}else{  
					$.reportround2Class(response.reportData.config,response.reportData.data);
				}
			},function(s,msg){alert(msg);});
		});
	</script>