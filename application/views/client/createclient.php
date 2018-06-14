<h1 class="page-title">
    <?php if ($isPop == 0) { ?>
        <a href="<?php
        $args = array("controller" => "client", "action" => "client");
        echo createUrl($args);
        ?>">
            <i class="fa fa-chevron-circle-left vtip" title="Back"></i>
            Manage Schools/Colleges
        </a>>	
    <?php } ?>				
    Add School/College</h1>
<div class="clr"></div>
<div class="">
    <div class="ylwRibbonHldr">
        <div class="tabitemsHldr"></div>
    </div>
    <div class="subTabWorkspace pad26">
        <div class="form-stmnt">
            <form method="post" id="create_school_form" action="">
                <div class="boxBody">
                    <dl class="fldList">
                        <dt class="nobr">Type of Institution<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6"><select class="form-control" name="client_institution_id" id="client_institution_id" required>
                                        <option value=""> - Select Institution Type - </option>
                                        <?php
                                        foreach ($client_institution_type as $c_type)
                                            echo "<option value=\"" . $c_type['client_institution_id'] . "\">" . $c_type['institution'] . "</option>\n";
                                        ?>
                                    </select></div></div></dd>
                    </dl>
                    <dl class="fldList">
                        <dt  class="nobr">School/College Name<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6"><input type="text" class="form-control" value="" name="client_name" required /></div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt class="nobr">School/College Address<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6">
                                    <input type="text" class="form-control mb5" placeholder="Address Line 1" value="" name="street" required />	
                                    <input type="text" class="form-control mb5" placeholder="Address Line 2" value="" name="addrline2" />										
                                    <!--<input type="text" class="form-control" placeholder="State" value="" name="state" required />-->
                                    <select class="form-control" name="country" id="country_id" required>
                                        <option value=""> - Select Country - </option>
                                        <?php
                                        foreach ($countries as $country)
                                            echo "<option value=\"" . $country['country_id'] . "\">" . $country['country_name'] . "</option>\n";
                                        ?>
                                    </select>
                                    <select class="form-control" name="state" id="state_id" required>
                                        <option value=""> - Select State - </option>												
                                    </select>
                                    <select class="form-control" name="city" id="city_id" required>
                                        <option value=""> - Select City - </option>												
                                    </select>											
                                </div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt class="nobr">Principal Name<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6"><input type="text" class="form-control" value="" name="principal_name" required /></div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt class="nobr">Email ID<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6"><input type="email" class="form-control" value="" placeholder="this will be the username" name="email" required /></div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt class="nobr">Phone Number:</dt>
                        <dd>
                            <div class="inlContBox ftySixty">
                                <div class="inlCBItm fty">
                                    <div class="fld blk">
                                        <div>
                                            <select name="country_code" id="country_code" class="form-control" >
                                                <?php
                                                foreach ($countryCodeList as $value) {
                                                    ?>
                                                    <option value="<?php echo $value['phonecode'] ?>"
                                                            <?php echo!empty($eUser['school_contact_number']) && $sc_country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
                                                            <?php echo "(+".$value['phonecode'] .") "; ?></option>
                                                        <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="inlCBItm sixty">
                                    <div class="fld">
                                        <div>
                                            <input type="text" class="form-control mask_ph "  name="phone" id="phone"  value="<?php echo!empty($school_contact_number) ? str_replace("-", '', $school_contact_number) : '' ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </dd>   
                    </dl>

                    <dl class="fldList">
                        <dt class="nobr">Password<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6"><input type="password" class="form-control pwd" value="" name="password" required /></div></div></dd>
                    </dl>
                    <dl class="fldList">
                        <dt class="nobr">Confirm Password<span class="astric">*</span>:</dt>
                        <dd><div class="row"><div class="col-sm-6"><input type="password" class="form-control cpwd" value="" required /></div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt class="nobr">Remarks:</dt>
                        <dd><div class="row"><div class="col-sm-6"><textarea name="remarks" class="form-control" ></textarea></div></div></dd>
                    </dl>

                    <dl class="fldList">
                        <dt>Is the school/college part of a network<span class="astric">*</span>:</dt>
                        <dd>
                            <div class="clearfix">
                                <div class="chkHldr autoW"><input type="radio" class="haveNetwork" name="haveNetwork" value="1"><label class="chkF radio"><span>Yes</span></label></div>
                                <div class="chkHldr autoW"><input type="radio" class="haveNetwork" checked="checked" name="haveNetwork" value="0"><label class="chkF radio"><span>No</span></label></div>
                            </div>
                        </dd>
                    </dl>

                    <dl id="networks" style="display:none;" class="fldList">
                        <dt>Network<span class="astric">*</span>:</dt>
                        <dd>
                            <div class="row">
                                <div class="col-sm-6 width-50-modal">
                                    <select class="form-control" id="scl_network" name="network">
                                        <option value=""> - Select Network - </option>
                                        <?php
                                        foreach ($networks as $network)
                                            echo "<option value=\"" . $network['network_id'] . "\">" . $network['network_name'] . "</option>\n";
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 width-50-modal">
                                    <a href="?controller=network&action=createNetwork&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add network." id="addNetworkBtn">Add New</a>
                                </div>
                            </div>
                        </dd>
                    </dl>
                    <dl id="provinces" style="display:none;" class="fldList">
                        <dt>Province:</dt>
                        <dd>
                            <div class="row">
                                <div class="col-sm-6 width-50-modal">
                                    <select class="form-control province-list-dropdown" name="province">
                                        <option value=""> - Select Province - </option>														
                                    </select>
                                </div>
                                <!--<div class="col-sm-3 width-50-modal">
                                        <a href="?controller=network&action=createProvince&amp;ispop=1" class="btn btn-primary execUrl vtip" title="Click to add province" id="addProvinceBtn">Add New</a>
                                </div>-->
                            </div>
                        </dd>
                    </dl>

                    <dl class="fldList">
                        <dt></dt>
                        <dd class="nobg">
                            <div class="row">
                                <div class="col-sm-6">
                                    <br>
                                    <input type="submit" value="Add" class="btn btn-primary">
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
<script type="text/javascript">
    $(document).ready(function () {
        //$('.mask_ph').mask("(+99) 999-9999-999");
    });
</script>