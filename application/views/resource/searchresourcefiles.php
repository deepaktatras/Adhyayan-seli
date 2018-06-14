<?php //echo "<pre>";print_r($filedata);die; ?>
       
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
                                                <!-- <th style="width:30px"></th>
                                                <th style="width:90px"></th> -->
                                                <th style="width:84px"><span>File size</span></th>
                                                <th style="width:90px"><span>Status</span></th>
                                                <th style="width:70px"><span>Action</span></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="wfm vertScrollArea resources">
                                <ul clas<div class="table-responsive">
                                     <table class="fileview tableData">
                                            <tbody>
                                                <?php foreach($filedata as $arr) {  ?>
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
                                                <?php } ?>
                                        </tbody>
                                    </table>
                                                            </div>
                                    </li>
                                </ul> 
                            </div>
                        </dl>
                    </div>
                    <div class="ajaxMsg" id="createresource"></div>
                    <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                    <input type="hidden" name="search_file" id="search_file" class="ajaxFilter" value="<?php echo $searchValue; ?>" > 
                    
                </form>
            </div>
            
            <?php } 
                              


            
            ?>
        </div>
            <?php echo $this->generateAjaxPaging($pages, $cPage); ?>
