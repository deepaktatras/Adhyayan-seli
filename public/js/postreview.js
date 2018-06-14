jQuery(document)
        .ready(
                function ($) {
                        $('.mulData').multiselect({  
                                numberDisplayed : 1
                          });
//                    tinymce.init({selector: 'textarea#id_comments', width: "100",
//                        height: "200",
//                        theme_advanced_resizing: true,
//                        theme_advanced_resizing_use_cookie: false,
//                        plugins: "textcolor", toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor"});

                    checkPostReviewCompletion();
                    $(document).on("revDataChanged", "body", function () {
                        $("#savePostReview").removeAttr("disabled");
                        checkPostReviewCompletion();
                    });
                    $(document)
                            .on(
                                    "change",
                                    "#post_review_form select,#post_review_form input[type=number],#post_review_form input[type=text],#post_review_form input[type=checkbox],#post_review_form input[type=radio],#post_review_form textarea",
                                    function () {
                                        $("body").trigger('revDataChanged');
                                    });
                    $(document).on("click", "#savePostReview", function () {
                        var f = $("#post_review_form");
                       // var comments = tinymce.get('id_comments').getContent();
                        // var comments = $(this).find('#id_comments').val();
                        var param = f.serialize() + "&token=" + getToken();
                        // $("#validationErrors").html('');
                        $(this).attr("disabled", "disabled");
                        apiCall(f, "savePostReview", param, function (s, data) {
                            alert(data.message);
                        }, function (s, msg, data) {
                            $("body").trigger('revDataChanged');
                            /*
                             * if(data!=undefined && data!=null &&
                             * data.errors!=undefined){ for(var prop in
                             * data.errors)
                             * addError("#validationErrors",data.errors[prop],prop); }
                             * alert(msg);
                             */
                            alert(msg);
                        }, function (s, d) {
                            $("body").trigger('revDataChanged');
                        });
                    });
                    $(document).on("submit", "#post_review_form", function () {

                        var f = $("#post_review_form");
                       // var comments = tinymce.get('id_comments').getContent();
                       // var comments = $(this).find('#id_comments').val();
                        var param = f.serialize() + "&submit=1&token=" + getToken() ;
                        ;
                        // $("#validationErrors").html('');
                        $(this).attr("disabled", "disabled");

                        apiCall(f, "savePostReview", param, function (s, data) {
                            alert("Successfully submitted");
                            window.location.reload();
                        }, function (s, msg, data) {
                            $("body").trigger('revDataChanged');
                            alert(msg);
                            /*
                             * if(data!=undefined && data!=null &&
                             * data.errors!=undefined){ for(var prop in
                             * data.errors)
                             * addError("#validationErrors",data.errors[prop],prop); }
                             * alert(msg);
                             */
                        }, function (s, d) {
                            alert("Error")
                        });
                        return false;
                    });
                    $(document).on("change", "[id^='postrev_medium_instruction']",
                            function () {
                                if ($(this).val() != 1)
                                {
                                    $("#langRow").show();
                                    $("select[name='instruction_lang']").prop('required', true);
                                } else
                                {
                                    $("#langRow").hide();
                                    $("select[name='instruction_lang']").prop('required', false);
                                }
                            });
//                    $(document).on("change", "#postrev_sbody_select",
//                            function () {
//                                if ($(this).val() == 2)
//                                    $("#postrev_sbody_levels").show();
//                                else
//                                    $("#postrev_sbody_levels").hide();
//                            });
                    $(document).on("change", "#postrev_decision_maker",
                            function () {
                                if ($(this).val() == 4)
                                {
                                    $("#postrev_decision_maker_text").show();
                                    $("input[name='decision_maker_other']").prop('required', true);
                                } else
                                {
                                    $("#postrev_decision_maker_text").hide();
                                    $("input[name='decision_maker_other']").prop('required', false);
                                }
                            });
                    $(document).on("change", "select.desc", function () {
                        $sel_drop_name = $(this).attr('name');
                        $sel_drop_val = $(this).val();
                        $(document).find('#' + $sel_drop_name + "_desc span").hide();
                        if (isValidText($sel_drop_val)) {
                            $(document).find('#' + $sel_drop_name + "_desc span#" + $sel_drop_val).show();
                        }
                    });

                    $(document).on("click", ".pRowAdd", function () {
                        var sn = $(this).parents(".addBtnWrap").first().find(".row").length;
                        var assessment_id = $("#post_review_form").find("#id_assessment_id").val();
                        var assessor_id = $("#post_review_form").find("#id_assessor_id").val();
                        var lang_id = $("#post_review_form").find("#lang_id").val();
                        apiCall(this, "addPostReviewDiagnosticRow", "assessor_id="+assessor_id+"&"+"assessment_id="+assessment_id+"&lang_id="+lang_id+"&"+"sn=" + ($(this).parents(".addBtnWrap").first().find(".prow").length + 1) +  "&token=" + getToken(), function (s, data) {                           
                                $(s).parents(".addBtnWrap").first().find(".prow").last().after(data.content);
                                $('.mulData').multiselect({  
                                    numberDisplayed : 1
                                });
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
                     $(document).on('change',".kpa",function(){
                         var contnr = "post_review_form";
                         var sn = $(this).parents(".addBtnWrap").first().find(".row").length;
                        var assessment_id = $("#post_review_form").find("#id_assessment_id").val();
                        var assessor_id = $("#post_review_form").find("#id_assessor_id").val();
                        var lang_id = $("#post_review_form").find("#lang_id").val();
                        var kpa_id = $(this).val();                        
                        var aDd = $(this).closest('.prow').find(".kq");
                        var aDd_1 = $(this).closest('.prow').find(".sq");
                        
                         apiCall(this, "getKeyQuestions","kpa_instance_id="+kpa_id+"&assessor_id="+assessor_id+"&"+"assessment_id="+assessment_id+"&lang_id="+lang_id+"&"+"sn=" + ($(this).parents(".addBtnWrap").first().find(".prow").length) +  "&token=" + getToken(), function (s, data) {
                         aDd.find("option").next().remove();                                                                    
                        addOptions(aDd, data.content, 'key_question_instance_id', 'key_question_text')  ; 
                        aDd_1.find("option").remove(); 
                        aDd_1.multiselect("destroy").multiselect({
                         numberDisplayed : 1   
                        });
                          
                        }, function (s, msg) {
                        alert(msg);
                        });
                        return false;
                    });
                    $(document).on('change',".kq",function(){
                        var contnr = "post_review_form";
                        var sn = $(this).parents(".addBtnWrap").first().find(".row").length;
                        var assessment_id = $("#post_review_form").find("#id_assessment_id").val();
                        var assessor_id = $("#post_review_form").find("#id_assessor_id").val();
                        var lang_id = $("#post_review_form").find("#lang_id").val();
                        var kq_id = $(this).val();                        
                        var aDd = $(this).closest('.prow').find(".sq");
                        
                        apiCall(this, "getCoreQuestions","key_question_instance_id="+kq_id+"&assessor_id="+assessor_id+"&"+"assessment_id="+assessment_id+"&lang_id="+lang_id+"&"+"sn=" + ($(this).parents(".addBtnWrap").first().find(".prow").length) +  "&token=" + getToken(), function (s, data) {
                        aDd.find("option").remove();                                                                                             	
                        addOptions(aDd, data.content, 'core_question_instance_id', 'core_question_text')  ;   
                        aDd.multiselect('destroy');
                        aDd.multiselect({  
		        	numberDisplayed : 1
		          });
                        }, function (s, msg) {
                        alert(msg);
                        });
                        return false;
                    });
                    
                    $(document).on("click", "#submitPostFeedback", function () {
                        var f = $("#post_feedback_form_myFeedback");
                        var param = f.serialize()+"&is_submit=1" + "&token=" + getToken();
                        
                       /* var paramsArr = f.serializeArray();
                        var notInParamsArr = ['assessment_id','sub_role','user_id','isAjaxRequest'];
                        //console.log(paramsArr);
                         var num_questions = 0;
                        $.each( paramsArr, function( index, value ){
                            
                            if(notInParamsArr.indexOf(paramsArr[index].name) < 0 ) {
                               if($.trim(paramsArr[index].value)!='' && $.trim(paramsArr[index].value)!='undefined' &&  $.trim(paramsArr[index].value)!=0)
                                num_questions++;
                            }
                        });
                        if(num_questions != (paramsArr.length-notInParamsArr.length)) {
                             showErrorMsgInMsgBox(f,'All questions are mandatory');
                        }else {*/
                            apiCall(f, "submitPeerFeedback", param, function (s, data) {
                                $("#peerfeedbackbutton_myFeedback").hide();
                               showAssSuccessMsgInMsgBox(s, data);
                               
                            },  function (s, data,rdata){ 
                                    showAssErrorMsgInMsgBox(s, data,0)
                            });
                        //}
                    });
                    $(document).on("click", "#submitFeedbackGoal", function () {
                        var f = $("#post_feedback_goal_form");
                        var param = f.serialize()+"&is_submit=1" + "&token=" + getToken();
                       
                            apiCall(f, "submitFeedbackGoal", param, function (s, data) {
                                $("#submitFeedbackGoal").hide();
                               showSuccessMsgInMsgBox(s, data);
                               
                            }, showErrorMsgInMsgBox);
                        //}
                    });
                    $(document).on("click", "#savePostFeedback", function () {
                        var f = $("#post_feedback_form_myFeedback");
                        var param = f.serialize() +"&save=1"+ "&token=" + getToken();
                       /* var paramsArr = f.serializeArray();
                        var notInParamsArr = ['assessment_id','sub_role','user_id','isAjaxRequest'];
                        //console.log(paramsArr);
                         var num_questions = 0;
                        $.each( paramsArr, function( index, value ){
                            
                            if(notInParamsArr.indexOf(paramsArr[index].name) < 0 ) {
                               if($.trim(paramsArr[index].value)!='' && $.trim(paramsArr[index].value)!='undefined' &&  $.trim(paramsArr[index].value)!=0)
                                num_questions++;
                            }
                        });
                        if(num_questions != (paramsArr.length-notInParamsArr.length)) {
                             showErrorMsgInMsgBox(f,'All questions are mandatory');
                        }else {*/
                            apiCall(f, "submitPeerFeedback", param, function (s, data) {
                               showAssSuccessMsgInMsgBox(s, data,0);
                            },  function (s, data,rdata){ 
                                    showAssErrorMsgInMsgBox(s, data,0)
                            });
                        //}
                    });
                    
                    $(document).on("click", "#submitSelfFeedback", function () {
                        var f = $("#post_self_feedback_form");
                        var num_questions = 0;
                        var questions = [];
                         $('textarea[name^="q_id"]').each(function(i, v) {
                            num_questions++;
                            if($.trim($(this).val())!='') {
                                questions[i] = $(this).val();
                            }
                        });
                        if(num_questions != questions.length) {
                             showErrorMsgInMsgBox(f,'All questions are mandatory');
                        }else {
                        
                            var param = f.serialize() +"&is_submit=1"+ "&token=" + getToken();
                            apiCall(f, "submitSelfFeedback", param, function (s, data) {
                                $("#selffeedbackbutton").hide();
                                showSuccessMsgInMsgBox(s, data);
                            },  showErrorMsgInMsgBox);
                        }
                        //return false;
                    });
                    
                   /* $(".submitBtn").click(function(){
                        //var id = this.id;
                        var altVal= this.alt;
                        var idVal= this.id;
                         var valArr = idVal.split("_");
                        var id = "submitSelfFeedback_"+altVal
                        
                        
                        if(valArr[0] == 'submitSelfFeedback') {
                            var formId = "post_self_feedback_form_"+altVal;
                                submitAssSelfFeedback(formId,altVal,0);
                        }else {
                            var formId = "#post_feedback_form_"+valArr[1];
                            submitAssPostFeedback(formId,valArr[1],0);
                        }
                       
                   });*/
                    $(".submitApprove").click(function(){
                        //var id = this.id;
                        var altVal= this.alt;
                        var idVal= this.id;
                        
                         var valArr = idVal.split("_");
                        var id = "submitSelfFeedback_"+valArr[1]
                        altVal = valArr[1];
                        if(valArr[0] == 'submitSelfFeedback') {
                            var formId = "post_self_feedback_form_"+valArr[1];
                            //alert(formId);
                                submitAssSelfFeedback(formId,altVal,1);
                        }else {
                            var formId = "#post_feedback_form_"+valArr[1];
                            submitAssPostFeedback(formId,valArr[1],1);
                        }
                       
                   });
                   
                   $(".saveApprove").click(function(){
                        //var id = this.id;
                        var idVal= this.id;
                        //alert(idVal);
                        var valArr = idVal.split("_");
                        var formId = "post_self_feedback_form_"+valArr[1];
                        if(valArr[0] == 'savePostFeedback') {
                            var formId = "#post_feedback_form_"+valArr[1];
                             saveAssPostFeedback(formId,valArr[1]);
                        }else {
                             var formId = "#post_self_feedback_form_"+valArr[1];
                             saveAssSelfFeedback(formId,valArr[1]);
                        }
                        //alert(valArr[1]);
                        //var id = "submitSelfFeedback_"+altVal
                        
                        
                   });
                    
                    $(document).on("click", "#saveSelfFeedback", function () {
                        var f = $("#post_self_feedback_form");
                        var num_questions = 0;
                        var questions = [];
                         $('textarea[name^="q_id"]').each(function(i, v) {
                            num_questions++;
                            if($.trim($(this).val())!='') {
                                questions[i] = $(this).val();
                            }
                        });
                        if(num_questions != questions.length) {
                             showErrorMsgInMsgBox(f,'All questions are mandatory');
                        }else {
                        
                            var param = f.serialize() + "&token=" + getToken();
                            apiCall(f, "submitSelfFeedback", param, function (s, data) {
                               // $("#selffeedbackbutton").hide();
                                showSuccessMsgInMsgBox(s, data);
                            },  showErrorMsgInMsgBox);
                        }
                    });
                    
                        
                });
function saveAssSelfFeedback(formId,id) {
    
   
   // alert(formId);
    var f = $(formId);
   // var f = $("#post_self_feedback_form");
    var num_questions = 0;
    var questions = [];
    $(formId).find('textarea').each(function(i, v) {
        num_questions++;
        if($.trim($(this).val())!='') {
            questions[i] = $(this).val();
        }
    });
    //alert(num_questions+"-"+questions.length);
    if(num_questions != questions.length) {
        showAssErrorMsgInMsgBox(f,'All questions are mandatory',id);
    }else {

        var param = f.serialize() + "&token=" + getToken();
        apiCall(f, "submitSelfFeedback", param, function (s, data) {
           // $("#selffeedbackbutton").hide();
            showAssSuccessMsgInMsgBox(s, data,id);
        },  showErrorMsgInMsgBox);
    }
}                
function saveAssPostFeedback(formId,id) {
    
        var f = $(formId);   
        var param = f.serialize() +"&save=1"+ "&token=" + getToken();
        apiCall(f, "submitPeerFeedback", param, function (s, data) {
            showAssSuccessMsgInMsgBox(s, data,id);
        }, function (s, data,rdata){ 
            showAssErrorMsgInMsgBox(s, data,id)
    });
}  
function submitAssPostFeedback(formId,id,isApprove) {
        var f = $(formId);   
        var is_Approve ='';
        if(isApprove == 1) {
            is_Approve = '&is_approve=1';
        }
        var param = f.serialize()+"&is_submit=1"+is_Approve + "&token=" + getToken();  
        apiCall(f, "submitPeerFeedback", param, function (s, data) {
            $("#peerfeedbackbutton_"+id).hide();
           showAssSuccessMsgInMsgBox(s, data,id);
        }, function (s, data,rdata){ 
            showAssErrorMsgInMsgBox(s, data,id)
    });
} 
function submitAssSelfFeedback(formId,id,isApprove) {
    
   
    var f = $("#"+formId);
    var num_questions = 0;
    //alert('approveBtn'+formId);
    var questions = [];
      $(formId).find('textarea').each(function(i, v) {
        num_questions++;
        if($.trim($(this).val())!='') {
            questions[i] = $(this).val();
        }
    });
    //alert(num_questions);
    if(num_questions != questions.length) {
         showAssErrorMsgInMsgBox(f,'All questions are mandatory',id);
    }else {

        var param = f.serialize() +"&is_submit=1"+ "&token=" + getToken();
        apiCall(f, "submitSelfFeedback", param, function (s, data) {
            $("#selffeedbackbutton_"+id).hide();
           showAssSuccessMsgInMsgBox(s, data,id);
        }, function (s, data,rdata){ 
            showAssErrorMsgInMsgBox(s, data,id)
    });
    }
}                
function checkPostReviewCompletion() {
    //var numOfInputs = $("#post_review_form select,#post_review_form input[type=text],#post_review_form textarea").filter('[required]:visible').length;
    var numOfIncompleteInputs = 0;
    $("#post_review_form select,#post_review_form input[type=text],#post_review_form input[type=number],#post_review_form textarea,#post_review_form input[type=checkbox],#post_review_form input[type=radio]").filter('[required]:visible').each(function () {
        if (!isValidText($(this).val()))
            numOfIncompleteInputs++;

    });
    if (numOfIncompleteInputs == 0)
        $("#submitPostReview").removeAttr("disabled");
    else
        $("#submitPostReview").attr("disabled", "disabled");

}
