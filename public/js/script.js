var allAssessors = [];
var allFacilitators = [];
var reviewType = 0;
var isFilter = 0;
var networks=[];
var provinces=[];
 var substringMatcher = function(strs) {
        return function findMatches(q, cb) {			
        var matches, substringRegex;		
          matches = [];		
			//console.log(s);
          substrRegex = new RegExp("^"+q.trim(), 'i');
          $.each(strs, function(i, str) {
            if (substrRegex.test(str)) {
              matches.push(str);
            }
          });
          cb(matches);
        };
      };
var logger = function ()
{
    var oldConsoleLog = null;
    var pub = {};

    pub.enableLogger = function enableLogger()
    {
        if (oldConsoleLog == null)
            return;

        window['console']['log'] = oldConsoleLog;
    };

    pub.disableLogger = function disableLogger()
    {
        oldConsoleLog = console.log;
        window['console']['log'] = function () {};
    };

    return pub;
}();
$(document).ready(function () {
    $(document).on("keypress", ".mask_ph ,.aqs_ph", function (e) {
         //alert("ok"+$(this).val())
         var val = $(this).val();
       //alert(e.which );
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)  ) {
         
                 //display error message        
                $("#errmsg").html("Digits Only").show().fadeOut("slow");
               return false;
    }if((e.which >= 47 && e.which < 58) && val.length  > 14) {
        
        return false;
    }
    
   });
    $(document).on("focusout", ".mask_ph ,.aqs_ph", function (e) {
         //alert("ok"+$(this).val())
         var val = $(this).val();
         if(val.length < 3 ){
             $(this).val('');
         }
   });
    function modalWorkSpace(){
        $('.modal .modal-content .subTabWorkspace').css('max-height', $(window).height() - 150 + 'px');
    }
    modalWorkSpace();
    $(window).resize(function(){
        modalWorkSpace();
    });

