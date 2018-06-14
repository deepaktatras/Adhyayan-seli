<?php 
 $resource_role_list = array_filter(explode(',', $folder_detail['user_role_id']));
 ?>

<div data-action="editresource" data-controller="resource" data-querystring="ajaxRequest=<?php echo $ajaxRequest; ?>&ispop=<?php echo $isPop; ?>&resource_id=<?php echo $folder_detail['resource_id']; ?>&resource_file_id=<?php echo $folder_detail['resource_file_id']; ?>&succMsg=1" class="filterByAjax resource_edit" >
    <h1 class="page-title">
            <a href="<?php
            $args = array("controller" => "resource", "action" => "resourceList");
            echo createUrl($args);
            ?>">
                <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
                Manage Folder
            </a>&rarr;	Edit Folder&rarr;<?php echo ucfirst($folder_detail['directory_name']);?>
    </h1>
    <div class="clr"></div>
    <div >
        <div class="ylwRibbonHldr">
            <div class="tabitemsHldr"></div>
        </div>
        <div class="subTabWorkspace pad26">
            <div class="form-stmnt">
                <form enctype="multipart/form-data" method="post" id="edit_folder_form" action="">
                    <div class="boxBody">
                        <dl class="fldList">
                            <dt>Folder Name<span class="astric">*</span>:</dt>
                            <dd><div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value="<?php echo $folder_detail['directory_name']; ?>" name="folder_name" required /></div></div></dd>
                        </dl>                       

                        
                        <dl class="fldList ">
                            <dt>Select Folder<span class="astric">*</span>:   </dt>
                            <dd>
                                <div class="wfm resources"></div>
                            </dd>
                        </dl>
                        <dl class="fldList padTop">
                            <div  id="addKpaBox">
                                <dt>Tags:   </dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" value="<?php echo $folder_detail['tags'];?>" class="form-control" name="dir_tags" placeholder="Enter folder tags" id="dir_tags" data-role="tagsinput">
                                        </div>

                                    </div>
                                </dd>
                            </div>
                        </dl>
                        
                      
                        <dl id="schools_type" class="fldList">
                            <dd>
                                <div class=" width-50-modal">
                                    <div class="chkHldr"> <input type="checkbox" name="resource_type" value="<?php echo ($folder_detail['directory_type'] == 1) ?$folder_detail['directory_type']:0;?>" id="resource_type" <?php echo ($folder_detail['directory_type'] == 1) ? 'checked = "checked"' : ''; ?> > <label class="chkF checkbox"><span>Please select this checkbox if you want to assign the resource to a network/non-network.</span></label></div>
                                </div>
                            </dd>            
                        </dl>
                        <div id="school_type_block" style="<?php echo ($folder_detail['directory_type'] == 1) ? 'display: block' : 'display: none'; ?>">
                                            <dl id="schools_type"  class="fldList">

                                                <dt>Schools Related To<span class="astric">*</span>:</dt>
                                                <dd>
                                                    <div class="row">
                                                        <div class="col-sm-6 width-50-modal">
                                                            <select class="form-control" id="school_related_to" name="network_option" >
                                                                <option value="1" <?php echo ($folder_detail['network_option'] == 1) ? "selected=selected" : ""; ?>>Network</option>
                                                                <option value="2" <?php echo ($folder_detail['network_option'] == 2) ? "selected=selected" : ""; ?>>Non Network </option>
                                                                <option value="3" <?php echo ($folder_detail['network_option'] == 3) ? "selected=selected" : ""; ?>>All</option>

                                                            </select>
                                                        </div>
                                                    </div>    
                                                </dd>
                                            </dl>

                                            <dl id="networks" <?php echo isset($is_network) && $is_network == 1 ? 'style="display:block;"' : 'style="display:none;"'; ?>  class="fldList">
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
                                           
                                            <dl id="rec_roles"  <?php echo isset($folder_roles) && count($folder_roles) > 0 ? '' : 'style="display:none;"'; ?> class="fldList">
                                                <dt>User Role<span class="astric">*</span>:</dt>
                                                <dd>
                                                    <div class="row">
                                                        <div class="col-sm-6 width-50-modal">
                                                             <select class="form-control province-list-dropdown " id="rec_role" name="roles[]" multiple="multiple">
                                                                <?php
                                                               
                                                                
                                                                foreach ($roles as $role) {
                                                                    if (in_array($role['role_id'], $folder_roles)) {
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
                                            
                                        
                                        <input type="hidden" name="directory_id" value="<?php echo $folder_detail['directory_id']; ?>" >
                                        <div id="createresource" class="ajaxMsg <?php echo $succMsg == 1 ? 'success active' : '' ?>" ><?php echo $succMsg == 1 ? 'Resource successfully updated' : '' ?></div>
                                        <div class="btnHldr ml6 text-right"><input type="submit" value="Update Folder" class="btn btn-primary"></div>
                                        </div>
                                        
                                        <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                                        </form>
                                        </div>
                                        </div>
                                        </div>
                                        </div>
                                        <script type="text/javascript">
                                            $(document).ready(function () {
                                                callFolderTree(<?php echo $directory_id;?>);
                                                $(".vertScrollArea").mCustomScrollbar({theme: "dark"});
                                                //$('.mask_ph').mask("(+99) 999-9999-999");        
                                            });
                                        </script>