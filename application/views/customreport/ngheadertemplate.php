<div class="ngRow ngHeaderText" ng-style="{height: headerRowHeight / 2}" style="text-align:center">I'm other header row</div>
<div ng-style="{ height: col.headerRowHeight / 2, top: col.headerRowHeight / 2 }" ng-repeat="col in renderedColumns" ng-class="col.colIndex()" class="ngHeaderCell">
	<div class="ngVerticalBar" ng-style="{height: col.headerRowHeight / 2}" ng-class="{ ngVerticalBarVisible: !$last }">&nbsp;</div>
	<div ng-header-cell></div>
</div>