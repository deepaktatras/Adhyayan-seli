<?php
class pdfClass{
	public  $reportTitleHeight=55;		
	public	$pageCount=0;
	public	$blockCount=0;
	public	$rowCount=0;
	public	$config=null;
	public	$data=null;
	public	$container=null;
	public	$currentPage=null;
	public	$currentBlock=null;
	public	$currentBlockStyle='';
	public	$currentBlockConfig=array();
	public  $pages=array();
	public	$currentSectionIndex=0;
	public	$rowNoInCurrentBlock=0;
	public $subContainer=null;
	public $maxHeightPageBody = null;
	public $tempVar = 0;
	public $maxHeightCoverPageBody = null;
	public $reportType = 0;
	public $firstPage = null;
	public $fileName = null;
	public $schoolName = null;	
	
	public function __construct($conf,$theData,$reportType,$schoolName,$fileName_Student=""){	
		
                $this->db=db::getInstance();
		$this->data = $theData;//print_r($data);		
		$this->config = $conf;
		$this->reportType = $reportType;
		$this->schoolName =$schoolName;
                $this->fileName_Student =$fileName_Student;
		$this->container='';//'$("#"'.$this->config['containerID'].')';
		$this->maxHeightPageBody = $conf['pageHeight']-$conf['headerHeight']-$conf['footerHeight']-$conf['pageNoBarHeight']-2*$conf['bodyTopBottomPadding'];
		$this->maxHeightCoverPageBody = ($conf['pageHeight']-$conf['coverHeaderHeight']-$conf['footerHeight']-$conf['pageNoBarHeight']-2*$conf['bodyTopBottomPadding']-$this->reportTitleHeight);
	} 
        
       
        
        
        