//        alertCounts();
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('#scroll').fadeIn();
        } else {
            $('#scroll').fadeOut();
        }
    });
    $('#scroll').click(function () {
        $("html, body").animate({scrollTop: 0}, 600);
        return false;
    });
    $('[disabled] .vtip').removeClass('vtip');
    $('[disabled] a,[disabled] span').css('cursor', 'not-allowed');
    $('[disabled] a,[disabled] i').attr('title', '');
    $('[disabled] a').attr('href', '');
    $('[disabled] a').on('click', function (e) {
        e.preventDefault();
    });
    logger.disableLogger();
    logger.enableLogger();
    var arrFilter = document.location.search.slice(1).split('&');
    for (var temp = 0; temp < arrFilter.length; temp++)
        if (arrFilter[temp].indexOf("filter") >= 0)
            isFilter = (((arrFilter[temp]).split("="))[1]) == 1 ? 1 : 0;
    //if user comes back via clicking chevron, filter is retained
    if (isFilter) {
        //$(document).find("[name='client_name']").val(localStorage.getItem("filter_cname")).data('value',localStorage.getItem("filter_cname"));		
        var f = $(document).find('.filters-bar');
        $(f).find(".ajaxFilter").each(function (i, e) {
            var n = $(e).attr("name");
            if (sessionStorage.getItem("pFilter_" + n) !== null)
                $(e).val(sessionStorage.getItem("pFilter_" + n)).data("value", sessionStorage.getItem("pFilter_" + n));

        });

        filterByAjax($(document).find('.filters-bar'), sessionStorage.getItem("pFilter_page"));
    }

    if ($.isFunction($(document).datetimepicker)) {
        $('.datePicker').datetimepicker({format: 'DD-MM-YYYY', useCurrent: false,pickTime: false});
        $('.date-Picker').datetimepicker({format: 'DD-MM-YYYY',useCurrent: false, pickTime: false});
    }

    $(".page-loading-class").removeClass('page-loading-class').find(".page-loading").removeClass("page-loading");

    $(document).on("click", ".unlinkClient", function () {
        apiCall(this, "removeClientFromNetwork", {"client_id": $(this).data("id"), "network_id": $(this).data("nid"), "token": getToken()}, function (s, data) {
            var nid = $(s).data("nid");
            $(s).parents("tr").first().remove();
            $("td#clientCountFor-" + nid).each(function(){
            	$(this).text($(this).text() - 1);
            });
            if($(".network-list").length)
                filterByAjax($(".network-list"));
        }, function (s, msg) {
            alert(msg);
        });
        return false;
    });
    $(document).on("click", ".unlinkClientFromProvince", function () {
        apiCall(this, "removeClientFromProvince", {"client_id": $(this).data("id"), "province_id": $(this).data("pid"), "token": getToken()}, function (s, data) {
            var pid = $(s).data("pid");
            $(s).parents("tr").first().remove();
            $("#clientInProvinceCountFor-" + pid).text($("#clientInProvinceCountFor-" + pid).text() - 1);
            if($(".network-list").length)
                filterByAjax($(".network-list"));
        }, function (s, msg) {
            alert(msg);
        });
        return false;
    });
    $(document).on('submit',"#clone_diagnostic_frm",function(e){
    	var diagnosticId = $(this).find("#id_diagnostic_id").val();
    	var diagnosticName = $(this).find("#id_cloned_diagnostic_name").val();
        var langId = $(this).find("#langId").val();
    	 apiCall(this, "cloneDiagnostic", {"token": getToken(),"diagnosticId":diagnosticId,"diagnosticName":diagnosticName,"langId":langId},
                 function (s, data) {
    		 		filterByAjax($(".diagnostic-list"));
    		 		showSuccessMsgInMsgBox(s, data);                     
                    
                 }, showErrorMsgInMsgBox);
    	 return false;
    });
    $(document).on("click", ".clientSelected", function () {
        var cid = $(this).data('id');
        var contnr = $(this).parents(".filterByAjax.client-list").data('for') != undefined ? "#" + $(this).parents(".filterByAjax.client-list").data('for') + " " : "";
        $(contnr + "#selected_client_id").val(cid);
        $(contnr + "#selected_client_name").html($(this).parents("tr").first().find(".client_name").text());
        $(contnr + "#selectClientBtn").text("Change");
        if ($(contnr + ".school_admin_id").length > 0) {
            var aDd = $(contnr + ".school_admin_id");
            aDd.find("option").next().remove();
            var aDd1 = $(contnr + ".student_round");
            aDd1.find("option").next().remove();
            if (cid > 0){
                apiCall(this, "getSchoolAdmins", {"token": getToken(), "client_id": cid}, function (s, data) {
                    addOptions(aDd, data.schoolAdmins, 'user_id', 'name')
                }, showErrorMsgInMsgBox);
            
            apiCall(this, "getrounds", {"token": getToken(), "client_id": cid}, function (s, data) {
                    addOptionsDisabled(aDd1, data.rounds, 'aqs_round', 'aqs_round',data.roundsUnused)
                }, showErrorMsgInMsgBox);
            }
        }
        $(this).parents(".modal").modal("hide");
         $("body").trigger('dataChanged');
    });
    $( document ).on( "click", ".notificationOption input[type='checkbox']", function() {
         //var notifcationClass = $(this).attr("class");
         var notifcationId = $(this).attr("id");
         var notifcationVal = $(this).attr("value");
         //alert(notifcationVal);
         var classId = $(this).attr("class");
         ids = notifcationId.split("-");
         if(ids[0] == 'assessor_peer_reimbursement' && $(this).prop('checked')) {
             $('#assessor_peer_feedback-'+ids[1]).prop('checked', true);
         }else if(ids[0] == 'assessor_peer_feedback' && !$(this).prop('checked')) {
                $('#assessor_peer_reimbursement-'+ids[1]).prop('checked', false);
                $('.remindr-'+classId).prop('checked', false);
                //alert(notifcationId);
         }else if(ids[0] == 'assessor_peer_reimbursement' && !$(this).prop('checked')) {
                $('#'+ids[0]+"_"+'remindr-'+ids[1]).prop('checked', false);
         }
        
    });
    $( document ).on( "click", ".remindrOption input[type='checkbox']", function() {
         //var notifcationClass = $(this).attr("class");
         var notifcationId = $(this).attr("id");
         ids = notifcationId.split("-");
         if(ids[0] == 'assessor_peer_reimbursement_remindr' && $(this).prop('checked')) {
             $('#assessor_peer_feedback_remindr-'+ids[1]).prop('checked', true);
         }else if(ids[0] == 'assessor_peer_feedback_remindr' && !$(this).prop('checked')) {
                $('#assessor_peer_reimbursement_remindr-'+ids[1]).prop('checked', false);
                //alert(notifcationId);
         }
        
    });
    $(document).on('keyup','#search_file',function(e) { 
        
        var searchValue = $("#search_file").val();
        if(searchValue.length>=3){
            
            //alert("ok");
            postData = "page=1&search_val= "+searchValue+"&token:"+ getToken();
            var querystring = '';
            ajaxCall('#search_file_form', 'resource', 'searchResourceFiles', postData,querystring, function (s, data) {
               $("#resourceData").html(data.content);
               // var aDd = $(contnr).find("#diagnostic_lang_id");
               // aDd.find("option").next().remove();
                //addOptions(aDd, data.langDiagnostics, 'language_id', 'language_words');
               // $(contnr).find("#diagnostic_lang_id").show();
            }, showErrorMsgInMsgBox);
            //alert("ok"+searchValue);
        }
    })
    
     /*$(document).on("change", "#create_school_assessment_form #diagnostic_lang_id", function () {
        var contnr = $(this).parents('form').first();
        var lang_id = $(this).val();
        if (lang_id !='' && lang_id !== null && lang_id !== undefined) {
            $('#errors').hide();
            var postData = "lang_id=" + lang_id + "&token=" + getToken();
            apiCall(this, "getLanguageDiagnostics", postData, function (s, data) {
                var aDd = $(contnr).find("#diagnostic_id");
                aDd.find("option").next().remove();
                addOptions(aDd, data.langDiagnostics, 'diagnostic_id', 'name');
                $(contnr).find("#diagnostic_id").show();
            }, showErrorMsgInMsgBox);
        } else {
            $('#errors').html('Please select a diagnostic language');
            $('#errors').show();
        }
    });*/
    
     $(document).on("change", "#create_school_assessment_form #diagnostic_id,#edit_school_assessment_form #diagnostic_id,#create_school_self_assessment_form #diagnostic_id,#edit_school_self_assessment_form #diagnostic_id", function () {
        var contnr = $(this).parents('form').first();
        var diagnostic_id = $(this).val();
        if (diagnostic_id !='' && diagnostic_id !== null && diagnostic_id !== undefined) {
            if(contnr.attr('id')=="create_school_self_assessment_form"){
            if(diagnostic_id=="72"){    
            $("#create_school_self_assessment_form .warn-show-hide").show();
            }else{
            $("#create_school_self_assessment_form .warn-show-hide").hide();    
            }
            }
            $('#errors').hide();
            var postData = "diagnostic_id=" + diagnostic_id + "&token=" + getToken();
            apiCall(this, "getDiagnosticLanguages", postData, function (s, data) {
                var aDd = $(contnr).find("#diagnostic_lang_id");
                aDd.find("option").next().remove();
                addOptions(aDd, data.langDiagnostics, 'language_id', 'language_words');
                $(contnr).find("#diagnostic_lang_id").show();
            }, showErrorMsgInMsgBox);
        } else {
            $('#errors').html('Please select a diagnostic');
            $('#errors').show();
            
            if(contnr.attr('id')=="create_school_self_assessment_form"){
            $("#create_school_self_assessment_form .warn-show-hide").hide();
           }
        }
    });
   
    $(document).on("click", "#converttopdf", function () {
        apiCall(this, "reportPdf", {"token": getToken()}, function (s, data) {
            console.log("success: " + data)
        }, showErrorMsgInMsgBox);
    })
    $(document).on("change", "#create_web_school_form .haveNetwork, #create_school_form .haveNetwork,#edit_school_form .haveNetwork", function () {
        $(this).parents("form").find(".haveNetwork:checked").val() == 1 ? $(this).parents("form").find("#networks").show().find("select").attr("required", "required").val('') : ($(this).parents("form").find("#networks").hide().find("select").removeAttr("required") && $(this).parents("form").find("#provinces").hide());
    });
    /*$(document).on("change", "#create_school_form #networks,#edit_school_form #networks", function () {
    	$(this).parents("form").find("#provinces").show().find("select").val('');
    });*/
   $(document).on("change", "#create_school_form #scl_network,#edit_school_form #edit_scl_network", function () {
        var contnr = $(this).parents('form').first();
        var network_id = $(this).val();
        if (network_id !='' && network_id !== null && network_id !== undefined) {
            $('#errors').hide();
            var postData = "network_id=" + network_id + "&token=" + getToken();
            apiCall(this, "getProvincesInNetwork", postData, function (s, data) {
                var aDd = $(contnr).find("#provinces .province-list-dropdown");
                aDd.find("option").next().remove();
                addOptions(aDd, data.message, 'province_id', 'province_name');
                $(contnr).find("#provinces").show();
            }, showErrorMsgInMsgBox);
        } else {
            $('#errors').html('Please select a network');
            $('#errors').show();
        }
    });
    
    //for collaborative review
   /* $(document).on('change','#create_school_assessment_form #review_type',function(){ 
        
        var reviewType = $("#review_type").val();
        
        if(reviewType!=undefined && reviewType!='' && reviewType == 1) {
            $("#collaborative-step2").show();
            //alert("ok"+reviewType);
        }else if(reviewType!=undefined && reviewType!='' && reviewType == 0)  {
            $("#collaborative-step2").hide();
        }
    
    });*/
    
    // get province by networks id/ids
   /* $(document).on("click", "#create_resource_form #rec_network,#edit_resource_form #rec_network", function () {
        var contnr = $(this).parents('form').first();
        var network_id = $('#rec_network').val();
        if (network_id!='' && network_id !== null && network_id !== undefined) {
            var postData = "network_id=" + network_id + "&token=" + getToken();
            $('#rec_schools').hide();                    
            $('#rec_users').hide();
            $('#provinces').hide();
            apiCall(this, "getProvincesInMultiNetwork", postData, function (s, data) {
                if (data.message != '') {
                    $('#errors').hide();
                    $('#provinces').show();                                        
                    var aDd = $(contnr).find("#provinces .province-list-dropdown");
                    aDd.find("option").next().remove();
                    addOptions(aDd, data.message, 'province_id', 'province_name');
                    aDd.append('<option value="all">ALL</option>');
                    $(contnr).find("#provinces").show();
                } else {                    
                    getSchools(network_id, this);
                }
            }, showErrorMsgInMsgBox);
        } else {
            $('#errors').html('Please select a network');
            $('#errors').show();
        }
        //  return false;

    });*/
    //get all schools
    /*$(document).on("change", "#create_resource_form #school_related_to,#edit_resource_form #school_related_to", function () {
        var contnr = $(this).parents('form').first();
        var option_type = $('#school_related_to').val();
        if (option_type!='' && option_type !== null && option_type !== undefined) {
            getAllSchools(option_type,this);
            /*var postData = "option_type=" + option_type + "&token=" + getToken();
            apiCall(this, "getAllSchools", postData, function (s, data) {
                if (data.message != '') {
                    $('#errors').hide();
                    $('#provinces').show();
                    $('#rec_schools').hide();                    
                    var aDd = $(contnr).find("#provinces .province-list-dropdown");
                    aDd.find("option").next().remove();
                    addOptions(aDd, data.message, 'province_id', 'province_name');
                    aDd.append('<option value="all">ALL</option>');
                    $(contnr).find("#provinces").show();
                } else {
                    $('#provinces').hide();
                    getSchools(network_id, this);
                }
            }, showErrorMsgInMsgBox);*/
       /* } else {
            $('#errors').html('Please select a network');
            $('#errors').show();
        }
        //  return false;

    });*/
   
    //create a sample for upload school assessment
    //get all schools
    $(document).on("click", " #sampleAssBtn", function () {
       
            var postData = "sample=1"+"&token=" + getToken();
            apiCall(this, "getDownloadSampleSchoolAssessment", postData, function (s, data) {
                if (data.message != '') {
                    file_url = data.site_url+"uploads/sample_csv/sample.xlsx";
                     window.open(file_url,'_blank' );
                } else {
                    //$('#provinces').hide();
                   // getSchools(network_id, this);
                }
            }, showErrorMsgInMsgBox);
        
        //  return false;

    });
    
    $(document).on("click", " #sampleStuBtn", function () {
       
            var postData = "gaid="+$("#gaid").val()+"&sample=1"+"&token=" + getToken();
            apiCall(this, "getDownloadSampleStudentProfile", postData, function (s, data) {
                if (data.message != '') {
                    file_url = data.site_url+"uploads/sample_csv/sample_student_profile.xlsx";
                     window.open(file_url,'_blank' );
                } else {
                    //$('#provinces').hide();
                   // getSchools(network_id, this);
                }
            }, showErrorMsgInMsgBox);
        
        //  return false;

    });
    
    //get all users by network/province/schools
   /* $(document).on("change", " #rec_school ", function () {
        
        getSchoolAllUsers();
        //  return false;
    });*/
    //get all users by network/province/schools
     /*$(document).on('change', 'input[name="roles[]"]', function(e){
        getSchoolAllUsers();
    });*/
    
    
    /*$(document).on("click", "#create_resource_form #rec_provinces,#edit_resource_form #rec_provinces", function () {
        
       
      //  return false;
    	var contnr = $(this).parents('form').first();    	
    	//var network_id = $(this).val();
    	var provience_ids = $('#rec_provinces').val();
       
        expr = /all/;  // no quotes here
        var all_ids='';
        if(expr.test(provience_ids)){
               //alert("ok"+provience_ids);
               $("#rec_provinces option").each(function()
                {
                    if($(this).val() !='' && $(this).val()!='undefined') {
                      all_ids  = all_ids + $(this).val()+"," ;
                }
                    // Add $(this).val() to your list
                });
                all_ids = all_ids.substring(0, all_ids.length-5)
        }
        if(all_ids.length>=1){
            provience_ids = all_ids;
           // var postData = "province_id="+all_ids+"&token=" + getToken();
        }
    	//var network_ids = $('#rec_network').val();
        if (provience_ids !== null && provience_ids !== undefined) {
            $('#errors').hide();
        //alert(provience_id);
    	var postData = "province_id="+provience_ids+"&token=" + getToken();
        apiCall(contnr, "getSchoolsInProvinces", postData, function (s, data) {
            if (data.status) {
                var aDd = $(contnr).find("#rec_schools .province-list-dropdown");
                aDd.find("option").next().remove();
                addOptions(aDd, data.message, 'client_id', 'client_name');
                $(contnr).find("#rec_schools").show();
                $('#rec_users').hide(); 
            }
        }, function(s,msg){
            $(contnr).find("#rec_schools").hide() 
            $(contnr).find("#rec_users").hide() 
            $("#createresource").show();
            $(s).find(".ajaxMsg").removeClass("success warning info").html(msg).addClass("danger active");
            $("#createresource").delay(2000).fadeOut();
        });
        } else {
            $('#errors').html('Please select a province');
            $('#errors').show();
        }
      //  return false;
    	
    });*/
    $("#login_popup form").submit(function () {
        postData = $(this).serialize();
        apiCall(this, "login", postData, function (s, data) {
            if(data.confirmstatus==0){
            $("#login_popup").find("input[type=email],input[type=password]").hide();
            $(".confirmMsg").show();
            $(".confirmMsg").html(data.errormsg);
            $("#login_popup").find("#loginsubmit").hide();
            $("#login_popup").find("#loginconfirm").show();
            $("#login_popup").find("#actionconfirm").val(1);
            }else{
            setToken(data.token);
            $("#login_popup").find("input[type=email],input[type=password]").show();
            $(".confirmMsg").hide();
            $("#login_popup").find("#loginsubmit").show();
            $("#login_popup").find("#loginconfirm").hide();
            $("#login_popup").find("#actionconfirm").val(0);
            
            $("#login_popup").find("input[type=email],input[type=password]").val('');
            $("#login_popup_wrap").removeClass("active").trigger("loggedIn");
            }
            
        }, showErrorMsgInMsgBox);
        return false;
    });
    
    $("#login_popup #logincancel").click(function(){
            $("#login_popup").find("input[type=email],input[type=password]").show();
            $("#login_popup").find("input[type=email],input[type=password]").val('');
            $(".confirmMsg").hide();
            $("#login_popup").find("#loginsubmit").show();
            $("#login_popup").find("#loginconfirm").hide();
            $("#login_popup").find("#actionconfirm").val(0);
    });
    
    
    
    $(document).on("change", "#create_school_assessment_form .external_assessor_id, #create_school_assessment_form .team_external_assessor_id, #edit_school_assessment_form .external_assessor_id, #edit_school_assessment_form .team_external_assessor_id,#create_college_assessment_form .external_assessor_id, #create_college_assessment_form .team_external_assessor_id, #edit_college_assessment_form .external_assessor_id, #edit_college_assessment_form .team_external_assessor_id", function () {
        allAssessors = [];
        var currentSelAssessorId = $(this).val();
        var currentSelAssessorHtmlId = $(this).attr('id');
        var frm = $(this).closest('form').first().attr('id');
        $('.team_row .external_assessor_id, .team_row .team_external_assessor_id ').each(function () {
            $(this).val() != '' ? allAssessors.push($(this).val()) : '';
        });

        //
        $('.team_row').each(function () {
            var currObjVal = $(this).find('.external_assessor_id, .team_external_assessor_id').val();
            var clientId = $(this).find('.external_client_id, .team_external_client_id').attr('id');
            var assessorId = $(this).find('.external_assessor_id, .team_external_assessor_id').attr('id');

            console.log("currentSelAssessorId" + " " + currentSelAssessorId)
            console.log("currObjVal " + currObjVal)
            //$(this).find('#'+assessorId+' option').remove()
            //console.log($("#"+assessorId).find('option'))	
            if (assessorId != currentSelAssessorHtmlId)
                ReloadExternalAssesorTeamMembersListForAssessment('#' + frm, clientId, assessorId, currObjVal);


        });

    });
    
     $(document).on("change", "#create_school_assessment_form .facilitator_id, #create_school_assessment_form .team_external_facilitator_id, #edit_school_assessment_form .facilitator_id, #edit_school_assessment_form .team_external_facilitator_id,#create_college_assessment_form .facilitator_id, #create_college_assessment_form .team_external_facilitator_id, #edit_college_assessment_form .facilitator_id, #edit_college_assessment_form .team_external_facilitator_id", function () {
        allFacilitators = [];
        //var currentSelAssessorId = $(this).val();
        //var currentSelAssessorHtmlId = $(this).attr('id');
       // var frm = $(this).closest('form').first().attr('id');
        $('.facilitator_row .facilitator_id, .facilitator_row .team_external_facilitator_id ').each(function () {
            //alert($(this).val());
            $(this).val() != '' ? allFacilitators.push($(this).val()) : '';
        });
        
     });
    
    //Added by Vikas for workshop add
    $(document).on("change", "#create_workshop_form .external_assessor_id, #create_workshop_form .team_facilitator_id, #edit_workshop_form .external_assessor_id, #edit_workshop_form .team_facilitator_id", function () {
        allAssessors = [];
        var currentSelAssessorId = $(this).val();
        var currentSelAssessorHtmlId = $(this).attr('id');
        var frm = $(this).closest('form').first().attr('id');
        $('.team_row .external_assessor_id, .team_row .team_facilitator_id ').each(function () {
            $(this).val() != '' ? allAssessors.push($(this).val()) : '';
        });

        //
        count=1;
        $('.team_row').each(function () {
            var currObjVal = $(this).find('.external_assessor_id, .team_facilitator_id').val();
            var clientId = $(this).find('.external_client_id, .team_facilitator_client_id').attr('id');
            var assessorId = $(this).find('.external_assessor_id, .team_facilitator_id').attr('id');

           console.log("currentSelAssessorId" + " " + currentSelAssessorId)
            console.log("currObjVal " + currObjVal)
            //$(this).find('#'+assessorId+' option').remove()
            //console.log($("#"+assessorId).find('option'))	
            if (assessorId != currentSelAssessorHtmlId){
                if(count==1){
                ReloadUsersTeamMembersListForWorkshop('#' + frm, clientId, assessorId, currObjVal);    
                }else{
                ReloadFacilitatorTeamMembersListForWorkshop('#' + frm, clientId, assessorId, currObjVal);
            }
            }
            
            count++;
        });

    });
    //Added by Vikas for workshop add

    $(document).on("change", "#create_school_assessment_form .internal_client_id", function () {
        loadAssesorListForAssessment($("#create_school_assessment_form"), "internal");
    });
    
    $(document).on("change", "#create_college_assessment_form .internal_client_id", function () {
        loadAssesorListForAssessment($("#create_college_assessment_form"), "internal");
    });
    
     $(document).on("change", "#create_school_self_assessment_form .internal_client_id", function () {
        loadAssesorListForAssessment($("#create_school_self_assessment_form"), "internal");
        
    var client_id_self = $("#create_school_self_assessment_form").find(".internal_client_id").val();
   //alert(client_id_self);
    var aDd_dia = $("#create_school_self_assessment_form").find(".diagnostic_id");
    aDd_dia.find("option").next().remove();
    if (client_id_self > 0)
        apiCall($("#create_school_self_assessment_form"), "getSelfReviewData", {"token": getToken(), "client_id": client_id_self}, function (s, data) {
            
            addOptions(aDd_dia, data.diagnostic, 'diagnostic_id', 'name');
            //(data.diagnostic);
            
            console.log(data);
            
        }, showErrorMsgInMsgBoxSelfReview);
    
    });
    
    //Added by Vikas for Facilitator Role
    $(document).on("change", "#create_school_assessment_form .facilitator_client_id,#create_college_assessment_form .facilitator_client_id", function () {
        var frm = $(this).closest('form');
        //loadFacilitatorListForAssessment($("#create_school_assessment_form"), "facilitator");
        loadFacilitatorListForAssessment(frm, "facilitator");
    });
      $(document).on("change", "#create_school_assessment_form .team_facilitator_client_id, #edit_school_assessment_form .team_facilitator_client_id,#create_college_assessment_form .team_facilitator_client_id, #edit_college_assessment_form .team_facilitator_client_id", function () {
        //loadExternalAssesorTeamMembersListForAssessment($("#create_school_assessment_form"),$(this).attr('id'),"team_external_assessor_id"+$(this).attr('id').slice(-1));
         //var frm = $(this).closest('form');
        //loadFacilitatorListForAssessment($("#create_school_assessment_form"), "facilitator");
        //loadFacilitatorListForAssessment(frm, "facilitator");
        var frm = $(this).closest('form');
        ReloadFacilitatorTeamMembersList(frm, $(this).attr('id'), "team_external_facilitator_id" + $(this).attr('id').slice(-1), '');
        //console.log(allAssessors.options)
    });
    
    $(document).on("change", "#create_workshop_form .external_client_id, #edit_workshop_form .external_client_id", function () {
        var frm = $(this).closest('form');
        //loadFacilitatorListForAssessment($("#create_school_assessment_form"), "facilitator");
        //loadAssesorListForAssessment(frm, "external");
        loadUserListForWorkshop(frm,"external");
        
    });
    
    //Added by Vikas for Facilitator Role
    $(document).on("change", "#create_school_assessment_form .external_client_id, #edit_school_assessment_form .external_client_id,#create_college_assessment_form .external_client_id, #edit_college_assessment_form .external_client_id", function () {
        //loadAssesorListForAssessment($("#create_school_assessment_form"),"external");
        var frm = $(this).closest('form');
        loadAssesorListForAssessment(frm, "external");
        //console.log(allAssessors.options)
    });
    //Added by Vikas for Facilitator Role
     $(document).on("click", "#collaborative-step1", function () {
         $("#ctreateSchoolAssessment-step1").show();
         $("#ctreateSchoolAssessment-step1").addClass('active');
         $("#ctreateSchoolAssessment-step2").removeClass('active');
         $("#ctreateSchoolAssessment-step2").hide();
         
     });
    //Added by Vikas for Facilitator Role
    $(document).on("change", " #create_school_assessment_form #review_type", function () {
         
         if($(this).val()!='' && $(this).val() == 1) {
             //alert("ok");
             $('#award option[value="1"]').attr("selected", "selected");
         }else {
             $('#award option').removeAttr("selected", "selected");
         }
         //alert($(this).val());        
     });
    
    
    $(document).on("change", "#create_school_assessment_form .team_external_client_id, #edit_school_assessment_form .team_external_client_id,#create_college_assessment_form .team_external_client_id, #edit_college_assessment_form .team_external_client_id", function () {
        //loadExternalAssesorTeamMembersListForAssessment($("#create_school_assessment_form"),$(this).attr('id'),"team_external_assessor_id"+$(this).attr('id').slice(-1));
        var frm = $(this).closest('form');
        ReloadExternalAssesorTeamMembersListForAssessment(frm, $(this).attr('id'), "team_external_assessor_id" + $(this).attr('id').slice(-1), '');
        //console.log(allAssessors.options)
    });
    
    //Added by Vikas for workshop add
    $(document).on("change", "#create_workshop_form .team_facilitator_client_id, #edit_workshop_form .team_facilitator_client_id", function () {
        //loadExternalAssesorTeamMembersListForAssessment($("#create_school_assessment_form"),$(this).attr('id'),"team_external_assessor_id"+$(this).attr('id').slice(-1));
        //alert("dsd");
        var frm = $(this).closest('form');
        //alert(frm);
      ReloadFacilitatorTeamMembersListForWorkshop(frm, $(this).attr('id'), "team_facilitator_id" + $(this).attr('id').slice(-1), '');
        //console.log(allAssessors.options)
    });
    //Added by Vikas for workshop add
    
    $(document).on("change", "#edit_school_assessment_form .internal_client_id", function () {
        loadAssesorListForEditAssessment($("#edit_school_assessment_form"), "internal");
    });
    $(document).on("change", "#edit_school_assessment_form .external_client_id", function () {
        loadAssesorListForEditAssessment($("#edit_school_assessment_form"), "external");
    });
    //Added by Vikas for Facilitator Role
    $(document).on("change", "#edit_school_assessment_form .facilitator_client_id", function () {
        loadFacilitatorListForAssessment($("#edit_school_assessment_form"), "facilitator");
    });
    //Added by Vikas for Facilitator Role
    
    /*$(document).on("submit","#create_school_assessment_form",function(){
     postData=$(this).serialize()+"&token="+getToken();
     apiCall(this,"createSchoolAssessment",postData,function(s,data){
     showSuccessMsgInMsgBox(s,data);$(s).find("select").val('');				
     $('#create_school_assessment_form .external_assessor_id').each(function(i,k){ 
     $(this).find('option').next().remove();				
     })
     $('#create_school_assessment_form .internal_assessor_id').each(function(i,k){ 
     $(this).find('option').next().remove();				
     })
     if($(".assessment-list").length>0)
     filterByAjax($(".assessment-list"));
     },showErrorMsgInMsgBox);
     return false;
     });*/

    //added by vikas for workshop add
    $(document).on("submit", "#create_workshop_form", function () {
       var postData = new FormData(this);
        postData.append("token", getToken());
        apiCall(this, "createWorkshop", postData, function (s, data) {
            showSuccessMsgInMsgBox(s, data);
            $(s).find("select").val('');
            $(s).find("textarea").val('');
            $(s).find("input[type=text]").val('');
            $('.wrapclear').html("");
            $('#create_workshop_form .facilitator_id').each(function (i, k) {
                $(this).find('option').next().remove();
            })
            
            if ($(".workshop-list").length > 0)
            filterByAjax($(".workshop-list"));
        }, showErrorMsgInMsgBox, undefined, {processData: false, contentType: false});
        return false;
    });
    
     $(document).on("click", "#save_student_profile", function () {
        postData =$('#create_student_profile').serialize();
        var form=$("#create_student_profile");
        apiCall(form, "createStudentProfile", postData+ "&token=" + getToken(), function (s, data) {
            showSuccessMsgInMsgBox(s, data);
            $("#validationErrors").hide();
            //$(s).find("select").val('');
            //$(s).find("textarea").val('');
            //$(s).find("input[type=text]").val('');
            
        }, function (s, msg, data) {
                    //console.log(data)
                    if (data != undefined && data != null && data.errors != undefined) {
                        $('#validationErrors').show();
                        addErrorNew("#validationErrors", data.errors);
                    } else {
                        addError("#validationErrors", data.message, '');
                    }
                });
        return false;
    });
     $(document).on("submit", "#create_student_profile", function () {
        postData = $(this).serialize()+"&is_submit=1" + "&token=" + getToken();
        apiCall(this, "createStudentProfile", postData, function (s, data) {
           showSuccessMsgInMsgBox(s, data);
           $("#validationErrors").hide();
           $("#save_student_profile").hide();
           alert(""+data.message+"");
           window.location.href="index.php?controller=assessment&action=createStudentProfileForm&assessment_id="+data.assessment_id+"&assessor_id="+data.assessor_id+"";
        }, function (s, msg, data) {
                    //console.log(data)
                    if (data != undefined && data != null && data.errors != undefined) {
                        $('#validationErrors').show();
                        addErrorNew("#validationErrors", data.errors);
                    } else {
                        addError("#validationErrors", data.message, '');
                    }
                });
        return false;
    });
    
    $(document).on("submit", "#edit_workshop_form", function (event) {
        //postData = $(this).serialize();
        var file = $("#attende_file")[0].files[0];
        var postData = new FormData(this);
        postData.append("token", getToken());
        postData.append('file', file);
        
        apiCall(this, "editWorkshop", postData, function (s, data) {
            showSuccessMsgInMsgBox(s, data);
            $(s).find("select").val('');
            $(s).find("textarea").val('');
            $(s).find("input[type=text]").val('');
            $(s).find("#file_attached").html('');
            $('.wrapclear').html("");
            $('#edit_workshop_form .external_assessor_id').each(function (i, k) {
                $(this).find('option').next().remove();
            })
            
            if ($(".workshop-list").length > 0)
            filterByAjax($(".workshop-list"));
        }, showErrorMsgInMsgBox, undefined, {processData: false, contentType: false});
        return false;
    });
    
    //new code for creating school review according tap admin on 06-06-2016 by Mohit Kumar
    $(document).on("submit", "#create_school_assessment_form", function () {
        postData = $(this).serialize() + "&token=" + getToken();
         
        apiCall(this, "createSchoolAssessmentNew", postData, function (s, data) {
            showSuccessMsgInMsgBox(s, data);
            var reviewType = $("#review_type").val();
            if(reviewType!='' && reviewType!= undefined && reviewType == 1){
                  $("#collaborative-step1").removeClass('active');
                  $("#collaborative-step2").show();
                  $("#collaborative-step2").removeClass('disabledTab');
                  $('#collaborative-step1').css('pointer-events', 'none')
                  $("#collaborative-step2").addClass('active');
                  window.location.href="index.php?isPop=1&controller=assessment&action=editSchoolAssessment&said="+data.assessment_id+"&tab2=1&assessment_type=1&new=1";
                  //enableAssessmentStep2(data.assessment_id);
                 
            }
            $(s).find("select").val('');
            $('#create_school_assessment_form .external_assessor_id').each(function (i, k) {
                $(this).find('option').next().remove();
                $('input[type="text"]').val('');
            })
            $('#create_school_assessment_form .internal_assessor_id').each(function (i, k) {
                $(this).find('option').next().remove();
                $('input[type="text"]').val('');
            })
            
            if ($(".assessment-list").length > 0)
                filterByAjax($(".assessment-list"));
        }, showErrorMsgInMsgBox);
        return false;
    });
    
     //new code for assign kpa to collaborative review
    $(document).on("submit", "#create-review-kpa", function () {
        postData = $(this).serialize() + "&token=" + getToken();
        apiCall(this, "createSchoolAssessmentKpa", postData, function (s, data) {
            showSuccessMsgInMsgBox(s, data);
            $(s).find("select").val('');
            $('#create_school_assessment_form .external_assessor_id').each(function (i, k) {
                $(this).find('option').next().remove();
                $('input[type="text"]').val('');
            })
            $('#create_school_assessment_form .internal_assessor_id').each(function (i, k) {
                $(this).find('option').next().remove();
                $('input[type="text"]').val('');
            })
            
            if ($(".assessment-list").length > 0)
                filterByAjax($(".assessment-list"));
        }, showErrorMsgInMsgBox);
        return false;
    });
     //new code for assign kpa to collaborative review
    $(document).on("submit", "#edit-review-kpa", function () {
        postData = $(this).serialize() + "&token=" + getToken();
       
        var isNewReview = $('.isNewReview').val();
        var assessmentRating = $('.assessmentRating').val();
        var assessmentRatingKpa = $('.assessmentRatingKpa').val();
          //alert(assessmentRatingKpa);
        if(assessmentRatingKpa >=1 && assessmentRating >0 && isNewReview != 1 ) { 
            if(confirm("You will lose rating data if you change assessor KPA")) {  
                updateAssessorsKpas(postData);
            }
        }else{
            updateAssessorsKpas(postData);
        }
        /*apiCall('#edit-review-kpa', "editSchoolAssessmentKpa", postData, function (s, data) {
             $(".ajaxMsg").show();
            showSuccessMsgInMsgBox(s, data);
            $(".ajaxMsg").delay(2000).fadeOut();
            
        }, function(s,data) { $(".ajaxMsg").show();showErrorMsgInMsgBox(s,data); $(".ajaxMsg").delay(2000).fadeOut(); } );
        */
    
    return false;
    });
    
    $(document).on("submit", "#create_college_assessment_form", function () {
        postData = $(this).serialize() + "&token=" + getToken();
        apiCall(this, "createCollegeAssessmentNew", postData, function (s, data) {
            showSuccessMsgInMsgBox(s, data);
            $(s).find("select").val('');
            $('#create_college_assessment_form .external_assessor_id').each(function (i, k) {
                $(this).find('option').next().remove();
                $('input[type="text"]').val('');
            })
            $('#create_college_assessment_form .internal_assessor_id').each(function (i, k) {
                $(this).find('option').next().remove();
                $('input[type="text"]').val('');
            })
            
            if ($(".assessment-list").length > 0)
                filterByAjax($(".assessment-list"));
        }, showErrorMsgInMsgBox);
        return false;
    });

    $(document).on("submit", "#edit_school_assessment_form", function () {
        postData = $(this).serialize() + "&token=" + getToken();
        console.log(postData);
        apiCall(this, "editSchoolAssessment", postData, function (s, data) {
            showSuccessMsgInMsgBox(s, data);
             var reviewType = $("#review_type").val();
            // alert(reviewType);
            //if(reviewType!='' && reviewType!= undefined && reviewType == 1){
                 // $("#collaborative-step1").removeClass('active');
                 // $("#collaborative-step2").show();
                 // $("#collaborative-step2").removeClass('disabledTab');
                 // $('#collaborative-step1').css('pointer-events', 'none')
                  //$("#collaborative-step2").addClass('active');
                  //enableAssessmentStep2(data.assessment_id);
             // }
             if(reviewType!='' && reviewType!= undefined && reviewType != 1){
                $(s).find("select").val('');
                $('#edit_school_assessment_form .external_assessor_id').each(function (i, k) {
                    $(this).find('option').next().remove();
                     $('input[type="text"]').val('');
                })
                $('#edit_school_assessment_form .internal_assessor_id').each(function (i, k) {
                    $(this).find('option').next().remove();
                     $('input[type="text"]').val('');
                })
                if ($(".assessment-list").length > 0)
                    filterByAjax($(".assessment-list"));
                $("#notification_settings").hide();
                
            }
        }, showErrorMsgInMsgBox);
        return false;
    });
    $(document).on("submit", "#notification-setting-form", function () {
        postData = $(this).serialize() + "&token=" + getToken();
        apiCall(this, "editSchoolAssessmentNotificationSettings", postData, function (s, data) {
            //$('.ajaxMsg').show();
            showSuccessMsgInMsgBox(s, data);
            
        }, showErrorMsgInMsgBox);
        return false;
    });
    
    $(document).on("submit", "#edit_college_assessment_form", function () {
        postData = $(this).serialize() + "&token=" + getToken();
        console.log(postData);
        apiCall(this, "editCollegeAssessment", postData, function (s, data) {
            showSuccessMsgInMsgBox(s, data);
            $(s).find("select").val('');
            $('#edit_college_assessment_form .external_assessor_id').each(function (i, k) {
                $(this).find('option').next().remove();
                 $('input[type="text"]').val('');
            })
            $('#edit_college_assessment_form .internal_assessor_id').each(function (i, k) {
                $(this).find('option').next().remove();
                 $('input[type="text"]').val('');
            })
            if ($(".assessment-list").length > 0)
                filterByAjax($(".assessment-list"));
        }, showErrorMsgInMsgBox);
        return false;
    });
    
    $(document).on("submit", "#create_school_self_assessment_form, #edit_school_self_assessment_form", function () {
        var frm = $(this).closest('form').first().attr('id');
        var postData = $(this).serialize() + "&token=" + getToken();
        var controller = '';
        controller = frm == 'create_school_self_assessment_form' ? 'createSchoolSelfAssessment' : 'editSchoolSelfAssessment';
        apiCall(this, controller, postData, function (s, data) {
            showSuccessMsgInMsgBox(s, data);
            $(s).find("select").val('');
            $('#' + frm + ' .internal_assessor_id').each(function (i, k) {
                $(this).find('option').next().remove();
            }) 
            apiCall(frm, "sendMail", postData, function (s, data) {
                //showSuccessMsgInMsgBox(s,data);
                if(data.message!=""){
                alert(data.message);
                }
                
                if ($(".assessment-list").length > 0)
                filterByAjax($(".assessment-list"));    
            },showErrorMsgInMsgBox);
        }, showErrorMsgInMsgBox);
        
        return false;
    });

    $(document).on("submit", "#create_teacher_assessment_form", function () {
        var cid = $(this).find('#selected_client_id').val();
        var dSelected = 0;
        $(".diagnostic_dd").each(function (i, v) {
            if ($(v).val() != "" && $(v).val() > 0) {
                dSelected = 1;
            }
        });
        if (cid == "" || !(cid > 0)) {
            alert('Please select a school');
        } else if (dSelected == 0) {
            alert('Please select a diagnostic');
        } else {
            postData = $(this).serialize() + "&token=" + getToken();
            apiCall(this, "createTeacherAssessment", postData, function (s, data) {
                showSuccessMsgInMsgBox(s, data);
                $(s).find("#external_reviewers_block .eAssessorNode").remove();
                $(s).find("#external_reviewers_block .empty").removeClass('notEmpty');
                $(s).find("select").val('');
                if ($(s).find("#selectClientBtn").length) {
                    $(s).find("#selectClientBtn").text("Select School");
                    $(s).find("#selected_client_id").val("");
                    $(s).find("#selected_client_name").html('');
                }
                alert(""+data.message+"");
                window.location.href="index.php?controller=assessment&action=editTeacherAssessment&gaid="+data.assessment_id+"&tab2=1";
                /*$.ajax({url: "index.php?controller=assessment&action=editTeacherAssessment&gaid="+data.assessment_id+"&ajaxRequest=1",
                    dataType:'json',  
                    success: function(result){
                        alert(result.content);
        $("#load_edit").html(result.content);
    }});*/
                if ($(".assessment-list").length > 0)
                    filterByAjax($(".assessment-list"));
            }, showErrorMsgInMsgBox);
        }

        return false;
    });


    $(document).on("submit", "#create_student_assessment_form", function () {
        var cid = $(this).find('#selected_client_id').val();
        var dSelected = 0;
        $(".diagnostic_dd").each(function (i, v) {
            if ($(v).val() != "" && $(v).val() > 0) {
                dSelected = 1;
            }
        });
        if (cid == "" || !(cid > 0)) {
            alert('Please select a school');
        } else if (dSelected == 0) {
            alert('Please select a diagnostic');
        } else {
            postData = $(this).serialize() + "&token=" + getToken();
            apiCall(this, "createStudentAssessment", postData, function (s, data) {
                showSuccessMsgInMsgBox(s, data);
                $(s).find("#external_reviewers_block .eAssessorNode").remove();
                $(s).find("#external_reviewers_block .empty").removeClass('notEmpty');
                $(s).find("select").val('');
                if ($(s).find("#selectClientBtn").length) {
                    $(s).find("#selectClientBtn").text("Select School");
                    $(s).find("#selected_client_id").val("");
                    $(s).find("#selected_client_name").html('');
                }
                alert(""+data.message+"");
                window.location.href="index.php?controller=assessment&action=editStudentAssessment&gaid="+data.assessment_id+"&tab2=1";
                /*$.ajax({url: "index.php?controller=assessment&action=editTeacherAssessment&gaid="+data.assessment_id+"&ajaxRequest=1",
                    dataType:'json',  
                    success: function(result){
                        alert(result.content);
        $("#load_edit").html(result.content);
    }});*/
                if ($(".assessment-list").length > 0)
                    filterByAjax($(".assessment-list"));
            }, showErrorMsgInMsgBox);
        }

        return false;
    });

    $(document).on("submit", "#add_school_to_network_form", function () {
        postData = $(this).serialize() + "&token=" + getToken();
        apiCall(this, "addSchoolToNetwork", postData, function (s, data) {
            showSuccessMsgInMsgBox(s, data);
            var cnt = $(s).find("select option:selected").length;
            $(s).find("select option:selected").remove();
            $("#schoolsInNetwork tbody").append(data.content);
            $("#clientCountFor-" + data.network_id).html(parseInt($("#clientCountFor-" + data.network_id).text()) + cnt);
            if($(".network-list").length)
                filterByAjax($(".network-list"));
        }, showErrorMsgInMsgBox);
        return false;
    });    
    $(document).on("submit", "#add_school_to_province_form", function () {
        postData = $(this).serialize() + "&token=" + getToken();
        apiCall(this, "addSchoolToProvince", postData, function (s, data) {
            showSuccessMsgInMsgBox(s, data);
            var cnt = $(s).find("select option:selected").length;
            $(s).find("select option:selected").remove();
            $("#schoolsInProvince tbody").append(data.content);
            $("#clientInProvinceCountFor-" + data.province_id).html(parseInt($("#clientInProvinceCountFor-" + data.province_id).text()) + cnt);
            if($(".network-list").length)
                filterByAjax($(".network-list"));
        }, showErrorMsgInMsgBox);
        return false;
    });
    $(document).on("change", '#create_school_assessment_form .internal_assessor_id, #edit_school_assessment_form .internal_assessor_id', function () {
        var frm = $(this).closest('form');
        var postData = frm.serialize() + "&token=" + getToken();
        apiCall(this, "getDiagnosticsForInternalAssessor", postData, function (s, data) {
            if (typeof data.hidediagnostics.hidediagnostics != undefined && data.hidediagnostics.hidediagnostics !== '' && data.hidediagnostics.hidediagnostics != null)
            {
                var diagOpt = $(frm).find("[name='diagnostic_id']");
                $(frm).find("[name='diagnostic_id'] option:gt(0)").remove();
                addOptions(diagOpt, data.allDiagnostics, 'diagnostic_id', 'name');
                var opt = $(diagOpt).find("option");
                var len = opt.length;
                var arr = (data.hidediagnostics.hidediagnostics).split(",");
                for (var i = 0; i < len; i++) {
                    if (typeof opt[i].value != undefined && opt[i].value !== '' && opt[i].value != null && $.inArray(opt[i].value, arr) >= 0)
                        $(frm).find("[name='diagnostic_id'] option[value=" + opt[i].value + "]").remove();
                }
                if ($(frm).find("[name='diagnostic_id'] option").length < 2)
                    alert("Sorry! You cannot assign any diagnostic because the user has outstanding reviews.");

            }
        }, function (s, msg) {
            console.log(s);
            console.log(msg)
        });
        return false;
    });
    
    $(document).on("change", '#create_college_assessment_form .internal_assessor_id, #edit_college_assessment_form .internal_assessor_id', function () {
        var frm = $(this).closest('form');
        var postData = frm.serialize() + "&token=" + getToken();
        apiCall(this, "getDiagnosticsForInternalAssessorCollege", postData, function (s, data) {
            if (typeof data.hidediagnostics.hidediagnostics != undefined)
            {
                var diagOpt = $(frm).find("[name='diagnostic_id']");
                $(frm).find("[name='diagnostic_id'] option:gt(0)").remove();
                addOptions(diagOpt, data.allDiagnostics, 'diagnostic_id', 'name');
                var opt = $(diagOpt).find("option");
                var len = opt.length;
                var arr = (data.hidediagnostics.hidediagnostics).split(",");
                for (var i = 0; i < len; i++) {
                    if (typeof opt[i].value != undefined && opt[i].value !== '' && opt[i].value != null && $.inArray(opt[i].value, arr) >= 0)
                        $(frm).find("[name='diagnostic_id'] option[value=" + opt[i].value + "]").remove();
                }
                if ($(frm).find("[name='diagnostic_id'] option").length < 2)
                    alert("Sorry! You cannot assign any diagnostic because the user has outstanding reviews.");

            }
        }, function (s, msg) {
            console.log(s);
            console.log(msg)
        });
        return false;
    });
    
    $(document).on("submit", "#create_user_form", function () {
        if (!($(this).find("#selected_client_id").val() > 0)) {
            alert("Please select a school");
        } else if ($(this).find(".pwd").val().length < 6) {
            alert("Minimum 6 character password required");
        } else if ($(this).find(".pwd").val() != $(this).find(".cpwd").val()) {
            alert("Confirm password didn't match");
        } else if ($(this).find('input[type=checkbox]').length > 0 && $(this).find('input[type=checkbox]:checked').length == 0) {
            alert("Please select user role");
        } else {
            postData = $(this).serialize();
            var pData = $(this).serialize() + "&token=" + getToken();
            var isPrincipal = 0;
            var deleteUser = 0;
            var users_id;
            apiCall(this, "checkUserRole", pData, //for principal user
                    function (s, data) {
                        //showSuccessMsgInMsgBox(s,data);
                        //alert()
                        isPrincipal = data.duplicate ? 1 : 0;
                        if (data.duplicate && confirm(data.message + " Do you really want to add another user with this role?"))
                        {
                            users_id = data.duplicate;
                            deleteUser = 1;
                            apiCall($("#create_user_form"), "createUser", postData + "&token=" + getToken() + "&role_id=6" + "&users_id=" + users_id,
                                    function (s, data) {
                                        var s = $("#create_user_form");
                                        showSuccessMsgInMsgBox(s, data);
                                        $(s).find(".ajaxMsg").removeClass("danger warning info").html(data.message).addClass("success active");
                                        if ($(".internal_assessor_id").length && $(s).find(".user-roles[value=3]:checked")) {
                                            loadAssesorListForAssessment($(".internal_assessor_id").parents("form"), "internal");
                                        }
                                        if ($(".external_assessor_id").length && $(s).find(".user-roles[value=3]:checked")) {
                                            loadAssesorListForAssessment($(".external_assessor_id").parents('form'), "external");
                                        }
                                        if ($(".user-list").length) {
                                            filterByAjax($(".user-list"));
                                        }
                                        if ($(".externalAssessorList").length) {
                                            filterByAjax($(".externalAssessorList"));
                                        }
                                        $(s).find("input[type=email],input[type=text],input[type=password]").val('');
                                        $(s).find('input[type=checkbox]').removeAttr("checked");
                                        if ($('#login_user_id').val() == 8) {
                                            var selected_client_id = '221';
                                            var selected_client_name = 'Independent Consultant';
                                        } else {
                                            var selected_client_id = '';
                                            var selected_client_name = '';
                                        }
                                        if ($(s).find("#selectClientBtn").length) {
                                            $(s).find("#selectClientBtn").text("Change School");
                                            $(s).find("#selected_client_id").val(selected_client_id);
                                            $(s).find("#selected_client_name").html(selected_client_name);
                                        }
                                    }, showErrorMsgInMsgBox);

                        } else if (!data.duplicate)
                        {
                            apiCall($("#create_user_form"), "createUser", postData + "&token=" + getToken(),
                                    function (s, data) {

                                        var s = $("#create_user_form");
                                        showSuccessMsgInMsgBox(s, data);
                                        $(s).find(".ajaxMsg").removeClass("danger warning info").html(data.message).addClass("success active");
                                        if ($(".internal_assessor_id").length && $(s).find(".user-roles[value=3]:checked")) {
                                            loadAssesorListForAssessment($(".internal_assessor_id").parents("form"), "internal");
                                        }
                                        if ($(".external_assessor_id").length && $(s).find(".user-roles[value=3]:checked")) {
                                            loadAssesorListForAssessment($(".external_assessor_id").parents('form'), "external");
                                        }
                                        if ($(".user-list").length) {
                                            filterByAjax($(".user-list"));
                                        }
                                        if ($(".externalAssessorList").length) {
                                            filterByAjax($(".externalAssessorList"));
                                        }
                                        $(s).find("input[type=email],input[type=text],input[type=password]").val('');
                                        $(s).find('input[type=checkbox]').removeAttr("checked");
                                        if ($('#login_user_id').val() == 8) {
                                            var selected_client_id = '221';
                                            var selected_client_name = 'Independent Consultant';
                                        } else {
                                            var selected_client_id = '';
                                            var selected_client_name = '';
                                        }
                                        if ($(s).find("#selectClientBtn").length) {
                                            $(s).find("#selectClientBtn").text("Change School");
                                            $(s).find("#selected_client_id").val(selected_client_id);
                                            $(s).find("#selected_client_name").html(selected_client_name);
                                        }
                                    }, showErrorMsgInMsgBox);



                        }

                    }, showErrorMsgInMsgBox);
        }


        return false;
    });

    $(document).on("submit", "#update_user_form", function () {
        if ($(this).find(".pwd").val() != "" && $(this).find(".pwd").val().length < 6) {
            alert("Minimum 6 character password required");
        } else if ($(this).find(".pwd").val() != $(this).find(".cpwd").val()) {
            alert("Confirm password didn't match");
        } else if ($(this).find('input[type=checkbox]').length > 0 && $(this).find('input[type=checkbox]:checked').length == 0) {
            alert("Please select user role");
        } else {
            var isPrincipal = 0;
            var deleteUser = 0;
            var users_id;
            postData = $(this).serialize();
            var pData = $(this).serialize() + "&token=" + getToken();
            var s = $("#update_user_form");
            // pass user id for gettig the user data on 16-05-2016 by Mohit Kumar
            var id = $('input[name="id"]').val();
            apiCall(s , "checkUserRole", pData, //for principal user
                    function (s, data) {
                        //showSuccessMsgInMsgBox(s,data);
                        //alert()

                        isPrincipal = data.duplicate ? 1 : 0;
                        if (data.duplicate && confirm(data.message + "Do you really want to add another user with this role?"))
                        {
                            users_id = data.duplicate;
                            deleteUser = 1;
                            apiCall(s, "updateUser", postData + "&token=" + getToken() + "&role_id=6" + "&users_id=" + users_id,
                                    function (s, data) {                                       
                                        showSuccessMsgInMsgBox(s, data);
                                        if ($(".user-list").length) {
                                            filterByAjax($(".user-list"));
                                        }
                                    }, showErrorMsgInMsgBox);

                        } else if (!data.duplicate)
                        {

                            apiCall(s, "updateUser", postData + "&token=" + getToken(),
                                    function (s, data) {
                                        
                                        showSuccessMsgInMsgBox(s, data);
                                        if ($(".user-list").length) {
                                            filterByAjax($(".user-list"));
                                        }
                                    }, showErrorMsgInMsgBox);
                        }
                    }, showErrorMsgInMsgBox);
        }
        return false;
    });

    $(document).on("submit", "#edit_school_form", function () {
        postData = $(this).serialize() + "&token=" + getToken();
        apiCall(this, "updateClient", postData,
                function (s, data) {
                    showSuccessMsgInMsgBox(s, data);
                    if ($(".client-list").length) {
                        filterByAjax($(".client-list"));
                    }
                }, showErrorMsgInMsgBox);
        return false;
    });

    $(document).on("submit", "#create_school_form", function () {
        if ($(this).find(".pwd").val().length < 6) {
            alert("Minimum 6 character password required");
        } else if ($(this).find(".pwd").val() != $(this).find(".cpwd").val()) {
            alert("Confirm password didn't match");
        } else {
            postData = $(this).serialize() + "&token=" + getToken();
            apiCall(this, "createClient", postData,
                    function (s, data) {
                        showSuccessMsgInMsgBox(s, data);
                        $(s).find("textarea,input[type=email],input[type=text],input[type=password],select").val('');
                        if ($(".client-list").length) {
                            filterByAjax($(".client-list"));
                        }
                    }, showErrorMsgInMsgBox);
        }
        return false;
    });
    $(document).on("submit", "#create_web_school_form", function () {
        if ($(this).find(".pwd").val().length < 6) {
            alert("Minimum 6 character password required");
        } else if ($(this).find(".pwd").val() != $(this).find(".cpwd").val()) {
            alert("Confirm password didn't match");
        } else {
            var postData = $(this).serialize();
            webApiCall(this, "createApiWebClient", postData, "api=1",
                    function (s, data) {
                        //showSuccessMsgInMsgBox(s,data);
                        alert(data.message);
                        $(s).find("textarea,input[type=email],input[type=text],input[type=password],select").val('');
                        //login
                        webApiCall(this, "loginApi", postData, "api=1",
                                function (s, data) {
                                    //showSuccessMsgInMsgBox(s,data);
                                    //alert(data.message);
                                    window.location = data.message;

                                }, showErrorMsgInMsgBox);



                    }, showErrorMsgInMsgBox);
        }
        return false;
    });
    $(document).on("submit", "#resetForm", function () {

        var postData = $(this).serialize();
        if ($("#hashkey").val() != undefined && $("#hashkey").val() != '')
        {
            if ($(this).find(".pwd").val().length < 6) {
                alert("Minimum 6 character password required");
            } else if ($(this).find(".pwd").val() != $(this).find(".cpwd").val()) {
                alert("Confirm password didn't match");
            } else
            {
                webApiCall(this, "updatePassApiWebClient", postData, "api=1",
                        function (s, data) {
                            //showSuccessMsgInMsgBox(s,data);
//						alert(data.message);
//                                                console.log(postData+"&email="+$('#email').val());
//                                                return false;
                            if ($('#process').val() == 'assessor') {
                                //login
                                webApiCall(this, "loginApi", postData + "&email=" + $('#email').val(), "api=1",
                                        function (s, data) {
                                            //showSuccessMsgInMsgBox(s,data);
                                            //alert(data.message);
                                            window.location = data.message;

                                        }, showErrorMsgInMsgBox);
                            } else {
                                location.href = data.siteurl;
                            }

                        }, showErrorMsgInMsgBox);
            }
        } else
        {
            webApiCall(this, "resetPassApiWebClient", postData, "api=1",
                    function (s, data) {
                        //showSuccessMsgInMsgBox(s,data);
                        alert(data.message);

                    }, showErrorMsgInMsgBox);
        }
        return false;
    });
    $(document).on("click", ".execUrl", function () {
        var hook = $(this).data('validator');
        if (hook != undefined && !window[hook]()) {
            return;
        }
        var e = $(this);
        var urlObj = deSerialize(e.attr("href"));
        var c = urlObj.controller;
        var a = urlObj.action;
        delete urlObj.controller;
        delete urlObj.action;
        var postData = {};
        var pSelector = e.data('postdata');
        if (pSelector != undefined && pSelector.length > 0) {

            $(pSelector).each(function (i, e2) {
                var n = $(e2).attr("name");
                var v = $(e2).val();
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
        }
        if (e.data("postformid") != undefined) {
            postData[e.data("postformid")] = e.parents("form").first().attr("id");
        }
        load_popup_page(e.parents("form"), c, a, postData, $.param(urlObj), e.data("size"), e.data("top"), e.data('modalclass'));
        return false;
    });

    $(document).on("click", ".paginHldr .paging,.paging_wrap .paging", function () {
        var val = $(this).data("value");
        //localStorage.setItem("filter_page",$(this).data('value'));
        if (val > 0)
            filterByAjax(this, $(this).data("value"));

        return false;
    });

    $(document).on("submit", ".filters-bar", function () {
        filterByAjax(this, 1);
        return false;
    });
    $(document).on("click", ".ajaxFilterReset", function () {
        $(this).parents(".filterByAjax").find('.ajaxFilter').val('');
        filterByAjax(this, 1);
        return false;
    });
    $(document).on("click", ".filterByAjax .sort", function () {
        filterByAjax(this, 1, $(this).data("value"), $(this).hasClass("sorted_asc") ? "desc" : "asc");
        return false;
    });
    $(document).on('hidden.bs.modal', '.modal', function () {
        $('.modal:visible').length && $(document.body).addClass('modal-open'); //fixes scrolling issue when we open multi popup
    });

    if ($(document).tooltip != undefined)
        $('[data-toggle="tooltip"]').tooltip();

    $(document).on("click", ".school-report.generate_report", function () {
        var f = $(this).parents("#reportsListWrapper");
        var yr = f.find(".valid_years").val(), mth = f.find(".valid_months").val();
        langId=$("#lang_id").val();
        var reportId = $(this).data('reportid');
        var report = true;
        //alert(langId);
        if((reportId==1 || reportId==2 || reportId==3) && langId==""){
            //alert("Please select language");
            //return false;
            report=false;
        }
        if ((yr > 0 || mth > 0) && report==true) {
           url=$(this).data("url") + "&years=" + yr + "&months=" + mth; 
           if(langId!="" && (reportId==1 || reportId==2 || reportId==3)){
           url += "&lang_id="+langId+""; 
           }
           window.open(url);
        } else{
            
            if(yr <= 0 && mth <= 0){
            alert("Please select validity period for the report.");
            return false;
        }
            if((reportId==1 || reportId==2 || reportId==3) && langId==""){
            alert("Please select language");
            return false;
        }
        
        }
    });
    $(document).on("change","#report_id_4",function(){
    	var href = $(document).find(".tchr-recomm").attr('href');
    	var diagIndex = href.search("diagnostic_id");
    	var diagnostic = $("#report_id_4").val();
        var dept ="";
        href = href.slice(0,diagIndex-1);
        //alert(href);
    	href = href+"&diagnostic_id="+diagnostic+"&dept_id="+dept+"";
        //alert(href);
        $(document).find(".tchr-recomm").attr('href',href);    	
    });
    
     $(document).on("change","#report_id_cat_4",function(){
        var href = $(document).find(".tchr-recomm").attr('href');
    	var diagIndex = href.search("dept_id");
    	var dept = $("#report_id_cat_4").val();
        href = href.slice(0,diagIndex-1);
        href = href+"&dept_id="+dept+"";
        //alert(href);
        $(document).find(".tchr-recomm").attr('href',href); 
        
     });
     
    $(document).on("change","#report_id_8",function(){
    	var href = $(document).find(".tchr-recomm").attr('href');
    	var diagIndex = href.search("diagnostic_id");
    	var diagnostic = $("#report_id_8").val();
    	href = href.slice(0,diagIndex-1);
    	href = href+"&diagnostic_id="+diagnostic;
    	$(document).find(".tchr-recomm").attr('href',href);    	
    });
    $(document).on("click", ".tchr-report.generate_report", function () {
        var f = $(this).parents("#reportsListWrapper");
        
        var yr = f.find(".valid_years").val(), mth = f.find(".valid_months").val();
        var langId = $("#lang_id").val();
        var reportId = $(this).data('reportid');
        var url = $(this).data("url");
        
        if(reportId==4)
        	url += "&diagnostic_id="+$("#report_id_4").val()+"&dept_id="+$("#report_id_cat_4").val()+"&assessment_id=0";
        else if(reportId==5)
        	url += "&assessment_id="+$("#report_id_5").val()+"&diagnostic_id=0";
        else if(reportId==7)
        	url += "&assessment_id="+$("#report_id_7").val()+"&diagnostic_id=0";
        else if(reportId==8)
        	url += "&diagnostic_id="+$("#report_id_8").val()+"&assessment_id=0";
        else if(reportId==9)
        	url += "&assessment_id="+$("#report_id_9").val()+"&diagnostic_id=0";
        else if(reportId==10)
        	url += "&assessment_id="+$("#report_id_10").val()+"&diagnostic_id=0";    
        if (yr > 0 || mth > 0) {
            window.open(url + "&years=" + yr + "&months=" + mth);        	
        } else
            alert("Please select validity period for the report.");
    });
    $(document).on("click", ".tchr-report.view_report", function () {
        var f = $(this).parents("#reportsListWrapper");        
        var reportId = $(this).data('reportid');
        var url = $(this).data("url");
        if(reportId==4)
        	url += "&diagnostic_id="+$("#report_id_4").val()+"&dept_id="+$("#report_id_cat_4").val()+"&assessment_id=0";
        else if(reportId==5)
        	url += "&assessment_id="+$("#report_id_5").val()+"&diagnostic_id=0";
        else if(reportId==7)
        	url += "&assessment_id="+$("#report_id_7").val()+"&diagnostic_id=0";
        else if(reportId==8)
        	url += "&diagnostic_id="+$("#report_id_8").val()+"&assessment_id=0";
        else if(reportId==9)
        	url += "&assessment_id="+$("#report_id_9").val()+"&diagnostic_id=0";
        else if(reportId==10)
        	url += "&assessment_id="+$("#report_id_10").val()+"&diagnostic_id=0";    
            window.open(url);        	       
    });
    
    $(document).on("change", "#report_id_4", function () {
       var groupassessment_id = $(this).data("groupassessment_id"); 
       var diagnostic_id=$("#report_id_4").val();
       //alert(groupassessment_id);
       //alert(diagnostic_id);
       
       postData = "groupassessment_id="+groupassessment_id+"&diagnostic_id="+diagnostic_id+"&token=" + getToken();
       apiCall(this, "updateDepartment", postData,
                function (s, data) {
                    //alert("Yes");
                var aDd = $("#report_id_cat_4");
                aDd.find("option").next().remove();
                addOptions(aDd, data.department, 'department_id', 'department');
                
                }, showErrorMsgInMsgBox);
        return false;
        
    });
    
    $(document).on("click", ".publish_report", function () {
        var f = $(this).parents("#reportsListWrapper");
        var yr = f.find(".valid_years").val(), mth = f.find(".valid_months").val(), aid = f.data("assessmentorgroupassessmentid"), c = f.find("#keyNotesAccepted"), atid = f.data("assesmenttypeid");
        if (c.length > 0 && !c.is(":checked")) {
            alert("Please approve the assessor key recommendations");
        } else if (yr > 0 || mth > 0) {
            apiCall(this, "publishReport", {"token": getToken(), "years": yr, "months": mth, "ass_or_group_ass_id": aid, "assessment_type_id": atid},
                    function (s, data) {
                        if (data.content != undefined && data.content != null) {
                            $(s).parents('.modal-body').html(data.content).find('.page-title').remove();
                            if ($(".assessment-list").length)
                                filterByAjax($(".assessment-list"));
                        }
                    }, function (s, msg) {
                alert(msg);
            });
        } else
            alert("Please select validity period for the report.");
    });

    $(document).on("click", ".eAssessorRow", function () {
        var id = $(this).data('id');
        var cont = $(".currentSelection");
        if (cont.find(".eAssessorNode-" + id).length)
            return;
        var name = $(this).addClass('selected').find(".eAssessorName").text();
        var email = $(this).addClass('selected').find(".eAssessorName").attr('title');
        cont.append('<div title="' + email + '" class="eAssessorNode clearfix eAssessorNode-' + id + '" data-id="' + id + '">' + name + '<input type="hidden" class="ajaxFilterAttach" name="eAssessor[' + id + ']" value="' + id + '"/><span class="delete"><i class="fa fa-times"></i></span></div>').find(".empty").addClass('notEmpty');
    });

    $(document).on("click", ".eAssessorNode .delete", function () {
        var id = $(this).parents(".eAssessorNode").data("id");
        var p = $(this).parents(".tag_boxes").first();
        $(".eAssessorNode-" + id).remove();
        $("#ex-user-" + id).removeClass('selected');
        if (p.find(".eAssessorNode").length == 0) {
            p.find(".empty").removeClass("notEmpty");
        }
        var trgr = p.data('trigger');
        if (trgr != undefined && trgr != null && trgr != "") {
            $("body").trigger(trgr);
        }
    });

    $(document).on("mouseenter", ".vtip", function (a) {
        //alert("enter");
        this.t = this.title;
        this.title = "";
        this.top = (a.pageY + 20);
        this.left = (a.pageX - 25);
        $("body").append('<p id="vtip"><img id="vtipArrow" />' + this.t + "</p>");
      //  $("body").append('<p class="vtip"><img id="vtipArrow" />' + this.t + "</p>");
        $("p#vtip #vtipArrow").attr("src", "public/images/vtip_arrow.png");
        $("p#vtip").css("top", this.top + "px").css("left", this.left + "px").fadeIn("slow");
    }).on("mouseleave", ".vtip", function () {
       // alert("leave");
        this.title = this.t;
        $("p#vtip").fadeOut("slow").remove()
        //$( "p#vtip" ).remove();
    }).on("mousemove", ".vtip", function (a) {
        //alert("move");
        this.top = (a.pageY + 20);
        this.left = (a.pageX - 25);
        $("p#vtip").css("top", this.top + "px").css("left", this.left + "px");
    });

    $(document).on("click", ".team_table .team_row .delete_row", function () {
        if ($(this).parents(".team_table").first().find(".team_row").length > 1) {
            var p = $(this).parents(".team_table");
            $(this).parents(".team_row").remove();
            p.first().find(".s_no").each(function (i, v) {
                $(v).html(i + 1)
            });
            var trgr = p.data('trigger');
            if (trgr != undefined && trgr != null && trgr != "") {
                $("body").trigger(trgr);
            }
        } else
            alert("You can't delete all the rows");
        return false;
    }); 
    $(document).on("click", " .facilitator_table .facilitator_row .delete_row", function () {
        if ($(this).parents(".facilitator_table").first().find(".facilitator_row").length > 1) {
            var p = $(this).parents(".facilitator_table");
            $(this).parents(".facilitator_row").remove();
            p.first().find(".s_no").each(function (i, v) {
                $(v).html(i + 1)
            });
            var trgr = p.data('trigger');
            if (trgr != undefined && trgr != null && trgr != "") {
                $("body").trigger(trgr);
            }
        } else
            alert("You can't delete all the rows");
        return false;
    });
	 $(document).on("click", ".provinceField .delete_row", function () {
		 var frm = $(this).parents("#create_network_form,#create_province_form");
        if (frm.first().find(".provinceField").length > 1)
				$(this).closest('.provinceField').remove();                                 
        else
            alert("You can't delete all the rows");
        return false;
    });
    $(document).on("click", ".additionalTeam_table .team_row .delete_row", function () {
        if ($(this).parents(".additionalTeam_table").first().find(".team_row").length > 1) {
            var p = $(this).parents(".additionalTeam_table");
            $(this).parents(".team_row").remove();
            p.first().find(".s_no").each(function (i, v) {
                $(v).html(i + 1)
            });
            var trgr = p.data('trigger');
            if (trgr != undefined && trgr != null && trgr != "") {
                $("body").trigger(trgr);
            }
        } else
            alert("You can't delete all the rows");
        return false;
    });


    $(document).on("click", ".team_table .extteamAddRow", function () {
        //var frm = $('.extteamAddRow').parents().find('form').first().attr('id');
        var frm = $('.extteamAddRow').closest('form').first().attr('id');

        var removeIds = [];
        //find all the selected Ids
        $("#" + frm + " .team_row").each(function () {
            //$(this).val()!=''? removeIds.push($(this).val()):'';
        });
        console.log(removeIds)
        apiCall(this, "addExternalReviewTeam", "sn=" + ($(this).parents(".team_table").first().find(".team_row").length + 1) + "&frm="+frm+"&token=" + getToken(), function (s, data) {

            $.when($(s).parents(".team_table").first().find(".team_row").last().after(data.content)).then(function () {
                //	var cid = $(this).parents().find('.external_client_id').val();				
                //loadAssesorListForSchoolAssessment(cid,frm,"external",removeIds);		
                return false;
            });
        }, function (s, msg) {
            alert(msg);
        });
    });
    
    /*
     * create facilitator row to add  multiple facilitator in school review
     * Owner:Deepak Thakur
     */
    $(document).on("click", ".facilitator_table .extteamAddRow", function () {
        //var frm = $('.extteamAddRow').parents().find('form').first().attr('id');
        var frm = $('.extteamAddRow').closest('form').first().attr('id');

        var removeIds = [];
        //find all the selected Ids
        $("#" + frm + " .facilitator_row").each(function () {
            //$(this).val()!=''? removeIds.push($(this).val()):'';
        });
        console.log(removeIds)
        apiCall(this, "addFacilitatorReviewTeam", "sn=" + ($(this).parents(".facilitator_table").first().find(".facilitator_row").length + 1) + "&frm="+frm+"&token=" + getToken(), function (s, data) {

            $.when($(s).parents(".facilitator_table").first().find(".facilitator_row").last().after(data.content)).then(function () {
                //	var cid = $(this).parents().find('.external_client_id').val();				
                //loadAssesorListForSchoolAssessment(cid,frm,"external",removeIds);		
                return false;
            });
        }, function (s, msg) {
            alert(msg);
        });
    });
    
    //Added by Vikas for workshop add
    
     $(document).on("click", ".team_table .extteamworkshopAddRow", function () {
        //var frm = $('.extteamAddRow').parents().find('form').first().attr('id');
        var frm = $('.extteamworkshopAddRow').closest('form').first().attr('id');
        //alert(frm);
        var removeIds = [];
        //find all the selected Ids
        $("#" + frm + " .team_row").each(function () {
            //$(this).val()!=''? removeIds.push($(this).val()):'';
        });
        console.log(removeIds)
        apiCall(this, "addFacilitatorTeam", "sn=" + ($(this).parents(".team_table").first().find(".team_row").length + 1) + "&token=" + getToken(), function (s, data) {

            $.when($(s).parents(".team_table").first().find(".team_row").last().after(data.content)).then(function () {
                //	var cid = $(this).parents().find('.external_client_id').val();				
                //loadAssesorListForSchoolAssessment(cid,frm,"external",removeIds);		
                return false;
            });
        }, function (s, msg) {
            alert(msg);
        });
    });
    //Added by Vikas for workshop add
    
    $(document).on("click", ".team_table .fltdAddRow", function () {
        var a = $(this).data('attach');
        var typ=$(this).data('type');
        apiCall(this, "addTeamRow", "sn=" + ($(this).parents(".team_table").first().find(".team_row").length + 1) + "&type=" + $(this).data('type') + "&token=" + getToken() + "&attach=" + (a == undefined ? '' : a), function (s, data) {
            $(s).parents(".team_table").first().find(".team_row").last().after(data.content);
            //$(s).parents(".team_table").first().find(".team_row").last().find(".aqs_ph").mask("(+99) 999-9999-999");
            $(s).parents(".team_table").first().find(".team_row").last().find('.selectpicker').selectpicker();
            $(s).parents(".team_table").first().find(".team_row").last().find('.datePicker').datetimepicker({format: 'MM/DD/YYYY', pickTime: false});
            //alert($(this).data('type'));
            if(typ=="teacherForAssessment" || typ=="teacherAssessor" ){
            
            $(s).parents(".team_table").first().find(".team_row").last().find('.date-Picker').datetimepicker({format: 'DD-MM-YYYY',useCurrent: false, pickTime: false}).on('dp.change', function(e){
            //alert("dsdggg");
            if(typ=="teacherAssessor"){
            $("#saveTchrAssessorsForm").removeAttr("disabled");    
            }else{
            $("#saveTchrsForAssessmnt").removeAttr("disabled");
            }
            $("#submitTchrsForAssessmnt").attr('disabled', "disabled");
            $("#vtip").remove();
         });
          
            }else{
            $(s).parents(".team_table").first().find(".team_row").last().find('.date-Picker').datetimepicker({format: 'DD-MM-YYYY',useCurrent: false, pickTime: false});
        }
        }, function (s, msg) {
            alert(msg);
        });
        return false;
    });
	$(document).on("click", ".addnewprovince", function () {
        var a = $(this).data('attach');
        apiCall(this, "addProvinceField","&token=" + getToken() + "&attach=" + (a == undefined ? '' : a), function (s, data) {
            $(s).parents("#create_province_form").find(".provinceField").last().after(data.content); 
            // $("#popup-network_createProvince").data('bs.modal',null);
            // $("#popup-network_createProvince").modal('show');
        }, function (s, msg) {
            alert(msg);
        });
        return false;
    });
    $(document).on("click", ".teamsInfoHldr .ovwRecomm.fltdAddRow", function () {        
       /* apiCall(this, "addOverviewRecommendations", "type="+ ($(this).data('type')) +"&"+"sn=" + ($(this).parents().find(".customTbl").first().find(".recRow").length + 1) + "&token=" + getToken() + "&attach=" , function (s, data) {
        	 $(s).parents().closest(".customTbl").first().find(".recRow").last().after(data.content);
        }, function (s, msg) {
            alert(msg);
        });*/
    	var type = $(this).data('type');var pos=0; var instance_id = $(this).data('id');
    	 apiCall(this, "addOverviewRecommendations", "instance_id="+(instance_id)+"&type="+ (type) +"&"+"sn=" + ($(this).parents().find(".customTbl."+type+"."+instance_id).first().find(".recRow").length + 1) + "&token=" + getToken() + "&attach=" , function (s, data) {
        	 $(s).parents().find(".customTbl."+type+"."+instance_id).first().find(".recRow").last().after(data.content);
        	 //pos = pos>70?pos+50:pos;
        	 //pos=$(s).parents('.vertScrollArea').find(".mCSB_dragger").position().top
        	 //pos=pos+40
        	// $(s).parents('.vertScrollArea').mCustomScrollbar("scrollTo",pos,{scrollInertia:5});
        }, function (s, msg) {
            alert(msg);
        });
        return false;
    });
    $(document).on("submit", "#teacher_recommendations_frm", function () {        
        apiCall(this, "saveTeacherOverviewRecommendations", $(this).serialize() + "&token=" + getToken() , function (s, data) {
        	showSuccessMsgInMsgBox(s,data);
        	 $(this).closest(".modal").modal("hide");
        }, showErrorMsgInMsgBox);
        return false;
    });    
    $(document).on("click",".recRow .delete_row",function(){
		if($(this).parents(".customTbl").first().find(".recRow").length>1){
			var p=$(this).parents(".customTbl");
			$(this).parents(".recRow").remove();			
			p.first().find(".s_no").each(function(i,v){;$(v).html(i+1)});
			var trgr=p.data('trigger');
			if(trgr!=undefined && trgr!=null && trgr!=""){
				$("body").trigger(trgr);
			}
		}else
			alert("You can't delete all the rows");
		return false;
	});
        
        
    $(document).on("click", ".additionalTeam_table .fltdAddRow", function () {
        var a = $(this).data('attach');
        apiCall(this, "addAdditionalRefTeamRow", "sn=" + ($(this).parents(".additionalTeam_table").first().find(".team_row").length + 1) + "&type=" + $(this).data('type') + "&token=" + getToken() + "&attach=" + (a == undefined ? '' : a), function (s, data) {
            $(s).parents(".additionalTeam_table").first().find(".team_row").last().after(data.content);
             $(s).parents(".additionalTeam_table").first().find(".team_row").last().find('.selectpicker').selectpicker();
            $(s).parents(".additionalTeam_table").first().find(".team_row").last().find('.datePicker').datetimepicker({format: 'MM/DD/YYYY', pickTime: false});
            $(s).parents(".additionalTeam_table").first().find(".team_row").last().find('.date-Picker').datetimepicker({format: 'DD-MM-YYYY',useCurrent: false, pickTime: false});
       
           // $(s).parents(".additionalTeam_table").first().find(".team_row").last().find(".aqs_ph").mask("(+99) 999-9999-999");
        }, function (s, msg) {
            alert(msg);
        });
        return false;
    });

    $(document).on("tchrAssessorsChanged", "body", function () {
        $("#saveTchrAssessorsForm").removeAttr("disabled");
    });
    /*$(document).on("tchrAsmtChanged","body",function(){
     $("#updateTeacherAssessment").removeAttr("disabled");		
     });*/

    $(document).on("tchrsForAssessmentChanged", "body", function () {
        $("#saveTchrsForAssessmnt").removeAttr("disabled");
        $("#submitTchrsForAssessmnt").attr('disabled', "disabled");
        $("#vtip").remove();
    });

    $(document).on("change", ".teacherAssessor_team input,.teacherAssessor_team select", function () {
        $("body").trigger($(this).parents('.teacherAssessor_team').first().data('trigger'));
        //$(".teacher_team").hide();
        //$("#up_teacher").hide();
        $("#submitTchrsForAssessmnt").attr('disabled', "disabled");
    });
    
    $(document).on("change", ".teacher_team input,.teacher_team select", function () {
        $("body").trigger($(this).parents('.teacher_team').first().data('trigger'));
    });
    
    
    
    $(document).on("click","#tchrAssessorsForm #status_id_2 .eAssessorNode .delete,#tchrAssessorsForm #status_id_3 .eAssessorNode .delete,#tchrAssessorsForm #status_id_1-0 .eAssessorNode .delete, #tchrAssessorsForm #status_id_1-1 .eAssessorNode .delete",function(){
     //alert($(this).id);
    $('#saveTchrAssessorsForm').trigger('click');  
    });
    
    /*$(document).on("click","#tchrAssessorsForm #add_edit_remove_teacher",function(){
    
    $("#saveTchrAssessorsForm").removeAttr('disabled'); 
    $('#saveTchrAssessorsForm').trigger('click');
    });*/
    
    $(document).on("click", "#saveTchrAssessorsForm,#saveTchrsForAssessmnt,#submitTchrsForAssessmnt", function () {
        var isManageTchrFrm = this.id != "saveTchrAssessorsForm" ? 1 : 0;
        //alert(isManageTchrFrm);
        var ignoreRejected = $(this).data('ignorerejected');
        $(this).data('ignorerejected', '0');
        var valid = 1;
        if(!isManageTchrFrm){
        $(".teacherAssessor_team .team_row").each(function (i, r) {
            var fn = $(r).find('.firstname'), ln = $(r).find('.lastname'), em = $(r).find('.email'), doj = $(r).find('.doj');
            if (fn.val().length > 0 || ln.val().length > 0 || em.val().length > 0 || doj.val().length > 0) {
                var pts = {string: /^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/, email: /^[A-Za-z0-9\._-]+@[A-Za-z0-9]+\.[A-Za-z]+$/, date: /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[0-9]|1[0-2])-(19|20)[0-9]{2}$/};
                if ($.trim(fn.val()).length == 0) {
                    alert('First Name is required');
                    fn.focus();
                    valid = 0;
                } else if (!pts.string.test(fn.val())) {
                    alert('Invalid First Name');
                    fn.focus();
                    valid = 0;
                } else if ($.trim(ln.val()).length == 0) {
                    alert('Last Name is required');
                    ln.focus();
                    valid = 0;
                } else if (!pts.string.test(ln.val())) {
                    alert('Invalid Last Name');
                    ln.focus();
                    valid = 0;
                } else if (em.val().length == 0 && doj.val().length == 0) {
                    alert('Email and Date of Joining both fields can\'t be empty');
                    em.focus();
                    valid = 0;
                } else if (em.val().length > 0 && !isValidEmail(em.val())) {
                    alert('Invalid Email');
                    em.focus();
                    valid = 0;
                } else if (doj.val().length > 0 && !pts.date.test(doj.val())) {
                    alert('Invalid Date');
                    doj.focus();
                    valid = 0;
                }/*else if (isManageTchrFrm && !($(r).find('.categoty_id').val() > 0)) {
                    alert('Please select category');
                    $(r).find('.categoty_id').focus();
                    valid = 0;
                } else if (isManageTchrFrm && !($(r).find('.assessor_id').val() > 0)) {
                    alert('Please select assessor');
                    $(r).find('.assessor_id').focus();
                    valid = 0;
                }*/

                if (valid == 0) {
                    return false;
                }
            }
        });
    }
       if(isManageTchrFrm){
        var save_ass=0;   
        $(".teacherAssessor_team .team_row").each(function (i, r) {
            var fn = $(r).find('.firstname'), ln = $(r).find('.lastname'), em = $(r).find('.email'), doj = $(r).find('.doj');
            if (fn.val().length > 0 || ln.val().length > 0 || em.val().length > 0 || doj.val().length > 0) {
            save_ass++;
            }
        });
        
        if(save_ass>0){
        alert('Please submit the Reviewers unsaved data or delete data');
        $("#saveTchrAssessorsForm").focus();
        valid = 0;    
        }else{
        $(".teacher_team .team_row").each(function (i, r) {
            var fn = $(r).find('.firstname'), ln = $(r).find('.lastname'), em = $(r).find('.email'), doj = $(r).find('.doj');
            if (fn.val().length > 0 || ln.val().length > 0 || em.val().length > 0 || doj.val().length > 0) {
                var pts = {string: /^[A-Za-z0-9\s,.\'@#$%&*+_-]+$/, email: /^[A-Za-z0-9\._-]+@[A-Za-z0-9]+\.[A-Za-z]+$/, date: /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[0-9]|1[0-2])-(19|20)[0-9]{2}$/};
                if ($.trim(fn.val()).length == 0) {
                    alert('First Name is required');
                    fn.focus();
                    valid = 0;
                } else if (!pts.string.test(fn.val())) {
                    alert('Invalid First Name');
                    fn.focus();
                    valid = 0;
                } else if ($.trim(ln.val()).length == 0) {
                    alert('Last Name is required');
                    ln.focus();
                    valid = 0;
                } else if (!pts.string.test(ln.val())) {
                    alert('Invalid Last Name');
                    ln.focus();
                    valid = 0;
                } else if (em.val().length == 0 && doj.val().length == 0) {
                    alert('Email and Date of Joining both fields can\'t be empty');
                    em.focus();
                    valid = 0;
                } else if (em.val().length > 0 && !isValidEmail(em.val())) {
                    alert('Invalid Email');
                    em.focus();
                    valid = 0;
                } else if (doj.val().length > 0 && !pts.date.test(doj.val())) {
                    alert('Invalid Date');
                    doj.focus();
                    valid = 0;
                } else if (isManageTchrFrm && !($(r).find('.department').val() > 0)) {
                    alert('Please select department');
                    $(r).find('.department').focus();
                    valid = 0;
                }else if (isManageTchrFrm && !($(r).find('.categoty_id').val() > 0)) {
                    alert('Please select category');
                    $(r).find('.categoty_id').focus();
                    valid = 0;
                } else if (isManageTchrFrm && !($(r).find('.assessor_id').val() > 0)) {
                    alert('Please select assessor');
                    $(r).find('.assessor_id').focus();
                    valid = 0;
                }

                if (valid == 0) {
                    return false;
                }
            }
        });
    }
    }
        if (valid > 0) {
            var f = $("#tchrAssessorsForm,#addTchrToAssmntForm");
            var param = f.serialize() + "&token=" + getToken() + "&proceedWithRejected=" + ignoreRejected + (this.id == "submitTchrsForAssessmnt" ? "&submit=1" : "");
            $("#validationErrors").html('');
            if(this.id != "add_edit_remove_teacher"){
            $(this).attr("disabled", "disabled");
            }
            $("#vtip").remove();
            apiCall(f,this.id == "saveTchrAssessorsForm" ? "saveTeacherAssessorsForm" : "saveTeachersForAssessmentForm", param, function (s, data) {
                //alert(data.add_type);
                if (s.attr("id") == "tchrAssessorsForm") {
                    if(data.add_type=="saveTeacherAssessorsForm"){
                    if (data.needConfirmation.length > 0) {
                        $("body").trigger('tchrAssessorsChanged');
                        var m = '';
                        for (var i = 0; i < data.needConfirmation.length; i++) {
                            m += data.needConfirmation[i].em + "<br>";
                        }
                        addError("#validationErrors", 'Following emails already exists in our records with <b>Rejected</b> status. <br>' + m + '<br><button onclick="$(\'#saveTchrAssessorsForm\').data(\'ignorerejected\',\'1\');$(\'#saveTchrAssessorsForm\').trigger(\'click\');" class="btn btn-primary">Click here</button> if you want to continue?');
                    } else {
                        $("#tchrAssessorsForm .tag_boxes .eAssessorNode").remove();
                        for (pro in data.content) {
                            var e = $("#tchrAssessorsForm #status_id_" + pro).append(data.content[pro]).find('.empty');
                            data.content[pro] == '' ? e.removeClass('notEmpty') : e.addClass('notEmpty');
                            ;
                        }
                        
                       
                         //alert($("#taid").val());   
                        if ($(".teacher_team").length > 0) { 
                         apiCall("#tchrAssessorsForm", "getReviewerListforSchool", {"token": getToken(), "grp_sch_id": $("#taid").val()}, function (s1, data1) {
                         //alert(data1); 
                         $('.teacher_team .team_row').each(function () {
                          var aDd = $(this).find('#assessor_id');
                          var currObjVal = aDd.val();
                          aDd.find("option").next().remove();
                          aDd.find("option").next().remove();
                          
                          addOptions_Optgroup(aDd, data1.reviwerlist, 'user_id', 'name','External');
                          //aDd.append('</optgroup>');
                          addOptions_Optgroup(aDd, data1.internalreviwerlist, 'user_id', 'name','Internal');
                          aDd.val(currObjVal);
                          aDd.selectpicker('refresh');
                         });
                         
                          //$(".teacher_team").show();
                          //$("#up_teacher").show();
                         
                        },function (s1, msg1, data1) {
                //$("body").trigger(s.find(".teacherAssessor_team").data('trigger'));
                alert(msg1);
            });
        }
                        
                        $("#tchrAssessorsForm .teacherAssessor_team .team_row").nextAll().remove();
                        $("#tchrAssessorsForm .teacherAssessor_team .team_row input").val('');
                        
                    }
                }else{
                    $("#tchrAssessorsForm .teacher_team .table tbody").html(data.content);
                    $('#tchrAssessorsForm .teacher_team .selectpicker').selectpicker();
                    $('#tchrAssessorsForm .teacher_team .date-Picker').datetimepicker({format: 'DD-MM-YYYY', pickTime: false}).on('dp.change', function(e){
        //alert("dsdgggdd");
        $("#saveTchrsForAssessmnt").removeAttr("disabled");
        $("#submitTchrsForAssessmnt").attr('disabled', "disabled");
        $("#vtip").remove();
        });
                    if (data.submitted) {
                        $("#submitTchrsForAssessmnt,#saveTchrsForAssessmnt").remove();
                    } else {
                        $("#saveTchrsForAssessmnt").attr('disabled', "disabled");
                        
                        data.enableSubmit == 1 ? $("#submitTchrsForAssessmnt").removeAttr('disabled') : $("#submitTchrsForAssessmnt").attr('disabled', "disabled");
                    }
                    alert(data.message);
                    if (data.submitted) {
                    location.reload();    
                    }
                }
                } else {
                    $("#addTchrToAssmntForm .teacherAssessor_team .table tbody").html(data.content);
                    $('#addTchrToAssmntForm .teacherAssessor_team .selectpicker').selectpicker();
                    
                    $('#addTchrToAssmntForm .teacherAssessor_team .date-Picker').datetimepicker({format: 'DD-MM-YYYY', pickTime: false}).on('dp.change', function(e){
        //alert("dsdgggdd");
        $("#saveTchrAssessorsForm").removeAttr("disabled");
        $("#submitTchrsForAssessmnt").attr('disabled', "disabled");
        $("#vtip").remove();
        });
                    if (data.submitted) {
                        $("#submitTchrsForAssessmnt,#saveTchrsForAssessmnt").remove();
                    } else {
                        $("#saveTchrsForAssessmnt").attr('disabled', "disabled");
                        data.enableSubmit == 1 ? $("#submitTchrsForAssessmnt").removeAttr('disabled') : $("#submitTchrsForAssessmnt").attr('disabled', "disabled");
                    }
                    alert(data.message);
                    if (data.submitted) {
                    location.reload();    
                    }
                }
            }, function (s, msg, data) {
                $("body").trigger(s.find(".teacherAssessor_team").data('trigger'));
                alert(msg);
            }, function (s, d) {
                $("body").trigger(s.find(".teacherAssessor_team").data('trigger'));
            });
        }
    });
    $(document).on("click", ".selectAll_eAssessorNode", function () {
        $(this).parents(".tag_boxes").first().find(".eAssessorNode").addClass('selected');
    });
    $(document).on("click", ".approve_eAssessorNode", function () {
        var s = $(this).parents(".tag_boxes").first().find(".eAssessorNode.selected");
        s.find("input").val('1');
        s.appendTo("#status_id_1-0.tag_boxes").removeClass('selected');
        s.length ? $("body").trigger('tchrAssessorsChanged') : '';
        $(this).parents(".tag_boxes").first().find(".eAssessorNode").length == 0 ? $(this).parents(".tag_boxes").first().find('.empty').removeClass('notEmpty') : '';
        $("#status_id_1-0.tag_boxes .empty").addClass('notEmpty');
        $('#saveTchrAssessorsForm').trigger('click');
        
    });
    $(document).on("click", ".reject_eAssessorNode", function () {
        var s = $(this).parents(".tag_boxes").first().find(".eAssessorNode.selected");
        s.find("input").val('3');
        s.appendTo("#status_id_3.tag_boxes").removeClass('selected');
        s.length ? $("body").trigger('tchrAssessorsChanged') : '';
        $(this).parents(".tag_boxes").first().find(".eAssessorNode").length == 0 ? $(this).parents(".tag_boxes").first().find('.empty').removeClass('notEmpty') : '';
        $("#status_id_3.tag_boxes .empty").addClass('notEmpty');
        $('#saveTchrAssessorsForm').trigger('click');
    });
    $(document).on("click", "#tchrAssessorsForm #status_id_3 .uname,#tchrAssessorsForm #status_id_2 .uname", function () {
        $(this).parents(".eAssessorNode").first().toggleClass('selected');
    });
    $(document).on("click", "#addTeacherlink", function () {
        if ($("#saveTchrAssessorsForm").length > 0 && $("#saveTchrAssessorsForm").attr("disabled") != "disabled") {
            return confirm("Unsaved data found.\nDo you want to continue without saving?");
        }
    });
    $(document).on("click", ".assessment-list .collapseGA", function () {
        var gaid = $(this).parents("tr").first().data('gaid');
        if ($(this).hasClass('fa-minus-circle')) {
            $(".assessment-list .ga-rows-" + gaid).hide();
            $(this).removeClass('fa-minus-circle').addClass("fa-plus-circle").parents("tr").first().removeClass('tchrAssmHead');
        } else if ($(".assessment-list .ga-rows-" + gaid).length) {
            $(".assessment-list .ga-rows-" + gaid).show();
            $(this).removeClass('fa-plus-circle').addClass("fa-minus-circle").parents("tr").first().addClass('tchrAssmHead');
        } else {
            apiCall(this, "getAssessmentOfGrpAss", "token=" + getToken() + "&gaid=" + gaid, function (s, data) {
                var cls = $(s).removeClass('fa-plus-circle').addClass("fa-minus-circle").parents("tr").first().addClass('tchrAssmHead').after(data.content).hasClass('even') ? 'even' : 'odd';
                $(".assessment-list .ga-rows-" + data.gaid).addClass(cls);
            }, function (s, msg) {
                alert(msg);
            });
        }
    });
    
    $(document).on("click", "#tchrAssessorsForm #collapseA", function () {
        if ($(this).hasClass('fa-minus-circle')) {
        $(".assessor_show_hide").slideToggle();    
        $(this).removeClass('fa-minus-circle').addClass("fa-plus-circle");
        $("#up_assessor").hide();
        }else if($(this).hasClass('fa-plus-circle')){
        $(".assessor_show_hide").slideToggle();      
        $(this).removeClass('fa-plus-circle').addClass("fa-minus-circle");
        $("#up_assessor").show();
        }
    });
    
    $(document).on("click", "#tchrAssessorsForm #collapseT", function () {
        if ($(this).hasClass('fa-minus-circle')) {
        $(".teacher_show_hide").slideToggle();    
        $(this).removeClass('fa-minus-circle').addClass("fa-plus-circle");
        $("#up_teacher").hide();
        }else if($(this).hasClass('fa-plus-circle')){
        $(".teacher_show_hide").slideToggle();      
        $(this).removeClass('fa-plus-circle').addClass("fa-minus-circle");
        $("#up_teacher").show();
        }
    });

    $(".nuibody .related").on("mouseenter", "input", function () {
        $(this).addClass("editLblField");
        $(this).removeAttr('readonly', 'readonly');
    }).on("mouseout", "input", function () {
        $(this).removeClass("editLblField");
        $(this).attr('readonly', 'readonly');
    });

    $("#chooseAssmt").trigger('click');
    $(document).on('click', '#updateTeacherAssessment', function () {
        var frm = $("#editTeacherAssessment");
        var cid = frm.find('#selected_client_id').val();
        var dSelected = 0;
        frm.find(".diagnostic_dd").each(function (i, v) {
            if ($(v).val() != "" && $(v).val() > 0) {
                dSelected = 1;
            }
        });
        if (cid == "" || !(cid > 0)) {
            alert('Please select a school');
        } else if (dSelected == 0) {
            alert('Please select a diagnostic');
        } else {
            postData = frm.serialize() + "&token=" + getToken();
            apiCall(frm, "updateTeacherAssessment", postData, function (s, data) {
                //showSuccessMsgInMsgBox(s,data);
                alert(data.message);
                //$("#saveTchrsForAssessmnt").removeAttr('disabled', "disabled");
                //$('#saveTchrsForAssessmnt').trigger('click');
                location.reload();
                if ($(".assessment-list").length > 0)
                    filterByAjax($(".assessment-list"));
            }, showErrorMsgInMsgBox);
        }

        return false;
    });
    
    $(document).on('click', '#updateStudentAssessment', function () {
        var frm = $("#editStudentAssessment");
        var cid = frm.find('#selected_client_id').val();
        var dSelected = 0;
        frm.find(".diagnostic_dd").each(function (i, v) {
            if ($(v).val() != "" && $(v).val() > 0) {
                dSelected = 1;
            }
        });
        if (cid == "" || !(cid > 0)) {
            alert('Please select a school');
        } else if (dSelected == 0) {
            alert('Please select a diagnostic');
        } else {
            postData = frm.serialize() + "&token=" + getToken();
            apiCall(frm, "updateStudentAssessment", postData, function (s, data) {
                //showSuccessMsgInMsgBox(s,data);
                alert(data.message);
                //$("#saveTchrsForAssessmnt").removeAttr('disabled', "disabled");
                //$('#saveTchrsForAssessmnt').trigger('click');
                location.reload();
                if ($(".assessment-list").length > 0)
                    filterByAjax($(".assessment-list"));
            }, showErrorMsgInMsgBox);
        }

        return false;
    });
    
    $(document).on("submit", "#choose_review_type_form", function () {
        reviewType = $("input[name=reviewtype]:checked").val();

        var frm = $("#choose_review_type_form");
        var postData = '';
        if (reviewType == '' || reviewType == undefined)
        {
            alert('Please choose review type');
            return false;
        } else if (reviewType == 2 || reviewType == 3)
        {
            // send mail to adhyayan admin 
            postData = frm.serialize() + "&token=" + getToken();
            apiCall(frm, "sendMail", postData, function (s, data) {
                //showSuccessMsgInMsgBox(s,data);
                alert(data.message);

            }, function (msg, a) {
                alert("Error");
                console.log(msg);
                console.log(a)
            });
        } else
        {
            /*postData = frm.serialize() + "&token=" + getToken();
            apiCall(frm, "checkPaymentSelfReview", postData, function (s, data) {
                //showSuccessMsgInMsgBox(s,data);
                if (data.auth == 0)
                    alert(data.message);
                else if (data.auth == 1)
                    $("#showschoolselfrev").trigger('click');
            }, function (msg, a) {
                alert("Error")
            });*/
            $("#showschoolselfrev").trigger('click');
        }
        $(this).parents(".modal").modal("hide");
        return false;


    });
    $(document).on("click", ".pay-now", function () {
        //for trial run pay now sets default values
        var product = 1;
        var paymentMode = 1; // online
        var assessmentId = $(this).data('asmt-id');
        var trnsId = Math.floor((Math.random() * 10000) + 1);
        var postData = "&token=" + getToken() + "&transactionId=" + trnsId + "&product=" + product + "&payment_mode_id=" + paymentMode + "&assessment_id=" + assessmentId;
        apiCall($(this), "saveClientProduct", postData, function (s, data) {
            //showSuccessMsgInMsgBox(s,data);
            //alert(data.message);
            alert("This is for trial run. You can now proceed further to self-review.");
            if ($(".assessment-list").length > 0)
                filterByAjax($(".assessment-list"));
        }, showErrorMsgInMsgBox);
        return false;
    });
    $(document).on("submit", "#choose_product_form", function () {
        var product = $("input[name=product]:checked").val();
        if (product == '' || product == undefined)
        {
            alert('Please choose product type');
            return false;
        }
        var frm = $("#choose_product_form");
        // var cid=frm.find('#selected_client_id').val();	
        var trnsId = Math.floor((Math.random() * 10000) + 1);
        postData = frm.serialize() + "&token=" + getToken() + "&transactionId=" + trnsId;
        apiCall(frm, "saveClientProduct", postData, function (s, data) {
            //showSuccessMsgInMsgBox(s,data);
            alert(data.message);
            $(this).parents(".modal").modal("hide");
            //location.reload();
            if ($(".assessment-list").length > 0)
                filterByAjax($(".assessment-list"));
        }, showErrorMsgInMsgBox);
        $(this).parents(".modal").modal("hide");
        return false;
    });
    $(document).on("click", ".apr-pmt", function () {
        console.log("assessment Id:" + $(this).data('asmt-id'));

        var assessmentId = $(this).data('asmt-id');
        var isApproved = $(this).data('approve');
        var approvalString ='';
        approvalString = isApproved==2?'reject':'approve';
        var approvalStringMsg = isApproved==2?'rejected':'approved';
         var reason = '';
        if (confirm("Are you sure you want to "+approvalString+" this review?"))
        {
            if(isApproved==2){
                reason= prompt("Please enter the reason for rejection:");
                if(!isValidText(reason))
                {
                    alert("Reason cannot be empty");return false;
                }
            }
            if ((assessmentId != '' || assessmentId != undefined))
            {
                var postData = "token=" + getToken() + "&assessmentId=" + assessmentId+"&isApproved="+isApproved+"&reason="+reason;
                apiCall($(this), "approveReview", postData, function (s, data) {
                    alert("The review has been "+approvalStringMsg);
                    if ($(".assessment-list").length > 0)
                        filterByAjax($(".assessment-list"));
                }, function (msg) {
                    alert("Error in approving review");
                });
            }
        }
    });
    
    $(document).on("change","#resource_type",function() { 
    
            if($(this).is(':checked')) {
                $("#school_type_block").show();
            }else {
                
                $("#school_type_block").hide();
            }
    });

    $(document).on("change", "#create_school_form #country_id, #edit_school_form #country_id, #school_dashboard_frm #country_id", function () {
        var countryId = $(this).parents('form').find('#country_id').val();
        var aDd = $(this).parents('form').find('#state_id');
        var aDd2 = $(this).parents('form').find('#city_id');

        if (countryId > 0) {
            apiCall(this, "getStateByCountry", {"token": getToken(), "country": countryId}, function (s, data) {
                aDd.find("option").next().remove();
                aDd2.find("option").next().remove();
                addOptions(aDd, data.states, 'state_id', 'state_name');
            }, showErrorMsgInMsgBox);
        }
        return false;
    });
    $(document).on("change", "#create_school_form #state_id, #edit_school_form #state_id, #create_student_profile #state_id", function () {
        //alert("ok");
        var state_id = $(this).parents('form').find('#state_id').val();
        var aDd = $(this).parents('form').find('#city_id');
        if (state_id > 0) {
            apiCall(this, "getCityByState", {"token": getToken(), "state": state_id}, function (s, data) {
                aDd.find("option").next().remove();
                addOptions(aDd, data.cities, 'city_id', 'city_name');
            }, showErrorMsgInMsgBox);
        }
        return false;
    });
    $(document).on("change", ".filterByAjax [name=country_id]", function () {
        var countryId = $(this).parents('form').find('[name=country_id]').val();
        var aDd = $(this).parents('form').find('[name=state_id]');
        var aDd2 = $(this).parents('form').find('[name=city]');

        if (countryId > 0) {
            apiCall(this, "getStateByCountry", {"token": getToken(), "country": countryId}, function (s, data) {
                aDd.find("option").next().remove();
                aDd2.find("option").next().remove();
                addOptions(aDd, data.states, 'state_id', 'state_name');
            }, showErrorMsgInMsgBox);
        }
        return false;
    });
    $(document).on("change", ".filterByAjax [name=state_id]", function () {
        var state_id = $(this).parents('form').find('[name=state_id]').val();
        var aDd = $(this).parents('form').find('[name=city]');
        if (state_id > 0) {
            apiCall(this, "getCityByState", {"token": getToken(), "state": state_id}, function (s, data) {
                aDd.find("option").next().remove();
                addOptions(aDd, data.cities, 'city_name', 'city_name');
            }, showErrorMsgInMsgBox);
        }
        return false;
    });
    $(document).on("change", ".filterByAjax [name=network_id]", function () {
        var networkId = $(this).parents('form').find('[name=network_id]').val();        
        var aDd2 = $(this).parents('form').find('[name=province_id]');
        if (networkId > 0) {
            apiCall(this, "getProvincesInNetwork", {"token": getToken(), "network_id": networkId}, function (s, data) {                
                aDd2.find("option").next().remove();
                addOptions(aDd2, data.message, 'province_id', 'province_name');
            }, showErrorMsgInMsgBox);
        }
        return false;
    });
    
    
    
    /*$(document).on('change','#editTeacherAssessment .school_admin_id,.diagnostic_dd,.eAssessorNode .delete',function(){		
     $("body").trigger('tchrAsmtChanged');
     });
     $(document).on('DOMNodeInserted','#editTeacherAssessment #external_reviewers_block',function(){		
     $("body").trigger('tchrAsmtChanged');
     });*/

    /*$(document).on('click','.nav-tabs a',function(e){
     //console.log($('.nav-tabs li').index($(this)))
     //console.log($('.nav-tabs li').index($(this)))
     if(!$("#updateTeacherAssessment").attr('disabled'))
     {			
     alert("Please save data before proceeding to step 2.");
     
     }	
     if(!$("#saveTchrAssessorsForm").attr('disabled'))
     {			
     alert("Please save data before proceeding to step 1.");				
     }
     });*/

    // function for sending invite mail to aqs team users on 14-06-2016 by Mohit Kumar
    $(document).on("submit", "#send_signup_email", function () {
        if ($('#email').val() == '') {
            alert("Email can't empty!")
        } else {
            var postData = $('#send_signup_email').serialize();
            apiCall($('#send_signup_email'), "sendSignUpEmail", postData + "&token=" + getToken(),
                    function (s, data) {
                        var s = $("#send_signup_email");
                        showSuccessMsgInMsgBox(s, data);
                        if (data.status == 1) {
                            $('#email').val('');
                        }
                        if ($(".user-list").length) {
                            filterByAjax($(".user-list"));
                        }
                    }, showErrorMsgInMsgBox
                    );
        }
        return false;
    });

    // function for update password on 25-07-2016 by Mohit Kumar
    $(document).on('submit', '#update_user_password', function () {
        if ($('.pwd').val() == '' && $('.pwd').val().length < 6) {
            alert('New Password can not be empty!');
        } else if ($('.cpwd').val() == '' && $('.cpwd').val().length < 6) {
            alert('Confirm Password can not be empty!')
        } else if ($('.pwd').val() != $('.cpwd').val()) {
            alert('New and Confirm password should be same!')
        } else {
            var postData = $('#update_user_password').serialize();
            apiCall($('#update_user_password'), "updateUserPassword", postData + "&token=" + getToken(),
                    function (s, data) {
                        var s = $("#update_user_password");
                        showSuccessMsgInMsgBox(s, data);
                        if (data.status == 1) {
                            $('.pwd').val('');
                            $('.cpwd').val('');
                        }
                    }, showErrorMsgInMsgBox
                    );
        }
        return false;
    });
    
    $(document).on('click','.delfolder', function(){
        //alert($(this).attr("id"));
        var directoryId = $(this).attr("id");
        //var postData = new FormData(this);
       // postData.append("token", getToken());
       // postData.append('file', file);
      
        var postData = "directory_id="+directoryId+"&token="+ getToken()
        var querystring = '';
       // alert('aaaa'+postData);
         apiCall('#list_resourcedirectory_form', "deleteFolder", postData,
                    function (s, data) {
                        showSuccessMsgInMsgBox(s, data);
                        if(data.status == 1) {
                            if(data.parent_id>=1 && data.childs < 1 && data.num_files < 1) {
                                $(".parent-"+data.parent_id).hide();
                                $(".child-"+data.parent_id).removeAttr('style');
                                $(".action-"+data.parent_id).append('<a id="'+data.parent_id+'" class="delete-btn-resource vtip delfolder" href="#" title="Delete"><i class="edit-btn-resource vtip fa fa-trash-o" title="Delete"></i>')
                            }
                            $("."+directoryId).hide();
                        }

                    }, showErrorMsgInMsgBox);
            return false;
        
        
    });
    $(document).on('click','.dwndhistory', function(){
        
       var resource_id = $(this).attr('id');
       var postData = "resource_id="+resource_id +"&token=" + getToken();
            apiCall(this, "resourceDownloadHistory", postData, function (s, data) {
                if (data.message != '') {
                    file_url = data.site_url+"public/resources/resource_log.xlsx";
                     window.open(file_url,'_blank' );
                } else {
                    //$('#provinces').hide();
                   // getSchools(network_id, this);
                }
            }, showErrorMsgInMsgBox);
        
        return false;
    });
     

    
    

    //function for create resource
    $(document).on("submit", "#create_resource_form", function (event) {
        var file = $("#resource_file")[0].files[0];

        var postData = new FormData(this);
        
        postData.append("token", getToken());
        postData.append('file', file);
        //console.log(JSON.stringify(postData));
        //alert(postData.toSource());
        apiCall(this, "createResource", postData,
                function (s, data) {
                    showSuccessMsgInMsgBox(s, data);
                    $(s).find("textarea,input[type=text],input[type=file]").val('');
                    $("option:selected").removeAttr("selected");
                    $(s).find("#file_attached").html('');
                    $(s).find("input[type=checkbox]").not("[disabled]").prop('checked', false);
                    $(s).find("#file").hide();
                    $(s).find("#url").hide();
                    //$(s).find("textarea,input[type=text],input[type=file]").val('');
                    $('#create_resource_form').trigger("reset");
                    $('#resource_type').attr('checked', false);
                    $("#rec_network").multiselect('refresh');
                    $("#school_type_block").hide();
                    $("#provinces").hide();                    
                    $("#rec_roles").hide();                    
                    $("#rec_schools").hide();
                    $("#rec_users").hide();
                    $("#school_type_block").hide();
                    if ($(".resource-list").length) {
                        filterByAjax($(".resource-list"));
                    }
                }, showErrorMsgInMsgBox, undefined, {processData: false, contentType: false});
        return false;
    });
  // create resource directory 
    $(document).on("submit", "#create_resourcedirectory_form", function (event) {
         var dir_name = $("#dir_name").val();
         var redirect_url = $("#redirec_url").val();
         
         var parent_dir_id = $("input:checkbox[name=directory]:checked").val();
         //alert(parent_dir_id);
         if (!$.trim(dir_name) ) {
            alert('Please enter a folder name');
        }else if(parent_dir_id == '' || parent_dir_id == 'undefined' || parent_dir_id == null) {
            alert('Please select a folder');
        }else {
            var postData = $('#create_resourcedirectory_form').serialize();
            apiCall(this, "createResourceDirectory", postData + "&token=" + getToken(),
                    function (s, data) {
                        if(data.status == 1)  {
                             $('input:checkbox').removeAttr('checked');
                        showSuccessMsgInMsgBox(s, data);
                       
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    }
                        
                    }, showErrorMsgInMsgBox
                    );
            
        }
        return false;
    });

    
    $(document).on("submit", "#edit_resourcedirectory_form", function (event) {
         var dir_name = $("#dir_name").val();
         var redirect_url = $("#redirec_url").val();
         
        // var parent_dir_id = $("input:checkbox[name=directory]:checked").val();
         //alert(parent_dir_id);
         if (!$.trim(dir_name) ) {
            alert('Please enter a folder name');
        }else {
            var postData = $('#edit_resourcedirectory_form').serialize();
            apiCall(this, "editResourceDirectory", postData + "&token=" + getToken(),
                    function (s, data) {
                        if(data.status == 1) 
                        showSuccessMsgInMsgBox(s, data);
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                        
                    }, showErrorMsgInMsgBox
                    );
        }
        return false;
    });
    
    $(document).on("click", "#addfolderbutton", function (event) {
         var dir_name = $("#dir_name").val();
         var contnr = $(this).parents("form").attr('id');
         contnr = "#"+contnr;
        // var redirect_url = $("#redirec_url").val();
         
         var parent_dir_id = $("input:radio[name=directory]:checked").val();
         //var contnr = 
         //alert(parent_dir_id);
         if (!$.trim(dir_name) ) {
            alert('Please enter a folder name');
        }else if(parent_dir_id == '' || parent_dir_id == 'undefined' || parent_dir_id == null) {
            alert('Please select a folder');
        }else {
            var postData = "new_dir_name="+dir_name+"&directory="+parent_dir_id;
            apiCall(contnr, "createResourceDirectory", postData + "&token=" + getToken(),
                    function (s, data) {
                        if(data.status == 1) 
                        showSuccessMsgInMsgBox(s, data);
                        setTimeout(function () {
                             $("#dir_name").val("");
                            $(".addfolder").hide();
                            //var querystring = "?controller=resource&action=createFolderTree&ajaxRequest=1";
                            
                               /* $( ".resources" ).load( "?controller=resource&action=createFolderTree&ajaxRequest=1" ,{ token: getToken() },function(response, status, xhr){
                                     jsonObj = $.parseJSON(response);
                                        $( ".resources" ).html(jsonObj.content);
                                });*/
                            callFolder();
                        }, 2000);
                        
                    }, showErrorMsgInMsgBox
                    );
        }
        return false;
    });
    $(document).on("click", "#create_resource_form .expand, #create_resource_form .collapse1,#list_resourcedirectory_form .expand, #list_resourcedirectory_form .collapse1,#create_resourcedirectory_form .expand, #create_resourcedirectory_form .collapse1,#edit_resource_form .expand, #edit_resource_form .collapse1", function () {
     //$(".expand,.collapse1").click(function () {
            $(this).toggle();
            $(this).prev().toggle();
            $(this).parent().parent().children().last().toggle();
            
        });
        $(document).on("click", "#create_resourcedirectory_form .expand1, #create_resource_form .collapse, #list_resourcedirectory_form .collapse,#create_resourcedirectory_form .collapse, #edit_resource_form .collapse", function () {
        //$(".collapse,.expand1").click(function () {
            $(this).toggle();
            $(this).next().toggle();
            $(this).parent().parent().children().last().toggle();
        });
        
        //collapse and expand by folder name
        $(document).on("click", ".expandName", function () {
        //$(".collapse,.expand1").click(function () {
        var liClass = $(this).closest("li").attr('class');
        liClass = liClass.split(" ");
        //alert(liClass);
        if(liClass[1]!='' && liClass[1]!='undefined') {
             if($("."+liClass[1]).closest("li").children("ul").length) {
                //alert("ok"+liClass);
                $("."+liClass[1]).closest("li").children("ul").toggle();
                var a = $("."+liClass[1]).closest("li").children().attr("class");
                var parentId = a.split(" ");
               // alert(parentId[1]);
                if(parentId[1]!='' && parentId[1]!='undefined') {
                    
                     $("."+parentId[1]).find(".expand").toggle();
                     $("."+parentId[1]).find(".collapse").toggle();
                 }
            
             }
       }
       
       
          
        });
        //collapse and expand by folder name
        $(document).on("click", ".expandName1", function () {
        //$(".collapse,.expand1").click(function () {
        var liClass = $(this).closest("li").attr('class');
        liClass = liClass.split(" ");
        //alert(liClass);
        if( liClass.length<2) {
            liClass[1] = liClass[0];
        }       
        if(liClass[1]!='' && liClass[1]!='undefined') {
            
             if($("."+liClass[1]).closest("li").children("ul").length) {
                //alert("ok"+liClass);
                $("."+liClass[1]).closest("li").children("ul").toggle();
                var a = $("."+liClass[1]).closest("li").children().attr("class");
                var parentId = a.split(" ");
                if(parentId[0]!='' && parentId[0]!='undefined') {
                    // $(this).closest("li").children().children().last().toggle();
                     $(this).closest("li").children().children('.expand').toggle();
                     $(this).closest("li").children().children('.collapse').toggle();
                    
//                     /$("."+parentId[0]).find(".collapse").toggle();
                 }
            
             }
       }
       
       
          
        });
        $('input[name="directory"]').on('change', function() {
            $('input[name="' + this.name + '"]').not(this).prop('checked', false);
        });
        
        $("li").click(function() {
            if($(this).next("li").attr('class') == 'file_tree') {
            if($(this).next("li"). css('display') == 'none') {
              $(this).next("li"). css("display", "block");
              if(  $(this).children('div').last().attr("class") == "chkbox") {
                  $(this).children('div').last().css("display", "block");
              }
               //$(".chkbox").css("display", "block");
            }else {
                 $(this).next("li"). css("display", "none");
            }
       }
        //alert($(this).next("li").attr('class'));
        //alert("test: ");
    });

    //function for update resource
    $(document).on("submit", "#edit_resource_form", function (event) {
        var postData = new FormData(this);
        postData.append("token", getToken());

        apiCall(this, "updateResource", postData,
                function (s, data) {
                    /*if ($(".resource-list").length) {
                        filterByAjax($(".resource-list"));
                    }
                    if ($(".resource_edit").length) {
                        filterByAjax($(".resource_edit"));
                    }*/
                   $(s).find("#file_attached").html('');
                    showSuccessMsgInMsgBox(s, data);
                }, showErrorMsgInMsgBox, undefined, {processData: false, contentType: false});
        return false;
    });
    //function for update folder
    $(document).on("submit", "#edit_folder_form", function (event) {
        var postData = new FormData(this);
        postData.append("token", getToken());

        apiCall(this, "updateFolder", postData,
                function (s, data) {
                    /*if ($(".resource-list").length) {
                        filterByAjax($(".resource-list"));
                    }
                    if ($(".resource_edit").length) {
                        filterByAjax($(".resource_edit"));
                    }*/
                   $(s).find("#file_attached").html('');
                    showSuccessMsgInMsgBox(s, data);
                }, showErrorMsgInMsgBox, undefined, {processData: false, contentType: false});
        return false;
    });

    /*
     * Function for upload user_excel_file_form
     */
    $(document).on("submit", "#user_excel_file_form", function (event) {
        var postData = new FormData(this);
        postData.append("token", getToken());
        apiCall(this, "uploadUserDetails", postData, 
        function (s, data) {
            
            //setToken(data.token);
            showSuccessMsgInMsgBox(s, data);
            // $("#login_popup").find("input[type=email],input[type=password]").val(''); 
            //$("#login_popup_wrap").removeClass("active").trigger("loggedIn");
        },
                showErrorMsgInMsgBox, undefined, {processData: false, contentType: false});
        return false;
    }); 
     $(document).on("submit","#create_network_form",function(){
		var postData=$(this).serialize()+"&token="+getToken();
		apiCall(this,"createNetwork",postData,
			function(s,d){				
				apiCall(s,"getNetworkList",{"token":getToken()},function(s,data){					
					networks =[]
					for(var i=0;i<data.networks.length;i++)
						networks.push(data.networks[i].network_name);
					data.message = d.message;
					showSuccessMsgInMsgBox(s,data);	
					$('.the-basics.network .typeahead').typeahead('destroy');
					 $('.the-basics.network .typeahead').typeahead({
						hint: true,
						highlight: true,
						minLength: 3		
					  },
					  {
						name: 'networks',
						source: substringMatcher(networks),
						limit:30
					  });
					},showErrorMsgInMsgBox);
				
				if($(".network-list").length){
					filterByAjax($(".network-list"));
				}
				if($("#scl_network").length){
					var nDd=$("#scl_network");
					nDd.find("option").next().remove();
					apiCall(s,"getNetworkList",{"token":getToken()},function(s,data){addOptions(nDd,data.networks,'network_id','network_name')},showErrorMsgInMsgBox);
				}
				if($(".network-list-dropdown").length){
					var nDd=$(".network-list-dropdown");
					nDd.find("option").next().remove();
					apiCall(s,"getNetworkList",{"token":getToken()},function(s,data){addOptions(nDd,data.networks,'network_id','network_name')},showErrorMsgInMsgBox);
				}
				$(s).find("input[type=text]").val('');
                               // $("#popup-network_createNetwork").data('bs.modal',null);
                               // $("#popup-network_createNetwork").modal('show');
			},showErrorMsgInMsgBox);
		return false;
	});
        
        $(document).on("submit", "#create_province_form", function () {
	      var  postData = $(this).serialize() + "&token=" + getToken();
	        apiCall(this, "createProvince", postData, function (s, d) {
				apiCall(s,"getProvinceList",{"token":getToken()},function(s,data){					
					provinces =[]
					for(var i=0;i<data.provinces.length;i++)
						provinces.push(data.provinces[i].province_name);
					data.message = d.message;
					//showSuccessMsgInMsgBox(s,data);	
					$('.the-basics.province .typeahead').typeahead('destroy');
					 $('.the-basics.province .typeahead').typeahead({
						hint: true,
						highlight: true,
						minLength: 3		
					  },
					  {
						name: 'provinces',
						source: substringMatcher(provinces),
						limit:30
					  });
					},showErrorMsgInMsgBox);	            
	            $(s).find("select").val('');
	            $(s).find("input[type='text']").val(''); 
				if($(".network-list").length){
					filterByAjax($(".network-list"));
				if($(".network-list-dropdown").length){
					var nDd=$(".network-list-dropdown");
					nDd.find("option").next().remove();
					apiCall(s,"getNetworkList",{"token":getToken()},function(s,data){addOptions(nDd,data.networks,'network_id','network_name')},showErrorMsgInMsgBox);
				}	
                               // $("#popup-network_createProvince").data('bs.modal',null);
                               // $("#popup-network_createProvince").modal('show');
				showSuccessMsgInMsgBox(s, d);                                
				}	
	        }, showErrorMsgInMsgBox);
	        return false;
	    });		
            
            $(document).on("submit","#update_network_form",function(){
		var postData=$(this).serialize()+"&token="+getToken();
		apiCall(this,"updateNetwork",postData,
		function(s,d){			
			apiCall(s,"getNetworkList",{"token":getToken()},function(s,data){					
					networks =[]
					data.message = d.message;
					showSuccessMsgInMsgBox(s,data);
					for(var i=0;i<data.networks.length;i++)
						networks.push(data.networks[i].network_name)											
					$('.the-basics.network .typeahead').typeahead('destroy');
					 $('.the-basics.network .typeahead').typeahead({
						hint: true,
						highlight: true,
						minLength: 3		
					  },
					  {
						name: 'networks',
						source: substringMatcher(networks),
						limit:30
					  });
					},showErrorMsgInMsgBox);
			if($(".network-list").length){
				filterByAjax($(".network-list"));
			}
                        //$("#popup-network_editNetwork").data('bs.modal',null);
                        //$("#popup-network_editNetwork").modal('show');
                 showSuccessMsgInMsgBox(s, d);   
		},showErrorMsgInMsgBox);
                 
		return false;
	});
        
        $(document).on("click","#aqsReportView",function(){ 
            var reportUrl = $(this).attr('rel');
            var reportId = $(this).data('reportid');
            var langId = $("#lang_id").val();
            
            //alert(reportUrl);
            if(langId=="" && (reportId==1 || reportId==2 || reportId==3)){
             alert("Please select language"); 
             return false;
            }
            else if(reportId==1 || reportId==2 || reportId==3){
            var reportUrl = reportUrl+"&lang_id="+langId;
            }
            
            window.open(reportUrl, '_blank');
            return false;
            //$( this ).attr( 'target', '_blank' );
             //window.location.href= redirectUrl;
           // alert(reportUrl+"&lang_id="+langId);
           // return false;
        });
        
         $(document).on("submit","#update_network_province_form",function(){
			postData=$(this).serialize()+"&token="+getToken();
			apiCall(this,"updateProvince",postData,
			function(s,d){			
				apiCall(s,"getProvinceList",{"token":getToken()},function(s,data){					
					provinces =[]
					for(var i=0;i<data.provinces.length;i++)
						provinces.push(data.provinces[i].province_name);
					data.message = d.message;
					//showSuccessMsgInMsgBox(s,data);	
					$('.the-basics.province .typeahead').typeahead('destroy');
					 $('.the-basics.province .typeahead').typeahead({
						hint: true,
						highlight: true,
						minLength: 3		
					  },
					  {
						name: 'provinces',
						source: substringMatcher(provinces),
						limit:30
					  });
					},showErrorMsgInMsgBox);
                                if($(".network-list").length){
					filterByAjax($(".network-list"));
				}        
                               // $("#popup-network_editNetworkProvince").data('bs.modal',null);
                               // $("#popup-network_editNetworkProvince").modal('show');                              
				showSuccessMsgInMsgBox(s, d);				
			},showErrorMsgInMsgBox);                      
			return false;
		});
                
                /*$(document).on("change","#user_excel_file",function(){ 
                    
                    var fileData = $(this)[0].files[0];
                    var validExtensions = ["xlsx","xls"];
                    if(fileData.name!='') {
                        //console.log(fileData.name);
                        var name = fileData.name;
                        var ext = name.split(".").pop();
                        //alert(validExtensions.indexOf(ext));
                        if(validExtensions.indexOf(ext)==-1) {
                            alert("Invalid file extension");
                            //return false;
                        }
                    }
                    
                    //console.log(fileData.name);
                
                });*/
                //upload AQS data
                /*
                * Function for upload user_excel_file_form
                */
               $(document).on("submit", "#upload_aqs_form", function (event) {
                   var postData = new FormData(this);
                   var imgVal = $('#user_excel_file').val();
                   if(imgVal == '') {
                       alert("Please browse a file");
                            return false;
                   }else {
                        fileData = $('input[type="file"]').get(0).files[0];
                        var validExtensions = ["xlsx","xls"];
                         if( fileData.name!='') {

                             var name = fileData.name;
                             var ext = name.split(".").pop();
                             if(validExtensions.indexOf(ext)==-1) {
                                 alert("Invalid file extension");
                                 return false;
                             }
                         }
                    }
                   // return false;
                   if (confirm('Have you reviewed the data? If yes, please proceed further?')) {
                   postData.append("token", getToken());
                   apiCall(this, "uploadAQSDetails", postData, 
                   function (s, data) {

                       //showSuccessMsgInMsgBox(s, data);
                       if(data.message == 'Success') {
                           //showSuccessMsgInMsgBox(s, data);
                          $("#createresource").html(data.message+"<br>").removeClass('danger').addClass("success active");
                       }else {
                          // showErrorMsgInMsgBox();
                          $("#createresource").html(data.message+"<br>").removeClass('success').addClass("danger active");
                          
                          //$("#createresource").delay(2000).fadeOut();
                       }
                        $("#createresource").show();
                        //$("#createresource").append(data.file_name+"<br>");
                        //$("#createresource").append(data.uploaded_date+"<br>");
                       $("#validationAQSErrors").html("");
                       $("#validationAQSWarnings").html("");
                       //$("#validationAQSErrors").hide();
                       $("#warningStatus").hide();
                       //alert(data.error);
                       if(data!=undefined && data!=null && data.error!=undefined){
                           if(data.error.length) {
                               $(".uploadaqserrors").show();
                            for(var prop in data.error){                               
                                    $("#validationAQSErrors").append(data.error[prop]+"<br>");                                  
                            }
                            $("#warningStatus").show();
                        }else { $(".uploadaqserrors").hide(); }

                        }if(data!=undefined && data!=null && data.warnings!=undefined){
                           // alert(data.warnings.length);
                             if(data.warnings.length) {
                                  $(".uploadaqswarnings").show();
                            for(var prop in data.warnings){
                                    //$("#validationAQSErrors").append("Warnings <br>");
                                    $("#validationAQSWarnings").append(data.warnings[prop]+"<br>");
                                   // $("#validationAQSErrors").append("<br>");
                            }
                            $("#warningStatus").show();
                        }else { $(".uploadaqswarnings").hide(); }
                        }
                   },
                           showErrorMsgInMsgBox, undefined, {processData: false, contentType: false});
                   }
                return false;
               }); 
               
               // show all warning of AQS upload
               $(document).on("click", "#warningStatus", function (event) {
                   $("#myModal").show();
                   //$("#validationAQSErrors").show();
               });
               // show all warning of AQS upload
               $(document).on("click", "#uploadaqserrorpopup", function (event) {
                   $("#myModal").modal("hide")
               
               });
               $('#create_student_profile input').on('change', function() {
                   $('input[type="radio"]:checked').each(function(){
                       
                       var val = $(this).val();
                       var radioId = $(this).attr('id');
                       //alert(radioId);
                       if(val == 1 && radioId!='') {
                           
                           $("."+radioId).show();
                       }else if(radioId!='') {
                           $("."+radioId).hide();
                       }
                       //alert($(this).attr('id'));
                   })
                     //alert($("#create_student_profile input[type='radio']:checked").attr('id'));
                });
               
});



 $(document).on("submit", "#upload_student_form", function (event) {
                   var postData = new FormData(this);
                   var imgVal = $('#user_excel_file').val();
                   if(imgVal == '') {
                       alert("Please browse a file");
                            return false;
                   }else {
                        fileData = $('input[type="file"]').get(0).files[0];
                        var validExtensions = ["xlsx","xls"];
                         if( fileData.name!='') {

                             var name = fileData.name;
                             var ext = name.split(".").pop();
                             if(validExtensions.indexOf(ext)==-1) {
                                 alert("Invalid file extension");
                                 return false;
                             }
                         }
                    }
                   // return false;
                   if (confirm('Have you reviewed the data? If yes, please proceed further?')) {
                   postData.append("token", getToken());
                   apiCall(this, "uploadStudentDetails", postData, 
                   function (s, data) {
                   //alert(data.error_msg);
                       //showSuccessMsgInMsgBox(s, data);
                       
                       if(data.error_msg == 0) {
                           //showSuccessMsgInMsgBox(s, data);
                          $(".ajaxMsg").html(data.message+"<br>").removeClass('danger').addClass("success active");
                       }else {
                          // showErrorMsgInMsgBox();
                          $(".ajaxMsg").html(data.message+"<br>").removeClass('success').addClass("danger active");
                          
                          //$("#createresource").delay(2000).fadeOut();
                       }
                       $(s).find("textarea,input[type=text],input[type=file]").val('');
                       $(s).find("#file_attached").html('');
                       
                       
                   },
                           showErrorMsgInMsgBox, undefined, {processData: false, contentType: false});
                   }
                return false;
               }); 

function filterByAjax(s, page, orderBy, ordertype) {
    var f = $(s).hasClass("filterByAjax") ? $(s) : $(s).parents(".filterByAjax");
    if (f.data("controller") != undefined && f.data("action") != undefined) {
        var a = f.data("action");
        var c = f.data("controller");
        var querystring = f.data("querystring") == undefined ? "" : f.data("querystring");
         
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
            if(a=="resourcelist"){
            $(".loader").fadeOut("slow");
            }
        
        if(c=="actionplan" && a=="actionplan1"){
           
        $('.selectpicker').selectpicker('refresh');
        $(".ribWrap").hide();
        }
            
        }, showErrorMsgInMsgBox);
    }
}

