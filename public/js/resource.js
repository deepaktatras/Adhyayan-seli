   
$(function () {
   $('#school_related_to').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            maxHeight: 300,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
});
$(function () {
   $('#rec_provinces').multiselect({
            enableFiltering: true,
             enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 2,
            buttonWidth: '420px',
            maxHeight: 120,
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
               ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },onDropdownHide: function (element, checked) {

                  getSchoolsInProvinces();
            }
        });
});
$(function () {
   $('#rec_school').multiselect({
            enableFiltering: true,
             enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            
            buttonWidth: '420px',
            numberDisplayed: 2,
            maxHeight: 120,
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
               ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },onDropdownHide: function (element, checked) {

                  getSchoolUsersRole();
            }
        });
});
$(function () {
   $('#rec_role').multiselect({
            enableFiltering: true,
             enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 2,
            buttonWidth: '420px',
            maxHeight: 120,
            templates: {
                 filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
               ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },onDropdownHide: function (element, checked) {

                  getSchoolAllUsers();
            }
        });
});
$(function () {
   $('#rec_users_select').multiselect({
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            numberDisplayed: 2,
            maxHeight: 120,
            templates: {
                 filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
               ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
});


$(function () {
    $('#rec_network').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonWidth: '420px',
        numberDisplayed: 2,
        maxHeight: 120,
        /*buttonWidth: 'auto',*/
        templates: {
            filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
               ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
        },

        onDropdownHide: function (element, checked) {
            //alert('dd');

            //var contnr = $("#create_resource_form");
            var contnr = $("form:first");
            //console.log(contnr);
            var network_id = $('#rec_network').val();
            if (network_id != '' && network_id !== null && network_id !== undefined) {
                var postData = "network_id=" + network_id + "&token=" + getToken();
                
                $("#rec_school").multiselect('destroy');
                $("#rec_provinces").multiselect('destroy');
                $("#rec_users_select").multiselect('destroy');
                $('#rec_schools').hide();
                $('#provinces').hide();
                $('#rec_users').hide();
                $('#rec_roles').hide();
                //alert('dd');
                apiCall(contnr, "getProvincesInMultiNetwork", postData, function (s, data) {
                    if (data.message != '') {
                        $('#errors').hide();
                        $('#provinces').show();
                        var aDd = $("#provinces .province-list-dropdown");
                        //aDd.find("option").next().remove();
                        aDd.find("option").remove();
                        addOptions(aDd, data.message, 'province_id', 'province_name');
                        //aDd.append('<option value="all">ALL</option>');
                        $(contnr).find("#provinces").show();
                        $("#rec_provinces").multiselect('destroy');
                        $('#rec_provinces').multiselect({
                            includeSelectAllOption: true,
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true,
                            numberDisplayed: 1,
                            buttonWidth: '420px',
                            maxHeight: 120,
                            templates: {
                                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                                   ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                            },
                            onDropdownHide: function (element, checked) {

                               getSchoolsInProvinces();

                            }
                        });

                        $(".caret").css('float', 'right');
                        $(".caret").css('margin', '8px 0');
                    } else {
                        //getSchools(network_id, this);
                       //var contnr = $("#create_resource_form");
                       var contnr = $("form:first");

                        var postData = "network_id=" + network_id + "&token=" + getToken();
                        apiCall(contnr, "getSchoolsInNetworks", postData, function (s, data) {
                            if (data.message != '') {
                               var aDd = $("#rec_schools .province-list-dropdown");
                                            aDd.find("option").remove();
                                            //aDd.find("option").next().remove();
                                            addOptions(aDd, data.message, 'client_id', 'client_name');
                                            $("#rec_schools").show();
                                            //$('#rec_users').hide();
                                            $("#rec_school").multiselect('destroy');
                                            $('#rec_school').multiselect({
                                                includeSelectAllOption: true,
                                                enableFiltering: true,
                                                enableCaseInsensitiveFiltering: true,
                                                numberDisplayed: 1,
                                                buttonWidth: '420px',
                                                maxHeight: 120,
                                                templates: {
                                                    filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                                                       ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                                                }, onDropdownHide: function (element, checked) {
                                                    //getSchoolAllUsers();
                                                    getSchoolUsersRole();
                                                }
                                            });
                                            $(".caret").css('float', 'right');
                                            $(".caret").css('margin', '8px 0');
                                        } else {
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

/*$(function () {
        $('#rec_network').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '420px',
            maxHeight: 300,            
           templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
               },

            onDropdownHide: function(element, checked) {
        //alert('dd');
        
        var contnr = $(this).parents('form').first();
        var network_id = $('#rec_network').val();
        if (network_id!='' && network_id !== null && network_id !== undefined) {
            var postData = "network_id=" + network_id + "&token=" + getToken();
            //$("#evd_school").multiselect('destroy');
             $("#rec_provinces").multiselect('destroy');
            //$('#rec_schools').hide();                    
           // $('#provinces').hide();
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
            maxHeight: 300,
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
               },
            onDropdownHide: function(element, checked) {
        //alert('dd');
        
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
    	var postData = "province_id="+provience_ids+"&token=" + getToken();
            //$("#evd_school").multiselect('destroy');
           // $("#rec_provinces").multiselect('destroy');
            //$('#rec_schools').hide();                    
           // $('#provinces').hide();
            apiCall(this, "getSchoolsInProvinces", postData, function (s, data) {
                if (data.message != '') {
                    $('#errors').hide();
                    $('#rec_schools').show();                                        
                    var aDd = $("#rec_schools .province-list-dropdown");
                    //aDd.find("option").next().remove();
                    aDd.find("option").remove();
                    $('#rec_schools').multiselect('rebuild');
                    //jQuery('#rec_school').multiselect('dataprovider', []);
                    //addOptions(aDd, data.message, 'client_id', 'client_name');
                    
                   var  list = data.message;
                    
                   $.each(list, function(i, primaryCategory) {
                   
                        aDd.append('<option value="' + primaryCategory['client_id'] + '">' + primaryCategory['client_name'] + '</option>');
                    });
                   // $('#rec_school').multiselect('rebuild');
                    $(contnr).find("#rec_schools").show();
                    
                    $('#rec_school').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '420px',
            maxHeight: 300,
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
               }, onDropdownHide: function(element, checked) {
                         //alert('dd');
                    getSchoolAllUsers(element, checked);
         
    }
            
        });
                    
            $(".caret").css('float', 'right');
            $(".caret").css('margin', '8px 0');          
        } else {                    
                 

                }
            }, showErrorMsgInMsgBox);
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
            maxHeight: 300,
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
               },
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
 
 });*/
 
    $(document).on('change', 'input[name="roles[]"]', function(e){
        //alert("ok");
       // getSchoolAllUsers();
    })
    $(document).on('click','#add_folder',function(e) { 
     $(".addfolder").show();
    })
  
    
    function getSchoolsInProvinces() {
        
         //  return false;
                               //var contnr = $("#create_resource_form");

                                var contnr = $("form:first");
                           //var network_id = $(this).val();
                                var provience_ids = $('#rec_provinces').val();

                                expr = /all/;  // no quotes here
                                var all_ids = '';
                                if (expr.test(provience_ids)) {
                                    //alert("ok"+provience_ids);
                                    $("#rec_provinces option").each(function ()
                                    {
                                        if ($(this).val() != '' && $(this).val() != 'undefined') {
                                            all_ids = all_ids + $(this).val() + ",";
                                        }
                                        // Add $(this).val() to your list
                                    });
                                    all_ids = all_ids.substring(0, all_ids.length - 5)
                                }
                                if (all_ids.length >= 1) {
                                    provience_ids = all_ids;
                                    // var postData = "province_id="+all_ids+"&token=" + getToken();
                                }
                                //var network_ids = $('#rec_network').val();
                                if (provience_ids !== null && provience_ids !== undefined) {
                                    $('#errors').hide();
                                    //alert(provience_id);
                                    var postData = "province_id=" + provience_ids + "&token=" + getToken();
                                    apiCall(contnr, "getSchoolsInProvinces", postData, function (s, data) {
                                        //alert(data.status);
                                        if (data.status) {
                                            var aDd = $("#rec_schools .province-list-dropdown");
                                            aDd.find("option").remove();
                                            //aDd.find("option").next().remove();
                                            addOptions(aDd, data.message, 'client_id', 'client_name');
                                            $("#rec_schools").show();
                                            //$('#rec_users').hide();
                                            $("#rec_school").multiselect('destroy');
                                            $('#rec_role').multiselect('deselectAll', false);
                                            $('#rec_role').multiselect('refresh');
                                            $("#rec_roles").hide();
                                            $('#rec_user').multiselect('deselectAll', false);
                                            $('#rec_user').multiselect('refresh');
                                            $("#rec_users").hide();
                                            $('#rec_school').multiselect({
                                                includeSelectAllOption: true,
                                                enableFiltering: true,
                                                enableCaseInsensitiveFiltering: true,
                                                buttonWidth: '420px',
                                                numberDisplayed: 1,
                                                maxHeight: 120,
                                                templates: {
                                                    filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                                                       ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                                                }, onDropdownHide: function (element, checked) {
                                                    //getSchoolAllUsers();
                                                    getSchoolUsersRole();
                                                }
                                            });
                                            $(".caret").css('float', 'right');
                                            $(".caret").css('margin', '8px 0');
                                        }
                                    }, function (s, msg) {
                                        //alert(msg);
                                        $('#rec_school').multiselect('deselectAll', false);
                                        $('#rec_school').multiselect('refresh');
                                        $("#rec_schools").hide();
                                        showErrorMsgInMsgBox(s,msg);
                                        //$('#errors').html('School does not exist for this province');
                                        //$('#errors').show();
                                    });
                                } else {
                                    $('#evd_school').multiselect('deselectAll', false);
                                    $('#evd_school').multiselect('refresh');
                                    $("#rec_schools").hide();
                                    //$('#errors').html('Please select a province');
                                    $('#errors').show();
                                }
        
    }
    
    function getSchoolUsersRole() {

    //var contnr = $("#create_resource_form");
    var contnr = $("form:first");
    //var network_id = $(this).val();
    var school_ids = $('#rec_school').val();
    // var user_role_ids;
    
    if (school_ids != '' && school_ids !== null && school_ids !== undefined) {

        var postData = "school_ids=" + school_ids + "&token=" + getToken();
        apiCall(contnr, "getSchoolUsersRole", postData, function (s, data) {
            if (data.status) {
                var aDd = $("#rec_roles .province-list-dropdown");
                aDd.find("option").remove();
                $("#rec_role").multiselect('destroy');
                $('#rec_user').multiselect('deselectAll', false);
                $('#rec_user').multiselect('refresh');
                $("#rec_users").hide();
                //aDd.find("option").next().remove();
                addOptions(aDd, data.role_list, 'role_id', 'role_name');
                $("#rec_roles").show();
                //$('#rec_users').hide();

                $('#rec_role').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    numberDisplayed: 1,
                    maxHeight: 120,
                    onDropdownHide: function(element, checked) { 
                      getSchoolAllUsers(element, checked);
                      //getSchoolUsersRole(element, checked);
            
                    },
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                         ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                    }
                });
                $(".caret").css('float', 'right');
                $(".caret").css('margin', '8px 0');
            } else {
            }
        }, function (s, msg) {
            //alert(msg);
            $('#rec_role').multiselect('deselectAll', false);
            $('#rec_role').multiselect('refresh');
            $("#rec_roles").hide();
            showErrorMsgInMsgBox(s, msg);
            //$('#errors').html('School does not exist for this province');
            //$('#errors').show();
        });
    }
}
    function getSchoolAllUsers() {

    //var contnr = $("#create_resource_form");
    var contnr = $("form:first");
    //var network_id = $(this).val();
    var school_ids = $('#rec_school').val();
    // var user_role_ids;
   /* var user_role_ids = $("input[name='roles[]']:checked").map(function () {
        return $(this).val();
    }).get();*/
    var user_role_ids =   $('#rec_role').val();  
    if (school_ids != '' && school_ids !== null && school_ids !== undefined) {

        var postData = "school_ids=" + school_ids + "&user_role_ids=" + user_role_ids + "&token=" + getToken();

        apiCall(contnr, "getSchoolAllUsers", postData, function (s, data) {
            if (data.status) {
                var aDd = $("#rec_users .province-list-dropdown");
                aDd.find("option").remove();
                $("#rec_users_select").multiselect('destroy');
                //aDd.find("option").next().remove();
                addOptions(aDd, data.message, 'user_id', 'name');
                $("#rec_users").show();
                //$('#rec_users').hide();

                $('#rec_users_select').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    numberDisplayed: 1,
                    buttonWidth: '420px',
                    maxHeight: 120,
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                           ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                    }
                });
                $(".caret").css('float', 'right');
                $(".caret").css('margin', '8px 0');
            } else {
            }
        }, function (s, msg) {
            //alert(msg);
            $('#rec_users_select').multiselect('deselectAll', false);
            $('#rec_users_select').multiselect('refresh');
            $("#rec_users").hide();
            showErrorMsgInMsgBox(s, msg);
            //$('#errors').html('School does not exist for this province');
            //$('#errors').show();
        });
    }
}
 
 function getAllSchoolsEvidence(option_type, senderFrom) {
    //var contnr = $("#create_resource_form");
    var contnr = $("form:first");

    //alert($(contnr).attr('id'));

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
                $('#rec_provinces').multiselect('rebuild');
                $("#rec_users").hide();
                $("#rec_roles").hide();
                // var aDd = $(contnr).find("#rec_schools .province-list-dropdown");
                
                var aDd = $("#rec_school");
                aDd.find("option").remove();
                addOptions(aDd, data.message, 'client_id', 'client_name');
                $(contnr).find("#rec_schools").show();
                //$('#evd_school').multiselect('refresh');
                $("#rec_school").multiselect('destroy');
                $('#rec_school').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            numberDisplayed: 1,
            buttonWidth: '420px',
            maxHeight: 300,
            onDropdownHide: function(element, checked) { 
                //getSchoolAllUsers(element, checked);
                getSchoolUsersRole(element, checked);
            
            },
            templates: {
                filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                 ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
        $(".caret").css('float', 'right');
   $(".caret").css('margin', '8px 0');
            } else {
                
                $('#rec_school').multiselect('deselectAll', false);
                $('#rec_school').multiselect('refresh');
                $('#rec_school').hide();
                $('#errors').html('No  Province/School exist for this network');
                $('#errors').show();
            }
        }, showErrorMsgInMsgBox);
    } else {
        
        $('#rec_school').multiselect('deselectAll', false);
        $('#rec_school').multiselect('refresh');
        $('#rec_schools').hide();
       //$('#provinces').hide();
        $('#rec_roles').hide();
        $('#rec_users').hide();
       
        //$("#rec_users").hide();
        $('#errors').hide();
        
        $('#provinces').hide();
        $('#rec_provinces').multiselect('deselectAll', false);
        $('#rec_provinces').multiselect('refresh');
        $('#networks').show();
        
    }
}

 
 $(document).on("change", "#create_resource_form #school_related_to,#edit_resource_form #school_related_to,#create_resourcedirectory_form #school_related_to,#edit_folder_form #school_related_to", function () {
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
    
    $(function () {
        var rtype =$('input[name=resource_link_type]:checked').val();
        if(rtype=="file"){
         $("#file").show();
        $("#url").hide();   
        }else if(rtype=="url"){
         $("#file").hide();
        $("#url").show();   
        }else{
        $("#file").hide();
        $("#url").hide();
        }
        
        
    });
    
    $(function () {
    $("#checkbox_div").on('click','input:radio', function() {

      //alert($(this).val());
      
      if($(this).val()=="file"){
          $("#file").show();
          $("#url").hide();
      }else if($(this).val()=="url"){
          $("#file").hide();
          $("#url").show();
      }

    });
    
  //alert($('input[name=resource_link_type]:checked').val());
    
}); 