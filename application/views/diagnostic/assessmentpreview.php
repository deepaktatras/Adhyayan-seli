<?php
$pTypeText=$assessment['role']==3?'Self-Review':'External Review';
$pTypeTextRating=$assessment['role']==3?(isset($diagnosticLabels['self_review_rating'])?$diagnosticLabels['self_review_rating']:'Self-Review Rating'):(isset($diagnosticLabels['external_review_rating'])?$diagnosticLabels['external_review_rating']:'External Review Rating');
$pTypeTextGradeSQ=$assessment['role']==3?(isset($diagnosticLabels['self_review_sq'])?$diagnosticLabels['self_review_sq']:'Self-Review Grade for S.Q'):(isset($diagnosticLabels['external_review_sq'])?$diagnosticLabels['external_review_sq']:'External Review Grade for S.Q');
$pTypeTextGradeKQ=$assessment['role']==3?(isset($diagnosticLabels['self_review_kq'])?$diagnosticLabels['self_review_kq']:'Self-Review Grade for K.Q'):(isset($diagnosticLabels['external_review_kq'])?$diagnosticLabels['external_review_kq']:'External Review Grade for K.Q');
?>
		<div class="panel-group aPreview-inner" id="accordion" role="tablist">
		<?php
		$ratingVisible=$isAdmin || $assessment['role']==4?true:false;
		$kpa_no=0;
		foreach($kpas as $kpa_id=>$kpa){
			$kpa_no++;
			$cq_no_inKpa=0;
			$scheme_id = 'scheme-'.$kpa['scheme_id'];
		?>
            <div class="panel panel-default">
              <div class="panel-heading related" role="tab">
                <h4 class="panel-title">
                  <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseKPA-<?php echo $kpa_no; ?>" aria-expanded="true" aria-controls="collapseOne">
                    KPA <?php echo $kpa['kpa_no']; ?>: <?php echo $kpa['kpa_name']; ?>
                  </a>
                </h4>
                <?php if($kpa['numericRating']>0 && $ratingVisible){ ?><label class="<?php echo $scheme_id; ?> result pScore-<?php echo $kpa['numericRating']; ?>"><span><?php echo $kpa['rating']; ?></span></label><?php } ?>
              </div>
              <div id="collapseKPA-<?php echo $kpa_no; ?>" class="panel-collapse collapse <?php echo $kpa_no==1?'in':''; ?>" role="tabpanel">
                <div class="panel-body vertScrollArea" style="height:445px;">
				<?php
				$kq_no=0;
				foreach($kqs[$kpa_id] as $kq_id=>$kq){
					$kq_no++;
				?>
                    <h5>K.Q <?php echo $kq_no; ?>: <?php echo $kq['key_question_text']; ?></h5>
                    <div class="tableHldr">
                        <table class="table customTbl">
                            <thead>
                                <tr>
                                    <th class="w25p tl"><?php echo isset($diagnosticLabels['Sub_Question'])?$diagnosticLabels['Sub_Question']:'Sub Questions'; ?> (S.Q)</th>
								<?php
								$jsRes=array("serialNos"=>array(),"scores"=>array());
								$numToAlph=array(1=>"a",2=>"b",3=>"c",4=>"d");
								$cq_no=0;
								foreach($cqs[$kq_id] as $cq_id=>$cq){
									$cq_no++;
									$cq_no_inKpa++;
								?>
									<th class="w25p tl vt"><?php echo $cq_no; ?>. <?php echo $cq['core_question_text']; ?></th>
								<?php
									$js_no=0;
									foreach($jss[$cq_id] as $js_id=>$js){
										$js_no++;
										$jsRes['serialNos'][$cq_no_inKpa][$js_id]=$numToAlph[$js_no];
										$jsRes['scores'][$cq_no][$js_id]=array("rating"=>$js['rating'],"numScore"=>$js['numericRating']);
									}
								}
								?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="tl"><?php echo isset($diagnosticLabels['Judgement_Statements'])?$diagnosticLabels['Judgement_Statements']:'Judgement Statements'; ?></td>
								<?php
								foreach($jsRes['serialNos'] as $cno=>$sns){
								?>
								<td class="pad0">
                                        <table class="w30p">
                                            <tbody>
                                                <tr>
											<?php
											foreach($sns as $jid=>$sn){
											?>
											<td class="prevJs" data-jsid="<?php echo $jid; ?>"><?php echo $cno.$sn; ?></td>
											<?php
											}
											?>
												</tr>
                                            </tbody>
                                        </table>
                                    </td> 
								<?php
								}
								?>
                                </tr>
                                <tr>
                                    <td class="tl"><?php echo $pTypeTextRating; ?></td>
								<?php
								foreach($jsRes['scores'] as $scs){
								?>
								<td class="pad0">
                                        <table class="w30p">
                                            <tbody>
                                                <tr>
											<?php
											foreach($scs as $jid=>$sc){
											?>
											<td class="pScore-<?php echo $sc['numScore']; ?>"><span class="prevJs" data-jsid="<?php echo $jid; ?>"><?php echo $sc['rating']; ?></span></td>
											<?php
											}
											?>
												</tr>
                                            </tbody>
                                        </table>
                                    </td> 
								<?php
								}
								?>
                                </tr>
                            </tbody>
                        </table>                       
                    </div>
				<?php 
				if($ratingVisible){
				?>
                    <div class="tableHldr">
                        <table class="table customTbl">
                            <tbody>
                                <tr>
                                    <td class="w25p tl"><?php echo $pTypeTextGradeSQ; ?></td>                                    
								<?php
								foreach($cqs[$kq_id] as $cq_id=>$cq){
								?>
									<td class="<?php echo $scheme_id; ?> w25p pScore-<?php echo $cq['numericRating']; ?>"><span><?php echo $cq['rating']; ?></span></td>
								<?php
								}
								?>
                                </tr>
                                <tr <?php if($scheme_id=='scheme-2') echo 'style="display:none;"';?>>
                                    <?php
                                    if($scheme_id=="scheme-4"){
                                    $class="".$scheme_id." w25p pScore-".$kq['numericRating']."";
                                        //$class="pScore-".$kq['numericRating']."";
                                    }else{
                                    $class="pScore-".$kq['numericRating']."";
                                    }
                                    ?>
                                    <td class="w25p tl"><?php echo $pTypeTextGradeKQ; ?></td> 
                                    <td colspan="3" class="<?php echo $class; ?>"><span><?php echo $kq['rating']; ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
				<?php 
				}
			 } 
			 if($assessment['role']==4){
			 	$oldKN = isset($akns[$kpa_id])?array_filter($akns[$kpa_id],function($var){ if($var['type']==''||$var['type']==NULL) return $var;}):'';
			 	?>
					<div class="keyNotesBox">
                        <h4><?php echo !empty($oldKN)?'Assessor Key Notes:':'Strengths and Recommendations:'?></h4>
                       
						<?php
						$akn_count=0;
						if(!empty($oldKN))
						{ echo "<ol>";
							if(isset($akns[$kpa_id]) && count($akns[$kpa_id])){
									foreach($akns[$kpa_id] as $akn_id=>$akn){
										$akn_count++;
										if(trim($akn['text_data'])!=""){
									?>
										<li class="prevKn" data-knid="<?php echo $akn_id; ?>"><?php echo $akn['text_data']; ?></li>
									<?php
										}
									}
							}
						echo "</ol>";	
						}
						else{
							$celebrateKN = isset($akns[$kpa_id])?array_filter($akns[$kpa_id],function($var){if($var['type']=='celebrate') return $var;}):'';
							$recommendationKN = isset($akns[$kpa_id])?array_filter($akns[$kpa_id],function($var){ if($var['type']=='recommendation') return $var;}):'';
							if(!empty($celebrateKN) && count($celebrateKN)){
								echo "<h4 class='keyNotesSub'>Celebrate:</h4><ol>";
								foreach($celebrateKN as $akn_id=>$akn){
									$akn_count++;
									if(trim($akn['text_data'])!=""){
										?>
									<li class="prevKn" data-knid="<?php echo $akn_id; ?>"><?php echo $akn['text_data']; ?></li>
									<?php
										}
									}
									echo "</ol>";
							}
							
							if(!empty($recommendationKN) && count($recommendationKN)){
								echo "<h4 class='keyNotesSub'>Recommendations:</h4><ol>";
								foreach($recommendationKN as $akn_id=>$akn){
									$akn_count++;
									if(trim($akn['text_data'])!=""){
										?>
																<li class="prevKn" data-knid="<?php echo $akn_id; ?>"><?php echo $akn['text_data']; ?></li>
																<?php
																	}
																}
																echo "</ol>";
							}
							
						}
						?>                        
                    </div>
				<?php
				}
				?>
                </div>
              </div>
            </div>
	<?php } ?>
        </div>
          
		<script type="text/javascript">
		$(document).ready(function(){
			$(".aPreview-inner .vertScrollArea").mCustomScrollbar({theme:"dark"});
		});
		</script>
