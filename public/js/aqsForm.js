var selfReview=0;
var collegeReview=0;
var isPaused = false;
var sTeam;
//var isSelfReviewData = function(f,param){
//    return loadDataAQSform(f,param)
//};
var loadDataAQSform = function (f,param){    
    apiCall(f,"loadAQSVersionData",param,function(s,data){			
			var aqs = data.aqs;
                        var countryCode = 91;
			var schoolTeam = data.school_team;
			var additionalRefTeam = data.aqsAdditionalTeam;
			var additional_data = data.additional_data;
			f.find(".team_table tr.team_row:gt(0)").remove();
			f.find(".additionalTeam_table tr.team_row:gt(0)").remove();
			f.not("#aqsf_version_id, input[type='hidden']").val('').removeAttr('selected');
			f.find("input[name$='[not_applicable]']").prop('checked',false);
			f.find("input[name$='[start_time]']").removeAttr('disabled');
			f.find("input[name$='[end_time]']").removeAttr('disabled');
			
			//f.find("input[id='aqsf_school_aqs_pref_start_date']").val('');
			//f.find("input[id='aqsf_school_aqs_pref_end_date']").val('');
			//alert(aqs.aqs_school_registration_num);
			
			f.find("input[type='checkbox']").prop('checked',false);
				for(var key in aqs) {
				  if(aqs.hasOwnProperty(key)) {
				    console.log(key + " -> " + aqs[key]);
					  if(key=='it_support_ids')
						  {						  
						  	for(var chk in aqs[key])
						  		{						  		
							  		var cval = (aqs[key][chk]).trim();						  		
							  		f.find("input[type='checkbox'][name='other[it_support][]'][value='"+cval+"']").prop('checked',true)
						  		}
						  }
                                          else if(key=='school_type_ids')
						  {						  
						  	for(var chk in aqs[key])
						  		{						  		
							  		var cval = (aqs[key][chk]).trim();						  		
							  		f.find("input[type='checkbox'][name='aqs[school_type_id][]'][value='"+cval+"']").prop('checked',true)
						  		}
						  }        
					  else if(key=='school_timing'){	
						  var naTiming = [];
						  for(var key in aqs["school_timing"])
							  {							  	
							    	f.find("input[name='other[timing]["+key+"][start_time]']").val((aqs["school_timing"][key]["start_time"]).substring(0,(aqs["school_timing"][key]["start_time"]).lastIndexOf(':')));
							    	f.find("input[name='other[timing]["+key+"][end_time]']").val((aqs["school_timing"][key]["end_time"]).substring(0,(aqs["school_timing"][key]["end_time"]).lastIndexOf(':')));
							    	f.find("input[name='other[timing]["+key+"][not_applicable]']").prop('checked',false)
							    	naTiming.push(parseInt(key));
							  }
						  for(var j=1; j<=5;j++)
						  {							
							  $.inArray(j,naTiming)<0?f.find("input[name='other[timing]["+j+"][not_applicable]']").prop('checked',true) && f.find("input[name='other[timing]["+j+"][start_time]']").attr('disabled','disabled').val('') && f.find("input[name='other[timing]["+j+"][end_time]']").attr('disabled','disabled').val(''):f.find("input[name='other[timing]["+j+"][start_time]']").removeAttr('disabled') && f.find("input[name='other[timing]["+j+"][end_time]']").removeAttr('disabled')  && f.find("input[name='other[timing]["+j+"][not_applicable]']").prop('checked',false);							 
						  }
					  } else if(key=='principal_phone_no'){	
                                                  var principle_num = aqs["principal_phone_no"];
                                                  principle_num = principle_num.split(")");
                                                  var principle_phone = 0;
                                                  if(principle_num.length > 1){
                                                    var country_code = principle_num[0].split("+");
                                                    countryCode = country_code[1];
                                                    principle_phone = principle_num[1];
                                                    
                                                 }else {
                                                      principle_phone = principle_num[0];
                                                 }
                                                 f.find("input[name='aqs[principal_phone_no]']").val(principle_phone);                                                
                                                 $('#pr_country_code').val(countryCode);
					  }  else if(key=='coordinator_phone_number'){	
                                                  //alert("ok"+aqs["principal_phone_no"]);
                                                  var coordinator_num = aqs["coordinator_phone_number"];
                                                  var coordinator_phone = 0;
                                                  coordinator_num = coordinator_num.split(")");
                                                  if(coordinator_num.length > 1){
                                                    var country_code = coordinator_num[0].split("+");
                                                    coordinator_phone = coordinator_num[1];
                                                     countryCode = country_code[1];
                                                 }else {
                                                      coordinator_phone = coordinator_num[0];	
                                                 }
                                                 f.find("input[name='aqs[coordinator_phone_number]']").val(coordinator_phone);   
                                                    $('#cr_country_code').val(countryCode);
                                                 // 					  
					  } else if(key=='accountant_phone_no'){	
                                                  //alert("ok"+aqs["principal_phone_no"]);
                                                  var accountant_num = aqs["accountant_phone_no"];
                                                  var accountant_phone_no = 0;                                                 
                                                  accountant_num = accountant_num.split(")");
                                                  if(accountant_num.length > 1){
                                                    country_code = accountant_num[0].split("+");
                                                    countryCode = country_code[1];
                                                    accountant_phone_no = accountant_num[1];
                                                   // alert(accountant_phone_no+countryCode);
                                                   
                                                 }else{
                                                        
                                                         accountant_phone_no = accountant_num[0];
                                                }
                                                     //alert(countryCode);
                                                    f.find("input[name='aqs[accountant_phone_no]']").val(accountant_phone_no);   
                                                    $('#ac_country_code').val(countryCode);	
                                                 // 					  
					  }					 
					  else
					  {
                                                  var afieldval = aqs[key];
                                                  afieldval = afieldval?afieldval.replace("'",""):afieldval;
                                                  afieldval = afieldval?afieldval.replace("",""):afieldval;
                                                 // console.log(afieldval);
                                                 if(key=='principal_email'||key=='school_name'||key=='principal_email'||key=='nstatus'||key=='school_aqs_pref_start_date'||key=='school_aqs_pref_end_date')
                                                     continue;
						  f.find("input[type='text']#aqsf_"+key+",select#aqsf_"+key+",input[type='email']#aqsf_"+key).val(aqs[key]);
						  //f.find("input[type='text']#aqsf_additional_"+key+",input[type='email']#aqsf_additional_"+key).val(aqs[key]);
						  f.find("input[type='url']#aqsf_"+key).val(aqs[key]);
						  f.find("input[type='radio'][name='aqs["+key+"]'][value='"+afieldval+"']").prop('checked',true);
                                                  
                                                  if(key=="aqs_school_registration_num") f.find("input[type='text']#aqsf_reg_num").val(aqs[key]);
                                                  if(key=="aqs_school_gst") { 
                                                   // alert(aqs[key]);  
                                                   if(aqs[key]!=undefined && aqs[key] == 0) {
                                                        $(".gst-info").hide();
                                                        f.find("input[type='radio']#aqsf_school_gst_no").prop('checked',true);
                                                        f.find("input[type='radio']#aqsf_school_gst_yes").prop('checked',false);
                                                    }
                                                    else{
                                                        $(".gst-info").show();
                                                        f.find("input[type='radio']#aqsf_school_gst_no").prop('checked',false);
                                                        f.find("input[type='radio']#aqsf_school_gst_yes").prop('checked',true);
                                                        if(key=="aqs_school_gst_num") f.find("input[type='text']#aqsf_gst_num").val(aqs[key]);
                                                     }
                    
                                                    }
                                                  
                                                  if(key=="num_class_rooms") f.find("input[type='text']#aqsf_class_rooms").val(aqs[key]);
                                                  
					  }
					 
				  }
				}
				
				for(var key in additional_data) {
					 if(key=='school_community_id')
					  {						  
					  	for(var chk in additional_data[key])
					  		{						  		
						  		var cval = (additional_data[key][chk]).trim();						  		
						  		f.find("input[type='checkbox'][name='additional[school_community_id][]'][value='"+cval+"']").prop('checked',true)
					  		}
					  }	
					 if(key=='review_medium_instrn_id')
					  {						  
					  	for(var chk in additional_data[key])
					  		{						  		
						  		var cval = (additional_data[key][chk]).trim();						  		
						  		f.find("input[type='checkbox'][name='additional[review_medium_instrn_id][]'][value='"+cval+"']").prop('checked',true)
					  		}
					  }	
					  if(additional_data.hasOwnProperty(key)) {					   							  
							  f.find("input[type='text']#aqsf_additional_"+key+",input[type='email']#aqsf_additional_"+key).val(additional_data[key]);
							  //f.find("input[type='url']#aqsf_"+key).val(aqs[key]);
							  f.find("input[type='radio'][name='additional_data["+key+"]'][value='"+additional_data[key]+"']").prop('checked',true);						 
					  }
					}
				
				
				var k =0;			
				if(schoolTeam.length)
				{
                                 
                                f.find("input#aqsf_schoolTeam_"+k+"_name").val(schoolTeam[k]["name"]);
			    	f.find("select#aqsf_schoolTeam_"+k+"_designation").val(schoolTeam[k]["designation_id"]);
			    	f.find("select#aqsf_schoolTeam_"+k+"_language").val(schoolTeam[k]["lang_id"]);
			    	f.find("input#aqsf_schoolTeam_"+k+"_email").val(schoolTeam[k]["email"]);
                                var schoolTeam_num = schoolTeam[k]["mobile"];
                                 var team_phone = 0;                                 
                                  schoolTeam_num = schoolTeam_num.split(")");
                                  if(schoolTeam_num.length > 1){
                                     var country_code = schoolTeam_num[0].split("+");
                                    team_phone = schoolTeam_num[1];
                                       countryCode = country_code[1];
                                    //$('#ac_country_code').val(country_code[1]);	
                                 }else {
                                     team_phone = schoolTeam_num[0];
                                 }
                                 f.find("input#aqsf_schoolTeam_"+k+"_mobile").val(team_phone);   
                                 f.find("input#aqsf_schoolTeam_"+k+"_c_code").val(countryCode);
			    	//f.find("input#aqsf_schoolTeam_"+k+"_mobile").val(schoolTeam[k]["mobile"]);
			    	if(schoolTeam.length>1)
			    	addteamrows(1,schoolTeam.length,schoolTeam);
				}
				else
					{
					f.find("input#aqsf_schoolTeam_"+k+"_name").val('');
				    	f.find("select#aqsf_schoolTeam_"+k+"_designation").val('');
				    	f.find("select#aqsf_schoolTeam_"+k+"_language").val('');
				    	f.find("input#aqsf_schoolTeam_"+k+"_email").val('');
				    	f.find("input#aqsf_schoolTeam_"+k+"_mobile").val('');
				    	console.log(f.find("team_row").length);
					}
				var ak=0;
				if(additionalRefTeam.length)
				{
					f.find("input#additional_team_name_"+ak).val(additionalRefTeam[ak]["name"]);
			    	f.find("input#additional_team_phone_"+ak).val(additionalRefTeam[ak]["phone"]);
                                 var addtnlTeam_num = additionalRefTeam[ak]["phone"];
                                  var addtnlPhone = 0;             
                                               
                                  addtnlTeam_num = addtnlTeam_num.split(")");
                                  
                                  if(addtnlTeam_num.length > 1){
                                      addtnlPhone = addtnlTeam_num[1];
                                      var country_code = addtnlTeam_num[0].split("+");
                                       countryCode = country_code[1];
                                    
                                }else {
                                     addtnlPhone = addtnlTeam_num[0];
                                }
                                    f.find("input#additional_team_phone_"+ak).val(addtnlPhone);   
                                    f.find("input#additional_team_ccode"+ak).val(countryCode); 
                                    //$('#ac_country_code').val(country_code[1]);	
			    	f.find("input#additional_team_email_"+ak).val(additionalRefTeam[ak]["email"]);
			    	f.find("input#additional_team_role_stake_"+ak).val(additionalRefTeam[ak]["role_stakeholder"]);			    	
			    	if(additionalRefTeam.length>1)
			    		addAdditionalteamrows1(1,additionalRefTeam.length,additionalRefTeam);
				}
				else
				{
					f.find("input#additional_team_name_"+ak).val('');
			    	f.find("input#additional_team_phone_"+ak).val('');
			    	f.find("select#additional_team_email_"+ak).val('');
			    	f.find("input#additional_team_role_stake_"+ak).val('');
				}
				 
			$('.selectpicker').selectpicker('refresh');			
			//if(f.find("input[type='radio']#aqsf_travel_arrangement_for_adhyayan_2").is(":checked"))
				f.find("input[type='radio']#aqsf_travel_arrangement_for_adhyayan_2").trigger('change');
			//if(f.find("input[type='radio']#aqsf_accomodation_arrangement_for_adhyayan_2").is(":checked"))
				f.find("input[type='radio']#aqsf_accomodation_arrangement_for_adhyayan_2").trigger('change');
			if(f.find("select#aqsf_referrer_id").val()==7)
				f.find("#aqsf_row_referred_text").show();
			else
				f.find("#aqsf_row_referred_text").hide();
			
			//f.find("input[id='aqsf_school_aqs_pref_start_date']").val('');
			//f.find("input[id='aqsf_school_aqs_pref_end_date']").val('');
			
			for(var i=2;i<=$('.nav.nav-tabs .item').length;i++)
			{
                            //if(i==4 && selfReview==1)
                               // checkFormTabCompletion($("#aqs-step"+5));
                           // else
                                checkFormTabCompletion($("#aqs-step"+i));
			}                        
		},function(s,msg,data){ 
			//$("body").trigger('aqsDataChanged');			
			return 0;
		},function(s,d){$("body").trigger('aqsDataChanged');},null,false);		
}

