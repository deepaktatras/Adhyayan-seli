$(function () {
   $('#create_student_data_form #report_type').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
        $(".caret").css('float', 'right');
        $(".caret").css('margin', '8px 0');
});


$(function () {
   $('#create_student_data_form #rec_network').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
        $(".caret").css('float', 'right');
        $(".caret").css('margin', '8px 0');
});


$(function () {
   $('#create_student_data_form #evd_round').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
        $(".caret").css('float', 'right');
        $(".caret").css('margin', '8px 0');
});

$(function () {
   $('#create_student_data_form #rec_provinces').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
        $(".caret").css('float', 'right');
        $(".caret").css('margin', '8px 0');
});


$(function () {
   $('#create_student_data_form #evd_school').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
 
    $(".caret").css('float', 'right');
    $(".caret").css('margin', '8px 0');
});


                
$(document).on("change", "#create_student_data_form #rec_network", function () {
$("#report_name").val('');
$('#report_name').prop('required', false);
$("#rec_report_name").hide();    
var contnr = $(this).parents('form').first();
var network_id = $('#rec_network').val();
var aDd = $("#provinces .province-list-dropdown");
//aDd.find("option").next().remove();
aDd.find("option").remove();

var report_type=$("#report_type").val();
 
var aDd_1 = $("#rec_schools .province-list-dropdown");
//aDd_1.find("option").next().remove();
aDd_1.find("option").remove();

if (network_id!='' && network_id !== null && network_id !== undefined) {
            var postData = "network_id=" + network_id + "&token=" + getToken();
             
         apiCall(this, "getProvincesInMultiNetwork", postData, function (s, data) {
               //alert(data.message);
                //aDd.find("option").remove();
                if (data.message != '') {
                    $('#errors').hide();
                    $('#provinces').show();
                    
                    if(report_type==11 || report_type==12){
                        
                    }else{
                    $('#rec_provinces').append($("<option/>", {
                    value: '',
                    text: 'Select Province/Centre'
                    }));
                }
                   
                    addOptions(aDd, data.message, 'province_id', 'province_name');
                    //aDd.append('<option value="all">ALL</option>');
                    $(contnr).find("#provinces").show();
                }
                
                if(report_type==11 || report_type==12){
                    //$("#rec_provinces").attr("width","500");
                    $('#rec_provinces').prop('multiple', true);
                    
                    $("#rec_provinces").multiselect('destroy');
                    $('#rec_provinces').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_network').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
                    
                    
                    $('#evd_school').prop('multiple', true);
                    //$("#evd_school").multiselect('false');
                    $("#evd_school").multiselect('destroy');
                    
                    $('#evd_school').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_provinces').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
                    
                    $(".caret").css('float', 'right');
                    $(".caret").css('margin', '8px 0');
                    
                }else{
                    
                    
                    
        
                    $('#evd_school').append($("<option/>", {
                    value: '',
                    text: 'Select School/ Batch'
                    }));
                    
                    $('#rec_provinces').prop('multiple', false);
                    
                    $("#rec_provinces").multiselect('destroy');
                    
                    $('#rec_provinces').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_network').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
                    
                    $('#evd_school').prop('multiple', false);
                    //$("#evd_school").multiselect('false');
                    $("#evd_school").multiselect('destroy');
                    
                    $('#evd_school').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_provinces').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
                    
                    $(".caret").css('float', 'right');
                    $(".caret").css('margin', '8px 0');
                    
                }
            });
            
            
}
    
});


