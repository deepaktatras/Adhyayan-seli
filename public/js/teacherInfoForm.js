$(document).ready(function(){
	//$(".aqs_ph").mask("(+99) 999-9999-999");
	var isFilled=1;
	$("#teacherInfoForm .required").each(function(i,e){
		if($(e).val().length==0){
			isFilled=0;
		}
	});
	if(isFilled){
		$("#submitTchrInfoForm").removeAttr("disabled");
	}
	$(document).on("click",".addDynamicRow",function(){
		var p=$(this).parents("dd.posRel").first();
		p.append('<div class="row pt20">'+p.find(".row").first().html()).find(".row").last().find('input').val('').after('<a class="removeDynamicRow"><i class="fa fa-times"></i></div>');
	});
	$(document).on("click",".removeDynamicRow",function(){
		$(this).parents(".row").first().remove();
		$("body").trigger('tchrInfoChanged');
	});
	$(document).on("tchrInfoChanged","body",function(){
		$("#saveTchrInfoForm").removeAttr("disabled");
		$('#submitTchrInfoForm').attr("disabled","disabled");
	});
	$(document).on("change","#teacherInfoForm .boxBody input,#teacherInfoForm .boxBody select",function(){ $("body").trigger('tchrInfoChanged'); });
	$("#teacherInfoForm").submit(function(){return false;});
	$(document).on("click","#saveTchrInfoForm",function(){
		$(this).attr("disabled","disabled");
		var f=$("#teacherInfoForm");
		var param=f.serialize()+"&token="+getToken();
		apiCall(f,"saveTchrInfo",param,function(s,data){ if(data.enableSubmit){$('#submitTchrInfoForm').removeAttr("disabled");}else{$('#submitTchrInfoForm').attr("disabled","disabled");} },function(s,msg){ $("body").trigger('tchrInfoChanged'); alert(msg); },function(s,d){$("body").trigger('tchrInfoChanged');});
	});
	$(document).on("click","#submitTchrInfoForm",function(){
		var f=$("#teacherInfoForm");
		var param=f.serialize()+"&submit=1&token="+getToken();
		apiCall(f,"saveTchrInfo",param,function(s,data){ if(data.submitted){$('#submitTchrInfoForm,#saveTchrInfoForm').remove();alert(data.message);window.location.reload();} },function(s,msg){  alert(msg); });
	});
});