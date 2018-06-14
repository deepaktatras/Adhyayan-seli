jQuery(document).ready(function ($) {
//creating diagnostic
    $(document).on('dataChanged', function () {
        $("#saveDiagBtn").removeAttr("disabled");
    });
    $(document).on('change', "#diagnostic_name,#id_recommendations_levels", function () {
        $(document).trigger("dataChanged");
    });
    $(document).on('change', "#allcontent", function () {
        $(document).trigger("dataChanged");
    });
    $(document).on('click', "#saveDiagBtn", function () {
        $("#saveDiagBtn").attr("disabled", true);
    });
    
    $(document).on('change', "#assessment_type", function () {
        if ($("#assessment_type").val() == 2)
            $("#teacherDiv").show();
        else
            $("#teacherDiv").hide();

    });
    
    $(document).on('change', "#diagnosticType #assessment_action", function () {
        $("#diagnostic_id_initial").val("");
        $("#equivalence_id").val("");
        $("#lang_id_original").val("");
        $("#diag_name").val("");
        $("#lang_name").val("");
        $("#kpa_recommendations_p").val("");
        $("#kq_recommendations_p").val("");
        $("#cq_recommendations_p").val("");
        $("#js_recommendations_p").val("");
        $("#diagnostic_id_parent_show").val("");
        $("#diagnostic_id_parent_lang").val("");
        $("#language_id").val("");
        $("#assessment_type").val("");
        $("#teacher_type").val("");
        
        if ($("#assessment_action").val() == 1){
            $("#assessment_type_sh").show();
            $("#language_id_sh").show();
            $("#diagnostic_id_sh").hide();
            $("#diagnostic_id_sh_lang").hide();
            $("#lag_label").html("Language");
        }else if($("#assessment_action").val() == 2){
            $("#assessment_type_sh").hide();
            $("#teacherDiv").hide();
            $("#language_id_sh").show();
            $("#diagnostic_id_sh").show();
            $("#diagnostic_id_sh_lang").show();
            $("#lag_label").html("Translate into ");
        }else{
            $("#assessment_type_sh").hide();
            $("#language_id_sh").hide();
            $("#teacherDiv").hide();
            $("#diagnostic_id_sh").hide();
            $("#diagnostic_id_sh_lang").hide();
            $("#lag_label").html("Language");
        }
        
        var contnr = $(this).parents('form').first();
        var action_type = $(this).val();
        
        if (action_type !='' && action_type !== null && action_type !== undefined) {
            $('#errors').hide();
            var postData = "action_type=" + action_type + "&token=" + getToken();
            apiCall(this, "getLanguagesfromA", postData, function (s, data) {
                var aDd = $(contnr).find("#language_id");
                aDd.find("option").next().remove();
                addOptions(aDd, data.languages, 'language_id', 'language_name');
                $(contnr).find("#language_id").show();
            }, showErrorMsgInMsgBox);
        }
        
    });
    
    $(document).on("change", "#diagnosticType #diagnostic_id_parent_show", function () {
        var contnr = $(this).parents('form').first();
        var diagnostic_id = $(this).val();
        if (diagnostic_id !='' && diagnostic_id !== null && diagnostic_id !== undefined) {
            $('#errors').hide();
            var postData = "diagnostic_id=" + diagnostic_id + "&token=" + getToken();
            apiCall(this, "getLanguagesfromDiagnostic", postData, function (s, data) {
                var aDd = $(contnr).find("#diagnostic_id_parent_lang");
                aDd.find("option").next().remove();
                addOptions(aDd, data.languages, 'language_id', 'language_name');
                
                $(contnr).find("#language_id").show();
                //$(contnr).find("#assessment_type").val(data.d_type);
               
                //$(contnr).find("#teacher_type").val(data.d_teacher_type);
                //$(contnr).find("#diagnostic_id_initial").val(data.diagnostic_id);
                //$(contnr).find("#equivalence_id").val(data.equivalence_id);
                //$(contnr).find("#lang_id_original").val(data.lang_id_original);
                //$(contnr).find("#diag_name").val(data.diag_title);
                //$(contnr).find("#kpa_recommendations_p").val(data.kpa_recommendations_p);
                //$(contnr).find("#kq_recommendations_p").val(data.kq_recommendations_p);
                //$(contnr).find("#cq_recommendations_p").val(data.cq_recommendations_p);
                //$(contnr).find("#js_recommendations_p").val(data.js_recommendations);
                //
                //alert(data.diag_title);
                //$(contnr).find("#assessment_type_sh").show();
                //$(contnr).find("#teacherDiv").show();
                
            }, showErrorMsgInMsgBox);
        } else {
            $('#errors').html('Please select a diagnostic');
            $('#errors').show();
        }
    });
    
    $(document).on("change", "#diagnosticType #diagnostic_id_parent_lang", function () {
        var contnr = $(this).parents('form').first();
        var diagnostic_id = $("#diagnostic_id_parent_show").val();
        var lang_id = $(this).val();
        
        if (diagnostic_id !='' && diagnostic_id !== null && diagnostic_id !== undefined) {
            $('#errors').hide();
            var postData = "diagnostic_id=" + diagnostic_id + "&lang_id="+lang_id+"&token=" + getToken();
            apiCall(this, "getLanguagesAvaifromDiagnostic", postData, function (s, data) {
                var aDd = $(contnr).find("#language_id");
                aDd.find("option").next().remove();
                addOptions(aDd, data.languages, 'language_id', 'language_name');
                
                $(contnr).find("#language_id").show();
                $(contnr).find("#assessment_type").val(data.d_type);
                $(contnr).find("#diagnostic_id_parent").val(data.lang_translation_id);
                
                $(contnr).find("#teacher_type").val(data.d_teacher_type);
                $(contnr).find("#diagnostic_id_initial").val(data.diagnostic_id);
                $(contnr).find("#equivalence_id").val(data.equivalence_id);
                $(contnr).find("#lang_id_original").val(data.lang_id_original);
                $(contnr).find("#diag_name").val(data.diag_title);
                $(contnr).find("#kpa_recommendations_p").val(data.kpa_recommendations_p);
                $(contnr).find("#kq_recommendations_p").val(data.kq_recommendations_p);
                $(contnr).find("#cq_recommendations_p").val(data.cq_recommendations_p);
                $(contnr).find("#js_recommendations_p").val(data.js_recommendations);
                
                //alert(data.diag_title);
                //$(contnr).find("#assessment_type_sh").show();
                //$(contnr).find("#teacherDiv").show();
                
            }, showErrorMsgInMsgBox);
        } else {
            $('#errors').html('Please select a diagnostic');
            $('#errors').show();
        }
    });
    
    $(document).on("change", "#diagnosticType #language_id", function () {
        var contnr = $(this).parents('form').first();
        var language_id = $(this).val();
        if (language_id !='' && language_id !== null && language_id !== undefined) {
            $('#errors').hide();
            var postData = "language_id=" + language_id + "&token=" + getToken();
            apiCall(this, "getLanguagesName", postData, function (s, data) {
                
                $(contnr).find("#lang_name").val(data.language);
                //alert(data.diag_title);
                //$(contnr).find("#assessment_type_sh").show();
                //$(contnr).find("#teacherDiv").show();
                
            }, showErrorMsgInMsgBox);
        } else {
            $('#errors').html('Please select a Language');
            $('#errors').show();
        }
    });
    
    $(document).find('.mulselect').multiselect({  
    	numberDisplayed : 1,
    	init:function(){
    		$(this).css('visibility',true)
    	}
      });  
    
    $(document).on('submit', "#addmoreform", function (e) {
        e.preventDefault();
        // kpaText = $("#editKpaBox input[type='text']").val();	  		  
        // $("#"+kpaId+" span").text(kpaText);
        $arr = [];
        $arrNew = [];
        $arrUnion = [];
        $("#sortableR li").each(function () {
            //console.log($(this).find('span').text());
            if ($(this).data('id') === 0)
                $arrNew.push($(this).children('span').text());//store all the new added selected Questions in an array
            else
                $arr.push($(this).data('id') + "_" + $(this).children('span').text());//store all the selected Questions in an array
                $arrUnion.push($(this).data('id') + "_" + $(this).children('span').text());
            //$(this).attr('data-type');
        });
        
        $("#sortableR_new li input").each(function () {
            if ($(this).data('id') === 0)
            $arrNew.push($(this).attr('name') + "_" + $(this).val());
            else
            $arr.push($(this).attr('name') + "_" + $(this).val());
            $arrUnion.push($(this).attr('name') + "_" + $(this).val());
        });
        
        // console.log($arr);
        var contnr = $('#btn_kpa_save').closest('.sortable-form').data('for') != undefined ? "#" + $('#btn_kpa_save').closest(".sortable-form").data('for') + " " : "";
        var type = $('#btn_kpa_save').closest('.sortable-form').data('type');
        var assessmentId = $('#btn_kpa_save').closest('.sortable-form').data('assessmentid');
        var diagnosticName = $(contnr + "#diagnostic_name").val().trim();
        var diagnosticId = $(contnr + "#diagnostic_id").val();
        var langId = $(contnr + "#langId").val();
        
        var parentdiaId = $(contnr + "#parent_diagnostic_id").val();
        var equivalenceId = $(contnr + "#equivalenceId").val();
        var langIdOriginal = $(contnr + "#langIdOriginal").val();
        
        var teacherCategoryId = $(contnr).find("#teacherCategoryId").val();
        var kpa_recommendations = $(contnr).find("#id_recommendations_levels [value='kpa_recommendations']:checked").length;
        var kq_recommendations = $(contnr).find("#id_recommendations_levels [value='kq_recommendations']:checked").length;
        var cq_recommendations = $(contnr).find("#id_recommendations_levels [value='cq_recommendations']:checked").length;
        var js_recommendations = $(contnr).find("#id_recommendations_levels [value='js_recommendations']:checked").length;

        if ($(contnr + "#diagnostic_name").val().trim() == '')
            alert("Please fill diagnostic title!");
        else
        {
            //console.log(contnr);
            //console.log(type);
            $(contnr + "#selected_" + type).val($arr);
            if (type == 'kpa')
            {
                var postData = new FormData(this);
                postData.append("token", getToken());
                postData.append("teacherCategoryId", teacherCategoryId);
                postData.append("diagnosticId", diagnosticId);
                for (var i = 0; i < $arrUnion.length; i++) {
                    postData.append('diagnostic_questions[' + i + ']', $arrUnion[i]);
                }

                for (var i = 0; i < $arr.length; i++) {
                    postData.append('diagnostic_questions_update[' + i + ']', $arr[i]);
                }

                for (var i = 0; i < $arrNew.length; i++) {
                    postData.append('diagnostic_questions_new[' + i + ']', $arrNew[i]);
                }
                postData.append("type", type);
                postData.append("assessmentId", assessmentId);
                postData.append("diagnosticName", diagnosticName);
                postData.append("langId", langId);
                postData.append("parentdiaId", parentdiaId);
                postData.append("equivalenceId", equivalenceId);
                postData.append("langIdOriginal", langIdOriginal);
                
                postData.append("kpa_recommendations", kpa_recommendations);
                postData.append("kq_recommendations", kq_recommendations);
                postData.append("cq_recommendations", cq_recommendations);
                postData.append("js_recommendations", js_recommendations);

                apiCall($('#btn_kpa_save').closest('.sortable-form').first(), "saveDiagnosticKpa", postData, function (s, data) {
                    showSuccessMsgInMsgBox($('.sortable-form'), data);
                    console.log('done');
                    $('#btn_kpa_save').closest(".modal").modal("hide");
                    $(contnr + "#allContent").html(data.content);
                    $(contnr + "#addmorekpa").attr('href', $(contnr + "#addmorekpa").attr('href') + diagnosticId);
                    $(contnr + "#diagnostic_id").val(data.diagnosticId);
                    $(document).trigger("dataChanged");
                }, showErrorMsgInMsgBox, undefined, {processData: false, contentType: false});
            } else if (type == 'kq')
            {
                var kpaId = $('#btn_kpa_save').closest('.sortable-form').data('id');
                console.log($('#btn_kpa_save').closest('.sortable-form').data('id'));
                apiCall($('#btn_kpa_save').closest('.sortable-form').first(), "saveDiagnosticKeyQuestions", {"token": getToken(), "kpaId": kpaId, "diagnosticId": diagnosticId,"langId":langId,"parentdiaId":parentdiaId,"equivalenceId":equivalenceId,"langIdOriginal":langIdOriginal, "diagnostic_questions": $arrUnion, "diagnostic_questions_update": $arr, "diagnostic_questions_new": $arrNew, "type": type, "assessmentId": assessmentId, "diagnosticName": diagnosticName,"kpa_recommendations": kpa_recommendations, "kq_recommendations":kq_recommendations, "cq_recommendations":cq_recommendations, "js_recommendations":js_recommendations},
                        function (s, data) {
                            showSuccessMsgInMsgBox($('.sortable-form'), data);
                            console.log('done');
                            $('#btn_kpa_save').closest(".modal").modal("hide");
                            $(contnr + "#allContent").html(data.content);
                            showKqTab({"kpaId": kpaId});
                            $(contnr + "#addmorekpa").attr('href', $(contnr + "#addmorekpa").attr('href') + diagnosticId);
                            $(contnr + "#diagnostic_id").val(data.diagnosticId);
                            $(document).trigger("dataChanged");
                        }, showErrorMsgInMsgBox);
            } else if (type == 'cq')
            {
                var kqId = $('#btn_kpa_save').closest('.sortable-form').data('kqid');
                var kpaId = $('#btn_kpa_save').closest('.sortable-form').data('id');
                console.log($('#btn_kpa_save').closest('.sortable-form').data('id'));
                apiCall($('#btn_kpa_save').closest('.sortable-form').first(), "saveDiagnosticCoreQuestions", {"token": getToken(), "kqId": kqId, "kpaId": kpaId, "diagnosticId": diagnosticId,"langId":langId,"parentdiaId":parentdiaId,"equivalenceId":equivalenceId,"langIdOriginal":langIdOriginal, "diagnostic_questions": $arrUnion, "diagnostic_questions_update": $arr, "diagnostic_questions_new": $arrNew, "type": type, "assessmentId": assessmentId, "diagnosticName": diagnosticName,"kpa_recommendations": kpa_recommendations, "kq_recommendations":kq_recommendations, "cq_recommendations":cq_recommendations, "js_recommendations":js_recommendations},
                        function (s, data) {
                            showSuccessMsgInMsgBox($('.sortable-form'), data);
                            console.log('done');
                            $('#btn_kpa_save').closest(".modal").modal("hide");
                            $(contnr + "#allContent").html(data.content);
                            showCqTab({"kpaId": kpaId, "kqid": kqId});
                            $(contnr + "#addmorekpa").attr('href', $(contnr + "#addmorekpa").attr('href') + diagnosticId);
                            $(contnr + "#diagnostic_id").val(data.diagnosticId);
                            $(document).trigger("dataChanged");
                        }, showErrorMsgInMsgBox);
            } else if (type == 'jss')
            {
                var kqId = $('#btn_kpa_save').closest('.sortable-form').data('kqid');
                var kpaId = $('#btn_kpa_save').closest('.sortable-form').data('id');
                var cqId = $('#btn_kpa_save').closest('.sortable-form').data('cqid');
                console.log($('#btn_kpa_save').closest('.sortable-form').data('id'));
                apiCall($('#btn_kpa_save').closest('.sortable-form').first(), "saveDiagnosticJudgementStatements", {"token": getToken(), "cqId": cqId, "kqId": kqId, "kpaId": kpaId, "diagnosticId": diagnosticId,"langId":langId,"parentdiaId":parentdiaId,"equivalenceId":equivalenceId,"langIdOriginal":langIdOriginal, "diagnostic_questions": $arrUnion, "diagnostic_questions_update": $arr, "diagnostic_questions_new": $arrNew, "type": type, "assessmentId": assessmentId, "diagnosticName": diagnosticName,"kpa_recommendations": kpa_recommendations, "kq_recommendations":kq_recommendations, "cq_recommendations":cq_recommendations, "js_recommendations":js_recommendations},
                        function (s, data) {
                            showSuccessMsgInMsgBox($('.sortable-form'), data);
                            console.log('done js');
                            $('#btn_kpa_save').closest(".modal").modal("hide");
                            $(contnr + "#allContent").html(data.content);
                            //$(contnr+"#allContent #kqA-cq"+cqId).html(data.content);
                            //var datatest;
                            //datatest = {"kpaId":kpaId,"kqid":kqId,"cqid":cqId};
                            console.log("jscalldata: ")
                            //console.log({"kpaId":kpaId,"kqid":kqId,"cqid":cqId});
                            showJsTab({"kpaId": kpaId, "kqid": kqId, "cqid": cqId});
                            $(contnr + "#addmorekpa").attr('href', $(contnr + "#addmorekpa").attr('href') + diagnosticId);
                            $(contnr + "#diagnostic_id").val(data.diagnosticId);
                            $(document).trigger("dataChanged");
                        }, showErrorMsgInMsgBox);
            }
        }
    });
    $(document).on('click', "#saveDiagBtn", function () {
        var contnr = $('#add_diagnostic_form').first();
        console.log($(contnr).find("#diagnostic_name").val())
        var assessmentId = $(contnr).find("#assessmentId").val();
        var diagnosticName = $(contnr).find("#diagnostic_name").val().trim();
        var diagnosticId = $(contnr).find("#diagnostic_id").val();
        var langId = $(contnr).find("#langId").val();
        var parentdiaId = $(contnr).find("#parent_diagnostic_id").val();
        var equivalenceId = $(contnr).find("#equivalenceId").val();
        var langIdOriginal = $(contnr).find("#langIdOriginal").val();
        
        var teacherCategoryId = $(contnr).find("#teacherCategoryId").val();
        var kpa_recommendations = $(contnr).find("#id_recommendations_levels [value='kpa_recommendations']:checked").length;
        var kq_recommendations = $(contnr).find("#id_recommendations_levels [value='kq_recommendations']:checked").length;
        var cq_recommendations = $(contnr).find("#id_recommendations_levels [value='cq_recommendations']:checked").length;
        var js_recommendations = $(contnr).find("#id_recommendations_levels [value='js_recommendations']:checked").length;
        
        if ($(contnr).find("#assessmentId").val() == '' || $(contnr).find("#assessmentId").val() < 1) {
            alert("Choose Review Type!");
            return false;
        }
        
        if ($(contnr).find("#langId").val() == '' || $(contnr).find("#langId").val() < 1) {
            alert("Choose Language!");
            return false;
        }
        
        if ($(contnr).find("#diagnostic_name").val().trim() == '')
        {
            alert("Please fill diagnostic title!");
            return false;
        }
        var assessmentId = $(contnr).find("#assessmentId").val();

        var dig_image_id = $('#dig_image_id').val();
        apiCall(contnr, "saveDiagnostic", {"token": getToken(), "teacherCategoryId": teacherCategoryId, "assessmentId": assessmentId, "diagnosticId": diagnosticId, "langId": langId, "parentdiaId":parentdiaId,"equivalenceId":equivalenceId,"langIdOriginal":langIdOriginal, "diagnosticName": diagnosticName, "dig_image_id": dig_image_id, "kpa_recommendations": kpa_recommendations, "kq_recommendations":kq_recommendations, "cq_recommendations":cq_recommendations, "js_recommendations":js_recommendations},
                function (s, data) {
                    $('#add_diagnostic_form').first().find("#diagnostic_id").val(data.diagnosticId);
                    //showSuccessMsgInMsgBox(s,data);
                    alert(data.message);
                }, showErrorMsgInMsgBox);
    });
    $(document).on("click", "#choose_assessment", function () {
        var contnr = $(this).parents('.sortable-form').data('for') != undefined ? "#" + $(this).parents(".sortable-form").data('for') + " " : "";
        
        if($("#assessment_action").val() == ''){
            alert("Choose Action!");
            return false;
        }
        else if ($("#assessment_action").val()=="2" && $("#diagnostic_id_parent_show").val() == '') {
            alert("Choose Diagnostic!");
            return false;
        }else if ($("#assessment_action").val()=="2" && $("#diagnostic_id_parent_lang").val() == '') {
            alert("Choose Available Diagnostic Languages!");
            return false;
        }else if ($("#assessment_action").val()=="2" && $("#diagnostic_id_parent").val() == '') {
            alert("Choose Diagnostic!");
            return false;
        }
        else if ($("#language_id").val() == '') {
            alert("Choose Language!");
            return false;
        }
        else if ($("#assessment_action").val()=="1" && $("#assessment_type").val() == '') {
            alert("Choose Review Type!");
            return false;
        } else if ($("#assessment_action").val()=="1" && $("#assessment_type").val() == 2 && $("#teacher_type").val() == '') {
            alert("Choose Type of teacher!");
            return false;
        } else if($("#assessment_type").val()!=1 && $("#language_id").val()!=9){
            alert("Only School Review is allowed in other than English Languages!");
            return false;
        }
        
        
        
        $(contnr + "#langId").val($("#language_id").val());//langVal
        $(contnr + "#assessmentId").val($("#assessment_type").val());//assesmentVal
        $(contnr + "#diagnostic_id").val($("#diagnostic_id_initial").val());//diagnosticId
        $(contnr + "#equivalenceId").val($("#equivalence_id").val());//equivalenceId
        $(contnr + "#parent_diagnostic_id").val($("#diagnostic_id_parent").val());//parentId
        $(contnr + "#langIdOriginal").val($("#lang_id_original").val());//originalLanguageId
        $(contnr + "#diagnostic_name").val($("#diag_name").val());//diagnostic Parent name
        
      //alert($("#kpa_recommendations_p").val());
        
                //$values = [];
                if(1*$("#diagnostic_id_parent").val()>0 && 1*$("#equivalence_id").val()>0 && 1*$("#lang_id_original").val()>0){
                if($("#kpa_recommendations_p").val()=="1"){
                    //$values.push("kpa_recommendations");
                    $("input[value='kpa_recommendations']").click();
                }
                
                if($("#kq_recommendations_p").val()=="1"){
                    //$values.push("kpa_recommendations");
                    $("input[value='kq_recommendations_p']").click();
                }
                
                if($("#cq_recommendations_p").val()=="1"){
                    //$values.push("kpa_recommendations");
                    $("input[value='cq_recommendations_p']").click();
                }
                
                if($("#js_recommendations_p").val()=="1"){
                    //$values.push("kpa_recommendations");
                    $("input[value='js_recommendations_p']").click();
                }
                
            }
                //$(contnr + "#id_recommendations_levels").val([ "kpa_recommendations" ]);
                //console.log($values);
        
        if ($("#assessment_type").val() == 2)
        {
            $(contnr + "#assesmentVal").text("Review Type: " + $("#assessment_type option:selected").text() + "(" + $("#teacher_type option:selected").text() + ")");
            $(contnr + "#teacherCategoryId").val($("#teacher_type").val());
        } else
        $(contnr + "#assesmentVal").text("Review Type: " + $("#assessment_type option:selected").text());
        $(contnr + "#assesmentVal").show();
        $(contnr + "#language_name").text("Language: " + $("#lang_name").val());
        $(contnr + "#language_name").show();
        //alert($(contnr+"#addmorekpa").attr('href'))
        $(contnr + "#addmorekpa").attr('href', $(contnr + "#addmorekpa").attr('href') + $(contnr + "#assessmentId").val());
        $(contnr + "#addmorekpa").attr('href', $(contnr + "#addmorekpa").attr('href') + '&langId='+$(contnr + "#langId").val());
        
        if(1*$(contnr + "#diagnostic_id").val()>0){
         $(contnr + "#addmorekpa").attr('href', $(contnr + "#addmorekpa").attr('href') + '&diagnosticId='+$(contnr + "#diagnostic_id").val());    
        }
        
        if(1*$(contnr + "#parent_diagnostic_id").val()>0){
         $(contnr + "#addmorekpa").attr('href', $(contnr + "#addmorekpa").attr('href') + '&parentId='+$(contnr + "#parent_diagnostic_id").val());    
        }
        
        if(1*$(contnr + "#equivalenceId").val()>0){
         $(contnr + "#addmorekpa").attr('href', $(contnr + "#addmorekpa").attr('href') + '&equivalenceId='+$(contnr + "#equivalenceId").val());    
        }
        
        if(1*$(contnr + "#langIdOriginal").val()>0){
         $(contnr + "#addmorekpa").attr('href', $(contnr + "#addmorekpa").attr('href') + '&langIdOriginal='+$(contnr + "#langIdOriginal").val());    
        }
        
        $(contnr + ".tab1Hldr").show();
        $(contnr + "#chooseAssmt").hide();
        $(this).parents(".modal").modal("hide");
        return false;
    });
    
    $(document).on("submit", "#add_diagnostic_form", function () {//submit the diagnostic		
        var diagnosticName = $("#diagnostic_name").val().trim();
        var diagnosticId = $("#diagnostic_id").val();
        var langId = $("#langId").val();
        
        var parentdiaId = $("#parent_diagnostic_id").val();
        var equivalenceId = $("#equivalenceId").val();
        var langIdOriginal = $("#langIdOriginal").val();
        
        var contnr = "#add_diagnostic_form";
        if ($("#add_diagnostic_form").find("#assessmentId").val() == '' || $("#add_diagnostic_form").find("#assessmentId").val() < 1) {
            alert("Choose Review Type!");
            return false;
        }
        
        if ($("#add_diagnostic_form").find("#langId").val() == '' || $("#add_diagnostic_form").find("#langId").val() < 1) {
            alert("Choose Language!");
            return false;
        }
        
        if ($("#add_diagnostic_form").find("#diagnostic_name").val().trim() == '')
        {
            alert("Please fill diagnostic title!");
            return false;
        }
        if (!confirm("You can not edit the diagnostic after submitting it. Are you sure that you want to submit diagnostic?"))
            return false;
        var kpa_recommendations = $(contnr).find("#id_recommendations_levels [value='kpa_recommendations']:checked").length;
        var kq_recommendations = $(contnr).find("#id_recommendations_levels [value='kq_recommendations']:checked").length;
        var cq_recommendations = $(contnr).find("#id_recommendations_levels [value='cq_recommendations']:checked").length;
        var js_recommendations = $(contnr).find("#id_recommendations_levels [value='js_recommendations']:checked").length;
        apiCall($(this), "submitDiagnostic", {"token": getToken(), "kpa_recommendations":kpa_recommendations,"kq_recommendations":kq_recommendations,"cq_recommendations":cq_recommendations,"js_recommendations":js_recommendations,"diagnosticId": diagnosticId,"langId":langId, "parentdiaId":parentdiaId, "equivalenceId":equivalenceId,"langIdOriginal":langIdOriginal, "diagnosticName": diagnosticName},
                function (s, data) {
                    showSuccessMsgInMsgBox(s, data);
                    $("#saveDiagBtn").remove();
                    $("input[type='submit']").remove();
                }, function showErrorMsgInMsgBox(s, msg, data) {
            //console.log(msg+" ");
            console.log(data);
            var href = '';
            var f = '';
            var currKpaTab = '';
            var currKqTab = '';
            //if(data.type)
            switch (data.type) {
                case 'kq' :
                    showKqTab(data);
                case 'kpa' :
                    break;
                case 'cq' :
                    showCqTab(data);
                    break;
                case 'js' :
                    showJsTab(data);
                    break;
            }
            $(s).find(".ajaxMsg").removeClass("success warning info").html(msg).addClass("danger active");
        });
        return false;
    });
    

});

