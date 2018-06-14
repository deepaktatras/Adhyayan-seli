<?php 
 $resource_role_list = array_filter(explode(',', $resource_detail['user_role_id']));
 //print_r($resource_detail);
 ?>

<div data-action="editresource" data-controller="resource" data-querystring="ajaxRequest=<?php echo $ajaxRequest; ?>&ispop=<?php echo $isPop; ?>&resource_id=<?php echo $resource_detail['resource_id']; ?>&resource_file_id=<?php echo $resource_detail['resource_file_id']; ?>&succMsg=1" class="filterByAjax resource_edit" >
    <h1 class="page-title">
            <a href="<?php
            $args = array("controller" => "resource", "action" => "resourceList");
            echo createUrl($args);
            ?>">
                <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
                Manage MyResources
            </a>&rarr;	Edit Resource&rarr;<?php echo ucfirst($resource_detail['resource_title']);?>
    </h1>
    <div class="clr"></div>
    <div >
        <div class="ylwRibbonHldr">
            <div class="tabitemsHldr"></div>
        </div>
        <div class="subTabWorkspace pad26">
            <div class="form-stmnt">
                <form enctype="multipart/form-data" method="post" id="edit_resource_form" action="">
                    <div class="boxBody">
                        <dl class="fldList">
                            <dt>Title<span class="astric">*</span>:</dt>
                            <dd><div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value="<?php echo $resource_detail['resource_title']; ?>" name="resource_title" required /></div></div></dd>
                        </dl>
                        <dl class="fldList">
                            <dt>Description:</dt>
                            <dd><div class="row"><div class="col-sm-6"><textarea name="resource_description" class="form-control" ><?php echo $resource_detail['resource_description']; ?></textarea></div></div></dd>
                        </dl>
                        <dl class="fldList">
                        <dt>Add Resource<span class="astric">*</span>:</dt>
                        <dd>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="clearfix" id="checkbox_div">
                                        <div class="chkHldr"><input type="radio" autocomplete="off" name="resource_link_type" value="file" <?php echo ($resource_detail['resource_link_type']=="file" || empty($resource_detail['resource_link_type']))?"checked='checked'":""; ?> ><label class="chkF radio"><span>File </span></label></div>
                                        <div class="chkHldr"><input type="radio" autocomplete="off" name="resource_link_type" value="url" <?php echo ($resource_detail['resource_link_type']=="url")?"checked='checked'":""; ?>><label class="chkF radio"><span>URL  </span></label></div>
                                    </div>
                                </div></div>
                        </dd>
                        </dl>
                        <dl class="fldList" id="file">
                            <dt>Attach Resource File<span class="astric">*</span>:</dt>
                            <dd><div class="row">
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
                                        <?php
                                        if (!empty($resource_detail['file_name'])) {
                                            $nArr = explode(".", $resource_detail['file_name']);
                                            $ext = strtolower(array_pop($nArr));
                                            ?>

                                            <div class="filesWrapper">
                                                <a href="<?php echo UPLOAD_URL_RESOURCE; ?><?php echo $resource_detail['file_name']; ?>" target="_blank">
                                                    <div class="filePrev uploaded vtip ext-<?php echo $ext; ?>" id="file-<?php echo $resource_detail['file_id']; ?>" title="<?php echo $resource_detail['file_name']; ?>">

                                                        <input type="hidden" name="file_id" value="<?php echo $resource_detail['file_id']; ?>">
                                                    </div>
                                                </a>
                                            </div>

                                        <?php } ?>
                                    </div>      

                                </div>

                            </dd>
                        </dl>
                        
                        <dl class="fldList" id="url">
                        <dt>Add URL<span class="astric">*</span>:</dt>
                        <dd>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" value="<?php echo $resource_detail['resource_link_type']=="url"?$resource_detail['resource_url']:"" ?>" name="resource_url" id="resource_url"  maxlength="255"/>
                                </div></div>
                        </dd>
                        </dl>
                        
                        <dl class="fldList ">
                            <dt>Select Folder<span class="astric">*</span>:   </dt>
                            <dd>
                                <div class="wfm resources"></div>
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
                                        <div class="col-sm-3 btnHldr">
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
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" value="<?php echo $resource_detail['tags'];?>" class="form-control" name="dir_tags" placeholder="Enter resource tags" id="dir_tags" data-role="tagsinput">
                                        </div>

                                    </div>
                                </dd>
                            </div>
                        </dl>
                        
                      
                        <dl id="schools_type" class="fldList">
                            <dd>
                                <div class=" width-50-modal">
                                    <div class="chkHldr"> <input type="checkbox" name="resource_type" value="<?php echo ($resource_detail['resource_type'] == 1) ?$resource_detail['resource_type']:0;?>" id="resource_type" <?php echo ($resource_detail['resource_type'] == 1) ? 'checked = "checked"' : ''; ?> > <label class="chkF checkbox"><span>Please select this checkbox if you want to assign the resource to a network/non-network.</span></label></div>
                                </div>
                            </dd>            
                        </dl>
                        <div id="school_type_block" style="<?php echo ($resource_detail['resource_type'] == 1) ? 'display: block' : 'display: none'; ?>">
                                            <dl id="schools_type"  class="fldList">

                                                <dt>Schools Related To<span class="astric">*</span>:</dt>
                                                <dd>
                                                    <div class="row">
                                                        <div class="col-sm-6 width-50-modal">
                                                            <select class="form-control" id="school_related_to" name="network_option" >
                                                                <option value="1" <?php echo ($resource_detail['network_option'] == 1) ? "selected=selected" : ""; ?>>Network</option>
                                                                <option value="2" <?php echo ($resource_detail['network_option'] == 2) ? "selected=selected" : ""; ?>>Non Network </option>
                                                                <option value="3" <?php echo ($resource_detail['network_option'] == 3) ? "selected=selected" : ""; ?>>All</option>

                                                            </select>
                                                        </div>
                                                    </div>    
                                                </dd>
                                            </dl>

                                            <dl id="networks" <?php echo isset($resource_networks) && count($resource_networks) > 0 ? 'style="display:block;"' : 'style="display:none;"'; ?>  class="fldList">
                                                <dt>Network<span class="astric">*</span>:</dt>
                                                <dd>
                                                    <div class="row">
                                                        <div class="col-sm-6 width-50-modal">
                                                            <select class="form-control" id="rec_network" name="network[]" multiple="multiple">
                                                                <?php
                                                                foreach ($networks as $network)
                                                                    if (in_array('all', $resource_networks[0]))
                                                                        echo "<option  selected='selected' value=\"" . $network['network_id'] . "\">" . $network['network_name'] . "</option>\n";
                                                                    else
                                                                        echo "<option " . (in_array($network['network_id'], array_column($resource_networks, 'network_id')) ? 'selected="selected"' : "") . " value=\"" . $network['network_id'] . "\">" . $network['network_name'] . "</option>\n";
                                                                ?>                                          
                                                            </select>
                                                        </div>

                                                </dd>
                                            </dl>
                                            <dl id="provinces" <?php echo isset($resource_provinces) && count($resource_provinces) > 0 ? '' : 'style="display:none;"'; ?> class="fldList">
                                                <dt>Province<span class="astric">*</span>:</dt>
                                                <dd>
                                                    <div class="row">
                                                        <div class="col-sm-6 width-50-modal">
                                                            <select class="form-control province-list-dropdown" name="province[]" id="rec_provinces" multiple="multiple">