function load_popup_page(senderForm, controller, action, postdata, queryString, size, top, modalclass) {
    ajaxCall(senderForm, controller, action, postdata, queryString, function (s, data) {
        var id = controller + "_" + action;
        $.createModal(id, "", data.content, size, top, modalclass);
        $("#popup-" + id + " .modal-title").html($("#popup-" + id + " .page-title").html());
        $("#popup-" + id + " .page-title").remove();
        
        if(controller=="actionplan" && action=="action1new"){
        $('.selectpicker').selectpicker('refresh');
        }
        //alert("test");
        if(action=="keyrecommendations"){
            
        $('.rec_dropdown').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%',
            maxHeight: 5 ,
            numberDisplayed: 1,
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                ul: '<ul class="multiselect-container dropdown-menu" style="width:100%;"></ul>',
               }
               
               });
               
           }
          
        
    }, showErrorMsgInMsgBox);
}

function loadAssesorListForAssessment(senderForm, assessorType) {
    var cid = $(senderForm).find("." + assessorType + "_client_id").val();
    var aDd = $(senderForm).find("." + assessorType + "_assessor_id");
    aDd.find("option").next().remove();
    if (cid > 0)
        apiCall(senderForm, assessorType == "external" ? "getExternalAssessors" : "getInternalAssessors", {"token": getToken(), "client_id": cid}, function (s, data) {
            addOptions(aDd, data.assessors, 'user_id', 'name')
        }, showErrorMsgInMsgBox);
}
//Added by Vikas for facilitator
function loadUserListForWorkshop(senderForm, assessorType) {
    var cid = $(senderForm).find("." + assessorType + "_client_id").val();
    var aDd = $(senderForm).find("." + assessorType + "_assessor_id");
    aDd.find("option").next().remove();
    if (cid > 0)
        apiCall(senderForm, "getUsers", {"token": getToken(), "client_id": cid}, function (s, data) {
            addOptions(aDd, data.assessors, 'user_id', 'name')
        }, showErrorMsgInMsgBox);
}

