$(function () {
   $('#school_related_to').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
});

$(function () {
   $('#round').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
});



$(function () {
   $('#assessment_type').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            maxHeight: 300,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
            onDropdownHide: function(element, checked) {
                var assessment_type=$("#assessment_type").val();
               
               if(assessment_type=="2" || assessment_type=="4"){
                   //$("#report_view").val(2);
                   //alert("sasa");
                   $('#report_view').multiselect('destroy');
                   //$('#report_view').multiselect('refresh');
                   $("#rec_view").show();
                   
                   $('#report_view').val(2);
                   
                   
                    $('#report_view').multiselect({
                     enableFiltering: false,
                     buttonWidth: '420px',
                     maxHeight: 210,
                     templates: {
                     ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                    },
                  });
                   $(".caret").css('float', 'right');
                   $(".caret").css('margin', '8px 0');
                   
                   $('#teacher_student').multiselect('destroy');
                   $("#rec_teacher_student").hide();
                   
                    $('#rec_network').multiselect('deselectAll', false);
                    $('#rec_network').multiselect('refresh');
                    $('#round').multiselect('deselectAll', false);
                    $('#round').multiselect('refresh');
                    $('#rec_provinces').multiselect('deselectAll', false);
                    $('#rec_provinces').multiselect('refresh');
                    $('#evd_school').multiselect('deselectAll', false);
                    $('#evd_school').multiselect('refresh');
                   
               }else{
                   //$("#report_view").val(1);
                   $('#report_view').multiselect('destroy');
                   //$('#report_view').multiselect('refresh');
                   $('#report_view').val(1);
                   $("#rec_view").hide();
                   
                   $('#teacher_student').multiselect('destroy');
                   $("#rec_teacher_student").hide();
                   
                    $('#rec_network').multiselect('deselectAll', false);
                    $('#rec_network').multiselect('refresh');
                    $('#round').multiselect('deselectAll', false);
                    $('#round').multiselect('refresh');
                    $('#rec_provinces').multiselect('deselectAll', false);
                    $('#rec_provinces').multiselect('refresh');
                    $('#evd_school').multiselect('deselectAll', false);
                    $('#evd_school').multiselect('refresh');
               }
            }   
        });
});