<?php
foreach ($network_provinces as $provinces)
    if (in_array('all', $resource_provinces[0]))
        echo "<option selected='selected' value=\"" . $provinces['province_id'] . "\">" . $provinces['province_name'] . "</option>\n";
    else
        echo "<option " . (in_array($provinces['province_id'], array_column($resource_provinces, 'province_id')) ? 'selected="selected"' : "") . " value=\"" . $provinces['province_id'] . "\">" . $provinces['province_name'] . "</option>\n";
?>
                                                            </select>
                                                        </div>

                                                    </div>
                                                </dd>
                                            </dl>
                                            <dl id="rec_schools" <?php echo isset($provinces_schools) && count($provinces_schools) > 0 ? '' : 'style="display:none;"'; ?> class="fldList">
                                                <dt>Schools<span class="astric">*</span>:</dt>
                                                <dd>
                                                    <div class="row">
                                                        <div class="col-sm-6 width-50-modal">
                                                            <select class="form-control province-list-dropdown" name="school[]" id="rec_school" multiple="multiple">
<?php
foreach ($provinces_schools as $schools)
    if (in_array('all', $resource_schools[0]))
        echo "<option selected='selected'  value=\"" . $schools['client_id'] . "\">" . $schools['client_name'] . "</option>\n";
    else
        echo "<option " . (in_array($schools['client_id'], array_column($resource_schools, 'client_id')) ? 'selected="selected"' : "") . " value=\"" . $schools['client_id'] . "\">" . $schools['client_name'] . "</option>\n";