var selfReviewPromptUser = function (){
    //if(selfReview==1){    
                            if(confirm("The school information is auto-populated from your previous entry on "+$("#aqsf_version_id option:eq(1)").text()+"\nWould you like to update the data? Click \"OK\" to update the information or click \"Cancel\" to submit the information."));
                                else{
                                    var f1=$("#aqsFormWrapper");
                                    var param1=f1.serialize()+"&submit=1&token="+getToken();
                                    $("#validationErrors").html('');
                                    apiCall(f1,"saveAqsForm",param1,function(s,data){ alert("Information has been saved. Redirecting you to manage my reviews where you can take the review by clicking \"Take Review\" button.");window.location = "?controller=assessment&action=assessment" },function(s,msg,data){ 
                                            if(data!=undefined && data!=null && data.errors!=undefined){
                                                    var fp=false;
                                                    for(var prop in data.errors){
                                                            fp=fp?fp:prop;
                                                            addError("#validationErrors",data.errors[prop],prop);
                                                    }
                                                    var e=$("#aqsf_"+fp);
                                                    if(e.length){
                                                            focusAqsElm(e);
                                                    }
                                            } alert(msg); 
                                    });                                    
                                 }
    //}
}
jQuery(document).ready(function($) {
	disableAQS=true;
	$(".vertScrollArea").mCustomScrollbar({theme:"dark"});
	//$('#aqsFormWrapper .aqs_ph').mask("(+99) 999-9999-999");
	
	$.mask.definitions['~']='[+-]';
	if($("#aqsf_terms_agree").val()==1)
	{		
		disableAQS=false;                                  
	}	
	$('#aqsFormWrapper .time input').mask('99:99',{placeholder:"hh:mm"});
	$('.isEditable .aqsDate').datetimepicker({format:'DD-MM-YYYY',useCurrent: false,pickTime:false})	
	.on('change',function(){		
		$("body").trigger('aqsDataChanged');
	});
	$('#aqsFormWrapper.isEditable .time').datetimepicker({format:'HH:mm',pickDate:false});
	$(document).on('click',"#aqs_acceptTerms",function(){
		disableAQS = false;
		$("#vtip").remove();
                //$("#aqsFormWrapper #saveAqsForm").trigger('click');		
                aqsFormEnable();
		var f=$("#aqsFormWrapper");		
		var param=f.serialize()+"&token="+getToken();
		$("#validationErrors").html('');
		$(this).attr("disabled","disabled");
		apiCall(f,"saveAqsForm",param,function(s,data){
                    alert("Thank you. You can now proceed further."); 
                    if($("#aqsf_version_id option").length>1)
                    {                        
                        //alert("The data will be auto-loaded from the recently filled review");                        		
                        var aqsversion = $("#aqsf_version_id option:eq(1)").val();
                        var astType = f.find("#aqsf_version_asstype").find("[data-id='"+aqsversion+"']").data('assessmentType');
                        var param=f.serialize()+"&token="+getToken()+"&load_assessment_type_id="+astType+"&aqsversion="+aqsversion;                        
                        $("#validationErrors").html('');
                        var e=$("#aqsf_referrer_id");                                               
                        focusAqsElm(e);
                        //dfd.done(loadDataAQSform(f,param),selfReviewPromptUser());
                       // loadDataAQSform(f,param).done(console.log('filled'));                                                                  
                       // dfd.resolve();
                       //doAsync3(f,param,loadDataAQSform);   
                       loadDataAQSform(f,param);
                       selfReviewPromptUser();
                       var vl=$("#aqsf_school_recognised_yes:checked").val();
		       if(vl!=undefined && vl==1){
			$(".reg-info").show().find("input").removeAttr("disabled");
                       }
		       else{
			$(".reg-info").hide().find('input').val('').attr("disabled","disabled");
                       }
                       
                    } 
                },function(s,msg,data){ 			
			if(data!=undefined && data!=null && data.errors!=undefined){
				for(var prop in data.errors)
					addError("#validationErrors",data.errors[prop],prop);
			} alert(msg); 
		});
		$("#aqs_acceptTerms").attr('disabled','disabled');
		$("#aqs_declineTerms").attr('disabled','disabled');
		
		$("#savSbtRrow").show();
		$("#termsRow").remove();
		 $("html, body").animate({ scrollTop: 0 }, 600); 
		 var e=$("#aqsf_referrer_id");
		 focusAqsElm(e);
		checkFormTabCompletion($("#aqs-step1"));
	});
	
	for(var i=2;i<=$('.nav.nav-tabs .item').length;i++)
		{
                    //if(i==4 && selfReview==1)
                       // checkFormTabCompletion($("#aqs-step"+5));
                    //else
                        checkFormTabCompletion($("#aqs-step"+i));
		}
	$(document).on('click',"#aqs_declineTerms",function(){
		disableAQS = true;
		alert("Are you sure that you want to decline terms and conditions?")
		aqsFormDisable();
	});
	if(disableAQS===true)	
		aqsFormDisable();	
	$(document).on("change","#aqsf_version_id",function(){
		var f=$("#aqsFormWrapper");		
		var id = $(this).val();
		var astType = f.find("#aqsf_version_asstype").find("[data-id='"+id+"']").data('assessmentType');
		var param=f.serialize()+"&token="+getToken()+"&load_assessment_type_id="+astType;
		console.log($(this).data('id'))
		$("#validationErrors").html('');
                loadDataAQSform(f,param);
                      var vl=$("#aqsf_school_recognised_yes:checked").val();
		       if(vl!=undefined && vl==1){
			$(".reg-info").show().find("input").removeAttr("disabled");
                       }
		       else{
			$(".reg-info").hide().find('input').val('').attr("disabled","disabled");
                       }
		/*
		apiCall(f,"loadAQSVersionData",param,function(s,data){			
			var aqs = data.aqs;
			var schoolTeam = data.school_team;
			var additionalRefTeam = data.aqsAdditionalTeam;
			var additional_data = data.additional_data;
			f.find(".team_table tr.team_row:gt(0)").remove();
			f.find(".additionalTeam_table tr.team_row:gt(0)").remove();
			f.not("#aqsf_version_id, input[type='hidden']").val('').removeAttr('selected');
			f.find("input[name$='[not_applicable]']").prop('checked',false);
			f.find("input[name$='[start_time]']").removeAttr('disabled');
			f.find("input[name$='[end_time]']").removeAttr('disabled');
			
			f.find("input[id='aqsf_school_aqs_pref_start_date']").val('');
			f.find("input[id='aqsf_school_aqs_pref_end_date']").val('');
			
			
			f.find("input[type='checkbox']").prop('checked',false);
				for(var key in aqs) {
				  if(aqs.hasOwnProperty(key)) {
				    //console.log(key + " -> " + aqs[key]);
					  if(key=='it_support_ids')
						  {						  
						  	for(var chk in aqs[key])
						  		{						  		
							  		var cval = (aqs[key][chk]).trim();						  		
							  		f.find("input[type='checkbox'][name='other[it_support][]'][value='"+cval+"']").prop('checked',true)
						  		}
						  }
					  else if(key=='school_timing'){	
						  var naTiming = [];
						  for(var key in aqs["school_timing"])
							  {							  	
							    	f.find("input[name='other[timing]["+key+"][start_time]']").val((aqs["school_timing"][key]["start_time"]).substring(0,(aqs["school_timing"][key]["start_time"]).lastIndexOf(':')));
							    	f.find("input[name='other[timing]["+key+"][end_time]']").val((aqs["school_timing"][key]["end_time"]).substring(0,(aqs["school_timing"][key]["end_time"]).lastIndexOf(':')));
							    	f.find("input[name='other[timing]["+key+"][not_applicable]']").prop('checked',false)
							    	naTiming.push(parseInt(key));
							  }
						  for(var j=1; j<=5;j++)
						  {							
							  $.inArray(j,naTiming)<0?f.find("input[name='other[timing]["+j+"][not_applicable]']").prop('checked',true) && f.find("input[name='other[timing]["+j+"][start_time]']").attr('disabled','disabled').val('') && f.find("input[name='other[timing]["+j+"][end_time]']").attr('disabled','disabled').val(''):f.find("input[name='other[timing]["+j+"][start_time]']").removeAttr('disabled') && f.find("input[name='other[timing]["+j+"][end_time]']").removeAttr('disabled')  && f.find("input[name='other[timing]["+j+"][not_applicable]']").prop('checked',false);							 
						  }
					  }					 
					  else
					  {
                                                  var afieldval = aqs[key];
                                                  afieldval = afieldval?afieldval.replace("'",""):afieldval;
                                                  afieldval = afieldval?afieldval.replace("",""):afieldval;
                                                 // console.log(afieldval);
                                                 if(key=='principal_email'||key=='school_name'||key=='principal_email'||key=='nstatus')
                                                     continue;
						  f.find("input[type='text']#aqsf_"+key+",select#aqsf_"+key+",input[type='email']#aqsf_"+key).val(aqs[key]);
						  //f.find("input[type='text']#aqsf_additional_"+key+",input[type='email']#aqsf_additional_"+key).val(aqs[key]);
						  f.find("input[type='url']#aqsf_"+key).val(aqs[key]);
						  f.find("input[type='radio'][name='aqs["+key+"]'][value='"+afieldval+"']").prop('checked',true);
					  }
					 
				  }
				}
				
				for(var key in additional_data) {
					 if(key=='school_community_id')
					  {						  
					  	for(var chk in additional_data[key])
					  		{						  		
						  		var cval = (additional_data[key][chk]).trim();						  		
						  		f.find("input[type='checkbox'][name='additional[school_community_id][]'][value='"+cval+"']").prop('checked',true)
					  		}
					  }	
					 if(key=='review_medium_instrn_id')
					  {						  
					  	for(var chk in additional_data[key])
					  		{						  		
						  		var cval = (additional_data[key][chk]).trim();						  		
						  		f.find("input[type='checkbox'][name='additional[review_medium_instrn_id][]'][value='"+cval+"']").prop('checked',true)
					  		}
					  }	
					  if(additional_data.hasOwnProperty(key)) {					   							  
							  f.find("input[type='text']#aqsf_additional_"+key+",input[type='email']#aqsf_additional_"+key).val(additional_data[key]);
							  //f.find("input[type='url']#aqsf_"+key).val(aqs[key]);
							  f.find("input[type='radio'][name='additional_data["+key+"]'][value='"+additional_data[key]+"']").prop('checked',true);						 
					  }
					}
				
				
				var k =0;			
				if(schoolTeam.length)
				{
                                f.find("input#aqsf_schoolTeam_"+k+"_name").val(schoolTeam[k]["name"]);
			    	f.find("input#aqsf_schoolTeam_"+k+"_designation").val(schoolTeam[k]["designation"]);
			    	f.find("select#aqsf_schoolTeam_"+k+"_language").val(schoolTeam[k]["lang_id"]);
			    	f.find("input#aqsf_schoolTeam_"+k+"_email").val(schoolTeam[k]["email"]);
			    	f.find("input#aqsf_schoolTeam_"+k+"_mobile").val(schoolTeam[k]["mobile"]);
			    	if(schoolTeam.length>1)
			    	addteamrows(1,schoolTeam.length,schoolTeam);
				}
				else
					{
					f.find("input#aqsf_schoolTeam_"+k+"_name").val('');
				    	f.find("input#aqsf_schoolTeam_"+k+"_designation").val('');
				    	f.find("select#aqsf_schoolTeam_"+k+"_language").val('');
				    	f.find("input#aqsf_schoolTeam_"+k+"_email").val('');
				    	f.find("input#aqsf_schoolTeam_"+k+"_mobile").val('');
				    	console.log(f.find("team_row").length);
					}
				var ak=0;
				if(additionalRefTeam.length)
				{
					f.find("input#additional_team_name_"+ak).val(additionalRefTeam[ak]["name"]);
			    	f.find("input#additional_team_phone_"+ak).val(additionalRefTeam[ak]["phone"]);
			    	f.find("input#additional_team_email_"+ak).val(additionalRefTeam[ak]["email"]);
			    	f.find("input#additional_team_role_stake_"+ak).val(additionalRefTeam[ak]["role_stakeholder"]);			    	
			    	if(additionalRefTeam.length>1)
			    		addAdditionalteamrows(1,additionalRefTeam.length,additionalRefTeam);
				}
				else
				{
					f.find("input#additional_team_name_"+ak).val('');
			    	f.find("input#additional_team_phone_"+ak).val('');
			    	f.find("select#additional_team_email_"+ak).val('');
			    	f.find("input#additional_team_role_stake_"+ak).val('');
				}
				 
			$('.selectpicker').selectpicker('refresh');			
			//if(f.find("input[type='radio']#aqsf_travel_arrangement_for_adhyayan_2").is(":checked"))
				f.find("input[type='radio']#aqsf_travel_arrangement_for_adhyayan_2").trigger('change');
			//if(f.find("input[type='radio']#aqsf_accomodation_arrangement_for_adhyayan_2").is(":checked"))
				f.find("input[type='radio']#aqsf_accomodation_arrangement_for_adhyayan_2").trigger('change');
			if(f.find("select#aqsf_referrer_id").val()==7)
				f.find("#aqsf_row_referred_text").show();
			else
				f.find("#aqsf_row_referred_text").hide();
			
			f.find("input[id='aqsf_school_aqs_pref_start_date']").val('');
			f.find("input[id='aqsf_school_aqs_pref_end_date']").val('');
			
			for(var i=2;i<=$('.nav.nav-tabs .item').length;i++)
			{
				checkFormTabCompletion($("#aqs-step"+i));
			}
		},function(s,msg,data){ 
			//$("body").trigger('aqsDataChanged');			
			console.log(2)
		},function(s,d){$("body").trigger('aqsDataChanged');console.log(3)});
		*/
	});
	$(document).on("click","#saveAqsForm",function(){
		var f=$("#aqsFormWrapper");
		/*$(f).find("input[type=email]").each(function(i,v){
			var vl=$(v).val();
			if(vl.length>0 && !isValidEmail(vl)){
				alert('Invalid email');
				focusAqsElm(v);
				return false;
			}
		});*/
               /* var num_class = $("#aqsf_class_rooms").val();
                var distance_airport = $("#aqsf_airport_distance").val();
                var distance_rail_station = $("#aqsf_rail_station_distance").val();
                var travel_arrangement_by = $("input[name='aqs[travel_arrangement_for_adhyayan]']:checked").val();
                $("#validationErrors").html('');
                if(! (/^\d+$/.test(num_class))) {
                    addError("#validationErrors",'Total no. of classrooms : Enter only numbers','undefined');
                    $( "#aqsf_class_rooms" ).focus();
                    $( "#aqsf_class_rooms" ).addClass('errorRed');
                    //$("#validationErrors").html("Total no. of classrooms : Enter only numbers");
                    return false;
                }
                if (travel_arrangement_by == '2') {
                    
                    if (distance_airport != '' && !(/^\d.+$/.test(distance_airport))) {
                        //alert("oj");
                        addError("#validationErrors", 'Airport distance from school  : Enter only numbers', 'undefined');
                        $("#aqsf_airport_distance").focus();
                        $("#aqsf_airport_distance").addClass('errorRed');
                        //$("#validationErrors").html("Total no. of classrooms : Enter only numbers");
                        return false;
                    } else if (distance_rail_station!='' && !(/^\d.+$/.test(distance_rail_station))) {
                        //alert("oj");
                        addError("#validationErrors", 'Railway Station distance from school : Enter only numbers', 'undefined');
                        $("#aqsf_rail_station_distance").focus();
                        $("#aqsf_rail_station_distance").addClass('errorRed');
                        //$("#validationErrors").html("Total no. of classrooms : Enter only numbers");
                        return false;
                    }
                }*/
		var param=f.serialize()+"&token="+getToken();		
		$(this).attr("disabled","disabled");
		apiCall(f,"saveAqsForm",param,function(s,data){                 
                $("#validationErrors").html("");
                
                },function(s,msg,data){ 
			$("body").trigger('aqsDataChanged');
			if(data!=undefined && data!=null && data.errors!=undefined){
                             $("#validationErrors").html("");
				for(var prop in data.errors)
					addError("#validationErrors",data.errors[prop],prop);
			} 
                        alert(msg); 
		},function(s,d){$("body").trigger('aqsDataChanged');});
	});
        
        $(document).on("change","#aqsf_no_of_buildings",function(){ 
        
                        var num_buildings = $("#aqsf_no_of_buildings").val();
                        if(num_buildings == 1) {
                            $("#distance_main").hide();
                        } else {
                            $("#distance_main").show();
                        }
        
        });
	
	$(document).on("click","#submitAqsForm",function(){
		if(confirm('Are you sure you want to submit this?')){
			var f=$("#aqsFormWrapper");
			var param=f.serialize()+"&submit=1&token="+getToken();
			$("#validationErrors").html('');
			apiCall(f,"saveAqsForm",param,function(s,data){ alert("Successfully submitted"); window.location.reload(); },function(s,msg,data){ 
				if(data!=undefined && data!=null && data.errors!=undefined){
					var fp=false;
					for(var prop in data.errors){
						fp=fp?fp:prop;
						addError("#validationErrors",data.errors[prop],prop);
					}
					var e=$("#aqsf_"+fp);
					if(e.length){
						focusAqsElm(e);
					}
				} alert(msg); 
			});
		}
	});
	
	$(document).on("aqsDataChanged","body",function(){
		$("#saveAqsForm").removeAttr("disabled");
	});
		
	
	$(document).on("change","#aqsFormWrapper select,#aqsFormWrapper input[type=text],#aqsFormWrapper input[type=checkbox],#aqsFormWrapper input[type=radio]",function(){
		$("body").trigger('aqsDataChanged');
	});
	
	$(document).on("change","#aqsFormWrapper input[type=email]",function(){
		var vl=$(this).val();
		if(vl.length>0 && !isValidEmail(vl)){
			$(this).val('');
		}
		$("body").trigger('aqsDataChanged');
	});
	
	$(document).on("change","#aqsFormWrapper input.number",function(){
		var vl=$(this).val();
		if(vl.length>0 && !(vl>0) && vl!=0){
			$(this).val('');
		}
	});
	$(document).on("change","#aqsf_referrer_id",function(){		
		$("#aqsf_referrer_id").val()==7?$("#aqsf_row_referred_text").show():$("#aqsf_row_referred_text").hide();
		$("#aqsf_referrer_text").val('');
	});
	$(document).on("change","#aqsFormWrapper input[type=url]",function(){
		var vl=$(this).val();
		if(vl.length>0 && !isValidUrl(vl)){
			$(this).val('');
		}
		$("body").trigger('aqsDataChanged');
	});
	
	$(document).on("change","#aqsFormWrapper .bName_same",function(){
		if($(this).is(":checked"))
			$("#aqsf_billing_name").val('').attr("disabled","disabled");
		else
			$("#aqsf_billing_name").removeAttr("disabled");
	});
	$(document).on("change","#aqsFormWrapper .bAddress_same",function(){
		if($(this).is(":checked"))
			$("#aqsf_billing_address").val('').attr("disabled","disabled");
		else
			$("#aqsf_billing_address").removeAttr("disabled");
	});
	$(document).on("change","#aqsFormWrapper .TTNotApplicable",function(){
		if($(this).is(":checked"))
			$(this).parents('.schSecHdr').first().removeClass('enabled').addClass('disabled').find('input[type=text]').val('').attr("disabled","disabled");
		else
			$(this).parents('.schSecHdr').first().removeClass('disabled').addClass('enabled').find('input[type=text]').removeAttr("disabled");
	});
	$(document).on("change","#aqsFormWrapper .time input",function(){
		var a=$(this).val().split(":");
		if(a.length!=2 || a[0]>23 || a[1]>59)
			$(this).val('');
	});
	$(document).on("change","#aqsFormWrapper .travel_arrang",function(){
		var vl=$(".travel_arrang:checked").val();
		if(vl!=undefined && vl==2)
			$(".travel-arr-info").show().find("input").removeAttr("disabled");
		else
			$(".travel-arr-info").hide().find('input').val('').attr("disabled","disabled");
	});
	$(document).on("change","#aqsFormWrapper .accom_arrang",function(){
		var vl=$(".accom_arrang:checked").val();
		if(vl!=undefined && vl==2)
			$(".hotel-arr-info").show().find("input").removeAttr("disabled");
		else
			$(".hotel-arr-info").hide().find('input').val('').attr("disabled","disabled");
	});
        $(document).on("change","#aqsFormWrapper .aqsf_school_recognised",function(){
		var vl=$("#aqsf_school_recognised_yes:checked").val();
		if(vl!=undefined && vl==1)
			$(".reg-info").show().find("input").removeAttr("disabled");
		else
			$(".reg-info").hide().find('input').val('').attr("disabled","disabled");
	});
        $(document).on("change","#aqsFormWrapper .aqsf_school_gst",function(){
		var vl=$("#aqsf_school_gst_yes:checked").val();
		if(vl!=undefined && vl==1)
			$(".gst-info").show().find("input").removeAttr("disabled");
		else
			$(".gst-info").hide().find('input').val('').attr("disabled","disabled");
	});
        
        $( document ).ready(function() {
                var vl=$("#aqsf_school_recognised_yes:checked").val();
                var gst=$("#aqsf_school_gst_yes:checked").val();
		if(vl!=undefined && vl==1)
			$(".reg-info").show().find("input").removeAttr("disabled");
		else
			$(".reg-info").hide().find('input').val('').attr("disabled","disabled");
                    
                if(gst!=undefined && gst==1)
			$(".gst-info").show().find("input").removeAttr("disabled");
                    
		else
			$(".gst-info").hide().find('input').val('').attr("disabled","disabled");
        });
       
	$(document).on("change","#aqsFormWrapper .aqsDate input",function(){		
		var a=$(this).val().split("-");
		if(a.length!=3 || a[1]>12 || a[0]>31 || a[2]<2000)
			$(this).val('');
		$("body").trigger('aqsDataChanged');
	});
	$(document).on("click",".alert .hasKey",function(){
		var e=$("#aqsf_"+$(this).data('id'));
		if(e.length){
			focusAqsElm(e);
		}
	});
	$(document).on("change","select, input",function(){		
		//var p=$(this).closest(".tab-pane").first();			
		checkFormTabCompletion(this)		
		//	$("a[href=#"+p.attr("id")+"]").parent().addClass("completed");
	});
	$(document).on("click","#aqsf_school_aqs_pref_start_date,#aqsf_school_aqs_pref_end_date",function(){				
		$(this).trigger('change')		
	});
});

