jQuery(document).ready(function($) {	
	var questions = [];	
	var grainArray = new Array(13,23,24,25,27);
	var stDate=null;
	var edDate=null;
	// Type Head (Suggestion Box)           
    var substringMatcher = function(strs) {
    return function findMatches(q, cb) {
    var matches, substringRegex;
      matches = [];
      substrRegex = new RegExp(q, 'i');
      $.each(strs, function(i, str) {
        if (substrRegex.test(str)) {
          matches.push(str);
        }
      });
      cb(matches);
    };
  };  
  $(document).on("click",".questionsList",function(){
		var id=$(this).data('id');	
		var sNo = $(this).data('parent-sno');
		var frm = $(this).closest('.filterByAjax').data('frm');
		var type = $(this).closest('.filterByAjax').data('type');
		//if($(this).parents().find("#"+frm+" .filterOperator").val())
		var cont = $(this).parents().find('.currentSelection.sno_'+sNo);
		var currOperator = $("#"+frm).find(".filterOperator#operator_id_"+sNo).val();		
		if(currOperator==8)
			$("#"+frm).find('.mulFilterValue#attr_val_list_'+sNo).find("option[value="+id+"]").prop("selected", "selected");
		else{
			$("#"+frm+" #attr_val_"+sNo).val(id);
			cont.html('');
			$('.questionsList').removeClass('selected');
		}
		
			$("#"+frm).find('.mulFilterValue#attr_val_list_'+sNo).multiselect('refresh');				
			
		if(cont.find(".questionNode-"+id).length)
			return;
		var name=$(this).addClass('selected').find(".questionName").text();		
		cont.append('<div title="'+name+'" class="questionNode clearfix questionNode-'+id+'" data-id="'+id+'">'+name+'<input type="hidden" class="ajaxFilterAttach" name="question['+type+'][]" value="'+id+'"/><span class="delete"><i class="fa fa-times"></i></span></div>').find(".empty").addClass('notEmpty');
		checkAttrForSubAttr(frm,sNo,type,'kpa');
	});
 
  $(document).on("click",".questionNode .delete",function(){
		var id=$(this).parents(".questionNode").data("id");
		var p=$(this).parents(".tag_boxes").first();
		var frm = $(this).closest('.filterByAjax').data('frm');
		var sNo = $("#id-question-"+id).data('parent-sno');
		var cont = $(this).parents().find('.currentSelection.sno_'+sNo);
		$(".questionNode-"+id).remove();
		$("#id-question-"+id).removeClass('selected');
		
		var currOperator = $("#"+frm).find(".filterOperator#operator_id_"+sNo).val();		
		if(currOperator==8)
			$("#"+frm).find('.mulFilterValue#attr_val_list_'+sNo).find("option[value="+id+"]").prop("selected",false);
		else{
			$("#"+frm+" #attr_val_"+sNo).val(id);
			cont.html('');
			$('.questionsList').removeClass('selected');
		}
		$("#"+frm).find('.mulFilterValue#attr_val_list_'+sNo).multiselect('refresh');	
		if(p.find(".questionNode").length==0){
			p.find(".empty").removeClass("notEmpty");
		}
		var trgr=p.data('trigger');
		if(trgr!=undefined && trgr!=null && trgr!=""){
			$("body").trigger(trgr);
		}
	});
  $(document).on('focus','#create_filter_form,#edit_filter_form',function(){
	  var frm = "#"+$(this).attr('id');
	  $(frm).find('.fdate,.sdate').datetimepicker({format: 'YYYY-MM-DD', maxDate: new Date, pickTime: false}).off('focus')
	  	.click(function () {
	      $(this).data("DateTimePicker").show();
	  });
	  $(frm).find(".fdate").on("dp.change", function (e) {
          $(frm).find('.sdate').data("DateTimePicker").setMinDate(e.date);
      });
      $(frm).find(".sdate").on("dp.change", function (e) {
    	  $(frm).find('.fdate').data("DateTimePicker").setMaxDate(e.date);
      });
      if(frm=="#edit_filter_form" && isValidText($(frm).find(".fdate").val()) && isValidText($(frm).find(".sdate").val()) )
      {
    	  $(frm).find('.sdate').data("DateTimePicker").setMinDate( $(frm).find(".fdate").val());
    	  $(frm).find('.fdate').data("DateTimePicker").setMaxDate($(frm).find('.sdate').val());
      }
  });
	$(document).on('click',"#self_review_only",function(){
		if($(this).is(':checked')){
			$(document).find('#validatedRow').show();
			$(document).find('#validatedRow input').prop('required',true);
		}	
		else{
			$(document).find('#validatedRow').hide();
			$(document).find('#validatedRow input').prop('required',false);
		}
	});
	$(document).on("change","#create_network_drop",function(){		
		if($(this).val()==0)			
				$("#id_network_name").show();			
		else{
			$("#id_network_name").hide();
			postData=$(this).serialize()+"&token="+getToken();
			apiCall(this,"getNetworkReportData",postData,function(s,data){
					//showSuccessMsgInMsgBox(s,data);
				//console.log(data)
				var frm = "#create_network_report_form";
				$(frm).find(".expRow :gt(0)").remove();
				//$(frm).find('#filter_name').val(data.data.network_report_id);
				//var selectize = $filterSelect[1].selectize;
				//selectize.setValue(data.data.network_report_id);
				var frm = "#create_network_report_form";
				//$(frm).parents(".customTbl").first().find(".expRow")
				$("#schoolFltrSection").show();
				$(frm).find("#applyFilter").trigger('click');			
				
				},showErrorMsgInMsgBox);
		}	
			
	});
	/*$(document).on("keydown",".selectize-control input[type='text']",function(event){
		//alert('hi')
		 var x = event.which || event.keyCode;
		 if(x!=8)
			 event.preventDefault();
	});	*/
	$(document).on("change","#create-filter-drop",function(event){				
	//console.log( $(".selectize-control input[type='text']").val());		
		if($(this).val()==0) //&& (event.keyCode!=8)
			{
				$("#create-filter-pop").trigger('click');
				$("#editFilter,#applyFilter").hide();
				$("#schoolFltrSection").hide();
			}
		else{
			$("#editFilter,#applyFilter").show();
			
		}
			
	});
	
	
	$(document).on('click','#editFilter',function(){
		var href = $("#edit-filter-pop").attr('href');
		$("#edit-filter-pop").attr('href',href+"&fid="+$('#create-filter-drop').val()).trigger('click');		
	});
	$(document).on("click",".filterRowAdd",function(){		
		var sn = $(this).parents(".addBtnWrap").first().find(".filter_row").length;
		apiCall(this,"addFilterRow","sn="+($(this).parents(".addBtnWrap").first().find(".filter_row").length+1)+"&isDashboard="+$(this).parents('form').first().find(".isDashboard").val()+"&token="+getToken(),function(s,data){
			if($(s).parents(".addBtnWrap").first().find(".subrow").last().data('sno')==sn)
				$(s).parents(".addBtnWrap").first().find(".subrow").last().after(data.content)
			else
				$(s).parents(".addBtnWrap").first().find(".filter_row").last().after(data.content);			
		},function(s,msg){ alert(msg); });
	});
	$(document).on("click",".fltdAddRow.exp",function(){		
		apiCall(this,"addNetworkExpRow","sn="+($(this).parents(".addBtnWrap").first().find(".expRow").length+1)+"&token="+getToken(),function(s,data){
			$(s).parents(".addBtnWrap").first().find(".expRow").last().after(data.content)			
		},function(s,msg){ alert(msg); });
	});
	$(document).on("click",".expRow .delete_row",function(){
		if($(this).parents(".customTbl").first().find(".expRow").length>1){
			var p=$(this).parents(".customTbl");
			$(this).parents(".expRow").remove();			
			p.first().find(".s_no").each(function(i,v){;$(v).html(i+1)});
			var trgr=p.data('trigger');
			if(trgr!=undefined && trgr!=null && trgr!=""){
				$("body").trigger(trgr);
			}
		}else
			alert("You can't delete all the rows");
		return false;
	});
	$(document).on("click",".filter_row .delete_row",function(){
		if($(this).parents(".customTbl").first().find(".filter_row").length>1){
			var p=$(this).parents(".customTbl");
			$(this).parents(".filter_row").remove();			
			p.first().find(".s_no").each(function(i,v){;$(v).html(i+1);
			$(this).closest(".filter_row").find('.mulFilterValue').attr('name','mul_attr_val_'+i+'[]');
			});
			var trgr=p.data('trigger');
			if(trgr!=undefined && trgr!=null && trgr!=""){
				$("body").trigger(trgr);
			}
		}else
			alert("You can't delete all the rows");
		return false;
	});
	$(document).on("change",".required",function(){
		$(this).closest('td').find('.multiselect').removeClass('error');
	});
	$(document).on("submit","#create_filter_form,#edit_filter_form",function(){
		var frm = $(this).find('form').attr('id');
		var isDashboard =$(frm).find("#fisdashboard").val();
		var error = 0;
				if(!isValidText($(this).find("#filter_id").val())){
					$(this).find("#filter_id").focus();
					$(this).find("#filter_id").val('');
					alert("Please fill filter name.");	
					error++;	
					return false;
				}
				$(frm).find('.required').each(function(){ 
					//console.log($(this).val())
					if($(this).val()===null||$(this).val()==''){					
						$(this).closest('td').find('.multiselect').addClass('error');
						error++;
					}
				});
				
				if(error>0){
					alert("Please select values.");
					return false;
				}		
		var postData=$(this).serialize()+"&token="+getToken();
		apiCall(this,"saveFilter",postData,function(s,data){
				showSuccessMsgInMsgBox(s,data);
			$(s).find('select').filter(function() {
		        return this.id.match(/id(?!_1)/)||this.id.match(/id(?!_1)/)||this.id.match(/list(?=_)/);
		    }).val();		
				if(isDashboard){//on dashboard all the fields have to be rest
					$(s).find('select').val('');
					$(s).find(".filter_row").find(".filterValue[id=attr_val_1]").find("option").next().remove();
				}
				else{// on network report first parameter not to be reset
						$(s).find('select').filter(function() {
					        return this.id.match(/id(?!_1)/)||this.id.match(/list(?=_)/)||this.id.match(/val(?!_1)/);
					    }).val('');
					}
				$(s).find("input[type='text']").val('');
				var aDd = $(s).find(".filter_row").find(".filterOperator");
				var aDd2 = $(s).find(".filter_row").find(".filterValue[id!=attr_val_1]");
				var aDd3 = $(s).find(".filter_row").find(".mulFilterValue");
				var aDd4 = $(s).find(".filter_row").find(".filterAttr");
				aDd.find("option").next().remove();
				aDd2.find("option").next().remove();
				aDd3.find("option").remove();				
				$(s).find('.mulFilterValue').multiselect('destroy');
			//	$(s).find('.mulFilterValue').multiselect('refresh');
				$(s).find('.mulFilterValue').multiselect({  
		        	numberDisplayed : 1
		          }); 
				//$("#fId").val('');
				if($("#fId").length)
					aDd4.find("option").next().remove();
				filterByAjax($(".filter-list"));
				filterByAjax($(".filter-list-sort"));
			},showErrorMsgInMsgBox);
		return false;
	});
	$(document).on("submit","#edit_network_report_form",function(){
		var postData=$(this).serialize()+"&token="+getToken();
		apiCall(this,"updateNetworkReport",postData,function(s,data){
			showSuccessMsgInMsgBox(s,data);			
			$(s).find("#view-network-pdf").show();	
			$(s).find("#view-ds-pdf").show();	
			href = "?controller=customreport&action=generateNetworkReportPDF";
			$("#view-network-pdf").attr('href',href+"&network_report_id="+data.network_report_id);
			href = "?controller=customreport&action=generateDataSummary";
			$("#view-ds-pdf").attr('href',href+"&network_report_id="+data.network_report_id);
		},showErrorMsgInMsgBox);		
		return false;
	});
	$(document).on("submit","#create_network_report_form",function(){
		var postData=$(this).serialize()+"&token="+getToken();
		var error=0;
		var href;
		$("#create_network_report_form").find("input[required]").each(function(){
			if(!isValidText($(this).val())){
					$(this).focus();
					$(this).val('');
					alert("Please fill this field.");	
					error++;
					return false;
			}
			
		});

		if(!isValidText($("#create-filter-drop").val()) || $("#create-filter-drop").val()<1){
			alert("Please select filter.");
			error++;
			return false;
		}
		if(!isValidText($("#id_clients").val())) //$("#id_clients").val(ct);
			{
			alert("Please select schools.");
			error++;
			return false;
			}
		
		if(error==0)
		apiCall(this,"saveNetworkReport",postData,function(s,data){
			showSuccessMsgInMsgBox(s,data);
			/*$(s).find("select").val('');
			$(s).find("input[type='text']").val('');	*/
			$(s).find("#view-network-pdf").show();
			$(s).find("#view-ds-pdf").show();
			//$(s).find("#sortableR").html('');
			href = "?controller=customreport&action=generateNetworkReportPDF";
			$("#view-network-pdf").attr('href',href+"&network_report_id="+data.network_report_id);	
			href = "?controller=customreport&action=generateDataSummary";
			$("#view-ds-pdf").attr('href',href+"&network_report_id="+data.network_report_id);	
		
		},showErrorMsgInMsgBox);		
		return false;
	});
	$(document).on("click","#showButton",function(){
		
		var error=0;
		var frm = "#admin_dashboard_frm";
		var href;		
		/*if(!isValidText($("#create-filter-drop").val()) || $("#create-filter-drop").val()<1){
			alert("Please select filter.");
			error++;
			return false;
		}*/
		if($(frm).find("#sortableRrow li").length<1 || $(frm).find("#sortableRcol li").length<1){
			alert("Select Rows and columns");
			return false;
		}
		if($(frm).find("#sortableRrow li").length>1 || $(frm).find("#sortableRcol li").length>1){
			alert("Please select exactly one variable in row and column");
			return false;
		}
		if($(frm).find("#sortableRrow li").data('id')==26 && !($.inArray(+$(frm).find("#sortableRcol li").data('id'),grainArray)>=0))//rating
		{
			alert("You can select KPA/Key Question/Sub Question/Judgement Statement only with Rating");
			return false;
		}
		else if($(frm).find("#sortableRcol li").data('id')==26 && !($.inArray(+$(frm).find("#sortableRrow li").data('id'),grainArray)>=0))//rating
		{
			alert("You can select KPA/Key Question/Sub Question/Judgement Statement only with Rating");
			return false;
		}
		var rows = $(frm).find("#sortableRrow li").data('id');
		var cols = $(frm).find("#sortableRcol li").data('id');
		var filter_name = $(frm).find("#create-filter-drop").val();
		var count_criteria = $(frm).find("input[name='count_criteria']:checked").val();
		var postData=$(frm).serialize()+"&token="+getToken()+"&rows="+rows+"&cols="+cols+"&count_criteria="+count_criteria+"&filter_name="+filter_name;
		if(error==0)
		apiCall(this,"applyFilterGenerateAdminData",postData,function(s,data){
			showSuccessMsgInMsgBox(frm,data);
			//alert(data.query)
			//console.log(data.query);
			//console.log(data.procedure);
				if(!(data.data.length>0)){
					alert("No results found.");
					localStorage.removeItem("admindata");
					localStorage.removeItem("adminfilterstr");
					localStorage.removeItem("basis");
					$(document).trigger('emptyData');
					return false;
				}
				console.log(data);
				$(document).trigger('filledData');				
				localStorage.setItem("admindata", JSON.stringify(data.data));
				localStorage.setItem("basis", (data.basis));
				localStorage.setItem("adminfilterstr", (data.filterString));
				$(frm).find("#showdata")[0].click();				
				//function(key, value) { return value === "" ? "" : value }
		},showErrorMsgInMsgBox);		
		return false;
	});	
	$(document).on('emptyData',function(){
		$("#showButton").addClass('disabled');
	});
	$(document).on('filledData',function(){
		$("#showButton").removeClass('disabled');
	});
	$(document).on("click","#school_dashboard_frm #applyFilter",function(){
		var frm = $("#school_dashboard_frm");
		var postData=$(frm).serialize()+"&token="+getToken();			
		apiCall(this,"getgraphData",postData,function(s,msg){	
			if(msg.data.length){
				loadGraph(sessionStorage.getItem("scheme1Award"),sessionStorage.getItem("scheme2Award"),msg.data);				
			}			
			else{
				alert("There is not enough data for this filter");
				//$("#awardGraph").html(' ');
			}
				
			},showErrorMsgInMsgBox);
		return false;
	});
	$(document).on("click","#admin_dashboard_frm #applyFilterHidden",function(){
		var frm = $("#admin_dashboard_frm");
		var postData=$(frm).serialize()+"&token="+getToken();	
		var boxHeight = 0;
		apiCall(this,"getvarList",postData,function(s,data){
			var leftBox = frm.find("#sortableL");
			leftBox.html('');
			if(data.vars.length){
				//console.log(data.vars);				
				for(var i in data.vars){
					leftBox.append('<li class="vtip" data-id="'+data.vars[i].filter_attr_id+'" title="'+data.vars[i].filter_attr_name+'"><span class="vtip" title="'+data.vars[i].filter_attr_name+'">'+data.vars[i].filter_attr_name+'</span></li>');
					questions.push(data.vars[i].filter_attr_name);
				}				
				boxHeight = $(frm).find('.leftQuestHldr .vertScrollArea').height();
			/*	$(frm).find('.rightConfirmedQueryBox .vertScrollArea').height(boxHeight/3);
				$(frm).find('.leftQuestHldr .vertScrollArea').height(boxHeight);
				$(frm).find('.connectedSortable.ui-sortable').css('min-height',boxHeight/3+'px');
				$(frm).find('.sortInfoIcon').height($(frm).find('.leftQuestHldr').height());	*/			
			}			
			else{				
				alert("There are no variables");	
			}
				
			},showErrorMsgInMsgBox);
		return false;
	});

	$(document).on("click","#create_network_report_form #applyFilter",function(){
		var frm = $("#create_network_report_form");
		var postData=$(frm).serialize()+"&token="+getToken();
		var boxHeight = 0;
		
		apiCall(frm,"getFilterSchools",postData,function(s,data){
				//console.log(data);
			var leftBox = frm.find("#sortableL");
			leftBox.html('');
			frm.find("#sortableR").html('');
				//showSuccessMsgInMsgBox(s,data);				
				if(data.schools.length>0){		
					$(frm).find("#num-filtered-schools").html(' ('+data.schools.length+')');
					$(frm).find("#num-selected-schools").html(' (0)');
					for(var i in data.schools){
						leftBox.append('<li class="vtip" data-id="'+data.schools[i].client_id+'" data-aqs-start="'+data.schools[i].start_date+'" data-aqs-end="'+data.schools[i].end_date+'" title="'+data.schools[i].client_name+'"><span class="vtip" title="'+data.schools[i].client_name+'">'+data.schools[i].client_name+'</span></li>');
						questions.push(data.schools[i].client_name);
					}
					$("#schoolFltrSection").show();
					boxHeight = $(frm).find('.leftQuestHldr .vertScrollArea').height();
					$(frm).find('.rightConfirmedQueryBox .vertScrollArea').height(boxHeight);
					$(frm).find('.leftQuestHldr .vertScrollArea').height(boxHeight);
					$(frm).find('.connectedSortable.ui-sortable').css('min-height',boxHeight+'px');
					$(frm).find('.sortInfoIcon').height($(frm).find('.leftQuestHldr').height());
					//console.log($('#create_network_report_form').find('#fltrSchools.vertScrollArea').height())
					$(document).trigger('checkEmptyClients');
				}
				else{
					alert("No results found!");
					$("#schoolFltrSection").hide();
				}
			},showErrorMsgInMsgBox);
		return false;
	});
	$(document).on('change','.filterValue',function(){
		var currFltr = $(this);
		var frm = $(this).parents('form').attr('id');
		var sNo = currFltr.closest(".filter_row").find('.s_no').html();
		var attrVal = currFltr.closest(".filter_row").find('.filterAttr').val();
		var id = currFltr.val();
		var name=currFltr.text();
		var cont = currFltr.closest(".filter_row").find('.currentSelection.sno_'+sNo);
		if($.inArray(+attrVal,grainArray)>=0){					
			cont.append('<div title="'+name+'" class="questionNode clearfix questionNode-'+id+'" data-id="'+id+'">'+name+'<input type="hidden" class="ajaxFilterAttach" name="question['+attrVal+'][]" value="'+id+'"/><span class="delete"><i class="fa fa-times"></i></span></div>').find(".empty").addClass('notEmpty');
		}
	});
	$(document).on('change','.mulFilterValue',function(){
		var currFltr = $(this);
		var frm = $(this).parents('form').attr('id');
		var sNo = currFltr.closest(".filter_row").find('.s_no').html();
		var attrVal = currFltr.closest(".filter_row").find('.filterAttr').val();
		var cont = currFltr.closest(".filter_row").find('.currentSelection.sno_'+sNo);
		var arr = $(this).find('option:selected');
		var id =''; var name='';
		var i =0; 
		if($.inArray(+attrVal,grainArray)>=0){	
			cont.html('');
			for(i=0;i<arr.length;i++){
				id = arr[i].value;
				name = arr[i].text;
				cont.append('<div title="'+name+'" class="questionNode clearfix questionNode-'+id+'" data-id="'+id+'">'+name+'<input type="hidden" class="ajaxFilterAttach" name="question['+attrVal+'][]" value="'+id+'"/><span class="delete"><i class="fa fa-times"></i></span></div>').find(".empty").addClass('notEmpty');
			}
		}
	});
	$(document).on('change','.filterAttr',function(){
		//$(this).closest(".filter_row").find("filterOperator");
		var currFltr = $(this);
		var frm = $(this).parents('form').attr('id');
		var attrVal = $(this).val();
		var attrText = $(this).find('option:selected').text();
		var csFound = 0;
		var postData = '';
		var currIndex = currFltr.closest(".filter_row").find('.s_no').html();		
		//remove subrow if any
	//	currFltr.closest(".filter_row").next(".subrow").remove();
		//if state is selected make sure that country is selected else load states for country-india
		//if(attrVal==3 && !($(this).closest(".table").find(".filter_row .filterAttr").val()=='10' && $(this).closest(".table").find(".filter_row .filterAttr[id$='_id_10']").closest(".filter_row").find(".val_td [required]").val()>0))
		if($.inArray(+attrVal,grainArray)>=0)
		{			
			postData="attr_id="+attrVal+"&token="+getToken()+"&label="+attrText+"&sno="+currIndex+"&frm="+frm;
			/*apiCall(this,"getDiagnosticGrainHtmlRow",postData,function(s,data){
				showSuccessMsgInMsgBox(s,data);							
				currFltr.closest(".filter_row").after(data.content);				
			});*/
			apiCall(this,"getSelectionLink",postData,function(s,data){
				showSuccessMsgInMsgBox(s,data);	
				if(currFltr.closest(".filter_row").find(".val_td .currentSelection").length)
					currFltr.closest(".filter_row").find(".val_td .currentSelection").html(data.content);
				else
					currFltr.closest(".filter_row").find(".val_td").append(data.content)
			});
		}
		else if(attrVal==3) 	
		{				
			$(this).closest(".table").find(".filter_row .filterAttr").each(function(){
				if($(this).val()==10 && $(this).closest(".filter_row").find(".val_td [required]").val()>0)
					csFound=$(this).closest(".filter_row").find(".val_td [required]").val();
			});
			if(csFound==0){				
				alert("Please select a country to add a state.");
				return false;
			}
		}
		else if(attrVal==11) 	
		{				
			$(this).closest(".table").find(".filter_row .filterAttr").each(function(){
				if($(this).val()==3 && $(this).closest(".filter_row").find(".val_td [required]").val()>0)
					csFound=$(this).closest(".filter_row").find(".val_td [required]").val();
			});
			if(csFound==0){				
				alert("Please select a state to add a city.");
				return false;
			}
		}
                else if(attrVal==12)//network must be loaded first to add a province 	
		{				
			$(this).closest(".table").find(".filter_row .filterAttr").each(function(){
				if($(this).val()==7 && $(this).closest(".filter_row").find(".val_td [required]").val()>0)
					csFound=$(this).closest(".filter_row").find(".val_td [required]").val();
			});
			if(csFound==0){				
				alert("Please select a network to load province.");
				return false;
			}
		}
		var aDd = $(this).closest(".filter_row").find(".filterOperator");
		var aDd2 = $(this).closest(".filter_row").find(".filterValue");
		var aDd3 = $(this).closest(".filter_row").find(".mulFilterValue");
		var aDd4 = $(this).closest(".filter_row").find(".between");
		if(!isValidText(attrVal)){
			aDd.find("option").next().remove();
			aDd2.find("option").next().remove();
			aDd3.find("option").remove();
			aDd4.find("option").next().remove();
			$('.mulFilterValue').multiselect('destroy');
			//$('.mulFilterValue').multiselect('refresh');
			$('.mulFilterValue').multiselect({  
	        	numberDisplayed : 1
	          }); 
			return false;
		}
		// if attribute is date of review no need to load values;show calendar instead				
		//var textVal = $(this).closest(".filter_row").find(".filterValueText");
		postData="attr_id="+attrVal+"&csId="+csFound+"&token="+getToken();
		apiCall(this,"loadOperatorsAndValues",postData,function(s,data){
				showSuccessMsgInMsgBox(s,data);			
				aDd.find("option").next().remove();
				aDd2.find("option").next().remove();
				aDd3.find("option").remove();
				aDd4.find("option").next().remove();
				addOptions(aDd,data.operators,'operator_id','operator_text');	
				if(data.values!='na')
				{
					if(data.values.length>0){
						addOptions(aDd2,data.values,Object.keys(data.values[0])[0],Object.keys(data.values[0])[1]);
						addOptions(aDd3,data.values,Object.keys(data.values[0])[0],Object.keys(data.values[0])[1]);
						addOptions(aDd4,data.values,Object.keys(data.values[0])[0],Object.keys(data.values[0])[1]);
						$('.mulFilterValue').multiselect('destroy');
						//$('.mulFilterValue').multiselect('refresh');
						$('.mulFilterValue').multiselect({  
							numberDisplayed : 1
						  }); 
						currFltr.closest(".filter_row").find(".between").prop('required',false).css({'display':'none'}).val('');
						currFltr.closest(".filter_row").find(".mulFilter-row").removeClass('required').css({'display':'none'}).val('');
						currFltr.closest(".filter_row").find(".filterValue").prop('required',true).css({'display':'block'});
					} else {
						alert('No Data Found!');
					}					
				}
				if(data.values=='na'){
					currFltr.closest(".filter_row").find(".between").prop('required',false).css({'display':'none'}).val('');
					currFltr.closest(".filter_row").find(".mulFilter-row").addClass('required').css({'display':'none'}).val('');
					currFltr.closest(".filter_row").find(".filterValueText").prop('required',true).css({'display':'block'});
				}
				
				if(attrVal==18)
				{
					postData="attr_id="+attrVal+"&csId="+csFound+"&token="+getToken();
					apiCall(this,"getDateFields",postData,function(s,data){
						showSuccessMsgInMsgBox(s,data);			
						
						currFltr.closest(".filter_row").find(".val_td").append(data.content);
						currFltr.closest(".filter_row").find(".between").prop('required',false).css({'display':'none'}).val('');
						currFltr.closest(".filter_row").find(".mulFilter-row").removeClass('required').css({'display':'none'}).val('');
						currFltr.closest(".filter_row").find(".filterValue").prop('required',false).css({'display':'none'}).val('');
					});
					
				}
				else
					currFltr.closest(".filter_row").find(".val_td .fdate, .val_td .sdate").remove();
					
			},showErrorMsgInMsgBox);
		return false;
	});
	$(document).on('change','.subAttr',function(){		
		var currSubFltr = $(this);
		var frm = $(this).parents('form').attr('id');					
		var attrVal = $(this).val();
		var thisRow = currSubFltr.closest(".subrow");
		var sno = thisRow.data('sno');
		var cardinality = $('#'+frm).find('.currentSelection.sno_'+sno+' .questionNode').length;
		var aDd = thisRow.find(".subOperator");
		var aDd2 = thisRow.find(".subCardinality");
		var aDd3 = thisRow.find(".subCriteria");
		var maxCardinalityFld = thisRow.find(".maxcardinality");
		postData="attr_id="+attrVal+"&token="+getToken()+"&cardinality="+cardinality;
		apiCall(this,"loadSubOperatorsAndValues",postData,function(s,data){
				showSuccessMsgInMsgBox(s,data);			
				aDd.find("option").next().remove();
				aDd2.find("option").next().remove();
				aDd3.find("option").next().remove();				
				addOptions(aDd,data.operators,'operator_id','operator_text');
				addOptions(aDd2,data.cardinality,'id','text');
				addOptions(aDd3,data.criteria,'rating_id','rating');	
				maxCardinalityFld.val(data.maxCardinality);
			},showErrorMsgInMsgBox);
		return false;
	});
	
	$(document).on('change','.filterOperator',function(){
		
		var currFltr = $(this);
		var currFltrAttr = currFltr.closest(".filter_row").find(".filterAttr").val();
		var currOperator = currFltr.val();
		if(currFltrAttr==18)
			return;
		var currIndex = currFltr.closest(".filter_row").find('.s_no').html();
		//on change of operator remove current selection tag boxes - kpa,kq,sq,js
		currFltr.closest(".filter_row").find(".currentSelection").html('');
		
		//if = operator is chosen twice for the same attribute, show message and disallow it
		currFltr.closest(".customTbl").find('.filter_row .filterAttr').each(function(i){			
			if(i!=(currIndex-1) && $(this).closest(".filter_row").find(".filterAttr").val()!='' && $(this).closest(".filter_row").find(".filterAttr").val()==currFltrAttr && currOperator==1 && currOperator==$(this).closest(".filter_row").find(".filterOperator").val())
				{
					alert("You can not select '=' operator twice for the same parameter.");
					currFltr.val('');					
				}
		});
		//if(currFltr.val()==1)
		
		//if operator is between, show firstvalue and secondvalue box
		var isNumericAttr = currFltr.closest(".filter_row").find(".filterAttr").val()==-100?1:0;		
		if(currOperator==7)//between and not date of review
		{
			currFltr.closest(".filter_row").find(".mulFilter-row,.mulFilterValue").css({'display':'none'}).val('');
			currFltr.closest(".filter_row").find(".mulFilter-row .mulFilterValue").removeClass('required');
			currFltr.closest(".filter_row").find(".between").prop('required',true).css({'display':'block'});
			currFltr.closest(".filter_row").find(".filterValue").prop('required',false).css({'display':'none'}).val('');
		}
		else if(currOperator==8){//in	
			currFltr.closest(".filter_row").find(".mulFilter-row,.mulFilterValue option:selected").removeAttr('selected');
			currFltr.closest(".filter_row").find('.mulFilterValue').multiselect('refresh');
			currFltr.closest(".filter_row").find(".mulFilter-row").css({'display':'block'});
			currFltr.closest(".filter_row").find(".mulFilter-row .mulFilterValue").addClass('required');
			currFltr.closest(".filter_row").find(".between").prop('required',false).css({'display':'none'}).val('');
			currFltr.closest(".filter_row").find(".filterValue").prop('required',false).css({'display':'none'}).val('');
		}		
		else{		//operator = and !=	
			currFltr.closest(".filter_row").find(".between,.mulFilter-row,.mulFilterValue").prop('required',false).css({'display':'none'}).val('');
			currFltr.closest(".filter_row").find(".filterValue").prop('required',true).css({'display':'block'});
			currFltr.closest(".filter_row").find(".mulFilter-row,.mulFilterValue").css({'display':'none'}).val('');
			currFltr.closest(".filter_row").find(".mulFilter-row .mulFilterValue").removeClass('required');
		}
		
	});

    $('.the-basics .typeahead').typeahead({
      hint: true,
      highlight: true,
      minLength: 3		
    },
    {
      name: 'questions',
      source: substringMatcher(questions),
		limit:30
    });
  
    $(document).on('sortremove','#sortableR',function(){
    	selectedClients();
    });//sortableRcol
    $(document).on('sortremove','#sortableRcol,#sortableRrow',function(){
    	var frm = "#admin_dashboard_frm";    
    	$(this).children('li').removeClass('selected');
    	$(document).trigger('checkEmptyVars');  		    	   		
    });
    $(document).on('sortreceive','#sortableRcol,#sortableRrow',function(){
    	var frm = "#admin_dashboard_frm"; 
    	$(this).children('li').removeClass('selected');
    	$(document).trigger('checkEmptyVars');        		
    });
    $(document).on('checkEmptyVars',function(){
    	var frm = "#admin_dashboard_frm";
    	if($(frm).find("#sortableRrow li").length<1 || $(frm).find("#sortableRcol li").length<1){
    		$(document).trigger('emptyData');
    		return false;
    	}
		if($(frm).find("#sortableRrow li").length>1 || $(frm).find("#sortableRcol li").length>1){
			$(document).trigger('emptyData');
			return false;
		}
		$(document).trigger('filledData');
		
    });
    $(document).on('sortreceive','#sortableR',function(){
    	selectedClients();
    });
    $(document).on('click','#select-all-sortable',function(){
    	$("#sortableL").find("li").addClass("selected");
    });
    $(document).on('click','#deselect-all-sortable',function(){
    	$("#sortableL").find("li").removeClass("selected");
    });    
    $('.typeahead').bind('typeahead:select', function(ev, suggestion) {	
		  //var id = $('#sortableL li[title="'+suggestion+'"]').data('id');
		  var title;
		  $('#sortableL li span').each(function(i,elem){
				//console.log($(this).attr('title'));
				suggestion = suggestion.replace("'","");
				title = $(this).attr('title');
				title = title.replace("'","");
				if(suggestion!=title)
					$(this).parent().hide();
				else
					$(this).parent().show();
		  });
		});
		
		$('.typeahead').bind('typeahead:render', function(ev, suggestion) {			
			var text='';
			$('#sortableL li').hide();
			$(".tt-dataset.tt-dataset .tt-suggestion").each(function(){				
				text = $(this).text();				
				$('#sortableL').find('li span[title="'+text+'"]').parent().show();								
			});
			
		});	      
	  $(document).on('keyup','#searchbox',function(e){
		  if(e.which=='13')
			  return false;
		  if($("#searchbox").val()=='')
		  {
			  $('#sortableL li span').each(function(){				
					$(this).parent().show();
			});
		  }
	  });
	  $(document).on('click',"#clrBtn",function(){		  
		 $( document ).trigger( "clrBtnClick");
		  
	  })
	  $(document).on('clrBtnClick',function(){				
			  $('#sortableL li span').each(function(){				
					$(this).parent().show();
			});
			$("#searchbox").val('');		  	
		});
	  $(document).on("click","#fltrselectform #btn-fltr-apply",function(){
		 var cont=$(this).parents().find(".currentSelection");		 
		 if($("#f-sortableR li").length>3){
			 alert("You can not apply more than 3 filters.");
			 return false;
		 }
		 
		 if($("#f-sortableR li").length>0)
			 cont.html('<span class="empty notEmpty">None Selected</span>');
		 else{
			// cont.html('<span class="empty">None Selected</span>');
			 alert("Nothing to select! Please select at least one filter to apply.");
			 return false;
		 }
		  $("#f-sortableR li").each(function(){									
				var id=$(this).data('id');								
				var name=$(this).find('span').text();				
				cont.append('<div title="'+name+'" class="selFilterNode selFilterNode-'+id+' clearfix fltr-'+id+'" data-id="'+id+'">'+name+'<input type="hidden" class="ajaxFilterAttach" name="sel-filter['+id+']" value="'+name+'"/><span class="delete"><i class="fa fa-times"></i></span></div>').find(".empty").addClass('notEmpty');
		 });
		  
		  var trgr=cont.data('trigger');
			if(trgr!=undefined && trgr!=null && trgr!="")
				$("body").trigger(trgr);
		
			$(this).parents(".modal").modal("hide");
			 return false;	
						
		});
	  
	  $(document).on("click",".selFilterNode .delete",function(){
			var id=$(this).parents(".selFilterNode").data("id");
			var p=$(this).parents(".currentSelection").first();
			p.find(".selFilterNode-"+id).remove();
			if(p.find(".selFilterNode").length==0){
				p.find(".empty").removeClass("notEmpty");
			}
			 var trgr=p.data('trigger');
				if(trgr!=undefined && trgr!=null && trgr!=""){
					$("body").trigger(trgr);
				}				
		});	 
	 
});
$(document).on('selFiltersUpdate',function(){
	var filterNodes = $(document).find('.currentSelection .selFilterNode');	
	var vals = new Array();
	var vals2="";	
	var kpaData = JSON.parse(sessionStorage.getItem("userKPAData"));
	filterNodes.each(function(i,v){vals.push($(v).data('id'));vals2 = vals2+","+$(v).data('id');});	
	sessionStorage["selfilters"] = JSON.stringify(vals);
	$(document).find('.selectedfilters').first().val(vals2.slice(1));
	var frm = $("#school_dashboard_frm");
	var postData=$(frm).serialize()+"&token="+getToken();		
	apiCall(this,"getgraphData",postData,function(s,msg){	
		if(msg.data.length){
			loadGraph(sessionStorage.getItem("scheme1Award"),sessionStorage.getItem("scheme2Award"),msg.data);
			apiCall(this,"getSubgraphData",postData,function(s,msg){	
				if(msg.data.length)
					loadSubGraphs('kpaGraph',msg.data,kpaData,+(msg.maxScaleY));		
				else{					 
					alert("There is not enough data matching the applied filter.Please change filter criteria.");			
				}
			return;		
				},showErrorMsgInMsgBox);
			
		}
		else{
			$(document).find(".currentSelection .selFilterNode").remove();
			$(document).find(".currentSelection").find(".empty").removeClass("notEmpty");
			sessionStorage["selfilters"]=JSON.stringify("");								
			alert("There is not enough data matching the applied filter.Please change filter criteria.");	
			$("body").trigger('selFiltersUpdate');
		}
	return;		
		},showErrorMsgInMsgBox);
	
	
});
$(document).on('checkEmptyClients',function(){
	var d = $(document);
	if(!(d.find('#sortableL').find('li').length))
	{		
		d.find("#select-all-sortable").prop("disabled",true);
		d.find("#deselect-all-sortable").prop("disabled",true);
	}
	else
	{		
		d.find("#select-all-sortable").prop("disabled",false);
		d.find("#deselect-all-sortable").prop("disabled",false);
	}
});
function selectedClients(){
	var tmpSt=null;
	var tmpEnd=null;
	var ct = null;
	var dformat=null;
	var frm = "#create_network_report_form";
	var list = $("#sortableR");
	$(document).trigger('checkEmptyClients');
	$(frm).find("#num-selected-schools").html(' ('+$(frm).find("#sortableR").find("li").length+')');
	$(frm).find("#num-filtered-schools").html(' ('+$(frm).find("#sortableL").find("li").length+')');
	$(list).find("li").each(function(i,e){
		//stDate = $(this).data('aqs-start');
		//edDate = $(this).data('aqs-end');
		$(this).removeClass('selected');
		ct = ct + $(this).data('id') + ",";
		if(i==0)
		{
			
			stDate = new Date($(this).data('aqs-start'));
			edDate = new Date($(this).data('aqs-end'));    			
		}
		else
		{
			tmpSt = new Date($(this).data('aqs-start'))
			tmpEnd = new Date($(this).data('aqs-end'))
			if(tmpSt<stDate)
				stDate=tmpSt;
			if(tmpEnd>edDate)
				edDate=tmpEnd;
			
		}  
                //alert(stDate);
	});
	
	if(typeof(stDate) !== "undefined" && stDate !== null && typeof(edDate) !== "undefined" && edDate !== null) 
	//if(stDate!=null && edDate!=null)
	{
                //alert(edDate);
		/*dformat = [ (stDate.getMonth()+1).padLeft(),
		            stDate.getDate().padLeft(),
		            stDate.getFullYear()].join('/');*/
            
                dformat = [ stDate.getDate().padLeft(),
                            (stDate.getMonth()+1).padLeft(),
		            stDate.getFullYear()].join('-');
		
		$(frm).find("#frm-date").val(dformat);
		/*dformat = [ (edDate.getMonth()+1).padLeft(),
		                edDate.getDate().padLeft(),
		                edDate.getFullYear()].join('/');*/
                dformat = [ edDate.getDate().padLeft(),
                             (edDate.getMonth()+1).padLeft(),
		             edDate.getFullYear()].join('-');
		$(frm).find("#to-date").val(dformat)
	}
	ct = ct==null?'':ct.slice(0,-1);
	if(typeof(ct) === "undefined" || ct === null || ct=='')
	{
		$(frm).find("#frm-date").val('');
		$(frm).find("#to-date").val('')
	}
	$("#id_clients").val(ct);
	//console.log(ct)
	//console.log(stDate);
	//console.log(edDate);

}
Number.prototype.padLeft = function(base,chr){
	   var  len = (String(base || 10).length - String(this).length)+1;
	   return len > 0? new Array(len).join(chr || '0')+this : this;
	}
