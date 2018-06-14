 $(document).ready(function () {  
$(function () {
        $(".team_kpa_id").multiselect('destroy');
        $('.team_kpa_id').multiselect({
         
            includeSelectAllOption: true,            
            buttonWidth: '220px',
            numberDisplayed: 1,
            maxHeight: 150,
           
            templates: {
                 ul: '<ul class="multiselect-container dropdown-menu" style="width:420px;"></ul>',
               } ,onDropdownHide: function (element, checked) {

                    toggleKpas();
                }
        });
});
toggleKpas();
 });

function toggleKpas() {
    allKpaValues = [];
                $(" #edit-review-kpa .team_kpa_id ").each(function () { 
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
               
                    //if($(this).attr("id") != selectedKpaId) {
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
                        
                    //}
                                           
                });
                 $(".team_kpa_id").multiselect('destroy');
                $('.team_kpa_id').multiselect({
                   includeSelectAllOption: true,
        
                   numberDisplayed: 1,
                   buttonWidth: '220px',
                   maxHeight: 150,

                   templates: {
                       filter: '<li class="multiselect-item filter"><div class="input-group"><input class="form-control multiselect-search" type="text"></div></li>',
                      },onDropdownHide: function (element, checked) {

                               //getAllKpas();
                              // alert("ok");
                               toggleKpas();
                              

                            }
                  });
}
