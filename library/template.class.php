<?php
class Template {

	protected $variables = array();
	protected $_controller;
	protected $_action;
	
	protected $_headerScripts=array();
	protected $_headerScriptsURL=array();
	protected $_footerScripts=array();
	protected $_headerStyles=array();
	protected $_headerStylesURL=array();
	protected $_footerStyles=array();
	protected $_addTextToHead="";
	protected $_addTextToFoot="";

	function __construct($controller,$action) {
		$this->_controller = $controller;
		$this->_action = $action;
	}

	function set($name,$value) {
		$this->variables[$name] = $value;
	}
	
	function addHeaderScript($name){
		$this->_headerScripts[]=$name;
	}
	function addHeaderScriptURL($name){
		$this->_headerScriptsURL[]=$name;
	}
	
	function clearHeaderScript(){
		$this->_headerScripts=array();
	}
	
	function addFooterScript($name){
		$this->_footerScripts[]=$name;
	}
	
	function clearFooterScript(){
		$this->_footerScripts=array();
	}
	
	function addHeaderStyle($name){
		$this->_headerStyles[]=$name;
	}
	function addHeaderStyleURL($name){
		$this->_headerStylesURL[]=$name;
	}
	
	function clearHeaderStyle(){
		$this->_headerStyles=array();
	}
	
	function addFooterStyle($name){
		$this->_footerStyles[]=$name;
	}
	
	function clearFooterStyle(){
		$this->_footerStyles=array();
	}
	
	function clearHeaderFooter(){
		$this->_headerScripts=array();
		$this->_footerScripts=array();
		$this->_headerStyles=array();
		$this->_footerStyles=array();
	}
	