function loadFacilitatorListForAssessment(senderForm, assessorType) {
    var cid = $(senderForm).find("." + assessorType + "_client_id").val();
    var aDd = $(senderForm).find("." + assessorType + "_id");
    aDd.find("option").next().remove();
    if (cid > 0)
        apiCall(senderForm, "getFacilitators", {"token": getToken(), "client_id": cid}, function (s, data) {
            addOptions(aDd, data.assessors, 'user_id', 'name')
        }, showErrorMsgInMsgBox);
}

function loadFacilitatorListForEditAssessment(senderForm, assessorType, selectedAssesor) {
    var cid = $(senderForm).find("." + assessorType + "_client_id").val();
    var aDd = $(senderForm).find("." + assessorType + "_id");
    aDd.find("option").next().remove();
    if (cid > 0)
        apiCall(senderForm, "getFacilitators", {"token": getToken(), "client_id": cid, "user_id": selectedAssesor}, function (s, data) {
            addOptions(aDd, data.assessors, 'user_id', 'name');
            if (selectedAssesor != undefined)
                for (i = 0; i < data.assessors.length; i++)
                {
                    if (selectedAssesor.indexOf(data.assessors[i].user_id) > -1)
                        aDd.find('option[value=' + data.assessors[i].user_id + ']').attr('selected', 'selected');
                }

        }, showErrorMsgInMsgBox);


}
//Added by Vikas for facilitator

