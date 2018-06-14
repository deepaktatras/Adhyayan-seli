<?php 
$searchTypes[] = array('key'=>'Files','value'=>'1');
$searchTypes[] = array('key'=>'Folder','value'=>'2');
//echo "<pre>";print_r($searchTypes);die; ?>
<div class="filterByAjax resource-list" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>" >
    <h1 class="page-title">
        <?php if ($isPop == 0) { ?>
            <a href="?"><i class="fa fa-chevron-circle-left vtip" title="Back"></i> Manage MyResources</a>
        <?php } ?>
        <!-- Resource List -->
    </h1>
    <?php 
                    $ajaxFilter = new ajaxFilter();
                    $ajaxFilter->addHidden("searchResource", 1);  
                    if(!empty($searchValue)){
                        $ajaxFilter->addHidden("search_val", $searchValue);  
                    }
                    
                    $ajaxFilter->addDropDown("search_for", $searchTypes, 'value', 'key', $searchFor, '');
                    $ajaxFilter->addTextBox("search_val", $searchValue, "File/Folder/Tag");
                    if (in_array("upload_resources", $user['capabilities'])) {
                        $ajaxFilter->addLink('<a href="?controller=resource&action=createResource&ispop=1" data-size="600" class="btn btn-primary icoBtn vtip fixonmodal" title="Add Resource" id="addResourceBtn"><i class="fa fa-file"></i></a>');
                        $ajaxFilter->addLink('<a href="?controller=resource&action=createResourceDirectory&ispop=1" data-size="800" class="btn btn-primary vtip icoBtn" id="addschoolDirBtn" title="Add Folder"><i class="fa fa-folder-open"></i></a> ');
                    }
                    $ajaxFilter->generateFilterBar(1);
            ?>
    <div class="clr"></div>
    <div class="loader"></div>
    
    <div class="box" >     
       
        <div class="subTabWorkspace pad26" id="resourceData">
            <?php if(isset($is_resource) && $is_resource == 1) { ?>
            
            <div style=" text-align: center; width: 100%;">
                <h2>
                    No Resource Found                    
                </h2>
            </div>
            
            <?php } else {?>
            <div class="form-stmnt">
                <form method="post" id="list_resourcedirectory_form" action="">
                    <div class="boxBody">
                        <dl class="fldList">
                            <div class="treeContTableWrap">
                                <div class="table-responsive">
                                    <table class="table treeTable mb0">
                                        <thead>
                                            <tr>
                                                <th><span>Name of Folder/File</span></th>
                                                <!-- <th style="width:30px"></th>-->
                                                <th style="width:90px"></th> 
                                                <th style="width:84px"><span>File size</span></th>
                                                <th style="width:90px"><span>Status</span></th>
                                                <th style="width:70px"><span>Action</span></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="wfm vertScrollArea resources">
                                <ul class="treeView"><li> 
                                        <?php
                                        if(!empty($searchResource) && $searchResource == 1){ ?>
                                            
                                            <table class="fileview tableData">
                                            <tbody>
                                                <?php
                                                if(empty($filedata) ) {?>
                                                
                                                <tr> <td colspan="4" style="text-align: center;"><span style=" color: red;">
                                                    
                                                       No record found   
                                                        </span>
                                                    </td>
                                                </tr>
                                                    <?php
                                                }else {
                                                if(isset($searchFor) && $searchFor == 2) { 
                                                    
                                                     foreach($filedata as $arr) {  ?>
                                                        <tr>                                                                         
                                                            <td><div class="treeCont">
                                                                    <div class="nameWrap expColcont"><span class="vtip expandName1  " title="Click here to view folder in tree format"><a class="execUrl" href="?controller=resource&action=treeFolder&ispop=1&directory_id=<?php echo $arr['directory_id']; ?>"><?php echo $arr['directory_name']; ?></a></span></div>
                                                                </div>
                                                            </td>
                                                            <!-- <td style="width:28px;"><div class="filedata"> </div></td>
                                                            <td style="width:92px;"><div class="filedata"> </div></td> -->
                                                            <td style="width:84px;"></td>
                                                            <td style="width:90px;"></td>
                                                        <?php if (in_array("upload_resources", $user['capabilities'])) {
                                                            ?>

                                                            <td style="width:70px;">
                                                               <div class="treeCont">
                                                                <?php if (in_array("upload_resources", $user['capabilities'])) { ?>
                                                                <div class="actionOpt action-<?php echo $arr['directory_id']; ?>" >                                                                     
                                                                  <a href="?controller=resource&action=updateFolder&ispop=1&directory_id=<?php echo $arr['directory_id']; ?>" class="edit-btn-resource vtip editfolder " id="<?php echo $arr['directory_id']; ?>" title="Edit"><i class="vtip fa fa-pencil-square-o" title="Edit"></i></a>                                                    
                                                                  <?php if( empty($arr['resource_directory']) ) { ?>
                                                                     <a href="#" class="delete-btn-resource vtip delfolder" id="<?php echo $arr['directory_id']; ?>" title="Delete"><i class="edit-btn-resource vtip fa fa-trash-o" title="Delete"></i></a>                                                    
                                                                   <?php  } ?>
                                                                </div>
                                                                <?php } ?>
                                                                
                                                            </div>
                                                            </td>
                                                                                                                              
                                                        <?php
                                                        } else {
                                                           //if ($arr['status'] == 1) {
                                                               ?>
                                                            <td style="width:70px;"></td>
                                                            <?php
                                                            //}
                                                        }
                                                        ?>
                                            </tr>
                                                
                                                <?php }
                                                } if(isset($searchFor) && $searchFor == 1) { 
                                                foreach($filedata as $arr) {  ?>
                                                <tr>                                                                         
                                                <td><div class="filename1 "> <a href="<?php echo $arr['resource_link_type']=="url"?"".$arr['resource_url']."":"".SITEURL."index.php?controller=resource&action=forcedDownload&file_id=".$arr['file_id']."&d_file=".$arr['resource_title'].""?>" class="vtip" target="_blank" title="<?php echo $arr['resource_title']; ?>"><?php echo $arr['resource_title']; ?></a></div></td>
                                                <!-- <td style="width:28px;"><div class="filedata"> </div></td>
                                                <td style="width:92px;"><div class="filedata"> </div></td> -->
                                                <td style="width:84px;"><div class="filedata"> <?php echo!empty($arr['file_size']) ? $arr['file_size'] : " "; ?></div></td>
                                            <?php if (in_array("upload_resources", $user['capabilities'])) {
                                                ?>

                                                <td style="width:90px;">
                                                    <div class="status">
                                                        <?php if ($arr['status'] == 1) { ?>
                                                            <span class="act-deact active">
                                                                <a class="vtip" title="Click this to make it Inactive" onclick="resourceStatusChange(<?php echo $arr['resource_id']; ?>, <?php echo $arr['resource_file_id']; ?>, <?php echo $arr['status'] == 0 ? 1 : 0; ?>)">
                                                                    <span class="active-link">Active</span>
                                                                </a>
                                                            </span>
                                                            <?php } else { ?>  
                                                            <span class="deactive">
                                                                <a class="vtip" title="Click this to make it Active" onclick="resourceStatusChange(<?php echo $arr['resource_id']; ?>, <?php echo $arr['resource_file_id']; ?>, <?php echo $arr['status'] == 0 ? 1 : 0; ?>)">
                                                                    <span class="deactive-link">Inactive</span>
                                                                </a>   
                                                            </span>
                                                        <?php } ?> 
                                                    </div> 
                                                </td>
                                                <td style="width:60px;">
                                                    <div class="actionOpt">                                                                                                                              
                                                        <a href="?controller=resource&action=editResource&ispop=1&resource_id=<?php echo $arr['resource_id']; ?>&resource_file_id=<?php echo $arr['resource_file_id']; ?>" class="edit-btn-resource vtip" title="Edit"><i class="vtip fa fa-pencil-square-o" title="Edit"></i></a>                                                    
                                                        <a href="?controller=resource&action=fileTree&ispop=1&resource_id=<?php echo $arr['resource_id']; ?>&resource_file_id=<?php echo $arr['resource_file_id']; ?>" class="tree-btn-resource vtip execUrl" title="View in tree format"><i class="vtip fa glyphicon glyphicon-list-alt" ></i></a>                                                    
                                                    </div>
                                                </td>                                                                    
                                            <?php
                                            } else {
                                               //if ($arr['status'] == 1) {
                                                   ?>
                                                <td style="width:90px;"></td>
                                                <td style="width:60px;"></td>
                                                <?php
                                                //}
                                            }
                                            ?>
                                            </tr>
                                                <?php }
                                                }
                                                }
                                                ?>
                                        </tbody>
                                    </table>
                                        <?php }else{
                                        $i = 0;
                                        $ul_flag = 0;
                                        $child_file = 0;
                                        $tree = treeView($directory_list, $user, $fileList,$logList);
                                        }


                                        
                                            ?>
                                    </li>
                                </ul> 
                            </div>
                        </dl>
                    </div>
                    <div class="ajaxMsg" id="createresource"></div>
                    <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                </form>
            </div>
            
            <?php } 
                                    function treeView($Categorys, $user = array(), $fileList,$logList = array()) {
                                            $i = 0;
                                            foreach ($Categorys as $Category) {
                                                $i++;
                                                ?>
                                                <ul> 
                                                    <li class="scndLvl">
                                                        <div class="expCol ">
                                                            <img alt="" class="collapse" src="<?php echo SITEURL; ?>public/images/Plus.png" alt="">
                                                            <img alt="" class="expand" src="<?php echo SITEURL; ?>public/images/Minus.png" alt="">
                                                        </div>
                                                        <div class="treeCont">
                                                            <div class="nameWrap expColcont"><span class="vtip expandName1" title="<?php echo $Category['directory_name']; ?>"><?php echo $Category['directory_name']; ?></span></div>
                                                        </div>

                                                        <?php
                                                        if (isset($Category['children']) && count($Category['children'])) {
                                                            
                                                            $childernFile = isset($fileList[$Category['directory_id']])?$fileList[$Category['directory_id']]:array();
                                                            childView($Category,0, 0, 1, $user, $fileList, $childernFile, 1,$logList);
                                                            //fileView($fileList[$Category['directory_id']], 0, $user);
                                                        } else {
                                                            //echo "</li></ul>";
                                                            if (isset($fileList[$Category['directory_id']]) && count($fileList[$Category['directory_id']])) {
                                                                //childView($Category, 0,1, $user,$fileList);
                                                               // echo '<li>';
                                                                echo '<ul style="display: block;">';
                                                                fileView($fileList[$Category['directory_id']], 0, $user,$fileList,$logList);
                                                                echo '</ul>';
                                                            } 
                                                            echo "</li></ul>";
                                                        }

                                                         
                                                    }
                                                }

                                                function childView($Category,$children, $count = 0, $ul_flag = 0, $user = array(), $fileList, $root_file, $is_files,$logList = array()) {
                                                    if($children != 1) {
                                                        
                                                       if ($ul_flag != 1) {
                                                           echo '<ul style="display: none;">';
                                                       } else {
                                                           echo '<ul style="display: block;">';
                                                       }
                                                    }else {
                                                        echo '<li><ul>';
                                                    }
                                                    if ($is_files) {
                                                        fileView($root_file, 0, $user,$fileList,$logList);
                                                    }
                                                    foreach ($Category['children'] as $arr) {
                                                        $empty_folder = 0;
                                                        if (isset($arr['children']) && count($arr['children'])) {
                                                            ?>  <li class="scndLvl <?php echo $arr['directory_id']; ?>"><div class="expCol parent-<?php echo $arr['directory_id']; ?>">
                                                                <img alt="d" class="collapse" src="<?php echo SITEURL; ?>public/images/Minus.png" />
                                                                <img alt="f" class="expand" src="<?php echo SITEURL; ?>public/images/Plus.png" />
                                                            </div>
                                                            <div class="treeCont">
                                                                <?php if (in_array("upload_resources", $user['capabilities'])) { ?>
                                                                <div class="actionOpt action-<?php echo $arr['directory_id']; ?>" >                                                                                                                              
                                                                  <a href="?controller=resource&action=updateFolder&ispop=1&directory_id=<?php echo $arr['directory_id']; ?>" class="edit-btn-resource vtip editfolder " id="<?php echo $arr['directory_id']; ?>" title="Edit"><i class="vtip fa fa-pencil-square-o" title="Edit"></i></a>                                                    
                                                                </div>
                                                                <?php } ?>
                                                                <div class="nameWrap expColcont "><i class="fa fa-folder-o child-<?php echo $arr['directory_id']; ?>" style=" display: none"></i><span class="vtip expandName" title="<?php echo $arr['directory_name']; ?>"><?php echo $arr['directory_name']; ?></span></div>
                                                                
                                                            </div>
                                                            <?php
                                                            // childView($arr, 1,$ul_flag,$user,$fileList);
                                                            if (isset($fileList[$arr['directory_id']])) {
                                                                // echo '</li><li  style = "display:block">';
                                                                echo '<ul style="display: none;">';
                                                                fileView($fileList[$arr['directory_id']], 0, $user, $fileList,$logList);
                                                                $ul_flag = 0;
                                                                $children = 0;
                                                                if(isset($arr['children'])) {
                                                                    //die('aaaa');
                                                                    $children = 1;
                                                                }else if($children == 1) {
                                                                    echo "</ul></li>";
                                                                }
                                                                childView($arr, $children,1, $ul_flag, $user, $fileList, $root_file, 0,$logList);
                                                                echo '</ul>';
                                                            } else {
                                                                //echo '<ul style="display: none;">';
                                                                // fileView($arr['files'], 0, $user);
                                                                $ul_flag = 0;
                                                                 
                                                                $children = 0;
                                                                childView($arr,$children, 1, $ul_flag, $user, $fileList, $root_file, 0,$logList);
                                                                $ul_flag = 0;
                                                            }
                                                        } else {
                                                            ?><li class="scndLvl <?php echo $arr['directory_id']; ?>">
                                                            <?php if (isset($fileList[$arr['directory_id']])){                                                               
                                                                ?> 
                                                                <div class="expCol ">
                                                                    <img alt="" class="collapse" src="<?php echo SITEURL; ?>public/images/Minus.png" />
                                                                    <img alt="" class="expand" src="<?php echo SITEURL; ?>public/images/Plus.png" />
                                                                </div>
                                                            <?php }else {
                                                                 $empty_folder = 1;
                                                            } ?>
                                                            <?php //if (isset($arr['files']) && count($arr['files'])) {  ?>
                                                            <div class="treeCont">                                                                
                                                                <?php if (in_array("upload_resources", $user['capabilities'])) {
                                                                            if (!isset($fileList[$arr['directory_id']])){ ?> 
                                                                    <div class="actionOpt" >                                                                                                                              
                                                                        <a href="?controller=resource&action=updateFolder&ispop=1&directory_id=<?php echo $arr['directory_id']; ?>" class="edit-btn-resource vtip editfolder " id="<?php echo $arr['directory_id']; ?>" title="Edit"><i class="vtip fa fa-pencil-square-o" title="Edit"></i></a>                                                    
                                                                        <a href="#" class="delete-btn-resource vtip delfolder" id="<?php echo $arr['directory_id']; ?>" title="Delete"><i class="edit-btn-resource vtip fa fa-trash-o" title="Delete"></i></a>                                                    
                                                                    </div>
                                                                <?php }else { ?>
                                                                    <div class="actionOpt  ">                                                                                                                              
                                                                        <a href="?controller=resource&action=updateFolder&ispop=1&directory_id=<?php echo $arr['directory_id']; ?>" class="edit-btn-resource vtip editfolder " id="<?php echo $arr['directory_id']; ?>" title="Edit"><i class="vtip fa fa-pencil-square-o" title="Edit"></i></a>                                                    
                                                                    </div>
                                                                <?php }
                                                                }
                                                                ?>
                                                                <div class="nameWrap <?php echo $arr['directory_id']; ?>" ><?php echo $empty_folder == 1?'<i class="fa fa-folder-o"></i>':''?><span class="vtip expandName1" title="<?php echo $arr['directory_name']; ?>"><?php echo $arr['directory_name']; ?></span></div>
                                                            </div> <?php //} ?>
                                                                <?php
                                                                if (!isset($fileList[$arr['directory_id']])) {
                                                                    echo "</li>";
                                                                } else {
                                                                    echo '<ul style="display: none;">';
                                                                    fileView($fileList[$arr['directory_id']], 0, $user, $fileList,$logList);
                                                                    echo '</ul>';
                                                                }
                                                            }
                                                        }
                                                        echo "</ul>";
                                                    }

                                                    function fileView($files, $count = 0, $user = array(),$fileList,$logList = array()) {
                                                        //print_r($files);die;
                                                        //  echo '<ul style="display: block;">';
                                                        foreach ($files as $arr) {
                                                            // print_r($arr);die;
                                                            $nArr = explode(".", $arr['file_name']);
                                                            $ext = strtolower(array_pop($nArr));
                                                            // if (isset($arr['children']) && count($arr['children'])) {
                                                            ?>  
                                                            <li>
                                                                <div class="table-responsive">
                                                                    <table class="fileview tableData">
                                                                        <tbody>
                                                                            <tr>                                                                         
                                                                            <td><div class="filename1 "> <a href="<?php echo $arr['resource_link_type']=="url"?"".$arr['resource_url']."":"".SITEURL."index.php?controller=resource&action=forcedDownload&file_id=".$arr['file_id']."&d_file=".$arr['resource_title']."&resource_id=".$arr['resource_id']?>" class="vtip" target="_blank" title="<?php echo $arr['resource_title']; ?>"><?php echo $arr['resource_title']; ?></a></div></td>
                                                                            <!-- <td style="width:28px;"><div class="filedata"> </div></td>
                                                                            <td style="width:92px;"><div class="filedata"> </div></td> -->
                                                                            <td style="width:84px;"><div class="filedata"> <?php echo!empty($arr['file_size']) ? $arr['file_size'] : " "; ?></div></td>
                                                                        <?php if (in_array("upload_resources", $user['capabilities'])) {
                                                                            ?>
                                                                        
                                                                            <td style="width:90px;">
                                                                                <div class="status" >
                                                                                    <?php if ($arr['status'] == 1) { ?>
                                                                                        <span class="act-deact active">
                                                                                            <a class="vtip" title="Click this to make it Inactive" onclick="resourceStatusChange(<?php echo $arr['resource_id']; ?>, <?php echo $arr['resource_file_id']; ?>, <?php echo $arr['status'] == 0 ? 1 : 0; ?>)">
                                                                                                <span class="active-link">Active</span>
                                                                                            </a>
                                                                                        </span>
                                                                                        <?php } else { ?>  
                                                                                        <span class="deactive">
                                                                                            <a class="vtip" title="Click this to make it Active" onclick="resourceStatusChange(<?php echo $arr['resource_id']; ?>, <?php echo $arr['resource_file_id']; ?>, <?php echo $arr['status'] == 0 ? 1 : 0; ?>)">
                                                                                                <span class="deactive-link">Inactive</span>
                                                                                            </a>   
                                                                                        </span>
                                                                                    <?php } ?> 
                                                                                </div> 
                                                                            </td>
                                                                            <td style="width:60px;">
                                                                                <div class="actionOpt">                                                                                                                              
                                                                                    <a href="?controller=resource&action=editResource&ispop=1&resource_id=<?php echo $arr['resource_id']; ?>&resource_file_id=<?php echo $arr['resource_file_id']; ?>" class="edit-btn-resource vtip" title="Edit"><i class="vtip fa fa-pencil-square-o" title="Edit"></i></a>                                                    
                                                                                    <?php if(!empty($logList) && in_array($arr['resource_id'], $logList)) { ?>
                                                                                        <a href="?controller=resource&action=resourceDownloadHistory&ispop=1&resource_id=<?php echo $arr['resource_id']; ?>&resource_file_id=<?php echo $arr['resource_file_id']; ?>" class="edit-btn-resource vtip dwndhistory" id="<?php echo $arr['resource_id']; ?>"  title="Get resource download history"><i class="vtip fa fa-print" title="Edit"></i></a>                                                    
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </td>                                                                    
                                                                        <?php
                                                                        } else {
                                                                           //if ($arr['status'] == 1) {
                                                                               ?>
                                                                            <td style="width:90px;"></td>
                                                                            <td style="width:60px;"></td>
                                                                            <?php
                                                                            //}
                                                                        }
                                                                        ?>
                                                                        <tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </li>
                                                    <?php
                                                    //childView($arr, 1);
                                                    //} 
                                                }
                                                //  echo "</ul>";
                                            }
            
            ?>
        </div>
    </div>
 <?php
if(isset($pages) && isset($filedata) )
    echo $this->generateAjaxPaging($pages, $cPage); ?>
    <!-- Initialize the plugin: -->
    <script type="text/javascript">
        $(window).load(function() {
             $(".loader").fadeOut("slow");
              //$(".box").fadeIn("slow");
        });
        $(document).ready(function () {
            $(".vertScrollArea").mCustomScrollbar({theme: "dark"});            
        });
         
    </script>