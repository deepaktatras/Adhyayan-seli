var app = angular.module('app', ['ngTouch', 'ui.grid','ui.grid.selection','ui.grid.exporter','ui.grid.resizeColumns']);
 

app.controller('MainCtrl', ['$scope', function ($scope) {
	$scope.gridOptions = {
			enableColumnResizing: true,
			/*enableFiltering :true,*/			
			enableGridMenu: true,
		    enableSelectAll: true,
		    enableSorting: true,
		    exporterCsvFilename: 'Dashboard-data.csv',
		    exporterMenuPdf: false,
		   /* exporterPdfDefaultStyle: {fontSize: 9},
		    exporterPdfTableStyle: {margin: [30, 30, 30, 30]},
		    exporterPdfTableHeaderStyle: {fontSize: 10, bold: true, italics: true, color: 'red'},		  
		    exporterPdfOrientation: 'portrait',
		    exporterPdfPageSize: 'LETTER',
		    exporterPdfMaxGridWidth: 500,*/
		    exporterCsvLinkElement: angular.element(document.querySelectorAll(".custom-csv-link-location")),
		    onRegisterApi: function(gridApi){
		      $scope.gridApi = gridApi;
		    }
		
			
	};
  $scope.gridOptions.data = localStorage.admindata;
  $scope.myColumnDefs = function(){
	  var cols = Object.keys(JSON.parse( $scope.gridOptions.data)[0]);
	  var k= 0;	
	  var numElements = cols.length;
	  var colList=[];
	  var i =0;		
	  var pivotTitle = 'pivotRow';
	  for(k=0;k<numElements;k++){
		  if((cols[k])==pivotTitle){			 			  
			  cols.splice(k,1);
			  cols.splice(0,0,pivotTitle);
			  break;
		  }
	  }
	  k=0;
	  for(k=0;k<numElements;k++){
		  colList.push({field:cols[k]/*,width:100+3*(cols[k]).length*/})
	  }
	  $scope.gridOptions.columnDefs=colList;
  }
  $scope.myColumnDefs();
}]);