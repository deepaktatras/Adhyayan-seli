<?php //echo "<pre>";print_r($fileList);die; ?>
<h1 class="page-title">
    	File Tree
</h1>
<div class="clr"></div>
<div class="">

    <div class="ylwRibbonHldr">
        <div class="tabitemsHldr"></div>
    </div>
    <div class="subTabWorkspace pad26">
        <div class="form-stmnt">
            <form method="post" id="edit_resource_form" action="">
                <div class="boxBody">
                    <dl class="fldList">
                       
                        <dd>
                           <div class="vertScrollArea">
                                <ul class="treeView"><li> 
                                    <?php
                                    $i = 0;
                                    $resource_detail = isset($resource_detail)?$resource_detail:array();
                                    //echo "<pre>";print_r($directory_list);die;
                                    $tree = treeView($directory_list, $resource_detail);

                                    function treeView($Categorys,$resource_detail) {
                                        $i = 0;
                                        foreach ($Categorys as $Category) {
                                            $i++;
                                            ?>
                                            <ul> <li class="mainWrap scndLvl"><div class="expCol">
                                                        <img alt="" class="collapse" src="<?php echo SITEURL; ?>public/images/<?php echo isset($resource_detail['directory_id'])&& $resource_detail['directory_id']>=1? 'Plus.png':'Minus.png';?>" />
                                                        <img alt="" class="expand" src="<?php echo SITEURL; ?>public/images/<?php echo isset($resource_detail['directory_id'])&& $resource_detail['directory_id']>=1? 'Minus.png':'Plus.png';?>"  />
                                                    </div>
                                                    <div class="treeCont chkHldr expColcont">
                                                        <input class="Checkbox1" name="directory" type="radio"  <?php echo (isset($resource_detail['directory_id']) && $resource_detail['directory_id'] == $Category['directory_id']) ? "checked=checked" : ""; ?> value="<?php echo $Category['directory_id']; ?>"><label class="chkF checkbox"><span><?php echo $Category['directory_name']; ?></span></label>
                                                    </div>

                                                    <div class="actRBox" id="add_folder"><!--<a href="#" data-size="800" class="btn btn-primary vtip icoBtn" id="addschoolDirBtn" title="Add Folder"><i class="fa fa-folder-open-o"></i> <span>Add</span></a> --></div>



                                                    <?php
                                                    if (isset($Category['children']) && count($Category['children'])) {
                                                        childView($Category, 0, $resource_detail);
                                                    } else {
                                                        echo "</li></ul>";
                                                    }
                                                }
                                            }

                                            function childView($Category, $count = 0,$resource_detail) {
                                                if(isset($resource_detail['directory_id'])&& $resource_detail['directory_id']>=1) {
                                                    echo '<ul style="display: block;">';
                                                }else {
                                                    echo '<ul style="display: none;">';
                                                }
                                                foreach ($Category['children'] as $arr) {
                                                    if (isset($arr['children']) && count($arr['children'])) {
                                                        ?>  <li class="scndLvl"><div class="expCol">
                                                            <img alt="" class="collapse" src="<?php echo SITEURL; ?>public/images/<?php echo isset($resource_detail['directory_id'])&& $resource_detail['directory_id']>=1? 'Plus.png':'Minus.png';?>" />
                                                            <img alt="" class="expand" src="<?php echo SITEURL; ?>public/images/<?php echo isset($resource_detail['directory_id'])&& $resource_detail['directory_id']>=1? 'Minus.png':'Plus.png';?>" />
                                                        </div>
                                                        <div class="treeCont chkHldr expColcont">
                                                            <input class="Checkbox1" name="directory" type="radio" <?php echo isset($resource_detail['directory_id']) && ($resource_detail['directory_id'] == $arr['directory_id']) ? "checked=checked" : ''; ?> value="<?php echo $arr['directory_id']; ?>"><label class="chkF checkbox"><span><?php echo $arr['directory_name']; ?></span></label>
                                                        </div>
                                                        <?php
                                                        childView($arr, 1,$resource_detail);
                                                    } else {
                                                        ?><li class="scndLvl">
                                                        <div class="treeCont chkHldr">
                                                            <input  class="Checkbox1" name="directory" type="radio" <?php echo isset($resource_detail['directory_id']) && ($resource_detail['directory_id'] == $arr['directory_id']) ? "checked=checked" : ''; ?> value="<?php echo $arr['directory_id']; ?>"><label class="chkF checkbox"><i class="fa fa-folder-o"></i><span><?php echo $arr['directory_name']; ?></span></label>
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
                                </li>
                                </ul></div></dd>
                    </dl>

                </div>

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