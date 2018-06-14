$(document).ready(function(){
	//add selected class to language selected
	//getLangSelected();
        //alert("okddd")
	$(document).on('change',"#lang",function(){
		var lang = $(this).val();
               // alert(lang);
		var d = new Date();
	    d.setTime(d.getTime() + (90*24*60*60*1000));
	    var expires = "expires="+ d.toUTCString();
	    document.cookie = "ADH_LANGCODE=" + lang + ";" + expires + "";
	    window.location.reload();
	});
        
        $(document).on('change',"#lang-n",function(){
		var lang = $(this).val();
               // alert(lang);
		url=$(this).data("url");
                //alert(url);
                url += "&lang_id="+lang+""; 
	    window.location.href=url;
            //window.reload(url);
	});
        
        $(document).on('change',"#lang-d",function(){
		var lang = $(this).val();
		var url = $("#lang-d option:selected").attr("id");
                //alert(lang);
                //alert(url);
                //if(lang!='all')
                //url += "&lang_id="+lang+"";
                $("#lang_id").val(lang);
                $(".filters-bar").submit();
	        //window.location.href=url;
            //window.reload(url);
	});
});
function getLangSelected(){
	var chkLangCk = getCookie("ADH_LANGCODE");
	var chkLang = chkLangCk?chkLangCk:'en';
	$('.langSel').removeClass('selected');
		 $('.langSel').each(function(i,k){
			 if($(k).data('lang')==chkLang)
				 $(k).addClass('selected');			 			 
		});
}
