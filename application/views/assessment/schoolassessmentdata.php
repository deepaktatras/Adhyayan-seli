<?php

$externalClient = explode(',', $assessment['user_ids']);
//$externalClient = $externalClient[1];
$externalClient = end($externalClient);
$externalClientKpa = isset($assignedKpas[$externalClient])?$assignedKpas[$externalClient]:array();
//print_r($assignedKpas);
$allAssessors = array($externalClient);
$subRolesT = $assessment['subroles'];
$subRolesT = !empty($subRolesT) ? explode(',', $subRolesT) : '';
$i = 1;
if (!empty($subRolesT))
    foreach ($subRolesT as $subRoleT) {
        $i++;
        $subRoleT = explode('_', $subRoleT);
        $memberT = $subRoleT[1];
        //$subRT = $subRoleT[1];
        array_push($allAssessors, $memberT);
    }
//$hideDiagnostics = explode(',', $hideDiagnostics['hidediagnostics']);
$externalRevPerc = null;
$internalRevPerc = null;
$rev = explode(',', $assessment['percCompletes']);
if (count($rev) > 1) {
    $externalRevPerc = $rev[1];
    $internalRevPerc = $rev[0];
} else
    $externalRevPerc = $internalRevPerc = $assessment['percCompletes'];
//$isEditable = ($disabled !== 0) ? $disabled : ( ($externalRevPerc > 0) ? 'disabled' : '');

$assessor_id='';
$numKpas = 0;

//print_r($assessmentKpas);
?>				

                    <div class="boxBody">									
                        <p><b>External Review team</b></p>
                        <div class="tableHldr teamsInfoHldr school_team team_table noShadow">
                            
                            <table class='table customTbl'>
                                <thead>
                                    <tr><th style="width:5%">Sr. No.</th><th style="width:35%">School</th><th style="width:25%">External Reviewer Role</th><th style="width:25%">External Reviewer Team member</th><th style="width:5%;">KPAs</th></tr>
                                </thead>
                                <tbody>
                                    <tr class='team_row'><td class='s_no'>1</td>
                                        <td>
                                                <?php
                                                foreach ($clients as $client){
                                                    if($assessment['external_client'] == $client['client_id']) {
                                                        echo $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '');
                                                       // echo ' <input type="hidden" name="external_client_id" id="external_client_id" value='.$client['client_id'].' >';
                                                    }
                                                }
                                                ?>
                                            </select></td>
                                        <td>Lead/Senior Associate</td>	
                                        <td>
                                                <?php
                                                foreach ($externalAssessors as $index => $ext) {
                                                    if ($externalClient == $ext['user_id'] ){
                                                        echo ' <input type="hidden" name="external_assessor_id" id="lead_assessor" value='.$ext['user_id'].' >';
                                                        echo  $ext['name'];
                                                }}
                                                ?>
                                            </select></td>
                                              <?php       $row1 = '<td><select multiple="multiple" class="form-control team_kpa_id" id="team_kpa_id' . 1 . '"  name="team_kpa_id['.$externalClient.'][]" ' . '>';
																	
                                            foreach ($assessmentKpas as $kpas){
                                                    $numKpas++;
                                                    $row1 .= "<option value=\"" . $kpas['kpa_id'] . "\"" . (!empty($kpas) && in_array($kpas['kpa_id'],$externalClientKpa) ? 'selected=selected' : '') . ">" . $kpas['kpa_name'] . "</option>\n";
                                            }

                                            $row1 .= '</select></td>';
                                            echo $row1; echo "</td>";?>
                                            
                                    </tr>
                                   
                                    <?php
                                     echo ' <input type="hidden" name="assessment_id"  value='.$assessment['assessment_id'].' >';
                                     echo ' <input type="hidden" name="num_kpa"  value='.$numKpas.' >';
                                    $subRoles = $assessment['subroles'];
                                    $subRoles = !empty($subRoles) ? explode(',', $subRoles) : '';
                                    //print_r($subRoles);
                                    $sn = 2;
                                    if (!empty($subRoles))
                                        foreach ($subRoles as $subRole) {
                                            $rowTeam = explode('_', $subRole);
                                            $teamExternalClientId = $rowTeam[0];
                                            $teamExternalMemberId = $rowTeam[1];
                                            $teamExternalRoleId = $rowTeam[2];
                                            $row = '<tr class="team_row">
										<td class="s_no">' . $sn . '</td>';
//                                                                                                                if($disabled!==0){
//                                                                                                                    echo '<input type="hidden" id="team_external_client_id'.$sn.'" class="team_external_client_id" value="'.$teamExternalClientId.'" name="externalReviewTeam[clientId][]">';
//                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalRoleId.'" name="externalReviewTeam[role][]">';
//                                                                                                                    echo '<input type="hidden"  value="'.$teamExternalMemberId.'" name="externalReviewTeam[member][]" id="team_external_assessor_id'.$sn.'">';
//                                                                                                                }
                                            $row .= '<td>';
								
                                            foreach ($clients as $client){
                                                if($teamExternalClientId == $client['client_id'] )
                                                     $row .= $client['client_name'] . ($client['city'] != "" ? ", " . $client['city'] : '');
                                            }

                                            $row .= '</td>';

                                            $row .= '<td>';
                                            foreach ($externalReviewRoles as $externalReviewer){
                                                if($externalReviewer['sub_role_id'] != '1' && $externalReviewer['sub_role_id'] == $teamExternalRoleId)                                            
                                                    $row .= $externalReviewer['sub_role_name'];
                                            }

                                            $row .= '</td>
										<td>';
                                            foreach ($externalAssessorsTeam[$sn - 2] as $index => $ext) {

                                                if ($teamExternalMemberId == $ext['user_id'] ){
                                                    $row .=  $ext['name'] ;
                                                    $assessor_id = $ext['user_id'];
                                                }
                                                //echo ' <input type="hidden" name="external_assessor_id" id="lead_assessor" value='.$ext['user_id'].' >';
                                            }


                                            $row .= '</select></td>';
                                                
                                             $row .= '<td><select multiple="multiple" class="form-control team_kpa_id" id="team_kpa_id' . $sn . '"  name="team_kpa_id['.$teamExternalMemberId.'][]" ' . '>';
																	
                                            foreach ($assessmentKpas as $kpas)
                                                $row .= "<option value=\"" . $kpas['kpa_id'] . "\"". (!empty($assignedKpas[$assessor_id]) && in_array($kpas['kpa_id'],$assignedKpas[$assessor_id]) ? 'selected=selected' : ''). ">" . $kpas['kpa_name'] . "</option>\n";

                                            $row .= '</select></td>';

										//'<td>';
                                            /*if ($disabled == '') {
                                                $row .= '<a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a>';
                                            }*/
                                           // $row .= '</td>
						$row .= '</tr>';

                                            echo $row;
                                            $sn++;
                                        }
                                    ?>										
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--external team ends-->

</div>




