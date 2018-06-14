<?php //echo "<pre>";print_r($directory_list);die;?>
<h1 class="page-title">
       	Edit Resource Folder
</h1>
<div class="clr"></div>
<div class="">
     
    <div class="ylwRibbonHldr">
        <div class="tabitemsHldr"></div>
    </div>
    <div class="subTabWorkspace pad26">
        <div class="form-stmnt">
            <form method="post" id="edit_resourcedirectory_form" action="">
                <div class="boxBody">
                   
                   
                    <dl class="fldList padTop">
                        <div  id="addKpaBox">
                            <dt>Folder Name<span class="astric">*</span>:   </dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="new_dir_name" placeholder="Enter folder name" id="dir_name" value="<?php echo $directory_details['directory_name'];?>">
                                        <input type="hidden" id="redirec_url" name="redirec_url" value="<?php echo SITEURL;?>?controller=resource&action=createResourceDirectory&ispop=1">
                                        <input type="hidden"  name="directory_id" value="<?php echo $directory_details['directory_id'];?>">
                                    </div>
                                    <div class="col-sm-3 btnHldr">
                                        <button type='submit' class='addQuest btn btn-primary addQbtn'>Save</button>
                                    </div>
                                </div>
                            </dd>
                        </div>
                    </dl>

                    
                </div>
                <div class="ajaxMsg"></div>
                <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
            </form>
        </div>
    </div>
</div>

<!-- Initialize the plugin: -->
    <script type="text/javascript">
        $(document).ready(function () {
            $(".vertScrollArea").mCustomScrollbar({theme: "dark"});
        });
    </script>