    function render() {
		extract($this->variables);
                if(isset($noHeader) && $noHeader == 1){
                   include (ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php'); 
                    
                }else if(isset($web) && $web==1){
			$addToHeader="";
			foreach($this->_headerStyles as $s){
				$addToHeader.="\t<link href=\"".SITEURL."public/css/$s\" rel=\"stylesheet\" />\n";
			}			
			foreach($this->_headerScripts as $s){
				$addToHeader.="\t<script type=\"text/javascript\" src=\"".SITEURL."public/js/$s\"></script>\n";
			}			
			$addToHeader.=$this->_addTextToHead;
				
		
				include (ROOT . 'application' . DS . 'views' . DS . 'webheader.php');
			
				
			if($notPermitted)
				include (ROOT . 'application' . DS . 'views' . DS . "index" . DS . 'noaccess.php');
				else if(!file_exists(ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php') || $is404)
					include (ROOT . 'application' . DS . 'views' . DS . "index" . DS . '404.php');
					else
						include (ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php');
							
						$addToFooter="";
						foreach($this->_footerStyles as $s){
							$addToFooter.="\t<link href=\"".SITEURL."public/css/$s\" rel=\"stylesheet\" />\n";
						}
						foreach($this->_footerScripts as $s){
							$addToFooter.="\t<script type=\"text/javascript\" src=\"".SITEURL."public/js/$s\"></script>\n";
						}
						$addToFooter.=$this->_addTextToFoot;
							
						if (file_exists(ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . 'footer.php')) {
							include (ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . 'footer.php');
						} else {
							include (ROOT . 'application' . DS . 'views' . DS . 'footer.php');
						}
		}
		else if($ajaxRequest!=1){
			$addToHeader="";
			foreach($this->_headerStyles as $s){
				$addToHeader.="\t<link href=\"".SITEURL."public/css/$s\" rel=\"stylesheet\" />\n";
			}			
			foreach($this->_headerStylesURL as $s){
				$addToHeader.="\t<link href=\"$s\" rel=\"stylesheet\" />\n";
			}
			foreach($this->_headerScripts as $s){
				$addToHeader.="\t<script type=\"text/javascript\" src=\"".SITEURL."public/js/$s\"></script>\n";
			}	
			foreach($this->_headerScriptsURL as $s){
				$addToHeader.="\t<script type=\"text/javascript\" src=\"$s\"></script>\n";
			}
			
			$addToHeader.=$this->_addTextToHead;
			
			if (file_exists(ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . 'header.php')) {
				include (ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . 'header.php');
			} else {
				include (ROOT . 'application' . DS . 'views' . DS . 'header.php');
			}
			
			if($notPermitted)
				include (ROOT . 'application' . DS . 'views' . DS . "index" . DS . 'noaccess.php');
			else if(!file_exists(ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php') || $is404)
				include (ROOT . 'application' . DS . 'views' . DS . "index" . DS . '404.php');
			else
				include (ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php');
			
			$addToFooter="";
			foreach($this->_footerStyles as $s){
				$addToFooter.="\t<link href=\"".SITEURL."public/css/$s\" rel=\"stylesheet\" />\n";
			}
			foreach($this->_footerScripts as $s){
				$addToFooter.="\t<script type=\"text/javascript\" src=\"".SITEURL."public/js/$s\"></script>\n";
			}
			$addToFooter.=$this->_addTextToFoot;
			
			if (file_exists(ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . 'footer.php')) {
				include (ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . 'footer.php');
			} else {
				include (ROOT . 'application' . DS . 'views' . DS . 'footer.php');
			}
		}else{
			if($notPermitted)
				include (ROOT . 'application' . DS . 'views' . DS . "index" . DS . 'noaccess.php');
			else if(!file_exists(ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php') || $is404)
				include (ROOT . 'application' . DS . 'views' . DS . "index" . DS . '404.php');
			else
				include (ROOT . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php');
		}
		
    }
	
	function generateAjaxPaging($pageCount=1,$currentPage=1){
		if($pageCount>1){
			$firstPagingFrom=1;
			$firstPagingTo=$pageCount;
			$secondPagingFrom=0;
			$secondPagingTo=$pageCount;
			if($pageCount>14){
				if($currentPage<6 ){
					$firstPagingTo=7;
					$secondPagingFrom=$pageCount-3;
				}else if(($currentPage+6)>$pageCount){
					$firstPagingTo=4;
					$secondPagingFrom=$pageCount-7;
				}else {
					$firstPagingFrom=$currentPage-3;
					$firstPagingTo=$currentPage+2;
					$secondPagingFrom=$pageCount-2;
				}
			}
			?>
			<div class="paginHldr">
				<ul class="pagination">
				<?php
				echo '<li '.($currentPage>1?'':'class="disabled"').'><a href="javascript:void(0)" class="paging" data-value="'.($currentPage>1?1:0).'" aria-label="First"><span aria-hidden="true">&laquo;</span></a></li>
				<li '.($currentPage>1?'':'class="disabled"').'><a href="javascript:void(0)" class="paging" data-value="'.($currentPage-1).'" aria-label="Previous"><span aria-hidden="true">&lsaquo;</span></a></li>';
				echo $firstPagingFrom>1?'<li><a class="paging" data-value="1" href="javascript:void(0)">1</a></li><li><a class="paging Space" href="#">...</a></li>':'';
				for($i=$firstPagingFrom;$i<=$firstPagingTo;$i++){
					echo "<li><a class=\"paging ".($i==$currentPage?"active":"")."\" data-value=\"$i\"  href=\"javascript:void(0)\">$i</a></li>";
				}
				if($secondPagingFrom){
					echo "<li><a class=\"paging Space\" href=\"javascript:void(0)\">...</a></li>";
					for($i=$secondPagingFrom;$i<=$secondPagingTo;$i++){
						echo "<li><a href=\"javascript:void(0)\" class=\"paging ".($i==$currentPage?"active":"")."\" data-value=\"$i\">$i</a></li>";
					}
				}
				echo '<li '.($pageCount!=$currentPage?'':'class="disabled"').'><a class="paging" data-value="'.($pageCount!=$currentPage?$currentPage+1:'').'" href="javascript:void(0)" aria-label="Next"><span aria-hidden="true">&rsaquo;</span></a></li>
				<li '.($pageCount!=$currentPage?'':'class="disabled"').'><a class="paging" data-value="'.($pageCount!=$currentPage?$pageCount:'').'" href="javascript:void(0)" aria-label="Last"><span aria-hidden="true">&raquo;</span></a></li>';
				?>
				</ul>
			</div>
			<?php
		}
	}

}
