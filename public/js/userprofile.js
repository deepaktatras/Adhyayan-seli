var isFileUploading=false;
var allowedExt1=["jpeg","png","gif","jpg","avi","mp4","mov","doc","docx","txt","xls","xlsx","pdf","csv",'xml','pptx','ppt','cdr','mp3','wav'];
var uploadQueue=[];
$(document).ready(function () {
    // code for disabled the user profile
    var divID = [];
    divID[0] = '#pd_personal';
    divID[1] = '#emergencyContact';
    divID[2] = '#additionalInfo';
    divID[3] = '#biography';
    divID[4] = '#introductoryAssessment';
    disableProfile = true;
    if ($('#term_condition').val() == 1) {
        disableProfile = false;
        $('.date').datetimepicker({format: 'DD-MM-YYYY', maxDate: new Date, pickTime: false}).on('change', function () {
            $("body").trigger('dataChanged');
        });
        //$("#userProfileForm").find('div#tc_contract').addClass('tab-pane fade in active');
    }
    
     if ($('#is_admin').val() == 1) {
          disableProfile = false;
     }
     
    if (disableProfile === true) {
        userProfileDisabled();
    }
    if ($('#is_facilitator').val() == 1) {
          userProfileDisabledFc();
     }

    $(document).on("click", "#agreeButton", function () {

        var checkCount = $("input[name='contract_value[]']:checked").length;
        var numofCheckBoxes = $("input[name='contract_value[]']").length;
        var isFacilitator = $('#is_facilitator').val();
        var checkFCount=0;
        var numofFCheckBoxes=0;
        var fcStatus=1;
        if(isFacilitator == 1) {
              checkFCount = $("input[name='contract_value_fc[]']:checked").length;
              numofFCheckBoxes = $("input[name='contract_value_fc[]']").length;
        }
        if (checkCount == numofCheckBoxes)
        {
            if(isFacilitator == 1){
               fcStatus = facilitatorTermConditionStatus(checkFCount,numofFCheckBoxes);
               if(fcStatus){
                $('.contract_value_text_fc').css({"border-color": "none", "border-weight": "none", "border-style": "none"}).focus();
                $('.contract_value_fc').prop('disabled', true);
               }
            }
            if(fcStatus){
                disableProfile = false;
                alert("Thank you. You can now proceed further.");
                $('.contract_value_text').css({"border-color": "none", "border-weight": "none", "border-style": "none"}).focus();
                $('.contract_value').prop('disabled', true);
                userProfileEnable();
                var e = $("#first_name");
                focusUserProfileTab(e)
                $('#termscondition_tab').hide();
                $('#tc_contract_tab').addClass('active');
                $('#tc_contract').addClass('active');
                $('#pd_personal').addClass('in');
                $('#myjourneyBtn').show();
                $("input.contract_value").attr("disabled", true);
                $('#termscondition_tab_refer').show();
                checkFormTabCompletion($('#termscondition'));
            }else{
                alert('Please accept terms & conditions for facilitator contract!')
                $('.contract_value_text_fc').css({"border-color": "#FF0000", "border-weight": "1px", "border-style": "solid"});
                $('.contract_value_fc').focus();
               // $('#tc_contract').hide();
                //$('#fc_contract').show();
                $('#fc_contract_tab').addClass('active');
                $('#tc_contract_tab').removeClass('active');
                $('#fc_contract').addClass('active in');
                $('#tc_contract').removeClass('active in');
               
            return false;
            }
        } else {
            alert('Please accept terms & conditions!')
            $('.contract_value_text').css({"border-color": "#FF0000", "border-weight": "1px", "border-style": "solid"});
            $('.contract_value').focus();
            return false;
        }
    });
    for (var i = 0; i < divID.length; i++) {
            checkFormTabCompletion($(divID[i]));
    }
    // add more language function on 26-05-2016 by Mohit Kumar
    $(document).on("click", ".language_table .languageAddRow", function () {
        //var frm = $('.extteamAddRow').parents().find('form').first().attr('id');
        var frm = $('.languageAddRow').closest('form').first().attr('id');

        var removeIds = '';
        apiCall(this, "addLanguageRow", "sn=" + ($(this).parents(".language_table").first().find(".language_row").length + 1) + "&token=" + getToken(), function (s, data) {
            checkFormTabCompletion(this);
            $("body").trigger('dataChanged');
            $.when($(s).parents(".language_table").first().find(".language_row").last().after(data.content)).then(function () {
                return false;
            });
        }, function (s, msg) {
            alert(msg);
        });
    });
     
    
    //
    $(document).on("change","#accomod_pref",function() {
        
        //alert($(this).val());
        if($(this).val() != '') {
            $('#info_acc').show();
        }else {
            $('#info_acc').hide();
        }
        
        //alert("deepak");
    })

    // remove language row on 26-05-2016 by Mohit Kumar
    $(document).on("click", ".language_row .delete_language_row", function () {
        if ($(this).parents(".language_table").first().find(".language_row").length > 1) {
            var p = $(this).parents(".language_table");
            $(this).parents(".language_row").remove();
            p.first().find(".s_no").each(function (i, v) {
                $(v).html(i + 1)
            });
            var trgr = p.data('trigger');
            if (trgr != undefined && trgr != null && trgr != "") {
                $("body").trigger(trgr);
            }
            $("body").trigger('dataChanged');
        } else
            alert("You can't delete all the rows");
        return false;
    });

    /*// show & hide other medical option on 26-05-2016 by Mohit Kumar
    $(document).on("click", "#medical_7", function () {
        if ($(this).is(':checked')) {
            $('#other_medical_div').show();
        } else {
            $('#other_medical_div').hide();
        }
        $('#other_medical_text').val('');
    });

    // show & hide other hobbies option on 27-05-2016 by Mohit Kumar
    $(document).on("click", "#hobbies_11", function () {
        if ($(this).is(':checked')) {
            $('#other_hobbies_div').show();
        } else {
            $('#other_hobbies_div').hide();
        }
        $('#other_hobbies_text').val('');
    });*/

    // disable the profile editable form on 27-05-2016 by Mohit Kumar
    $(document).on("click", "#disagreeButton", function () {
        disableProfile = true;
        alert("Are you sure that you want to decline terms and conditions?")
        userProfileDisabled();
        window.location.href = '?'
    });


    $(document).on("click", "#submitUserProfile", function () {
        if (confirm('Are you sure you want to submit this?')) {
//            
                if ($('#medical_other').is(':checked')) {
                    if ($('#other_medical_text').val().trim() == '') {
                        alert('Please specify the other medical conditions');
                        return false;
                    }
                }
                if ($('#hobbies_other').is(':checked')) {
                    if ($('#other_hobbies_text').val().trim() == '') {
                        alert('Please specify the other hobbies');
                        return false;
                    }
                }
                var postData = $('#userProfileForm').serialize();

                apiCall($('#userProfileForm'), "updateUserProfile", postData + "&is_submit=1&token=" + getToken(),
                        function (s, data) {
                            $("body").trigger('dataChanged');
                            if (($('#table').val() == 'd_aqs_team' || $('#table').val() == 'd_AQS_team') && data.status == 1) {
                                window.location.href = '?controller=login&action=login'
                            } else {
//                            var s = $("#userProfileForm");
//                            showSuccessMsgInMsgBox(s, data);
//                            if ($(".user-list").length) {
//                                filterByAjax($(".user-list"));
//                            }
//                            $("#updateUserProfile").prop('disabled', true);
                                alert("Successfully submitted");
                                window.location.reload();
                            }

                        }, function (s, msg, data) {
                    //console.log(data)
                    if (data != undefined && data != null && data.errors != undefined) {
                        $('#validationErrors').show();
                        addErrorNew("#validationErrors", data.errors);
                    } else {
                        $("#validationErrors").show();
                        $("#validationErrors").html('');
                        addError("#validationErrors", data.message, '');
                    }
                }, function (s, d) {
                    $("body").trigger('dataChanged');
                }
                );

//            }
        }
        return false;
    });
    
    
    
    $(document).on("click", "#saveIntroductoryAssessment", function () {
       
            var postData = $('#userAssessmentForm').serialize();

            apiCall($('#userAssessmentForm'), "updateUserIntroAss", postData + "&token=" + getToken(),
                function (s, data) {

                    $("body").trigger('dataChanged');

                    if (data.status == 1) {
                        
                        showSuccessMsgInMsgBox(s, data);
                        $("#saveIntroductoryAssessment").addClass("disabled");
                        //$("#submitIntroductoryAssessment").addClass("disabled");
                        $('#score').html(data.score);
                        $('#validationErrors').hide();
                    } else {
                        showErrorMsgInMsgBox(s, data);
//                        checkFormTabCompletion(s);
                    }

                }, function (s, msg, data) {
                   if (data != undefined && data != null && data.errors != undefined) {
                        $('#validationErrors').show();
                        addErrorNew("#validationErrors", data.errors);
                    } else {
                        addError("#validationErrors", data.message, '');
                    }
                } );
           
//        }
        return false;
    });
    
     $(document).on("click", "#submitIntroductoryAssessment", function () {
       
            var postData = $('#userAssessmentForm').serialize();

            apiCall($('#userAssessmentForm'), "updateUserIntroAss", postData +"&is_submit=1"+ "&token=" + getToken(),
                function (s, data) {

                    $("body").trigger('dataChanged');

                    if (data.status == 1) {
                        
                        showSuccessMsgInMsgBox(s, data);
                        $("#saveIntroductoryAssessment").addClass("disabled");
                        $("#submitIntroductoryAssessment").addClass("disabled");
                         $('#validationErrors').hide();
                         $('#submitIntroductoryAssessment').hide();
                         $('#saveIntroductoryAssessment').hide();
                         $('#score').show();
                         $('#score').html(data.score);
                    } else {
                        showErrorMsgInMsgBox(s, data);
//                        checkFormTabCompletion(s);
                    }

                }, function (s, msg, data) {
                   if (data != undefined && data != null && data.errors != undefined) {
                        $('#validationErrors').show();
                        addErrorNew("#validationErrors", data.errors);
                    } else {
                        addError("#validationErrors", data.message, '');
                    }
                } 
           );
//        }
        return false;
    });

        $(document).on("click",".filePrev.waiting .delete",function(){ 
		$(this).parent().remove();
	});
	$(document).on("click",".filePrev.uploaded .delete",function(){
		/*var f=$(this).parents(".filePrev");
		apiCall(f,"deleteFile",{"token":getToken(),"file_id":f.data("id")},function(s,data){f.remove();},function(s,msg){ alert(msg); });*/
		$(this).parent().remove();
                $("p").remove("#vtip");
                var uploadDoc = 0;
                 
                // $(".filesWrapper").each(function(){
                $('div','.filesWrapper').each(function(){
                     
                     if($(this).attr("id")!='undefined' || $(this).attr("id")!= '') {
                         uploadDoc++;
                     }
                     
                 });
                 if(uploadDoc < 1) {
                    // alert("no file");
                    //$('#pd_personal_tab').removeClass('completed');
                    //$('#activeStatus_tab').addClass('completed');
                 }
                 var par = $(this).parent().attr('id');
                 var arr = par.split('-');                 
                 if(arr[1] == "resume") {
                   // $('#biography_tab').removeClass('completed');
                    //$('#activeStatus_tab').addClass('completed');
                 }
		$("body").trigger('dataChanged');
	});
        $('.uploadBtn').on("change",function(){
            var uploadBtnId = $(this).attr('id');
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
                       // console.log("oooo"+fWrap);
                     
			for (var i = 0; i < files.length; i++) {
				var nArr=files[i].name.split(".");
				var ext=nArr.pop().toLowerCase();
				if(files[i].size>1048576000){
					alert("File too big. Max. 100MB allowed");
				}else if (nArr.length>0 && allowedExt.indexOf(ext)!=-1) {
					fWrap.append('<div class="filePrev waiting"><span class="delete fa"></span><div class="inner"></div></div>');
                                         //console.table(fWrap.find(".filePrev").last());
					uploadQueue.push({file:files[i],elem:fWrap.find(".filePrev").last(),ext:ext});
                                        
				} else {
					alert(files[i].name+" : file type not allowed. Only "+allowedExt.join(", ")+" type of files are allowed");					
				}
			}
			if(isFileUploading==false && uploadQueue.length>0){
				uploadFile(uploadBtnId);
                                //checkFormTabCompletion(this,'upload')
                                
                                
			}
		}
		return false;
	});
        
        $('#create_workshop_form .uploadBtnWorkshop,#edit_workshop_form .uploadBtnWorkshop').on("change",function(){
            var uploadBtnId = $(this).attr('id');
            var file_cat= $(this).attr('reltype');
          
            
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
			var fWrap=$(this).parents(".judgementS").find(".filesWrapper"+file_cat+"");
                       // console.log("oooo"+fWrap);
                     
			for (var i = 0; i < files.length; i++) {
				var nArr=files[i].name.split(".");
				var ext=nArr.pop().toLowerCase();
				if(files[i].size>1048576000){
					alert("File too big. Max. 100MB allowed");
				}else if (nArr.length>0 && allowedExt.indexOf(ext)!=-1) {
					fWrap.append('<div class="filePrev waiting"><span class="delete fa"></span><div class="inner"></div></div>');
                                         //console.table(fWrap.find(".filePrev").last());
					uploadQueue.push({file:files[i],elem:fWrap.find(".filePrev").last(),ext:ext});
                                        
				} else {
					alert(files[i].name+" : file type not allowed. Only "+allowedExt.join(", ")+" type of files are allowed");					
				}
			}
			if(isFileUploading==false && uploadQueue.length>0){
				uploadFile(uploadBtnId);
                                checkFormTabCompletion(this,'upload')
                                
                                
			}
		}
		return false;
	});
        
        $('.uploadResumeBtn').on("change",function(){
            var allowedExt1=["jpeg","png","gif","jpg","avi","mp4","mov","doc","docx","txt","xls","xlsx","pdf","csv",'xml','pptx','ppt','cdr','mp3','wav'];
            var uploadBtnId = $(this).attr('id');
               if(uploadBtnId=='profile_resume'){
                    var allowedExt = ["doc","docx","txt"];
                } else {
                    var allowedExt = allowedExt1;
                }
		if($(this)[0].files==undefined || typeof FileReader === "undefined"){
			alert("Sorry your browser does not support HTML5 file uploading");
		}else{
			//var files = $(this)[0].files;
                        var file = this.files[0];
                        name = file.name;
                        size = file.size;
                        type = file.type;
                       // alert(name);
			//m=files;
			var fWrap=$(this).parents(".judgementSResumes").find(".filesWrapperResumes");
			//for (var i = 0; i < files.length; i++) {
				var nArr=file.name.split(".");
				var ext=nArr.pop().toLowerCase();
				if(file.size>1048576000){
					alert("File too big. Max. 100MB allowed");
				}else if (nArr.length>0 && allowedExt.indexOf(ext)!=-1) {
					fWrap.html('<div class="filePrev waiting"><span class="delete fa"></span><div class="inner"></div></div>');
                                        //var resObj = fWrap.find(".filePrev").last();
					uploadQueue.push({file:file,ext:ext});
				} else {
					alert(file.name+" : file type not allowed. Only "+allowedExt.join(", ")+" type of files are allowed");					
				}
			//}
			if(isFileUploading==false && uploadQueue.length>0){
				uploadFile(uploadBtnId);
                                checkFormTabCompletion(this,'upload')
			}
		}
		return false;
	});
	
	
    $(document).on("click", "#saveUserProfile", function () {
       
            var postData = $('#userProfileForm').serialize()+"&user_profile=1"+ "&token=" + getToken();
            var s = $('#userProfileForm');
           // var pData = $(this).serialize() + "&token=" + getToken();
              updateUser(postData,s,function(output){
                    //alert("ok"+output.client_id);
                    if(output.status = 1 && output.client_id >=1 ) {
                        updateUserProfile(postData,s);
                    }
            });
        
        return false;
    });
    
    $(document).on("click", "#saveUserAssessment", function () {
       
            var postData = $('#userAssessmentForm').serialize();

            apiCall($('#userAssessmentForm'), "updateUserAssessment", postData + "&token=" + getToken(),
                function (s, data) {

                    $("body").trigger('dataChanged');

                    if (($('#table').val() == 'd_aqs_team' || $('#table').val() == 'd_AQS_team') && data.status == 1) {
                        //window.location.href = '?controller=login&action=login'
                        webApiCall(this,"loginApi",postData+"&autologin=login","api=1",
                            function(s,data){
                                    //showSuccessMsgInMsgBox(s,data);
                                    //alert(data.message);
                            window.location=data.message;

                        },showErrorMsgInMsgBox);
                    } else {
                        var s = $("#userProfileForm");
                        $("#saveUserProfile").prop('disabled', true);
//                        showSuccessMsgInMsgBox(s, data);
//                        if ($(".user-list").length) {
//                            filterByAjax($(".user-list"));
//                        }
                        showSuccessMsgInMsgBox(s, data);
                        s.find('.errorRed').removeClass('errorRed');
                        $('#validationErrors').hide();
//                        checkFormTabCompletion(s);
                    }

                }, function (s, msg, data) {
                    //console.log(data)
                    if (data != undefined && data != null && data.errors != undefined) {
                        $('#validationErrors').show();
//                        var fp = false;
                        //var i=0;
                        addErrorNew("#validationErrors", data.errors);
//                        for (var prop in data.errors) {
//                            if(i==0){
//                                $('#'+prop).focus();
//                            }  
//                            $('#'+prop).addClass('errorRed');
//                            fp = fp ? fp : prop;
//                            addErrorNew("#validationErrors", data.errors[prop], prop);
//                            i++;
//                        }
                    } else {
                        addError("#validationErrors", data.message, '');
                    }
                }, function (s, d) {
                    $("body").trigger('dataChanged');
                }
            );
//        }
        return false;
    });

    $('#termscondition_tab').click(function () {
        $("#userProfileForm").find('.transLayer .tc_wrapper #tab_terms .nav-tabs #tc_contract_tab').addClass('active');
        $("#userProfileForm").find(".transLayer .tc_wrapper .tab-content #tc_contract").addClass('active');
        $("#tc_contract").addClass('in');
        //$("#tc_contract").addClass('in');
        $("#fc_contract").removeClass('active in');
        $("#fc_contract_tab").removeClass('active');
    });
    $('#termscondition_tab_refer').click(function () {
          $("#tc_contract").addClass('in');
    });
    
    $('#profileDtls_tab').click(function () {
        $("#userProfileForm").find('.transLayer .tc_wrapper #tab_terms .nav-tabs #pd_personal_tab').addClass('active');
        $("#userProfileForm").find(".transLayer .tc_wrapper .tab-content #pd_personal").addClass('active');
    });
    $(document).on("change", "#selectClientBtn,#userProfileForm select,#userProfileForm input[type=text],#userProfileForm input[type=checkbox],#userProfileForm input[type=radio],#userProfileForm textarea,#userProfileForm input[type=password]", function () {
        $("body").trigger('dataChanged');
        // var p = $(this).closest(".tab-pane").first();
        checkFormTabCompletion(this)
    });
    
     $(document).on("change", "#userAssessmentForm select,#userAssessmentForm input[type=text],#userAssessmentForm input[type=checkbox],#userAssessmentForm input[type=radio],#userAssessmentForm textarea,#userAssessmentForm input[type=password]", function () {
        $("body").trigger('dataChanged');
        // var p = $(this).closest(".tab-pane").first();
       // checkFormTabCompletion(this)
    });
     
    $(document).on("dataChanged", "body", function () {
        $("#saveUserProfile").removeAttr("disabled");
        $("#submitUserProfile").removeAttr("disabled");
        $("#saveIntroductoryAssessment").removeClass("disabled");
        $("#submitIntroductoryAssessment").removeClass("disabled");
        
    });

    $(document).on('click','#user_dob',function(){ 
       // alert("ok"+$(this).val());
       var dob_yy = $("#dob_yy").val();
       var dob_mm = $("#dob_mm").val();
       var isLeap=0;
       if(dob_yy !='') {
           if(((dob_yy%4 == 0) && (dob_yy%100!=0)) || (dob_yy%400 == 0)) {
                //alert(dob_yy);
                isLeap = 1;
            }
    }
    //alert(isLeap);
       if(isLeap != 1 && dob_mm !='' && dob_mm == 2) {
        
        $("#dob_dd option[value='30']").hide();
        $("#dob_dd option[value='31']").hide();
        $("#dob_dd option[value='29']").hide();
     }else if(isLeap == 1 && dob_mm !='' && dob_mm == 2) {
   
        $("#dob_dd option[value='30']").hide();
        $("#dob_dd option[value='31']").hide();
        $("#dob_dd option[value='29']").show();
     }else  {
        
          $("#dob_dd option[value='30']").show();
          $("#dob_dd option[value='31']").show();
     }
    

   });
   

});
(function ($) {
    // code for counting words for textarea on 27-05-2016 by Mohit Kumar
    $.fn.textareaCounter = function (options) {
        // setting the defaults
        // $("textarea").textareaCounter({ limit: 100 });
        var defaults = {
            limit: 50
        };
        var options = $.extend(defaults, options);

        // and the plugin begins
        return this.each(function () {
            var obj, text, wordcount, limited;

            obj = $(this);
            obj.after('<span style="font-size: 11px; clear: both; margin-top: 3px; display: block;" id="counter-text">Max. ' + options.limit + ' words</span>');

            obj.keyup(function () {
                text = obj.val();
                if (text === "") {
                    wordcount = 0;
                } else {
                    wordcount = $.trim(text).split(" ").length;
                }
                if (wordcount > options.limit) {
                    $("#counter-text").html('<span style="color: #DD0000;">0 words left</span>');
                    limited = $.trim(text).split(" ", options.limit);
                    limited = limited.join(" ");
                    $(this).val(limited);
                } else {
                    $("#counter-text").html((options.limit - wordcount) + ' words left');
                }
            });
        });
    };
})(jQuery);

