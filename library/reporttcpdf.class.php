<?php
class reporttcpdf extends TCPDF {
	public $top_margin = 20;
	public $footer_text = null;
	public $footerBG = null;
	public $other_footer_text = null;
	public $footerColor = null;
	public $footerHeight = null;
	public $pageNoBarHeight = null;
        public $assessemnt_type=null;
        public $coverAddressAntarang=null;
        public $coverAddressAdhyayanFoundation=null;
	/**
	 * Overwrite Header() method.
	 * @public
	 */
       
        public function Header() {
                //echo $this->assessemnt_type;die;
                $logoPath = './public/images/reports/Rep-logo.png';
                if($this->source == 1){

                    $logo_path = trim(CRON_ROOT,'library/');
                    $logoPath = "/".$logo_path.'/public/images/reports/Rep-logo.png';
                }
		if ($this->tocpage) {
			// *** replace the following parent::Header() with your code for TOC page
                        if($this->assessemnt_type==4){
                        
                        $html='<div style="text-align:center;"><b>'.$this->getHeaderData()['title'].'</b></div>';
                        $this->writeHTML($html, true, false, true, false, '');
                        }else if($this->assessemnt_type==1){
                        $html='<div style="text-align:right;background-color:#800000;><img src="'.$logoPath.'" width="150px;"></div>';
                        $this->writeHTML($html, true, false, true, false, '');
                        }else{
			parent::Header ();
                        }
		} else {
			// *** replace the following parent::Header() with your code for normal pages
                        
			if($this->assessemnt_type==4){
                        $html='<div style="text-align:center;"><b>'.$this->getHeaderData()['title'].'</b></div>';
                        $this->writeHTML($html, true, false, true, false, '');  
                        }else if($this->assessemnt_type==1){
                         $html='<div style="text-align:right;margin-top:0px;background-color:#800000"><img src="'.$logoPath.'"  width="250px;"></div>';
                        $this->writeHTML($html, true, false, true, false, '');   
                        }else{
                        parent::Header ();
                        }
		}
	}

	/**
	 * Overwrite Footer() method.
	 * @public
	 */
	public function Footer() {

		/* if ($this->tocpage) {
			// *** replace the following parent::Footer() with your code for TOC page
			//parent::Footer ();
		} */ 
		if ($this->print_footer && $this->page==1){			
			//$this->bottom_margin = $this->GetY() + 150;
                        
			
                        if($this->assessemnt_type==4){
                        $this->SetY(-41);
			 
			//$this->MultiCell(0, 0,$this->coverAddressAntarang, 0, 'C', 0, 1, '', '', true, null, true);
                        $html='<style>table.pdfFtr{width:100%;border-collapse:collapse;border-spacing:0;}.pdfFtr .halfSec{width:48.5%;vertical-align:middle;}.pdfFtr .halfSec.fl{text-align:center;}.pdfFtr .halfSec.fr{text-align:center;}.pdfFtr.broad .halfSec{padding:20px 30px;}.pdfFtr.thin .halfSec{padding:15px 20px;}
.antHead{font-weight: bold;color:#76AD1B}.adhHead{font-weight: bold;color:#C70039}
</style><div class="coverAddress"><table border="0"  class="pdfFtr broad"> <tr><td class="halfSec fl" align="center">'.$this->coverAddressAntarang.'</td><td class="halfSec fr" align="center">'.$this->coverAddressAdhyayanFoundation.'</td></tr></table></div>';    
                        $this->writeHTML($html, true, false, true, false, '');
                        $this->SetTextColor ( 81, 19, 19 );
                        
                        }else{
                        $this->SetY(-31);
			$this->SetTextColor ( 81, 19, 19 );    
                        $this->MultiCell(0, 0,$this->footer_text, 0, 'C', 0, 1, '', '', true, null, true);    
                        }
			$this->SetTextColor ( 204, 204, 204 );
			$this->MultiCell(0, 6,'Page '.$this->getAliasNumPage(), 0, 'C', 0, 1, '', '', true, null, true);
			//$this->Cell(0, 0, 'Page '.$this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
			$this->SetFillColor ( 41, 32, 26 );
			$this->SetTextColor ( 144, 128, 118 );
                         if($this->report_type == 1){
                            $this->Rect($this->x-PDF_MARGIN_LEFT, $this->y-1, $this->w+40, $this->y, 'DF');
                         }else{
                            $this->Rect($this->x-PDF_MARGIN_LEFT, $this->y-1, $this->w, $this->y, 'DF');
                         }
			$this->MultiCell($this->w, '',$this->other_footer_text, 0, 'C',false , 0, $this->x-PDF_MARGIN_LEFT, '', true, null, false,'','','M');						
		}
		else{
			// *** replace the following parent::Footer() with your code for normal pages
			$this->bottom_margin = $this->GetY() + 5;
		//	parent::Footer ();	
			$this->SetY(-16);			
			$this->SetTextColor ( 81, 19, 19 );
			$this->MultiCell(0, 6,'Page '.$this->getAliasNumPage(), 0, 'C', 0, 1, '', '', true, null, true);
			$this->SetFillColor ( 41, 32, 26 );
			$this->SetTextColor ( 144, 128, 118 );		
			//$this->write(10,$this->other_footer_text,'',TRUE,'C');
                        //echo"aaa". $this->report_type;
                        if($this->report_type == 1){
                            $this->Rect($this->x-PDF_MARGIN_LEFT, $this->y-1, $this->w+40, $this->y, 'DF');
                        }else{
                            $this->Rect($this->x-PDF_MARGIN_LEFT, $this->y-1, $this->w, $this->y, 'DF');
                        }
			$this->MultiCell($this->w, '',$this->other_footer_text, 0, 'C',false , 0, $this->x-PDF_MARGIN_LEFT, '', true, null, false,'','','M');	
		}
	}
}