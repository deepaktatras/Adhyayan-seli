

$(function() {
  //do something
  $(".ribWrap").hide();
  $(".date-show").hide();
  $(".activity_comments-show").hide();
});
var allowedExt1=["jpeg","png","gif","jpg","avi","mp4","mov","doc","docx","txt","xls","xlsx","pdf","cvs",'xml','pptx','ppt','cdr','mp3','wav'];
	var uploadQueue=[];
        var isFileUploading=false;
        var uploadBtnId = '';
jQuery(document).ready(function($) {
$('body').on("change",'.uploadImpactStmntBtn',function(){
    // alert("ok");
         uploadBtnId = $(this).attr('id');
         
                if(uploadBtnId=='profile_resume'){
                    var allowedExt = ["doc","docx","txt"];
                }else{
                     var allowedExt = allowedExt1;
                }
               
		if($(this)[0].files==undefined || typeof FileReader === "undefined"){
			alert("Sorry your browser does not support HTML5 file uploading");
		}else{
			var files = $(this)[0].files;
			m=files;
			var fWrap=$(this).parents(".judgementS").find(".filesWrapper");
			for (var i = 0; i < files.length; i++) {
				var nArr=files[i].name.split(".");
				var ext=nArr.pop().toLowerCase();
				if(files[i].size>1048576000){
					alert("File too big. Max. 100MB allowed");
				}else if (nArr.length>0 && allowedExt.indexOf(ext)!=-1) {
					fWrap.append('<div class="filePrev waiting"><span class="delete fa"></span><div class="inner"></div></div>');
					uploadQueue.push({file:files[i],elem:fWrap.find(".filePrev").last(),ext:ext});
				} else {
					alert(files[i].name+" : file type not allowed. Only "+allowedExt.join(", ")+" type of files are allowed");					
				}
			}
			if(isFileUploading==false && uploadQueue.length>0){
				uploadFile(uploadBtnId);
                                //checkFormTabCompletion(this)
			}
		}
		return false;
});

$(document).on("click", ".collapse-impact", function () {
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

$(document).on("click",".filePrev.uploaded .delete",function(){
		/*var f=$(this).parents(".filePrev");
		apiCall(f,"deleteFile",{"token":getToken(),"file_id":f.data("id")},function(s,data){f.remove();},function(s,msg){ alert(msg); });*/
		$(this).parent().remove();
               $("p").remove("#vtip");
	});
$(document).on("click", ".team_action .impactteam", function () {
        //var frm = $('.impactteam').parents().find('form').first().attr('id');
        var frm = $('.impactteam').closest('form').first().attr('id');
        //alert(frm);
        var id_c=$(this).data("id");
        //alert($(this).parents(".team_action").first().find(".teamrow").length);
        //console.log(removeIds)
        apiCall(this, "addImpactTeam", "sn=" + ($(this).parents(".team_action").first().find(".teamrow").length + 1) + "&frm="+frm+"&id_c="+id_c+"&token=" + getToken(), function (s, data) {
            //alert(data.content);
            //alert($(s).parents(".team_action").first().find(".team_row").last().after(data.content));
            //$("#team_30618").find(".team_row").last().append(data.content);
            //$.when($("#team_30618").find(".team_row").last().after(data.content)).then(function () {
                //	var cid = $(this).parents().find('.external_client_id').val();				
                //loadAssesorListForSchoolAssessment(cid,frm,"external",removeIds);		
               // return false;
            //});
            //alert(id_c);
            $('#team_'+id_c+' tr:last').after(data.content);
            $('.selectpicker').selectpicker('refresh');
            
        }, function (s, msg) {
            alert(msg);
        });
    });
    
    
    
    $(document).on("click",".teamrow .delete_row",function(){
                //alert("dsd");
		if($(this).parents(".customTbl_inner").first().find(".teamrow").length>1){
			var p=$(this).parents(".customTbl_inner");
			$(this).parents(".teamrow").remove();			
			
		}else
			alert("You can't delete all the rows");
		return false;
	});
     $(document).on("click",".impactstatements .delete_row",function(){
            //alert("dsd");
            if($(this).parents(".customTbl").first().find(".teamrowac2").length>0){
                    var p=$(this).parents(".customTbl");
                    $(this).parents(".teamrowac2").remove();	
                    p.first().find(".s_no").each(function(i,v){;$(v).html(i+1);});

            }else
                    alert("You can't delete all the rows");
            return false;
    });
    /*$(document).on("click",".saverow",function(){
        
      var frm = $('.impactteam').closest('form').first().attr('id');
        //alert(frm);
      var id_c=$(this).data("id");  
      
      //alert(id_c);
      
      return false;
        
    });*/


$(document).on("click", ".team_action2 .team-add", function () {
        //var frm = $('.impactteam').parents().find('form').first().attr('id');
        var frm = $('.team-add').closest('form').first().attr('id');
        
        //alert($(this).parents(".team_action").first().find(".teamrow").length);
        //console.log(removeIds)
        apiCall(this, "addActionTeam", "sn=" + ($(this).parents(".team_action2").first().find(".teamrow2").length + 1) + "&frm="+frm+"&token=" + getToken(), function (s, data) {
            //alert(data.content);
            //alert($(s).parents(".team_action").first().find(".team_row").last().after(data.content));
            //$("#team_30618").find(".team_row").last().append(data.content);
            //$.when($("#team_30618").find(".team_row").last().after(data.content)).then(function () {
                //	var cid = $(this).parents().find('.external_client_id').val();				
                //loadAssesorListForSchoolAssessment(cid,frm,"external",removeIds);		
               // return false;
            //});
            //alert(id_c);
            $('#team2 tr:last').after(data.content);
            $('.selectpicker').selectpicker('refresh');
            
            
        }, function (s, msg) {
            alert(msg);
        });
    });
    
    
    $(document).on("click",".teamrow2 .delete_row",function(){
                //alert("dsd");
		if($(this).parents(".customTbl").first().find(".teamrow2").length>1){
			var p=$(this).parents(".customTbl");
			$(this).parents(".teamrow2").remove();	
                        p.first().find(".s_no").each(function(i,v){;$(v).html(i+1);});
			
		}else
			alert("You can't delete all the rows");
		return false;
	});
        
        
        
        $(document).on("click", ".activity_action2 .activity-add", function () {
        //var frm = $('.impactteam').parents().find('form').first().attr('id');
        var frm = $('.activity-add').closest('form').first().attr('id');
        
        //alert($(this).parents(".team_action").first().find(".teamrow").length);
        //console.log(removeIds)
        apiCall(this, "addActionActity", "sn=" + ($(this).parents(".activity_action2").first().find(".teamrowac2").length + 1) + "&frm="+frm+"&token=" + getToken(), function (s, data) {
            //alert(data.content);
            //alert($(s).parents(".team_action").first().find(".team_row").last().after(data.content));
            //$("#team_30618").find(".team_row").last().append(data.content);
            //$.when($("#team_30618").find(".team_row").last().after(data.content)).then(function () {
                //	var cid = $(this).parents().find('.external_client_id').val();				
                //loadAssesorListForSchoolAssessment(cid,frm,"external",removeIds);		
               // return false;
            //});
            //alert(id_c);
            $('#activityStmnt tr:last').after(data.content);
            $('.selectpicker').selectpicker('refresh');
            $('.datePicker').datetimepicker({format: 'DD-MM-YYYY',useCurrent: false, pickTime: false});
            
            $('#actionplanform2 select.aholder').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '220px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:220px;"></ul>',
               },
        });
        $(".tdcaret .caret").css('float', 'right');
        $(".tdcaret .caret").css('margin', '8px 0');
        
        }, function (s, msg) {
            alert(msg);
        });
    });
    
    $(document).on("click", ".impactstatements .impact-add", function () {
         
        var impactStmntId = $(this).attr('rel');
        var startDate = $("#from_date").val()
        var endDate = $("#to_date").val()
        var tableClass = 'impactStmnt-'+impactStmntId;
        var frm = $('.activity-add').closest('form').first().attr('id');
        apiCall(this, "addActionImpactStmnt", "sn=" + ($(this).parents(".impactstatements").first().find(".teamrowac2").length + 1) +"&impactStmntId="+impactStmntId+ "&frm="+frm+"&token=" + getToken(), function (s, data) {
       
            $('#'+tableClass+' tr:last').after(data.content);
            $('.selectpicker').selectpicker('refresh');
            $('.datePicker').datetimepicker({format: 'DD-MM-YYYY',useCurrent: false,minDate:startDate,maxDate:endDate, pickTime: false});
        }, function (s, msg) {
            alert(msg);
        });
    });
    
    $(document).on("change", ".impactstatements .methodType", function () {
           //$(this).find('.selectpicker.methodType').on('change', function(){
           var impactStmntOption = $(this).attr('id');
           var impactStmntId = $(this).val();
           
               var activityOptionId = "#actopt-"+impactStmntOption;
               var stakeholderId = "#stake-"+impactStmntOption;
               //alert(impactStmntId);
               if (impactStmntId == '1') {
                    $(activityOptionId).hide();
                    $(stakeholderId).hide();
                }
                else if (impactStmntId == '2') {
                    //alert('ok');
                    $(activityOptionId).show();
                    $(stakeholderId).hide();
                }
                else if (impactStmntId == '3') {
                    $(activityOptionId).hide();
                    $(stakeholderId).hide();
                }
                else if (impactStmntId == '4') {
                        $(activityOptionId).hide();
                        $(stakeholderId).show();
                }
                
           //});
        });
    
    /*$(document).on("click",".activity_action2 .delete_row",function(){
            if($(this).parents(".customTbl").first().find(".teamrowac2").length>1){
                    var p=$(this).parents(".customTbl");
                    $(this).parents(".teamrowac2").remove();	
                    p.first().find(".s_no").each(function(i,v){;$(v).html(i+1);});

            }else
                    alert("You can't delete all the rows");
            return false;
    });
    */
        $(document).on("click",".activity_action2 .delete_row",function(){
                //alert("dsd");
		if($(this).parents(".customTbl").first().find(".teamrowac2").length>0){
			var p=$(this).parents(".customTbl");
			$(this).parents(".teamrowac2").remove();	
                        //p.first().find(".s_no").each(function(i,v){;$(v).html(i+1);});
			
		}else
			alert("You can't delete all the rows");
		return false;
	});

$(document).on("click",".saverow",function(){
    
    //alert("dad");
    var fired_button = $(this).val();
    //alert(fired_button);
    var id_c=$(this).data("item-id");
    $("#actionplanform1").data("item-id",id_c);
    $("#actionplanform1").data("item-id-type",fired_button);
    
    
});

$(document).on("submit", "#actionplanform1", function () {
    
    
        var id_c=$(this).data("item-id");
        var id_y=$(this).data("item-id-type");
        var assessment_id =$("#assessment_id").val();
        //alert(id_y);
        postData = $(this).serialize() + "&token=" + getToken()+"&id_c="+id_c+"&id_y="+id_y+"";
        //$().popover('show');
        apiCall(this, "createAction1", postData, function (s, data) {
            //alert("sasa");
    $('.popover').popover('hide');        
    if(data.error==1){
       if(data.popup!='' && data.popup != undefined) {
            $("#fromdate_"+id_c).val(data.fromdate)
            $("#todate_"+id_c).val(data.todate)
            alert("Errors: "+data.popup); 
       }else{
            alert("Errors: Please check the error!"); 
        }
        
    var popoverTemplate = ['<div class="timePickerWrapper popover ">',
        '<div class="arrow"></div>',
        '<div class="popover-content" style=" color: red;">',
        '</div>',
        '</div>'].join('');
    //$('.popover').popover('hide');
   for (var key in data.message) {
       
   $('#tr_'+id_c+' [data-id='+key+']').popover({
   'placement':'bottom',
   'content':''+data.message[key]+'',
   'template': popoverTemplate
   }).popover('show');
   
   }
   
   $(document).on("click","#tr_"+id_c+" .sholder,#tr_"+id_c+" .iholder,#tr_"+id_c+" .impactteam",function(){
       
     //$('#tr_'+id_c+' [data-id='+id_c+']').popover('hide');
     $('#tr_'+id_c+' [data-id='+id_c+']').popover('destroy');
     
   });
   
   $(document).on("click","#tr_"+id_c+" .ra",function(){
       
     //$('#tr_'+id_c+' [data-id='+id_c+']').popover('hide');
     $('#tr_'+id_c+' [data-id=authority_'+id_c+']').popover('destroy');
     
   });
   
   $(document).on("click","#tr_"+id_c+" .ld",function(){
       
     //$('#tr_'+id_c+' [data-id='+id_c+']').popover('hide');
     $('#tr_'+id_c+' [data-id=leader_'+id_c+']').popover('destroy');
     
   });
   
   $(document).on("click","#tr_"+id_c+" .fr",function(){
       
     //$('#tr_'+id_c+' [data-id='+id_c+']').popover('hide');
     $('#tr_'+id_c+' [data-id=frequency_r_'+id_c+']').popover('destroy');
     
   });
   
   $(document).on("click","#tr_"+id_c+" .da,#tr_"+id_c+".datePicker",function(){
       
     //$('#tr_'+id_c+' [data-id='+id_c+']').popover('hide');
     $('#tr_'+id_c+' [data-id=fromdate_'+id_c+']').popover('destroy');
     $('#tr_'+id_c+' [data-id=todate_'+id_c+']').popover('destroy');
     //alert("test");
     
   });
   
   $(document).on("click",".popover",function(){
       
     //$(this).popover('hide');
     $(this).popover('destroy');
     
   })
       
   
   
    }else{
        
        if(id_c>0){
        window.location.href= "index.php?controller=actionplan&action=actionplan2&id_c="+id_c+"&assessment_id="+assessment_id+"";
        }else{
        alert(data.message);
        showSuccessMsgInMsgBox(s, data);
        $( ".ajaxFilterReset" ).trigger( "click" );
        
        }
    }
   

            //alert(data.message);
            //showSuccessMsgInMsgBox(s, data);
            
            
            
        }, showErrorMsgInMsgBox);
        return false;
    });

$(document).on("click", "#saveAction2", function () {
    
    
        var id_c =$("#id_c").val();
        var assessment_id =$("#assessment_id").val();
        //alert(id_y);
        postData = $('#actionplanform2').serialize() + "&token=" + getToken()+"";
        saveAction2(postData,assessment_id,id_c);
        return false;
     
});
$(document).on("click", "#submitAction2", function () {
    
    
        var id_c =$("#id_c").val();
        var assessment_id =$("#assessment_id").val();
        //alert(id_y);
        postData = $('#actionplanform2').serialize()+ "&is_submit=1"+"&token=" + getToken()+"";
        if(confirm('You are going to submit the Action planning. By doing this, Action planning dashboard will no longer be editable. Do you want to continue?')){
            saveAction2(postData,assessment_id,id_c);
        }
        return false;
     
});

                    $(document).on("submit", "#action1new", function () {
                    var frm = $("#popup-actionplan_action1new.modal.fade.in");  
                    postData = $(this).serialize() + "&token=" + getToken()+"";
                    
                    apiCall(this, "createActionnew1", postData, function (s, data) {
                     
                    showSuccessMsgInMsgBox(s, data);
                    
                    $( ".ajaxFilterReset " ).trigger( "click" );
                    $(".ribWrap").hide();
                    alert("Data Saved Successfully!");				
		    frm.modal('hide');
                        
                    }, showErrorMsgInMsgBox);
        
                     return false; 
                     
                  });
                  
                  
                  $(document).on("change","select.rec",function(){
                    var currentval=$(this).val();
                    var matchcount=0;
                   $("select.rec").each(function(i) {
                       if($(this).val()==currentval){
                          matchcount++; 
                       }
                       //alert($(this).val());
                   });
                   
                   if(matchcount>1){
                       $(this).val("");
                       $('.selectpicker').selectpicker('refresh');
                       alert("Duplicate Recommendation selected . Please choose another");
                    }
                      
                  });
                   
                   $(document).on("click", ".pRowAdd", function () {
                        var sn = $(this).parents(".addBtnWrap").first().find(".row").length;
                        var assessment_id = $("#action1new").find("#assessment_id").val();
                        
                        apiCall(this, "addPlanningDiagnosticRow", "assessment_id="+assessment_id+"&"+"sn=" + ($(this).parents(".addBtnWrap").first().find(".prow").length + 1) +  "&token=" + getToken(), function (s, data) {                           
                                $(s).parents(".addBtnWrap").first().find(".prow").last().after(data.content);
                             $('.selectpicker').selectpicker('refresh');    
                        }, function (s, msg) {
                            alert(msg);
                        });
                    });
                    
                    	$(document).on("click",".prow .delete_row",function(){
                                if($(this).parents(".postRevTbl").first().find(".prow").length>1){
                                    var p=$(this).parents(".postRevTbl");
                                    $(this).parents(".prow").remove();	
                                    p.first().find(".s_no").each(function(i,v){;$(v).html(i+1);});
                                }
                                else
                                    alert("You can't delete all the rows");
                                return false;
                         });
                    

                    $(document).on("click","#actionplanform1 .delete_new",function(){
                        
                       if(confirm("Are you sure to delete?")){
                           
                       var id_c=$(this).data("deleteid"); 
                       var assessment_id = $("#actionplanform1").find("#assessment_id").val();
                       apiCall(this, "deletePlanningrow","id_c="+id_c+"&"+"assessment_id="+assessment_id+"&token=" + getToken(), function (s, data) {
                       
                       alert(data.message);
                       $( ".ajaxFilterReset " ).trigger( "click" );
                       $(".ribWrap").hide();
                       }, function (s, msg) {
                        alert(msg);
                       });
                       
                       }
                        
                       return false;
                        
                    });     
                         
                    $(document).on('change',".kpa",function(){
                         var contnr = "action1new";
                         var sn = $(this).parents(".addBtnWrap").first().find(".row").length;
                        var assessment_id = $("#action1new").find("#assessment_id").val();
                        
                        var kpa_id = $(this).val();                        
                        var aDd = $(this).closest('.prow').find("select.kq");
                        var aDd_1 = $(this).closest('.prow').find(".sq");
                        
                        apiCall(this, "getKeyQuestionsPlanning","kpa_instance_id="+kpa_id+"&"+"assessment_id="+assessment_id+"&"+"sn=" + ($(this).parents(".addBtnWrap").first().find(".prow").length) +  "&token=" + getToken(), function (s, data) {
                        aDd.find("option").next().remove();                                                                    
                        addOptions(aDd, data.content, 'key_question_instance_id', 'key_question_text')  ; 
                        $('.selectpicker').selectpicker('refresh'); 
                        }, function (s, msg) {
                        alert(msg);
                        });
                        return false;
                    });
                    
                    $(document).on('change',".kq",function(){
                        var contnr = "action1new";
                        var sn = $(this).parents(".addBtnWrap").first().find(".row").length;
                        var assessment_id = $("#action1new").find("#assessment_id").val();
                        
                        var kq_id = $(this).val();                        
                        var aDd = $(this).closest('.prow').find("select.sq");
                        
                        apiCall(this, "getCoreQuestionsPlanning","key_question_instance_id="+kq_id+"&"+"assessment_id="+assessment_id+"&"+"sn=" + ($(this).parents(".addBtnWrap").first().find(".prow").length) +  "&token=" + getToken(), function (s, data) {
                        //aDd.find("option").remove();
                        aDd.find("option").next().remove();
                        addOptions(aDd, data.content, 'core_question_instance_id', 'core_question_text')  ;   
                        $('.selectpicker').selectpicker('refresh'); 
                        }, function (s, msg) {
                        alert(msg);
                        });
                        return false;
                    });
                    
                    
                    $(document).on('change',".sq",function(){
                        var contnr = "action1new";
                        var sn = $(this).parents(".addBtnWrap").first().find(".row").length;
                        var assessment_id = $("#action1new").find("#assessment_id").val();
                        
                        var sq_id = $(this).val();                        
                        var aDd = $(this).closest('.prow').find("select.js");
                        
                        apiCall(this, "getJSPlanning","core_question_instance_id="+sq_id+"&"+"assessment_id="+assessment_id+"&"+"sn=" + ($(this).parents(".addBtnWrap").first().find(".prow").length) +  "&token=" + getToken(), function (s, data) {
                        aDd.find("option").next().remove();                                                                                             	
                        addOptions(aDd, data.content, 'judgement_statement_instance_id', 'judgement_statement_text')  ;   
                        $('.selectpicker').selectpicker('refresh'); 
                        }, function (s, msg) {
                        alert(msg);
                        });
                        return false;
                    });
                    
                    
                    $(document).on('change',".js",function(){
                        var contnr = "action1new";
                        var sn = $(this).parents(".addBtnWrap").first().find(".row").length;
                        var assessment_id = $("#action1new").find("#assessment_id").val();
                        
                        var js_id = $(this).val();                        
                        var aDd = $(this).closest('.prow').find("select.rec");
                        
                        apiCall(this, "getrecPlanning","judgement_statement_instance_id="+js_id+"&"+"assessment_id="+assessment_id+"&"+"sn=" + ($(this).parents(".addBtnWrap").first().find(".prow").length) +  "&token=" + getToken(), function (s, data) {
                        aDd.find("option").next().remove();                                                                                             	
                        addOptions(aDd, data.content, 'recommendation_id', 'recommendation_text');   
                        $('.selectpicker').selectpicker('refresh'); 
                        }, function (s, msg) {
                        alert(msg);
                        });
                        return false;
                    });
                    });
                    
                    function uploadFile(){
    
		var fileElm;
		if(uploadQueue.length>0){
			isFileUploading=true;
			fileElm=uploadQueue.shift();
			if(fileElm.elem==undefined || fileElm.elem.parent().length==0){
				uploadFile(uploadBtnId);
			}else
				fileElm.elem.removeClass("waiting").addClass("uploading");
				
		}else{
			isFileUploading=false;
			return;
		}
                var file_cat= $("#"+uploadBtnId+"").attr('reltype');
                if(file_cat==undefined){
                    file_cat='';
                }
                
		var fe=fileElm.elem;
		var formData = new FormData();
		formData.append("token",getToken());
		formData.append('filename', fileElm.file.name);
		formData.append('filetype', fileElm.file.type);
		formData.append('file', fileElm.file);
                $("input[type=file]").val('');
		
		$.ajax({
			url: "?controller=api&action=uploadFile",
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
                        async:false,
			success: function (rData) {
                            
                          
                                //alert(file_cat);
                                if(rData!=undefined && rData!=null && rData.status!=undefined){
					if(rData.status==-1){
						isFileUploading=false;
						if(!$("#login_popup_wrap").hasClass('active')){
							sessionExpired();
						}
						
						fe.removeClass("uploading").addClass("waiting");
						uploadQueue.unshift(fileElm);
						$("#login_popup_wrap").on("loggedIn",uploadFile);
					}else if(rData.status==1){
                                                
                                                ids = uploadBtnId.split("-");
                                               // alert(ids[0]);
						fe.attr("id","file-"+rData.id).removeClass("uploading").addClass("uploaded vtip ext-"+rData.ext).data("id",rData.id).attr("title",rData.name).append('<input type="hidden" value="'+rData.id+'" name="impactStmnt[files]['+ids[0]+']['+ids[1]+'][]" />');
						fe.find(".inner").html('<a target="_blank" href="'+rData.url+'"> </a>');
						$("body").trigger('dataChanged');
						uploadFile(uploadBtnId);
					}else{
						alert("Error while uploading file '"+fileElm.file.name+"': "+rData.message);
						fe.remove();
					}
				}else{
					alert("Unknown error occurred while uploading file '"+fileElm.file.name+"'.");
					fe.remove();
					uploadFile();
				}
			},
			error:function(a,status){
				var msg="Error while connecting to server";
				if(status=="timeout")
					msg='Request time out';
				else if(status=="parsererror")
					msg='Unknown response from server';
				alert("Error occurred while uploading file '"+fileElm.file.name+"' : "+msg);
				fe.remove();
				uploadFile();
			},
			xhrFields: {
				onprogress:function(e){
					var perc = parseInt(e.loaded / e.total * 100, 10);
					//console.log(perc);
				}
			}
		});
	}
                    
                    
        $(document).on("click", "#actionplanform2 .collapseGA", function () {
        var id = $(this).parents("tr").first().data('id');
        
        if ($(this).hasClass('fa-minus-circle')) {
            $("#actionplanform2  .ga-rows-" + id).hide();
            $(this).removeClass('fa-minus-circle').addClass("fa-plus-circle").parents("tr").first().removeClass('tchrAssmHead');
        } else if ($("#actionplanform2 .ga-rows-" + id).length) {
            $("#actionplanform2  .ga-rows-" + id).show();
            $(this).removeClass('fa-plus-circle').addClass("fa-minus-circle").parents("tr").first().addClass('tchrAssmHead');
        } else {
            
            apiCall(this, "getActivityPostponed", "token=" + getToken() + "&id=" + id, function (s, data) {
                
                var cls = $(s).removeClass('fa-plus-circle').addClass("fa-minus-circle").parents("tr").first().addClass('tchrAssmHead').after(data.content).hasClass('even') ? 'even' : 'odd';
                $("#actionplanform2 .ga-rows-" + data.gaid).addClass(cls);
                
            }, function (s, msg) {
                alert(msg);
            });
        }
    });
    
    
    
    $(document).on("change", "#actionplanform2 select.astatus", function () {
    var id = $(this).parents("tr").first().data('id');
    //alert($(this).val());
    var date_val_val=$("tr[data-id='" + id + "'] .arealdate").html();
    if($(this).val()=="3"){
        
    var date_val=$("tr[data-id='" + id + "'] .adateda").val();
    var comments_val=$("tr[data-id='" + id + "'] .acomments").val();
    
    //alert(comments_val); 
    $("tr[data-id='" + id + "'] .date-show").show();
    $("tr[data-id='" + id + "'] .arealdate").html(date_val);
    $("tr[data-id='" + id + "'] .adateda").val("");
    
    $("tr[data-id='" + id + "'] .activity_comments-show").show();
    
    $("tr[data-id='" + id + "'] .activity_comments-show").prop("title", comments_val);
    $("tr[data-id='" + id + "'] .acomments").val("");
    
    }else if(date_val_val!=""){
        var date_val=$("tr[data-id='" + id + "'] .arealdate").html();
        $("tr[data-id='" + id + "'] .date-show").hide();
        $("tr[data-id='" + id + "'] .adateda").val(date_val);
        $("tr[data-id='" + id + "'] .arealdate").html("");
        
        var comments_val=$("tr[data-id='" + id + "'] .activity_comments-show").attr('title');
        $("tr[data-id='" + id + "'] .activity_comments-show").hide();
        $("tr[data-id='" + id + "'] .activity_comments-show").prop("title", '');
        $("tr[data-id='" + id + "'] .acomments").val(comments_val);
    }
    
    
        
    });
    function saveAction2(postData,assessment_id,id_c){
    
    
    
       // var id_c =$("#id_c").val();
       // var assessment_id =$("#assessment_id").val();
        //alert(id_y);
        //postData = $('#actionplanform2').serialize() + "&token=" + getToken()+"";
        //$().popover('show');
        apiCall('#actionplanform2', "createAction2", postData, function (s, data) {
           // alert("sasa");
            
    $('.popover').popover('hide');
    
    if(data.error==1){
        
    alert("Errors: Please check the error!");    
        
    var popoverTemplate = ['<div class="timePickerWrapper popover ">',
        '<div class="arrow"></div>',
        '<div class="popover-content" style=" color: red;">',
        '</div>',
        '</div>'].join('');
    //$('.popover').popover('hide');
   var focusid=""; 
   var idd=0;
   for (var key in data.message['team_designation']) {
       
   //alert(key);
   //var kk=1*key+1*1;
   //alert(kk);
   $('select.dholder').eq(key).popover({
   'placement':'bottom',
   'content':''+data.message['team_designation'][key]+'',
   'template': popoverTemplate
   }).popover('show');
   
   
   if(idd==0 && focusid==""){
     focusid=$('select.dholder').eq(key); 
   }
   idd++;
   }
   
   for (var key in data.message['team_member_name']) {
       
   //alert(key);
   //var kk=1*key+1*1;
   //alert(kk);
   $('input.tholder').eq(key).popover({
   'placement':'bottom',
   'content':''+data.message['team_member_name'][key]+'',
   'template': popoverTemplate
   }).popover('show');
   
   if(idd==0 && focusid==""){
     focusid=$('select.tholder').eq(key); 
   }
   
   }
   
   
   for (var key in data.message['activity_stackholder']) {
       
   //alert(key);
   //var kk=1*key+1*1;
   //alert(kk);
   $('select.aholder').eq(key).popover({
   'placement':'bottom',
   'content':''+data.message['activity_stackholder'][key]+'',
   'template': popoverTemplate
   }).popover('show');
   
   if(idd==0 && focusid==""){
     focusid=$('select.aholder').eq(key); 
   }
   
   }
   
   for (var key in data.message['activity']) {
       
   //alert(key);
   //var kk=1*key+1*1;
   //alert(kk);
   $('select.act').eq(key).popover({
   'placement':'bottom',
   'content':''+data.message['activity'][key]+'',
   'template': popoverTemplate
   }).popover('show');
   
   if(idd==0 && focusid==""){
     focusid=$('select.act').eq(key); 
   }
   
   }
   
   for (var key in data.message['activity_details']) {
       
   //alert(key);
   //var kk=1*key+1*1;
   //alert(kk);
   $('textarea.ad').eq(key).popover({
   'placement':'bottom',
   'content':''+data.message['activity_details'][key]+'',
   'template': popoverTemplate
   }).popover('show');
   
   if(idd==0 && focusid==""){
     focusid=$('textarea.ad').eq(key); 
   }
   
   }
   
   
   for (var key in data.message['activity_status']) {
       
   //alert(key);
   //var kk=1*key+1*1;
   //alert(kk);
   $('select.astatus').eq(key).popover({
   'placement':'bottom',
   'content':''+data.message['activity_status'][key]+'',
   'template': popoverTemplate
   }).popover('show');
   
   if(idd==0 && focusid==""){
     focusid=$('select.astatus').eq(key); 
   }
   
   }
   
   
   for (var key in data.message['activity_date']) {
       
   //alert(key);
   //var kk=1*key+1*1;
   //alert(kk);
   $('div.adate').eq(key).popover({
   'placement':'bottom',
   'content':''+data.message['activity_date'][key]+'',
   'template': popoverTemplate
   }).popover('show');
   
   if(idd==0 && focusid==""){
     //focusid=$('input.adate').eq(key);
     focusid=$('textarea.acomments').eq(key);
   }
   
   }
   
   
   for (var key in data.message['activity_actual_date']) {
       
   //alert(key);
   //var kk=1*key+1*1;
   //alert(kk);
   $('div.fdate').eq(key).popover({
   'placement':'bottom',
   'content':''+data.message['activity_actual_date'][key]+'',
   'template': popoverTemplate
   }).popover('show');
   
   if(idd==0 && focusid==""){
     //focusid=$('input.fdate').eq(key);
     focusid=$('textarea.acomments').eq(key);
   }
   
   }
   
   
   for (var key in data.message['activity_comments']) {
       
   //alert(key);
   //var kk=1*key+1*1;
   //alert(kk);
   $('textarea.acomments').eq(key).popover({
   'placement':'bottom',
   'content':''+data.message['activity_comments'][key]+'',
   'template': popoverTemplate
   }).popover('show');
   
   if(idd==0 && focusid==""){
     focusid=$('textarea.acomments').eq(key); 
   }
   
   }
   
   //validations for impact statements  <--start--->
   for (var key in data.message['impact_date']) {

        $('div.impact_date').eq(key).popover({
        'placement':'bottom',
        'content':''+data.message['impact_date'][key]+'',
        'template': popoverTemplate
        }).popover('show');

        if(idd==0 && focusid==""){
          focusid=$('select.impact_activity_method').eq(key); 
        }
   
    }
    for (var key in data.message['impact_activity_method']) {
       
        $('select.impact_activity_method').eq(key).popover({
        'placement':'bottom',
        'content':''+data.message['impact_activity_method'][key]+'',
        'template': popoverTemplate
        }).popover('show');

        if(idd==0 && focusid==""){
          focusid=$('select.impact_activity_method').eq(key); 
        }   
    }
    for (var key in data.message['impact_stakeholder']) {
       
        $('select.impact_stakeholder').eq(key).popover({
        'placement':'bottom',
        'content':''+data.message['impact_stakeholder'][key]+'',
        'template': popoverTemplate
        }).popover('show');

        if(idd==0 && focusid==""){
          focusid=$('select.impact_stakeholder').eq(key); 
        }   
    }
    for (var key in data.message['impact_activity_option']) {
       
        $('select.impact_activity_option').eq(key).popover({
        'placement':'bottom',
        'content':''+data.message['impact_activity_option'][key]+'',
        'template': popoverTemplate
        }).popover('show');

        if(idd==0 && focusid==""){
          focusid=$('select.impact_activity_option').eq(key); 
        }   
    }
    for (var key in data.message['impact_comments']) {
       
        $('textarea.impact_comments').eq(key).popover({
        'placement':'bottom',
        'content':''+data.message['impact_comments'][key]+'',
        'template': popoverTemplate
        }).popover('show');

        if(idd==0 && focusid==""){
          focusid=$('textarea.impact_comments').eq(key); 
        }   
    }
   
   //validation for impact statement <--end-->
   $(document).on("click",".popover",function(){
       
     //$(this).popover('hide');
     $(this).popover('destroy');
     
   });
       
    $(document).on("click",".tholder,.ad,.adate,.acomments,.fdate,.impact_comments,.impact_date",function(){
       
     //$('#tr_'+id_c+' [data-id='+id_c+']').popover('hide');
     $(this).popover('destroy');
     
   });
   
   $(document).on("mouseleave",".adate,.fdate,.impact_date",function(){
      //alert("dsds"); 
     //$('#tr_'+id_c+' [data-id='+id_c+']').popover('hide');
     $(this).popover('destroy');
     
   });
   
   $(document).on("change",".dholder,.aholder,.astatus,.act,.impact_activity_method,.impact_activity_option,.impact_stakeholder",function(){
       
     //$('#tr_'+id_c+' [data-id='+id_c+']').popover('hide');
     $(this).popover('destroy');
     
   });
   
   
   
   
   if(focusid!=""){
   focusid.focus();
   }
   
   
   
    }else{
        
        //alert(data.message);
        showSuccessMsgInMsgBox(s, data);
        var postData="token:"+ getToken();
        var querystring="id_c="+id_c+"&assessment_id="+assessment_id+"";
        ajaxCall(this, 'actionplan', 'actionplan2', postData,querystring, function (s, data) { 
            //alert(data.content);
            $(".nuibody .container").html(data.content);
            $('.selectpicker').selectpicker('refresh');
            //s.replaceWith(data.content);
            $('#actionplanform2 select.aholder').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '220px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:220px;"></ul>',
               },
        });
        //$(".caret").css('float', 'right');
        //$(".caret").css('margin', '8px 0');
        $(".tdcaret .caret").css('float', 'right');
        $(".tdcaret .caret").css('margin', '8px 0');
        
        
        var id_c =$("#id_c").val();
        var assessment_id =$("#assessment_id").val();
        var querystring="&id_c="+id_c+"&assessment_id="+assessment_id+"";
        apiCall($("#actionplanform2"), "actionplandata", "token=" + getToken() +""+querystring, function (s, data) {
                
            
            //alert(data.xaxis);
            chartdata.xAxis = data.xaxis;
            chartdata.series =data.data; 
            $('#container').highcharts(chartdata);

           var yVis = false;
           $('#container').highcharts().yAxis[0].update({
            visible: yVis
           });
                
            }, function (s, msg) {
                alert(msg);
            });
            
        }, showErrorMsgInMsgBox);
        
        
        
    }
   

            //alert(data.message);
            //showSuccessMsgInMsgBox(s, data);
            
            
            
        }, showErrorMsgInMsgBox);
        return false;
}
    
   $(function () {
   $('#actionplanform2 select.aholder').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '220px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:220px;"></ul>',
               },
        });
        //$(".caret").css('float', 'right');
        //$(".caret").css('margin', '8px 0');
        $(".tdcaret .caret").css('float', 'right');
        $(".tdcaret .caret").css('margin', '8px 0');
});
/////////////////////////////////////////////////////

 Highcharts.SVGRenderer.prototype.symbols.plus = function (x, y, w, h) {
    return [
        'M', x, y + (5 * h) / 8,
        'L', x, y + (3 * h) / 8,
        'L', x + (3 * w) / 8, y + (3 * h) / 8,
        'L', x + (3 * w) / 8, y,
        'L', x + (5 * w) / 8, y,
        'L', x + (5 * w) / 8, y + (3 * h) / 8,
        'L', x + w, y + (3 * h) / 8,
        'L', x + w, y + (5 * h) / 8,
        'L', x + (5 * w) / 8, y + (5 * h) / 8,
        'L', x + (5 * w) / 8, y + h,
        'L', x + (3 * w) / 8, y + h,
        'L', x + (3 * w) / 8, y + (5 * h) / 8,
        'L', x, y + (5 * h) / 8,
        'z'
    ];
};