//function to check facilitator term and statement status
function facilitatorTermConditionStatus(checkCount,numofCheckBoxes){
    
    if(checkCount == numofCheckBoxes){
        return true;
    }else {
        return false;
    }
}
//function for disable tabs except term and condition tab on 02-06-2016 by Mohit Kumar
function userProfileDisabled() {
    var f = $('#userProfileForm');
    f.find(".subTabWorkspace .tab-pane:gt(0) *,.ylwRibbonHldr .tabitemsHldr .nav .item:gt(0) *").each(function () {
        $(this).attr('disabled', 'disabled');
        $(this).css('pointer-events', 'none');
    });
    $('#term_condition').val(0);
    $('#tc_agreement').find('div').removeAttr('disabled');
    $('#tc_agreement').find('p').removeAttr('disabled');
    $('#tc_agreement').find('h4').removeAttr('disabled');
    $('#tc_agreement').find('b').removeAttr('disabled');
    $('#tc_contract').find('div').removeAttr('disabled');
    $('#tc_contract').find('p').removeAttr('disabled');
    $('#tc_contract').find('ul').removeAttr('disabled');
    $('#tc_contract').find('ol').removeAttr('disabled');
    $('#tc_contract').find('li').removeAttr('disabled');
    $('#tc_contract').find('h4').removeAttr('disabled');
    $('#tc_contract').find('b').removeAttr('disabled');
    $('#tc_contract').find('a').removeAttr('disabled').css('pointer-events', 'auto');
    $('.contract_value').removeAttr('disabled').css('pointer-events', 'auto');
    $('a#tap_overview').removeAttr('disabled').css('pointer-events', 'auto');
    $('a#Code_of_Conduct').removeAttr('disabled').css('pointer-events', 'auto');
}
function userProfileDisabledFc() {
  
    $('#fc_contract').find('div').removeAttr('disabled');
    $('#fc_contract').find('p').removeAttr('disabled');
    $('#fc_contract').find('ul').removeAttr('disabled');
    $('#fc_contract').find('ol').removeAttr('disabled');
    $('#fc_contract').find('li').removeAttr('disabled');
    $('#fc_contract').find('h4').removeAttr('disabled');
    $('#fc_contract').find('b').removeAttr('disabled');
    $('#fc_contract').find('a').removeAttr('disabled').css('pointer-events', 'auto');
    $('.contract_value_fc').removeAttr('disabled').css('pointer-events', 'auto');
    $('a#tap_overview').removeAttr('disabled').css('pointer-events', 'auto');
    $('a#Code_of_Conduct').removeAttr('disabled').css('pointer-events', 'auto');
}

