<?php

class schoolAssessmentAwardMatrix{
	var $selectedTier;
	var $firstCompulsoryKpaScore;
	var $secondCompulsoryKpaScore;
	var $noOfScore1234InKpas=array(1=>0,2=>0,3=>0,4=>0);
	
	function __construct($compulsoryKpaScore1,$compulsoryKpaScore2,$noOfScore1234InKpas,$selectedTier){
		$this->firstCompulsoryKpaScore=$compulsoryKpaScore1;
		$this->secondCompulsoryKpaScore=$compulsoryKpaScore2;
		$this->selectedTier = $selectedTier;
		foreach($noOfScore1234InKpas as $k=>$v)
			$this->noOfScore1234InKpas[$k]=$v;
	}
	
	function firstLevel(){
        if($this->noOfScore1234InKpas[1] > 2){
            return 10;
        }
        else if($this->noOfScore1234InKpas[1] > 0){
			return $this->secondLevel();
		}
        else 
            return $this->thirdLevel();
    }
    
    function secondLevel(){
        if($this->firstCompulsoryKpaScore >= 2 || $this->secondCompulsoryKpaScore>=2){
            return 9;
        }
        else{
            return 10;
        }
    }
    
    function thirdLevel(){
        
        if(($this->noOfScore1234InKpas[4]+$this->noOfScore1234InKpas[3])>2){
            return $this->fourthLevel();
        }
        else if(($this->noOfScore1234InKpas[4]+$this->noOfScore1234InKpas[3])<2){
			return 9;
		}
		elseif($this->firstCompulsoryKpaScore >=3 || $this->secondCompulsoryKpaScore>=3){
			 return 8;
		 }
		 else{
			 return 9;
		 }
   }
    
  function fourthLevel(){
      
      if($this->firstCompulsoryKpaScore>=3 || $this->secondCompulsoryKpaScore>=3){
      
          if($this->noOfScore1234InKpas[4]==0){
                if($this->selectedTier==3){
                    return 8;
                }
                else if($this->noOfScore1234InKpas[2] > 2){
					return 6;
				}
				else{
					return 5;
				}
          }
          else{
              return $this->fifthLevel();
          }
      }
      else{
          return 6;
      }
      
  }
   
  function fifthLevel(){
      
      if($this->selectedTier == 8){
          return 7;
      }
      else if($this->noOfScore1234InKpas[2] == 3){
		  return 6;
	  }
	  else{
		 return $this->sixthLevel();
	  }
      
  } 
   
  function sixthLevel(){
      if($this->firstCompulsoryKpaScore >=3 && $this->secondCompulsoryKpaScore>=3){
          return $this->seventhLevel();
      }
      else if($this->noOfScore1234InKpas[2] < 2){
		  if($this->selectedTier == 2){
			  return 5;
		  }
		  else{
			  return 3;
		  }
	  }
	  else if($this->selectedTier==3){
		return 7;
	  }
	  else{
		return 5;
	  }
  }
    
  function seventhLevel(){
      if($this->noOfScore1234InKpas[2] < 2){
          if($this->noOfScore1234InKpas[4] > 1){
              return $this->eighthLevel();
          }
          else if($this->selectedTier == 2){
			 return 4;
		  }
		  else{
			return 3;
		  }
      }
      else{
         return 5;
      }
  }

  function eighthLevel(){
	if($this->noOfScore1234InKpas[2] > 0 ){
		return $this->ninethLevel();
	}
	else if($this->selectedTier == 2){
		return 4;
	}
	else if($this->firstCompulsoryKpaScore == 4 && $this->secondCompulsoryKpaScore == 4){
		return 1;
	}
	else{
		return 2;
	}
  }
  
  function ninethLevel(){
      if($this->selectedTier == 2){
         return 4;
      }
      else{
		return 3;
      }
  }
}