function loadGraphold(userData,graphData){	 
	 var margin = {top: 40, right: 20, bottom: 50, left: 40},
	     width = 920 - margin.left - margin.right,
	     height = 460 - margin.top - margin.bottom;

	 var x0 = d3.scale.ordinal()
	     .rangeRoundBands([0, width-140], .1);

	 var x1 = d3.scale.ordinal();

	 var y = d3.scale.linear()
	     .range([height, 0]);

	 var color = d3.scale.ordinal()
	     .range(["#388a5b","#67A0A0","#A06767","#B73394"]);

	 var xAxis = d3.svg.axis()
	     .scale(x0)
	     .orient("bottom");

	 var yAxis = d3.svg.axis()
	     .scale(y)
	     .orient("left")
	     .tickFormat(d3.format(",d"));
	 $("#awardGraph").html('');
	// Define the div for the dtooltip
	 var div = d3.select("body").append("div")	
	     .attr("class", "dtooltip")				
	     .style("opacity", 0);
	 
	 var svg = d3.select("#awardGraph").append("svg")
	     .attr("width", width + margin.left + margin.right)
	     .attr("height", height + margin.top + margin.bottom)
	     .append("g")
	     .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	 var awardNames = d3.keys(graphData[1]).filter(function(key) { return key !== "award"; });  

	 graphData.forEach(function(d) {
	     d.awards = awardNames.map(function(name) { return {name: name, value: +d[name]}; });
	 });
	 x0.domain(graphData.map(function(d) { return d.award }));
	 x1.domain(awardNames).rangeRoundBands([0, x0.rangeBand()]);
	 y.domain([0, d3.max(graphData, function(d) { return d3.max(d.awards, function(d) { return d.value; }); })]);

	 svg.append("g")
	 .attr("class", "x axis")
	 .attr("transform", "translate(0," + height + ")")
	 .call(xAxis)
	 .append("text")	
	 .attr("dy", "2.8em")
	 .attr("dx", "31em")
	 .style({"text-anchor" : "end","font-weight":"bold"})
	 .text("Awards");

	 svg.append("g")
	 .attr("class", "y axis")
	 .call(yAxis)
	 .append("text")
	 .attr("transform", "rotate(-90)")
	 .attr("y", 6)
	 .attr("dy", "-2.5em")
	 .attr("dx", "-9.5em")
	 .style({"text-anchor" : "end","font-weight":"bold"})
	 .text("Number of Schools");

	 var tier = svg.selectAll(".tier")
	 .data(graphData)
	 .enter().append("g")
	 .attr("class", "tier")
	 .attr("transform", function(d) {
	 	if(userData.award==d.award)
	 		$(this).attr("data-award",d.award);
	 	return "translate(" + x0(d.award) + ",0)";
	  });

	 //.attr("xlink:href", function(d){return "http://www.google.com";})
	 tier.selectAll("rect")
	 .data(function(d) { return d.awards; })
	 .enter().append("svg:a")
	 .append("rect")
	 .attr("width", x1.rangeBand())//x1.rangeBand()>100?100:x1.rangeBand()
	 .attr("x", function(d) {
	 	if(userData.tier==d.name)		
	 		$(this).attr("data-tier",d.name);
	 	return x1(d.name); })
	 .attr("y", function(d) {if(!isNaN(d.value)) return y(d.value); else return y(0) })
	 .attr("height", function(d) { if(!isNaN(d.value)) return Number(height) - y(Number(d.value)); else return 0; })
	 .style("fill", function(d) { return color(d.name); });	
	 
	
	/*var bar = svg.selectAll('.tier rect').append("text").text(function(d){ return d3.format(",")(d[1])})
     .attr("x", function(d) { return x1(d[0])+x.rangeBand()/2; })
     .attr("y", function(d) { return y(d[1])-5; })
     .attr("text-anchor", "middle");
	 */
	 var legend = svg.selectAll(".legend")
	 .data(awardNames.slice().reverse())
	 .enter().append("g")
	 .attr("class", "legend")
	 .attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; });


	 legend.append("text")
	 .attr("x", width-86)
	 .attr("y", 9)
	 .attr("dy", ".35em")
	 .style("text-anchor", "start")
	 .text(function(d) { return d; });
	 

	 legend.append("rect")
	 .attr("x", width - 110)
	 .attr("width", 18)
	 .attr("height", 18)
	 .style("fill", color);
	 
	 //$('g.tier[data-award="'+userData.award+'"] [data-tier="'+userData.tier+'"]').attr('id','userAward').after("<text>you are here</text>")
	 var userPosBar = $('g.tier[data-award="'+userData.award+'"] [data-tier="'+userData.tier+'"]').attr('id','userAward');
	 $('g.tier[data-award="'+userData.award+'"] [data-tier="'+userData.tier+'"]').parent('a').attr('id','userAward-a');	
	 //svg.selectAll("g.tier #userAward-a").append("text").attr("x",$(userPosBar).attr("x")).attr("y",$(userPosBar).attr("y")).attr("dx","0.5em").attr("y","-0.5em").attr("height",$(userPosBar).attr("height")).text("You").style("font-weight","bold");
	 svg.selectAll("g.tier #userAward-a").append("text").attr("x",$(userPosBar).attr("x")).attr("y",$(userPosBar).attr("y")).attr("dx","0.5em").attr("dy","-0.5em").text("You").style("font-weight","bold");
	 
}
function loadGraphstack(userData,graphData){	 
	$("#awardGraph").html('');
	var svg = d3.select("#awardGraph").append("svg"),
    margin = {top: 40, right: 20, bottom: 50, left: 80},
    width = 900 - margin.left - margin.right,
    height = 600 - margin.top - margin.bottom;
	svg.attr("width", width + margin.left + margin.right +30 )
    .attr("height", height + margin.top + margin.bottom); 
    var g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")"); 
    var fntSize = "1.4em";   	
	
	var x = d3.scaleBand()
    .rangeRound([0, width-50])
    .padding(0.1)
    .align(0.1);
	
	var y = d3.scaleLinear()
	    .rangeRound([height, 0]);	
	var z = d3.scaleOrdinal()
	    .range(["#566BBF", "#D47F46", "#5E8A69", "#929096", "#a05d56", "#d0743c", "#ff8c00"]);	
	var stack = d3.stack();	
	var awards = d3.keys(graphData[1]).filter(function(key) { return key != "tier"; });
	 graphData.forEach(function(d) {
		 d.total=0;
	    awards.map(function(name) { return d.total+=+(!isNaN(d[name])?d[name]:0 )});		 
	 });
	 graphData.sort(function(a, b) { return b.total - a.total; });

	  x.domain(graphData.map(function(d) { return d.tier; }));
	  y.domain([0, d3.max(graphData, function(d) { return d.total; })]).nice();
	  z.domain(awards);
	 
	  g.selectAll(".award")
	    .data(stack.keys(awards)(graphData))
	    .enter().append("g")	     
	      .attr("fill", function(d) { 	    	  	    	 
	    	  return z(d.key); })
	   .attr("class",function(d){
		   if(d.key==userData.award)
	    		  return("award user")
	    	return("award")	  ;
	   }) 	  
	    .selectAll("rect")
	    .data(function(d) {return d; })
	    .enter().append("rect")
	      .attr("x", function(d) { return x(d.data.tier); })
	      .attr("y", function(d) { return y(d[1])})
	      .attr("data-tier",function(d){
		   if(d.data.tier==userData.tier)
	    		  return(d.data.tier);
	      }) 
	      .attr("height", function(d) { 	    	  
	    	  return Math.abs(y(d[0]) - y(d[1])); })
	      .attr("width", x.bandwidth())
	      .on("mouseover", function() { dtooltip.style("display", null); })
		  .on("mouseout", function() { dtooltip.style("display", "none"); })
		  .on("mousemove", function(d) {	
			  if(Math.abs(d[1]-d[0])==0)
				  return false;
		    var xPosition = d3.mouse(this)[0]+63;
		    var yPosition = d3.mouse(this)[1]+17;
		    dtooltip.attr("transform", "translate(" + xPosition + "," + yPosition + ")");		    
		    dtooltip.select("text").text(Math.abs(d[1]-d[0]));
		  });

	 g.append("g")
	      .attr("class", "axis axis--x")	      
	      .attr("transform", "translate(0," + height + ")")
	      .call(d3.axisBottom(x))
	      .style("font-size","1em");
	
	 g.append("g")
	 .attr("class", "axis axis--y")
	 .call(d3.axisLeft(y).ticks(10, "s"))
	 .append("text")
	 .attr("transform", "rotate(-90)")	 
	 .attr("y", y(y.ticks(10).pop()))
	 .attr("dy", "-3.0em")
	 .attr("dx", "-17em")
	  .attr("text-anchor", "start")
	  .style("font-size",fntSize)
	  .attr("fill", "#000")
	  .text("Number of schools");
	 
	 
	// Prep the dtooltip bits, initial display is hidden
	 var dtooltip = svg.append("g")
	   .attr("class", "dtooltip")
	   .style("display", "none");
	     
	 dtooltip.append("rect")
	   .attr("width", 30)
	   .attr("height", 20)
	   .attr("fill", "white")
	   .style("opacity", 0.5);

	 dtooltip.append("text")
	   .attr("x", 15)
	   .attr("dy", "1.2em")
	   .style("text-anchor", "middle")
	   .attr("font-size", "12px")
	   .attr("font-weight", "bold");
	 
	 var legend = g.selectAll(".legend")
	    .data((awards).reverse())
	    .enter().append("g")
	      .attr("class", "legend")
	      .attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; })
	      .style("font", "10px sans-serif");

	  legend.append("rect")
	      .attr("x", width-35)
	      .attr("width", 18)
	      .attr("height", 18)
	      .attr("fill", z);

	  legend.append("text")
	      .attr("x", width-10)
	      .attr("y", 9)
	      .attr("dy", ".35em")
	      .style("font-size",fntSize)
	      .attr("text-anchor", "start")
	      .text(function(d) { return d; });
	  
	  var userPosBar = $('g.award.user [data-tier="'+userData.tier+'"]').attr('id','userAward');			
	  g.select("g.user")	  
	  .append("circle").attr("cx",parseInt($(userPosBar).attr("width"))/4+parseInt($(userPosBar).attr("x"))).attr("cy",parseInt($(userPosBar).attr("height")/2)+parseInt($(userPosBar).attr("y"))).attr("r",5).attr("stroke","#000").attr("stroke-width","3").attr("fill","#000");
	  g.select("g.user").insert("text").attr("x",parseInt($(userPosBar).attr("width"))/4+parseInt($(userPosBar).attr("x"))+12).attr("y",parseInt($(userPosBar).attr("height")/2)+parseInt($(userPosBar).attr("y"))+4).text("You ("+userData.tier+" "+userData.award+")").style("font-weight","bold").attr("fill","#000");

}
function loadGraph(userAwardScheme1,userAwardScheme2,graphData){	 
	$("#awardGraph").html('');
	var svg = d3.select("#awardGraph").append("svg"),
    margin = {top: 40, right: 20, bottom: 80, left: 80},
    width = 930 - margin.left - margin.right,
    height = 450 - margin.top - margin.bottom;
	svg.attr("width", width + margin.left + margin.right +30 )
    .attr("height", height + margin.top + margin.bottom); 
    var g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")"); 
    var fntSize = "1.4em";   	
	
	var x = d3.scaleBand()
    .rangeRound([0, width-200])
    .padding(0.1)
    .align(0.1);
	
	var y = d3.scaleLinear()
	    .rangeRound([height, 0]);	
	var z = d3.scaleOrdinal()
	    .range(["#566BBF", "#D47F46", "#5E8A69", "#929096", "#a05d56", "#857F58", "#855875"]);	
	var stack = d3.stack();	
	var fStacks = d3.keys(graphData[0]).filter(function(key) { return key!='award' });
	 graphData.forEach(function(d) {
		 d.total=0;
		 fStacks.map(function(name) { return d.total+=+(!isNaN(d[name])?d[name]:0 )});		 
	 });
	// graphData.sort(function(a, b) { return b.total - a.total; });

	  x.domain(graphData.map(function(d) { return d.award; }));
	  y.domain([0, d3.max(graphData, function(d) { return d.total; })]).nice();
	  z.domain(fStacks);
	 
	  g.selectAll(".award")
	    .data(stack.keys(fStacks)(graphData))
	    .enter().append("g")	     
	      .attr("fill", function(d) { 	    	  	    	 
	    	  return z(d.key); })
	   .attr("class",function(d){		  
	    	return("award")	  ;
	   }) 	  
	    .selectAll("rect")
	    .data(function(d) {return d; })
	    .enter().append("rect")
	      .attr("class",function(d){
	    	  if(d.data.award==userAwardScheme1||d.data.award==userAwardScheme2)
	    		  return "user";
	      }) 	
	      .attr("x", function(d) { return x(d.data.award); })
	      .attr("y", function(d) { return y(d[1])})	      	      
	      .attr("height", function(d) { 	    	  
	    	  return Math.abs(y(d[0]) - y(d[1])); })
	      .attr("width", x.bandwidth())
	      .on("mouseover", function() { dtooltip.style("display", null); })
		  .on("mouseout", function() { dtooltip.style("display", "none"); })
		  .on("mousemove", function(d) {	
			  if(Math.abs(d[1]-d[0])==0)
				  return false;
		    var xPosition = d3.mouse(this)[0]+63;
		    var yPosition = d3.mouse(this)[1]+17;
		    dtooltip.attr("transform", "translate(" + xPosition + "," + yPosition + ")");		    
		    dtooltip.select("text").text(Math.abs(d[1]-d[0]));
		  });

	 g.append("g")
	      .attr("class", "axis axis--x")	      
	      .attr("transform", "translate(0," + height + ")")
	      .call(d3.axisBottom(x))
	      .selectAll("text")
	      .style("text-anchor","end")
	      .style("font-size","1.2em")	      
          .attr("dx", "-.8em")
          .attr("dy", ".15em")
          .attr("transform", function(d) {
              return "rotate(-30)" 
           });	     
	
	 g.append("g")
	 .attr("class", "axis axis--y")
	 .call(d3.axisLeft(y).ticks(10, "s"))
	 .append("text")
	 .attr("transform", "rotate(-90)")	 
	 .attr("y", y(y.ticks(10).pop()))
	 .attr("dy", "-3.0em")
	 .attr("dx", "-17em")
	  .attr("text-anchor", "start")
	  .style("font-size",fntSize)
	  .attr("fill", "#000")
	  .text("Number of schools");
	 
	 
	// Prep the dtooltip bits, initial display is hidden
	 var dtooltip = svg.append("g")
	   .attr("class", "dtooltip")
	   .style("display", "none");
	     
	 dtooltip.append("rect")
	   .attr("width", 30)
	   .attr("height", 20)
	   .attr("fill", "white")
	   .style("opacity", 0.5);

	 dtooltip.append("text")
	   .attr("x", 15)
	   .attr("dy", "1.2em")
	   .style("text-anchor", "middle")
	   .attr("font-size", "12px")
	   .attr("font-weight", "bold");
	 
	 var legend = g.selectAll(".legend")
	    .data((fStacks).reverse())
	    .enter().append("g")
	      .attr("class", "legend")
	      .attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; })
	      .style("font", "10px sans-serif");

	  legend.append("rect")
	      .attr("x", width-200)
	      .attr("width", 18)
	      .attr("height", 18)
	      .attr("fill", z);

	  legend.append("text")
	      .attr("x", width-180)
	      .attr("y", 9)
	      .attr("dy", ".35em")
	      .style("font-size",fntSize)
	      .attr("text-anchor", "start")
	      .text(function(d) { return d=='default'?'All data':d; });
	  
	  var userPosBar = $('g.award .user').last();
	  if(userPosBar.length){
		  g.select("g.award")	  
		  .append("circle").attr("cx",parseInt($(userPosBar).attr("width")/4)+parseInt($(userPosBar).attr("x"))-3).attr("cy",parseInt($(userPosBar).attr("y"))-10).attr("r",5).attr("stroke","#000").attr("stroke-width","3").attr("fill","#000");
		  g.select("g.award").insert("text").attr("dy","-0.3em").attr("x",parseInt($(userPosBar).attr("width")/4)+parseInt($(userPosBar).attr("x"))+7).attr("y",parseInt($(userPosBar).attr("y"))).text("You").style("font-weight","bold").attr("fill","#000");
	  }
}
function loadSubGraphs(id,data,userParamData,maxScaleY){
	$("#"+id).html('');	
	//get x axis parameter name in an array - kpa or kq or sq or js  
	console.log(data)    
	var thisParam;	
	var i = 1;
    data.forEach(function(d){ 
    	for(var a in userParamData)
        	if(userParamData[a].kpa_name==d.name)
        		thisParam= userParamData[a]; 
    		subGraph(i,id,d,thisParam,+maxScaleY);
    		i++
    });    
	 
}
function subGraph(i,id,data,userParamData,maxScaleY){		
	var ratings = ["Needs Attention","Variable","Good","Outstanding"];
	var fStacks = d3.keys(data[0]).filter(function(key) { return key!='rating' });
	var userRating = userParamData!=undefined?userParamData.rating:0;
	var maxVal =0;
	for(var k in data){
		data[k].total=0;
		fStacks.map(function(name) { 
			data[k].total+=+(!isNaN(data[k][name])?data[k][name]:0 );
			maxVal = data[k].total>maxVal?data[k].total:maxVal; return;});				
	} 
	if(maxVal==0)
		{			
			return;
		}
	var svg = d3.select("#"+id).append("svg"),
    margin = {top: 35, right: 20, bottom: 60, left: 60},
    width = 350 - margin.left - margin.right,
    height = 330 - margin.top - margin.bottom;
	svg.attr("width", width + margin.left + margin.right )
    .attr("height", height + margin.top + margin.bottom); 
	//Title labels
	svg.append("text")
    .attr("dx", "11em")
     .attr("dy","1em")
    //.attr("y", top + (row * (height + inMarg)) + (height / 2) + 12)
    .style("font-family", "sans-serif")
    .style("text-anchor", "middle")
    .style("font-size", "0.9em")
    .style("opacity","0.9")
    .text(data.name);
    var g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")"); 
    var fntSize = "1.4em"; 
    var x = d3.scaleBand()
    .rangeRound([0, width])
    .padding(0.1)
    .align(0.1);
    var y = d3.scaleLinear()
    .rangeRound([height, 0]);	
	var z = d3.scaleOrdinal()
	    .range(["#566BBF", "#D47F46", "#5E8A69", "#929096", "#a05d56", "#857F58", "#855875"]);	
	var stack = d3.stack();	     
	var stackBarData=new Array();
	for(var k in data){	
		if(k!='name')stackBarData.push(data[k]);		
	} ;
	
	 x.domain(ratings);
	  y.domain([0,+maxScaleY ]).nice();
	  z.domain(fStacks);
	  
	  //draw bars with stacks
	  g.selectAll(".rating")
	    .data(stack.keys(fStacks)(stackBarData))
	    .enter().append("g")	     
	      .attr("fill", function(d) { 	    	  
	    	  return z(d.key);
	    	   })
	   .attr("class",'rating') 	  
	    .selectAll("rect")
	    .data(function(d) {return d; })
	    .enter().append("rect")
	      .attr("class",function(d){ if(d.data.rating==userRating) return 'user';})
	      .attr("x", function(d) { return x(d.data.rating); })
	      .attr("y", function(d) { return  y(d[1]);})	      
	      .attr("height", function(d) { 	    	  
	    	  return Math.abs(y(d[0]) - y(d[1])); })
	    .attr("width", x.bandwidth())
	    .on("mouseover", function() { dtooltip.style("display", null); })
		.on("mouseout", function() { dtooltip.style("display", "none"); })
		.on("mousemove", function(d) {	
			  if(Math.abs(d[1]-d[0])==0)
				  return false;
		    var xPosition = d3.mouse(this)[0]+63;
		    var yPosition = d3.mouse(this)[1]+17;
		    dtooltip.attr("transform", "translate(" + xPosition + "," + yPosition + ")");		    
		    dtooltip.select("text").text(Math.abs(d[1]-d[0]));
		  });
	  
	  g.append("g")
      .attr("class", "axis axis--x")	      
      .attr("transform", "translate(0," + height + ")")
      .call(d3.axisBottom(x))
      .selectAll("text")
      .style("text-anchor","end")
      .style("font-size","1.2em")	      
      .attr("dx", "-0.17em")
      .attr("dy", "0.85em")
      .attr("transform", function(d) {
          return "rotate(-30)" 
       });	     

	 g.append("g")
	 .attr("class", "axis axis--y")
	 .call(d3.axisLeft(y).ticks(10, "s"))
	 .append("text")
	 .attr("transform", "rotate(-90)")	 
	 .attr("y", y(y.ticks(10).pop()))
	 .attr("dy", "-2.5em")
	 .attr("dx", "-12em")
	  .attr("text-anchor", "start")
	  .style("font-size",fntSize)
	  .attr("fill", "#000")
	  .text(function(d){if(i%3==1) return "Number of schools";return"";}); 
		// Prep the dtooltip bits, initial display is hidden
	 var dtooltip = svg.append("g")
	   .attr("class", "dtooltip")
	   .style("display", "none");
	     
	 dtooltip.append("rect")
	   .attr("width", 30)
	   .attr("height", 20)
	   .attr("fill", "white")
	   .style("opacity", 0.5);

	 dtooltip.append("text")
	   .attr("x", 15)
	   .attr("dy", "1.2em")
	   .style("text-anchor", "middle")
	   .attr("font-size", "12px")
	   .attr("font-weight", "bold");
	
	 var userPosBar = $('g.rating .user').last();
	 if(userPosBar.length){
		 g.select("g.rating")	  
		  .append("circle").attr("cx",parseInt($(userPosBar).attr("width")/4)+parseInt($(userPosBar).attr("x"))-3).attr("cy",parseInt($(userPosBar).attr("y"))-10).attr("r",5).attr("stroke","#000").attr("stroke-width","3").attr("fill","#000");
		 g.select("g.rating").insert("text").attr("dy","-0.3em").attr("x",parseInt($(userPosBar).attr("width")/4+7)+parseInt($(userPosBar).attr("x"))).attr("y",parseInt($(userPosBar).attr("y"))).text("You").style("font-weight","bold").attr("fill","#000");
	 }
	 
}
function type(d, i, columns) {
	  for (i = 1, t = 0; i < columns.length; ++i) t += d[columns[i]] = +d[columns[i]];
	  d.total = t;
	  return d;
}
function wrap(text, width) {
	  text.each(function() {
	    var text = d3.select(this),
	        words = text.text().split(/\s+/).reverse(),
	        word,
	        line = [],
	        lineNumber = 0,
	        lineHeight = 1.1, // ems
	        y = text.attr("y"),
	        dy = parseFloat(text.attr("dy")),
	        tspan = text.text(null).append("tspan").attr("x", 0).attr("y", y).attr("dy", dy + "em");
	    while (word = words.pop()) {
	      line.push(word);
	      tspan.text(line.join(" "));
	      if (tspan.node().getComputedTextLength() > width) {
	        line.pop();
	        tspan.text(line.join(" "));
	        line = [word];
	        tspan = text.append("tspan").attr("x", 0).attr("y", y).attr("dy", ++lineNumber * lineHeight + dy + "em").text(word);
	      }
	    }
	  });
	}
function checkAttrForSubAttr(frm,sno,attrVal){
	var postData='';
	var currFltr = $('#'+frm).find('.currentSelection.sno_'+sno)
	var cardinality = currFltr.length;
	if(currFltr.length){
		postData="attr_id="+attrVal+"&token="+getToken()+"&cardinality="+cardinality+"&sno="+sno;
		apiCall(this,"getDiagnosticGrainHtmlRow",postData,function(s,data){
			showSuccessMsgInMsgBox(s,data);
			//if subrow exists replace it else add new
			if(currFltr.closest(".filter_row").next('tr.subrow').length)
				currFltr.closest(".filter_row").next('tr.subrow').replaceWith(data.content);	
			else	
				currFltr.closest(".filter_row").after(data.content);				
		});
		
	}
}