//function to upload file
function uploadFile(uploadBtnId){
    
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
                            
                            if(uploadBtnId == 'profile_resume') {
                                
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
						$(".filePrev").attr("id","file-resume-"+rData.id).removeClass("uploading").removeClass("waiting").addClass("uploaded vtip ext-"+rData.ext).data("id",rData.id).attr("title",rData.name);
                                                $(".filePrev").append('<input type="hidden" value="'+rData.id+'" name="profile_resume" />');
                                                $(".filePrev").find(".inner").html('<a target="_blank" href="'+rData.url+'"> </a>');
						$("body").trigger('dataChanged');
					}else{
						alert("Error while uploading file '"+fileElm.file.name+"': "+rData.message);
						fe.remove();
					}
				}else{
					alert("Unknown error occurred while uploading file '"+fileElm.file.name+"'.");
					fe.remove();
					uploadFile(uploadBtnId);
				}
                            }else if(uploadBtnId == 'actionplan'){
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
                                                $(".filePrev").removeClass("uploading").removeClass("waiting");
						fe.attr("id","file-"+rData.id).removeClass("uploading").addClass("uploaded vtip ext-"+rData.ext).data("id",rData.id).attr("title",rData.name).append('<input type="hidden" value="'+rData.id+'" name="data['+fe.parents(".kpa").data("id")+'-'+fe.parents(".keyQ").data("id")+'-'+fe.parents(".coreQ").data("id")+'-'+fe.parents(".judgementS").data("id")+'][files][]" />');
						fe.find(".inner").html('<a target="_blank" href="'+rData.url+'"> </a>');
						$("body").trigger('dataChanged');
						uploadFile(uploadBtnId);
					}else{
						alert("Error while uploading file '"+fileElm.file.name+"': "+rData.message);
						fe.remove();
						uploadFile(uploadBtnId);
					}
				}else{
					alert("Unknown error occurred while uploading file '"+fileElm.file.name+"'.");
					fe.remove();
					uploadFile(uploadBtnId);
				}
                            }
                            else if(uploadBtnId.indexOf('workshop_upload') !== -1) {
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
						fe.attr("id","file-"+rData.id).removeClass("uploading").addClass("uploaded vtip ext-"+rData.ext).data("id",rData.id).attr("title",rData.name).append('<input type="hidden" value="'+rData.id+'" class="filesNames" name="workshop_upload[]" /><input type="hidden" value="'+file_cat+'" name="workshop_upload_cat[]" />');
						fe.find(".inner").html('<a target="_blank" href="'+rData.url+'"> </a>');
                                                var matches = 0;
                                                $(".filesWrapper"+file_cat+" .filesNames").each(function(i, val) {
                                                  matches++;   
                                                });
						$("#count_"+file_cat+"").html("Total Files:"+matches+"");
                                                $("body").trigger('dataChanged');
						$("body").trigger('dataChanged');
                                                isFileUploading=false;
					}else{
						alert("Error while uploading file '"+fileElm.file.name+"': "+rData.message);
						fe.remove();
					}
				}else{
					alert("Unknown error occurred while uploading file '"+fileElm.file.name+"'.");
					fe.remove();
					uploadFile(uploadBtnId);
				}
                            }else{
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
						fe.attr("id","file-"+rData.id).removeClass("uploading").addClass("uploaded vtip ext-"+rData.ext).data("id",rData.id).attr("title",rData.name).append('<input type="hidden" value="'+rData.id+'" name="data['+fe.parents(".kpa").data("id")+'-'+fe.parents(".keyQ").data("id")+'-'+fe.parents(".coreQ").data("id")+'-'+fe.parents(".judgementS").data("id")+'][files][]" />');
						fe.find(".inner").html('<a target="_blank" href="'+rData.url+'"> </a>');
						$("body").trigger('dataChanged');
						uploadFile(uploadBtnId);
					}else{
						alert("Error while uploading file '"+fileElm.file.name+"': "+rData.message);
						fe.remove();
						uploadFile(uploadBtnId);
					}
				}else{
					alert("Unknown error occurred while uploading file '"+fileElm.file.name+"'.");
					fe.remove();
					uploadFile(uploadBtnId);
				}
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
				uploadFile(uploadBtnId);
			},
			xhrFields: {
				onprogress:function(e){
					var perc = parseInt(e.loaded / e.total * 100, 10);
					//console.log(perc);
				}
			}
		});
	}

