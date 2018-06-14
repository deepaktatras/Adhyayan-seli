<?php
   // echo "<pre>";print_r($diagnosticLabels);
if ($kpa_id > 0) {
    $isReadOnly = empty($isReadOnly) ? 0 : 1;
    $readOnlyText = $isReadOnly ? 'readonly="readonly"' : "";
    $disabledText = $isReadOnly ? 'disabled="disabled"' : "";
    $numToAlph = array(1 => "a", 2 => "b", 3 => "c", 4 => "d");
    $cq_no_inKpa = 0;

   // print_r($akqns);
   // print_r($acqns);
   // print_r($ajsns);
    $kpa_count=count($kqs);
    ?>
    <div role="tabpanel" data-tabtype="kpa" data-schemeid="<?php echo $scheme_id ?>" data-id="<?php echo $kpa_id; ?>" class="tab-pane fade in kpa<?php echo $isActive ? " active" : ""; ?>" id="kpa<?php echo $kpa_id; ?>">
        <div class="gradePart grade-kpa mr30">
        		<?php if(isset($diagData['kpa_recommendations']) && $assessment['role'] == 4){?><a class='keyRecLink btn btn-primary execUrl kR kpa' id="kr_kpa_<?php echo $kpa_id; ?>" data-type='kpa' href="?controller=diagnostic&action=keyrecommendations&type=kpa&instance_id=<?php echo $kpa_id;?>&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessor_id;?>&lang_id=<?php echo $prefferedLanguage;?>&external=<?php echo $external?>&is_collaborative=<?php echo $is_collaborative?>&kpa7id=<?php echo $kpa7Id;?>"><i class="fa "></i><?php echo $diagnosticLabels['Key_Recommendations'];?></a><?php } ?>
        	<span data-score="<?php echo $kpa['numericRating']; ?>" class="<?php echo $scheme_id ;?> labelbg thescore score-<?php echo $kpa['numericRating']; ?>"><?php echo $kpa['rating']; ?></span>
        </div>
        <h2 class="pad030 mb10"><?php if ($ddiagnosticId == 32) { ?><img src="<?php echo SITEURL . 'public/images/changemaker.png'; ?>" width="180" /><?php } else {
            if (isset($image_name) && $image_name != '') { ?>

                <img src="<?php echo UPLOAD_URL_DIAGNOSTIC . '' . $image_name ?>" alt="<?php echo $image_name; ?>" id="resizable" class="resizable ui-widget-content">

            <?php } 
        } echo $kpa['kpa_name']; ?></h2>
        <div class="subTabWorkspaceOuter">
            <div class="ylwRibbonHldr">
                <a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab2_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
                <div class="collapse navbar-collapse tabitemsHldr" id="tab2_Toggle">
                    <ul class="yellowTab nav nav-tabs"> 
                        <?php
                        $i = 0;
                        //echo "<pre>"; print_r($kqs[9]);die;
                        foreach ($kqs[$kpa_id] as $kq_id => $kq) {
                            $i++;
                            $isKqKNComplete=true;
                            if(isset($assessment) && $assessment['role']==4 && $akqns!=0 && !empty($akqns)){
                            	if(isset($akqns[$kq_id]) && $akqns[$kq_id] && count($akqns[$kq_id])){
                            		foreach($akqns[$kq_id] as $akn_id=>$akn){
                            			if(empty($akn['text_data'])){
                            				$isKqKNComplete=false;
                            				$assessmentFilled=0;
                            				break;
                            			}
                            		}
                            	}
                            	else{
                            		$isKqKNComplete=false;
                            		$assessmentFilled=0;
                            	}
                            }else
                                $isKqKNComplete=false;
                            ?>
                            <li class="item<?php echo $i == 1 ? " active" : ""; ?> <?php echo $kq['numericRating'] > 0 && $isKqKNComplete ? "completed" : ""; ?>"><a href="#kq<?php echo $kq_id; ?>" data-toggle="tab" class="vtip" title="<?php echo htmlspecialchars($kq['key_question_text']); ?>"><?php echo $diagnosticLabels['Key_Question'] ." ". $i; ?></a>
                            	<?php if(isset($assessment) && $assessment['role']==4 && $akqns!=0){ ?><input name="aknotes[]" type="hidden" class='keyQ key-notes-val' id='keyQ_<?php echo $kq_id; ?>' value="<?php $isKqKNComplete ? print 1: print 0; ?>"><?php } ?>
                            </li>
                            <?php
                        }
  						?>                    
                    </ul>
                </div>
            </div>
            <div class="subTabWorkspace pt10">
                <div class="tab-content">
                    <?php
                    $kq_no = 0;
                    foreach ($kqs[$kpa_id] as $kq_id => $kq) {
                        $kq_no++;
                        ?>
                        <div role="tabpanel" data-tabtype="keyQ" data-schemeid="<?php echo $scheme_id ?>" data-id="<?php echo $kq_id; ?>" class="tab-pane fade in keyQ<?php echo $kq_no == 1 ? " active" : ""; ?>" id="kq<?php echo $kq_id; ?>">
                            <div class="gradePart grade-keyQ"><?php if(isset($diagData) && $diagData['kq_recommendations'] && $assessment['role'] == 4){?><a class='keyRecLink btn btn-primary execUrl kR keyQ' id="kr_keyQ_<?php echo $kq_id ?>" data-type='kq' href="?controller=diagnostic&action=keyrecommendations&type=key_question&instance_id=<?php echo $kq_id;?>&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessor_id;?>&lang_id=<?php echo $prefferedLanguage;?>&external=<?php echo $external?>&is_collaborative=<?php echo $is_collaborative?>" ><i class="fa "></i><?php echo $diagnosticLabels['Key_Recommendations'];?></a><?php } ?>  <span data-score="<?php echo $kq['numericRating']; ?>" class="<?php echo $scheme_id ;?> labelbg thescore score-<?php echo $kq['numericRating']; ?>"><?php echo $kq['rating']; ?></span></div>
                            <h3 class="mb10"><?php echo $kq_no; ?>. <?php echo $kq['key_question_text']; ?></h3>
                            <div class="tab3Hldr">
                                <a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab3_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
                                <div class="collapse navbar-collapse tabitemsHldr" id="tab3_Toggle">
                                    <ul class="blackTab nav nav-tabs">
                                        <?php
                                        $i = 0;
                                        foreach ($cqs[$kq_id] as $cq_id => $cq) {
                                            $i++;
                                            $isCqKNComplete=true;
                                            if(isset($acqns) && isset($assessment) && $assessment['role']==4 && $acqns!=0){
                                            	if(isset($acqns[$cq_id]) &&  $acqns[$cq_id] && count($acqns[$cq_id])){
                                            		foreach($acqns[$cq_id] as $akn_id=>$akn){
                                            			if(empty($akn['text_data'])){
                                            				$isCqKNComplete=false;
                                            				$assessmentFilled=0;
                                            				break;
                                            			}
                                            		}
                                            	}
                                            	else{
                                            		$isCqKNComplete=false;
                                            		$assessmentFilled=0;
                                            	}
                                            } else
                                                $isCqKNComplete=false;
                                            ?>
                                            <li class="item<?php echo $i == 1 ? " active" : ""; ?> <?php echo $cq['numericRating'] > 0 && $isCqKNComplete ? "completed" : ""; ?>"><a href="#kqA-cq<?php echo $cq_id; ?>" data-toggle="tab" class="vtip" title="<?php echo htmlspecialchars($cq['core_question_text']); ?>"><?php echo $diagnosticLabels['Sub_Question'] ." ". $i; ?></a>
                                           <?php  if(isset($assessment) && $assessment['role']==4 && $acqns!=0){ ?> <input name="aknotes[]" type="hidden" class='coreQ key-notes-val' id='coreQ_<?php echo $cq_id; ?>' value="<?php $isCqKNComplete ? print 1: print 0; ?>"><?php } ?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                    <ul class="ratInd flotedInTab">
                                        <li><span class="blue"><?php echo $diagnosticLabels['Always'];?></span></li>
                                        <li><span class="green"><?php echo $diagnosticLabels['Mostly'];?></span></li>
                                        <li><span class="yellow"><?php echo $diagnosticLabels['Sometimes'];?></span></li>
                                        <li><span class="red"><?php echo $diagnosticLabels['Rarely'];?></span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panelWhitebox">
                                <div class="tab-content">
                                    <?php
                                    $cq_no = 0;

                                    foreach ($cqs[$kq_id] as $cq_id => $cq) {
                                        $cq_no++;
                                        $cq_no_inKpa++;
                                        ?>
                                        <div role="tabpanel" data-tabtype="coreQ" data-schemeid="<?php echo $scheme_id ?>" data-id="<?php echo $cq_id; ?>" class="tab-pane fade in coreQ<?php echo $cq_no == 1 ? " active" : ""; ?>" id="kqA-cq<?php echo $cq_id; ?>">

                                            <div class="gradePart grade-coreQ"><?php if(isset($diagData) && $diagData['cq_recommendations'] && $assessment['role'] == 4){?><a class='keyRecLink btn btn-primary execUrl kR coreQ' id="kr_coreQ_<?php echo $cq_id ?>"  data-type='cq' href="?controller=diagnostic&action=keyrecommendations&type=core_question&instance_id=<?php echo $cq_id;?>&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessor_id;?>&lang_id=<?php echo $prefferedLanguage;?>&external=<?php echo $external?>&is_collaborative=<?php echo $is_collaborative?>" ><i class="fa "></i><?php echo $diagnosticLabels['Key_Recommendations'];?></a><?php } ?>   <span data-score="<?php echo $cq['numericRating']; ?>" class="<?php echo $scheme_id ;?> labelbg thescore score-<?php echo $cq['numericRating']; ?>"><?php echo $cq['rating']; ?></span></div>

                                            <h4><?php echo $cq_no_inKpa; ?>. <span class="coreQText"><?php echo $cq['core_question_text']; ?></span></h4>

                                            <ul class="questList">
                                                <?php
                                                $js_no = 0;
//                                                                                                                                                echo $cq_id.'<br/>';
//                                                                                                                                                echo '<pre>';
//                                                                                                                                                print_r($jss);
//                                                                                                                                                echo '</pre>';
                                                foreach ($jss[$cq_id] as $js_id => $js) {
                                                    $js_no++;
                                                    $name = "data[$kpa_id-$kq_id-$cq_id-$js_id]";
                                                    if (empty($js['numericRating']))
                                                        $kpaFilled = 0;
                                                    ?>
                                                    <li data-id="<?php echo $js_id; ?>" id="js-id-<?php echo $js_id; ?>" class="judgementS">
                                                        <div class="clearfix">
                                                            
                                                            <div class="questCont">
                                                                <p><strong><?php echo $cq_no_inKpa . $numToAlph[$js_no] . ". " . $js['judgement_statement_text']; ?></strong></p>
                                                                <hr>
                                                                <textarea autocomplete="off" class="form-control evidence-text" <?php echo $readOnlyText; ?> name="<?php echo $name; ?>[text]" rows="1" placeholder="<?php echo $diagnosticLabels['Evidence_Txt'];?>" cols="20"><?php echo $js['evidence_text']; ?></textarea>
                                                                                    
                                                                	
                                                            </div>
                                                            <div class="rightBoxpart">
                                                                <div class="ratingBox radioWrapper text-center">
                                                                    <strong class="mr2"><?php echo $diagnosticLabels['Your_Rating'];?>:</strong>
                                                                    <div class="rate always vtip" title="<?php echo $diagnosticLabels['Always'];?>"><input type="radio" <?php echo $disabledText; ?> class="radio_js key-65" value="4" <?php echo $js['numericRating'] == 4 ? 'checked="checked"' : ''; ?> name="<?php echo $name; ?>[value]" autocomplete="off"><i class="fa fa-circle-o"></i></div>
                                                                    <div class="rate mostly vtip" title="<?php echo $diagnosticLabels['Mostly'];?>"><input type="radio" <?php echo $disabledText; ?> class="radio_js key-77" value="3" <?php echo $js['numericRating'] == 3 ? 'checked="checked"' : ''; ?> name="<?php echo $name; ?>[value]" autocomplete="off"><i class="fa fa-circle-o"></i></div>
                                                                    <div class="rate sometimes vtip" title="<?php echo $diagnosticLabels['Sometimes'];?>"><input type="radio" <?php echo $disabledText; ?> class="radio_js key-83" value="2" <?php echo $js['numericRating'] == 2 ? 'checked="checked"' : ''; ?> name="<?php echo $name; ?>[value]" autocomplete="off"><i class="fa fa-circle-o"></i></div>
                                                                    <div class="rate rarely vtip" title="<?php echo $diagnosticLabels['Rarely'];?>"><input type="radio" <?php echo $disabledText; ?> class="radio_js key-82" value="1" <?php echo ($js['numericRating'] == 1 || ((isset($assessment['assessment_type_id']) && $assessment['assessment_type_id']==4) && empty($js['numericRating']))) ? 'checked="checked"' : ''; ?> name="<?php echo $name; ?>[value]" autocomplete="off"><i class="fa fa-circle-o"></i></div>
                                                                </div>
                                                                <div class="clr"></div>
                                                                <div class="upldHldr">                                                                                               
                                                                    <div class="inlineBtns">
                                                                        <?php if (!$isReadOnly) { ?> 
                                                                        <div class="fileUpload btn btn-primary mr0 vtip" title="Only jpeg, png, gif, jpg, avi, mp4, mov, doc, docx, txt, xls, xlsx, pdf, cvs, xml, pptx, ppt, cdr, mp3, wav type of files are allowed">
                                                                            <i class="fa fa-arrow-up"></i> <span><?php echo $diagnosticLabels['Evidence'];?></span>
                                                                            <input type="file" autocomplete="off" <?php echo $readOnlyText; ?> multiple="multiple" title=" " class="upload uploadBtn">
                                                                        </div> 
                                                                        <?php } ?>
                                                                        
                                                                        <?php 
                                                                $isJsKNComplete = true;
                                                                if(!empty($ajsns) && $ajsns!=0 && $assessment['role'] == 4){                                                                   	
                                                                	if($assessment['role']==4){
                                                                		if(isset($ajsns[$js_id]) && count($ajsns[$js_id])){
                                                                			foreach($ajsns[$js_id] as $akn_id=>$akn){
                                                                				if(empty($akn['text_data'])){
                                                                					$isJsKNComplete=false;
                                                                					$assessmentFilled=0;
                                                                					break;
                                                                				}
                                                                			}
                                                                		}
                                                                		else{
                                                                			$isJsKNComplete=false;
                                                                			$assessmentFilled=0;
                                                                		}
                                                                	}
                                                                	?><?php }
                                                                		else 
                                                                			$isJsKNComplete=false;
                                                                	?>
                                                               <?php if(isset($diagData) && $diagData['js_recommendations'] && $assessment['role'] == 4){?> <a class='keyRecLink btn btn-primary execUrl kR judgementS' id="kr_judgementS_<?php echo $js_id ?>"  href="?controller=diagnostic&action=keyrecommendations&type=judgement_statement&instance_id=<?php echo $js_id;?>&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessor_id;?>&lang_id=<?php echo $prefferedLanguage;?>&external=<?php echo $external?>&is_collaborative=<?php echo $is_collaborative?>" ><i class="fa "></i><?php echo $diagnosticLabels['Key_Recommendations'];?></a>
                                                                			  <input name="js_aknotes[]" type="hidden" class='judgementS key-notes-val' id='judgementS_<?php echo $js_id; ?>' value="<?php $isJsKNComplete ? print 1: print 0; ?>"><?php } ?> </div>
                                                                    <div class="filesWrapper">
                                                                        <?php
                                                                        $files = diagnosticModel::decodeFileArray($js['files']);
                                                                        foreach ($files as $file_id => $file_name) {
                                                                            echo '<div ' . $readOnlyText . ' class="filePrev uploaded vtip ext-' . diagnosticModel::getFileExt($file_name) . '" id="file-' . $file_id . '" title="' . $file_name . '">' . ($isReadOnly ? '' : '<span class="delete fa"></span>') . '<div class="inner"><a href="' . UPLOAD_URL . '' . $file_name . '" target="_blank"> </a></div><input type="hidden" name="' . $name . '[files][]" value="' . $file_id . '"></div>';
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                                
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }                   
                    ?>
                </div>
            </div>
            <div class="clearfix">
                <?php if (!empty($assessment_id)) { ?><a class="fr nuibtn viewBtn vtip execUrl" title="Last saved data will be previewed" data-modalclass="modal-lg aPreview" href="?controller=diagnostic&action=assessmentPreview&assessment_id=<?php echo $assessment_id; ?>&assessor_id=<?php echo $assessor_id; ?>&kpa_id=<?php echo $kpa_id; ?>&lang_id=<?php echo $prefferedLanguage;?>&external=<?php echo $external;?>" ><?php echo $diagnosticLabels['View'];?></a><?php } ?>
            </div>
        </div>
    </div>
<?php } ?>