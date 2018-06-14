<?php
class ajaxFilter{
	
	protected $_fields=array();
	protected $_links=array();
	
	function addTextBox($name,$value,$placeholder=""){
            $this->_fields[]='<input type="text" autocomplete="off" data-value="'.$value.'" class="ajaxFilter" name="'.$name.'" value="'.$value.'" placeholder="'.$placeholder.'" />';
	}
        
        function addTextBoxEtc($name,$value,$placeholder="",$etc=''){
            $this->_fields[]='<input type="text" autocomplete="off" data-value="'.$value.'" class="ajaxFilter" name="'.$name.'" value="'.$value.'" placeholder="'.$placeholder.'" '.$etc.' />';
	}
	
	function addDropDown($name,$values,$id_key,$value_key,$selected="",$placeholder="",$cssClass=''){
		$html='<select autocomplete="off" data-value="'.$selected.'" class="ajaxFilter '.$cssClass.'" name="'.$name.'">';
                if(!empty($placeholder)) {
                    $html .= '<option '.(""==$selected?'selected="selected"':"").' value=""> -- '.$placeholder.' -- </option>
		';
                }
		
		foreach($values as $value)
			$html.='<option '.($value[$id_key]==$selected?'selected="selected"':"").' value="'.$value[$id_key].'">'.$value[$value_key].'</option>
			';
		$html.='</select>';
		$this->_fields[]=$html;
	}
        
        function addDropDown_etc($name,$values,$id_key,$value_key,$selected="",$placeholder="",$cssClass='',$etc=''){
		$html='<select autocomplete="off" data-value="'.$selected.'" class="ajaxFilter '.$cssClass.'" name="'.$name.'" '.$etc.'>
		<option '.(""==$selected?'selected="selected"':"").' value=""> -- '.$placeholder.' -- </option>
		';
		foreach($values as $value)
			$html.='<option '.($value[$id_key]==$selected?'selected="selected"':"").' value="'.$value[$id_key].'">'.$value[$value_key].'</option>
			';
		$html.='</select>';
		$this->_fields[]=$html;
	}
	
	function addHidden($name,$value){
		$this->_fields[]='<input type="hidden" autocomplete="off" data-value="'.$value.'" class="ajaxFilter" name="'.$name.'" value="'.$value.'" />';
	}
        function addLink($value){
		$this->_links[]=$value;
	}
        
        
        function addHiddenEtc($name,$value,$etc){
		$this->_fields[]='<input type="hidden" autocomplete="off" data-value="'.$value.'" class="ajaxFilter" name="'.$name.'" value="'.$value.'" '.$etc.'/>';
	}
	
	function generateFilterBar($cleanAfterPrinting=0){
		$size=count($this->_fields);
		if($size){
			?>
	<form action="" method="post" class="filters-bar ylwRibbonHldr">
		<div class="ribWrap clearfix">
			<div class="fl fieldsArea"> 
				<label>Filter By:</label>
			<?php
			foreach($this->_fields as $field){
				echo $field; 
			}
			?>
				<!--<button type="submit" class="ajaxFilterBtn"><i class="fa fa-filter"></i>Filter</button>
				<button type="button" class="ajaxFilterReset">Reset</button>-->
                                <button type="submit" class="ajaxFilterBtn fa fa-filter vtip"  title="Filter"></button>
				<button type="button" class="ajaxFilterReset fa fa-remove vtip" title="Reset"></button>
                                
			</div>
                              <?php  
                              if(isset($this->_links) && count($this->_links)>=1) { ?>
                                    <span style=" float: right;">
                                    <?php
                                    foreach($this->_links as $field){
                                            echo $field; 
                                    }
                                    ?>
                                    </span>
                              <?php } ?>
		</div>
	</form>
			<?php
		}
		if($cleanAfterPrinting)
			$this->clean();
	}
        
	function generatesearchBar($cleanAfterPrinting=0){
		$size=count($this->_fields);
		if($size){
			?>
	<form action="" method="post" class="filters-bar ylwRibbonHldr">
		<div class="ribWrap clearfix">
			<div class="fl fieldsArea"> 
				<label>Search By:</label>
			<?php
			foreach($this->_fields as $field){
				echo $field; 
			}
			?>
				<!--<button type="submit" class="ajaxFilterBtn"><i class="fa fa-filter"></i>Filter</button>
				<button type="button" class="ajaxFilterReset">Reset</button>-->
                                <button type="submit" class="ajaxFilterBtn fa fa-filter vtip"  title="Filter"></button>
				<button type="button" class="ajaxFilterReset fa fa-remove vtip" title="Reset"></button>
			</div>
		</div>
	</form>
			<?php
		}
		if($cleanAfterPrinting)
			$this->clean();
	}
	
        function generateFilterBarHidden($cleanAfterPrinting=0){
		$size=count($this->_fields);
		if($size){
			?>
<form action="" method="post" class="filters-bar ylwRibbonHldr" >
		<div class="ribWrap clearfix" style="display: none;">
			<div class="fl fieldsArea"> 
				<label>Filter By:</label>
			<?php
			foreach($this->_fields as $field){
				echo $field; 
			}
			?>
				<!--<button type="submit" class="ajaxFilterBtn"><i class="fa fa-filter"></i>Filter</button>
				<button type="button" class="ajaxFilterReset">Reset</button>-->
                                <button type="submit" class="ajaxFilterBtn fa fa-filter vtip"  title="Filter"></button>
				<button type="button" class="ajaxFilterReset fa fa-remove vtip" title="Reset"></button>
			</div>
		</div>
	</form>
			<?php
		}
		if($cleanAfterPrinting)
			$this->clean();
	}
        
	function clean(){
		$this->_fields=array();
	}
        
        // add function for date filter on 25-07-2016 by Mohit Kumar
        function addDateBox($name,$value,$placeholder="",$onclick='',$etc=''){
            if($onclick!=''){
                $onclick='onchange="'.$onclick.'"';
            }
            $this->_fields[]='<input type="text" autocomplete="off" data-value="'.$value.'" class="ajaxFilter '.$name.'" name="'.$name.'" id="'.$name.'" value="'.$value.'" placeholder="'.$placeholder.'" title="'.$placeholder.'" '.$onclick.' '.$etc.' readonly/>';
	}
}