// function for enable the user profile
function userProfileEnable() {
    var f = $("#userProfileForm");
    f.find(".subTabWorkspace .tab-pane:gt(0) *,.ylwRibbonHldr .tabitemsHldr .nav .item:gt(0) *").each(function () {
        $(this).removeAttr('disabled');
        $(this).css('pointer-events', 'auto');
    });

    if ($('#date_picker').hasClass('date') == false) {
        $('#date_picker').addClass('date');
    }
   // f.find('div#tc_contract').addClass('tab-pane fade in active');
    $('.date').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false, maxDate: new Date, pickTime: false});
    $('#agreeRow').hide();
    $('#termsRow').show();
    $('.languageAddRow').show();
    $('.uploadBtn').removeAttr('disabled');
    f.find("#term_condition").val(1);

//    $('#tc_agreement').show();
}
// function for focus on next tab when user accept the term & condition
function focusUserProfileTab(e) {
    var f = $("#userProfileForm");
    f.find(".nav-tabs li.item.active,.tab-pane.active").removeClass("active");
//    f.find("li.item a[href=#"+$(e).parents(".tab-pane").first().addClass("fade in active").attr("id")+"]").parent().addClass("active");
    /*f.find('#profileDtls_tab').addClass('active');
    f.find("#profileDtls").addClass('in active');
    f.find("#pd_personal_tab").addClass('active');
    f.find("#pd_personal").addClass('active');*/
     //f.find('#profileDtls_tab').addClass('active');
    //f.find("#profileDtls").addClass('in active');
    f.find("#pd_personal_tab").addClass('active');
    f.find("#pd_personal").addClass('active');
    $(e).focus();
}

