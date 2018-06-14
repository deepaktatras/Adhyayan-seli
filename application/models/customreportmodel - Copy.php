<?php
class customreportModel extends Model{
	static function getFilterRow($sno=1,$filterData=0,$isDashboard=0){		
		$customreportModel = new customreportModel();
		//grainarray
		$grainArray = array(13,23,24,25,27);
		$subFilterRow = '';
		//between
		$fValue = null;
		$sValue = null;
		$fValuesOpt = null;
		$sValuesOpt = null;
		if($isDashboard==1)
			$params = $customreportModel->getAllFilterParams();
		else
			$params = $sno==1?($customreportModel->getFilterParams(1)):$customreportModel->getFilterParams();		
		$paramOpt = null;
		foreach($params as $parameter)
		{
			$paramOpt .= "<option value=\"$parameter[filter_attr_id]\"" .($filterData['filter_attr_id']==$parameter['filter_attr_id']?'selected=selected':'').">".$parameter['filter_attr_name']."</option>";
		}
		
		$operatorOpt = null;
		$valuesOpt = null;
		$mulValuesOpt = null;
		$mulVals = null;
		if($sno==1 && $filterData==0 && $isDashboard==0)
		{
			$row1Op =$customreportModel->getAttrOperators(9);//first parameter is fixed to award scheme for award scheme, load operators
			$row1Vals =$customreportModel->getAttrValues(9);//for award scheme, load values
			foreach($row1Op as $parameter)
				$operatorOpt .= "<option value=\"$parameter[operator_id]\"".($filterData['filter_operator']==$parameter['operator_id']?'selected=selected':'').">".$parameter['operator_text']."</option>";
				$vals = $customreportModel->getAttrValues($filterData['filter_attr_id']);
					
				$keys = array_keys($vals[0]);
				$k1 = $keys[0];
				$k2 = $keys[1];
				//print_r($keys);
				
				//echo strlen($filterData['mul']);
				foreach($vals as $val)
				{
					$valuesOpt .= "<option value=\"$val[$k1]\">".$val[$k2]."</option>";
					$fValuesOpt .= "<option value=\"$val[$k1]\">".$val[$k2]."</option>";
					$sValuesOpt .= "<option value=\"$val[$k1]\">".$val[$k2]."</option>";
					//multiple select
					$mulValuesOpt .= "<option value=\"$val[$k1]\">".$val[$k2]."</option>";
				}
		
		}
		if($filterData!=0)
		{				
			$params = $customreportModel->getAttrOperators($filterData['filter_attr_id']);
			$csId=0;
			foreach($params as $parameter)						
				$operatorOpt .= "<option value=\"$parameter[operator_id]\"".($filterData['filter_operator']==$parameter['operator_id']?'selected=selected':'').">".$parameter['operator_text']."</option>";			
			if($filterData['filter_attr_id']==3)//for state get country
				$csId = $customreportModel->getFilterData($filterData['filter_id'],10);
			elseif($filterData['filter_attr_id']==11)//for city get state
				$csId = $customreportModel->getFilterData($filterData['filter_id'],3);
			elseif(in_array($filterData['filter_attr_id'],$grainArray)){//get subattributes for KPA,kq,sq,js
				//get filter sub attr data
				$sub_attr_ids = $customreportModel->getSubAttr();
				//print_r($filterData);
				$subFilterData = $customreportModel->getSubFilterData($filterData['filter_instance_id']);				
				$grainArr = array("13"=>1,"23"=>2,"24"=>3,"25"=>4,"27"=>5);			
				if(!empty($subFilterData))
					$subFilterRow = $customreportModel->getDiagnosticGrainHtmlRowforEdit($grainArr[$filterData['filter_attr_id']], $filterData['filter_attr_id'], $subFilterData['filter_sub_attr_max_cardinality'],$sub_attr_ids, $sno,$subFilterData,$grainArr[$subFilterData['filter_sub_attr_id']]);
			}
			$csId!=0? $csId=$csId['filter_attr_value']:'';
			$vals = $customreportModel->getAttrValues($filterData['filter_attr_id'],$csId);			
			$keys = array_keys($vals[0]);
			$k1 = $keys[0];
			$k2 = $keys[1];			
			$filterData['filter_operator']==7?(($sValue = $filterData['filter_s_value']) && ($fValue=$filterData['filter_f_value']) ):'';
			//print_r($keys);
			$mulVals = strlen($filterData['mul'])>0?explode(',',$filterData['mul']):array();			
			foreach($vals as $val)	
			{
				$valuesOpt .= "<option value=\"$val[$k1]\"".($filterData['filter_attr_value']==$val[$k1]?'selected=selected':'').">".$val[$k2]."</option>";
				$fValuesOpt .= "<option value=\"$val[$k1]\"".($filterData['filter_f_value']==$val[$k1]?'selected=selected':'').">".$val[$k2]."</option>";
				$sValuesOpt .= "<option value=\"$val[$k1]\"".($filterData['filter_s_value']==$val[$k1]?'selected=selected':'').">".$val[$k2]."</option>";
				//multiple select
				$mulValuesOpt .= "<option value=\"$val[$k1]\"".(in_array($val[$k1], $mulVals)?'selected=selected':'').">".$val[$k2]."</option>";
			}
			
			
		}				
		$removeBtn = $sno>1?'<a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a>':'';
		$html = "<tr class='filter_row'>
		<td class='s_no'>$sno</td>				
		<td>
			<select class='form-control filterAttr' name='attr_id[]' id='attr_id_$sno' required>".($sno==1 && $isDashboard==0?'':"<option value=''> - Attribute Name - </option>")."
			".$paramOpt."
			</select>
		</td>
		<td>
			<select class='form-control filterOperator' name='operator_id[]' id='operator_id_$sno' required>".($sno==1 && $isDashboard==0?'':"<option value=''> - Operator - </option>")."
			".$operatorOpt."
			</select>
		</td>
		<td class='val_td'> <select class='form-control filterValue' style='".(empty($mulVals) && empty($fValue)?'display:block;':'display:none;')."' name='attr_val[]' id='attr_val_$sno' ".(empty($mulVals)&& empty($fValue)?'required':'').">
			<option value=''> - Select value  - </option>".$valuesOpt."
			</select>	
			<div class='mulFilter-row' style='width:150px;".(empty($mulVals)?'display:none;':'display:block;')."'><select id='attr_val_list_$sno'  name='mul_attr_val_".($sno-1)."[]' class='form-control mulFilterValue".(empty($mulVals)?'':' required')."' multiple='multiple'>".$mulValuesOpt."				   
				</select>
			</div>			
			
			<select class='form-control between' style='".(!empty($fValue) && $filterData['filter_attr_id']!=18?'display:block;':'display:none;')."' name='f_attr_val[]' id='f_attr_val_$sno' ".(!empty($fValue) && $filterData['filter_attr_id']!=18?'required':'').">
			<option value=''> - Select value  - </option>".$fValuesOpt."
			</select>	
			
			<select class='form-control between' style='".(!empty($sValue) && $filterData['filter_attr_id']!=18?'display:block;':'display:none;')."' name='s_attr_val[]' id='f_attr_val_$sno' ".(!empty($sValue) && $filterData['filter_attr_id']!=18?'required':'').">
			<option value=''> - Select value  - </option>".$sValuesOpt."
			</select>					
			";		
		//if grainArray element 
		//$html.= in_array($filterData['filter_attr_id'],$grainArray)?(self::getSelectionBox($filterData['filter_attr_id'])):'';
		//if date of review		
		$html.= $filterData['filter_attr_id']==18?(self::getDateRow($fValue,$sValue)):'';
		$html .= "</td>
		<td>".$removeBtn."</td>			
		</tr>".$subFilterRow;
				
		return $html;
	}
	static function getDateRow($fval='',$sval=''){
		$html = "<input type='text' autocomplete='off' class='fdate' name='fdate' id='fdate' value='".$fval."' placeholder='From Date' title='From Date' readonly='' required>
				<input type='text' autocomplete='off' class='sdate' name='sdate' id='sdate' value='".$sval."' placeholder='To Date' title='To Date' readonly='' required>";
		return $html;
	}
	static function getSelectionBox($typeId,$label,$sno,$frm){
		$html = '<div style="display:none;" class="currentSelection tag_boxes clearfix sno_'.$sno.'">
					<span class="empty">Nothing selected yet</span>
				</div>';
		$html .= '<a data-size="850" data-postdata="#'.$frm.' .questionNode input[name^=\'question['.$typeId.']\']" class="btn btn-danger vtip execUrl" href="?controller=customreport&amp;action=diagnosticlevelques&amp;frm='.$frm.'&type='.$typeId.'&amp;ispop=1&amp;sno='.$sno.'" title="Click to select relevant questions.">Select '.$label.'</a>';
		return $html;
	}
	static function getExperienceRow($sno=1,$text=''){
		$removeBtn = $sno>1?'<a href="javascript:void(0)" class="delete_row"><i class="fa fa-times"></i></a>':'';
		$html = "<tr class='expRow'>";
		$html .= "<td class='s_no'>$sno</td>		
				  <td><textarea class='form-control' name='review_experience[]' id='review_experience_$sno' placeholder='Please enter your experience here'>".$text."</textarea></td>					   		
				<td>".$removeBtn."</td>";
		$html .= "</tr>";		
		return $html;
	}
	static function getDiagnosticGrainHtmlRow($grainLevel,$attr_id,$cardinality=1,$sub_attr_ids,$sno){	
		$grains = array("1"=>"KPA","2"=>"Key Question","3"=>"Sub Question","4"=>"Judgement Statement","5"=>"Judgement Distance");
		$count = count($grains);
		$grainOpt ='<select class="form-control subAttr" name="subattr_id['.$attr_id.']" ><option>--Rating Basis--</option>';
		for($i=$grainLevel;$i<=$count;$i++)
			$grainOpt.='<option value="'.$sub_attr_ids[$i-1]['attr_id'].'">'.$grains[$i].'</option>';
		$grainOpt.="</select>";
		$customreportModel = new self();
		//$ops = $customreportModel->getAttrSubOperators($attr_id);
		$operatorOpt = "";		
		//foreach($ops as $parameter)
			//$operatorOpt .= "<option value=\"$parameter[operator_id]\"".($filterData['filter_operator']==$parameter['operator_id']?'selected=selected':'').">".$parameter['operator_text']."</option>";
		/*$cardinalityFactor = 0;
		switch($attr_id){
			case 13 : $cardinalityFactor = $cardinality*pow(3,0);
			break;
			case 23 : $cardinalityFactor = $cardinality*pow(3,1);
			break;
			case 24 : $cardinalityFactor = $cardinality*pow(3,2);
			break;
			case 25 : $cardinalityFactor = $cardinality*pow(3,3);
			break;
		}*/
		
		$operatorOpt = "<select class='form-control subOperator' name='suboperator_id[".$attr_id."]' ><option value=''>--Operator--</option>".$operatorOpt."</select>";
		$cardinalityOpt = "<select class='form-control subCardinality' name='subcardinality_id[".$attr_id."]' ><option value=''>--Number--</option></select>";
		$ratingsOpt = "<select class='form-control subCriteria' name='subcriteria_id[".$attr_id."]' ><option value=''>--Criteria--</option></select>";
		
		$html = "<tr class='subrow' data-sno='".$sno."'><input type='hidden' class='maxcardinality' name='max_cardinality[".$attr_id."]' /><td></td><td>".$grainOpt."</td><td>".$operatorOpt.$cardinalityOpt."</td><td>$ratingsOpt</td><td></td></tr>";
		return $html;
	}
	function getDiagnosticGrainHtmlRowforEdit($grainLevel,$attr_id,$cardinality,$sub_attr_ids,$sno,$subattr_data,$selectedGrain){		
		$grains = array("1"=>"KPA","2"=>"Key Question","3"=>"Sub Question","4"=>"Judgement Statement","5"=>"Judgement Distance");
		$count = count($grains);
		$criteria = array();
		//$operators = '';
		$operatorOpt='';
		$grainOpt ='<select class="form-control subAttr" name="subattr_id['.$attr_id.']" ><option value="">--Rating Basis--</option>';
		for($i=$grainLevel;$i<=$count;$i++)
			$grainOpt.='<option value="'.$sub_attr_ids[$i-1]['attr_id'].'" '.($selectedGrain==$i?"selected=selected":"").'>'.$grains[$i].'</option>';
			$grainOpt.="</select>";
			$customreportModel = new self();
			//$ops = $customreportModel->getAttrSubOperators($attr_id);						
			$cardinalityFactor = 0;
			switch($attr_id){
				case 13 : $cardinalityFactor = $cardinality*pow(3,0);
				break;
				case 23 : $cardinalityFactor = $cardinality*pow(3,1);
				break;
				case 24 : $cardinalityFactor = $cardinality*pow(3,2);
				break;
				case 25 :
        		case 27: $cardinalityFactor = $cardinality*pow(3,3);
        		break;
			}
			$crdnArr = "";			
			$criteriaOpt ="";
			for($i=1;$i<=$cardinalityFactor;$i++){				
				$crdnArr.='<option value="'.$i.'" '.($i==$subattr_data['filter_sub_attr_cardinality']?"selected=selected":"").'>'.$i.'</option>';
			}
			$is_judgement = $attr_id==25?1:0;
			if($subattr_data['filter_sub_attr_id']==27){//for judgement distance
				array_push($criteria,array("rating_id"=>'0',"rating"=>"Agreement"));
				array_push($criteria,array("rating_id"=>'1',"rating"=>"Disagreement by One"));
				array_push($criteria,array("rating_id"=>'2',"rating"=>"Disagreement by Two"));
				array_push($criteria,array("rating_id"=>'3',"rating"=>"Disagreement by Three"));
			}
			else
				$criteria = $customreportModel->getRatingForAttr($is_judgement);			
			foreach($criteria as $c){
				$criteriaOpt.="<option value='".$c['rating_id']."' ".($c['rating_id']==$subattr_data['filter_sub_attr_rating']?'selected=selected':'').">".$c['rating']."</option>";
			}
			$operators = $customreportModel->getAttrSubOperators($attr_id);				
			foreach($operators as $op){
				$operatorOpt.="<option value='".$op['operator_id']."' ".($op['operator_id']==$subattr_data['filter_sub_attr_operator']?'selected=selected':'').">".$op['operator_text']."</option>";
			}
			
			$operatorOpt = "<select class='form-control subOperator' name='suboperator_id[".$attr_id."]' ><option value=''>--Operator--</option>".$operatorOpt."</select>";
			$cardinalityOpt = "<select class='form-control subCardinality' name='subcardinality_id[".$attr_id."]' ><option value=''>--Number--</option>".$crdnArr."</select>";
			$ratingsOpt = "<select class='form-control subCriteria' name='subcriteria_id[".$attr_id."]' ><option value=''>--Criteria--</option>".$criteriaOpt."</select>";
	
			$html = "<tr class='subrow' data-sno='".$sno."'><input type='hidden' class='maxcardinality' value='".$subattr_data['filter_sub_attr_max_cardinality']."' name='max_cardinality[".$attr_id."]' /><td></td><td>".$grainOpt."</td><td>".$operatorOpt.$cardinalityOpt."</td><td>$ratingsOpt</td><td></td></tr>";
			return $html;
	}
	private function getFilterParams($awardScheme=0){
		$sql = "select filter_attr_id,filter_attr_name,filter_table,filter_table_col_id,filter_table_col_name from d_filter_attr where active=1 ";
		$awardScheme==1?$sql.=' and filter_attr_id=9 and filter_attr_id<=12':$sql.=' and filter_attr_id!=9 and filter_attr_id<=12';
		$res = $this->db->get_results($sql);
		return $res?$res:array();
		
	}
	private function getAllFilterParams(){
		$sql = "select filter_attr_id,filter_attr_name,filter_table,filter_table_col_id,filter_table_col_name from d_filter_attr where active=1 and filter_attr_id not in(26,27) order by filter_attr_name";		
		$res = $this->db->get_results($sql);
		return $res?$res:array();
	
	}
	function getFilterOperators(){
		$res = $this->db->get_results("SELECT operator_id,operator_text FROM d_filter_operator;");
		return $res?$res:array();
	
	}
	function saveFilter($filter_name,$added_by){		
		if($this->db->insert("d_filter",array("filter_name"=>$filter_name,"added_by"=>$added_by,"create_date"=>date("Y-m-d H:i:s"))))
			return $this->db->get_last_insert_id();
		return false;
	}
	function updateFilter($filter_id,$filter_name){
		if($this->db->update("d_filter",array("filter_name"=>$filter_name),array("filter_id"=>$filter_id)))
			return true;
			return false;
	}
	function saveFilterAttr($filter_id,$filter_attr_id,$filter_operator,$filter_attr_value,$filter_attr_value_text,$filter_f_value,$filter_s_value){
		if($this->db->insert("h_filter_attr",array("filter_id"=>$filter_id,"filter_attr_id"=>$filter_attr_id,"filter_operator"=>$filter_operator,"filter_attr_value"=>$filter_attr_value,"filter_attr_value_text"=>$filter_attr_value_text,"filter_f_value"=>$filter_f_value,"filter_s_value"=>$filter_s_value)))
			return $this->db->get_last_insert_id();
		return false;
	}
	function deleteFilterAttr($filter_id){
		if($this->db->delete("h_filter_attr",array("filter_id"=>$filter_id)))
			return true;
			return false;
	}
	function saveFilterAttrMulVals($filter_instance_id,$val){
		if($this->db->insert("h_filter_multiple_vals",array("filter_instance_id"=>$filter_instance_id,"value"=>$val)))
			return true;
			return false;
	}
	function deleteFilterAttrMulVals($filter_instance_id){
		if($this->db->delete("h_filter_multiple_vals",array("filter_instance_id"=>$filter_instance_id)))
			return true;
			return false;
	}
	function deleteSubFilterAttr($filter_instance_id){
		if($this->db->delete("h_filter_sub_attr",array("filter_instance_id"=>$filter_instance_id)))
			return true;
			return false;
	}
	function getAttrOperators($attr_id){
		$res = $this->db->get_results("SELECT fao.operator_id,if(operator_text='!=','Not equals to',operator_text) as operator_text FROM h_filter_attr_operator fao inner join d_filter_operator o on fao.operator_id=o.operator_id where filter_attr_id=?",array($attr_id));
		return $res?$res:array();
	}
	function getAttrSubOperators($attr_id){
		$res = $this->db->get_results("SELECT fao.operator_id,operator_text FROM h_filter_sub_attr_operator fao inner join d_filter_operator o on fao.operator_id=o.operator_id where filter_sub_attr_id=?",array($attr_id));
		return $res?$res:array();
	}
	function checkDuplicateReportName($repname){
		$sql = "SELECT report_name from h_network_report where report_name=?";
		$res = $this->db->get_row($sql,array($repname));
		return $res?true:false;
	}	
	function checkDuplicateFilterName($filter_name){
		$sql = "SELECT filter_name from d_filter where filter_name=?";
		$res = $this->db->get_row($sql,array($filter_name));
		return $res?true:false;
	}
	function getAttrValues($attr_id,$csId=0,$isPivot=0){
		//for country
		//$country_id = $attr_id==3? 101:0;
		//$attr_id==3?($csId=10):0;
		$sql = "SELECT filter_table,filter_table_col_id,filter_table_col_name FROM d_filter_attr where filter_attr_id='$attr_id'
					into @table_name,@col_id,@col_text";
		if(!$this->db->query($sql))
			return false;
		$res = $this->db->get_row("select @table_name,@col_id,@col_text order by @col_text;");	
		$table_name = $res['@table_name'];
		$col_id = $res['@col_id'];
		$col_text = $res['@col_text'];	
		if($attr_id==12)//province
			$res = $this->db->get_results("select $col_id as province,$col_text as province_text from $table_name where $col_id!=? and $col_id!=''  order by $col_text",array(0));
		elseif($attr_id==16)//if year			
			$res = $this->db->get_results("select distinct year(str_to_date($col_text,'%m/%d/%Y')) yearid, year(str_to_date($col_text,'%m/%d/%Y')) year from $table_name where $col_id!=? and $col_text is not null and $col_text<>'' order by $col_text",array(0));
		elseif($attr_id==17)//if month
			$res = $this->db->get_results("select distinct month(str_to_date($col_text,'%m/%d/%Y')) monthid, month(str_to_date($col_text,'%m/%d/%Y')) month from $table_name where $col_id!=? and $col_text is not null and $col_text<>'' order by $col_text",array(0));		
		elseif($attr_id==21)//if external reviewer
			$res = $this->db->get_results("select distinct user_id,name from d_user inner join h_user_user_role using(user_id) where role_id=4;");
		elseif($attr_id==22)//number of reviews for school review only			
			$res = $this->db->get_results("select distinct count(*) as num_review_id,count(*) as num_reviews from d_assessment inner join d_diagnostic using(diagnostic_id) where assessment_type_id=1 and client_id not in (115) group by client_id order by num_reviews");	
		else	
			$res = $this->db->get_results("select $col_id,$col_text from $table_name where $col_id!=? and $col_text is not null and $col_text<>''".($attr_id==3?" AND country_id=$csId":($attr_id==11?" AND state_id=$csId":'')." order by $col_text"),array(0));		
		
		if($isPivot==1)
			return array("tbl_name"=>$table_name,"tbl_col"=>$col_id,"tbl_col_name"=>$col_text);
		return $res?$res:array();
	}
	function getFilters($added_by=0){
		$sql = "SELECT filter_id,filter_name FROM d_filter WHERE 1=1 ";
		$sql .= $added_by>0?' AND added_by='.$added_by:'';
		$res = $this->db->get_results($sql);
		return $res?$res:array();
	}
	function getRecentFilter($added_by){
		$sql = "SELECT filter_id,filter_name FROM d_filter WHERE added_by = ? order by filter_id desc limit 1 ";		
		$res = $this->db->get_row($sql,array($added_by));
		return $res?$res:array();
	}
	function getFilterData($filter_id,$attr_id=0){		
		if($attr_id>0){			
		$sql = "SELECT fa.filter_attr_value FROM d_filter df
				 inner join h_filter_attr fa on df.filter_id=fa.filter_id
				 left join h_filter_multiple_vals mv on fa.filter_instance_id=mv.filter_instance_id
				 where df.filter_id= ?  AND fa.filter_attr_id=?
				 group by fa.filter_instance_id;";
			$res = $this->db->get_row($sql,array($filter_id,$attr_id));
			return $res?$res:array();
		}
		else{
			$sql = "SELECT df.*,fa.*,group_concat(mv.value) as mul FROM d_filter df
				 inner join h_filter_attr fa on df.filter_id=fa.filter_id
				 left join h_filter_multiple_vals mv on fa.filter_instance_id=mv.filter_instance_id
				 where df.filter_id= ?
				 group by fa.filter_instance_id;";
			$res = $this->db->get_results($sql,array($filter_id));
			return $res?$res:array();
		}
	}
	function getSubFilterData($filter_instance_id){	
			$sql = "select * from h_filter_sub_attr where filter_instance_id=?";
			$res = $this->db->get_row($sql,array($filter_instance_id));
			return $res?$res:array();		
	}
	function getAttrLabel($attr_id){
		$sql = "select filter_attr_name from d_filter_attr where filter_attr_id=?";
		$res = $this->db->get_row($sql,array($attr_id));
		return $res?$res:array();
	}
	function applyFilterQuery($filter_id){
		$sql = "SELECT dfa.filter_table,dfa.filter_attr_id,dfa.filter_attr_name,dfa.filter_table_col_id,filter_table_col_name,fa.filter_instance_id,fo.operator_id,(fo.operator_text),if(group_concat(mv.value) is not null,
				group_concat(mv.value),fa.filter_attr_value) as value,filter_f_value,filter_s_value
				 FROM d_filter df
				 inner join h_filter_attr fa on df.filter_id=fa.filter_id
				 inner join d_filter_attr dfa on  dfa.filter_attr_id=fa.filter_attr_id
				 inner join h_filter_attr_operator fao on fao.filter_attr_id=fa.filter_attr_id and fa.filter_operator=fao.operator_id
				 inner join d_filter_operator fo on fo.operator_id= fao.operator_id
				 left join h_filter_multiple_vals mv on fa.filter_instance_id=mv.filter_instance_id
				 where df.filter_id=?
				 group by fa.filter_instance_id";
		$res = $this->db->get_results($sql,array($filter_id));
		return $res?$res:array();
	}
	function getStaticTableVals($table_name,$table_col_id,$table_col_name,$vals){
		$sql = "SELECT group_concat(concat(\"'\",$table_col_name,\"'\")) as staticCol FROM $table_name where $table_col_id in($vals)";		
		$res = $this->db->get_row($sql);
		return $res?$res:array();
	}
	function getAQSclients($params){
		/*$sql="select distinct c.client_id,c.client_name,a.assessment_id,aqs.school_aqs_pref_start_date as start_date,aqs.school_aqs_pref_end_date as end_date from d_client c inner join d_assessment a on c.client_id=a.client_id
			inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id and d.assessment_type_id=1	and d.diagnostic_id in(1,2)
 			inner join d_AQS_data aqs on aqs.id=a.aqsdata_id left join h_client_network n on c.client_id=n.client_id WHERE 
			 d_sub_assessment_type_id!=1 AND aqs.school_aqs_pref_start_date!='' AND aqs.school_aqs_pref_end_date!=''
				AND aqs.school_aqs_pref_start_date IS NOT NULL AND aqs.school_aqs_pref_end_date IS NOT NULL AND a.internal_award IS NOT NULL AND a.external_award IS NOT NULL AND ".$params."  GROUP BY c.client_id ORDER BY 
 					c.client_name asc, str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc";*/
		$sql1 = "select @row_number:=0;
				select @row_number1:=0;";
		$sql2 = "select t2.* from (select (@row_number1:=@row_number1+1) as rowId,c.client_id,c.client_name,a.assessment_id,aqs.school_aqs_pref_start_date as start_date,aqs.school_aqs_pref_end_date as end_date from d_client c inner join d_assessment a on c.client_id=a.client_id
				inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id and d.assessment_type_id=1	
	 			inner join d_AQS_data aqs on aqs.id=a.aqsdata_id left join h_client_network n on c.client_id=n.client_id WHERE c.client_id not in (115,154,175) AND
				d_sub_assessment_type_id!=1 AND aqs.school_aqs_pref_start_date!='' AND aqs.school_aqs_pref_end_date!=''
				AND aqs.school_aqs_pref_start_date IS NOT NULL AND aqs.school_aqs_pref_end_date IS NOT NULL AND a.internal_award IS NOT NULL AND a.external_award IS NOT NULL AND ".$params." ORDER BY 
	 		    str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc,rowId ) t2
				inner join
			   (select max(t1.rowId) rowId,t1.client_id from (
			    select (@row_number:=@row_number+1) as rowId,c.client_id,c.client_name,a.assessment_id,aqs.school_aqs_pref_start_date as start_date,aqs.school_aqs_pref_end_date as end_date from d_client c inner join d_assessment a on c.client_id=a.client_id
				inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id and d.assessment_type_id=1	
 				inner join d_AQS_data aqs on aqs.id=a.aqsdata_id left join h_client_network n on c.client_id=n.client_id WHERE c.client_id not in (115,154,175) AND
			 	d_sub_assessment_type_id!=1 AND aqs.school_aqs_pref_start_date!='' AND aqs.school_aqs_pref_end_date!=''
				AND aqs.school_aqs_pref_start_date IS NOT NULL AND aqs.school_aqs_pref_end_date IS NOT NULL AND a.internal_award IS NOT NULL AND a.external_award IS NOT NULL AND ".$params."  ORDER BY 
 				str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc,rowId ) as t1
                group by t1.client_id ) t3 on t2.rowId=t3.rowId order by  t2.client_name;";
		$res = $this->db->query($sql1);
		$res = $this->db->get_results($sql2);
		return $res?$res:array();
	}
	function createAQSclientsTbl($params,$network_report_id){
		/*$sql="drop table if exists aqsclients;
				create temporary table aqsclients 
				select distinct c.client_id,c.client_name,a.assessment_id,aqs.school_aqs_pref_start_date as start_date,aqs.school_aqs_pref_end_date as end_date from 
				h_network_report r inner join h_network_report_clients rc on r.network_report_id = rc.network_report_id and r.network_report_id=?
				inner join d_client c on c.client_id = rc.client_id 				
				inner join d_assessment a on c.client_id=a.client_id
			inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id and d.assessment_type_id=1 and d_sub_assessment_type_id!=1 and d.diagnostic_id in(1,2)
 			inner join d_AQS_data aqs on aqs.id=a.aqsdata_id left join h_client_network n on c.client_id=n.client_id WHERE
			 d_sub_assessment_type_id!=1 AND aqs.school_aqs_pref_start_date!='' AND aqs.school_aqs_pref_end_date!=''
				AND aqs.school_aqs_pref_start_date IS NOT NULL AND aqs.school_aqs_pref_end_date IS NOT NULL AND a.internal_award IS NOT NULL AND a.external_award IS NOT NULL AND ".$params."  GROUP BY c.client_id ORDER BY
 					str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc";*/
		$sql ="select @row_number:=0;
			   select @row_number1:=0;
				drop temporary table if exists aqsclients;
				create temporary table aqsclients				
			   select t2.* from (select (@row_number1:=@row_number1+1) as rowId,c.client_id,c.client_name,a.assessment_id,aqs.school_aqs_pref_start_date as start_date,aqs.school_aqs_pref_end_date as end_date from 
			   h_network_report r inner join h_network_report_clients rc on r.network_report_id = rc.network_report_id and r.network_report_id=?
			    inner join d_client c on c.client_id = rc.client_id 				
			    inner join d_assessment a on c.client_id=a.client_id
			    inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id and d.assessment_type_id=1 and d_sub_assessment_type_id!=1 
 			    inner join d_AQS_data aqs on aqs.id=a.aqsdata_id left join h_client_network n on c.client_id=n.client_id WHERE
			    d_sub_assessment_type_id!=1 AND aqs.school_aqs_pref_start_date!='' AND aqs.school_aqs_pref_end_date!=''
			    AND aqs.school_aqs_pref_start_date IS NOT NULL AND aqs.school_aqs_pref_end_date IS NOT NULL AND a.internal_award IS NOT NULL AND a.external_award IS NOT NULL AND ".$params." ORDER BY
 			   str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc,rowId ) t2 
			   inner join
			   (select max(t1.rowId) rowId,t1.client_id from (
			   select (@row_number:=@row_number+1) as rowId,c.client_id,c.client_name,a.assessment_id,aqs.school_aqs_pref_start_date as start_date,aqs.school_aqs_pref_end_date as end_date from 
			   h_network_report r inner join h_network_report_clients rc on r.network_report_id = rc.network_report_id and r.network_report_id=?
			   inner join d_client c on c.client_id = rc.client_id 				
			   inner join d_assessment a on c.client_id=a.client_id
			   inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id and d.assessment_type_id=1 and d_sub_assessment_type_id!=1 
 			   inner join d_AQS_data aqs on aqs.id=a.aqsdata_id left join h_client_network n on c.client_id=n.client_id WHERE
			   d_sub_assessment_type_id!=1 AND aqs.school_aqs_pref_start_date!='' AND aqs.school_aqs_pref_end_date!=''
			   AND aqs.school_aqs_pref_start_date IS NOT NULL AND aqs.school_aqs_pref_end_date IS NOT NULL AND a.internal_award IS NOT NULL AND a.external_award IS NOT NULL AND ".$params." ORDER BY
 			   str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc,rowId ) as t1
               group by t1.client_id ) t3 on t2.rowId=t3.rowId;";		
		 	  $this->db->query($sql,array($network_report_id,$network_report_id));		 	  
		 	 $res = $this->db->get_results("select * from aqsclients");
			return $res?$res:array();	 	 
	}
	function saveNetworkReport($report_id,$report_name,$filter_id,$review_experience,$include_self_review=0,$is_validated=0)
	{
		if($this->db->insert('h_network_report',array("report_id"=>$report_id,"report_name"=>$report_name,"filter_id"=>$filter_id,"review_experience"=>$review_experience,"include_self_review"=>$include_self_review,"is_validated"=>$is_validated)))
			return $this->db->get_last_insert_id();
		return false;
	}
	function updateNetworkReport($review_experience,$network_report_id)
	{		
		if($this->db->update("h_network_report",array("review_experience"=>$review_experience),array("network_report_id"=>$network_report_id)))
			return true;
		return false;
	}
	function saveNetworkReportClients($network_report_id,$client_id)
	{
		if($this->db->insert('h_network_report_clients',array("network_report_id"=>$network_report_id,"client_id"=>$client_id)))
			return true;
			return false;
	}
	function getNetworkReports()
	{
		$sql = "SELECT network_report_id,report_name,create_date from h_network_report";
		$res = $this->db->get_results($sql);
		return $res?$res:array();
	}
	function getNetworkReportsList($args = array())
	{
		$args=$this->parse_arg($args,array("report_name_like"=>"","max_rows"=>10,"page"=>1,"order_by"=>"","order_type"=>""));
		$order_by=array("create_date"=>"create_date","report_name"=>"report_name");
		$sqlArgs=array();
	
		$sql = "SELECT SQL_CALC_FOUND_ROWS h_network_report.*,network_report_id,report_name,create_date from h_network_report where 1 = 1 ";
		if($args['report_name_like']!=""){
			$sql.="and report_name like ? ";
			$sqlArgs[]="%".$args['report_name_like']."%";
		}
		
		$sql.=" order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"create_date").($args["order_type"]=="desc"?" desc ":" asc ").$this->limit_query($args['max_rows'],$args['page']);
		$res= $this->db->get_results($sql,$sqlArgs);
		$this->setPageCount($args['max_rows']);		
		return $res;
	}
	function getNetworkReportData($network_report_id)
	{
		$sql = "SELECT nr.*,group_concat(nrc.client_id) clients FROM h_network_report nr inner join h_network_report_clients nrc on
				nr.network_report_id=nrc.network_report_id where nr.network_report_id=? group by  nr.network_report_id";
		$res = $this->db->get_row($sql,array($network_report_id));
		return $res?$res:array();
	}
         // function for getting getting schools which have completed their review on 28-04-2016 by Mohit Kumar
    function getSchoolIdByNetworkId($network_id){
        $SQL="Select t1.client_id,t2.assessment_id,GROUP_CONCAT(t3.user_id) as user_id from d_client t1 Left Join d_assessment t2 On "
            . "(t1.client_id=t2.client_id) left Join h_assessment_user t3 On (t2.assessment_id=t3.assessment_id) Left Join h_client_network t4 On "
            . "(t1.client_id=t4.client_id) where t4.network_id='".$network_id."' and t3.isFilled='1' and percComplete='100.00' group by client_id";
        $data = $this->db->get_results($SQL);
        $final = array();
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $user_id = explode(',', $value['user_id']);
                if(count($user_id)==2){
                    $final[]=$value;
                }
            }
        }        
        return $final;
    }
    
    // function for getting start date and end date for review
    function getStartEndDate($start_client_data,$end_client_data){
        $SQL="SELECT min(str_to_date(start_date,'%m/%d/%Y')) as startdate,max(str_to_date(end_date,'%m/%d/%Y')) as enddate from aqsclients;";        
        $res = $this->db->get_row($SQL);
        return $res?$res:array();
    }
    
    // function for getting school awards

 // function for getting school awards
    function getSchoolAwards(){
    	$SQL="drop temporary table if exists clientlist;
			create temporary table clientlist
			select c.client_id,c.client_name,language_name,board,brd.board_id,aqs.annual_fee,fee.fee_id,school_type,region_name,c.city,c.city_id,st.state_name,st.state_id,a.assessment_id,a.diagnostic_id,a.award_scheme_id,a.internal_award,a.external_award,a.d_sub_assessment_type_id
			 from d_client c inner join h_client_network n on n.client_id=c.client_id			
			inner join d_assessment a on a.client_id = c.client_id
    		inner join aqsclients aq on aq.client_id = a.client_id and a.assessment_id = aq.assessment_id
			inner join d_AQS_data aqs on aqs.id=a.aqsdata_id
    		inner join d_board brd on brd.board_id = aqs.board_id	
            left join d_school_type dst on dst.school_type_id=aqs.school_type_id
            left join d_language med on med.language_id=aqs.medium_instruction
            left join d_school_region sr on sr.region_id = aqs.school_region_id	
    		left join d_states st on c.state_id = st.state_id	
    		left join d_fees fee on fee.fee_text=aqs.annual_fee	
    		where c.client_id not in (115,154,175)	
			group by c.client_id
			order by str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc";
    	$this->db->query($SQL);    	
        $SQL="drop temporary table if exists networkawards;
			create temporary table networkawards
			select client_id,client_name,city,language_name,board,annual_fee,state_name,school_type,region_name,replace(replace(award_name_template,'<Award>',award_name),'<Tier>',standard_name) as external_award_text,d.diagnostic_id from clientlist a
			inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id and d.assessment_type_id=1
			inner join h_award_scheme b on a.award_scheme_id = b.award_scheme_id
			inner join d_award_scheme aws on aws.award_scheme_id = a.award_scheme_id
			inner join d_award da on da.award_id = b.award_id
			left join d_tier e on e.standard_id = b.tier_id
			where b.order = a.external_award and d_sub_assessment_type_id!=1 and a.external_award is not null   
            group by a.client_id order by b.tier_id,b.award_id
        ";
        $this->db->query($SQL);		 
        $SQL1="select * from networkawards;";
        $data = $this->db->get_results($SQL1);
        return $data;
    }
    function getDataAwardParamCount($param){
    	$sql = "select count(distinct $param) num from clientlist";
    	$res = $this->db->get_row($sql);
    	return $res ? $res:array();
    }
    function getDataAwardParamGroupCount($param,$groupby){
    	$sql = "select count(distinct $param) num,$groupby param from networkawards group by $groupby";
    	$res = $this->db->get_results($sql);
    	return $res ? $res:array();    
    }
	function getInternalAwards(){
		$SQL="select client_id,client_name,city,replace(replace(award_name_template,'<Award>',award_name),'<Tier>',standard_name) as internal_award_text from clientlist a
			inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id and d.assessment_type_id=1
			inner join h_award_scheme b on a.award_scheme_id = b.award_scheme_id
			inner join d_award_scheme aws on aws.award_scheme_id = a.award_scheme_id
			inner join d_award da on da.award_id = b.award_id
			left join d_tier e on e.standard_id = b.tier_id
			where b.order = a.internal_award and d_sub_assessment_type_id!=1 and a.internal_award is not null   
            group by a.client_id order by b.award_id
        ";
        
        $data = $this->db->get_results($SQL);
        return $data;
	}
    function getPerformaceKPAS(){
    	$sql="drop temporary table if exists tempratingGraph;
				create temporary table tempratingGraph select distinct d.kpa_id,d.kpa_name ,c.kpa_order,ast.client_name,e.*
				from clientlist ast
				inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1
				inner join h_kpa_instance_score a on a.assessment_id=ast.assessment_id
									inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id and c.kpa_order < 7
									inner join d_kpa d on d.kpa_id = c.kpa_id
									inner join d_rating e on a.d_rating_rating_id = e.rating_id
									inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id and f.role = 4					
									inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id					
									group by kpa_name,e.rating_id,ast.client_id
								order by c.`kpa_order` asc;  
    			select count(distinct client_name)  into @total from tempratingGraph ;
    			";
    	$this->db->query($sql);
    	$sql = "select kpa_id,kpa_name,rating_id,rating,count(rating) as num,@total, count(rating)*100/@total as 'percentage',count(distinct client_name) as 'num'  from tempratingGraph t group by kpa_name,rating_id order by kpa_order; ";
    	//$sql = "select kpa_id,kpa_name,rating_id,rating,count(rating) as num,@total, count(distinct client_name) as 'percentage'  from tempratingGraph t group by kpa_name,rating_id order by kpa_id; ";
    	$data = $this->db->get_results($sql);
    	return $data;
    }
    function getAllSchoolPerf(){
    	$sql = "select * from tempratingGraph group by client_name,kpa_name order by client_name,kpa_order";
    	$data = $this->db->get_results($sql);
    	return $data;
    }
    function createKPACQRating(){
    	/* $sql="drop temporary table if exists kpaCQexternalgraph;
create temporary table kpaCQexternalgraph select kpa.kpa_id,kpa.kpa_name,cq_order,core_question_text,rating,e.rating_id,ast.client_id,ast.client_name,h.order as numericRating from
clientlist ast inner join h_cq_score a on a.assessment_id = ast.assessment_id
inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1				 
					 inner join h_kq_cq c on a.core_question_instance_id = c.core_question_instance_id
					 inner join d_core_question d on d.core_question_id = c.core_question_id
					 inner join d_rating e on a.d_rating_rating_id = e.rating_id
					 inner join h_assessment_user f on a.assessor_id = f.user_id and ast.assessment_id = f.assessment_id and f.role=4 
                     inner join h_kpa_diagnostic i on i.diagnostic_id = ast.diagnostic_id and i.kpa_order <7 
					 inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and c.key_question_instance_id = j.key_question_instance_id and i.kpa_instance_id=j.kpa_instance_id                    
                   inner join d_kpa kpa on kpa.kpa_id = i.kpa_id
					 inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id					 
					 	order by  c.`cq_order` asc;
    	Select count(distinct client_id) into @total  from kpaCQexternalgraph;		
    "; */
    	
    $sql = "           
drop temporary table if exists kpaCQexternalgraph;
create temporary table kpaCQexternalgraph select distinct kpa.kpa_id,kpa.kpa_name,d.core_question_id,c.key_question_instance_id as kqid,kq_order,cq_order,core_question_text,rating,e.rating_id,ast.client_id,ast.client_name,h.order as numericRating from
clientlist ast inner join h_cq_score a on a.assessment_id = ast.assessment_id
inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1				 
					 inner join h_kq_cq c on a.core_question_instance_id = c.core_question_instance_id
					 inner join d_core_question d on d.core_question_id = c.core_question_id
					 inner join d_rating e on a.d_rating_rating_id = e.rating_id
					 inner join h_assessment_user f on a.assessor_id = f.user_id and ast.assessment_id = f.assessment_id and f.role=4 
                     inner join h_kpa_diagnostic i on i.diagnostic_id = ast.diagnostic_id and i.kpa_order <7 
					 inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and c.key_question_instance_id = j.key_question_instance_id and i.kpa_instance_id=j.kpa_instance_id                    
                   inner join d_kpa kpa on kpa.kpa_id = i.kpa_id
					 inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id					 
					 	order by  i.kpa_order,kq_order,c.`cq_order` asc;
    	Select count(distinct client_id) into @total  from kpaCQexternalgraph;	
       ";	
    	$this->db->query($sql);    	
    }
    function createDiagnosticStmtNumbers($diagnostic_id){
		$sql = "drop temporary table if exists tempdiagorder;
create temporary table tempdiagorder  
 select kpa.kpa_id,kpa_name,key_question_text,core_question_text,judgement_statement_text,kpa_order,kq_order,cq_order,js_order from d_diagnostic d inner join h_kpa_diagnostic kd on d.diagnostic_id=kd.diagnostic_id
 inner join d_kpa kpa on kpa.kpa_id=kd.kpa_id
 inner join h_kpa_kq hkq on hkq.kpa_instance_id = kd.kpa_instance_id
 inner join d_key_question kq on  kq.key_question_id = hkq.key_question_id
 inner join h_kq_cq hcq on hcq.key_question_instance_id = hkq.key_question_instance_id
 inner join d_core_question cq on cq.core_question_id = hcq.core_question_id
 inner join h_cq_js_instance hjs on hjs.core_question_instance_id = hcq.core_question_instance_id
 inner join d_judgement_statement js on js.judgement_statement_id = hjs.judgement_statement_id
 where d.diagnostic_id=?
 order by kpa_order,kq_order,cq_order,js_order;
";
		$this->db->query ( $sql, array (
				$diagnostic_id 
		) );
		$sql = " 
select @running:=0;
select @running1:=0;
select @previous:='';
select @previous1:='';
 drop temporary table  if exists diagnosticStmtNum;
 create temporary table diagnosticStmtNum as   		
  select kpa_id,kpa_name,KQnum,key_question_text,CQnum,core_question_text,judgement_statement_text from(
 select  @running:=if(@previous=concat(kpa_name,key_question_text),@running,if(@running=3,1,@running+1)) as KQnum, 
        @previous:=concat(kpa_name,key_question_text),
        @running1:=if(@previous1=concat(kpa_name,key_question_text,core_question_text),@running1,if(@running1=9,1,@running1+1)) as CQnum,
        @previous1:=concat(kpa_name,key_question_text,core_question_text),@running1,if(@running1=9,1,@running1+1),
       kpa_id, kpa_name,key_question_text,core_question_text,judgement_statement_text
        from tempdiagorder ) b;";
		$this->db->query ( $sql );
    }
    function getKPAKqCqstatements($kpa_id,$kqnum,$cqnum){
    	$sql = "select distinct key_question_text,core_question_text,judgement_statement_text from diagnosticStmtNum where kpa_id=? and KQnum=? and CQnum=?
 				order by KQnum,CQnum asc; ";
    	$res = $this->db->get_results($sql,array($kpa_id,$kqnum,$cqnum));
    	return $res?$res:array();
    }
    function getKPAKqCqTable($data,$cqnum){
    	$numToAlph = array(1 => "a", 2 => "b", 3 => "c");
    	$j=1;
    	$html = '<tr style="font-weight:bold;"><td style="border:1px solid #000000;">SQ'.$cqnum.'</td><td style="border:1px solid #000000;">'.$data[0]['core_question_text'].'</td></tr>';
		foreach($data as $js)
			$html.='<tr><td style="border:1px solid #000000;">'.$cqnum.$numToAlph[$j++].'</td><td style="border:1px solid #000000;">'.$js['judgement_statement_text'].'</td></tr>';    	    
    	return $html;
    }
    function getKPAKQJSTable($data,$kqnum,$tableNum){
    	$numToAlph = array(1 => "a", 2 => "b", 3 => "c");
    	$numToRating =  array(1=>"R",2=>"S",3=>"M",4=>"A"); 
    	$numToColor =  array(1=>"color:#D12200;",2=>"color:#dbb113;",3=>"color:#5e9900;",4=>"color:#307ACE;");
    	$j=1;
    	$cqnum = ($kqnum -1)*3+1;
    	$html = '<br/><table border="0" cellpadding="3"><thead>
    			<tr><th colspan="10"><span style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10">Table '.$tableNum.'</span></th></tr>
    			<tr style="text-align:center;font-weight:bold;border:1px solid #000000;"><th style="width:37%;border:1px solid #000000;">Judgement Statements</th><th style="width:7%;border:1px solid #000000;">'.$cqnum.$numToAlph[1].'</th>
    				<th style="width:7%;border:1px solid #000000;">'.$cqnum.$numToAlph[2].'</th>
    				<th style="width:7%;border:1px solid #000000;">'.$cqnum++.$numToAlph[3].'</th>
    				<th style="width:7%;border:1px solid #000000;">'.$cqnum.$numToAlph[1].'</th>
					<th style="width:7%;border:1px solid #000000;">'.$cqnum.$numToAlph[2].'</th>
					<th style="width:7%;border:1px solid #000000;">'.$cqnum++.$numToAlph[3].'</th>
					<th style="width:7%;border:1px solid #000000;">'.$cqnum.$numToAlph[1].'</th>
					<th style="width:7%;border:1px solid #000000;">'.$cqnum.$numToAlph[2].'</th>
					<th style="width:7%;border:1px solid #000000;">'.$cqnum++.$numToAlph[3].'</th>	
					</tr>		
    			</thead>';
    	 foreach($data as $row){
    	 	$ratings = explode(',',$row['rating']);    	 	
    	 	$jd = explode(',',$row['jd']);
    	 	$html.='<tr>
    	 			<td style="width:37%;text-align:left;border:1px solid #000000;">'.$row['client_name'].'</td>';
    	 		foreach($ratings as $k=>$r)
    	 			$html.='<td  style="border:1px solid #000000;text-align:center;width:7%;font-weight:bold;'.($jd[$k]==1||$jd[$k]==0?"background-color:#CCCCCC;":"").$numToColor[$r].'">'.$numToRating[$r].'</td>';
    	 	$html.= '</tr>';
    	 }
    	$html .='</table>';    	
    	return $html;
    }
    function createKPAParameterWiseAnalysis(){
    	$sql = "drop temporary table if exists KPAParameterWiseGraph;
    			create temporary table KPAParameterWiseGraph select distinct d.kpa_id,ast.client_id,state_id,fee_id,board_id,state_name 'state',board 'board',annual_fee 'fee',h.order as numericRating,e.rating from
    			clientlist ast
				inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1
				inner join h_kpa_instance_score a on a.assessment_id=ast.assessment_id
									inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id and c.kpa_order 
									inner join d_kpa d on d.kpa_id = c.kpa_id
									inner join d_rating e on a.d_rating_rating_id = e.rating_id
									inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id and f.role = 4					
									inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id														
								order by c.`kpa_order` asc;
    			";
    	$this->db->query($sql);
    }   
    function getKPAParameterWiseAnalysis($kpa_id,$parameter){    	
    	$order = $parameter=='fee'?($parameter.'_id'):$parameter;
    	$sql = "select count(distinct client_id) num,numericRating,rating,$parameter as 'parameter' from KPAParameterWiseGraph where kpa_id=? and $parameter is not null and $parameter !=''  group by $parameter,numericRating order by $order";    	
    	$res = $this->db->get_results($sql,array($kpa_id));
    	return $res?$res:array();
    	
    }
    function createKqKPARating(){
    	$sql = "drop temporary table if exists KqKpaRating;
create temporary table KqKpaRating select i.kpa_id,j.key_question_id,rating,e.rating_id,ast.client_id,ast.client_name,h.order as numericRating from
				clientlist ast inner join h_kq_instance_score a on a.assessment_id = ast.assessment_id
				inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1
				inner join d_rating e on a.d_rating_rating_id = e.rating_id
				inner join h_assessment_user f on a.assessor_id = f.user_id and ast.assessment_id = f.assessment_id and f.role=4 
				inner join h_kpa_diagnostic i on i.diagnostic_id = ast.diagnostic_id and i.kpa_order <7 
				inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and i.kpa_instance_id=j.kpa_instance_id and j.key_question_instance_id=a.key_question_instance_id                                       
				inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id					 
				order by  j.`kq_order` asc;";
    	
    	return $this->db->query($sql);
    }
    function getKpaKqRating(){
    	/*$sql = "select kpa_id,group_concat(key_question_id) as KqIds,sum(if(rating_id=7 or rating_id=8,1,0)) as 'goodRatings',count(rating_id) as 'totalRatings',if(sum(if(rating_id=7 or rating_id=8,1,0))=count(rating_id),1,0) as isGoodRating from KqKpaRating 
    			group by kpa_id -- having isGoodRating=1";*/
    	//$sql = "select * from KqKpaRating group by kpa_id,key_question_id";
    	/*$sql = "select kpa_id,KqIds,if(sum(isgood)=count(kpa_id),1,0) as isGoodRating from (select kpa_id,group_concat(key_question_id) as KqIds,sum(if(rating_id=7 or rating_id=8,1,0)) as 'goodRatings',count(rating_id) as 'totalRatings',if(sum(if(rating_id=7 or rating_id=8,1,0))>=2,1,0) as isgood from 
(select i.kpa_id,j.key_question_id,rating,e.rating_id,ast.client_id,h.order as numericRating,a.assessor_id from
				clientlist ast inner join h_kq_instance_score a on a.assessment_id = ast.assessment_id
				inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1
				inner join h_assessment_user f on a.assessor_id = f.user_id and ast.assessment_id = f.assessment_id and f.role=4 
                inner join d_rating e on a.d_rating_rating_id = e.rating_id				
				 inner join h_kpa_diagnostic i on i.diagnostic_id = ast.diagnostic_id and i.kpa_order <7 
				inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and j.key_question_instance_id=a.key_question_instance_id                inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id					 
				order by  j.`kq_order` asc) KqKpaRating group by kpa_id,client_id) temp group by kpa_id having isGoodRating=1";*/
    	$sql = "select kpa_id,key_question_id,group_concat(rating_id) as ratings,count(client_id) as num from (select i.kpa_id,j.key_question_id,rating,e.rating_id,ast.client_id,h.order as numericRating,a.assessor_id,kq_order from
				clientlist ast inner join h_kq_instance_score a on a.assessment_id = ast.assessment_id
				inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1
				inner join h_assessment_user f on a.assessor_id = f.user_id and ast.assessment_id = f.assessment_id and f.role=4 
                inner join d_rating e on a.d_rating_rating_id = e.rating_id				
				 inner join h_kpa_diagnostic i on i.diagnostic_id = ast.diagnostic_id and i.kpa_order <7 
				inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and j.key_question_instance_id=a.key_question_instance_id                inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id					 
				order by  j.`kq_order` asc) ttab2 group by kpa_id,key_question_id order by ttab2.kq_order; ";
    	$res = $this->db->get_results($sql);    	
    	return $res?$res:array();
    }
	 function createKpa7Evidence(){
    	$sql="drop temporary table if exists Kpa7Evidence;
    		select @running:=0;
select @running1:=0;
select @previous:='';
select @previous1:='';	
create temporary table Kpa7Evidence 
    		
select 
        @running:=if(@previous=concat(kpa_id,key_question_id),@running,if(@running=3,1,@running+1)) as KQnum, concat('KQ',@running),
        @previous:=concat(kpa_id,key_question_id),
        @running1:=if(@previous1=concat(kpa_id,key_question_id,core_question_id),@running1,if(@running1=9,1,@running1+1)) as CQnum,
        @previous1:=concat(kpa_id,key_question_id,core_question_id),@running1,if(@running1=9,1,@running1+1),
        concat('SQ',@running1),
        a.*
        from
        (
select kpa.kpa_id,key_question_id,kpa.kpa_name,cq_order,d.core_question_id,core_question_text,rating,e.rating_id,
h.order as numericRating,evidence_text from
clientlist ast inner join h_cq_score a on a.assessment_id = ast.assessment_id
inner join h_cq_js_instance cjs on cjs.core_question_instance_id=a.core_question_instance_id
inner join f_score fs on fs.judgement_statement_instance_id=cjs.judgement_statement_instance_id
inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1				 
					 inner join h_kq_cq c on a.core_question_instance_id = c.core_question_instance_id
					 inner join d_core_question d on d.core_question_id = c.core_question_id
					 inner join d_rating e on a.d_rating_rating_id = e.rating_id
					 inner join h_assessment_user f on a.assessor_id = f.user_id and ast.assessment_id = f.assessment_id and f.role=3 and fs.assessor_id=f.user_id
                     inner join h_kpa_diagnostic i on i.diagnostic_id = ast.diagnostic_id and i.kpa_order =7
					 inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and c.key_question_instance_id = j.key_question_instance_id and i.kpa_instance_id=j.kpa_instance_id                    
                   inner join d_kpa kpa on kpa.kpa_id = i.kpa_id
					 inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id					 
					 	order by  i.kpa_order,j.kq_order,c.`cq_order` asc
                        ) a	;    						 	
	 ";	 
	 $this->db->query($sql);    
	 }
	 function getKPA7Evidence($cqNum){
		// $sql = "Select group_concat(evidence_text) from Kpa7Evidence group by client_name,cq_order where cq_order=?";
		$sql = "SET SESSION group_concat_max_len = 1073741824;";
		$this->db->query($sql);
		 $sql = "Select group_concat( distinct evidence_text SEPARATOR '<br/>') as text from Kpa7Evidence where CQnum=$cqNum group by core_question_id";
		//echo $sql;	
		$res = $this->db->get_row($sql,array($cqNum));
				
    	return $res?$res:array();
	 }
    function createKpa7Rating($role=3){
    	$sql="drop temporary table if exists Kpa7Rating;
create temporary table Kpa7Rating select distinct kpa.kpa_id,kpa.kpa_name,d.core_question_id,kq_order,cq_order,core_question_text,rating,e.rating_id,ast.client_id,ast.client_name,h.order as numericRating from
clientlist ast inner join h_cq_score a on a.assessment_id = ast.assessment_id
inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1				 
					 inner join h_kq_cq c on a.core_question_instance_id = c.core_question_instance_id
					 inner join d_core_question d on d.core_question_id = c.core_question_id
					 inner join d_rating e on a.d_rating_rating_id = e.rating_id
					 inner join h_assessment_user f on a.assessor_id = f.user_id and ast.assessment_id = f.assessment_id and f.role=$role
                     inner join h_kpa_diagnostic i on i.diagnostic_id = ast.diagnostic_id and i.kpa_order =7
					 inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and c.key_question_instance_id = j.key_question_instance_id and i.kpa_instance_id=j.kpa_instance_id                    
                   inner join d_kpa kpa on kpa.kpa_id = i.kpa_id
					 inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id					 
					 	order by  kq_order,c.`cq_order` asc;    	
					 	Select count(distinct client_id) into @kpa7clients  from Kpa7Rating;
    ";
    	/*$sql="drop table if exists Kpa7Rating;
create table Kpa7Rating select kpa.kpa_id,kpa.kpa_name,cq_order,core_question_text,rating,e.rating_id,ast.client_id,h.order as numericRating from
d_assessment ast inner join h_cq_score a on a.assessment_id = ast.assessment_id and ast.assessment_id in (29,9)
inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1				 
					 inner join h_kq_cq c on a.core_question_instance_id = c.core_question_instance_id
					 inner join d_core_question d on d.core_question_id = c.core_question_id
					 inner join d_rating e on a.d_rating_rating_id = e.rating_id
					 inner join h_assessment_user f on a.assessor_id = f.user_id and ast.assessment_id = f.assessment_id and f.role=3
                     inner join h_kpa_diagnostic i on i.diagnostic_id = ast.diagnostic_id and i.kpa_order =7
					 inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and c.key_question_instance_id = j.key_question_instance_id and i.kpa_instance_id=j.kpa_instance_id                    
                   inner join d_kpa kpa on kpa.kpa_id = i.kpa_id
					 inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id					 
					 	order by  c.`cq_order` asc;	
					 	Select count(distinct client_id) into @kpa7clients  from Kpa7Rating;
    ";*/
    	
    	$this->db->query($sql);
    }
