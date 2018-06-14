<?php //echo "<pre>";print_r($directory_list);die;?>
<h1 class="page-title">
    <a href="<?php $args = array("controller" => "resource", "action" => "resourcelist");
        echo createUrl($args);?>"> <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
        Manage Resource
    </a> &rarr;
    <a href="<?php $args = array("controller" => "resource", "action" => "createresource");
        echo createUrl($args); ?>">Add Resource </a> &rarr; Resource Folder List
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
                                            <ul> <li><div class="expCol">
                                                        <img alt="" class="expand1" src="<?php echo SITEURL; ?>public/images/Minus.png" />
                                                        <img alt="" class="collapse1" style=" display: none" src="<?php echo SITEURL; ?>public/images/Plus.png"  />
                                                    </div>
                                                    <div class="treeCont chkHldr">
                                                        <input class="Checkbox1" name="directory" type="checkbox" value="<?php echo $Category['directory_id']; ?>"><label class="chkF checkbox"><span><?php echo $Category['directory_name']; ?></span></label>
                                                    </div>

                                                    <?php
                                                    if (isset($Category['children']) && count($Category['children'])) {
                                                        childView($Category, 0);
                                                    } else {
                                                        echo "</li></ul>";
                                                    }
                                                }
                                            }

                                            function childView($Category, $count = 0) {
                                                echo '<ul style="display: block;">';
                                                foreach ($Category['children'] as $arr) {
                                                    if (isset($arr['children']) && count($arr['children'])) {
                                                        ?>  <li><div class="expCol">
                                                            <img alt="" class="expand1" src="<?php echo SITEURL; ?>public/images/Minus.png" />
                                                            <img alt="" class="collapse1" style=" display: none" src="<?php echo SITEURL; ?>public/images/Plus.png" />
                                                        </div>
                                                        <div class="treeCont chkHldr">
                                                            <input class="Checkbox1" name="directory" type="checkbox" value="<?php echo $arr['directory_id']; ?>"><label class="chkF checkbox"><span><?php echo $arr['directory_name']; ?></span></label>
                                                        </div>
                                                        <?php
                                                        childView($arr, 1);
                                                    } else {
                                                        ?><li>
                                                        <div class="treeCont chkHldr">
                                                            <input  class="Checkbox1" name="directory" type="checkbox" value="<?php echo $arr['directory_id']; ?>"><label class="chkF checkbox"><span><?php echo $arr['directory_name']; ?></span></label>
                                                        </div>
                                                        <?php
                                                        echo "</li>";
                                                    }
                                                }

                                                echo "</ul>";
                                            }
                                            ?>
                                    </li>
                                </ul> </li></ul>
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