// function for enter on numbers
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

// function for enter on alphabats
function isLetterKey(event) {
    var inputValue = event.charCode;
    if (!(inputValue >= 65 && inputValue <= 124) && (inputValue != 32 && inputValue != 0)) {
        event.preventDefault();
    }
}

// function for adding language from user profile page on 27-06-2016 by Mohit Kumar
function addNewLanguage(input_id, i, value) {
    if (value == 'other') {
        $('#other_language_div' + i).show();
        $('#other_language' + i).removeAttr('disabled').focus();
    } else {
        $('#other_language_div' + i).hide();
        $('#other_language' + i).val('');
        $('#other_language' + i).attr('disabled', 'disabled');
    }
}

function filterByAjax(s, page, orderBy, ordertype) {
    var f = $(s).hasClass("filterByAjax") ? $(s) : $(s).parents(".filterByAjax");
    if (f.data("controller") != undefined && f.data("action") != undefined) {
        var a = f.data("action");
        var c = f.data("controller");

        var querystring = f.data("querystring") == undefined ? "" : f.data("querystring");
        if ($('#id').val() != '' && $('#client_id').val() != '') {
            querystring = querystring + "id=" + $('#id').val() + "&client_id=" + $('#client_id').val();
        }
        var cPage = f.find(".paginHldr .paging.active,.paging_wrap .paging.active").data("value");
        page = page == undefined ? (cPage == undefined ? 1 : cPage) : page;
        var postData = {page: page};
        $(f).find(".ajaxFilter").each(function (i, e) {
            var n = $(e).attr("name");
            var v = page > 1 ? $(e).data("value") : $(e).val();
            if (n != undefined && n != "" && v != "")
                postData[n] = v;
        });

        $(f).find(".ajaxFilterAttach").each(function (i, e) {
            var n = $(e).attr("name");
            var v = $(e).val();
            if (n != undefined && n != "" && v != "") {
                if (n.indexOf("[") > 0 && n.indexOf("]") > 0) {
                    n = n.substring(0, n.indexOf("["));
                    if (postData[n] == undefined)
                        postData[n] = [];
                    postData[n].push(v);
                } else
                    postData[n] = v;
            }
        });

        if (orderBy != undefined) {
            postData['order_by'] = orderBy;
        } else if (f.find(".sorted_desc,.sorted_asc").length > 0) {
            postData['order_by'] = f.find(".sorted_desc,.sorted_asc").data("value");
        }

        postData['order_type'] = ordertype != undefined ? ordertype : (f.find(".sorted_desc").length > 0 ? "desc" : "asc");
        // pass user id for gettig the user data on 16-05-2016 by Mohit Kumar
        //postData['id']=id;

//                postData['id']=id;

        // postData['id']=id;

        ajaxCall(f, c, a, postData, querystring, function (s, data) {
            sessionStorage.clear();
            for (key in postData)
                sessionStorage.setItem("pFilter_" + key, postData[key]);

            s.replaceWith(data.content);
            $(".modal-dialog .page-title").remove();
        }, showErrorMsgInMsgBox);
    }
}

