<h1 class="page-title">
    <?php if (isset($isPop) && $isPop == 0) { ?>
        <a href="<?php
        $args = array("controller" => "user", "action" => "user");
        echo createUrl($args);
        ?>">
            <i class="fa fa-chevron-circle-left vtip" title="Back"></i>

            Manage <?php
            if (current($user['role_ids']) == 8) {
                echo 'Assessors';
            } else {
                echo 'Users';
            }
            ?>
        </a> &rarr;
    <?php } ?>	
    Import <?php echo current($user['role_ids']) == 8 ? 'Assessor' : 'User'; ?> Profile
</h1>
<div class="clr"></div>
<div class="">
    <div class="ylwRibbonHldr">
        <div class="tabitemsHldr"></div>
    </div>
    <div class="subTabWorkspace pad26">
        <div class="form-stmnt">
            <form method="post" enctype="multipart/form-data" id="user_excel_file_form" action="">
                <div class="boxBody">
                     <input type="hidden" name="login_user_id" id="login_user_id" value="<?php echo current($user['role_ids']) ?>"/>
                     
                    <dl class="fldList">
                        <dt>Attach User Profile Excel File<span class="astric">*</span>:</dt>
                        <dd><div class="row">
                                <div class="col-sm-8">
                                    <div class="file-up-wrapper clearfix">
                                        <div class="fileUpload filt-nupload btn btn-primary mr0 col-sm-4">
                                            <i class="glyphicon glyphicon-folder-open"></i> <span>Attach File</span>  
                                            <input type="file" class="upload uploadBtn" title="" name="user_excel_file" id="user_excel_file" autocomplete="off">
                                        </div>
                                        <div class="file-info">(Only excel file is allowed)</div>
                                    </div>
                                </div>                              
                            </div></dd>
                    </dl>
                     
                     <dl class="fldList">
                        <dt>Starting row no.<span class="astric">*</span>:</dt>
                        <dd><div class="row">
                                <div class="col-sm-6">
                                    <select name="start_row" class="form-control" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>            
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
                                    <input type="submit" value="Upload" class="btn btn-primary">
                                </div>
                            </div>
                        </dd>
                    </dl>
                </div>
                <div class="ajaxMsg"></div>
                <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                <?php 
//                $language_read = array('english', 'hindi');
//                $language_write = array('english');
//                $language_speak = array('hindi');
//                $language_list = array_unique(array_merge($language_read,$language_write,$language_speak), SORT_REGULAR);
//                print_r($language_list);?>
            </form>
        </div>
    </div>
</div>