function getExternalAssesorListForAssessment(senderForm) {
    allAssessors = [];
    $('.team_row .external_assessor_id, .team_row .team_external_assessor_id ').each(function () {
        $(this).val() != '' ? allAssessors.push($(this).val()) : '';
    });

}
/*function loadExternalAssesorTeamMembersListForAssessment(senderForm,assessorId,memberId){
 var cid=$(senderForm).find("#"+assessorId).val();	
 var aDd=$(senderForm).find("#"+memberId);	
 aDd.find("option").next().remove();
 if(cid>0)
 apiCall(senderForm,"getExternalAssessors",{"token":getToken(),"client_id":cid},function(s,data){addOptions(aDd,data.assessors,'user_id','name');},showErrorMsgInMsgBox);
 }*/
function ReloadExternalAssesorTeamMembersListForAssessment(senderForm, assessorId, memberId, currObjVal) {
    var cid = $(senderForm).find("#" + assessorId).val();
    var aDd = $(senderForm).find("#" + memberId);
    aDd.find("option").next().remove();
    if (cid > 0)
        apiCall(senderForm, "getExternalAssessors", {"token": getToken(), "client_id": cid}, function (s, data) {
            console.log(data.assessors.length)
            for (var i = 0; i < data.assessors.length; i++)
            {                
                if ($.inArray(data.assessors[i].user_id, allAssessors) >= 0 && data.assessors[i].user_id != currObjVal)
                    data.assessors.splice(i, 1);
            }
            $.when(addOptions(aDd, data.assessors, 'user_id', 'name')).then(function () {
                currObjVal != '' ? aDd.find('option[value=' + currObjVal + ']').attr('selected', 'selected') : '';
            });
            //	console.log(aDd.find('option[value='+currObjVal+']').attr('selected','selected'))
        }, showErrorMsgInMsgBox);
}