// function for check all tabs are completely filled or not

function checkFormTabCompletion(sender) {
    var p = $(sender).closest(".tab-pane").first();
    var tabId = p.attr('id');
    //alert("okddddd"+tabId);
//    console.log(tabId)
    var emptyFields = 0;
    $("a[href=#"+p.attr("id")+"]").parent().removeClass("completed");
    var notIncludeField = '';
    if(tabId=='pd_personal'){
        var language = $('#userProfileForm select[name^="languageData"]').length;
        
        var languageOther = [];
        for(var i=1;i<=language;i++){
            languageOther[i]='#other_language'+i;
        }
        var languageOther1 = languageOther.join(',');
        notIncludeField="input[type='password'],input[type='hidden'],#whatsapp_num,#other_medical_text,#other_hobbies_text"+languageOther1;
    } else {
        notIncludeField="input[type='password'],input[type='hidden'],#other_medical_text,#other_hobbies_text,#travel_sickness_text";
    }
    p.find("input[type='text'],select,input[type='email'],textarea").not(notIncludeField).each(function(){	
       if($(this).val()=='' || $(this).val()==null )
        {
            emptyFields++;
            return;
        }
    });
     if(tabId=='tc_contract'){       
         
            p.find(".contract_value").not(notIncludeField).each(function(){
           
            if(!$(this).is(":checked")){
                 //alert("ok");
                 emptyFields++;
                 return;
            }
            
        });
     }if(tabId=='fc_contract'){       
         
            p.find(".contract_value").not(notIncludeField).each(function(){
           
            if(!$(this).is(":checked")){
                 //alert("ok");
                 emptyFields++;
                 return;
            }
            
        });
     }
    if(tabId=='pd_personal'){
        var languageLength = $('#userProfileForm select[name^="languageData"]').length;
        for(var i=1; i<=languageLength;i++){
            if(isValidText(p.find('#language_id'+i).val()) &&
                    (p.find('#language_speak'+i).is(':checked') ||
                     p.find('#language_read'+i).is(':checked') ||
                     p.find('#language_write'+i).is(':checked'))){
                if(p.find('#language_id'+i).val()=='other' && !isValidText(p.find('#other_language'+i).val()))
                {
                    emptyFields++;
                    return;
                }
            } else {
                emptyFields++;
                return;
            }
          /*  var uploadDocId;
            $(".filesWrapper > div").each(function(){
                uploadDocId = $(this).attr("id");
            });
            if(typeof uploadDocId === 'undefined' || uploadDocId == '') {
                emptyFields++;
                return;
            }*/
        } 
        p.find(".user-roles").each(function(){				
        var chkBox = $(this).first().attr('name');
        //alert(chkBox);        
        if(!$('[name="'+chkBox+'"]:checked').length)
        {
            //alert(radioBtn);
            emptyFields++;
            return;
        }				
    });
        
//        console.log(languageLength)
    }
    if(tabId=='additionalInfo'){
        var numMedicalConditions = p.find("input[name^='medical_conditions']:checked").length; 
        var medicalSickness = p.find("input[name^='travel_sickness']:checked").val();
        if(medicalSickness == "yes") {
            if(!isValidText(p.find('#travel_sickness_text').val())==true){
                emptyFields++;
                return;
            }
        }
        if(numMedicalConditions>=1){
            if(p.find('#medical_7').is(':checked') && !isValidText(p.find('#other_medical_text').val())==true){
                emptyFields++;
                return;
            }
        }else {
            emptyFields++;
            return;
        }
    }
    if(tabId=='biography'){
        var numhobbies = p.find("input[name^='hobbies[]']:checked").length;
        if(numhobbies>=1){
            if(p.find('#hobbies_11').is(':checked') && !isValidText(p.find('#other_hobbies_text').val())==true){
                emptyFields++;
                return;
            }
        } else {
            emptyFields++;
            return;
        }
        var uploadResId;
      /* $(".filesWrapperResumes > div").each(function(){
                uploadResId = $(this).attr("id");
        });
        if(typeof uploadResId === 'undefined' || uploadResId == '') {
            emptyFields++;
            return;
        }*/
    }

    p.find(".radioClass").each(function(){				
        var radioBtn = $(this).first().attr('name');
        
        if(!$('[name="'+radioBtn+'"]:checked').length)
        {
            //alert(radioBtn);
            emptyFields++;
            return;
        }				
    });
   // alert(emptyFields);
//    console.log(emptyFields)
    if(tabId=='tc_contract' || tabId=='fc_contract' ){
        if(emptyFields==0){
            $('#termscondition_tab').addClass('completed');
            $('#activeStatus_tab').addClass('completed');
        } else {
            $('#termscondition_tab').removeClass('completed');
            $('#activeStatus_tab').removeClass('completed');
        }
    } else if(tabId=='introductoryAssessment'){
        if(emptyFields==0){
            $('#introductoryAssessment_tab').addClass('completed');
        } else {
            $('#introductoryAssessment_tab').removeClass('completed');
        }
    } else {
        /*var medicalSickness = p.find("input[name^='travel_sickness']:checked").val();
        if(medicalSickness == "no") {
            emptyFields--;
        }*/
        if(emptyFields==0){
            $("a[href=#"+p.attr("id")+"]").parent().addClass("completed");
        } else {
            $("a[href=#"+p.attr("id")+"]").parent().removeClass("completed");
        }
        if($('#pd_personal_tab').hasClass('completed') && $('#emergencyContact_tab').hasClass('completed') &&
                $('#additionalInfo_tab').hasClass('completed') && $('#biography_tab').hasClass('completed'))
        {
            $('#profileDtls_tab').addClass('completed');
        } else {
            $('#profileDtls_tab').removeClass('completed');
        }
        $('#reviewDtls_tab').removeClass('completed');
    }
}