$(function () {
        
        $('#rec_network').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '420px',
            maxHeight: ($(window).height()-($('#rec_network').offset().top+110)),
            numberDisplayed: 2,
            /*buttonWidth: 'auto',*/
           templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },

            onDropdownHide: function(element, checked) {
        //alert('dd');
        
        var contnr = $(this).parents('form').first();
        var network_id = $('#rec_network').val();
        if (network_id!='' && network_id !== null && network_id !== undefined) {
            var postData = "network_id=" + network_id + "&token=" + getToken();
            $("#evd_school").multiselect('destroy');
            $("#rec_provinces").multiselect('destroy');
            $('#rec_schools').hide();                    
            $('#provinces').hide();
            $('#teacher_student').multiselect('destroy');
            $('#rec_teacher_student').hide();
            apiCall(this, "getProvincesInMultiNetwork", postData, function (s, data) {
                if (data.message != '') {
                    $('#errors').hide();
                    $('#provinces').show();                                        
                    var aDd = $("#provinces .province-list-dropdown");
                    //aDd.find("option").next().remove();
                    aDd.find("option").remove();
                    addOptions(aDd, data.message, 'province_id', 'province_name');
                    //aDd.append('<option value="all">ALL</option>');
                    $(contnr).find("#provinces").show();
                    
                    $('#rec_provinces').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '420px',
            maxHeight: ($(window).height()-($('#rec_provinces').offset().top+110)),
            numberDisplayed: 2,
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
            onDropdownHide: function(element, checked) {
            
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
            //alert(data.status);
            if (data.status) {
                var aDd = $("#rec_schools .province-list-dropdown");
                aDd.find("option").remove();
                //aDd.find("option").next().remove();
                addOptions(aDd, data.message, 'client_id', 'client_name');
                $("#rec_schools").show();
                //$('#rec_users').hide();
                $("#evd_school").multiselect('destroy');
                 $('#teacher_student').multiselect('destroy');
                 $('#rec_teacher_student').hide();
            
                $('#evd_school').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '420px',
            maxHeight: ($(window).height()-($('#evd_school').offset().top+110)),
            numberDisplayed: 2,
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
            onDropdownHide: function(element, checked) {
                //alert("dsds");
               getTeachersStudents(this);
            } 
        });
           $(".caret").css('float', 'right');
   $(".caret").css('margin', '8px 0');         
            }
        }, function(s,msg){
            $('#evd_school').multiselect('deselectAll', false);
            $('#evd_school').multiselect('refresh');
            $("#rec_schools").hide(); 
            //$(contnr).find("#rec_users").hide() 
            //$("#createresource").show();
            //$(s).find(".ajaxMsg").removeClass("success warning info").html(msg).addClass("danger active");
            //$("#createresource").delay(2000).fadeOut();
        });
        } else {
            $('#evd_school').multiselect('deselectAll', false);
            $('#evd_school').multiselect('refresh');
            $("#rec_schools").hide(); 
            //$('#errors').html('Please select a province');
            $('#errors').show();
        }
                
             }  
        });
                    
            $(".caret").css('float', 'right');
   $(".caret").css('margin', '8px 0');          
                } else {                    
                    //getSchools(network_id, this);
                    var contnr = $(this).parents('form').first();  
    	
    	var postData = "network_id="+network_id+"&token=" + getToken();
        apiCall(this, "getSchoolsInNetworks", postData, function (s, data) {
            if (data.message != '') {
                $('#rec_schools').show();
                //$('#rec_users').hide();
                $('#errors').hide();
                var aDd = $("#evd_school");
                //aDd.find("option").next().remove();
                aDd.find("option").remove();
                addOptions(aDd, data.message, 'client_id', 'client_name');
                $(contnr).find("#rec_schools").show();
                //$(contnr).find("#rec_schools").show();
                $("#evd_school").multiselect('destroy');
                $('#evd_school').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '420px',
            maxHeight: ($(window).height()-($('#evd_school').offset().top+110)),
            numberDisplayed: 2,
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
               
            onDropdownHide: function(element, checked) {
                //alert("dsds");
               getTeachersStudents(this);
            } 
        });
            $(".caret").css('float', 'right');
   $(".caret").css('margin', '8px 0');    
            }else{
                $('#evd_school').multiselect('deselectAll', false);
                $('#evd_school').multiselect('refresh');
                $('#rec_schools').hide(); 
                $('#errors').html('No Province/School exist for this network');
                $('#errors').show();
            }
        }, showErrorMsgInMsgBox);
                    
                }
            }, showErrorMsgInMsgBox);
        } else {
            $('#evd_school').multiselect('deselectAll', false);
            $('#evd_school').multiselect('refresh');
            $('#rec_schools').hide();
            $('#rec_provinces').multiselect('deselectAll', false);
            $('#rec_provinces').multiselect('refresh');
            $('#provinces').hide();
            //$('#errors').html('Please select a network');
            $('#errors').show();
        }
    }
        });
        
   $(".caret").css('float', 'right');
   $(".caret").css('margin', '8px 0');
 
 });
 
 function getTeachersStudents(senderFrom){
     var contnr = $(this).parents('form').first();  
     var assessment_type=$("#assessment_type").val();
               
               if(assessment_type=="2" || assessment_type=="4"){
               // $('#teacher_student').multiselect('destroy');
                //$('#rec_teacher_student').hide();   
                var school_ids = $('#evd_school').val();
       
        
                var all_ids_school='';
                expr_school = /all/;  // no quotes here
             if(expr_school.test(school_ids)){
               //alert("ok"+provience_ids);
               $("#evd_school option").each(function()
                {
                    if($(this).val() !='' && $(this).val()!='undefined') {
                      all_ids_school  = all_ids_school + $(this).val()+"," ;
                }
                    // Add $(this).val() to your list
                });
                all_ids_school = all_ids_school.substring(0, all_ids.length-5)
        }
        
        if(all_ids_school.length>=1){
            school_ids = all_ids;
           // var postData = "province_id="+all_ids+"&token=" + getToken();
        }
        
        if (school_ids !== null && school_ids !== undefined) {
            $('#errors').hide();
        //alert(provience_id);
    	var postData = "school_ids="+school_ids+"&assessment_type="+assessment_type+"&token=" + getToken();
        //alert(postData);
        apiCall(senderFrom, "getAllTeachersStudents", postData, function (s, data) {
            //$('#teacher_student').multiselect('deselectAll', false);
            //$('#teacher_student').multiselect('refresh');
            //alert("sas");
            if (data.message != '' && data.NF=="0") {
                //alert(contnr);
                var aDd = $("#teacher_student");
                aDd.find("option").remove();
                //alert(data.message);
                addOptions(aDd, data.message, 'user_id', 'name');
                $("#rec_teacher_student").show();
                //$('#evd_school').multiselect('refresh');
                //$("#teacher_student").multiselect('destroy');
                $('#teacher_student').multiselect('destroy');
                $('#teacher_student').multiselect({
                   includeSelectAllOption: true,
                   enableFiltering: true,
                   enableCaseInsensitiveFiltering: true,
                   buttonWidth: '420px',
                   maxHeight: ($(window).height()-($('#teacher_student').offset().top+110)),
                   numberDisplayed: 2,
            
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               } 
                    
                });
                
                $(".caret").css('float', 'right');
                  $(".caret").css('margin', '8px 0'); 
                  
            } else {
                
                $('#teacher_student').multiselect('deselectAll', false);
                $('#teacher_student').multiselect('refresh');
                $('#rec_teacher_student').hide();
                $('#errors').html('No  User exist for these school');
                $('#errors').show();
            }
            
            
        }, showErrorMsgInMsgBox);
        
                   //$("#rec_teacher_student").show();
                   $("#rec_view").show();
               }
               
               }
 }
 
 function getAllSchoolsEvidence(option_type, senderFrom) {
    var contnr = $(this).parents('form').first();   
    
    if (option_type != '1') {
        var postData = "school_related_to=" + option_type + "&token=" + getToken();
        apiCall(senderFrom, "getAllSchools", postData, function (s, data) {
            if (data.message != '') {
                //alert("ds");
                $('#rec_schools').show();
                $('#errors').hide();
                
                $('#networks').hide();
                //$('#rec_network').multiselect('refresh');
                $('#provinces').hide();
                $('#rec_provinces').multiselect('deselectAll', false);
                $('#rec_provinces').multiselect('refresh');
                //$("#rec_users").hide();
                // var aDd = $(contnr).find("#rec_schools .province-list-dropdown");
                
                var aDd = $("#evd_school");
                aDd.find("option").remove();
                addOptions(aDd, data.message, 'client_id', 'client_name');
                $(contnr).find("#rec_schools").show();
                //$('#evd_school').multiselect('refresh');
                $("#evd_school").multiselect('destroy');
                 $('#teacher_student').multiselect('destroy');
                 $('#rec_teacher_student').hide();
                 
                $('#evd_school').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '420px',
            maxHeight: ($(window).height()-($('#evd_school').offset().top+110)),
            numberDisplayed: 2,
            
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
            onDropdownHide: function(element, checked) {
                //alert("dsd");
               getTeachersStudents(senderFrom);
            }   
        });
        $(".caret").css('float', 'right');
   $(".caret").css('margin', '8px 0');
            } else {
                
                $('#evd_school').multiselect('deselectAll', false);
                $('#evd_school').multiselect('refresh');
                $('#rec_schools').hide();
                $('#errors').html('No  Province/School exist for this network');
                $('#errors').show();
            }
        }, showErrorMsgInMsgBox);
    } else {
        
        $('#evd_school').multiselect('deselectAll', false);
        $('#evd_school').multiselect('refresh');
        $('#rec_schools').hide();
       
        //$("#rec_users").hide();
        $('#errors').hide();
        
        $('#provinces').hide();
        $('#rec_provinces').multiselect('deselectAll', false);
        $('#rec_provinces').multiselect('refresh');
        $('#networks').show();
        
    }
}

 
 $(document).on("change", "#create_evidence_data_form #school_related_to", function () {
        var contnr = $(this).parents('form').first();
        var option_type = $('#school_related_to').val();
        
        if (option_type!='' && option_type !== null && option_type !== undefined) {
            //$("#evd_school").multiselect('destroy');
            getAllSchoolsEvidence(option_type,this);
            
            /*$('#evd_school').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            onDropdownHide: function(element, checked) {
        alert('dd');
    }
        });*/
        
        } else {
            //$('#errors').html('Please select a network');
            $('#errors').show();
        }
        //  return false;

    });