//Added by Vikas for workshop add
function ReloadFacilitatorTeamMembersListForWorkshop(senderForm, assessorId, memberId, currObjVal) {
    var cid = $(senderForm).find("#" + assessorId).val();
    var aDd = $(senderForm).find("#" + memberId);
    aDd.find("option").next().remove();
    if (cid > 0)
        apiCall(senderForm, "getFacilitators", {"token": getToken(), "client_id": cid}, function (s, data) {
            console.log(allAssessors)
            for (var i = 0; i < data.assessors.length; i++)
            {                
                if ($.inArray(data.assessors[i].user_id, allAssessors) >= 0 && data.assessors[i].user_id != currObjVal)
                    data.assessors.splice(i, 1);
            }
            $.when(addOptions(aDd, data.assessors, 'user_id', 'name')).then(function () {
                currObjVal != '' ? aDd.find('option[value=' + currObjVal + ']').attr('selected', 'selected') : '';
            });
            //	console.log(aDd.find('option[value='+currObjVal+']').attr('selected','selected'))
        }, showErrorMsgInMsgBox);
}
//Added by deepak for add facilitator in school review
function ReloadFacilitatorTeamMembersList(senderForm, assessorId, memberId, currObjVal) {
    var cid = $(senderForm).find("#" + assessorId).val();
    var aDd = $(senderForm).find("#" + memberId);
    aDd.find("option").next().remove();
    if (cid > 0)        
        apiCall(senderForm, "getFacilitators", {"token": getToken(), "client_id": cid}, function (s, data) {
            //data.assessors.splice(1, 1);
            for (var i = 0; i < data.assessors.length; i++)
            {         
               if ($.inArray(data.assessors[i].user_id, allFacilitators) >= 0 && data.assessors[i].user_id != currObjVal)
                    data.assessors.splice(i, 1);
                
                //if ($.inArray(data.assessors[i].user_id, allFacilitators) >= 0 && data.assessors[i].user_id != currObjVal)
                    //data.assessors.splice(i, 1);
            }
           
            //console.log(data.assessors);
            $.when(addOptions(aDd, data.assessors, 'user_id', 'name')).then(function () {
                currObjVal != '' ? aDd.find('option[value=' + currObjVal + ']').attr('selected', 'selected') : '';
            });
            //	console.log(aDd.find('option[value='+currObjVal+']').attr('selected','selected'))
        }, showErrorMsgInMsgBox);
}