// function for checking language is already exist not
function checkLanguageExist(value,index){
    if(value==''){
        $('#other_language'+index).val('').focus().css({'border': '1px solid red'});
    } else {
        var languageCount = $('#userProfileForm select[name^="languageData"]').length;
        var languageOther = [];
        var j=0;
        for(var i=1;i<=languageCount;i++){
            if(index!=i){
                languageOther[j]=$('#other_language'+i).val().toLowerCase();
                j++;
            }
        }
        if($.inArray(value.toLowerCase(),languageOther)==-1 || languageOther.length==0){
            apiCall(this,"checkLanguageExist",{"token":getToken(),'language':value,'languageCount':languageCount},
                function(s,data){
                    $('#other_language'+index).css({'border': '1px solid #7d6702'});
                }, function (s, msg, data) {
                    addError("#validationErrors", data.message, '');
                }
            );
        } else {
            alert('This language is already exist. Please enter new language!')
            $('#other_language'+index).val('').focus();
        }
        
    }
}

// function for displaying the error msgs on 29-07-2016 by Mohit Kumar
function addErrorNew(c,m){
    var fp = false;
    var i=0;
    var div = '';
    for (var prop in m) {
        if(i==0){
            $('#'+prop).focus();
        }  
        $('#'+prop).addClass('errorRed');
        fp = fp ? fp : prop;
        var hasKey=prop==undefined || prop==null || prop=="" || $("#aqsf_"+prop).length==0?false:true;
        div+='<div class="alert alert-danger mt25 alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="'+(hasKey?'hasKey':'')+'" data-id="'+(hasKey?prop:'')+'">'+m[prop]+'</span></div>';
        i++;
    }
    $(c).html(div);
}

