	<?php
        $sample_file='';
        if($type=="Assessor"){
        $sample_file=' (<a href="public/sample_csv/sample_assessor_template.csv" class="small">Sample Format</a>)';   
        }else if($type=="Teacher"){
        $sample_file=' (<a href="public/sample_csv/sample_teacher_template.csv" class="small">Sample Format</a>)';      
        }else if($type=="Student"){
        $sample_file=' (<a href="public/sample_csv/sample_student_template.csv" class="small">Sample Format</a>)';      
        }
        ?>
        <h1 class="page-title">Upload <?php echo $type; ?> List</h1>
	<div class="clr"></div>
	<div class="">
		<div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
		</div>
		<div class="subTabWorkspace pad26">
				<div id="fileUploadWrap" class="">
					<div id="fileList">
						
					</div>
					<div style="line-height: 34px;">
                                            Only .csv file is allowed <?php echo $sample_file; ?>
						<button title="Upload <?php echo $type; ?> List" id="upload<?php echo $type; ?>List" class="btn pull-right btn-primary vtip">Select File</button>
					</div>
					<div class="clr"></div>
				</div>
		</div>	
	</div>
	
	<script type="text/javascript">
        userFileType='<?php echo $type; ?>';    
	$(document).ready(function(){
		var uploader = new plupload.Uploader({
			runtimes : 'html5,flash,silverlight,html4',
			browse_button : 'upload<?php echo $type; ?>List',
			//container:"fileUploadWrap",
			url : '?controller=api&action=uploadFileInParts',
			flash_swf_url : '<?php echo SITEURL; ?>public/js/plupload/Moxie.swf',
			silverlight_xap_url : '<?php echo SITEURL; ?>public/js/plupload/Moxie.xap',
			
			filters : {max_file_size : '20mb',mime_types: [{title : "CSV files", extensions : "csv"}]},
			multi_selection:false,
			init: {
				PostInit: function() {
					$("#fileList").html('');
				},
				BeforeUpload:function(up,file){
					up.setOption("multipart_params",{"token" : getToken(),"actionAfterUpload":"extract<?php echo $type; ?>CSV","taid":'<?php echo $teacherAssessment['group_assessment_id']; ?>' });
				},
				FilesAdded: function(up, files) {
					plupload.each(files, function(file) {
						$("#fileList").append('<div id="'+file.id+'" class="row"><div class="col-sm-6">'+file.name+' (' + plupload.formatSize(file.size) + ')</div><div class="col-sm-6"><div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">0%</div></div></div></div>');
					});
					uploader.start();
				},
				UploadProgress: function(up, file) {
					$("#fileList #"+file.id).find(".progress-bar").css("width",file.percent+"%").attr("aria-valuenow",file.percent+"%").html(file.percent+"%");
				},
				Error: function(up, err) {alert("\nError #" + err.code + ": " + err.message);},
				FileUploaded: function(up,file,resp){
					var rData=null;
					try{
						rData = $.parseJSON(resp.response);
					}catch(e){}
                                        console.log(resp);
					if(rData!=undefined && rData!=null && rData.status!=undefined){
						if(rData.status==-1){
							$("#fileList #"+file.id).find(".progress-bar").html('Failed').addClass('progress-bar-danger');
							sessionExpired();
						}else if(rData.status==1){
							if(rData.result!=undefined && rData.result!=null){
								if(rData.result.error>0){
                                                                        $("#fileList #"+file.id).find(".progress-bar").html('Failed').addClass('progress-bar-danger');
									alert(rData.result.msg);
								}else if(rData.result.content!=''){
                                                                        if(userFileType=="Teacher"){
                                                                        up_type_class="teacher_team";    
                                                                        }else if(userFileType=="Student"){
                                                                         up_type_class="teacher_team";    
                                                                        }else{
                                                                        up_type_class="teacherAssessor_team";    
                                                                        }
									$("."+up_type_class+" .team_row").last().after(rData.result.content);
									$("."+up_type_class+" .team_row").each(function(i,row){
										var allEmpty=1;
										$(row).find("input").each(function(j,el){
											if($(el).val().length>0)
												allEmpty=0;
										});
										if(allEmpty)
											$(row).remove();
									});
									$("."+up_type_class+" .team_row .date-Picker").datetimepicker({format:'MM-DD-YYYY',pickTime:false});
									$('.'+up_type_class+' .team_row .selectpicker').selectpicker();
									$("."+up_type_class+" .team_row .s_no").each(function(i,e){ $(e).html(i+1); });
									$("body").trigger($('.'+up_type_class+'').data('trigger'));
                                                                        if(userFileType=="Assessor"){
                                                                        //$(".teacher_team").hide();
                                                                        //$("#up_teacher").hide();
                                                                        $("#submitTchrsForAssessmnt").attr('disabled', "disabled");
                                                                        }
									$("#upload<?php echo $type; ?>List").parents(".modal").modal("hide");
								}else{
                                                                alert("Check the data format of uploaded file");    
                                                                $("#fileList #"+file.id).find(".progress-bar").html('Failed').addClass('progress-bar-danger');
    
                                                                }
							}else{
								alert('File uploaded successfuly but still we got some error from server');
							}
						}else{
							$("#fileList #"+file.id).find(".progress-bar").html('Failed').addClass('progress-bar-danger');
							alert(rData.message);
						}
					}else{
						alert('Unknown response from server');
					}
				}
			}
		});
		uploader.init();
	});
	</script>