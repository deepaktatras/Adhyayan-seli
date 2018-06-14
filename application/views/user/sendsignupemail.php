<h1 class="page-title">Invite User for Signup</h1>
<div class="clr"></div>
<div class="">
    <div class="ylwRibbonHldr">
        <div class="tabitemsHldr"></div>
    </div>
    <div class="subTabWorkspace pad26">
        <div class="form-stmnt">
            <form method="post" id="send_signup_email" action="" name="send_signup_email">
                <div class="boxBody">
                    <dl class="fldList">
                        <dt>Email ID<span class="astric">*</span>:</dt>
                        <dd>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="email" class="form-control" placeholder="Enter Email" name="email" id="email"
                                           value="<?php echo !empty($email)?$email:''?>" required autocomplete="off"
                                           <?php echo !empty($email)?'readonly':''?>/>
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
                                    <input type="hidden" name="id" id="id" value="<?php echo $_REQUEST['id']?>"/>
                                    <input type="submit" value="Send Email" class="btn btn-primary">
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