// function for open text box for other option
function openOtherTextBox(id,type){   
    
    if (type == 'travel_sicknes') {
        
        if (id == 'travel_sickness_yes') {
            $('#other_' + type + '_div').show();
        } else {
            $('#other_' + type + '_div').hide();
        }
        //$('#other_' + type + '_text').val('');
    } else {
        if ($('#' + id).is(':checked')) {
            $('#other_' + type + '_div').show();
        } else {
            $('#other_' + type + '_div').hide();
        }
        $('#other_' + type + '_text').val('');
    }
}

function updateUserProfile(postData,s) {
    
    apiCall($('#userProfileForm'), "updateUserProfile", postData + "&token=" + getToken(),
                function (s, data) {

                    $("body").trigger('dataChanged');

                    if (($('#table').val() == 'd_aqs_team' || $('#table').val() == 'd_AQS_team') && data.status == 1) {
                        //window.location.href = '?controller=login&action=login'
                        webApiCall(this,"loginApi",postData+"&autologin=login","api=1",
                            function(s,data){
                                    //showSuccessMsgInMsgBox(s,data);
                                    //alert(data.message);
                            window.location=data.message;

                        },showErrorMsgInMsgBox);
                    } else {
                        var s = $("#userProfileForm");
                        $("#saveUserProfile").prop('disabled', true);
//                        showSuccessMsgInMsgBox(s, data);
//                        if ($(".user-list").length) {
//                            filterByAjax($(".user-list"));
//                        }
                        showSuccessMsgInMsgBox(s, data);
                        s.find('.errorRed').removeClass('errorRed');
                        $('#validationErrors').hide();
//                        checkFormTabCompletion(s);
                    }

                }, function (s, msg, data) {
                    //console.log(data)
                    if (data != undefined && data != null && data.errors != undefined) {
                        $('#validationErrors').show();
//                        var fp = false;
                        //var i=0;
                        addErrorNew("#validationErrors", data.errors);
//                        for (var prop in data.errors) {
//                            if(i==0){
//                                $('#'+prop).focus();
//                            }  
//                            $('#'+prop).addClass('errorRed');
//                            fp = fp ? fp : prop;
//                            addErrorNew("#validationErrors", data.errors[prop], prop);
//                            i++;
//                        }
                    } else {
                        $("#validationErrors").show();
                        $('#validationErrors').html('');
                        addError("#validationErrors", data.message, '');
                    }
                }, function (s, d) {
                    $("body").trigger('dataChanged');
                }
            );
//        }
    
}
	