$(document).on("change", "#create_student_data_form #rec_provinces", function () {
        
        var contnr = $(this).parents('form').first();    	
    	//var network_id = $(this).val();
    	var provience_ids = $('#rec_provinces').val();
        
        var report_type=$("#report_type").val();
        
        //alert(provience_ids);
        
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
                all_ids = all_ids.substring(0, all_ids.length-5);
        }
        
        if(all_ids.length>=1){
            provience_ids = all_ids;
           // var postData = "province_id="+all_ids+"&token=" + getToken();
        }
        
        //alert(provience_ids);
        
        
        //alert(all_ids.length);
        //alert(provience_ids);
        var totselected=0;
        var valData= provience_ids;
        if(valData!==null){
        var valNew=valData.toString().split(',');
        totselected=valNew.length;
        }
        //alert(totselected);
        
        if(totselected>1 && (report_type==11 || report_type==12)){
           $("#rec_report_name").show(); 
           $('#report_name').prop('required', true);
        }else{
           $("#report_name").val('');
           $('#report_name').prop('required', false);
           $("#rec_report_name").hide();
           
        }
        
          var aDd = $("#rec_schools .province-list-dropdown");
          aDd.find("option").remove();
          
          
          if(report_type==11 || report_type==12){
              
          }else{
                   $('#evd_school').append($("<option/>", {
                    value: '',
                    text: 'Select School/ Batch'
                    }));
          }
          
        
        if (provience_ids !== null && provience_ids !== undefined) {
            $('#errors').hide();
        //alert(provience_ids);
    	var postData = "province_id="+provience_ids+"&token=" + getToken();
        apiCall(contnr, "getSchoolsInProvinces", postData, function (s, data) {
            //alert(data.status);
          
            if (data.status) {
                
                addOptions(aDd, data.message, 'client_id', 'client_name');
                $("#rec_schools").show();
            }
        
            
            if(report_type==11 || report_type==12){
                    //$("#rec_provinces").attr("width","500");
                    $('#evd_school').prop('multiple', true);
                    
                    $("#evd_school").multiselect('destroy');
                    $('#evd_school').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_provinces').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
                    $(".caret").css('float', 'right');
                    $(".caret").css('margin', '8px 0');
                }else{
                    
                    $('#evd_school').prop('multiple', false);
                    
                    $("#evd_school").multiselect('destroy');
                    $('#evd_school').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_provinces').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
                    
                    $(".caret").css('float', 'right');
                    $(".caret").css('margin', '8px 0');
                }
            
            
        });
    }
        
    
});


$(document).on('submit',"#create_student_data_form",function(e){
    	postData = $(this).serialize() + "&token=" + getToken();
        //alert(postData);
    	 apiCall(this, "createStudentReport", postData,
                 function (s, data) {
    		 
                    showSuccessMsgInMsgBox(s, data);
                    
            //$(s).find("select").val('');
            //$(s).find("textarea").val('');
            $(s).find("input[type=text]").val('');
            
                    
                 }, showErrorMsgInMsgBox);
    	 return false;
});

$(document).on("change", "#create_student_data_form #report_type", function () {
    var report_type=$(this).val();
    //$("#rec_network")..multiselect;
    $("#rec_network").val("");
    $("#rec_network").multiselect('destroy');  
    
        $('#rec_network').multiselect({
            enableFiltering: false,
            includeSelectAllOption: true,
            buttonWidth: '420px',
            maxHeight: 210,
            templates: {
                ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               },
        });
        $(".caret").css('float', 'right');
        $(".caret").css('margin', '8px 0');
    $("#report_name").val('');
    $('#report_name').prop('required', false);
    $("#rec_report_name").hide();
    
    if(report_type=="11"){
       //$("#evd_school").attr("disabled","disabled");
       //$("#rec_provinces").removeAttr("disabled");
       $("#rec_schools .astric").hide();
       $("#provinces .astric").show();
        
        var aDd = $("#provinces .province-list-dropdown");
        aDd.find("option").remove();

        var aDd_1 = $("#rec_schools .province-list-dropdown");
        aDd_1.find("option").remove();
        
        $('#rec_provinces').prop('required', true);
        $('#evd_school').prop('required', false);
        
        $('#rec_provinces').prop('multiple', true);
                    
                    $("#rec_provinces").multiselect('destroy');
                    $('#rec_provinces').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_network').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
                    
                    
                    $('#evd_school').prop('multiple', true);
                    //$("#evd_school").multiselect('false');
                    $("#evd_school").multiselect('destroy');
                    
                    $('#evd_school').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_provinces').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
                    
                    $(".caret").css('float', 'right');
                    $(".caret").css('margin', '8px 0');
        
       
    }else if(report_type=="12"){
        //$("#evd_school").attr("disabled","disabled");
        //$("#rec_provinces").attr("disabled","disabled");
        $("#rec_schools .astric").hide();
        $("#provinces .astric").hide();
        
        var aDd = $("#provinces .province-list-dropdown");
        aDd.find("option").remove();

        var aDd_1 = $("#rec_schools .province-list-dropdown");
        aDd_1.find("option").remove();
        
        $('#rec_provinces').prop('required', false);
        $('#evd_school').prop('required', false);
        
        $('#rec_provinces').prop('multiple', true);
                    
                    $("#rec_provinces").multiselect('destroy');
                    $('#rec_provinces').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_network').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
                    
                    
                    $('#evd_school').prop('multiple', true);
                    //$("#evd_school").multiselect('false');
                    $("#evd_school").multiselect('destroy');
                    
                    $('#evd_school').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_provinces').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
        
                   $(".caret").css('float', 'right');
                   $(".caret").css('margin', '8px 0');
        
    }else{
        $("#evd_school").removeAttr("disabled");
        $("#rec_provinces").removeAttr("disabled");
        
        var aDd = $("#provinces .province-list-dropdown");
        aDd.find("option").remove();
        var aDd_1 = $("#rec_schools .province-list-dropdown");
        aDd_1.find("option").remove();
        
        $('#rec_provinces').prop('required', true);
        $('#evd_school').prop('required', true);
        
        $('#rec_provinces').append($("<option/>", {
        value: '',
        text: 'Select Province/Centre'
        }));
        
        $('#evd_school').append($("<option/>", {
        value: '',
        text: 'Select School/ Batch'
        }));
        
        
                    $('#rec_provinces').prop('multiple', false);
                    
                    $("#rec_provinces").multiselect('destroy');
                    $('#rec_provinces').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_network').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
                    
                    
                    $('#evd_school').prop('multiple', false);
                    //$("#evd_school").multiselect('false');
                    $("#evd_school").multiselect('destroy');
                    
                    $('#evd_school').multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    buttonWidth: '420px',
                    maxHeight: ($(window).height()-($('#rec_provinces').offset().top+110)),
                    templates: {
                        filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                        ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
                     },
                    });
        
                     $(".caret").css('float', 'right');
                     $(".caret").css('margin', '8px 0');
                     
        $("#rec_schools .astric").show();
        $("#provinces .astric").show();
    }
    
    
});

