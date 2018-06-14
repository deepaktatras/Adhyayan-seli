<?php
error_reporting(E_ALL);
		ini_set('display_errors','On');
if ($kpa_id > 0) {
    $isReadOnly = empty($isReadOnly) ? 0 : 1;
    $readOnlyText = $isReadOnly ? 'readonly="readonly"' : "";
    $disabledText = $isReadOnly ? 'disabled="disabled"' : "";
    $numToAlph = array(1 => "a", 2 => "b", 3 => "c", 4 => "d");
    $cq_no_inKpa = 0;
    ?>
    <div role="tabpanel" data-tabtype="kpa" data-id="<?php echo $kpa_id; ?>" class="tab-pane fade in kpa<?php echo $isActive ? " active" : ""; ?>" id="kpa<?php echo $kpa_id; ?>">
        <h2 class="pad030 mb10 related"><?php if ($image_name != '') { ?>

                <img src="<?php echo UPLOAD_URL_DIAGNOSTIC . '' . $image_name ?>" alt="<?php echo $image_name; ?>" id="resizable" class="resizable ui-widget-content">

                <input type="hidden" id="dig_image_id" name="dig_image_id" value="<?php echo $imageId ? $imageId : NULL; ?>" >
            <?php } ?>
            <?php echo $kpa['kpa_name']; ?></h2>
        <div class='tab2Hldr'>

            <div class="tabitemsHldr">
                <a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab3_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
                <div class="collapse navbar-collapse tabitemsHldr" id="tab3_Toggle">
                <?php if (!empty($kqs[$kpa_id])) { ?>
                    <ul class="yellowTab nav nav-tabs"> 
                        <?php
                        $i = 0;
                        //echo "dd: ";print_r($kqs);
                        foreach ($kqs[$kpa_id] as $kq_id => $kq) {
                            $i++;
                            ?>
                            <li class="item<?php echo $i == 1 ? " active" : ""; ?>"><a href="#kq<?php echo $kq_id; ?>" data-toggle="tab" class="vtip" title="<?php echo htmlspecialchars($kq['key_question_text']); ?>"><?php echo $diagnosticLabels['Key_Question']; ?> <?php echo $i; ?></a></li>
                            <?php
                        }
                        ?>													
                    </ul>
                <?php } ?>
                </div>
                <div class="flotedInTab add">
                    <a href="?controller=diagnostic&action=addmoreform&type=kq&assessmentId=<?php echo $assessmentId; ?>&diagnosticId=<?php echo $diagnosticId; ?>&kpaId=<?php echo $kpa_id; ?>&langId=<?php echo $langId; ?>&parentId=<?php echo $parentId;?>&equivalenceId=<?php echo $equivalenceId;?>&langIdOriginal=<?php echo $langIdOriginal;?>" data-postformid="for" class="vtip execUrl" title="Click to Add KQs" data-toggle="modal" data-target="#addMoreKPA" data-size="800" data-validator="isDiagnosticName"><i class="fa fa-plus-circle"></i>Add KQ</a>                	
                </div>	
                <input type="hidden" autocomplete="off" name="kpaId" id="kpaId" value="<?php echo $kpa_id; ?>" />
            </div>
        </div>

        <div class="subTabWorkspaceOuter">
            <div class="subTabWorkspace pt10">
                <div class="tab-content">
                    <?php if (!empty($kqs[$kpa_id])) { ?>
                        <?php
                        $kq_no = 0;
                        foreach ($kqs[$kpa_id] as $kq_id => $kq) {
                            $kq_no++;
                            ?>                                        
                            <div role="tabpanel" data-tabtype="keyQ" data-id="<?php echo $kq_id; ?>" class="tab-pane fade in keyQ<?php echo $kq_no == 1 ? " active" : ""; ?>" id="kq<?php echo $kq_id; ?>">
                                <h3 class="related mb10"><?php echo $kq['key_question_text']; ?></h3>
                                <div class="tab3Hldr">
                                    <div class="tabitemsHldr" id="tab3_Toggle">
                                        <a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab4_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
                                        <div class="collapse navbar-collapse tabitemsHldr" id="tab4_Toggle">
                                        <?php if (!empty($cqs[$kq_id])) { ?> 
                                            <ul class="blackTab nav nav-tabs">													  
                                                <?php
                                                $i = 0;
                                                foreach ($cqs[$kq_id] as $cq_id => $cq) {
                                                    $i++;
                                                    ?>
                                                    <li class="item<?php echo $i == 1 ? " active" : ""; ?>"><a href="#kqA-cq<?php echo $cq_id; ?>" data-toggle="tab" class="vtip" title="<?php echo htmlspecialchars($cq['core_question_text']); ?>"><?php echo $diagnosticLabels['Sub_Question'] ?> <?php echo $i; ?></a></li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        <?php } ?> 
                                        </div>
                                        <div class="flotedInTab add">
                                            <a href="?controller=diagnostic&action=addmoreform&type=cq&assessmentId=<?php echo $assessmentId; ?>&diagnosticId=<?php echo $diagnosticId; ?>&kpaId=<?php echo $kpa_id; ?>&kqid=<?php echo $kq_id; ?>&langId=<?php echo $langId; ?>&parentId=<?php echo $parentId;?>&equivalenceId=<?php echo $equivalenceId;?>&langIdOriginal=<?php echo $langIdOriginal;?>" data-postformid="for" class="vtip execUrl" title="Click to add SQs" data-toggle="modal" data-target="#addMoreKPA" title="KPAs" data-size="800" data-validator="isDiagnosticName"><i class="fa fa-plus-circle"></i>Add SQ</a>                                       		
                                        </div>
                                    </div>
                                </div>
                                <div class="panelWhitebox pt10">
                                    <div class="tab-content">
                                        <?php if (!empty($cqs[$kq_id])) { ?> 
                                            <?php
                                            $cq_no = 0;
                                            foreach ($cqs[$kq_id] as $cq_id => $cq) {
                                                $cq_no++;
                                                $cq_no_inKpa++;
                                                ?>                                                    
                                                <div role="tabpanel" data-tabtype="coreQ" data-id="<?php echo $cq_id; ?>" class="tab-pane fade in coreQ<?php echo $cq_no == 1 ? " active" : ""; ?>" id="kqA-cq<?php echo $cq_id; ?>">
                                                    <div class="queryBoxWrapper">
                                                        <div class="row mb10">
                                                            <div class="col-md-10"><h3 class="pull-left related mb10"><?php echo $cq['core_question_text']; ?></h3></div>
                                                            <div class="col-md-2 text-right">
                                                            	<a href="?controller=diagnostic&action=addmoreform&type=jss&assessmentId=<?php echo $assessmentId; ?>&diagnosticId=<?php echo $diagnosticId; ?>&kpaId=<?php echo $kpa_id; ?>&kqid=<?php echo $kq_id; ?>&cqId=<?php echo $cq_id; ?>&langId=<?php echo $langId; ?>&parentId=<?php echo $parentId;?>&equivalenceId=<?php echo $equivalenceId;?>&langIdOriginal=<?php echo $langIdOriginal;?>" data-postformid="for" class="execUrl btn btn-primary vtip" title="Click to add JSs" data-toggle="modal" data-target="#addMoreKPA" title="KPAs" data-size="800" data-validator="isDiagnosticName"><i class="fa fa-plus-circle"></i>Add JS</a>                                                            														          	
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12"> 
                                                                <div class="leftQuestHldr ">
                                                                    <?php if (!empty($jss[$cq_id])) { ?> 
                                                                        <ul id="sortable1" class="connectedSortable">
                                                                            <?php
                                                                            $js_no = 0;
                                                                            foreach ($jss[$cq_id] as $js_id => $js) {
                                                                                $js_no++;
                                                                                $name = "data[$kpa_id-$kq_id-$cq_id-$js_id]";
                                                                                ?>
                                                                                <li><?php echo $cq_no_inKpa . $numToAlph[$js_no] . ". " . $js['judgement_statement_text']; ?></li>                                                                            
                                                                                <?php
                                                                            }
                                                                            ?>	
                                                                        </ul>
                                                                    <?php } ?>	
                                                                </div>
                                                            </div>
                                                        </div>                                            
                                                    </div>
                                                </div>    
                                            <?php } ?>
                                        <?php } ?> 	
                                    </div>
                                </div>
                            </div>                                       
                        <?php } ?>    
                    <?php } ?>    										
                </div>                                    
            </div>                                
        </div>
    </div>
<?php } ?>