function showJsTab(data)
{
    var f = $("#add_diagnostic_form");
    f.find(".redTab.nav-tabs li.item.active, .tab-pane.active.kpa").removeClass("active");
    console.log(f.find("li.item a[href=#kpa" + data.kpaId + "]"))
    f.find("li.item a[href=#kpa" + data.kpaId + "]").parent().addClass("active");
    var currKpaTab = f.find("#kpa" + data.kpaId).addClass("fade in active");
    currKpaTab.find(".yellowTab.nav-tabs li.item.active, .tab-pane.active.keyQ").removeClass("active");
    f.find("li.item a[href=#kq" + data.kqid + "]").parent().addClass("active");
    var currKqTab = f.find("#kq" + data.kqid).addClass('in active');
    currKqTab.find(".blackTab.nav-tabs li.item.active, .tab-pane.active.coreQ").removeClass("active");
    currKqTab.find(".blackTab.nav-tabs li.item a[href=#kqA-cq" + data.cqid + "]").parent().addClass('active');
    currKqTab.find("#kqA-cq" + data.cqid).addClass('active in');
}
function showCqTab(data)
{
    var f = $("#add_diagnostic_form");
    f.find(".redTab.nav-tabs li.item.active, .tab-pane.active.kpa").removeClass("active");
    f.find("li.item a[href=#kpa" + data.kpaId + "]").parent().addClass("active");
    var currKpaTab = f.find("#kpa" + data.kpaId).addClass("fade in active");
    currKpaTab.find(".yellowTab.nav-tabs li.item.active, .tab-pane.active.keyQ").removeClass("active");
    f.find("li.item a[href=#kq" + data.kqid + "]").parent().addClass("active");
    var currKqTab = f.find("#kq" + data.kqid).addClass('in active');
    currKqTab.find(".blackTab.nav-tabs li.item.active, .tab-pane.active.coreQ").removeClass("active");
    currKqTab.find(".blackTab.nav-tabs li.item").first().addClass("active");
    currKqTab.find(".tab-pane.coreQ").first().addClass("in active");
}
function showKqTab(data) {
    var f = $("#add_diagnostic_form");
    f.find(".redTab.nav-tabs li.item.active, .tab-pane.active.kpa").removeClass("active");
    f.find("li.item a[href=#kpa" + data.kpaId + "]").parent().addClass("active");
    var currKpaTab = f.find("#kpa" + data.kpaId).addClass("fade in active");
    currKpaTab.find(".yellowTab.nav-tabs li.item.active, .tab-pane.active.keyQ").removeClass("active");
    currKpaTab.find(".yellowTab.nav-tabs li.item").first().addClass("active");
    var currKqTab = currKpaTab.find(".tab-pane.keyQ").first().addClass("active in");
    currKqTab.find(".blackTab.nav-tabs li.item.active, .tab-pane.active.coreQ").removeClass("active");
    currKqTab.find(".blackTab.nav-tabs li.item").first().addClass('active');
    currKqTab.find(".tab-pane.coreQ").first().addClass('in active');
}