function focusAqsElm(e){
	var f=$("#aqsFormWrapper");
	f.find(".nav-tabs li.item.active,.tab-pane.active").removeClass("active");
	f.find("li.item a[href=#"+$(e).parents(".tab-pane").first().addClass("fade in active").attr("id")+"]").parent().addClass("active");
	$(e).focus();
}


function aqsFormDisable()
{
	
	var f = $("#aqsFormWrapper");		//
	f.find("#aqsf_version_id").attr('disabled','disabled').css("pointer-events","none");
	f.find(".subTabWorkspace .tab-pane:gt(0) *,.ylwRibbonHldr .tabitemsHldr .nav .item:gt(0) *").each(function(){
		$(this).attr('disabled','disabled');
		$(this).css('pointer-events','none');		
	});
	$("#aqsf_terms_agree").val(0);	
        
}	
function aqsFormEnable(){	
	var f = $("#aqsFormWrapper");		//
	f.find("#aqsf_version_id").removeAttr('disabled').css("pointer-events","none");
	f.find(".subTabWorkspace .tab-pane:gt(0) *,.ylwRibbonHldr .tabitemsHldr .nav .item:gt(0) *").each(function(){
		$(this).removeAttr('disabled');
		$(this).css('pointer-events','auto');
	});
	$('.selectpicker').selectpicker('refresh');
	f.find("input[name=nstatus]").attr('disabled','disabled');
	f.find("#aqsf_principal_email").attr('disabled','disabled');
	f.find("#aqsf_terms_agree").val(1);
}
function checkFormTabCompletion(sender){
    
	//var hierarchy={kpa:"keyQ",keyQ:"coreQ"};
	var p=$(sender).closest(".tab-pane").first();	
	var tabId = p.attr('id');
	console.log(tabId)
	var numOfFields = 0;
	var emptyFields = 0;
	$("a[href=#"+p.attr("id")+"]").parent().removeClass("completed");
	var optionalFields = ['aqs[referrer_id]','aqs[coordinator_name]','aqs[coordinator_phone_number]','aqs[coordinator_email]','aqs[school_website]','aqs[school_email]','aqs[school_website]',
            'aqs[aqs_school_registration_num]','aqs[aqs_school_gst_num]','aqs[airport_name]','aqs[rail_station_distance]','aqs[airport_distance]','aqs[rail_station_name]','aqs[hotel_name]',
            'aqs[hotel_school_distance]','aqs[hotel_station_distance]','aqs[hotel_airport_distance]','aqs[no_of_gates]','aqs[no_of_buildings]',
        'aqs[distance_main_building]','aqs[accountant_email]','aqs[accountant_name]','aqs[accountant_phone_no]','aqs[billing_name]',
    'aqs[billing_address]','aqs[accomodation_arrangement_for_adhyayan]','aqs[travel_arrangement_for_adhyayan]'];
	var travelArrangementFields = ['aqs[airport_name]','aqs[rail_station_distance]','aqs[airport_distance]','aqs[rail_station_name]'];
	var hotelArrangementFields = ['aqs[hotel_name]','aqs[hotel_school_distance]','aqs[hotel_station_distance]','aqs[hotel_airport_distance]'];
	if(p.length)
		{
			p.find("select,input[type='text'],input[type='email']").not('[name^="other[timing]"]').each(function(){	
				
				if($(this).val().trim()==''||$(this).val().trim()==null)
					{
                                               var check_blank="Yes";
                                                if($(this).attr('class')=="tableTxtFld aqs_ph"){
                                                var id_current;
                                                id_current=$(this).attr('id').split('_');
                                                id_current=id_current[2];
                                                if($("#aqsf_schoolTeam_"+id_current+"_designation").val()=="7"){
                                                 //check_blank="No";
                                                 check_blank="Yes";
                                                }
                                                }
						if(($(this).attr('name')=='aqs[billing_name]' && $("[name='other[bName_same]']").is(":checked"))||($(this).attr('name')=='aqs[billing_address]' && $("[name='other[bAddress_same]']").is(":checked"))||($(this).attr('name')=='aqs[no_of_buildings]' && $(this).val()=='1' && $('[name="aqs[distance_main_building]"]').val()=='' )||($(this).attr('name')=='aqs[distance_main_building]' && $(this).val()=='' && $('[name="aqs[no_of_buildings]"]').val()=='1'  ));
						else if(($(this).attr('name')=='aqs[referrer_text]' && $("#aqsf_referrer_id").val()!=7)||($.inArray($(this).attr('name'),optionalFields)>=0));	
                                                //else if( $("input[name='aqs[aqs_school_recognised]']:checked").val()==2);
						else if(check_blank=="No"){
                                                    
                                                }
                                                else
						{	
							emptyFields++;
							return;
						}					
					}				
				});
                                
                       
			if(tabId=='aqs-step3' && selfReview==0 && collegeReview==0)
			{
			/*var numStartTime = p.find("input[name^='other[timing]'][name$='start_time]']").length;
			var numEndTime = p.find("input[name^='other[timing]'][name$='end_time]']").length;
			var numEmptyStartTime = 0;//p.find("input[name^='other[timing]'][name$='start_time]'][value='00:00:00']").length;
			var numEmptyEndTime = 0;//p.find("input[name^='other[timing]'][name$='end_time]'][value='00:00:00']").length;
			var i =1;
			var j =1;
			var timeValSt,timeValEnd;
			
			for(i=1;i<=numStartTime;i++)
				{
					timeValSt = p.find("input[name='other[timing]["+i+"][start_time]']").val();					
					timeValEnd = p.find("input[name='other[timing]["+i+"][end_time]']").val();
					timeCheck = p.find("input[name='other[timing]["+i+"][not_applicable]']").is(":checked");
					
					if(!(timeCheck) && ((timeValSt=="00:00"||timeValSt=="") || (timeValEnd=="00:00"||timeValEnd=="")))
					{
						emptyFields++;
						break;
					}
					
				}
				p.find("input[name^='other[timing]'][name$='start_time]']").each(function(){
					if($(this).val()=="00:00"||$(this).val()==""||$(this).val()==null)
						numEmptyStartTime++;
				});
				p.find("input[name^='other[timing]'][name$='end_time]']").each(function(){
					if($(this).val()=="00:00"||$(this).val()==""||$(this).val()==null)
						numEmptyEndTime++;
				});
			
				if(numStartTime==numEmptyStartTime||numEndTime==numEmptyEndTime)
				{
				emptyFields++;
				return;
				}*/
			}
                        //alert($("input[name='aqs[aqs_school_recognised]']:checked").val());
                       
        
        
                        
			p.find(".radioRow").each(function(){				
					var radioBtn = $(this).find("input[type='radio']").first().attr('name');
                                        
                                        if(radioBtn != 'aqs[accomodation_arrangement_for_adhyayan]' && radioBtn != 'aqs[travel_arrangement_for_adhyayan]' ){
					if(!$('[name="'+radioBtn+'"]:checked').length)
					{
						emptyFields++;
						return;
					}/*else if(radioBtn == 'aqs[travel_arrangement_for_adhyayan]' && $('[name="'+radioBtn+'"]:checked').val()==2)  {
                                             jQuery.each(travelArrangementFields, function(index, item) {
                                                 if($('input[name="'+item+'"]').val().trim() == '' || $('input[name="'+item+'"]').val().trim() == 'undefined' || $('input[name="'+item+'"]').val().trim() == null) {
                                                     //emptyFields++;
                                                     return;
                                                 }
                                            });                                             
                                        }else if(radioBtn == 'aqs[accomodation_arrangement_for_adhyayan]' && $('[name="'+radioBtn+'"]:checked').val()==2)  {
                                             jQuery.each(hotelArrangementFields, function(index, item) {
                                                 if($('input[name="'+item+'"]').val().trim() == '' || $('input[name="'+item+'"]').val().trim() == 'undefined' || $('input[name="'+item+'"]').val().trim() == null) {
                                                     //emptyFields++;
                                                     return;
                                                 }
                                            });                                             
                                        }*/else if(radioBtn == 'aqs[aqs_school_recognised]' && $('[name="'+radioBtn+'"]:checked').val()==1)  {
                                             //jQuery.each(travelArrangementFields, function(index, item) {
                                                 if($('input[name="aqs[aqs_school_registration_num]"]').val().trim() == '' || $('input[name="aqs[aqs_school_registration_num]"]').val().trim() == 'undefined' || $('input[name="aqs[aqs_school_registration_num]"]').val().trim() == null) {
                                                     emptyFields++;
                                                     return;
                                            }
                                            //});                                             
                                        }else if(radioBtn == 'aqs[aqs_school_gst]' && $('[name="'+radioBtn+'"]:checked').val()==1)  {
                                             //jQuery.each(travelArrangementFields, function(index, item) {
                                                 if($('input[name="aqs[aqs_school_gst_num]"]').val().trim() == '' || $('input[name="aqs[aqs_school_gst_num]"]').val().trim() == 'undefined' || $('input[name="aqs[aqs_school_gst_num]"]').val().trim() == null) {
                                                     emptyFields++;
                                                     return;
                                                 }
                                            //});                                             
                                        }
                                    }
                                        
			});                                                                         

                        if($("input[name='aqs[aqs_school_recognised]']:checked").val() == 1) {
                            
                            if($('#aqsf_reg_num').val()=="undefined") {
                                
                                emptyFields++;
                                return;
                            }
                        }
                        if($("input[name='aqs[aqs_school_gst]']:checked").val() == 1) {
                            
                            if($('#aqsf_gst_num').val()=="undefined") {
                                
                                emptyFields++;
                                return;
                            }
                        }
                              
                       /* if($("input[name='aqs[num_class_rooms]']").val() == '' ) {
                            
                            alert($("input[name='aqs[num_class_rooms]']").val());
                                emptyFields++;
                                return;
                        }*/
                         
			p.find(".checkboxRow").each(function(){				
					var checkBox = $(this).find("input[type='checkbox']").first().attr('name');				
					if(!$('[name="'+checkBox+'"]:checked').length)
					{
						emptyFields++;
						return;
					}				
			});
                         var srNo = 0;
                       p.find(".team_row").each(function() {
                           //alert("ok");
                           // var emailFieldId = $(this).find("input[type='email']").attr('id');
                            var mobileFieldId = "aqsf_schoolTeam_"+srNo+"_mobile";
                            var emailFieldId = "aqsf_schoolTeam_"+srNo+"_email";
                            if( $("#"+emailFieldId).val() == '') {
                               // alert("oooo");
                                emptyFields--;
                            }if($("#"+mobileFieldId).val() == ''){
                                
                                 emptyFields--;
                            }
                            
                           srNo++;
                        });
                        //alert(emptyFields);
		}
                
	emptyFields<=0?$("a[href=#"+p.attr("id")+"]").parent().addClass("completed"):$("a[href=#"+p.attr("id")+"]").parent().removeClass("completed");
	
}
function addteamrows(k,numOfRows,schoolTeam){	
	sTeam=schoolTeam
	var f=$("#aqsFormWrapper");
	var addBtn = f.find(".team_table.school_team>a");	
	var a=$(this).data('attach');	
	apiCall(addBtn,"addTeamRow","sn="+(k+1)+"&type="+addBtn.data('type')+"&token="+getToken()+"&attach="+(a==undefined?'':a),function(s,data){			
		$(s).parents(".team_table").first().find(".team_row").last().after(data.content);
		//$(s).parents(".team_table").first().find(".team_row").last().find(".aqs_ph").mask("(+99) 999-9999-999");
		$(s).parents(".team_table").first().find(".team_row").last().find('.selectpicker').selectpicker();
		$(s).parents(".team_table").first().find(".team_row").last().find('.datePicker').datetimepicker({format:'MM/DD/YYYY',pickTime:false});
		$(s).parents(".team_table").first().find(".team_row").last().find('.date-Picker').datetimepicker({format:'MM-DD-YYYY',pickTime:false});
		f.find("input#aqsf_schoolTeam_"+k+"_name").val(sTeam[k]["name"]);
    	//alert(sTeam[k]["designation_id"]);
        f.find("select#aqsf_schoolTeam_"+k+"_designation").val(sTeam[k]["designation_id"]);
    	f.find("select#aqsf_schoolTeam_"+k+"_language").val(sTeam[k]["lang_id"]);
    	f.find("input#aqsf_schoolTeam_"+k+"_email").val(sTeam[k]["email"]);
    	f.find("input#aqsf_schoolTeam_"+k+"_mobile").val(sTeam[k]["mobile"]);
    	$('.selectpicker').selectpicker('refresh');	
		 k++;
			if(k<numOfRows)
			{				
				addteamrows(k,numOfRows,sTeam);
			}
			
	},function(s,msg){ alert(msg); });
	
}
function addAdditionalteamrows(k,numOfRows,team){
	var sTeamtemp=team;
	var f=$("#aqsFormWrapper");
	var addBtn = f.find(".additionalTeam_table>a");	
	var a=$(this).data('attach');					
		apiCall(addBtn,"addAdditionalRefTeamRow","sn="+(k+1)+"&type="+addBtn.data('type')+"&token="+getToken()+"&attach="+(a==undefined?'':a),function(s,data){
			//console.log(data)
		$(s).parents(".additionalTeam_table").first().find(".team_row").last().after(data.content);
                //$(s).parents(".team_table").first().find(".team_row").last().find(".aqs_ph").mask("(+99) 999-9999-999");
		$(s).parents(".team_table").first().find(".team_row").last().find('.selectpicker').selectpicker();
		$(s).parents(".team_table").first().find(".team_row").last().find('.datePicker').datetimepicker({format:'MM/DD/YYYY',pickTime:false});
		$(s).parents(".team_table").first().find(".team_row").last().find('.date-Picker').datetimepicker({format:'MM-DD-YYYY',pickTime:false});
		
		//$(s).parents(".additionalTeam_table").first().find(".team_row").last().find(".aqs_ph").mask("(+99) 999-9999-999");		
		f.find("input#additional_team_name_"+k).val(sTeamtemp[k]["name"]);
    	f.find("input#additional_team_phone_"+k).val(sTeamtemp[k]["phone"]);
    	f.find("input#additional_team_email_"+k).val(sTeamtemp[k]["email"]);
    	f.find("input#additional_team_role_stake_"+k).val(sTeamtemp[k]["role_stakeholder"]);    	    		
		 k++;
			if(k<numOfRows)
			{				
				addAdditionalteamrows(k,numOfRows,sTeamtemp);
			}
			
	},function(s,msg){ alert(msg); });
	
}