<h1 class="page-title">Recommendation</h1>
<div class="clr"></div>

                    
                        <div class="ylwRibbonHldr">
			<div class="tabitemsHldr"></div>
		        </div>
                        <div class="subTabWorkspace pad26">
                            
                            <h5>Recommendation :</h5><?php echo $recommendation['text_data'] ?>
                            
                            
                            <?php
                            $kpan=explode("/",(explode(",",$recommendation['kk1'])[0]));
                            $kpa=explode("@#@$",$recommendation['kpa_text']);
                            ?>
                            <br><br>
                            <div>
                            <b><?php echo $kpan[0] ?></b> :<?php echo $kpa[0] ?>
                            </div>
                            
                            <table class="table customTbl fldSet team">
                                            <thead>
                                                	
                                                <tr>
                                                    <th>Key Question</th>
                                                    <th>Sub Question</th>
                                                    <th>Judgement Statement</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $kq=explode("@#@$",$recommendation['kq_text']);
                                                $cq=explode("@#@$",$recommendation['cq_text']);
                                                $js=explode("@#@$",$recommendation['js_text']);
                                                foreach($kpa as $key=>$val){
                                                $kpan=explode("/",(explode(",",$recommendation['kk1'])[$key])); 
                                               
                                                ?>
                                                <tr>
                                                <td style="text-align: left"><?php echo $kpan[1] ?>)  <?php echo $kq[$key] ?></td>
                                                <td  style="text-align: left"><?php echo $kpan[2] ?>)  <?php echo $cq[$key] ?></td>
                                                <td  style="text-align: left"><?php echo $kpan[3] ?>)  <?php echo $js[$key] ?></td>
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                             </table>
                        </div>

                
           