if (Highcharts.VMLRenderer) {
    Highcharts.VMLRenderer.prototype.symbols.plus = Highcharts.SVGRenderer.prototype.symbols.plus;
    
}





//Highcharts.chart('container', {
//backgroundColor: '#d1c382',
var chartdata = {
    chart: {
        type: 'scatter',
        backgroundColor: 'transparent',
        zoomType: 'xy',
        borderColor: '#000000',
        borderWidth: 0,
        
    },
    title: {
        text: ''
    },
    /*subtitle: {
        text: 'Source: Heinz  2003'
    },*/
    xAxis: [],
    yAxis: {
        visible:false,
        title: {
        text: null
    },
    
    lineWidth: 0,
    gridLineWidth: 0,
    tickInterval: null,
    tickPixelInterval: 1,
    endOnTick: false,
    min: 0
    },
    
    /*legend: {
        layout: 'horizontal',
        align: 'left',
        verticalAlign: 'bottom',
        x: 100,
        y: 10,
        floating: false,
        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
        borderWidth: 1
    },*/
legend: {
            layout: 'horizontal',
            align: 'left',
            verticalAlign: 'bottom',
            x: 10,
            y: 0,
            floating: false,
            borderWidth: 1,
            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#d1c382'),
            
        },
        credits: {
        enabled: false
        },

            tooltip: {
               formatter: function() {

                var s = '';
                var index = this.series.data.indexOf(this.point);
//alert('test-tooltip.php?index='+index+'&series='+this.series.name+'');
                   //var data = $.get('test-tooltip.php?index='+index+'&series='+this.series.name+'', function(data) {
                             // return 'jj';
                            //});

    var rV='';                

    /*$.ajax({
      dataType: "json",
      url: 'test-tooltip.php?index='+index+'&series='+this.series.name+'',
      async: false, // this will stall the tooltip
      success: function(ajax){
        rV = ""+ajax.series+"<br>"+ajax.date+"<br>"+ajax.des+"";
      }
    });*/
       var id_c =$("#id_c").val();
       var assessment_id =$("#assessment_id").val();
       //var querystring="&id_c="+id_c+"&assessment_id="+assessment_id+"";            
       var querystring="&id_c="+id_c+"&assessment_id="+assessment_id+"&index="+index+"&series="+this.series.name+"";
       
      apiCall($("#actionplanform2"), "actionplantip", "token=" + getToken() +""+querystring, function (s, data) {
                
            
            rV = ""+data.data.series+"<br>"+data.data.a_date+"<br>"+data.data.textshow+"";
                
            }, function (s, msg) {
                alert(msg);
            },'','',false);             
                   
    
    return rV;

                //var value1 = this.series.valuef;
                
                //alert(index);
                //$.each(this.points, function(i, point) {
                    //s += ''+ this.series.name +'<br><b>'+ this.x + '('+ this.y + ')</b>';
                //});
                
                //return 'jj';
            },
            shared: true,
            useHTML: true
            },
    plotOptions: {
        scatter: {
            marker: {
                radius: 5,
                states: {
                    hover: {
                        enabled: true,
                        lineColor: 'rgb(100,100,100)'
                    }
                }
            },
            states: {
                hover: {
                    marker: {
                        enabled: false
                    }
                }
            }
        }
    },
    series: [],
    exporting: {
        enabled: false,
        sourceWidth: 800,
        sourceHeight: 180
    }
};


