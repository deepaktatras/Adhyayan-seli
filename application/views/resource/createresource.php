<h1 class="page-title">
    <a href="<?php $args = array("controller" => "resource", "action" => "resourcelist");
    echo createUrl($args);
    ?>">
        <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
        Manage Resource

    </a> &rarr;	Add Resource
</h1>

<div class="clr"></div>
<div class="">
    <div class="ylwRibbonHldr">
        <!--<div class="tabitemsHldr btnPlcHdr">
            <a href="?controller=resource&action=listResourceDirectory&ispop=1" data-size="800" class="btn btn-primary icoBtn vtip" id="addschoolAssBtn" title="View All Folder"><i class="fa fa-folder-open"></i></a> 
            <a href="?controller=resource&action=createResourceDirectory&ispop=1" data-size="800" class="btn btn-primary icoBtn vtip" id="addschoolDirBtn" title="Add Directory"><i class="fa fa-folder-o"></i></a> 
        </div>-->
    </div>
    <div class="subTabWorkspace pad26">
        <div class="form-stmnt">
            <form enctype="multipart/form-data" method="post" id="create_resource_form" action="">
                <div class="boxBody">
                    <dl class="fldList">
                        <dt>Title<span class="astric">*</span>:</dt>
                        <dd><div class="row">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" value="" name="resource_title" required /></div></div></dd>
                    </dl>
                    <dl class="fldList">
                        <dt>Description:</dt>
                        <dd><div class="row"><div class="col-sm-6"><textarea name="resource_description" class="form-control" ></textarea></div></div></dd>
                    </dl>
                    <dl class="fldList">
                        <dt>Add Resource<span class="astric">*</span>:</dt>
                        <dd>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="clearfix" id="checkbox_div">
                                        <div class="chkHldr"><input type="radio" autocomplete="off" name="resource_link_type" value="file" Checked="Checked"><label class="chkF radio"><span>File </span></label></div>
                                        <div class="chkHldr"><input type="radio" autocomplete="off" name="resource_link_type" value="url"><label class="chkF radio"><span>URL  </span></label></div>
                                    </div>
                                </div></div>
                        </dd>
                    </dl>
                    <dl class="fldList" id="file">
                        <dt>Attach File<span class="astric">*</span>:</dt>
                        <dd>
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="file-up-wrapper clearfix">
                                        <div class="fileUpload filt-nupload btn btn-primary mr0 col-sm-4">
                                            <i class="glyphicon glyphicon-folder-open"></i> <span>Attach File</span>  
                                            <input type="file" class="upload uploadBtn" title="" name="resource_file" id="resource_file" autocomplete="off">
                                        </div>                                       
                                        <span id="file_attached" class="fileName"></span>
                                    </div>
                                    <div class="file-info">
                                        <span class="fileNote">Only jpeg, png, gif, jpg, avi, mp4, mov, doc, docx, txt, xls, xlsx, pdf, csv, xml, pptx, ppt, cdr, mp3, wav type of files are allowed</span>
                                    </div>
                                </div>                              
                            </div>
                        </dd>
                    </dl>
                    
                    <dl class="fldList" id="url">
                        <dt>Add URL<span class="astric">*</span>:</dt>
                        <dd>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" value="" name="resource_url" id="resource_url"  maxlength="255"/>
                                </div></div>
                        </dd>
                    </dl>
                    
                    <dl class="fldList">
                        <dt>Select Folder<span class="astric">*</span>:   </dt>
                        <dd>
                            <div class="wfm  resources"></div>
                        </dd>
                    </dl>
                    <dl class="fldList padTop addfolder " style=" display: none;">
                        <div  id="addKpaBox">
                            <dt>Folder Name<span class="astric">*</span>:   </dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="new_dir_name" placeholder="Enter folder name" id="dir_name">
                                        <input type="hidden" id="redirec_url" name="redirec_url" value="<?php echo SITEURL;?>?controller=resource&action=createResourceDirectory&ispop=1">
                                    </div>
                                    <div class="col-sm-3">
                                        <button type='button' class='addQuest btn btn-primary addQbtn' id="addfolderbutton"><i class="fa fa-plus"></i>Add</button>
                                    </div>
                                </div>
                            </dd>
                        </div>
                    </dl>
                   <dl class="fldList padTop">
                            <div  id="addKpaBox">
                                <dt>Tags:   </dt>
                                <dd>
                                    
                                   <!-- <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            
                                            <input type="text" name="tags" placeholder="Tags" class="form-control tm-input tm-input-info"/>
                                        </div>
                                    </div>-->
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input value="<?php echo isset($folder_detail['tags'])?$folder_detail['tags']:'';?>" class="form-control" name="dir_tags" placeholder="Enter resource tags" id="dir_tags" data-role="tagsinput">
                                        </div>

                                    </div>
                                </dd>
                            </div>
                        </dl>
                    <dl id="schools_type"  class="fldList">
                        <dd>
                            <div class="width-50-modal">
                                <div class="chkHldr"><input class="user-roles" name="resource_type" value="resource_type" id="resource_type" type="checkbox"><label class="chkF checkbox"><span>Please select this checkbox if you want to assign the resource to a network/non-network.</span></label></div>                                    
                            </div>
                        </dd>            
                    </dl>
                    <div id="school_type_block" style=" display: none;">
                        <dl id="schools_type"  class="fldList">
                                <dt>Schools Related To<span class="astric">*</span>:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6 width-50-modal">
                                            <select class="form-control" id="school_related_to" name="school_related_to" >
                                                <option value="1" selected="selected">Network</option>
                                                <option value="2">Non Network </option>
                                                <option value="3">All</option>

                                            </select>
                                        </div>
                                    </div>    
                                </dd>
                        </dl>

                        <dl id="networks" class="fldList">
                                <dt>Network<span class="astric">*</span>:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6 width-50-modal">
                                            <select class="form-control" id="rec_network" name="network[]" multiple="multiple">
                                                <?php
                                                foreach ($networks as $network)
                                                    echo "<option value=\"" . $network['network_id'] . "\">" . $network['network_name'] . "</option>\n";
                                                    echo "<option value=\"" . 'all' . "\">" . 'ALL' . "</option>\n";
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                            <dl id="provinces" style="display:none;" class="fldList">
                                <dt>Province:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6 width-50-modal">
                                            <select class="form-control province-list-dropdown" name="province[]" id="rec_provinces" multiple="multiple">
                                            </select>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                        
                        <dl id="rec_schools" style="display:none;" class="fldList">
                                <dt>Schools<span class="astric">*</span>:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6 width-50-modal">
                                            <select class="form-control province-list-dropdown" name="school[]" id="rec_school" multiple="multiple">
                                            </select>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                         <dl id="rec_roles" style="display:none;" class="fldList">
                                <dt>User Role<span class="astric">*</span>:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6 width-50-modal">
                                             <select class="form-control province-list-dropdown " id="rec_role" name="roles[]" multiple="multiple">
                                                <?php
                                                foreach ($roles as $role)
                                                    echo "<option value=\"" . $role['role_id'] . "\">" . $role['role_name'] . "</option>\n";
                                                    echo "<option value=\"" . 'all' . "\">" . 'ALL' . "</option>\n";
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                          
                        <dl id="rec_users" style="display:none;" class="fldList">
                                <dt>Users<span class="astric">*</span>:</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6 width-50-modal">
                                            <select class="form-control province-list-dropdown" name="rec_user[]" id="rec_users_select" multiple="multiple">
                                            </select>
                                        </div>
                                        <!--<div class="col-sm-3 width-50-modal">
                                                <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                        </div>-->
                                    </div>
                                </dd>
                            </dl>
                    </div>
                    
                    <div id="errors" style=" display: none;"></div>
                    <div class="ajaxMsg" id="createresource"></div>
                    <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>">
                    <div class="btnHldr ml6 text-right"><input type="submit" value="Add Resource" class="btn btn-primary"></div>                 
                </div>                
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        callFolder();
        $(".vertScrollArea").mCustomScrollbar({theme: "dark"});
         $(".tm-input").tagsManager();
            // 
    });
</script>