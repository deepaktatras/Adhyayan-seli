
<h1 class="page-title">
    <?php if (isset($isPop) && $isPop == 0) { ?>
        <a href="<?php
        $args = array("controller" => "assessment", "action" => "editStudentAssessment", "gaid" => $gaid);
        echo createUrl($args);
        ?>">
            <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
            Edit Reviews
        </a> &rarr;
    <?php } ?>
    Student Profile Upload 
</h1>
<div class="clr"></div>
<div class="">
    <div class="ylwRibbonHldr">
        <span class="btn btn-primary pull-right execUrl mr30" id="sampleStuBtn">Download Sample Student Profile</span>
        <div class="tabitemsHldr"></div>
    </div>
    <div class="subTabWorkspace pad26">
        <div class="form-stmnt">
            <form method="post" id="upload_student_form" action="">
                <input type="hidden" name="gaid" id="gaid" value="<?php echo $gaid ?>">
                <div class="boxBody">
                   
                   <!-- <dl class="fldList">
                        <dt>Attach User Profile Excel File<span class="astric">*</span>:</dt>
                        <dd><div class="row">
                                <div class="col-sm-8">
                                    <div class="file-up-wrapper clearfix">
                                        <div class="fileUpload filt-nupload btn btn-primary mr0 col-sm-4">
                                            <i class="glyphicon glyphicon-folder-open"></i> <span>Attach File</span>  
                                            <input type="file" class="upload uploadBtn" title="" name="aqs_excel_file" id="user_excel_file" autocomplete="off">
                                        </div>
                                        <span id="file_attached" class="fileName"></span>
                                        <div class="file-info">(Only excel file is allowed)</div>
                                    </div>
                                </div>                              
                            </div></dd>
                    </dl>-->
                   
                   <dl class="fldList">
                       <dt>Upload Student Profile Sheet<span class="astric">*</span>:</dt>
                       <dd>

                           <div class="row">


                               <div class="col-sm-8" > 
                                   <div class="file-up-wrapper clearfix" id="workshopupfile">
                                       <div class="fileUpload filt-nupload-workshop btn btn-primary mr0 col-sm-4">
                                           <i class="glyphicon glyphicon-folder-open"></i> <span>Upload File</span>  
                                           <input type="file" class="upload uploadBtn" title="" name="aqs_excel_file" id="user_excel_file"  autocomplete="off">
                                       </div>  
                                       <span id="file_attached" class="workshopfileName"></span>

                                   </div>
                                   <div class="file-info" id="workshopupfile">
                                       <span class="fileNote">(Only excel file is allowed) 

                                       </span> 
                                   </div>


                               </div>    

                       </dd>
                   </dl>
                   <dl class="fldList">
                        <dt></dt>
                        <dd class="nobg">
                            <div class="row">
                                <div class="col-sm-6">
                                    <br>
                                    <input type="submit" value="Upload" class="btn btn-primary" >
                                    
                                </div>
                            </div>
                            
                            <span style=" display: none;" id="warningStatus" class="uploadaqserrormsg" data-toggle='modal' data-target='#myModal'>Click Here To View Warnings/Errors</span>
                        </dd>
                    </dl>

                </div>
                <div class="ajaxMsg"></div>
                <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                
                
            </form>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade">
                    <div class="modal-dialog " >
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">AQS Upload Warnings/Errors</h4>
                            </div>  
                            <div class="modal-body">
                                <div class="clr"></div>
                                <div class="">
                                    
                                    <div class="ylwRibbonHldr">
                                            <div class="tabitemsHldr"></div>
                                    </div>
                                    <div class="subTabWorkspace pad26 uploadaqserrors">
                                        <h4>Errors</h4>
                                        <div class="form-stmnt text-danger" id="validationAQSErrors" >
                                            
                                        </div>
                                    </div>
                                    <div class="subTabWorkspace pad26 uploadaqswarnings">
                                        <h4>Warnings</h4>
                                        <div class="form-stmnt text-warning" id="validationAQSWarnings" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
<script>
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    $('.aqs_sdate').datetimepicker({format: 'MM/DD/YYYY', pickTime: false, minDate: today});
    $('.aqs_edate').datetimepicker({format: 'MM/DD/YYYY', pickTime: false, minDate: today});
    $(document).on("click", "#aqsf_school_aqs_pref_start_date,#aqsf_school_aqs_pref_end_date", function () {
        $(this).trigger('change')
    });
</script>