//$(".highcharts-yaxis-labels").hide();


//$.getJSON('data.php', function(data) {

/*chartdata.xAxis = [{
            categories: ['', '', '', 'Today', '']
         }];

chartdata.series =data;
$('#container').highcharts(chartdata);
var yVis = false;
$('#container').highcharts().yAxis[0].update({
            visible: yVis
        });*/
//});

//chartdata.xAxis = [{
            //categories: ['', '', '', 'Today', '']
         //}];
//chartdata.series = [{"name":"Principle","color":"rgba(223, 83, 83, .5)","data":[0,"",0,"",0]}];
/*chartdata.series = [{
        name: 'Research-C',
        color: 'rgba(0,128,0, .5)',
        data: [0,'', 0, '', 0],
        marker: {
        symbol: 'triangle'
    }

    },

    {
   name: 'Research-P',
        color: 'rgba(255,165,0, .5)',
        data: [0.75,'', 0, '', 0],
        marker: {
        symbol: 'triangle'
    }

    }
  , {
        name: 'Planning-C',
        color: 'rgba(0,128,0, .5)',
        data: [1.50, 0.50, '', 0.50, 0],
        marker: {
        symbol: 'circle'
    }
    },
 {
        name: 'Planning-P',
        color: 'rgba(255,165,0, .5)',
        data: [2.25, 0.50, '', 0.50, 0],
        marker: {
        symbol: 'circle'
    }
    }
];*/

 /*$(function () {
     
        var id_c =$("#id_c").val();
        var assessment_id =$("#assessment_id").val();
        var querystring="&id_c="+id_c+"&assessment_id="+assessment_id+"";
        apiCall($("#actionplanform2"), "actionplandata", "token=" + getToken() +""+querystring, function (s, data) {
                
          
            //alert(data.xaxis);
            chartdata.xAxis = data.xaxis;
            chartdata.series =data.data; 
            $('#container').highcharts(chartdata);

           var yVis = false;
           $('#container').highcharts().yAxis[0].update({
            visible: yVis
           });
                
            }, function (s, msg) {
                alert(msg);
            });
     
     

    });*/