	public function getFileName($reportType,$schoolName)
	{
		$suffix = '';
		$pdfModel = new pdfModel();
		$suffix =$pdfModel->getReportName($reportType);
                
                if(isset($_GET['client_id'])){
                    $clientId=$_GET['client_id'];
                }else{
                    $clientId='';
                }
		$suffix = $suffix['report_name'];
                if($this->reportType==9){
                $fileName =  $schoolName;    
                }else if($this->reportType==5 ){
                $fileName =  $suffix." ".$this->data[0]["sectionBody"][1]["blockBody"]["dataArray"][0][1];
                }else if($this->reportType==1 && $clientId!=''){
                $fileName =  $schoolName.' AQS Comparative Report Round 2';
                }else if($this->reportType==7 ){
                //$fileName =  $suffix.' '.$this->teacherInfo['name']['value'];
                }else{
		$fileName =  $schoolName.' '.$suffix.' Card';
                }
		$fileName = htmlentities($fileName, ENT_QUOTES, 'UTF-8');
		$fileName = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $fileName);
		$fileName = html_entity_decode($fileName, ENT_QUOTES, 'UTF-8');
                if($this->reportType==5 || $this->reportType==7){
		$fileName = preg_replace(array('~[^0-9a-z]~i', '~[ -]+~'), ' ', $fileName);
                }
                if($this->reportType==5 || $this->reportType==7){
		$fileName = ucwords($fileName);
                }
		return trim($fileName, ' -');
	}	
	public function generate(){
				//Call this function to generate report
				//this.addCoverPage();
				//echo "hi";
				//print_r($this->data);				
				if($this->reportType==9){
                                $this->fileName = $this->getFileName($this->reportType,$this->fileName_Student);
                                }else{
                                $this->fileName = $this->getFileName($this->reportType,$this->schoolName);
                                }
				if($this->data!='' && $this->data!=null){
					$section_count=count($this->data);
					//echo $section_count;
					for($this->currentSectionIndex=0;$this->currentSectionIndex<$section_count;$this->currentSectionIndex++){
						$pages[]= $this->addSection($this->data[$this->currentSectionIndex]);
					}
					$this->reportType==3?($pages[1]=$this->firstPage):($pages[0]=$this->firstPage);
					
					foreach($pages as $page)
					echo $page;
				}
			}
	public function addBlock($block){
				//Adds a new block in the current page
				$block['config']=empty($block['config'])?'':$block['config'];
				if(!empty($block['config']['startNewPage']) && $block['config']['startNewPage']==1){
					$this->addPage();
				}
				/*if(!empty($block['indexKey']) && $block['indexKey']!=""){
					$("#indexKey-"+block.indexKey).html(this.pageCount);//convert
				}*/
				$this->currentBlockStyle=(empty($block['style'])?'':$block['style']);
				$this->currentBlockConfig=$block['config'];
				//print_r($block);									
				return $this->addNewTable($block);
				
			}	
	public function addSection($section){
		//print_r($section);
				//Adds a new section in the report
				return $this->addPage($section);
				/*if(!empty($section['indexKey']) && $section['indexKey']!=""){
					//$("#indexKey-"+section.indexKey).html(this.pageCount);
					echo "index";
				}*/
				
			}		
	public function addPage(){
				//Adds a new page in the report
				$this->pageCount++;
				/*if($this->pageCount==1)
					$this->container = '<div id="page-'.$this->pageCount.'" class="pageContainer" style="height:'.$this->config['pageHeight'].'px;width:'.$this->config['pageWidth'].'px;">'.$this->getHeader().$this->getBody($this->data[$this->currentSectionIndex]).'<div class="page-footer">'.($this->pageCount==1?'<div class="coverAddress">'.$this->config['coverAddress'].'</div>':'').'<div style="line-height:'.$this->config['pageNoBarHeight'].'px;" class="page-num">Page '.$this->pageCount.'</div><div class="page-footer-inner" style="line-height:'.$this->config['footerHeight'].'px;background-color:'.$this->config['footerBG'].';color:'.$this->config['footerColor'].'">'.$this->config['footerText'].'</div></div>'.'</div>';				
				else*/
					$this->container = '<div id="page-'.$this->pageCount.'" class="pageContainer" style="height:'.$this->config['pageHeight'].'px;width:'.$this->config['pageWidth'].'px;">'.$this->getHeader().$this->getBody($this->data[$this->currentSectionIndex]).$this->getFooter().'</div>';				
				//below page is to adjust cover page height
				if($this->pageCount==1){
					$this->tempVar=$this->maxHeightPageBody;
					$this->maxHeightPageBody=$this->maxHeightCoverPageBody - 80;
					//$this->maxHeightPageBody=$this->maxHeightCoverPageBody - $(".coverAddress").innerHeight();
				}else
					$this->maxHeightPageBody=$this->tempVar;

				$this->currentPage='$("#page-"'.$this->pageCount.'" .page-inner")';
				//echo $this->container;
				
				$this->firstPage=$this->reportType==3?($this->pageCount==2?$this->container:$this->firstPage):($this->pageCount==1?$this->container:$this->firstPage);				
				//echo "atest: ".strpos($this->firstPage,'<span id="indexKey-'.$this->pageCount.'"></span>');
				//$this->firstPage = preg_replace('id="indexKey-'.$this->pageCount.'"','id="indexKey-'.$this->pageCount.'"',$this->firstPage);
				$this->firstPage = str_replace('<span id="indexKey-'.$this->pageCount.'"></span>','<span id="indexKey-'.$this->pageCount.'">'.($this->pageCount+1).'</span>',$this->firstPage);
				//echo $this->firstPage;
				if($this->reportType==3 && $this->pageCount>=2)
				{
					switch($this->pageCount)
					{
						case 2: $this->firstPage = str_replace('<span id="indexKey-1"></span>','<span id="indexKey-1">3</span>',$this->firstPage);								
								break;						
						default: $this->firstPage = str_replace('<span id="indexKey-'.$this->pageCount.'"></span>','<span id="indexKey-'.$this->pageCount.'">'.($this->pageCount-1).'</span>',$this->firstPage);
									$this->firstPage = str_replace('<span id="indexKey-3"></span>','<span id="indexKey-3">3</span>',$this->firstPage);
								break;
					}
				}
				return $this->container;
			}				
	public function getHeader(){
				//returns HTML of header for a page
				/*$h=$this->pageCount==1?$this->config['coverHeaderHeight']:'60px';
				$pTpBm=$this->pageCount==1?$this->config['coverHeaderPadding']:'10px';
				return '<div class="page-header '.($this->pageCount==1?'page-cover':'').'" style="height:'.$h.'px;background-color:'.$this->config['headerBG'].';"><div style="padding-top:'.$pTpBm.'px;padding-bottom:'.$pTpBm.'px;padding-right:'.$this->config['pageLeftRightPadding'].'px;height:'.($h-$pTpBm*2).'px" class="header-img"><img src="'.$this->config['headerImg'].'" /></div></div>'.($this->pageCount==1?'<div id="reportTitle" style="line-height:'.$this->reportTitleHeight.'px">'.$this->config['reportTitle'].'</div>':'');								
			*/
			}
	public function getPDFHeader(){
				//returns HTML of header for a page
				$h=$this->pageCount==1?$this->config['coverHeaderHeight']:'60px';
				$pTpBm=$this->pageCount==1?$this->config['coverHeaderPadding']:'10px';
				return '<div class="page-header '.($this->pageCount==1?'page-cover':'').'" style="height:'.$h.'px;background-color:'.$this->config['headerBG'].';"><div style="padding-top:'.$pTpBm.'px;padding-bottom:'.$pTpBm.'px;padding-right:'.$this->config['pageLeftRightPadding'].'px;height:'.($h-$pTpBm*2).'px" class="header-img"><img src="'.$this->config['headerImg'].'" /></div></div>'.($this->pageCount==1?'<div id="reportTitle" style="line-height:'.$this->reportTitleHeight.'px">'.$this->config['reportTitle'].'</div>':'');								
			
			}		
	public function	getFooter(){
			
				/*return '<sethtmlpagefooter name="firstpage" value="on" show-this-page="1" />
				<sethtmlpagefooter name="otherpages" value="on" />';*/
				//returns HTML of footer for a page
				//return '<div class="page-footer">'.($this->pageCount==1?'<div class="coverAddress">'.$this->config['coverAddress'].'</div>':'').'<div style="line-height:'.$this->config['pageNoBarHeight'].'px;" class="page-num">Page '.$this->pageCount.'</div><div class="page-footer-inner" style="line-height:'.$this->config['footerHeight'].'px;background-color:'.$this->config['footerBG'].';color:'.$this->config['footerColor'].'">'.$this->config['footerText'].'</div></div>';
			}
	public function	getPDFFooter(){
				//returns HTML of footer for a page				
				return '<div class="page-footer">'.($this->pageCount==1?'<div class="coverAddress">'.$this->config['coverAddress'].'</div>':'').'<div style="line-height:'.$this->config['pageNoBarHeight'].'px;" class="page-num">Page '.'{PAGENO}'.'</div><div class="page-footer-inner" style="line-height:'.$this->config['footerHeight'].'px;background-color:'.$this->config['footerBG'].';color:'.$this->config['footerColor'].'">'.$this->config['footerText'].'</div></div>';
			}		
	public function getBody($section){
				//returns HTML of inner body for a page
				//return '<div class="page-inner" style="padding:'.$this->config['bodyTopBottomPadding'].'px '.$this->config['pageLeftRightPadding'].'px;"></div>';
				$pageBreak = 0;
				$body = '<div class="page-inner" style="padding:1px 50px;">';
				
				if(!empty($section['sectionHeading']) && !empty($section['sectionHeading']['text']) && $section['sectionHeading']['text']!=""){
					$sec = '<div class="section_head '.(!empty($section['sectionHeading']['style'])?$section['sectionHeading']['style']:'').'">'.$section['sectionHeading']['text'].'</div>';
					$body .= $sec;
				}
				if(!empty($section['sectionBody']) && count($section['sectionBody'])){
					//print_r($section); 
					$block_count=count($section['sectionBody']);
					for($i=0;$i<$block_count;$i++){
						$body .= $this->addBlock($section['sectionBody'][$i]);						
					}
				}
				if(!$pageBreak)					
					$body .= '</div>';
				return $body;
			}
	public function addPageBreak($section,$block_num,$block_count){
				//Adds a new page in the report				
				//echo "pbb";
				$this->pageCount++;						
				$this->subContainer = '<div id="page-'.$this->pageCount.'" class="pageContainer" style="height:'.$this->config['pageHeight'].'px;width:'.$this->config['pageWidth'].'px;">'.$this->getHeader().$this->getPageBreakBody($section).$this->getFooter();				
				//$this->subContainer = '<div id="page-'.$this->pageCount.'" class="pageContainer" style="height:'.$this->config['pageHeight'].'px;width:'.$this->config['pageWidth'].'px;">'.$this->getHeader().$this->getPageBreakBody($section).$this->getFooter().'</div>';				
				//below page is to adjust cover page height				
					$this->maxHeightPageBody=$this->tempVar;
					//$this->pageCount++;
			//	$this->subContainer .= '<div id="page-'.$this->pageCount.'" class="pageContainer" style="height:'.$this->config['pageHeight'].'px;width:'.$this->config['pageWidth'].'px;">'.$this->getHeader().'<div class="page-inner" style="padding:'.$this->config['bodyTopBottomPadding'].'px '.$this->config['pageLeftRightPadding'].'px;">';
				
				//$this->currentPage='$("#page-"'.$this->pageCount.'" .page-inner")';
				//echo $this->subContainer;
				return $this->subContainer;
			}		
	public function getPageBreakBody($section){		
			//print_r($section);
			
				$body = '<div class="page-inner" style="padding:1px '.$this->config['pageLeftRightPadding'].'px;">';
				//$body .= $this->addBlock($section);
				$this->currentBlockStyle=empty($section['style'])?'':$section['style'];
				$this->currentBlockConfig=empty($section['config'])?'':$section['config'];
				//print_r($block);									
				$body .= $this->addNewTable($section);
				
				$body .= '</div>';
				return $body;
				
			}		
		public function addSectionHeading($sectionHeading){
				//Adds section heading to the current page
				$this->currentPage .= '<div class="section_head '.(!empty($sectionHeading['style'])?$sectionHeading['style']:'').'">'.$sectionHeading['text'].'</div>';
				//echo $this->currentPage;
			}		
	public function addRow($data,$isHead,$style){
				//Adds a new row to the current block				
				
				$this->rowCount++;
				$h='<tr class="'.$style.' '.($isHead==1?"head-row":"body-row rowNo-".$this->rowNoInCurrentBlock).'" id="row-'.$this->rowCount.'">';
				$col_count=count($data);//print_r($data);				
				for($i=0;$i<$col_count;$i++){
					//print_r($data[$i]);										
					$h .= empty($data[$i])?'<td></td>':'<td'.(empty($data[$i]['cSpan'])?'':' colspan="'.$data[$i]['cSpan'].'"').(empty($data[$i]['rSpan'])?'':' rowspan="'.$data[$i]['rSpan'].'"').' class="'.(empty($data[$i]['style'])?'':$data[$i]['style']).($isHead?'':' colNo-'.$i).'">'.(!isset($data[$i]['text'])?$data[$i]:$data[$i]['text']).'</td>';					
				}
				$h .='</tr>';
				//if($this->reportType==3)
					//echo "recommendation report: ".$this->reportType;
				//echo "<pre>";echo $h;echo "</pre>";
				return $h;
				//$this->currentBlock .= $h;
				//$pH=$this.currentPage.height();					
			}
	public function addNewTable($block){
					
				//Adds a new block in the current page
				$this->blockCount++;
				//return '<table '.(!empty($index) && $index!=""?'data-indexkey="'.$index.'"':'').' '.(!empty($this->currentBlockConfig['groupby'])?'data-groupby="'.$this->currentBlockConfig.['groupby'].'"':'').' id="block-'.$this->blockCount.'" class="the-block '.(empty($this->currentBlockStyle)?'':$this->currentBlockStyle).'"></table>';				
				$table = strpos($this->currentBlockStyle,'a-grph')?'<div style="margin-top:7px;margin-bottom:0px;width:100%;border-right:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;">':'';
				$table .= (!empty($this->currentBlockConfig['groupby'])&&$this->currentBlockConfig['groupby']=='kpa-134' && $this->blockCount==15?'<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>':'').'<table '.(strpos($this->currentBlockStyle,'a-grph')?'':'').(!empty($this->currentBlockConfig['groupby'])?'data-groupby="'.$this->currentBlockConfig['groupby'].'"':'').' id="block-'.$this->blockCount.'" class="the-block '.(empty($this->currentBlockStyle)?'':$this->currentBlockStyle).'">';
				
				$this->rowNoInCurrentBlock=0;
				if(!empty($block['blockHeading']) && !empty($block['blockHeading']['data']) && count($block['blockHeading']['data'])){					
					$table .= $this->addRow(($block['blockHeading']['data']),1,empty($block['blockHeading']['style'])?'':$block['blockHeading']['style']);
					$this->rowNoInCurrentBlock++;
				}
				if( !empty($block['blockBody']) && !empty($block['blockBody']['dataArray']) && count($block['blockBody']['dataArray'])){
					$dataArray_count=count($block['blockBody']['dataArray']);
					$style=!empty($block['blockBody']['style'])?$block['blockBody']['style']:'';
					for($i=0;$i<$dataArray_count;$i++){
						$table .= $this->addRow($block['blockBody']['dataArray'][$i],0,$style);
						$this->rowNoInCurrentBlock++;
					}
				}
				
				//echo $table;
				
				$table .= '</table>';
				//$table .= (strpos($this->currentBlockStyle,'a-grph')?'<hr style="width:714px;color:#000000;margin-top:-7px;" />':'');
				$table .= strpos($this->currentBlockStyle,'a-grph')?'</div>':'';
				
				return $table;
			}		
	
}