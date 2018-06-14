<?php $url=createUrl(array("controller"=>"diagnostic","action"=>"diagnostic"));
?>
<div class="filterByAjax diagnostic-list" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
    <h1 class="page-title">
        Diagnostics
        <?php if (in_array("manage_diagnostic", $user['capabilities'])) { ?>
            <a href="?controller=diagnostic&action=addDiagnostic" class="btn btn-primary pull-right">Create Diagnostic</a> 								
        <?php }  ?>
            <select class="langSel" name="lang" id="lang-d">
                <option id="<?php echo $url;?>" value="all">All</option>
                <?php foreach($diagnosticsLanguage as $val) { ?>
                <option id="<?php echo $url;?>" value="<?php echo $val['language_id'];?>" <?php echo (isset($preferredLanguage) && $preferredLanguage == $val['language_id'])?"selected":'';?> ><?php echo $val['language_words'];?></option>
                <!--<option value="hi">हिंदी</option>-->
                <?php } ?>
            </select>
            <?php 
           //echo '(<span data-lang="hi" class="langSel">हिंदी</span>&nbsp;<span data-lang="en" class="langSel">English</span>)';
        ?>
        
        <div class="clr"></div>
    </h1>


    <div class="asmntTypeContainer">						
        <?php
        $ajaxFilter = new ajaxFilter();
        $ajaxFilter->addTextbox("dia_name", $filterParam["name_like"], "Name");
        $ajaxFilter->addHiddenEtc("lang_id", $filterParam["lang_id"], "id='lang_id'");

        $ajaxFilter->addDropDown("isPublished", array(array("id" => "no", "value" => "Not Published"), array("id" => "yes", "value" => "Published")), 'id', 'value', $filterParam["isPublished"], "Status");
        $ajaxFilter->addDropDown("assessment_type_id", $assessment_types, 'assessment_type_id', 'assessment_type_name', $filterParam["assessment_type_id"], "Review Type", "bigger");
        
        $ajaxFilter->generateFilterBar(1);
        ?>

        <div class="tableHldr">
            <table class="cmnTable">
                <thead>
                    <tr>
                        <th data-value="name" class="sort <?php echo $orderBy == "name" ? "sorted_" . $orderType : ""; ?>">Diagnostic Name</th>
                        <th data-value="language_id" class="sort <?php echo $orderBy == "language_id" ? "sorted_" . $orderType : ""; ?>">Language</th>
                        <th data-value="isPublished" class="sort <?php echo $orderBy == "isPublished" ? "sorted_" . $orderType : ""; ?>">Is Published</th>
                        <th data-value="date_created" class="sort <?php echo $orderBy == "date_created" ? "sorted_" . $orderType : ""; ?>">Created on</th>
                        <th data-value="date_published" class="sort <?php echo $orderBy == "date_published" ? "sorted_" . $orderType : ""; ?>">Published on</th>
                        <th data-value="assessment_type" class="sort <?php echo $orderBy == "assessment_type" ? "sorted_" . $orderType : ""; ?>">Review Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($diagnostics))
                        foreach ($diagnostics as $diagnostic) {
                            ?>
                            <tr>
                                <td><?php echo $diagnostic['name']; ?></td>
                                <td><?php echo $diagnostic['language_name']; ?></td>
                                <td><?php echo $diagnostic['isPublished'] ? "Yes" : "No"; ?></td>
                                <td><?php echo ChangeFormat($diagnostic['date_created'],"d-m-Y H:i:s"); ?></td>
                                <td><?php echo ChangeFormat($diagnostic['date_published'],"d-m-Y H:i:s"); ?></td>
                                <td><?php echo $diagnostic['assessment_type_name']; ?></td>
                                <td><?php $diagnostic['isPublished'] ? print "<a href='?controller=diagnostic&action=diagnosticForm&id=" . $diagnostic['diagnostic_id'] . "&langId=".$diagnostic['language_id']."'>View</a>&nbsp;<a href='?controller=diagnostic&action=convertToWordFile&id=" . $diagnostic['diagnostic_id'] . "&langId=".$diagnostic['language_id']."' class='vtip' title='Download'><i class='fa fa-file-word-o' aria-hidden='true'></i></a>&nbsp;<a href='?controller=diagnostic&action=cloneDiagnostic&diagnostic_id=" . $diagnostic['diagnostic_id'] . "&langId=".$diagnostic['language_id']."' class='vtip execUrl' title='Click to clone the diagnostic'><i class='fa fa-files-o' aria-hidden='true'></i></a>" : print "<a href='?controller=diagnostic&action=addDiagnostic&assessmentId=" . $diagnostic['assessment_type_id'] . "&diagnosticId=" . $diagnostic['diagnostic_id'] . "&langId=".$diagnostic['language_id']."'>Edit</a>"; ?></td>
                                <!--<td><?php //$diagnostic['isPublished'] ? print "<a href='?controller=diagnostic&action=diagnosticForm&id=" . $diagnostic['diagnostic_id'] . "&langId=".$diagnostic['language_id']."'>View</a>&nbsp;<a href='?controller=diagnostic&action=convertToWordFile&id=" . $diagnostic['diagnostic_id'] . "&langId=".$diagnostic['language_id']."' class='vtip' title='Download'><i class='fa fa-file-word-o' aria-hidden='true'></i></a>&nbsp;" : print "<a href='?controller=diagnostic&action=addDiagnostic&assessmentId=" . $diagnostic['assessment_type_id'] . "&diagnosticId=" . $d<!--iagnostic['diagnostic_id'] . "&langId=".$diagnostic['language_id']."'>Edit</a>"; ?></td>-->
                            </tr>
                        <?php
                        } else {
                        ?>
                        <tr>
                            <td colspan="6">No diagnostic found</td>
                        </tr>
                        <?php }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="ajaxMsg"></div>
    </div>
</div>