//square,diamond

$(document).on("click", "#submitActionReport", function () {
    
    
        var id_c =$("#id_c").val();
        var assessment_id =$("#assessment_id").val();
        var datesrange =$("#datesrange").val();
        var eurl =$("#exportUrl").val();
        //alert(id_y);
        var querystring = "&id_c="+id_c+"&assessment_id="+assessment_id+"&datesrange="+datesrange+"&type=image";
        
        apiCall($("#actionplanform2"), "actionplandata", "token=" + getToken() +""+querystring, function (s, data) {
        
        chartdata.xAxis = data.xaxis;
            
        chartdata.series =data.data;
        
        
        //$('#container').highcharts(chartdata);
        var exportUrl = eurl;
        var exportUrl2= 'http://export.highcharts.com/';
        
        // POST parameter for Highcharts export server
       var object = {
        options: JSON.stringify(chartdata),
        type: 'image/png',
        async: true
       };
       
    // Ajax request
    $.ajax({
        type: 'post',
        url: exportUrl,
        data: object,
        timeout: 7000,
        async: false,
        beforeSend: function () {
        $("#ajaxLoading").show();
        
        },
        complete: function () {
           
        $("#ajaxLoading").hide();
        
        },
        success: function (data) {
            //Submit data from your server
             //alert(data);
             var querystring = "&id_c="+id_c+"&assessment_id="+assessment_id+"&datesrange="+datesrange+"&file="+data+"&type=local";
             apiCall($("#actionplanform2"), "actionplanchartsave", "token=" + getToken() +""+querystring, function (s, data) {
             //window.location.href= "index.php?controller=actionplan&action=actionplan2&id_c="+id_c+"&assessment_id="+assessment_id+"&datesrange="+datesrange+"";   
             //alert(data.file);
             window.open("index.php?controller=report&action=actionPlan&id_c="+id_c+"&assessment_id="+assessment_id+"&datesrange="+datesrange+"","_blank");
                        }, function (s, msg) {
                alert(msg);
            });
             // Ajax request
            /*$.ajax({
                type: 'post',
                url: 'posttweet.php',//this your local file
                data: {'url' : exportUrl+data},
                success: function (data2) {
                    //Response from your server
                    //if your teste.php print response. echo "" or die("") ;
                    alert(data2);
                }
            });*/
        },
        error: function (jqXHR, exception) {
        var msg = '';
        if (jqXHR.status === 0) {
            
            //msg = 'Not connect.\n Verify Network.';
            
            $.ajax({
                    type: 'post',
                    url: exportUrl2,
                    data: object,
                    async: false,
                    beforeSend: function () {
                    $("#ajaxLoading").show();
        
                    },
                    complete: function () {
           
                     $("#ajaxLoading").hide();
        
                    },
                    success: function (data) {
                       //alert(data);
                       var querystring = "&id_c="+id_c+"&assessment_id="+assessment_id+"&datesrange="+datesrange+"&file="+data+"&type=outside";
                       apiCall($("#actionplanform2"), "actionplanchartsave", "token=" + getToken() +""+querystring, function (s, data) {
                       //window.location.href= "index.php?controller=actionplan&action=actionplan2&id_c="+id_c+"&assessment_id="+assessment_id+"&datesrange="+datesrange+"";
                       window.open("index.php?controller=report&action=actionPlan&id_c="+id_c+"&assessment_id="+assessment_id+"&datesrange="+datesrange+"","_blank");
                       }, function (s, msg) {
                       alert(msg);
                       });
                        
                    },
                    error: function (jqXHR, exception) {
                        if (jqXHR.status === 0) {
                            msg = 'Not connect.\n Verify Network.';
                        }else if (jqXHR.status == 404) {
                            msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                            msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                            msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                            msg = 'Time out error.';
                        } else if (exception === 'abort') {
                            msg = 'Ajax request aborted.';
                        } else {
                            msg = 'Uncaught Error.\n' + jqXHR.responseText;
                        }
                        alert(msg);
                    }
                    
                });
            
            
            
        } else if (jqXHR.status == 404) {
            msg = 'Requested page not found. [404]';
        } else if (jqXHR.status == 500) {
            msg = 'Internal Server Error [500].';
        } else if (exception === 'parsererror') {
            msg = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
            msg = 'Time out error.';
        } else if (exception === 'abort') {
            msg = 'Ajax request aborted.';
        } else {
            msg = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        //alert(msg);
    }
    });
    
            
        }, function (s, msg) {
                alert(msg);
            });
        
});

