<?php
$country_code = '';
$principal_phone_no = '';
if (isset($eClient['principal_phone_no'])) {
    $number = explode(")", $eClient['principal_phone_no']);

    if (isset($number[0]) && count($number) > 1) {
        $country_code = explode("+", $number[0]);
        $principal_phone_no = trim($number[1]);
    } else if (count($number) == 1) {
        $principal_phone_no = trim($number[0]);
    } else if (isset($number[1])) {
        $principal_phone_no = trim($number[1]);
    }
}
//print_r($country_code);
if (isset($eClient['client_id'])) {
    ?>
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
        Update School/College</h1>
    <div class="clr"></div>
    <div class="">
        <div class="ylwRibbonHldr">
            <div class="tabitemsHldr"></div>
        </div>
        <div class="subTabWorkspace pad26">
            <div class="form-stmnt">
                <form method="post" id="edit_school_form" action="">
                    <div class="boxBody">
                        <dl class="fldList">
                            <dt class="nobr">Type of Institution<span class="astric">*</span>:</dt>
                            <dd><div class="row"><div class="col-sm-6"><select class="form-control" name="client_institution_id" id="client_institution_id" required>
                                            <option value=""> - Select Institution Type - </option>
                                            <?php
                                            foreach ($client_institution_type as $c_type)
                                                echo $eClient['client_institution_id'] == $c_type['client_institution_id'] ? "<option value=\"" . $c_type['client_institution_id'] . "\" selected=\"selected\">" . $c_type['institution'] . "</option>\n" : "<option value=\"" . $c_type['client_institution_id'] . "\">" . $c_type['institution'] . "</option>\n";
                                            ?>
                                        </select></div></div></dd>
                        </dl>
                        <dl class="fldList">
                            <dt class="nobr">School/College Name<span class="astric">*</span>:</dt>
                            <dd><div class="row"><div class="col-sm-6"><input type="text" class="form-control" value="<?php echo $eClient['client_name']; ?>" name="client_name" required /></div></div></dd>
                        </dl>



                        <dl class="fldList">
                            <dt class="nobr">School/College Address<span class="astric">*</span>:</dt>
                            <dd><div class="row"><div class="col-sm-6">
                                        <input type="text" class="form-control mb5" placeholder="Address Line 1" value="<?php echo $eClient['street']; ?>" name="street" required />	
                                        <input type="text" class="form-control mb5" placeholder="Address Line 2" value="<?php echo $eClient['addressLine2']; ?>" name="addrline2" />										
                                        <!--<input type="text" class="form-control" placeholder="State" value="" name="state" required />-->
                                        <select class="form-control" name="country" id="country_id" required>
                                            <option value=""> - Select Country - </option>
                                            <?php
                                            foreach ($countries as $country)
                                                print $eClient['country_id'] == $country['country_id'] ? "<option selected=\"selected\" value=\"" . $country['country_id'] . "\">" . $country['country_name'] . "</option>\n" : "<option  value=\"" . $country['country_id'] . "\">" . $country['country_name'] . "</option>\n";
                                            ?>
                                        </select>
                                        <select class="form-control" name="state" id="state_id" required>
                                            <option value=""> - Select State - </option>	
                                            <?php
                                            foreach ($states as $state)
                                                print $eClient['state_id'] == $state['state_id'] ? "<option selected=\"selected\" value=\"" . $state['state_id'] . "\">" . $state['state_name'] . "</option>\n" : "<option  value=\"" . $state['state_id'] . "\">" . $state['state_name'] . "</option>\n";
                                            ?>											
                                        </select>
                                        <select class="form-control" name="city" id="city_id" required>
                                            <option value=""> - Select City - </option>		
                                            <?php
                                            foreach ($cities as $city)
                                                print $eClient['city_id'] == $city['city_id'] ? "<option selected=\"selected\" value=\"" . $city['city_id'] . "\">" . $city['city_name'] . "</option>\n" : "<option  value=\"" . $city['city_id'] . "\">" . $city['city_name'] . "</option>\n";
                                            ?>											
                                        </select>											
                                    </div></div></dd>
                        </dl>




                        <dl class="fldList">
                            <dt class="nobr">Principal Name<span class="astric">*</span>:</dt>
                            <dd><div class="row"><div class="col-sm-6"><input type="text" class="form-control" value="<?php echo $principal['name']; ?>" name="principal_name" required /></div></div></dd>
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
                                                                <?php echo!empty($country_code[1]) && $country_code[1] == $value['phonecode'] ? 'Selected' : '' ?>>
                                                            <?php echo "(+".$value['phonecode'] .") ";?></option>
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
                                                <input type="text" class="form-control mask_ph "  name="phone" id="phone"  value="<?php echo!empty($principal_phone_no) ? str_replace("-", '', $principal_phone_no) : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </dd>
                        </dl>

                        <dl class="fldList">
                            <dt class="nobr">Remarks:</dt>
                            <dd><div class="row"><div class="col-sm-6"><textarea name="remarks" class="form-control" ><?php echo $eClient['remarks']; ?></textarea></div></div></dd>
                        </dl>

                        <dl class="fldList">
                            <dt>Is the school/college part of a network<span class="astric">*</span>:</dt>
                            <dd>
                                <div class="clearfix">
                                    <div class="chkHldr autoW"><input type="radio" class="haveNetwork" <?php echo $eClient['network_id'] > 0 ? 'checked="checked"' : ""; ?> name="haveNetwork" value="1"><label class="chkF radio"><span>Yes</span></label></div>
                                    <div class="chkHldr autoW"><input type="radio" class="haveNetwork" <?php echo $eClient['network_id'] > 0 ? "" : 'checked="checked"'; ?> name="haveNetwork" value="0"><label class="chkF radio"><span>No</span></label></div>
                                </div>
                            </dd>
                        </dl>

                        <dl id="networks" <?php echo $eClient['network_id'] > 0 ? '' : 'style="display:none;"'; ?> class="fldList">
                            <dt>Network<span class="astric">*</span>:</dt>
                            <dd><div class="row"><div class="col-sm-6"><select class="form-control" name="network" id="edit_scl_network">
                                            <option value=""> - Select Network - </option>
                                            <?php
                                            foreach ($networks as $network)
                                                echo "<option " . ($eClient['network_id'] == $network['network_id'] ? 'selected="selected"' : "") . " value=\"" . $network['network_id'] . "\">" . $network['network_name'] . "</option>\n";
                                            ?>
                                        </select></div></div></dd>
                        </dl>
                        <dl id="provinces" <?php echo $eClient['network_id'] > 0 ? '' : 'style="display:none;"'; ?> class="fldList">
                            <dt>Province:</dt>
                            <dd><div class="row"><div class="col-sm-6"><select class="form-control province-list-dropdown" name="province">
                                            <option value=""> - Select Province - </option>
                                            <?php
                                            foreach ($provinces as $province)
                                                echo "<option " . ($eClient['province_id'] == $province['province_id'] ? 'selected="selected"' : "") . " value=\"" . $province['province_id'] . "\">" . $province['province_name'] . "</option>\n";
                                            ?>
                                        </select></div></div></dd>
                        </dl>

                        <dl class="fldList">
                            <dt></dt>
                            <dd class="nobg">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <br>
                                        <input type="submit" value="Update" class="btn btn-primary">
                                        <input type="hidden" value="<?php echo $eClient['client_id']; ?>" name="id" />
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
<?php } else { ?>
    <h1>School does not exist</h1>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function () {
        //$('.mask_ph').mask("(+99) 999-9999-999");
    });
</script>