function ReloadUsersTeamMembersListForWorkshop(senderForm, assessorId, memberId, currObjVal) {
    var cid = $(senderForm).find("#" + assessorId).val();
    var aDd = $(senderForm).find("#" + memberId);
    aDd.find("option").next().remove();
    if (cid > 0)
        apiCall(senderForm, "getUsers", {"token": getToken(), "client_id": cid}, function (s, data) {
            console.log(data.assessors.length)
            for (var i = 0; i < data.assessors.length; i++)
            {                
                if ($.inArray(data.assessors[i].user_id, allAssessors) >= 0 && data.assessors[i].user_id != currObjVal)
                    data.assessors.splice(i, 1);
            }
            $.when(addOptions(aDd, data.assessors, 'user_id', 'name')).then(function () {
                currObjVal != '' ? aDd.find('option[value=' + currObjVal + ']').attr('selected', 'selected') : '';
            });
            //	console.log(aDd.find('option[value='+currObjVal+']').attr('selected','selected'))
        }, showErrorMsgInMsgBox);
}
//Added by Vikas for workshop add

/*function loadAssesorListForSchoolAssessment(cid,senderForm,assessorType,removeIds){			
 var aDd=senderForm;
 aDd.find("option").next().remove();
 if(cid>0)
 apiCall(senderForm,assessorType=="external"?"getExternalAssessors":"getInternalAssessors",{"token":getToken(),"client_id":cid},function(s,data){
 
 for(var i=0;i<data.assessors.length;i++)
 {
 
 if($.inArray(data.assessors[i].user_id,removeIds)>=0)				
 data.assessors.splice(i,1);
 }
 
 addOptions(aDd,data.assessors,'user_id','name')
 },showErrorMsgInMsgBox);
 }*/
function loadAssesorListForSchoolAssessment(cid, senderForm, assessorType, removeIds) {
    var aDd = $('#' + senderForm + ' .external_assessor_id').last();
    aDd.find("option").next().remove();
    if (cid > 0)
        apiCall(senderForm, assessorType == "external" ? "getExternalAssessors" : "getInternalAssessors", {"token": getToken(), "client_id": cid}, function (s, data) {
            for (var i = 0; i < data.assessors.length; i++) {
                if ($.inArray(data.assessors[i].user_id, removeIds) >= 0)
                {
                    //console.log()
                    data.assessors.splice(i, 1);
                }
            }
            addOptions(aDd, data.assessors, 'user_id', 'name');
        }, showErrorMsgInMsgBox);
}
function loadAssesorListForEditAssessment(senderForm, assessorType, selectedAssesor,isEditable=0) {
    var cid = $(senderForm).find("." + assessorType + "_client_id").val();
    var aDd = $(senderForm).find("." + assessorType + "_assessor_id");
    aDd.find("option").next().remove();
    if (cid > 0)
        apiCall(senderForm, assessorType == "external" ? "getExternalAssessors" : "getEditInternalAssessors", {"token": getToken(), "client_id": cid, "user_id": selectedAssesor,"isEditable":isEditable}, function (s, data) {
            addOptions(aDd, data.assessors, 'user_id', 'name');
            if (assessorType == 'external')
            {
                allAssessors = data.assessors;
            }
            if (selectedAssesor != undefined)
                for (i = 0; i < data.assessors.length; i++)
                {
                    if (selectedAssesor.indexOf(data.assessors[i].user_id) > -1)
                        aDd.find('option[value=' + data.assessors[i].user_id + ']').attr('selected', 'selected');
                }

        }, showErrorMsgInMsgBox);


}

function ajaxCall(senderForm, controller, action, postData, queryString, onSuccess, onError, onSessionOut, setExtraVariables) {
    serverCall(senderForm, controller, action, 'ajaxRequest=1&' + queryString, postData, onSuccess, onError, onSessionOut, setExtraVariables);
}

function apiCall(senderForm, action, postData, onSuccess, onError, onSessionOut, setExtraVariables,async) {
    serverCall(senderForm, 'api', action, '', postData, onSuccess, onError, onSessionOut, setExtraVariables,async);
}

function serverCall(senderForm, controller, action, queryString, postData, onSuccess, onError, onSessionOut, setExtraVariables,async) {
    if (typeof(async)==='undefined')        
        async = true;
    $(senderForm).find(".ajaxMsg").removeClass("active");
    var ajaxParamObj = {
        url: "?controller=" + controller + "&action=" + action + "&" + queryString,
        method: "post",
        data: postData,
        dataType: 'json',
        async: async,
//                cache: false,
//                contentType: false,
//                processData: false,
        beforeSend: function () {
            if(action!="actionplantip"){
            $("#ajaxLoading").show();
        }
        },
        complete: function () {
            if(action!="actionplantip"){
            $("#ajaxLoading").hide();
        }
        },
        success: function (rData) {
            //alert(rData.message);
            if (rData != undefined && rData != null && rData.status != undefined) {
                if (rData.status == -1) {
                    onSessionOut != undefined ? onSessionOut(senderForm, rData) : '';
                    sessionExpired();
                } else if (rData.status == 1) {
                    onSuccess != undefined ? onSuccess(senderForm, rData) : '';
                } else {
                    onError != undefined ? onError(senderForm, rData.message, rData) : '';
                }
            } else {
                onError != undefined ? onError(senderForm, 'Unknown response from server') : '';
            }
''        },
        error: function (a, status) {
            var msg = "Error while connecting to server";
            if (status == "timeout")
                msg = 'Request time out';
            else if (status == "parsererror")
                msg = 'Unknown response from server';
            onError != undefined ? onError(senderForm, msg) : '';
        }
    };

    if (setExtraVariables != undefined && setExtraVariables != null && typeof setExtraVariables == "object") {
        for (var objKey in setExtraVariables) {
            ajaxParamObj[objKey] = setExtraVariables[objKey];
        }
    }

    $.ajax(ajaxParamObj);

}
function webApiCall(senderForm, action, postData, queryString, onSuccess, onError) {
    $(senderForm).find(".ajaxMsg").removeClass("active");
    $.ajax({
        url: "?controller=web" + "&action=" + action + "&" + queryString,
        method: "post",
        data: postData,
        dataType: 'json',
        beforeSend: function () {
            $("#ajaxLoading").show();
        },
        complete: function () {
            $("#ajaxLoading").hide();
        },
        success: function (rData) {
            if (rData != undefined && rData != null && rData.status != undefined) {
                if (rData.status == 1) {
                    onSuccess != undefined ? onSuccess(senderForm, rData) : '';
                } else {
                    onError != undefined ? onError(senderForm, rData.message, rData) : '';
                }
            } else {
                onError != undefined ? onError(senderForm, 'Unknown response from server') : '';
            }
        },
        error: function (a, status) {
            var msg = "Error while connecting to server";
            if (status == "parsererror")
                msg = 'Unknown response from server';
            onError != undefined ? onError(senderForm, msg) : '';
        }
    });
}

function showSuccessMsgInMsgBox(s, data) {
    $("#createresource").show();
    $(s).find(".ajaxMsg").show();
    //alert(data.message);
    $(s).find(".ajaxMsg").removeClass("danger warning info").html(data.message).addClass("success active");
    $("#createresource").delay(2000).fadeOut();
}

function showErrorMsgInMsgBox(s, msg) {
    $("#createresource").show();
    $(s).find(".ajaxMsg").removeClass("success warning info").html(msg).addClass("danger active");
    $("#createresource").delay(2000).fadeOut();
}


function showErrorMsgInMsgBoxSelfReview(s, msg) {
    //alert("ok"+msg);
    $(".internal_client_id").val("");
    $("#createresource").show();
    $(s).find(".ajaxMsg").removeClass("success warning info").html(msg).addClass("danger active");
    $("#createresource").delay(2000).fadeOut();
    alert("Info: "+msg);
}

function showAssErrorMsgInMsgBox(s, msg, userId) {
    var msgId = '';
    if(userId) {
        msgId = "#createresource_"+userId;
    }else {
        msgId = "#createresource";
    }
    $(s).find(msgId).show();
    $(s).find(msgId).removeClass("success warning info").html(msg).addClass("danger active");
    $(s).find(msgId).delay(2000).fadeOut();
}

function showAssSuccessMsgInMsgBox(s, data, userId) {
    var msgId = '';
    if(userId) {
        msgId = "#createresource_"+userId;
    }else {
        msgId = "#createresource";
    }
    $(s).find(msgId).show();
    $(s).find(msgId).removeClass("danger warning info").html(data.message).addClass("success active");
   $(s).find(msgId).delay(2000).fadeOut();
}


function sessionExpired() {
    $("#login_popup .ajaxMsg").html('Session Expired. Please login again.');
    $("#login_popup").find("input[type=email],input[type=password]").val('');
    $("#login_popup_wrap").addClass("active");
}
function getToken() {
    return getCookie("ADH_TOKEN");
}
function setToken(token) {
    setCookie("ADH_TOKEN", token);
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);
        if (c.indexOf(name) == 0)
            return c.substring(name.length, c.length);
    }
    return "";
}

function setCookie(cname, value) {
    document.cookie = cname + "=" + value;
}

function addOptions(to, list, key, value) {
    for (var i = 0; i < list.length; i++)
        to.append('<option value="' + list[i][key] + '">' + list[i][value] + '</option>');
}

function addOptionsDisabled(to, list, key, value,list1) {
    for (var i = 0; i < list.length; i++){
     if($.inArray( list[i][key], list1 ) > -1){   
        to.append('<option value="' + list[i][key] + '" >' + list[i][value] + '</option>');
    }else{
       to.append('<option value="' + list[i][key] + '" disabled="disabled">' + list[i][value] + '</option>'); 
    }
    }
}

function addOptions_Optgroup(to, list, key, value,type_opt) {
    var optgroup = $('<optgroup>');
    optgroup.attr('label',type_opt);
    for (var i = 0; i < list.length; i++)
        optgroup.append('<option value="' + list[i][key] + '">' + list[i][value] + '</option>');
    
    to.append(optgroup);
}

function validate(form) {
    var valid = true
//	$(form).find(".")
}

function deSerialize(queryString) {
    var iH = queryString.indexOf("#");
    var Arr = queryString.substring(queryString.indexOf("?") + 1, iH == -1 ? queryString.length : iH).split("&");
    var Obj = {};
    for (var i = 0; i < Arr.length; i++) {
        var t = Arr[i].split("=");
        Obj[t[0]] = t.length > 1 ? t[1] : "";
    }
    return Obj;
}

+function ($) {
    jQuery.createModal = function (id, title, content, size, top, modalclass) {
        var html = '<div id="popup-' + id + '" class="modal fade" data-backdrop="static" data-keyboard="false"><div class="modal-dialog ' + (modalclass != undefined ? modalclass : '') + '" style="' + (top != undefined && top > 0 ? 'margin-top:' + top + 'px;' : '') + (size != undefined && size > 0 ? 'width:' + size + 'px;' : '') + '"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">' + title + '</h4></div><div class="modal-body">' + content + '</div><!--<div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div>--> </div></div></div>';
        $("#popup_wrapper #popup-" + id).remove();
        $("#popup_wrapper").append(html);
        $("#popup_wrapper #popup-" + id).modal("show");
    }
}(jQuery);

