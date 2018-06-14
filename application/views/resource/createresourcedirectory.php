<?php //echo "<pre>";print_r($directory_list);die;?>
<h1 class="page-title">
    <a href="<?php $args = array("controller" => "resource", "action" => "resourcelist");
    echo createUrl($args);
    ?>">
        <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
        Manage Resource

    </a> &rarr;	Add Resource Folder
</h1>
<div class="clr"></div>
<div class="">
     
    <div class="ylwRibbonHldr">
        <div class="tabitemsHldr"></div>
    </div>
    <div class="subTabWorkspace pad26">
        <div class="form-stmnt">
            <form method="post" id="create_resourcedirectory_form" action="">
                <div class="boxBody">
                    <dl class="fldList">
                        <dt>Select Folder<span class="astric">*</span>:   </dt>
                        <dd>
                            <div class="wfm vertScrollArea resources">
                            <ul class="treeView"><li> 
                                    <?php
                                    $i = 0;
                                    $tree = treeView($directory_list, 0);
                                    function treeView($Categorys) {
                                        $i = 0;
                                        foreach ($Categorys as $Category) {
                                            $i++;
                                            ?>
                                            <ul> <li class="scndLvl"><div class="expCol">
                                                        <img alt="" class="collapse" src="<?php echo SITEURL; ?>public/images/Plus.png" />
                                                        <img alt="" class="expand" src="<?php echo SITEURL; ?>public/images/Minus.png"  />
                                                    </div>
                                                    <div class="treeCont chkHldr expColcont">
                                                        <input class="Checkbox1" name="directory" type="checkbox" value="<?php echo $Category['directory_id']; ?>"><label class="chkF checkbox"><span><?php echo $Category['directory_name']; ?></span></label>
                                                    </div>

                                                    <?php
                                                    if (isset($Category['children']) && count($Category['children'])) {
                                                        childView($Category, 0,0);
                                                    } else {
                                                        echo "</li></ul>";
                                                    }
                                                }
                                            }

                                            function childView($Category, $count = 0,$ul_flag=0) {
                                                if($ul_flag == 1)
                                                    echo '<ul style="display: none;">';
                                                else {
                                                    echo '<ul style="display: block;">';}
                                                foreach ($Category['children'] as $arr) {
                                                    if (isset($arr['children']) && count($arr['children'])) {
                                                        ?>  <li class="scndLvl"><div class="expCol">
                                                            <img alt="" class="collapse" src="<?php echo SITEURL; ?>public/images/Minus.png" />
                                                            <img alt="" class="expand" src="<?php echo SITEURL; ?>public/images/Plus.png" />
                                                        </div>
                                                        <div class="treeCont chkHldr expColcont">
                                                            <input class="Checkbox1" name="directory" type="checkbox" value="<?php echo $arr['directory_id']; ?>"><label class="chkF checkbox"><span><?php echo $arr['directory_name']; ?></span></label>
                                                        </div>
                                                        <?php
                                                        childView($arr, 1,1);
                                                    } else {
                                                        ?><li class="scndLvl">
                                                        <div class="treeCont chkHldr">
                                                            <input  class="Checkbox1" name="directory" type="checkbox" value="<?php echo $arr['directory_id']; ?>"><label class="chkF checkbox"><i class="fa fa-folder-o"></i><span><?php echo $arr['directory_name']; ?></span></label>
                                                        </div>
                                                        <?php
                                                        echo "</li>";
                                                    }
                                                }

                                                echo "</ul>";
                                            }
                                            ?>
                                    </li>
                                </ul> 
                            </div>
                        </dd>
                    </dl>
                   
                    <dl class="fldList padTop">
                        <div  id="addKpaBox">
                            <dt>Folder Name<span class="astric">*</span>:   </dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="new_dir_name" placeholder="Enter folder name" id="dir_name">
                                        <input type="hidden" id="redirec_url" name="redirec_url" value="<?php echo SITEURL;?>?controller=resource&action=createResourceDirectory&ispop=1">
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
                                        <input type="text" class="form-control" name="dir_tags" placeholder="Enter folder tags" id="dir_tags" data-role="tagsinput">                                    </div>
                                   
                                </div>
                            </dd>
                        </div>
                    </dl>
                     <dl id="schools_type"  class="fldList">
                        <dd>
                            <div class="width-50-modal">
                                <div class="chkHldr"><input class="user-roles" name="resource_type" value="1" id="resource_type" type="checkbox"><label class="chkF checkbox"><span>Please select this checkbox if you want to assign the resource to a network/non-network.</span></label></div>                                    
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
                    <div class="btnHldr ml6 text-right"><button type='submit' class='addQuest btn btn-primary addQbtn'><i class="fa fa-plus"></i>Add</button></div>
                    
                </div>
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