?>
                                                            </select>
                                                        </div>
                                                        <!--<div class="col-sm-3 width-50-modal">
                                                                <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                                        </div>-->
                                                    </div>
                                                </dd>
                                            </dl>
                                           
                                            <dl id="rec_roles"  <?php echo isset($resource_role_list) && count($resource_role_list) > 0 ? '' : 'style="display:none;"'; ?> class="fldList">
                                                <dt>User Role<span class="astric">*</span>:</dt>
                                                <dd>
                                                    <div class="row">
                                                        <div class="col-sm-6 width-50-modal">
                                                             <select class="form-control province-list-dropdown " id="rec_role" name="roles[]" multiple="multiple">
                                                                <?php
                                                               
                                                                
                                                                foreach ($roles as $role) {
                                                                    if (in_array($role['role_id'], $resource_role_list)) {
                                                                        $selected = ' selected="selected"';
                                                                    } else {
                                                                        $selected = '';
                                                                    }
                                                                    echo "<option $selected value=\"" . $role['role_id'] . "\">" . $role['role_name'] . "</option>\n";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </dd>
                                            </dl>
                                            <dl id="rec_users" <?php echo isset($resource_users) && count($resource_users) > 0 ? '' : 'style="display:none;"'; ?> class="fldList">
                                                <dt>Users<span class="astric">*</span>:</dt>
                                                <dd>
                                                    <div class="row">
                                                        <div class="col-sm-6 width-50-modal">
                                                            <select class="form-control province-list-dropdown" name="rec_user[]" id="rec_users_select" multiple="multiple">
<?php
foreach ($school_users as $users)
    if (in_array('all', $resource_users[0]))
        echo "<option  selected='selected' value=\"" . $users['user_id'] . "\">" . $users['name'] . "</option>\n";
    else
        echo "<option " . (in_array($users['user_id'], array_column($resource_users, 'user_id')) ? 'selected="selected"' : "") . " value=\"" . $users['user_id'] . "\">" . $users['name'] . "</option>\n";
?>
                                                            </select>
                                                        </div>
                                                        <!--<div class="col-sm-3 width-50-modal">
                                                                <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                                        </div>-->
                                                    </div>
                                                </dd>
                                            </dl>
                                        </div>
                                            
                                        
                                        <input type="hidden" name="resource_id" value="<?php echo $resource_detail['resource_id']; ?>" >
                                        <input type="hidden" name="resource_file_id" value="<?php echo $resource_detail['resource_file_id']; ?>" >
                                        <div id="createresource" class="ajaxMsg <?php echo $succMsg == 1 ? 'success active' : '' ?>" ><?php echo $succMsg == 1 ? 'Resource successfully updated' : '' ?></div>
                                        <div class="btnHldr ml6 text-right"><input type="submit" value="Update Resource" class="btn btn-primary"></div>
                                        </div>
                                        
                                        <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                                        </form>
                                        </div>
                                        </div>
                                        </div>
                                        </div>
                                        <script type="text/javascript">
                                            $(document).ready(function () {
                                                callFolder(<?php echo $resource_id;?>,<?php echo $resource_file_id;?>);
                                                $(".vertScrollArea").mCustomScrollbar({theme: "dark"});
                                                //$('.mask_ph').mask("(+99) 999-9999-999");        
                                            });
                                        </script>