function isValidEmail(email) {
    var pattern = new RegExp(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
    //var pattern = new RegExp(/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^/);
    return pattern.test(email);
}

function isValidUrl(u) {
    u = u.split(".");
    return u.length > 1 && u[0].length > 1 && u[1].length > 1 ? true : false;
}
function addError(c, m, k) {
    var hasKey = k == undefined || k == null || k == "" || $("#aqsf_" + k).length == 0 ? false : true;
    $(c).append('<div class="alert alert-danger mt25 alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span class="' + (hasKey ? 'hasKey' : '') + '" data-id="' + (hasKey ? k : '') + '">' + m + '</span></div>');
}
function isDiagnosticName() {
    var ctr = $(this).parents('.sortable-form').data('for') != undefined ? "#" + $(this).parents(".sortable-form").data('for') + " " : "";
    var diagnosticName = $(ctr + "#diagnostic_name").val();
    if (diagnosticName.trim() == '') {
        alert("Please fill diagnostic title!");
        return false;
    }
    return true;

}
function isValidText(t) {

    if (t == null || t == "" || t.trim() == '')
        return false;

    return true;

}
// function for enable and disable the role checkbox on 12-05-2016 by Mohit
function checkboxEnableDisable(id, role_id, class_name, login_user_role) {
    if (role_id == 8) {
        if ($('#' + id + role_id).is(':checked')) {
            $('input.' + class_name).prop('disabled', true);
            $('#' + id + role_id).removeAttr('disabled');
        } else {
            $('input.' + class_name).prop('disabled', false);
        }
    } else {
        if ($('#' + id + role_id).is(':checked')) {
            $('#' + id + '8').prop('disabled', true);
        } else {
            if ($('input.' + class_name).is(':checked')) {
                $('#' + id + '8').prop('disabled', true);
            } else {
                if (login_user_role == 1) {
                    $('#' + id + '8').prop('disabled', false);
                } else {
                    $('#' + id + '8').prop('disabled', true);
                }
            }
        }
    }
}

//function for check & uncheck checkbox
function checkall() {
    if (document.frm.tickall.checked) {
        for (i = 0; i < document.frm.elements.length; i++)
        {
            if (document.frm.elements[i].type == "checkbox" && document.frm.elements[i].id == "delid")
            {
                document.frm.elements[i].checked = true
            }
        }
    } else {
        for (i = 0; i < document.frm.elements.length; i++)
        {
            for (i = 0; i < document.frm.elements.length; i++)
            {
                if (document.frm.elements[i].type == "checkbox" && document.frm.elements[i].id == "delid")
                {
                    document.frm.elements[i].checked = false
                }
            }
        }
    }
}



// function for getting alert count
function alertCounts() {
    $.ajax({
        type: 'post',
        url: "?controller=api&action=getAlertCount",
        data: {'token': getToken(), 'assessor_value': $('#assessor_value').val(), 'review_value': $('#review_value').val()},
        async: true,
        cache: false,
        timeout: 50000,
        success: function (response) {
            var a = $.parseJSON(response);
            addMessage("new", a);
            setTimeout(alertCounts, 1000);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            setTimeout(alertCounts, 15000);
        }
    });
}
// function for updating the updated alert count on 08-08-2016 by mohit kumar
function addMessage(type, a) {
    if (a.status == 1) {
        $('b#totalAlertCount').text(a.totalCount);
        var assessorRef = Number(a.assessorCount) > 0 ? 1 : 0;
        var reviewRef = Number(a.reviewCount) > 0 ? 1 : 0;
        $('a#assessor_count').text("New Assessor - " + a.assessorCount).attr('href', '?controller=user&action=user&ref=' + assessorRef);
        $('a#review_count').text("New Review - " + a.reviewCount).attr('href', '?controller=assessment&action=assessment&ref=' + reviewRef);
    }
}



// Callback that creates and populates a data table, 
// instantiates the pie chart, passes in the data and
// draws it on 17-06-2016 by Mohit Kumar

function drawChart() {
    // Create the data table.

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Role');
    data.addColumn('number', 'Review Count');
    var jsonPieChartData = $.ajax({
        type: 'post',
        url: "?controller=api&action=getAllAssessorsReviewCount",
        data: {'token': getToken()},
        async: false,
        cache: false
    }).responseText;
    var a = $.parseJSON(jsonPieChartData);

    if (a.count.Lead_Assessor == 0 && a.count.Apprentice == 0 && a.count.Associate == 0 && a.count.Intern == 0) {
        $('div#chart_div').html('There is no review count for any users');
    } else {
        var lead = Number(a.count.Lead_Assessor) != '' ? Number(a.count.Lead_Assessor) : 0;
        var apprentice = Number(a.count.Apprentice) != '' ? Number(a.count.Apprentice) : 0;
        var associate = Number(a.count.Associate) != '' ? Number(a.count.Associate) : 0;
        var intern = Number(a.count.Intern) != '' ? Number(a.count.Intern) : 0;

        data.addRows([
            ['Lead Assessor', lead],
            ['Apprentice', apprentice],
            ['Associate', associate],
            ['Intern', intern]
        ]);

        // Set chart options
        var options = {'title': 'External Assessor Review Counts',
            'width': 600,
            'height': 350,
            'backgroundColor': '#a4a4a4',
            'is3D': true
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }

}

function drawPie(pieName, dataSet, selectString, colors, margin, outerRadius, innerRadius, sortArcs) {

    // pieName => A unique drawing identifier that has no spaces, no "." and no "#" characters.
    // dataSet => Input Data for the chart, itself.
    // selectString => String that allows you to pass in
    //           a D3 select string.
    // colors => String to set color scale.  Values can be...
    //           => "colorScale10"
    //           => "colorScale20"
    //           => "colorScale20b"
    //           => "colorScale20c"
    // margin => Integer margin offset value.
    // outerRadius => Integer outer radius value.
    // innerRadius => Integer inner radius value.
    // sortArcs => Controls sorting of Arcs by value.
    //              0 = No Sort.  Maintain original order.
    //              1 = Sort by arc value size.

    // Color Scale Handling...


    var colorScale = d3.scale.category20c();
    switch (colors)
    {
        case "colorScale10":
            colorScale = d3.scale.category10();
            break;
        case "colorScale20":
            colorScale = d3.scale.category20();
            break;
        case "colorScale20b":
            colorScale = d3.scale.category20b();
            break;
        case "colorScale20c":
            colorScale = d3.scale.category20c();
            break;
        default:
            colorScale = d3.scale.category20c();
    }


    var canvasWidth = 700;
    var pieWidthTotal = outerRadius * 2;

    var pieCenterX = outerRadius + margin / 2;
    var pieCenterY = outerRadius + margin / 2;
    var legendBulletOffset = 30;
    var legendVerticalOffset = outerRadius - margin;
    var legendTextOffset = 20;
    var textVerticalSpace = 20;

    var canvasHeight = 0;
    var pieDrivenHeight = outerRadius * 2 + margin * 2;
    var legendTextDrivenHeight = (dataSet.length * textVerticalSpace) + margin * 2;
    // Autoadjust Canvas Height
    if (pieDrivenHeight >= legendTextDrivenHeight)
    {
        canvasHeight = pieDrivenHeight;
    } else
    {
        canvasHeight = legendTextDrivenHeight;
    }

    var x = d3.scale.linear().domain([0, d3.max(dataSet, function (d) {
            return d.magnitude;
        })]).rangeRound([0, pieWidthTotal]);
    var y = d3.scale.linear().domain([0, dataSet.length]).range([0, (dataSet.length * 20)]);


    var synchronizedMouseOver = function () {
        var arc = d3.select(this);
        var indexValue = arc.attr("index_value");

        var arcSelector = "." + "pie-" + pieName + "-arc-" + indexValue;
        var selectedArc = d3.selectAll(arcSelector);
        selectedArc.style("fill", "Maroon");

        var bulletSelector = "." + "pie-" + pieName + "-legendBullet-" + indexValue;
        var selectedLegendBullet = d3.selectAll(bulletSelector);
        selectedLegendBullet.style("fill", "Maroon");

        var textSelector = "." + "pie-" + pieName + "-legendText-" + indexValue;
        var selectedLegendText = d3.selectAll(textSelector);
        selectedLegendText.style("fill", "Maroon");
    };

    var synchronizedMouseOut = function () {
        var arc = d3.select(this);
        var indexValue = arc.attr("index_value");

        var arcSelector = "." + "pie-" + pieName + "-arc-" + indexValue;
        var selectedArc = d3.selectAll(arcSelector);
        var colorValue = selectedArc.attr("color_value");
        selectedArc.style("fill", colorValue);

        var bulletSelector = "." + "pie-" + pieName + "-legendBullet-" + indexValue;
        var selectedLegendBullet = d3.selectAll(bulletSelector);
        var colorValue = selectedLegendBullet.attr("color_value");
        selectedLegendBullet.style("fill", colorValue);

        var textSelector = "." + "pie-" + pieName + "-legendText-" + indexValue;
        var selectedLegendText = d3.selectAll(textSelector);
        selectedLegendText.style("fill", "Blue");
    };

    var tweenPie = function (b) {
        b.innerRadius = 0;
        var i = d3.interpolate({startAngle: 0, endAngle: 0}, b);
        return function (t) {
            return arc(i(t));
        };
    }

    // Create a drawing canvas...
    var canvas = d3.select(selectString)
            .append("svg:svg") //create the SVG element inside the <body>
            .data([dataSet]) //associate our data with the document
            .attr("width", canvasWidth) //set the width of the canvas
            .attr("height", canvasHeight) //set the height of the canvas
            .append("svg:g") //make a group to hold our pie chart
            .attr("transform", "translate(" + pieCenterX + "," + pieCenterY + ")") // Set center of pie

    // Define an arc generator. This will create <path> elements for using arc data.
    var arc = d3.svg.arc()
            .innerRadius(innerRadius) // Causes center of pie to be hollow
            .outerRadius(outerRadius);

    // Define a pie layout: the pie angle encodes the value of dataSet.
    // Since our data is in the form of a post-parsed CSV string, the
    // values are Strings which we coerce to Numbers.
    var pie = d3.layout.pie()
            .value(function (d) {
                return d.magnitude;
            })
            .sort(function (a, b) {
                if (sortArcs == 1) {
                    return b.magnitude - a.magnitude;
                } else {
                    return null;
                }
            });

    // Select all <g> elements with class slice (there aren't any yet)
    var arcs = canvas.selectAll("g.slice")
            // Associate the generated pie data (an array of arcs, each having startAngle,
            // endAngle and value properties) 
            .data(pie)
            // This will create <g> elements for every "extra" data element that should be associated
            // with a selection. The result is creating a <g> for every object in the data array
            // Create a group to hold each slice (we will have a <path> and a <text>      // element associated with each slice)
            .enter().append("svg:a")
            .attr("xlink:href", function (d) {
                return d.data.link;
            })
            .append("svg:g")
            .attr("class", "slice")    //allow us to style things in the slices (like text)
            // Set the color for each slice to be chosen from the color function defined above
            // This creates the actual SVG path using the associated data (pie) with the arc drawing function
            .style("stroke", "White")
            .attr("d", arc);

    arcs.append("svg:path")
            // Set the color for each slice to be chosen from the color function defined above
            // This creates the actual SVG path using the associated data (pie) with the arc drawing function
            .attr("fill", function (d, i) {
                return colorScale(i);
            })
            .attr("color_value", function (d, i) {
                return colorScale(i);
            }) // Bar fill color...
            .attr("index_value", function (d, i) {
                return "index-" + i;
            })
            .attr("class", function (d, i) {
                return "pie-" + pieName + "-arc-index-" + i;
            })
            .style("stroke", "White")
            .attr("d", arc)
            .on('mouseover', synchronizedMouseOver)
            .on("mouseout", synchronizedMouseOut)
            .transition()
            .ease("bounce")
            .duration(2000)
            .delay(function (d, i) {
                return i * 50;
            })
            .attrTween("d", tweenPie);

    // Add a magnitude value to the larger arcs, translated to the arc centroid and rotated.
    arcs.filter(function (d) {
        return d.endAngle - d.startAngle > .2;
    }).append("svg:text")
            .attr("dy", ".35em")
            .attr("text-anchor", "middle")
            //.attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")rotate(" + angle(d) + ")"; })
            .attr("transform", function (d) { //set the label's origin to the center of the arc
                //we have to make sure to set these before calling arc.centroid
                d.outerRadius = outerRadius; // Set Outer Coordinate
                d.innerRadius = innerRadius; // Set Inner Coordinate
                return "translate(" + arc.centroid(d) + ")rotate(" + angle(d) + ")";
            })
            .style("fill", "White")
            .style("font", "normal 12px Arial")
            .text(function (d) {
                return d.data.magnitude;
            });

    // Computes the angle of an arc, converting from radians to degrees.
    function angle(d) {
        var a = (d.startAngle + d.endAngle) * 90 / Math.PI - 90;
        return a > 90 ? a - 180 : a;
    }

    // Plot the bullet circles...
    canvas.selectAll("circle")
            .data(dataSet).enter().append("svg:circle") // Append circle elements
            .attr("cx", pieWidthTotal + legendBulletOffset)
            .attr("cy", function (d, i) {
                return i * textVerticalSpace - legendVerticalOffset;
            })
            .attr("stroke-width", ".5")
            .style("fill", function (d, i) {
                return colorScale(i);
            }) // Bullet fill color
            .attr("r", 5)
            .attr("color_value", function (d, i) {
                return colorScale(i);
            }) // Bar fill color...
            .attr("index_value", function (d, i) {
                return "index-" + i;
            })
            .attr("class", function (d, i) {
                return "pie-" + pieName + "-legendBullet-index-" + i;
            })
            .on('mouseover', synchronizedMouseOver)
            .on("mouseout", synchronizedMouseOut);

    // Create hyper linked text at right that acts as label key...
    canvas.selectAll("a.legend_link")
            .data(dataSet) // Instruct to bind dataSet to text elements
            .enter().append("svg:a") // Append legend elements
            .attr("xlink:href", function (d) {
                return d.link;
            })
            .append("text")
            .attr("text-anchor", "center")
            .attr("x", pieWidthTotal + legendBulletOffset + legendTextOffset)
            //.attr("y", function(d, i) { return legendOffset + i*20 - 10; })
            //.attr("cy", function(d, i) {    return i*textVerticalSpace - legendVerticalOffset; } )
            .attr("y", function (d, i) {
                return i * textVerticalSpace - legendVerticalOffset;
            })
            .attr("dx", 0)
            .attr("dy", "5px") // Controls padding to place text in alignment with bullets
            .text(function (d) {
                return d.legendLabel;
            })
            .attr("color_value", function (d, i) {
                return colorScale(i);
            }) // Bar fill color...
            .attr("index_value", function (d, i) {
                return "index-" + i;
            })
            .attr("class", function (d, i) {
                return "pie-" + pieName + "-legendText-index-" + i;
            })
            .style("fill", "Blue")
            .style("font", "normal 1.5em Arial")
            .on('mouseover', synchronizedMouseOver)
            .on("mouseout", synchronizedMouseOut);

}

function resourceStatusChange(resource_id, resource_file_id, val) {
    var contnr = $(this).parents('form').first();
    var postdata = {'resource_id': resource_id, 'resource_file_id': resource_file_id, 'rstatus': val, 'token': getToken()};
    apiCall(this, "resourceStatusChange", postdata,
            function (s, data) {
                $('.ajaxMsg').show();
                $('.ajaxMsg').html(data.message).addClass("success active");
               // showSuccessMsgInMsgBox(s, data);
                if ($(".resource-list").length) {
                    filterByAjax($(".resource-list"));
                }
            }, showErrorMsgInMsgBox);
    return false;
}

// function to get schools 
function getSchools(network_id,senderFrom) {
   var contnr = $(this).parents('form').first();  
    	
    	var postData = "network_id="+network_id+"&token=" + getToken();
        apiCall(senderFrom, "getSchoolsInNetworks", postData, function (s, data) {
            if (data.message != '') {
                $('#rec_schools').show();
                $('#rec_users').hide();
                $('#errors').hide();
                var aDd = $("#rec_school");
                aDd.find("option").next().remove();
                addOptions(aDd, data.message, 'client_id', 'client_name');
                $(contnr).find("#rec_schools").show();
            }else{
                $('#rec_schools').hide(); 
                $('#errors').html('No record exist for this network');
                $('#errors').show();
            }
        }, showErrorMsgInMsgBox);
}

// function to get all schools by option type 
function getAllSchools(option_type, senderFrom) {
    var contnr = $(this).parents('form').first();    
    if (option_type != '1') {
        var postData = "school_related_to=" + option_type + "&token=" + getToken();
        apiCall(senderFrom, "getAllSchools", postData, function (s, data) {
            if (data.message != '') {
                $('#rec_schools').show();
                $('#errors').hide();
                $('#networks').hide();
                $('#provinces').hide();
                $("#rec_users").hide();
                // var aDd = $(contnr).find("#rec_schools .province-list-dropdown");
                var aDd = $("#rec_school");
                aDd.find("option").next().remove();
                addOptions(aDd, data.message, 'client_id', 'client_name');
                $(contnr).find("#rec_schools").show();
            } else {
                $('#rec_schools').hide();
                $('#errors').html('No record exist for this network');
                $('#errors').show();
            }
        }, showErrorMsgInMsgBox);
    } else {
        $('#rec_schools').hide();
        $("#rec_users").hide();
        $('#errors').hide();
        $('#provinces').hide();
        $('#networks').show();
    }
}

/*function getSchoolAllUsers() {
    var contnr = $(this).parents('form').first();
        var school_ids = $('#rec_school').val();
        // var user_role_ids;
        var user_role_ids = $("input[name='roles[]']:checked").map(function () {
            return $(this).val();
        }).get();
        if (school_ids != '' && school_ids !== null && school_ids !== undefined) {

            // getAllSchools(option_type,this);
            // getSchoolAllUsers(school_ids,user_role_ids,contnr);
            var postData = "school_ids=" + school_ids + "&user_role_ids=" + user_role_ids + "&token=" + getToken();
            apiCall(this, "getSchoolAllUsers", postData, function (s, data) {
                if (data.message != '' && data.message !== null) {
                    $('#errors').hide();
                    //var aDd = $(contnr).find("#rec_users .province-list-dropdown");
                    var aDd = $("#rec_users_select");
                    aDd.find("option").next().remove();
                    addOptions(aDd, data.message, 'user_id', 'name');
                    aDd.append('<option value="all">ALL</option>');
                    //$(contnr).find("#rec_users").show();
                    $("#rec_users").show();
                } else {
                    $("#createresource").show();
                    $("#createresource").html("Users not exist").addClass("danger active");
//                   / $(s).find(".ajaxMsg").removeClass("success warning info").html("Users not exist").addClass("danger active");
                    $("#createresource").delay(2000).fadeOut();
                    $("#rec_users").hide();
                }
            }, showErrorMsgInMsgBox);
        } else {
           // $('#errors').html('Please select a network');
            //$('#errors').show();
        }
}*/

 $(document).on('change','#create_resource_form #resource_file ,#edit_resource_form #resource_file,#edit_workshop_form #attende_file,#upload_aqs_form #user_excel_file,#upload_student_form  #user_excel_file',function() {
        var original_file=$(this).val();
        var filename = basename(original_file);
        //alert(filename);
        $("#file_attached").html("<span>"+filename+"</span>");
    });
    
function basename(path) {
   return path.split(/[\\/]/).pop();
}
function getOtherText() {
    if($('#school_rating_txt').is(':visible')) {
        $("#school_rating_txt").hide();
    }else {
        $("#school_rating_txt").show();
    }
   // alert($('#school_rating_txt').is(':visible'));
   //alert("ok");
   
}

/**
 * Set the list opened or closed
 * */
function setStatus(node){
    var elements = [];
    $(node).each(function(){
        elements.push($(node).nextAll());
    });
    for (var i = 0; i < elements.length; i++) {
        if (elements[i].css('display') == 'none'){
            elements[i].fadeIn(0);
        }else{
            elements[i].fadeOut(0);
        }
    }
    if (elements[0].css('display') != 'none') {
        $(node).addClass('active');
    }else{
        $(node).removeClass('active');
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

function callFolder( resource_id, resource_file_id) {
    
    if (typeof(resource_id)==='undefined')        
        resource_id = true;
    if (typeof(resource_file_id)==='undefined')        
        resource_file_id = true;
    var postData = "resource_id="+resource_id+"&resource_file_id="+resource_file_id+"&token:"+ getToken()
    var querystring = '';
   // alert('aaaa'+postData);
     ajaxCall('#create_resource_form', 'resource', 'createFolderTree', postData, querystring, function (s, data) {
         //alert(data.content);
         //jsonObj = $.parseJSON(data);
         $( ".resources" ).html(data.content);
         
         //$("head").append("<link  href='public/css/jquery.mCustomScrollbar.min.css' type='text/css' rel='stylesheet' />");
         //$("head").append("<script  href='public/js/jquery.mCustomScrollbar.concat.min.js' type='text/javascript' />");
         $(".vertScrollArea").mCustomScrollbar({theme: "dark"});
    }, showErrorMsgInMsgBox);
}

function isNumberKey1(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode;
         //alert(charCode);
         if (charCode >=48 && charCode <=57)
		 {
            return true;
			}
			else if(charCode==46 || charCode==8)
			{
			return true;
			}
			
			else
			{

         return false;
		 }
    }
function callFolderTree( directory_id) {
    
    if (typeof(directory_id)==='undefined')        
        directory_id = true;
   
    var postData = "directory_id="+directory_id+"&token:"+ getToken()
    var querystring = '';
   // alert('aaaa'+postData);
     ajaxCall('#create_resource_form', 'resource', 'createFolderTree', postData, querystring, function (s, data) {
         //alert(data.content);
         //jsonObj = $.parseJSON(data);
         $( ".resources" ).html(data.content);
         
         //$("head").append("<link  href='public/css/jquery.mCustomScrollbar.min.css' type='text/css' rel='stylesheet' />");
         //$("head").append("<script  href='public/js/jquery.mCustomScrollbar.concat.min.js' type='text/javascript' />");
         $(".vertScrollArea").mCustomScrollbar({theme: "dark"});
    }, showErrorMsgInMsgBox);
}

function updateUser(postData,s,updateUserProfileData) {
    
      
            var isPrincipal = 0;
            var deleteUser = 0;
            var users_id;
           // postData = $(this).serialize();
            //var pData = $(this).serialize() + "&token=" + getToken();
            //var s = $("#userProfileForm");
            // pass user id for gettig the user data on 16-05-2016 by Mohit Kumar
            var id = $('input[name="id"]').val();
            apiCall(s , "checkUserRole", postData , //for principal user
                    function (s, data) {
                        //showSuccessMsgInMsgBox(s,data);
                        //alert()

                        isPrincipal = data.duplicate ? 1 : 0;
                        if (data.duplicate && confirm(data.message + "Do you really want to add another user with this role?"))
                        {
                            users_id = data.duplicate;
                            deleteUser = 1;
                            apiCall(s, "updateUser", postData  + "&role_id=6" + "&users_id=" + users_id,
                                    function (s, data) {
                                        
                                        updateUserProfileData(data);
                                        /*client_id = data.client_id;
                                        showSuccessMsgInMsgBox(s, data);
                                        if ($(".user-list").length) {
                                            filterByAjax($(".user-list"));
                                        }*/
                                    }, showErrorMsgInMsgBox);

                        } else if (!data.duplicate)
                        {

                            apiCall(s, "updateUser", postData ,
                                    function (s, data) {
                                        
                                        updateUserProfileData(data);
                                        //client_id = data.client_id;
                                       /* showSuccessMsgInMsgBox(s, data);
                                        if ($(".user-list").length) {
                                            filterByAjax($(".user-list"));
                                        }*/
                                    }, showErrorMsgInMsgBox);
                        }
                    }, showErrorMsgInMsgBox);
                   // alert(client_id);
        return client_id;
    
}

function getStep2(assessment_id,isEditable = 0) {
    
       var postData = "assessment_id="+assessment_id+"&editStatus="+isEditable+"&token:"+ getToken()
       var querystring = '';
   // alert('aaaa'+postData);
     ajaxCall('#create-review-kpa', 'assessment', 'schoolAssessmentData', postData, querystring, function (s, data) {
         //alert(data.content);
         $("#ctreateSchoolAssessment-step2").show();
         $("#ctreateSchoolAssessment-step1").hide();
         $("#kpa-step2").html(data.content);
         $(".team_kpa_id").multiselect('destroy');
         $('.team_kpa_id').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            numberDisplayed: 1,
            buttonWidth: '420px',
            maxHeight: 300,
           
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                 ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               }
           });
          // $('.team_kpa_id').click(function(v){ 
               
            toggleKpas();
                
             
                //alert("ok"+$(this).val());
                //});
           $(document).on("change","#create-review-kpa .team_kpa_id,#edit-review-kpa .team_kpa_id ",function () { 
               
                var selectedKpaId = $(this).attr("id");
                var id='';
                selectedKpaValues = [];
                allKpaValues = [];
                allSelectedKpaValues = [];
                var strVal = $(this).val();
                if(strVal!='' && strVal!=undefined){
                    strVal = strVal.toString();
                    selectedKpaValues = strVal.split(",");
                }
                //$(".team_kpa_id option").removeAttr('disabled');
                $(".team_kpa_id").each(function () { 
                    var id = $(this).attr("id");
                    var allKpaValue = $("#"+id).val();
                    //alert(allKpaValue);
                    if(allKpaValue!='' && allKpaValue!=undefined){
                        allKpaValue = allKpaValue.toString();
                        allKpaValues = $.merge(allKpaValues,allKpaValue.split(","));
                     }
                    
                                           
                });
                $(".team_kpa_id").each(function () { 
                    var id = $(this).attr("id");
               
                    if($(this).attr("id") != selectedKpaId) {
                           //alert("ok");
                         $("#"+id+" option").removeAttr('disabled');
                       //$.each(selectedKpaValues, function( index, value ) {
                       $.each(allKpaValues, function( index, value ) {
                                    //alert( index + ": " + value );
                                   if($("#"+id+" option[value='"+value+"']").is(":checked") == false){
                                    //if( $("#"+id+" option[value='"+value+"']").prop("checked") == false){
                                         $("#"+id+" option[value='"+value+"']").attr("disabled","disabled");
                                     }
                                     //}
                        });
                        
                    }
                                           
                });
                
             
               
                //alert("ok"+$(this).val());
                });             
                
              
                }, showErrorMsgInMsgBox);
}


function updateAssessorsKpas(postData) {
    
     apiCall('#edit-review-kpa', "editSchoolAssessmentKpa", postData, function (s, data) {
             $(".ajaxMsg").show();
            showSuccessMsgInMsgBox(s, data);
            $(".ajaxMsg").delay(2000).fadeOut();
            
        }, function(s,data) { $(".ajaxMsg").show();showErrorMsgInMsgBox(s,data); $(".ajaxMsg").delay(2000).fadeOut(); } );
        
}


(function($){
  $(window).on('load',function(){
      $('.nuiHdrBtmBar .container ul.mainNav ul li').each(function(){
         $(this).find('i.fa').on('click', function(){
             $(this).parent('li').find('ul').toggle();
         });
     });
  });
})(jQuery);