$(document).ready(function () {
/*$(".filterByAjax [name=report_id]").hide();
$(".filterByAjax [name=network_id]").hide();
$(".filterByAjax [name=province_id]").hide();
$(".filterByAjax [name=client_id]").hide();
$(".filterByAjax [name=aqs_round]").hide();
*/
if($(".filterByAjax [name=assessment_type_id]").val()==4){

$(".filterByAjax [name=report_id]").show();
$(".filterByAjax [name=network_id]").show();
$(".filterByAjax [name=province_id]").show();
$(".filterByAjax [name=client_id]").show();
$(".filterByAjax [name=round_id]").show();

}else{
  
$(".filterByAjax [name=report_id]").hide();
$(".filterByAjax [name=network_id]").hide();
$(".filterByAjax [name=province_id]").hide();
$(".filterByAjax [name=client_id]").hide();
$(".filterByAjax [name=round_id]").hide();

}    

$(document).on("change", ".filterByAjax [name=assessment_type_id]", function () {

if($(this).val()==4){

$(".filterByAjax [name=report_id]").show();
$(".filterByAjax [name=network_id]").show();
$(".filterByAjax [name=province_id]").show();
$(".filterByAjax [name=client_id]").show();
$(".filterByAjax [name=round_id]").show();

}else{
  
$(".filterByAjax [name=report_id]").hide();
$(".filterByAjax [name=network_id]").hide();
$(".filterByAjax [name=province_id]").hide();
$(".filterByAjax [name=client_id]").hide();
$(".filterByAjax [name=round_id]").hide();

}    
    
});

});


$(document).on("change", ".filterByAjax [name=province_id]", function () {
        var ProvinceId = $(this).parents('form').find('[name=province_id]').val();        
        var aDd2 = $(this).parents('form').find('[name=client_id]');
        if (ProvinceId > 0) {
            apiCall(this, "getSchoolsInProvinces", {"token": getToken(), "province_id": ProvinceId}, function (s, data) {                
                aDd2.find("option").next().remove();
                addOptions(aDd2, data.message, 'client_id', 'client_name');
            }, showErrorMsgInMsgBox);
        }
        return false;
    });
    
$(document).on("change", ".filterByAjax  [name=report_id]", function () {
    var report_type=$(this).val();
    if(report_type=="11"){
       $(".filterByAjax  [name=client_id]").attr("disabled","disabled");
       $(".filterByAjax  [name=province_id]").removeAttr("disabled");
       //$("#rec_schools .astric").hide();
       //$("#provinces .astric").show();
    }else if(report_type=="12"){
        $(".filterByAjax  [name=client_id]").attr("disabled","disabled");
        $(".filterByAjax  [name=province_id]").attr("disabled","disabled");
        //$("#rec_schools .astric").hide();
        //$("#provinces .astric").hide();
    }else{
        $(".filterByAjax  [name=client_id]").removeAttr("disabled");
        $(".filterByAjax  [name=province_id]").removeAttr("disabled");
        //$("#rec_schools .astric").show();
        //$("#provinces .astric").show();
    }
    
    
});    