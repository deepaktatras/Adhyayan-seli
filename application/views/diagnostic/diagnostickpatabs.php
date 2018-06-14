<div class="tab1Hldr mb10"> 
	<div class="tabitemsHldr">
            <a href="javascript:void(0);" class="navIcon collapsed" data-toggle="collapse" data-target="#tab2_Toggle" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></a>
            <div class="collapse navbar-collapse tabitemsHldr" id="tab2_Toggle">
                <ul class="redTab nav nav-tabs">

                                <?php
                                //print_r($diagnosticLabels);
                                $i=0;
                                $selected_kpas =array();
                                foreach($kpas as $kpa_id=>$kpa){
                                        $i++;											
                                        ?>
                                        <li class="item<?php echo $i==1?" active":""; ?>"><a href="#kpa<?php echo $kpa_id; ?>" data-toggle="tab" class="vtip" title="<?php echo htmlspecialchars($kpa['kpa_name']); ?>"><?php echo $diagnosticLabels['KPA']?> <?php echo $i; ?></a></li>
                                        <?php
                                        array_push($selected_kpas,$kpa_id);
                                } 	
                                $selected_kpas = implode(',',$selected_kpas);		
                                ?>							   
                </ul>
            </div>
        <div class="flotedInTab add" >
            <a href="?controller=diagnostic&action=addmoreform&type=kpa&assessmentId=<?php echo $assessmentId; ?>&diagnosticId=<?php echo $diagnosticId;?>&langId=<?php echo $langId;?>&parentId=<?php echo $parentId;?>&equivalenceId=<?php echo $equivalenceId;?>&langIdOriginal=<?php echo $langIdOriginal;?>" data-postformid="for" class="vtip execUrl subQpane" title="Add More" data-toggle="modal" data-target="#addMoreKPA" title="Click to add KPAs" data-size="800" data-validator="isDiagnosticName" data-validator="isDiagnosticName"><i class="fa fa-plus-circle"></i> Add KPA</a>			
		</div>
    </div>												
</div>
<!-- Tab panes -->
<div class="tab-content mainTabCont">
	<!-- KPA Tab Contents -->
	<?php 				 $activeKpa =1;
						$activeKpa=isset($activeKpa)?$activeKpa:0;
						$kpa_no=0;
						foreach($kpas as $kpa_id=>$kpa){
							$kpa_no++;
							$isActive=$kpa_no==$activeKpa?true:false;							
							include('common'.DS.'kpacontenttab.php');							
						} 
						?>
</div>