<?php
if (isset($eUser['user_id'])) {
    ?>

    <h1 class="page-title">
        <?php if ($isPop == 0) { ?>
            <a href="<?php
            $args = array("controller" => "user", "action" => "user");
            echo createUrl($args);
            ?>">
                <i class="fa fa-chevron-circle-left vtip" title="Back"></i>

                Manage Users
            </a> >
        <?php } ?>	
        Update Password

    </h1>
    <div class="clr"></div>
    <div class="">
        <div class="ylwRibbonHldr">
            <div class="tabitemsHldr"></div>
        </div>
        <div class="subTabWorkspace pad26">
            <div class="form-stmnt">
                <form method="post" id="update_user_password" action="">
                    <div class="boxBody">
                        <dl class="fldList">
                            <dt>
                                New Password<span class="astric">*</span>:
                            </dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control pwd" value="" name="password" />
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <dl class="fldList">
                            <dt>
                                Confirm Password<span class="astric">*</span>:
                            </dt>
                            <dd>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control cpwd" value="" />
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
                                        <input type="submit" value="Update Password" class="btn btn-primary">
                                        <input type="hidden" value="<?php echo $eUser['user_id']; ?>" name="id" />
                                    </div>
                                </div>
                            </dd>
                        </dl>
                    </div>
                    <div class="ajaxMsg"></div>
                    <input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
                </form>
            </div>
        </div>
    </div>
<?php 

} else { ?>
    <h1>User does not exist</h1>
<?php 
} ?>