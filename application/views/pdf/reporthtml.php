<?php print_r($assessment);echo $group_assessment_id," ";
echo $assessment_id;
if((($assessment_id>0 && $assessment['statusByRole'][4]==1 ) || $group_assessment_id>0  ) && $assessment['report_published']!=1 && $assessment['aqs_status']==1){
?>
<div id="quickLinks">
	<div class="pRel">
		<a href="javascript:void(0)" class="closeIt">&#10005</a>
		<ul>
		<?php
		if($assessment_id>0){
		?>
			<li><a class="editLinks" href="?controller=diagnostic&action=assessmentForm&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessment['userIdByRole'][4]; ?>">Edit External Assessment</a></li>
		<?php if($assessment['statusByRole'][3]==1){ ?>
			<li><a class="editLinks" href="?controller=diagnostic&action=assessmentForm&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessment['userIdByRole'][3]; ?>">Edit Internal Assessment</a></li>
		<?php }
			if($assessment['assessment_type_id']==2 && $assessment['isTchrInfoFilled']==1){
				?>
				<li><a class="editLinks" href="?controller=diagnostic&action=teacherInfoForm&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessment['userIdByRole'][3]; ?>">Edit Teacher Info</a></li>
				<?php
			}
		}
		?>
			<li><a class="editLinks" href="?controller=diagnostic&action=aqsForm&assmntId_or_grpAssmntId=<?php echo $assessment['group_assessment_id']>0?$assessment['group_assessment_id']:$assessment['assessment_id']; ?>&assessment_type_id=<?php echo $assessment['assessment_type_id']; ?>">Edit AQS Information</a></li>
		</ul>
	</div>
</div>
<?php
}
?>	