function getKpa7rating()
    {
    	/* $sql = "select kpa_id,kpa_name,cq_order,core_question_id,core_question_text,group_concat(rating_id order by cq_order) as ratingIds,@kpa7clients as total from Kpa7Rating 
 group by kpa_name,core_question_text
 order by cq_order"; */
    	$sql = "drop temporary table if exists tempkpa7rating;
    			create temporary table tempkpa7rating as select distinct kpa_id,kpa_name,kq_order,cq_order,core_question_id,core_question_text,group_concat(rating_id order by cq_order) as ratingIds,@kpa7clients as total from Kpa7Rating 
 group by kpa_name,core_question_text
 order by kq_order,cq_order;
    			select @a_num:=0;";
    	$res = $this->db->get_results($sql);
    	$sql = "select a.*,(select @a_num:=@a_num+1) cq_order from tempkpa7rating a;";
    	$res = $this->db->get_results($sql);
    	return $res?$res:array();
    }
    function getKpa7clientCQrating(){
    	 $sql = "select group_concat(rating_id order by kq_order,cq_order asc) as ratingIds,client_id,client_name from Kpa7Rating 
 group by client_id -- order by kq_order,cq_order
 ; ";     	
    	/* $sql = " drop temporary table if exists tKpa7Rating;
    			create temporary table tKpa7Rating as select distinct group_concat(rating_id order by cq_order) as ratingIds,core_question_id,client_id,client_name from Kpa7Rating 
 group by client_id order by kq_order,cq_order
 ;
    			select @b_num:=0;";
    	$res = $this->db->get_results($sql);
    	$sql = "select a.*,(select @b_num:=@b_num+1) cq_order from tKpa7Rating a;"; */
    	$res = $this->db->get_results($sql);
    	return $res?$res:array();
    }
    function getKpaCQrating($kpa)
    {
    	/* $sql = "select kpa_id,kpa_name,cq_order,core_question_text,group_concat(rating_id order by cq_order) as ratingIds,@total as total from kpaCQexternalgraph where kpa_id=?
 group by kpa_name,core_question_text
 order by cq_order"; */
    	
    	$sql = " drop temporary table if exists tempkpacqdata;
    			create temporary table tempkpacqdata as select kpa_id,kpa_name,core_question_text,core_question_id,group_concat(rating_id order by cq_order) as ratingIds,@total as total from kpaCQexternalgraph where kpa_id=?
 group by kpa_name,core_question_text
 order by kq_order,cq_order asc;
    			select @cq_num:=0;
    			";
    	$this->db->query($sql,array($kpa));
    	$sql = "select a.*,(select @cq_num:=@cq_num+1) cq_order from tempkpacqdata a ";
    	$res = $this->db->get_results($sql);
    	return $res?$res:array();
    }
    function getCQstatements()
    {
    	$sql = "SELECT * FROM d_core_question_stmt;";
    	$res = $this->db->get_results($sql);
    	return $res?$res:array();
    }
    function getKQstatement($key_question_id)
    {
    	$sql = "SELECT * FROM d_key_question_stmt where key_question_id = ?";
    	$res = $this->db->get_row($sql,array($key_question_id));
    	return $res?$res:array();
    }
    function getClientCount(){
    	$sql = "SELECT count(distinct client_id) as num from clientlist";
    	$data = $this->db->get_row($sql);
    	return $data;
    }
    function createJDtable(){
    	$SQL="drop temporary table if exists clientlist2;
			create temporary table clientlist2
			select c.client_id,c.client_name,language_name,board,brd.board_id,aqs.annual_fee,fee.fee_id,school_type,region_name,c.city,c.city_id,st.state_name,st.state_id,a.assessment_id,a.diagnostic_id,a.award_scheme_id,a.internal_award,a.external_award,a.d_sub_assessment_type_id
			 from d_client c inner join h_client_network n on n.client_id=c.client_id			
			inner join d_assessment a on a.client_id = c.client_id
    		inner join aqsclients aq on aq.client_id = a.client_id and a.assessment_id = aq.assessment_id
			inner join d_AQS_data aqs on aqs.id=a.aqsdata_id
    		inner join d_board brd on brd.board_id = aqs.board_id	
            left join d_school_type dst on dst.school_type_id=aqs.school_type_id
            left join d_language med on med.language_id=aqs.medium_instruction
            left join d_school_region sr on sr.region_id = aqs.school_region_id	
    		left join d_states st on c.state_id = st.state_id	
    		left join d_fees fee on fee.fee_text=aqs.annual_fee	
    		where c.client_id not in (115,154,175)	
			group by c.client_id
			order by str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc
    		;";
    	$this->db->query($SQL); 
    	$sql = "drop temporary table if exists KpaJDtable;	
    		create temporary table KpaJDtable
select i.client_id,i.kpa_id,i.client_name,e.kpa_order,e.kq_order,e.cq_order,e.`js_order`,e.numericRating as externalRating, 
 abs(i.numericRating-e.numericRating) as JD 
 from (select distinct h.kpa_id,kpa_order,kq_order,cq_order,c.`js_order`,ga.client_id,ga.client_name,c.core_question_instance_id,d.judgement_statement_id,judgement_statement_text,role,a.rating_id as numericRating
					 from f_score a
					 inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id and b.role=3
					 inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
					 inner join d_judgement_statement d on d.judgement_statement_id = c.judgement_statement_id					 
					 inner join clientlist2 ga on a.assessment_id = ga.assessment_id
					 inner join h_kpa_diagnostic h on h.diagnostic_id = ga.diagnostic_id and h.kpa_order<7
					 inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
					 inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id					 
                     
					 where a.isFinal = 1                      
					 order by kpa_order,kq_order,cq_order,c.`js_order` asc) i inner join (select distinct h.kpa_id,kpa_order,kq_order,cq_order,c.`js_order`,g.client_id,g.client_name,c.core_question_instance_id,d.judgement_statement_id,judgement_statement_text,role,a.rating_id as numericRating
					 from f_score a
					 inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id and b.role=4
					 inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
					 inner join d_judgement_statement d on d.judgement_statement_id = c.judgement_statement_id					 
					 inner join clientlist g on a.assessment_id = g.assessment_id
					 inner join h_kpa_diagnostic h on h.diagnostic_id = g.diagnostic_id and h.kpa_order<7
					 inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
					 inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id					 
					 where a.isFinal = 1                      
					 order by kpa_order,kq_order,cq_order,c.`js_order` asc) e 
on i.client_id = e.client_id and e.judgement_statement_id=i.judgement_statement_id
    			order by e.kpa_order,e.kq_order,e.cq_order,e.`js_order`";
    	$this->db->query($sql);
    }
    function getKpaKqRatingAndJd($kpa_id,$offset,$count){
    	$sql = "select client_name,group_concat(JD  order by kpa_order,kq_order,cq_order,js_order) jd,group_concat(externalRating order by kpa_order,kq_order,cq_order,js_order) rating from (SELECT client_name,JD,externalRating,kpa_order,kq_order,cq_order,js_order from KpaJDtable where kpa_id=?  order by kpa_order,kq_order,cq_order,js_order limit $offset,$count) a group by client_name";
    	$res = $this->db->get_results($sql,array($kpa_id));
    	return $res? $res :array();
    }
    function getKpaJd($kpa_id){
    	$sql = "select kpa_id,client_name,group_concat(JD) as JD from KpaJDtable where kpa_id =? group by client_name;";
    	$res = $this->db->get_results($sql,array($kpa_id));
    	return $res? $res :array();
    }
    function getClientsJd(){
    	$sql = "select client_name,client_id,group_concat(JD) as JD from KpaJDtable group by client_name;";
    	$res = $this->db->get_results($sql);
    	return $res? $res :array();
    }
    function createAnnex2Data(){//always mostly count kpawise
    	$sql = "drop temporary table if exists annex2data ;
    			create temporary table annex2data as
select assessment_id,kpa_id,client_id,kpa_order,sum(Always)Always,sum(Mostly)Mostly,sum(Sometimes)Sometimes,sum(Rarely)Rarely
from (
select assessment_id,kpa_id,client_id,kpa_order,if(numericRating=4,count,0)Always,if(numericRating=3,count,0)Mostly,if(numericRating=2,count,0)Sometimes,if(numericRating=1,count,0)Rarely from
(
                select assessment_id,kpa_id,kpa_order,client_id,numericRating,count from
(
select distinct ga.assessment_id,ga.client_name,h.kpa_id,h.kpa_order,ga.client_id,c.core_question_instance_id,d.judgement_statement_id,judgement_statement_text,role,a.rating_id as numericRating,count(rating_id) as count
					 from f_score a
					 inner join h_assessment_user b on a.assessor_id = b.user_id and a.assessment_id = b.assessment_id and b.role=4
					 inner join h_cq_js_instance c on a.judgement_statement_instance_id = c.judgement_statement_instance_id
					 inner join d_judgement_statement d on d.judgement_statement_id = c.judgement_statement_id					 
					 inner join clientlist ga on a.assessment_id = ga.assessment_id
					 inner join h_kpa_diagnostic h on h.diagnostic_id = ga.diagnostic_id 
					 inner join h_kpa_kq i on h.kpa_instance_id = i.kpa_instance_id
					 inner join h_kq_cq j on i.key_question_instance_id = j.key_question_instance_id and j.core_question_instance_id = c.core_question_instance_id					                      
					 where a.isFinal = 1 
                     group by ga.client_id,ga.assessment_id,kpa_id,a.rating_id
					 order by ga.client_name,
                     h.kpa_order asc,c.`js_order` asc
) a                    
group by client_id,assessment_id,kpa_id,numericRating
order by client_id,assessment_id,kpa_order
)
b
group by client_id,assessment_id,kpa_id,numericRating
order by client_id,assessment_id,kpa_order
) c
group by client_id,assessment_id,kpa_id
order by client_id,assessment_id,kpa_order
;";
    	$this->db->query($sql);
    }
    function getAnnex2TblByClientId($client_id){
    	$sql = "Select kpa_id,kpa_order,client_id,Mostly,Always from annex2data where client_id=? order by kpa_order";
    	$res= $this->db->get_results($sql,array($client_id));
    	return $res?$res:array();
    }
   /* function genRatingDiffKPA(){
    	$sql = "drop table if exists kpaCQinternalgraph;
create table kpaCQinternalgraph select kpa.kpa_id,kpa.kpa_name,cq_order,core_question_text,rating,e.rating_id,ast.client_id,ast.client_name,h.order as numericRating from
clientlist ast inner join h_cq_score a on a.assessment_id = ast.assessment_id
inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1
					 inner join h_kq_cq c on a.core_question_instance_id = c.core_question_instance_id
					 inner join d_core_question d on d.core_question_id = c.core_question_id
					 inner join d_rating e on a.d_rating_rating_id = e.rating_id
					 inner join h_assessment_user f on a.assessor_id = f.user_id and ast.assessment_id = f.assessment_id and f.role=3
                     inner join h_kpa_diagnostic i on i.diagnostic_id = ast.diagnostic_id and i.kpa_order <7
					 inner join h_kpa_kq j on i.kpa_instance_id = j.kpa_instance_id and c.key_question_instance_id = j.key_question_instance_id and i.kpa_instance_id=j.kpa_instance_id
                   inner join d_kpa kpa on kpa.kpa_id = i.kpa_id
					 inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id
					 	order by  c.`cq_order` asc";
    	$this->db->query($sql);    	
    }
    function getRatingDiffKPA($kpa_id){    	
    	$sql = "select i.kpa_id,i.client_id,i.client_name,i.cq_order,abs(i.numericRating-e.numericRating) as JD from kpaCQinternalgraph i inner join kpaCQexternalgraph e 
    			on i.kpa_id=e.kpa_id and i.client_id=e.client_id and i.cq_order = e.cq_order where i.kpa_id=? order by i.cq_order";
    	$res = $this->db->get_results($sql,$kpa_id);
    	return $res?$res:array();
    }*/
    // function for getting count of school awards
   /* function getSchoolAwardsCount($network_id){      
        $SQL1="select external_award_text, count(*) num from networkawards group by external_award_text;";
        $data = $this->db->get_results($SQL1);
        $array1 = array();
        foreach ($data as $key => $value) {
            if($value['external_award_text']=='National Gold' || $value['external_award_text']=='National Silver' || 
                    $value['external_award_text']=='National Platinum' || $value['external_award_text']=='National Bronze')
            {
                $array1['National'][str_replace('National ','',$value['external_award_text'])] = $value['num'];
            } else if($value['external_award_text']=='International Gold' || $value['external_award_text']=='International Silver' || 
                    $value['external_award_text']=='International Platinum' || $value['external_award_text']=='International Bronze')
            {
                $array1['International'][str_replace('International ','',$value['external_award_text'])] = $value['num'];
            } else if($value['external_award_text']=='State Gold' || $value['external_award_text']=='State Silver' || 
                    $value['external_award_text']=='State Platinum' || $value['external_award_text']=='State Bronze')
            {
                $array1['State'][str_replace('State ','',$value['external_award_text'])] = $value['num'];
            }
        }
        $array = $array1;
        return $array;
    } */
    function getSchoolAwardsCount(){
    	$SQL1="select external_award_text, count(*) num from networkawards group by external_award_text;";
    	$data = $this->db->get_results($SQL1);
    	$array1 = array();
    	foreach ($data as $key => $value) {
    		if($value['external_award_text']=='National Gold' || $value['external_award_text']=='National Silver' ||
    				$value['external_award_text']=='National Platinum' || $value['external_award_text']=='National Bronze')
    		{
    			$array1['National'][str_replace('National ','',$value['external_award_text'])] = $value['num'];
    		} else if($value['external_award_text']=='International Gold' || $value['external_award_text']=='International Silver' ||
    				$value['external_award_text']=='International Platinum' || $value['external_award_text']=='International Bronze')
    		{
    			$array1['International'][str_replace('International ','',$value['external_award_text'])] = $value['num'];
    		} else if($value['external_award_text']=='State Gold' || $value['external_award_text']=='State Silver' ||
    				$value['external_award_text']=='State Platinum' || $value['external_award_text']=='State Bronze')
    		{
    			$array1['State'][str_replace('State ','',$value['external_award_text'])] = $value['num'];
    		}
    		else
    		{
    			$array1['Grade'][$value['external_award_text']] = $value['num'];
    		}
    	}
    	$array = $array1;
    	return $array;
    }
    function getKPACQtbl($data,$kpanum,$num_schools,$iskpa7=0,$isValid=0){
    	$CQstmtNumStart = 0;
    	$CQstmtNumStart = ($kpanum-1)*9;
    	$overallArray = array ();
    	$kpa1Tbl ="";
    	$subQArray = array (
    			1,
    			2,
    			3,
    			4,
    			5,
    			6,
    			7,
    			8,
    			9
    	);
    	$OutstandingArray = array ();
    	$GoodArray = array ();
    	$VariableArray = array ();
    	$NeedsAttArray = array ();
    	$cqNo = 1;
    	$kpaCQarr = $data;
    	$resultTextHigh = "";
    	$resultTextMid="";
    	$resultTextLow="";    	
    	foreach ( $kpaCQarr as $kpaCq ) {    				
				 $cqId = $kpaCq ['core_question_id'];
				 $allRatings = $kpaCq ['ratingIds'];
				 $NeedsAttArray [$cqNo] = round((substr_count ( $allRatings, 5 ) * 100) / $kpaCq ['total'],1);
				 $VariableArray [$cqNo] = round((substr_count ( $allRatings, 6 ) * 100) / $kpaCq ['total'],1);
				 $GoodArray [$cqNo] = round((substr_count ( $allRatings, 7 ) * 100) / $kpaCq ['total'],1);
				 $OutstandingArray [$cqNo] = round((substr_count ( $allRatings, 8 ) * 100) / $kpaCq ['total'],1);
				 //$overallArray [$cqNo] = $NeedsAttArray [$cqNo] >= 56 ? 5 : ($VariableArray [$cqNo] >= 56 ? 6 : ($GoodArray [$cqNo] >= 56 ? 7 : ( $OutstandingArray [$cqNo] >= 56 ? 8 : $this->findMaxRating($OutstandingArray [$cqNo],$GoodArray [$cqNo],$VariableArray [$cqNo],$NeedsAttArray [$cqNo]))));
				 $overallArray [$cqNo] = round($this->getWeightage($NeedsAttArray [$cqNo],5)+$this->getWeightage($VariableArray [$cqNo],6)+$this->getWeightage($GoodArray [$cqNo],7)+$this->getWeightage($OutstandingArray [$cqNo],8),1);
				/*  if($overallArray [$cqNo]>2.5)
				 	$resultTextHigh = empty($resultTextHigh)?'<p>'.'<img width="10" height="10" src="' . SITEURL .'public/images/green.jpg" />'.'&nbsp;<b>Most schools in the network are good in</b>:</p>'.'<p>'.$cqNo.'. '.$CQstmts[$cqId]['statement'].'</p>':($resultTextHigh.'<p>'.$cqNo.'. '.$CQstmts[$cqId]['statement'].'</p>');
				 	elseif($overallArray [$cqNo]>=1.5 && $overallArray [$cqNo]<=2.5 )
				 	$resultTextMid = empty($resultTextMid)?'<p>'.'<img width="10" height="10" src="' . SITEURL .'public/images/amber.jpg" />'.'&nbsp;<b>Most schools in the network are variable in</b>:</p>'.'<p>'.$cqNo.'. '.$CQstmts[$cqId]['statement'].'</p>':($resultTextMid.'<p>'.$cqNo.'. '.$CQstmts[$cqId]['statement'].'</p>');
				 	elseif($overallArray [$cqNo]<1.5)
				 	$resultTextLow = empty($resultTextLow)?'<p>'.'<img width="10" height="10" src="' . SITEURL .'public/images/red.jpg" />'.'&nbsp;<b>Most schools in the network need attention in</b>:</p>'.'<p>'.$cqNo.'. '.$CQstmts[$cqId]['statement'].'</p>':($resultTextLow.'<p>'.$cqNo.'. '.$CQstmts[$cqId]['statement'].'</p>');
				 */
				 	$cqNo ++;
				 					 
    	}       
    
    	$rowHeading = array (
    			'Key questions',
    			'Sub-questions',
    			'Outstanding',
    			'Good',
    			'Variable',
    			'Needs Attention'
    	);
    	$currRating='';
    	// create rows
    	for($i = 0; $i < 6; $i ++) {
    
    		switch($i){
    			case 2 : $currRating = $OutstandingArray;
    			$rowColor = '#307ACE;';
    			break;
    			case 3 : $currRating = $GoodArray;
    			$rowColor = '#5e9900;';
    			break;
    			case 4 : $currRating = $VariableArray;
    			$rowColor = '#D0B122;';
    			break;
    			case 5 : $currRating = $NeedsAttArray;
    			$rowColor = '#D12200;;';
    			break;
    		}
    		if($i==0)
    			$kpa1Tbl .= '<thead><tr><td colspan="2">' . $rowHeading [$i] . '</td><td colspan="3" style="'.$this->getColorForWeightage($this->calculateKQRating($overallArray [1],$overallArray [2],$overallArray [3])).'">1</td><td colspan="3" style="'.$this->getColorForWeightage($this->calculateKQRating($overallArray [4],$overallArray [5],$overallArray [6])).'">2</td><td colspan="3" style="'.$this->getColorForWeightage($this->calculateKQRating($overallArray [7],$overallArray [8],$overallArray [9])).'">3</td></tr>';
    		else if ($i == 1)
    			$kpa1Tbl .= '<tr>
			<td colspan="2" style="background-color:#CCCCCC;">' . $rowHeading [$i] . '</td>
			<td style="' . $this->getColorForWeightage($overallArray [1] ) . '">1</td>
			<td style="' . $this->getColorForWeightage($overallArray [2] ) .  '">2</td>
			<td style="'. $this->getColorForWeightage($overallArray [3] ) . '">3</td>
			<td style="'. $this->getColorForWeightage($overallArray [4] ) .  '">4</td>
			<td style="'. $this->getColorForWeightage($overallArray [5] ) .  '">5</td>
			<td style="'. $this->getColorForWeightage($overallArray [6] ) .  '">6</td>
			<td style="'. $this->getColorForWeightage($overallArray [7] ) .  '">7</td>
			<td style="'. $this->getColorForWeightage($overallArray [8] ) .  '">8</td>		
			<td style="'. $this->getColorForWeightage($overallArray [9] ) .  '">9</td>
			</tr>';    			
    			else
    				$kpa1Tbl .= '<tr>
			<td colspan="2"  style="background-color:'.$rowColor.'">' . $rowHeading [$i] . '</td>
			<td>' . $currRating [1] . '%</td>
			<td>' . $currRating [2] . '%</td>
			<td>' . $currRating [3] . '%</td>
			<td>' . $currRating [4] . '%</td>
			<td>' . $currRating [5] . '%</td>
			<td>' . $currRating [6] . '%</td>
			<td>' . $currRating [7] . '%</td>
			<td>' . $currRating [8] . '%</td>
			<td>' . $currRating [9] . '%</td>
			</tr>';
    					
    	}
    	$globalStandard = "To take the next best step towards a global standard, the network will need to:<ul>
    			<li> Learn from and implement best practice across the schools and internationally and adapt and apply such systems across the network. Adapt and apply such systems to the schools as Province led policies and practices</li>
    			</ul>";
    	/* return '<table cellspacing="0" cellpadding="1" border="1" align="center">' . $kpa1Tbl . '</table>'.($iskpa7==1 && $isValid ==0 ?'<p style="height:10px;font-size:10px;text-align:right;"><b>*SCORES NOT VALIDATED</b></p>':'').
    			$resultTextHigh.$resultTextMid.$resultTextLow ; */
    	return '<table cellspacing="0" cellpadding="1" border="1" align="center">' . $kpa1Tbl . '</table>'.($iskpa7==1 && $isValid ==0 ?'<p style="height:10px;font-size:10px;text-align:right;"><b>*SCORES NOT VALIDATED</b></p>':'');
    }
    function calculateKQRating($sq1rating,$sq2rating,$sq3rating){
    	//dominant ratings will be used
     	$array = array($sq1rating,$sq2rating,$sq3rating);
     	$leastWeightage =0;
     	$midWeightage =0;
     	$highWeightage =0;
    
    	array_walk($array,function($val) use (&$leastWeightage,&$midWeightage,&$highWeightage){      		
    		if($val<1.5)
    			$leastWeightage++;
    		elseif($val>=1.5 && $val<=2.5)
    			$midWeightage++;
    		elseif($val>2.5)
    			$highWeightage++;    		
    	});   
    		if($leastWeightage>=2)
    			$KQrating = '1';
    		elseif($midWeightage>=2)
    			$KQrating = '2';
    		elseif($highWeightage>=2)
    			$KQrating = '3';
    		else
    			$KQrating = '2';
    	unset($leastWeightage);
    	unset($midWeightage);
    	unset($highWeightage);
    	return $KQrating;
    }
    function getColorForWeightage($weightage){
    	$color = '';
    	switch($weightage){
    		case $weightage < 1.5 : $color = '#d12200;';
    		break;
    		case $weightage >= 1.5 && $weightage <= 2.5 : $color = '#D0B122;';
    		break;
    		case $weightage > 2.5 : $color = '#5e9900;';
    		break;    		
    	}
    	return 'background-color:'.$color;
    }
    function getKPAparameterGraph($kpa_param){    	
    	$x = 0;
    	$prev_param_id="";
    	$uriString="";
    	foreach($kpa_param as $par){
    		if ($x == 0 || $prev_param_id != $par ['parameter']) {
    			$x > 0 ? ($uriString .= "&") : '';
    			$uriString .= "parameter$x=name_" . urlencode ( $par ['parameter'] ) . ";" .str_replace(" ","",$par ['rating']). "_" . $par ['num'];
    			$x ++;
    		} else {
    			$uriString .= ";" .str_replace(" ","",$par ['rating']). "_" . $par['num'];
    		}
    		$prev_param_id = $par ['parameter'];
    	}  
    	return $uriString;
    }
    function findMaxRating($outs,$good,$var,$needsat){
    	$ratingArr = array(8=>$outs,7=>$good,6=>$var,5=>$needsat);
    	$max = max($ratingArr);
    	$keys = array_keys($ratingArr,$max);
    	return $keys[count($keys)-1];
    }
    
    function getIcon($rating){
    	if($rating ==5)
    		return '!';
    	if($rating ==6)
    		return '?';
    	if($rating ==7)
    		return '<img width="15" height="15" src="' . SITEURL .'public/images/tick.png" />';
    	if($rating ==8)
    		return '<img width="15" height="15" src="' . SITEURL .'public/images/tick.png" />';
    }
   /* function createKpaJDtbl($tableNum,$data,$kpa_name){  
    	$i=1;    	
    	$thead = '<table cellspacing="0" cellpadding="1" border="0" align="center" nobr="true">
    			<tr style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10;border:solid 1px #ffffff;"><td style="border:solid 1px #ffffff;" colspan="5">Table '.$tableNum.'</td></tr>
    			<tr style="background-color:#fbc140;border:solid 1px #000000;font-weight:bold;"><td style="border:solid 1px #000000;">'.$kpa_name.'</td><td style="border:solid 1px #000000;" >Agreements</td><td style="border:solid 1px #000000;">Disagreements by 1</td><td style="border:solid 1px #000000;">Disagreements by 2</td><td style="border:solid 1px #000000;">Disagreements by 3</td></tr>';
    	$tbody = $thead;
    	foreach ($data as $client)
    	{
    		if($i%22==0)
    		{
    			$tbody.='</table>'.'<table cellspacing="0" cellpadding="1" border="0" align="center" nobr="true">
    			<tr style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10;border:solid 1px #ffffff;"><td style="border:solid 1px #ffffff;" colspan="5">Table '.$tableNum.' (continued)</td></tr>
    			<tr style="background-color:#fbc140;border:solid 1px #000000;font-weight:bold;"><td style="border:solid 1px #000000;">'.$kpa_name.'</td><td style="border:solid 1px #000000;" >Agreements</td><td style="border:solid 1px #000000;">Disagreements by 1</td><td style="border:solid 1px #000000;">Disagreements by 2</td><td style="border:solid 1px #000000;">Disagreements by 3</td></tr>';
    		}
    			
    		$allJD = $client ['JD'];
    		$agreements = round(substr_count( $allJD, 0 )*100/27,1).'%' ;
    		$disagree1 = round(substr_count( $allJD, 1 )*100/27,1).'%' ;
    		$disagree2 = round(substr_count( $allJD, 2 )*100/27,1).'%' ;
    		$disagree3 = round(substr_count( $allJD, 3 )*100/27,1).'%' ;
    		$tbody .='<tr style="border:solid 1px #000000;">
    				<td style="background-color:#fbc140;border:solid 1px #000000;">'.$client['client_name'].'</td>
    				<td style="border:solid 1px #000000;">'.$agreements.'</td>
    				<td style="border:solid 1px #000000;">'.$disagree1.'</td>
    				<td style="border:solid 1px #000000;">'.$disagree2.'</td>
    				<td style="border:solid 1px #000000;">'.$disagree3.'</td>		
    				</tr>';
    		$i++;
    	}
    	$table = $tbody.'</table>';
    	return $table;
    }*/
    function createKpaJDtbl($tableNum,$data,$kpa_name){   	
    	$thead = '<table cellspacing="0" cellpadding="1" border="0" align="center"'.($kpa_name=='LeaderShip & Management'?"nobr=true":"").'>
    			<thead>
    			<tr style="text-align:center;font-size:12;font-weight:normal;color:#6c0d10;border:solid 1px #ffffff;"><td style="border:solid 1px #ffffff;" colspan="5">Table '.$tableNum.'</td></tr>
    			<tr style="background-color:#fbc140;border:solid 1px #000000;font-weight:bold;"><th style="border:solid 1px #000000;">'.$kpa_name.'</th><th style="border:solid 1px #000000;" >Agreements</th><th style="border:solid 1px #000000;">Disagreements by 1</th><th style="border:solid 1px #000000;">Disagreements by 2</th><th style="border:solid 1px #000000;">Disagreements by 3</th>
    			</tr></thead>';
    	$tbody = $thead;
    	foreach ($data as $client)
    	{    		    		 
    		$allJD = $client ['JD'];
    		$agreements = round(substr_count( $allJD, 0 )*100/27,1).'%' ;
    		$disagree1 = round(substr_count( $allJD, 1 )*100/27,1).'%' ;
    		$disagree2 = round(substr_count( $allJD, 2 )*100/27,1).'%' ;
    		$disagree3 = round(substr_count( $allJD, 3 )*100/27,1).'%' ;
    		$tbody .='<tr style="border:solid 1px #000000;">
    				<td style="background-color:#fbc140;border:solid 1px #000000;">'.$client['client_name'].'</td>
    				<td style="border:solid 1px #000000;">'.$agreements.'</td>
    				<td style="border:solid 1px #000000;">'.$disagree1.'</td>
    				<td style="border:solid 1px #000000;">'.$disagree2.'</td>
    				<td style="border:solid 1px #000000;">'.$disagree3.'</td>
    				</tr>';    		
    	}
    	$table = $tbody.'</table>';
    	return $table;
    }
	function getWeightage($perc,$rating){
		$weightAge = 0;
		switch($rating)
		{
			case 5: $weightAge = 1 * $perc; //needs attention
			break;
			case 6: $weightAge = 2 * $perc; // variable
			break;
			case 7: $weightAge = 3 * $perc;//good
			break;
			case 8: $weightAge = 4 * $perc;//o/s
			break;
		}
		return $weightAge/100;
	}
	function getClientLatestReviewData($client_id,$assessment_type_id=1){
		/*$sql = "select a.assessment_id,a.client_id,a.diagnostic_id,concat(standard_name,' ',aw.award_name) as userAward,h.order,standard_id,
				aqs.id,aqs.school_name,aqs.board_id,aqs.school_type_id,aqs.no_of_students,aqs.student_type_id,aqs.annual_fee,aqs.school_aqs_pref_start_date,aqs.school_aqs_pref_end_date from d_assessment a 
				inner join d_client cl on a.client_id=cl.client_id
				inner join d_diagnostic d on a.diagnostic_id = d.diagnostic_id and d.assessment_type_id=?
				inner join d_AQS_data aqs on a.aqsdata_id = aqs.id				
				inner join h_award_scheme h on a.award_scheme_id=h.award_scheme_id and h.order=a.external_award 
				inner join d_award aw on aw.award_id = h.award_id 
                inner join d_fees f on f.fee_text=aqs.annual_fee
                inner join d_school_strength str on str.strength_text=aqs.no_of_students
				left join d_tier t on  h.tier_id=t.standard_id
				 where a.client_id=? order by str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc limit 1";	*/
		$sql="select a.assessment_id,a.client_id,a.diagnostic_id,
			(select concat(standard_name, ' ',award_name) from h_award_scheme h inner join d_award using(award_id) left join d_tier t on  h.tier_id=t.standard_id where h.order=a.external_award and award_scheme_id=1 ) scheme1Award,
			(select award_name from h_award_scheme h inner join d_award using(award_id) left join d_tier t on  h.tier_id=t.standard_id where h.order=a.external_award and award_scheme_id=2 ) scheme2Award,
							aqs.id,aqs.school_name,aqs.board_id,aqs.school_type_id,aqs.no_of_students,aqs.student_type_id,aqs.annual_fee,aqs.school_aqs_pref_start_date,aqs.school_aqs_pref_end_date from d_assessment a 
							inner join d_client cl on a.client_id=cl.client_id
							inner join d_diagnostic d on a.diagnostic_id = d.diagnostic_id and d.assessment_type_id=?
							inner join d_AQS_data aqs on a.aqsdata_id = aqs.id							
			                left join d_fees f on f.fee_text=aqs.annual_fee
			                left join d_school_strength str on str.strength_text=aqs.no_of_students				
							 where a.client_id=? and a.external_award is not null order by str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc limit 1";
		$res = $this->db->get_row($sql,array($assessment_type_id,$client_id));
		return $res?$res:array();
	}
	 function generateComparisonData($country_id){
		$sql = "select @row_number:=0;
select @row_number1:=0;
drop table if exists temp_reviewData;
-- truncate table temp_reviewData;
create table temp_reviewData as
select t2.* from (select (@row_number1:=@row_number1+1) as rowId,cl.client_id,cl.province,a.assessment_id,a.external_award,a.diagnostic_id,a.award_scheme_id,a.tier_id,a.tier_id as standard_id,cl.country_id,cl.state_id,cl.city_id,aqs.board_id,aqs.school_type_id,aqs.no_of_students,aqs.student_type_id,aqs.annual_fee,aqs.school_aqs_pref_start_date,aqs.school_aqs_pref_end_date,cn.network_id from d_assessment a
				inner join d_client cl on a.client_id=cl.client_id
				inner join d_diagnostic d on a.diagnostic_id = d.diagnostic_id and d.assessment_type_id=1
				inner join d_AQS_data aqs on a.aqsdata_id = aqs.id
                inner join d_fees f on f.fee_text=aqs.annual_fee
                inner join d_school_strength str on str.strength_text=aqs.no_of_students
                 left join h_client_network cn on cn.client_id=cl.client_id
				 where a.external_award is not null order by str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc,rowId ) t2 inner join
(select max(t1.rowId) rowId,t1.client_id from (
select (@row_number:=@row_number+1) as rowId,cl.client_id,cl.province,a.assessment_id,a.external_award,a.diagnostic_id,a.award_scheme_id,a.tier_id,cl.country_id,cl.state_id,cl.city_id,aqs.board_id,aqs.school_type_id,aqs.no_of_students,aqs.student_type_id,aqs.annual_fee,aqs.school_aqs_pref_start_date,aqs.school_aqs_pref_end_date,cn.network_id from d_assessment a
				inner join d_client cl on a.client_id=cl.client_id
				inner join d_diagnostic d on a.diagnostic_id = d.diagnostic_id and d.assessment_type_id=1
				inner join d_AQS_data aqs on a.aqsdata_id = aqs.id
                inner join d_fees f on f.fee_text=aqs.annual_fee
                inner join d_school_strength str on str.strength_text=aqs.no_of_students
                left join h_client_network cn on cn.client_id=cl.client_id
				 where a.external_award is not null order by str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc,rowId ) as t1
                group by t1.client_id ) t3 on t2.rowId=t3.rowId;";
		$this->db->query($sql,array($country_id,$country_id));
	} 
	/*function generateComparisonData(){
		$sql = "select t2.* from (select (@row_number1:=@row_number1+1) as rowId,cl.client_id,a.assessment_id,a.external_award,a.diagnostic_id,a.award_scheme_id,a.tier_id,cl.country_id,cl.state_id,cl.city_id,aqs.board_id,aqs.school_type_id,aqs.no_of_students,aqs.student_type_id,aqs.annual_fee,aqs.school_aqs_pref_start_date,aqs.school_aqs_pref_end_date from d_assessment a
				inner join d_client cl on a.client_id=cl.client_id
				inner join d_diagnostic d on a.diagnostic_id = d.diagnostic_id and d.assessment_type_id=1
				inner join d_AQS_data aqs on a.aqsdata_id = aqs.id
                inner join d_fees f on f.fee_text=aqs.annual_fee
                inner join d_school_strength str on str.strength_text=aqs.no_of_students
				 where a.external_award is not null order by str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc,rowId ) t2 inner join
(select min(t1.rowId) rowId,t1.client_id from (
select (@row_number:=@row_number+1) as rowId,cl.client_id,a.assessment_id,a.external_award,a.diagnostic_id,a.award_scheme_id,a.tier_id,cl.country_id,cl.state_id,cl.city_id,aqs.board_id,aqs.school_type_id,aqs.no_of_students,aqs.student_type_id,aqs.annual_fee,aqs.school_aqs_pref_start_date,aqs.school_aqs_pref_end_date from d_assessment a
				inner join d_client cl on a.client_id=cl.client_id
				inner join d_diagnostic d on a.diagnostic_id = d.diagnostic_id and d.assessment_type_id=1
				inner join d_AQS_data aqs on a.aqsdata_id = aqs.id
                inner join d_fees f on f.fee_text=aqs.annual_fee
                inner join d_school_strength str on str.strength_text=aqs.no_of_students
				 where a.external_award is not null order by str_to_date(school_aqs_pref_start_date,'%m/%d/%Y') desc,rowId ) as t1
                group by t1.client_id ) t3 on t2.rowId=t3.rowId;
                 ;";
		$this->db->query($sql);
	}*/
	function getClientComparisonAwards(array $paramsArr,$awardScheme){		
		$sql="";
		if(count($paramsArr)==1){//for default when page loads or no filter is applied
			$params=$paramsArr[0];
			$sql = "select a.award_scheme_id,d.award_id,standard_name,award_name,if(b.num,b.num,0) num  from h_award_scheme a inner join d_award d on d.award_id = a.award_id
					left join d_tier t on t.standard_id=a.tier_id
	 				left join (select tier_id,external_award,count(external_award) as num from temp_reviewData where ".$params."
					group by external_award) b on a.order=b.external_award WHERE 1=1".$awardScheme." order by standard_id desc,award_id desc";
		}
		else {
			$i=0;
			$sqlSubQuery="";
			//print_r($paramsArr);
			foreach($paramsArr as $params){
				$i>0?$sqlSubQuery.=" union ":"";
				$sqlSubQuery .= "select * from temp_reviewData  where ".$params;				
				$i++;
			}			
			$sql = "select a.award_scheme_id,d.award_id,standard_name,d.award_name END as award_name,if(b.num,b.num,0) num  from h_award_scheme a inner join d_award d on d.award_id = a.award_id
					left join d_tier t on t.standard_id=a.tier_id
	 				left join (select tier_id, external_award,count(external_award) as num from (".$sqlSubQuery." )att group by external_award) b on a.order=b.external_award WHERE 1=1".$awardScheme." order by standard_id desc,award_id desc";
		}					
		$res = $this->db->get_results($sql);
		//print_r($res);
		return $res?$res:array();
	}
	function getClientComparisonAwardsCombination($subQuery,$awardScheme){
		$sql = "select a.award_scheme_id,d.award_id,standard_name,award_name,if(b.num,b.num,0) num  from h_award_scheme a inner join d_award d on d.award_id = a.award_id
					left join d_tier t on t.standard_id=a.tier_id
	 				left join ( select tier_id, external_award,count(external_award) as num from (".$subQuery.") att group by external_award) b on a.order=b.external_award WHERE 1=1".$awardScheme." order by standard_id desc,award_id desc";
		$res = $this->db->get_results($sql);
		//print_r($res);
		return $res?$res:array();
	}
	function createKpaRatingsDashboard(){
		$sql = "drop table if exists tempratingGraph;
				create temporary table tempratingGraph select d.kpa_id,d.kpa_name,e.*,ast.*
				from temp_reviewData ast
				inner join d_diagnostic dg on dg.diagnostic_id=ast.diagnostic_id and dg.assessment_type_id=1
				inner join h_kpa_instance_score a on a.assessment_id=ast.assessment_id
									inner join h_kpa_diagnostic c on a.kpa_instance_id = c.kpa_instance_id -- and c.kpa_order < 7
									inner join d_kpa d on d.kpa_id = c.kpa_id
									inner join d_rating e on a.d_rating_rating_id = e.rating_id
									inner join h_assessment_user f on a.assessor_id = f.user_id and a.assessment_id = f.assessment_id and f.role = 4					
									inner join h_diagnostic_rating_scheme h on h.rating_id = a.d_rating_rating_id and h.diagnostic_id = ast.diagnostic_id					
									group by kpa_name,e.rating_id,ast.client_id
								order by c.`kpa_order` asc; ";
		$this->db->query($sql);
	}
	function getKpaRatingsDasboard($kpa_id,$subquery=""){
		$subquery!=""?($subquery = " AND assessment_id IN (".$subquery.")"):'';
		$sql = "select e.rating,b.kpa_id,b.kpa_name,b.num from d_rating e left join ( select kpa_id,kpa_name,rating,ifnull(count(rating),0) as num,rating_id from tempratingGraph WHERE kpa_id= $kpa_id ".$subquery." group by kpa_id,rating_id) b on e.rating_id = b.rating_id where e.rating_id in(5,6,7,8);";
		
		$res = $this->db->get_results($sql);
		//print_r($res);
		return $res?$res:array();
	}
	function getKpasDasboard(){
		$sql = "select distinct kpa_id,kpa_name from tempratingGraph";		
		$res = $this->db->get_results($sql);
		//print_r($res);
		return $res?$res:array();
	}

	public function getDiagnosticLevelQues($args=array(),$tbl,$id,$name){
		$args=$this->parse_arg($args,array($name."_like"=>"","max_rows"=>10,"page"=>1,"order_by"=>$name,"order_type"=>"asc"));
		$order_by=array("name"=>$name);
		$sqlArgs=array();		
		$sql="SELECT
				SQL_CALC_FOUND_ROWS t.*,t.$id,t.$name
				from $tbl t 
				where 1=1  ";
		// get external assessor user list for tap admin by giving external user id (4) in query on 12-05-2016 by Mohit Kumar
//	print_r($args);
		if($args[$name.'_like']!=""){
			$sql.="and $name like ? ";
			$sqlArgs[]="%".$args[$name.'_like']."%";
		}		
		if($args[$id]>0){
			$sql.="and $id = ? ";
			$sqlArgs[]=$args[$id];
		}		


		$sql.=" order by ".(isset($order_by[$args["order_by"]])?$order_by[$args["order_by"]]:"$name").($args["order_type"]=="desc"?" desc ":" asc ").$this->limit_query($args['max_rows'],$args['page']);
		$res= $this->db->get_results($sql,$sqlArgs);
		$this->setPageCount($args['max_rows']);
		//echo $sql;
		return $res;
	}
	
	public static function getQuestionsListNode($sno,$question,$type,$addDelete=1,$value=null){
	//print_r($question);
		return '<div title="'.$question['text'].'" class="questionNode clearfix questionNode-'.$question['id'].'" data-id="'.$question['id'].'"><span class="uname">'.$question['text'].'</span><input type="hidden" class="ajaxFilterAttach" name="question['.$type.'][]" value="'.(empty($value)?$question['id']:$value).'"/>'.($addDelete?'<span class="delete"><i class="fa fa-times"></i></span>':'').'</div>';
	
	}
	public function getQuestionTextforIds( $tableName,$id,$name,$currentSelectionIds ){
		$sql = "select $id as id,$name as text from $tableName where $id in ($currentSelectionIds)";
		//echo $sql,$currentSelectionIds;
		$res = $this->db->get_results($sql);
		$res?$res:array();
		return $res;
	}
	function getSubAttr(){
		$sql = "select distinct filter_sub_attr_id attr_id from h_filter_sub_attr_operator";		
		$res = $this->db->get_results($sql);		
		$res?$res:array();
		return $res;
	}
	function getRatingForAttr($is_judgestmt_rating=0){
		$sql = "SELECT distinct rating_id,rating FROM h_diagnostic_rating_scheme inner join d_rating using(rating_id)
 			where is_judgestmt_rating=?";
		$res = $this->db->get_results($sql,array($is_judgestmt_rating));
		$res?$res:array();
		return $res;
	}
	function getRatingText($rating_id){
		$sql = "SELECT distinct rating FROM d_rating where rating_id=?";
		$res = $this->db->get_row($sql,array($rating_id));
		$res?$res:array();
		return $res;
	}
	function saveFilterSubAttr($filter_instance_id,$filter_sub_attr_id,$filter_sub_operator,$filter_sub_attr_rating,$filter_sub_attr_cardinality,$filter_sub_attr_max_cardinality){
		if($this->db->insert("h_filter_sub_attr",array("filter_instance_id"=>$filter_instance_id,"filter_sub_attr_id"=>$filter_sub_attr_id,"filter_sub_attr_operator"=>$filter_sub_operator,"filter_sub_attr_rating"=>$filter_sub_attr_rating,"filter_sub_attr_cardinality"=>$filter_sub_attr_cardinality,"filter_sub_attr_max_cardinality"=>$filter_sub_attr_max_cardinality)))
			return true;
			return false;
	}
	function deleteFilterSubAttr($filter_instance_id){
		if($this->db->delete("h_filter_sub_attr",array("filter_instance_id"=>$filter_instance_id)))
			return true;
			return false;
	}
	function getvarList(){
		$sql = "Select filter_attr_id,filter_attr_name from d_filter_attr where active=1 order by filter_attr_name";
		$res = $this->db->get_results($sql);
		$res?$res:array();
		return $res;
	}
	function createAdminDashboardData() {
		$this->db->query ( "drop  table if exists t_kpaData;
create  table t_kpaData(client_id int(11),diagnostic_id int(11),assessor_id int(11),assessment_id int(11),d_sub_assessment_type_id int(11),award_scheme_id int(11),tier_id int(11),kpa_id int(11),kpa_instance_id int(11),user_id int(11),role int(11),d_rating_rating_id int(11),
INDEX idx_tkpa(assessment_id,user_id,kpa_instance_id));
insert into t_kpaData
SELECT a.client_id,d.diagnostic_id,hu.user_id,a.assessment_id,a.d_sub_assessment_type_id,a.award_scheme_id,a.tier_id,k.kpa_id,h.kpa_instance_id,hu.user_id,hu.role,
s.d_rating_rating_id FROM 
d_kpa k  inner join h_kpa_diagnostic h on h.kpa_id=k.kpa_id
inner join d_diagnostic d on d.diagnostic_id=h.diagnostic_id 
inner join d_assessment a on a.diagnostic_id=d.diagnostic_id 
inner join h_assessment_user hu on hu.assessment_id=a.assessment_id 
inner join h_kpa_instance_score s on h.kpa_instance_id=s.kpa_instance_id and a.assessment_id=s.assessment_id and s.assessor_id=hu.user_id
 where d.assessment_type_id=1 and hu.role=4;
 
 drop  table if exists t_kq;
create  table t_kq(assessment_id int(11),kpa_instance_id int(11),key_question_id int(11),key_question_instance_id int(11),d_rating_rating_id int(11),assessor_id int(11), INDEX(assessment_id,assessor_id,kpa_instance_id)) ;
insert into t_kq
select ks.assessment_id,kpa_instance_id,key_question_id,key_question_instance_id,d_rating_rating_id,assessor_id from 
h_kq_instance_score ks inner join h_kpa_kq using(key_question_instance_id)
inner join h_assessment_user hu on ks.assessor_id=hu.user_id and ks.assessment_id=hu.assessment_id 
inner join d_assessment a on a.assessment_id=ks.assessment_id
inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id 
where d.assessment_type_id=1 and  hu.role=4;
; " );
		$this->db->query ( "drop   table if exists t_sq;
create   table t_sq(assessment_id int(11),core_question_id int(11),key_question_instance_id int(11),core_question_instance_id int(11),d_rating_rating_id int(11),assessor_id int(11),
index idx_js(assessment_id,assessor_id,key_question_instance_id));
insert into t_sq
select cs.assessment_id,kc.core_question_id,kc.key_question_instance_id,cs.core_question_instance_id,cs.d_rating_rating_id,cs.assessor_id from 
h_kq_cq kc inner join h_cq_score cs on cs.core_question_instance_id = kc.core_question_instance_id
inner join h_assessment_user hu on cs.assessor_id=hu.user_id and cs.assessment_id=hu.assessment_id
inner join d_assessment a on a.assessment_id=cs.assessment_id
inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id 
where d.assessment_type_id=1 and  hu.role=4;" );
		// judgement statemnts;
		/* $this->db->query ( "drop  table if exists t_js;
create  table t_js(assessment_id int(11),core_question_instance_id int(11),judgement_statement_id int(11),judgement_statement_instance_id int(11),rating_id int(11),assessor_id int(11),
index idx_js(assessment_id,assessor_id,core_question_instance_id));
insert into t_js 
select fs.assessment_id,cj.core_question_instance_id,cj.judgement_statement_id,fs.judgement_statement_instance_id,fs.rating_id,fs.assessor_id from 
h_cq_js_instance cj inner join f_score fs on fs.judgement_statement_instance_id = cj.judgement_statement_instance_id
inner join h_assessment_user hu on fs.assessor_id=hu.user_id and fs.assessment_id=hu.assessment_id
 inner join d_assessment a on a.assessment_id=fs.assessment_id
 inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id 
where d.assessment_type_id=1 and hu.role=4 and fs.isFinal=1 ;" ); */	
		$this->db->query ( "drop  table if exists t_js;
create  table t_js(assessment_id int(11),core_question_instance_id int(11),judgement_statement_id int(11),judgement_statement_instance_id int(11),rating_id int(11),assessor_id int(11),jd int(1),score_id int(11),
index idx_js(assessment_id,assessor_id,core_question_instance_id));
insert into t_js 
select fs.assessment_id,cj.core_question_instance_id,cj.judgement_statement_id,fs.judgement_statement_instance_id,fs.rating_id,fs.assessor_id,
(abs(fs.rating_id-(select distinct fs1.rating_id from f_score fs1  where fs1.assessment_id=fs.assessment_id and fs1.assessor_id!=fs.assessor_id and fs1.judgement_statement_instance_id=fs.judgement_statement_instance_id and fs1.isFinal=1)))jd,score_id
 from 
h_cq_js_instance cj inner join f_score fs on fs.judgement_statement_instance_id = cj.judgement_statement_instance_id
inner join h_assessment_user hu on fs.assessor_id=hu.user_id and fs.assessment_id=hu.assessment_id
 inner join d_assessment a on a.assessment_id=fs.assessment_id
 inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id 
where d.assessment_type_id=1 and hu.role=4 and fs.isFinal=1 ;" );
	}
	function getAdminDashboardData($params,$joinQuery,$pivotCol,$pivotRow,$count_criteria,$order_by){		
		$sql="drop temporary table if exists allData;
		create temporary table allData
		select tbl.*,if($pivotCol is null OR $pivotCol='','NA',$pivotCol) as pivotCol,if($pivotRow is null OR $pivotRow='','NA',$pivotRow) as pivotRow from (
		-- select tbl.*,if($pivotCol=0,0,if($pivotCol is null OR $pivotCol='','NA',$pivotCol)) as pivotCol,if($pivotRow is null OR $pivotRow='','NA',$pivotRow) as pivotRow from (   
		-- select tbl.*,$pivotCol as pivotCol,$pivotRow as pivotRow from (
		select v_aqs.*,kpa.assessor_id,kpa.role,kpa.kpa_id,kpa.kpa_instance_id,kpa.d_rating_rating_id as rating13,kq.key_question_id,kq.key_question_instance_id,kq.d_rating_rating_id rating23,
		sq.core_question_id,sq.core_question_instance_id,sq.d_rating_rating_id as rating24,js.judgement_statement_id,js.rating_id as rating25,js.judgement_statement_instance_id,score_id,jd from  t_kpaData kpa
		inner join t_kq kq on kq.assessment_id=kpa.assessment_id and kq.assessor_id=kpa.user_id and  kpa.kpa_instance_id=kq.kpa_instance_id
		inner join t_sq sq on sq.assessment_id=kq.assessment_id and kq.assessor_id=sq.assessor_id and sq.key_question_instance_id=kq.key_question_instance_id
		inner join t_js js on js.assessment_id=sq.assessment_id and sq.assessor_id=js.assessor_id and js.core_question_instance_id=sq.core_question_instance_id		
		inner join v_aqs on v_aqs.assessment_id=kpa.assessment_id and v_aqs.assessment_id=kq.assessment_id and v_aqs.assessment_id=sq.assessment_id and v_aqs.assessment_id=js.assessment_id  )tbl ".$joinQuery."  WHERE 1=1 and tbl.client_id not in(115,154,175) ".$params.$order_by ;				
		$this->db->query($sql);	
		//echo $sql;die;
		$res = $this->db->get_row("select count(*) as num from allData");
		$isData = $res['num'];
		//echo $isData;die;
		// $isData = $this->db->get_var("select * from allData");
		if(!$isData)
			return 0; 
		$aggregateCol = '';		
		switch($pivotRow){
			case "concat(ifnull(standard_name,''),' ',award_name)":$aggregateCol='assessment_id';
					break;
			case "c.client_name":$aggregateCol='client_name';
					break;
			case "assessor_id":$aggregateCol='assessment_id';
					break;
			default:$aggregateCol='client_id';
					break;
		}
		switch($pivotCol){
			case "concat(ifnull(standard_name,''),' ',award_name)":$aggregateCol='assessment_id';
				break;
			case "c.client_name":$aggregateCol='client_name';
					break;
			case "assessor_id":$aggregateCol='assessment_id';
					break;
			default:$aggregateCol='client_id';
				break;
		}		 
		//$aggregateCol = $count_criteria;
		
		$res = $this->db->get_results("call Pivot('allData','pivotRow','pivotCol','$aggregateCol','','')");		
		$res?$res:array();
		return $res;
	}
	function getMatchingColName($tblName,$colId,$colText,$attrId,$otherAttrId,$customReportObject){
		//$param['filter_table']=='d_fees'?($param['filter_table_col_id']='annual_fee')
		$join='';
		switch($tblName){
			case 'd_fees': $colId=$colText;$colText='annual_fee';
				$join = "inner join {$tblName} on {$tblName}.{$colId} = tbl.{$colText}";
				break;
			case 'd_school_strength':$colId=$colText;$colText='no_of_students';
				$join = "inner join {$tblName} on {$tblName}.{$colId} = tbl.{$colText}";
				break;
			case 'd_tier':$colText='tier_id';
				$join = "inner join {$tblName} on {$tblName}.{$colId} = tbl.{$colText}";
				break;
			case 'd_language':$colText='medium_instruction';
				$join = "inner join {$tblName} on {$tblName}.{$colId} = tbl.{$colText}";
				break;
			case 'd_school_region':$colText='school_region_id';
				$join = "inner join {$tblName} on {$tblName}.{$colId} = tbl.{$colText}";
				break;
			case 'd_user':$colText='assessor_id';
				$join = "inner join {$tblName} on {$tblName}.{$colId} = tbl.{$colText} and tbl.role=4";
				break;	
			/* case 'd_kpa':$join = " inner join h_kpa_diagnostic on tbl.kpa_instance_id = h_kpa_diagnostic.kpa_instance_id  inner join {$tblName} on {$tblName}.{$colId} = h_kpa_diagnostic.kpa_id ";
				break;	
			case 'd_key_question':$join = " inner join h_kpa_kq on tbl.key_question_instance_id = h_kpa_kq.key_question_instance_id  inner join {$tblName} on {$tblName}.{$colId} = h_kpa_kq.key_question_id ";
				break;	
			case 'd_core_question':$join = " inner join h_kq_cq on tbl.core_question_instance_id = h_kq_cq.core_question_instance_id  inner join {$tblName} on {$tblName}.{$colId} = h_kq_cq.core_question_id ";
				break;	
			case 'd_judgement_statement':$join = " inner join h_cq_js_instance on tbl.judgement_statement_instance_id = h_cq_js_instance.judgement_statement_instance_id  inner join {$tblName} on {$tblName}.{$colId} = h_cq_js_instance.judgement_statement_id ";
				break;	 */							
			case 'd_award':$join = "inner join h_award_scheme h on  tbl.external_award = h.`order`  and tbl.award_scheme_id=h.award_scheme_id inner join d_award on d_award.award_id = h.award_id left join d_tier on d_tier.standard_id = h.tier_id";
				break;
			case 'd_AQS_data': switch($attrId){
									case '16': //year
									case '17'://month
									case '18'://date	
								}
				break;
			case 'd_school_class': $join = ' inner join d_school_class sc on sc.class_id = tbl.classes_from inner join d_school_class sc2 on sc2.class_id = tbl.classes_to ';	
					break;	
			case 'd_rating':$colText='rating'.$otherAttrId;
					$join = "inner join {$tblName} on {$tblName}.{$colId} = tbl.{$colText}";
					break;	
			case 't_jd': $join='';/*$customReportObject->createJudgementDistance();
					$join = "inner join {$tblName} on {$tblName}.assessment_id = tbl.assessment_id and {$tblName}.judgement_statement_instance_id = tbl.judgement_statement_instance_id";*/
					break;
			case '': $join = ' inner join d_client c on c.client_id = tbl.client_id ';
					break;//for num of reviews
				
			default: $join = "inner join {$tblName} on {$tblName}.{$colId} = tbl.{$colId}";
		}
		
		return $join;
	}
	function getsubAttrBuildQuery($sub_attr_id,$sub_attr_idrating,$sub_operator,$cardinality,$sub_attr_rating){
		$col = '';
		$sql = '';
		switch($sub_attr_id){
			case 13 : $col = 'kpa_id';
					break;
			case 23 : $col = 'key_question_id';
					break;
			case 24 : $col = 'core_question_id';
					break;
			case 25 : $col = 'judgement_statement_id';
					break;	
		 	case 27 : $col = 'jd';
					break; 
		}
		
		/* $sql="select distinct assessment_id,kpa_id, rating13 from (select v_aqs.*,kpa.assessor_id,kpa.role,kpa.kpa_id,kpa.kpa_instance_id,kpa.d_rating_rating_id as rating13,kq.key_question_id,kq.key_question_instance_id,kq.d_rating_rating_id rating23,
		sq.core_question_id,sq.core_question_instance_id,sq.d_rating_rating_id as rating24,js.judgement_statement_id,js.rating_id as rating25,js.judgement_statement_instance_id from  t_kpaData kpa
		inner join t_kq kq on kq.assessment_id=kpa.assessment_id and kq.assessor_id=kpa.user_id and  kpa.kpa_instance_id=kq.kpa_instance_id
		inner join t_sq sq on sq.assessment_id=kq.assessment_id and kq.assessor_id=sq.assessor_id and sq.key_question_instance_id=kq.key_question_instance_id
		inner join t_js js on js.assessment_id=sq.assessment_id and sq.assessor_id=js.assessor_id and js.core_question_instance_id=sq.core_question_instance_id
		inner join v_aqs on v_aqs.assessment_id=kpa.assessment_id and v_aqs.assessment_id=kq.assessment_id and v_aqs.assessment_id=sq.assessment_id and v_aqs.assessment_id=js.assessment_id) ";		 */
		if($sub_attr_id==27){
			
			
			$sql="select group_concat(score_id) as score_id from (select distinct score_id from (select distinct assessment_id,kpa_id, rating13,jd,score_id from (select v_aqs.*,kpa.assessor_id,kpa.role,kpa.kpa_id,kpa.kpa_instance_id,kpa.d_rating_rating_id as rating13,kq.key_question_id,kq.key_question_instance_id,kq.d_rating_rating_id rating23,
			sq.core_question_id,sq.core_question_instance_id,sq.d_rating_rating_id as rating24,js.judgement_statement_id,js.rating_id as rating25,js.judgement_statement_instance_id,jd,score_id from  t_kpaData kpa
			inner join t_kq kq on kq.assessment_id=kpa.assessment_id and kq.assessor_id=kpa.user_id and  kpa.kpa_instance_id=kq.kpa_instance_id
			inner join t_sq sq on sq.assessment_id=kq.assessment_id and kq.assessor_id=sq.assessor_id and sq.key_question_instance_id=kq.key_question_instance_id
			inner join t_js js on js.assessment_id=sq.assessment_id and sq.assessor_id=js.assessor_id and js.core_question_instance_id=sq.core_question_instance_id
			-- inner join t_jd1 jd on jd.assessment_id=js.assessment_id and jd.judgement_statement_id=js.judgement_statement_id
			inner join v_aqs on v_aqs.assessment_id=kpa.assessment_id and v_aqs.assessment_id=kq.assessment_id and v_aqs.assessment_id=sq.assessment_id and v_aqs.assessment_id=js.assessment_id)tbl1 group by assessment_id,$col ) tbl2
			group by assessment_id
			having sum(if(jd=$sub_attr_rating,1,0))$sub_operator $cardinality ) t";
			//echo $sql;die;
		}
		else	
			$sql="select group_concat(score_id) as score_id from (select distinct assessment_id,score_id from (select distinct assessment_id,kpa_id, rating13,score_id from (select v_aqs.*,kpa.assessor_id,kpa.role,kpa.kpa_id,kpa.kpa_instance_id,kpa.d_rating_rating_id as rating13,kq.key_question_id,kq.key_question_instance_id,kq.d_rating_rating_id rating23,
			sq.core_question_id,sq.core_question_instance_id,sq.d_rating_rating_id as rating24,js.judgement_statement_id,js.rating_id as rating25,js.judgement_statement_instance_id,score_id from  t_kpaData kpa
			inner join t_kq kq on kq.assessment_id=kpa.assessment_id and kq.assessor_id=kpa.user_id and  kpa.kpa_instance_id=kq.kpa_instance_id
			inner join t_sq sq on sq.assessment_id=kq.assessment_id and kq.assessor_id=sq.assessor_id and sq.key_question_instance_id=kq.key_question_instance_id
			inner join t_js js on js.assessment_id=sq.assessment_id and sq.assessor_id=js.assessor_id and js.core_question_instance_id=sq.core_question_instance_id
			-- inner join t_jd jd on jd.assessment_id=js.assessment_id and jd.judgement_statement_id=js.judgement_statement_id
			inner join v_aqs on v_aqs.assessment_id=kpa.assessment_id and v_aqs.assessment_id=kq.assessment_id and v_aqs.assessment_id=sq.assessment_id and v_aqs.assessment_id=js.assessment_id)tbl1 group by assessment_id,$col ) tbl2
	        group by assessment_id
	        having sum(if($sub_attr_idrating=$sub_attr_rating,1,0))$sub_operator $cardinality ) t";
	//	echo $sql;die;
		$res = $this->db->get_row($sql);
		$res?$res:array();
		
		return $res;
	}
	function createJudgementDistance(){
		$sql = "drop temporary table if exists js_internal;
				create temporary table js_internal 
				select fs.assessment_id,cj.core_question_instance_id,cj.judgement_statement_id,fs.judgement_statement_instance_id,fs.rating_id,fs.assessor_id from
				h_cq_js_instance cj inner join f_score fs on fs.judgement_statement_instance_id = cj.judgement_statement_instance_id
				inner join h_assessment_user hu on fs.assessor_id=hu.user_id and fs.assessment_id=hu.assessment_id
				 inner join d_assessment a on a.assessment_id=fs.assessment_id inner join d_diagnostic d on d.diagnostic_id=a.diagnostic_id
				where d.assessment_type_id=1 and hu.role=3 and fs.isFinal=1;
				
				drop table if exists t_jd;
create  table t_jd(assessment_id int(11),judgement_statement_instance_id int(11),judgement_statement_id int(11),jd int(1),
index idx_jd(assessment_id,judgement_statement_id,judgement_statement_instance_id,jd));
insert into t_jd
				select js_internal.assessment_id,t_js.judgement_statement_instance_id,t_js.judgement_statement_id,abs(js_internal.rating_id-t_js.rating_id) jd
from js_internal inner join t_js using(assessment_id,judgement_statement_instance_id,judgement_statement_id);				
				";
		//echo $sql;
		$this->db->query($sql);
	}
	function getStringForFilterValue($filter_table,$filter_col_id,$filter_col_name,$filter_attr_id,$operator_id,$filter_multiple_vals,$first_val,$second_val){
		
		//get table name to populate
		$res = '';
		if(in_array($filter_attr_id,array(16,17,18)))
		{
			$res['data'] = $filter_multiple_vals?$filter_multiple_vals:("$first_val and $second_val");
		}
		else	
		switch($operator){
			/* //IN
			case '8':	$sql = "Select * from $filter_table where $filter_col_id IN (?) ";
				$res = $this->db->get_results($sql,array($filter_multiple_vals));
				break; */
			//between	
			case '7':	$sql = "Select group_concat($filter_col_name) data from $filter_table where $filter_col_id IN ('".$first_val."','".$second_val."') ";
				$res = $this->db->get_row($sql);
				break;
			default: 	$sql = "Select group_concat($filter_col_name) data from $filter_table where $filter_col_id IN ($filter_multiple_vals) ";
				$res = $this->db->get_row($sql);
				break;
		}		
		return $res;
	}
	function getKeyForReportKPA(){
		$html = '<table border="1" cellpadding="3">
					<thead>
						<tr style="font-weight:bold;"><th style="width:15%;">Color Code</th><th style="width:85%;">Description</th></tr>
					</thead>
					<tr><td style="width:15%;color:#307ACE;font-weight:bold;">A</td><td style="width:85%;">Always</td></tr>
					<tr><td style="color:#5e9900;font-weight:bold;">M</td><td>Mostly</td></tr>
					<tr><td style="color:#dbb113;font-weight:bold;">S</td><td>Sometimes</td></tr>
					<tr><td style="color:#D12200;font-weight:bold;">R</td><td>Rarely</td></tr>
					<tr><td style="background-color:#CCCCCC;">&nbsp;</td><td>Match between the self-review and external review ratings (judgement distance = 0,1,-1)</td></tr>
				</table>';
		return $html;
	}
}
