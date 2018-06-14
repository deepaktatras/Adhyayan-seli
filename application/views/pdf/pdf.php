<?php
ini_set("memory_limit","256M");
//echo "<pre>";print_r($data['data']);die;
ob_start();
//print_r($data['data'][0]);
$html ='';
echo '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Report Card </title>
<style type="text/css">
@page {
  size: auto; 
  footer: html_myFooter1; 
  header: html_myHeader1;	
}
@page :first {
 footer: html_myFooter2; 
 header: html_myHeader2;
 resetpagenum: 1; 
}
body {
    margin: 0;
    padding: 0;
}
table{
	border-collapse:collapse;
}		
#page-1 {
    margin-top: 0;
}
#reportContainer {
    font-family: CalibriRegular;
}
.pageContainer {
 /*   margin: 20px auto 0;*/
	position: relative;
	background-color: #fff;
}
.header-img img {
    height: 100%;
    width: auto;
}
.header-img {
    text-align: right;
}

.page-footer {
    /*bottom: 0;
    position: absolute;*/
    text-align: center;
    width: 100%;
	font-size: 11px;
}
#reportContainer {
    background-color: #666;
}
.section_head, body .the-block td.brownHead {
    background-color: #6c0d10;
    color: #fff;
    font-family: CalibriBold;
    font-size: 18px;
    line-height: 38px;
	margin-bottom:15px;
	padding:0;
    text-align: center;
}
body .greyHead, body .barGraph .head-row {
    background-color: #c8c8c8;
    font-family: CalibriBold;
    font-size: 16px;
    line-height: 35px;
	color: #000;
    text-align: center;
}
.onlyGreyHead{
	background-color: #c8c8c8;
	font-family: CalibriBold;
    font-size: 16px;
    line-height: 35px;
	color: #000;
    text-align: center;
	width:100%;
	margin-bottom: 18px;
}
.page-inner .the-block:last-child {
    margin-bottom: 0 !important;
}
.page-cover .header-img{
	text-align:center;
}
.page-num{
    color: #ccc;
	font-size: 13px;
    text-align: center;
}

.bordered td {
    border-right: 1px solid #1d1b1b;
    border-top: 1px solid #1d1b1b;
}
.bordered {
    border-bottom: 1px solid #1d1b1b;
    border-left: 1px solid #1d1b1b;
	margin-bottom: 8px;
	width: 100%;
	text-align: center;
}
.pieGraph {
    
	margin-bottom: 8px;
	width: 100%;
	text-align: center;
        height:100%;
}
.bordered .tcClass{
	text-align:left;
	padding-left:10px;
}		
.borderedGrp td {
    border-left: 1px solid #1d1b1b;
    border-top: 1px solid #1d1b1b;
}
.borderedGrp {
    border-bottom: 1px solid #1d1b1b;
    border-left: 1px solid #1d1b1b;
	margin-bottom: 0px;
	width: 100%;
	text-align: center;
}
body .kpaStyle .body-row  td.colNo-0 {
    width: 25.6%;
	text-align: left;
	font-size: 11px;
	padding: 0px 3px;
    line-height: 18px;
}
.colSize-1 {
    width: 8.4%;
}
.colSize-3 {
    width: 25.2%;
}
.assessornoteTbl{text-align: left;padding: 5px 5px 5px 5px;}
.assessornoteTbl ul{margin-left:100px;}		
.kpaStyle {
    font-family: CalibriRegular;
    font-size: 12px;
}
.kpaStyle .body-row td {
    padding: 0px;
	font-size:11px;
}
.kpaStyle .head-row td {
    font-family: CalibriRegular;
    font-size: 16px;
	padding-left: 5px;
    padding-right: 5px;
}
.kpaStyle .body-row .cQn {
    display: inline-block;
    font-size: 12px;
    line-height: 17px;
	padding: 0 7px;
    text-align: left;
}
.kpaStyle .body-row.rowNo-0 .colNo-1, .kpaStyle .body-row.rowNo-0 .colNo-2, .kpaStyle .body-row.rowNo-0 .colNo-3 {
    vertical-align: top;
}
.reportIndex .body-row .colNo-1 {
    text-align: left;
}
.reportIndex .body-row td {
    font-family: CalibriRegular;
    padding: 5px 5px 5px 10px;
}
.kpablock td {
    font-family: CalibriRegular;
    font-size: 13px;
    line-height: 18px;
    padding: 4px 0 4px 20px;
    text-align: left;
    width: 50%;
}
		
.pull-left{
	float:left;
}
.kpablock td span {
    padding-left: 15px;
}
.score-1{
	color:red;
}
.score-2{
	color:#e8bf19;
}
.score-3{
	color:green;
}
.score-4{
	color:#0070c0;
}
.scheme-1-score-1{
	color:red;
}
.scheme-1-score-2{
	color:#e8bf19;
}
.scheme-1-score-3{
	color:green;
}
.scheme-1-score-4{
	color:#0070c0;
}
#reportTitle {
    background-color: #c8c8c8;
    color: #474343;
    font-family: CalibriRegular;
    font-size: 20px;
    text-align: center;
}
.bigBold{
    font-size: 19px;
    font-weight: bold;
	margin-top:5px;
}
.reportInfo{
	font-size: 16px;
	line-height:50px;
	margin-bottom: 18px;
}
.coverInfoBlock {
    width: 100%;
	text-align: center;
	font-family: CalibriRegular,Verdana,sans-serif;
}
.blueColor{
	color: #1f07f7 !important;
}
.redColor{
	color: #ff0000 !important;
}
.textBold{
    font-weight: bold;
}
body .the-block.awardBlock td {
    text-align: left;
	color: #3e3b39;
	font-family: CalibriRegular;
	padding: 5px 5px 5px 10px;
}
body .the-block.awardBlock {
    margin-bottom: 18px;
	text-align:left;
}
.coverAddress{
	color: #511313;
    font-family: CalibriRegular;
    font-size: 9pt;
    font-weight: bold;
    line-height: 20px;
    text-align: center;
    position: absolute;
    bottom: 0;
    right: 0;
    width: 827px;
}
.comparisonBlock .head-row td {
    font-family: CalibriBold;
    font-size: 14px;
    line-height: 20px;
    padding:2px;
}
.comparisonBlock  td {
    font-size: 15px;
	font-family: CalibriRegular;
}
.comparisonBlock.the-block.bordered {
    margin-bottom: 30px;
}
.barGraph.the-block.bordered, .barGraph.the-block.bordered td {
    border-color: #c8c8c8;
}
.stepDesc,.barDesc{
	float:left;
	width:100px;
	position: relative;
}
.theBarGraph{
	float:left;
	width:70%;
}
.theBarGraphTbl {
    width: 100%;	
}
.graph-bar1 {
    float: left;
}
.graph-top-image0 {
    width: 27px;
    height: 9px;
    background: url("./public/images/reports/graph-top2.png") repeat scroll 0 0;
}
.graph-top-image1 {
    width: 27px;
    height: 9px;
    background: url("./public/images/reports/graph-top.png") repeat scroll 0 0;
}
.graph-rep0 {
    width: 27px;
    background: url("./public/images/reports/graph-rep.png") repeat scroll 0 0;
}
.graph-rep1 {
    width: 27px;
    background: url("./public/images/reports/graph-rep3.png") repeat scroll 0 0;
}
.gScore-0{
	display:none;
}
.barStyle-0{
	position:relative;
	min-height: 9px;
	background-color: #BF3733;
}
.barStyle-1  {
    min-height: 9px;
	position:relative;
	background-color: #d68e88;
}
.theBarGraphTbl td {
    border: 0 none !important;
    margin: 0 !important;
    padding: 0 !important;
    vertical-align: bottom;
}
.graphSteps {
    font-family: CalibriRegular;
    font-size: 13px;
    line-height: 18px;
    margin-left: 10%;   
    text-align: left;
    width: 75%;
}
.barStyle-0 .barHead {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-top-mid.png") repeat scroll 0 0;
	position:absolute;
	top:0;
	width: 100%;
    z-index: 999;
}
.barStyle-0 .barHeadRight {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-top-right.png") no-repeat scroll right 0;
    height: 9px;
}
.barStyle-0 .barHeadLeft {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-top-left.png") no-repeat scroll left 0;
}
.barStyle-0 .barBody {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-rep-mid.png") repeat scroll 0 0;
}
.barStyle-0 .barBodyRight {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-rep-right.png") repeat-y scroll right 0;
}
.barStyle-0 .barBodyLeft {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-rep-left.png") repeat-y scroll left 0;
}
.barStyle-1 .barHead {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-top2-mid.png") repeat scroll 0 0;
	position:absolute;
	top:0;
	width: 100%;
    z-index: 999;
}
.barStyle-1 .barHeadRight {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-top2-right.png") no-repeat scroll right 0;
    height: 9px;
}
.barStyle-1 .barHeadLeft {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-top2-left.png") no-repeat scroll left 0;
}
.barStyle-1 .barBody {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-rep2-mid.png") repeat scroll 0 0;
}
.barStyle-1 .barBodyRight {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-rep2-right.png") repeat-y scroll right 0;
}
.barStyle-1 .barBodyLeft {
    background: rgba(0, 0, 0, 0) url("./public/images/reports/graph-rep2-left.png") repeat-y scroll left 0;
}
.barDesc-0 {
    background:  url("./public/images/reports/ssre.png") no-repeat scroll 0 center;
}
.barDesc-1 {
    background:  url("./public/images/reports/sere.png") no-repeat scroll 0 center;
}
.barDescItem {
    color: #1051a6;
    font-family: CalibriRegular;
    font-size: 12px;
    text-align: left;
	margin-left: 25%;
	padding-left: 15px;
}
.infoBelowGraph{
	color: #1f6ab5;
    font-size: 12px;
    font-weight: bold;
	font-family: CalibriRegular;
	text-align:center;
	margin-top: 5px;
}
.infoOnYAxis {
    font-family: CalibriRegular;
    font-size: 11px;
    letter-spacing: 12px;
    position: absolute;
	padding-top: 55%;
    right: 0;
    transform: rotate(270deg);
}
.keyInfoBlock .colNo-0 {
    width: 14%;
}
.keyInfoBlock td {
    color: #3e3b39;
    font-family: CalibriRegular;
    font-size: 14px;
    padding: 5px 5px 5px 10px;
    text-align: left !important;
}
body  #reportContainer.loadingData{
	background:url("./public/images/reports/loading.gif") no-repeat center center #fff;
	min-height:500px;
}
.awardBlock .colNo-1 {
    min-width: 50%;
}
.the-block.keyQblock {
    border-collapse: separate;
    border-spacing: 15px 10px;
	text-align:center;
	margin-bottom: 40px;
	font-size: 16px;
	font-family:CalibriRegular;
}
.keyQblock td {
    border: 1px solid #000;
}
.keyQblock .rowNo-0 .colNo-0, .keyQblock .rowNo-1 .colNo-0 {
    font-weight: bold;
    padding: 10px;
    /*white-space: nowrap;*/
	width:21%;
}
.keyQblock .rowNo-0 .colNo-1 {
    border-color: transparent;
    background: rgba(0, 0, 0, 0) url("./public/images/reports/dashed.png") no-repeat scroll center bottom;
}
.keyQblock .rowNo-2 td, .keyQblock .rowNo-3 td, .keyQblock .rowNo-4 td, .keyQblock .rowNo-5 td {
    border: 2px solid grey;
	padding-bottom: 4px;
    padding-top: 4px;
}
.keyQblock .rowNo-1 .colNo-1, .keyQblock .rowNo-1 .colNo-2, .keyQblock .rowNo-1 .colNo-3 {
    border-width: 2px;
	width: 25%;
}
.min-h{
	min-height:118px;
	display:block;
}
.score-bg-4 {
    background-color: #2e5ce6 !important;
}
.score-bg-3 {
    background-color: #47b247 !important;
}
.score-bg-2 {
    background-color: #e8bf19 !important;
}
.score-bg-1 {
    background-color: red !important;
}
.scoreB-4{
	border-color:#2e5ce6 !important;
}
.scoreB-3{
	border-color:#47b247 !important;
}
.scoreB-2{
	border-color:#e8bf19 !important;
}
.scoreB-1{
	border-color:red !important;
}
.the-block.onlytext {
    font-family: CalibriRegular;
    font-size: 14px;
    line-height: 15px;
    margin-bottom: 20px;
}
.keysForRecmBlock .head-row td {
    background-color: #e8e1e1;
    font-weight: bold;
    padding-bottom: 5px;
    padding-top: 5px;
}
.keysForRecmBlock .body-row .colNo-0 {
    width: 7%;
}
.keysForRecmBlock.the-block {
    font-family: CalibriRegular;
	line-height: 24px;
    font-size: 14px;
}
.keysForRecmBlock .body-row .colNo-1 {
    width: 24%;
}
.keysForRecmBlock .body-row .colNo-2 {
    padding-left: 5px;
    padding-right: 5px;
    text-align: left;
}
.comparisonBlock.lineHeightSm .head-row td {
    font-family: CalibriRegular;
    font-weight: bold;
    line-height: 16px;
}
.comparisonBlock.lineHeightSm .colNo-0 {
    padding-left: 10px;
    text-align: left;
}
.comparisonBlock.lineHeightSm td {
    font-size: 14px;
}
.barNameCol {
    color: #273497;
    float: left;
    font-family: CalibriRegular;
    font-size: 12px;
    line-height: 14px;
    text-align: center;
}
.clear {
    clear: both;
    height: 0;
}
.mb0{
	margin-bottom:0 !important;
}
.the-block.textBlock {
    font-family: CalibriRegular;
    font-size: 14px;
	margin-bottom: 10px;
    line-height: 15px;
	width: 100%;
}
.the-block.textBlock td {
    padding: 5px;
}
.the-block.textBlock a {
    color: #000;
}
.keyNotesBlock .head-row {
    background-color: #f7f4f4;
    font-weight: bold;
    line-height: 30px;
}
.the-block.keyNotesBlock {
    font-family: CalibriRegular;
    font-size: 14px;
	text-align: left;
	margin-bottom: 15px;
}
.keyNotesBlock .colNo-0 {
    width: 35px;
}
.keyNotesBlock td {
    padding-left: 10px;
    padding-right: 10px;
}
.italic {
    font-style: italic;
}
.text-center {
    text-align: center !important;
}
.text-bold{
	font-weight:bold;
}
.text-right{
	text-align:right;
}
.the-block ol {
    margin: 0;
}
.border-outer {
    border: 1px solid #000;
}
.qualityDefStand .body-row td {
    font-family: CalibriRegular;
    font-size: 14px;
    line-height: 26px;
    padding: 0 5px 0 10px;
}
.the-block.qualityDefStand {
    margin-left: auto;
    margin-right: auto;
    width: 80%;
}
.qualityDefStand .rowNo-0 .colNo-1 {
    padding-top: 10px;
}
.qualityDefStand .rowNo-3 .colNo-1 {
    padding-bottom: 10px;
}
.qualityDefDonBos .head-td {
    background-color: rgb(192,0,0);
    color: #fff;
    font-weight: bold;
}
.the-block.qualityDefDonBos {
    font-family: calibriRegular;
    font-size: 14px;
    line-height: 15px;
    margin-left: auto;
    margin-right: auto;
    width: 80%;
	text-align:center;
}
.qualityDefDonBos .colNo-0 {
    text-align: left;
}
.qualityDefDonBos .body-row td {
    padding: 10px;
}
.recomCoverBlock {
    background-color: #800000;
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    line-height: 1em;
    margin: 40px auto 0;
    padding: 43px 20px 40px;
    width: 80%;
}
.recomCoverBlock .bigText{
	font-size:26px;
}
.recomCoverBlock .mediumText{
	font-size:19px;
}
.recomCoverBlock .mBigText{
	font-size:23px;
	line-height:1em;
}
.the-block.aqsTeam {
    font-family: CalibriRegular;
    font-size: 14px;
    line-height: 15px;
	text-align: center;
	width:100%;
}
.the-block.aqsTeam table td {
    padding: 2px 2px 2px 10px;
    text-align: left;
}
.the-block.aqsTeam  td {
    padding: 20px;
	vertical-align:top;
}
.the-block.aqsTeam table thead td {
    font-weight: bold;
    line-height: 30px;
}
.team-head {
    padding-bottom: 15px;
    text-align: left;
}
.the-block.aqsTeam table {
    margin-bottom: 0;
	min-width: 90%;
}
.the-block.recomIndex {
    margin-bottom: 15px;
}
.the-block.rTextblock {
    font-family: CalibriRegular;
    font-size: 14px;
    line-height: 15px;
	margin-top: 20px;
}
.recmText {
    padding-left: 20px;
}
.color-red {
    color: red;
}
.color-yellow {
    color: yellow;
}
.color-green {
    color: green;
}
.rTextblock .body-row.rowNo-0 .colNo-0 {
    padding-top: 20px;
}
.the-block.rTextblock:first-child {
    margin-top: 0;
}
.emptyBar div {
    display: none;
}

#ajaxLoading {
    background: rgba(255, 255, 255, 0.7) url("./public/images/loading.gif") no-repeat scroll center center;
    display: none;
    height: 100%;
    left: 0;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 9999999;
}
.haveQuickLinks #quickLinks {
	display:block;
}
#quickLinks {
    background-color:#ffffff;
	font-family:Helvetica, Arial, sans-serif;
	font-size:14px;
	-webkit-box-shadow:0 5px 6px rgba(0,0,0,0.3);
	-moz-box-shadow:0 5px 6px rgba(0,0,0,0.3);
	box-shadow:0 5px 6px rgba(0,0,0,0.3);
    padding: 10px 0;
    position: fixed;
    text-align: center;
    top: 0;
    width: 100%;
    z-index: 9999;
	display:none;
}
#quickLinks ul {
    list-style: outside none none;
	margin: 0;
}
#quickLinks li {
    display: inline-block;
}
#quickLinks ul a {
	margin:0 15px;
	text-decoration:none;
	background-color:#6C0D10;
	color:#ffffff;
	padding:4px 10px;
	display:inline-block;
	-webkit-border-radius:4px;
	-moz-border-radius:4px;
	border-radius:4px;
}
#quickLinks .closeIt {
    color: #6C0D10;
    position: absolute;
    right: 20px;
	margin-top: -2px;
    text-decoration: none;
}
.pRel{
	position:relative;
}
body.haveQuickLinks {
    padding-top: 40px;
}
.pieLbColor {
    float: left;
    height: 13px;
    margin-right: 5px;
	opacity: 0.9;
    width: 13px;
}
.pieLb {
    top: 208px;
    width: 40%;
    margin-top: 10px;
    margin-left: 204px;
    position: absolute;
    left: 420px;
}
.pieLbText {
    font-size: 13px;
    line-height: 1em;
    margin-left: 18px;
}
.pieLbRow {
    margin-bottom: 10px;
}
body .redHead {
    background-color: #c23a37;
    padding: 5px 15px;
	color: #fff;
}
body .fs-sm{
	font-size: 13px;
    line-height: 15px;
	text-align:center;
	padding-right: 20px;
}
body .fs-sm.gBdr
{
	border-bottom: solid 4px #000000;
}
.bordered.mb25{
	margin-bottom:25px;
}
.bordered.mt20{
	margin-top:20px;
}
.firstColBold .colNo-0 {
    font-weight: bold;
}
.col-4 td {
    min-width: 25%;
}

body .the-block td.seqHead {
    line-height: 1.1em;
    padding: 5px;
}
.mb15{
	margin-bottom:15px;
}
.grHead {
    font-size: 14px;
}
.lcLabels {
    float: right;
    font-size: 12px;
    line-height: 15px;
    margin-top: 60px;
	margin-right: 10px;
}
.lcLine {
    float: left;
    height: 2px;
    margin-right: 5px;
    margin-top: 6px;
    width: 25px;
}
.lcText {
    float: left;
}
.grBox {
    font-size: 14px;
}
body table.the-block .fontNormal{
	font-family: CalibriRegular;
}
body table.the-block .fs-16{
	font-size: 16px;
}
body table.the-block .fs-14{
	font-size: 14px;
}

@media print {
    body .pageContainer {
		
		margin:0 auto;
		/*height:1200px !important;*/
	}
	#reportContainer {
		background-color: #fff;
	}
	.section_head, body .the-block td.brownHead{
		margin-bottom:10px;
	}
	.barStyle-0 .barHead,.barStyle-0 .barHeadRight,.barStyle-0 .barHeadLeft,.barStyle-0 .barBody,.barStyle-0 .barBodyRight,.barStyle-0 .barBodyLeft,.barStyle-1 .barHead,.barStyle-1 .barHeadRight ,.barStyle-1 .barHeadLeft,.barStyle-1 .barBody,.barStyle-1 .barBodyRight,.barStyle-1 .barBodyLeft{
		background:none;
	}
	body.haveQuickLinks #quickLinks{
		display:none;
	}
	body.haveQuickLinks {
		padding-top: 0;
	}
}
.noborder{
	border:0 !important;
	padding:0 !important;
}
.tchrAwardInfoBlock td {
	font-size:13px !important;
	text-align:left;
	padding-left:2px;
	padding-top:2px;	
}		
.tchrAwardInfoBlock .head-row td {
    background-color: #e8e1e1;
    font-weight: bold;
    padding-bottom: 5px;
    padding-top: 5px;    
}
.ul-style{
   list-style:none;
}
.perTable { font-family: DejaVuSansCondensed; font-size: 9pt; line-height: 1.2;
 margin-top: 2pt; margin-bottom: 5pt; text-align: center; }
.perTable td, .perTable th { padding: 5pt; text-align: center; }
table.pdfHdr{width:100%;border-collapse:collapse;border-spacing:0;}.pdfHdr .halfSec{width:50%;vertical-align:middle;}.pdfHdr .halfSec.fl{text-align:left;}.pdfHdr .halfSec.fr{text-align:right;}.pdfHdr.broad .halfSec{padding:0px 0px;}.pdfHdr.thin .halfSec{padding:0px 0px;}

.pdfHdr .thirdhSec{width:60%;vertical-align:middle;}.pdfHdr .thirdSec{width:20%;vertical-align:middle;}.pdfHdr .thirdSec.fl{text-align:left;}.pdfHdr .thirdSec.fr{text-align:right;}.pdfHdr.broad .thirdSec{padding:0px 0px;}.pdfHdr.thin .thirdSec{padding:0px 0px;}
.pdfHdr .hSec{width:33%;vertical-align:middle;}.pdfHdr .hSec.fl{text-align:left;}.pdfHdr .hSec.fr{text-align:right;}.pdfHdr.broad .hSec{padding:5px 30px;}.pdfHdr.thin .hSec{padding:5px 20px;}
table.pdfFtr{width:100%;border-collapse:collapse;border-spacing:0;}.pdfFtr .halfSec{width:48.5%;vertical-align:middle;}.pdfFtr .halfSec.fl{text-align:center;}.pdfFtr .halfSec.fr{text-align:center;}.pdfFtr.broad .halfSec{padding:20px 30px;}.pdfFtr.thin .halfSec{padding:15px 20px;}
.antHead{font-weight: bold;color:#76AD1B}.adhHead{font-weight: bold;color:#C70039}
.scheme-2-score-1{color:#d12200;}.scheme-2-score-2{color:#ce7230;}.scheme-2-score-3{color:#D0B122;}.scheme-2-score-4{color:#5e9900;}.scheme-2-score-5{color:#307ACE;}.scheme-2-score-6{color:#FFFFFF;}
.scheme-2-score-1-bg{background-color:#d12200;}.scheme-2-score-2-bg{background-color:#ce7230;}.scheme-2-score-3-bg{background-color:#D0B122;}.scheme-2-score-4-bg{background-color:#5e9900;}.scheme-2-score-5-bg{background-color:#307ACE;}
.scheme-2.score-1{color:#d12200;}.scheme-2.score-2{color:#ce7230;}.scheme-2.score-3{color:#D0B122;}.scheme-2.score-4{color:#5e9900;}.scheme-2.score-5{color:#307ACE;}.scheme-2.score-6{color:#FFFFFF;}
.blankBar_green{height:12px;margin-bottom:4px;background-color:#00b050;}.blankBar_yellow{height:12px;margin-bottom:4px;background-color:#ffc000;}
#total_wrapper .barGraph.the-block.bordered,#total_wrapper .barGraph.the-block.bordered td{border:0;}
</style>
</head>
<body>';
if($data['config']['isChildProt']==1){
	echo '<htmlpageheader name="myHeader1" style="display:none">
<table class="pdfHdr thin"><tr><td class="halfSec fl" align="left"><a href=""><img src="'.$data['config']['headerImgAdh'].'" alt="" style="height:50px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'.$data['config']['childProtImg'].'" alt=""></a></td></tr> </table>
<table style="border-bottom:solid 5px #6c0d10;width:100%"><tr style="border-bottom:solid 5px #6c0d10;width:100%"><td style="border-bottom:solid 5px #6c0d10;width:100%">&nbsp;</td></tr></table>
</htmlpageheader>
<htmlpageheader name="myHeader2" style="display:none">
<table class="pdfHdr broad"><tr><td class="halfSec fl" align="left"><a href=""><img src="'.$data['config']['headerImgAdh'].'" alt="" style="height:57px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'.$data['config']['childProtImg'].'"  alt=""></a></td></tr>
<tr><td colspan="2" style="font-weight:bold;text-align:center;">The Teacher Performance Review has been initiated and validated by '.($data['config']['schoolName']).'</td></tr>
<tr style="border-bottom:solid 5px #6c0d10;width:100%"><td colspan="2" style="border-bottom:solid 5px #6c0d10;width:100%"></td></tr>		
</table>		
</htmlpageheader>
<htmlpagefooter name="myFooter2" style="display:none">
<div class="page-footer"><div class="coverAddress">'.$data['config']['coverAddress'].'</div>
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
<htmlpagefooter name="myFooter1" style="display:none">
<div class="page-footer">
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
';
}else if($data['config']['isShishuvanTeacherReview']==1){
	echo '<htmlpageheader name="myHeader1" style="display:none">
<table class="pdfHdr thin"><tr><td class="halfSec fl" align="left"><a href=""><img src="'.$data['config']['headerImgAdh'].'" alt="" style="height:50px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'.$data['config']['isShishuvanTeacherImg'].'" alt=""></a></td></tr> </table>
<table style="border-bottom:solid 5px #6c0d10;width:100%"><tr style="border-bottom:solid 5px #6c0d10;width:100%"><td style="border-bottom:solid 5px #6c0d10;width:100%">&nbsp;</td></tr></table>
</htmlpageheader>
<htmlpageheader name="myHeader2" style="display:none">
<table class="pdfHdr broad"><tr><td class="halfSec fl" align="left"><a href=""><img src="'.$data['config']['headerImgAdh'].'" alt="" style="height:57px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'.$data['config']['isShishuvanTeacherImg'].'"  alt=""></a></td></tr>
<tr><td colspan="2" style="font-weight:bold;text-align:center;">The Teacher Performance Review has been initiated and validated by '.($data['config']['schoolName']).'</td></tr>
<tr style="border-bottom:solid 5px #6c0d10;width:100%"><td colspan="2" style="border-bottom:solid 5px #6c0d10;width:100%"></td></tr>		
</table>		
</htmlpageheader>
<htmlpagefooter name="myFooter2" style="display:none">
<div class="page-footer"><div class="coverAddress">'.$data['config']['coverAddress'].'</div>
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
<htmlpagefooter name="myFooter1" style="display:none">
<div class="page-footer">
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
';
}else if($data['config']['isDominicSavioTeacherReview']==1){
	echo '<htmlpageheader name="myHeader1" style="display:none">
<table class="pdfHdr thin" style="padding:8px;"><tr><td class="halfSec fl" align="left"><a href=""><img src="'.$data['config']['headerImgAdh'].'" alt="" style="height:50px;"></a></td><td class="halfSec fr" align="right"><a href=""><img height="60px;" src="'.$data['config']['isDominicSavioTeacherImg'].'" alt=""></a></td></tr> </table>
<table style="border-bottom:solid 5px #6c0d10;width:100%"><tr style="border-bottom:solid 5px #6c0d10;width:100%"><td style="border-bottom:solid 5px #6c0d10;width:100%">&nbsp;</td></tr></table>
</htmlpageheader>
<htmlpageheader name="myHeader2" style="display:none">
<table class="pdfHdr broad" style="padding:8px;"><tr><td class="halfSec fl" align="left"><a href=""><img src="'.$data['config']['headerImgAdh'].'" alt="" style="height:57px;"></a></td><td class="halfSec fr" align="right"><a href=""><img  height="80px;" src="'.$data['config']['isDominicSavioTeacherImg'].'"  alt=""></a></td></tr>
<tr><td colspan="2" style="font-weight:bold;text-align:center;">The Teacher Performance Review has been initiated and validated by '.($data['config']['schoolName']).'</td></tr>
<tr style="border-bottom:solid 5px #6c0d10;width:100%"><td colspan="2" style="border-bottom:solid 5px #6c0d10;width:100%"></td></tr>		
</table>		
</htmlpageheader>
<htmlpagefooter name="myFooter2" style="display:none">
<div class="page-footer"><div class="coverAddress">'.$data['config']['coverAddress'].'</div>
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
<htmlpagefooter name="myFooter1" style="display:none">
<div class="page-footer">
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
';
}else if($data['config']['isStudentReview']==1){
	echo '<htmlpageheader name="myHeader1" style="display:none">
<table class="pdfHdr broad"><tr><td class="hSec fl" align="left"><a href=""><img src="'.$data['config']['headerStudentImgAdh'].'" alt="" style="height:100px;"></a></td><td class="hSec fr" align="right"><a href=""><img src="'.$data['config']['isStudentReviewImg'].'" alt=""></a></td></tr>  
</table><div class="blankBar_green"></div><div class="blankBar_yellow"></div>
</htmlpageheader>
<htmlpageheader name="myHeader2" style="display:none">
<table class="pdfHdr broad"><tr><td class="thirdSec fl" align="left"><a href=""><img src="'.$data['config']['headerStudentImgAdh'].'" alt="" style="height:100px;"></a></td><td style="text-align:center;color:#00b050;font-size:22px;font-weight:bold;"  class="thirdhSec">Career Readiness for Individuals</td><td class="thirdSec fr" align="right"><a href=""><img src="'.$data['config']['isStudentReviewImg'].'"  alt=""></a></td></tr>		
</table><div class="blankBar_green"></div><div class="blankBar_yellow"></div>		
</htmlpageheader>
<htmlpagefooter name="myFooter2" style="display:none">
<div class="page-footer"><div class="coverAddress"><table border="0"  class="pdfFtr broad"> <tr><td class="halfSec fl" align="center">'.$data['config']['coverAddressAntarang'].'</td><td class="halfSec fr" align="center">'.$data['config']['coverAddressAdhyayanFoundation'].'</td></tr></table></div>
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
<htmlpagefooter name="myFooter1" style="display:none">
<div class="page-footer">
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
';
}else if($data['config']['iscollegeReview']==1){
	echo '<htmlpageheader name="myHeader1" style="display:none">
<table class="pdfHdr broad"><tr><td class="hSec fl" align="left"><a href=""><img src="'.$data['config']['headerCollegeImgAdh'].'" alt="" style="height:100px;"></a></td><td class="hSec fr" align="right"><a href=""><img src="'.$data['config']['isCollegeReviewImg'].'" alt=""></a></td></tr>  
</table><div class="blankBar_green"></div><div class="blankBar_yellow"></div>
</htmlpageheader>
<htmlpageheader name="myHeader2" style="display:none">
<table class="pdfHdr broad"><tr><td class="thirdSec fl" align="left"><a href=""><img src="'.$data['config']['headerCollegeImgAdh'].'" alt="" style="height:100px;"></a></td><td style="text-align:center;color:#00b050;font-size:22px;font-weight:bold;"  class="thirdhSec">Career Readiness Review for Colleges</td><td class="thirdSec fr" align="right"><a href=""><img src="'.$data['config']['isCollegeReviewImg'].'"  alt=""></a></td></tr>		
</table><div class="blankBar_green"></div><div class="blankBar_yellow"></div>		
</htmlpageheader>
<htmlpagefooter name="myFooter2" style="display:none">
<div class="page-footer"><div class="coverAddress"><table border="0"  class="pdfFtr broad"> <tr><td class="halfSec fl" align="center">'.$data['config']['coverAddressAntarang'].'</td><td class="halfSec fr" align="center">'.$data['config']['coverAddressAdhyayanFoundation'].'</td></tr></table></div>
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
<htmlpagefooter name="myFooter1" style="display:none">
<div class="page-footer">
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
';
}
else if($data['config']['isChangeMaker']){	
echo '<htmlpageheader name="myHeader1" style="display:none">
<table class="pdfHdr thin"><tr><td class="halfSec fl" align="left"><a href=""><img src="'.$data['config']['headerImgAdh'].'" alt="" style="height:52px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'.$data['config']['headerImgChsmall'].'" alt=""></a></td></tr> </table>
<table style="border-bottom:solid 5px #6c0d10;width:100%"><tr style="border-bottom:solid 5px #6c0d10;width:100%"><td style="border-bottom:solid 5px #6c0d10;width:100%">&nbsp;</td></tr></table>
</htmlpageheader>		
<htmlpageheader name="myHeader2" style="display:none">
<table class="pdfHdr broad"><tr><td class="halfSec fl" align="left"><a href=""><img src="'.$data['config']['headerImgAdh'].'" alt="" style="height:80px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'.$data['config']['headerImgCh'].'" style="height:75px;" alt=""></a></td></tr></table>
<table style="border-bottom:solid 5px #6c0d10;width:100%"><tr style="border-bottom:solid 5px #6c0d10;width:100%"><td style="border-bottom:solid 5px #6c0d10;width:100%">&nbsp;</td></tr></table>
</htmlpageheader>		
<htmlpagefooter name="myFooter2" style="display:none">
<div class="page-footer"><div class="coverAddress">'.$data['config']['coverAddress'].'</div>
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
<htmlpagefooter name="myFooter1" style="display:none">
<div class="page-footer">
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
';
}
else if($data['config']['isCoBranded']){
	echo '<htmlpageheader name="myHeader1" style="display:none">
<div class="page-header page-cover" style="height:60px;background-color:'.$data['config']['headerBG'].'"><div style="padding-top:10px;padding-bottom:10px;padding-right:'.$data['config']['pageLeftRightPadding'].'px;height:60px;" class="header-img"><img src="'.$data['config']['headerImg'].'" /></div>	</div>
</htmlpageheader>
<htmlpageheader name="myHeader2" style="display:none">
<table class="pdfHdr broad"><tr><td class="halfSec fl" align="left"><a href=""><img src="'.$data['config']['headerImgAdh'].'" alt="" style="height:80px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'.$data['config']['coBrandedImg'].'" style="height:80px;" alt=""></a></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" style="text-align:center;font-weight:bold;font-size:12px;font-family: CalibriRegular,Verdana,sans-serif;" align="center">Don Bosco School Self-Review and Evaluation Programme (DBSSRE) conducted under the aegis of \'Don Bosco for Excellence\'</td></tr>
<tr style="border-bottom:solid 5px #6c0d10;width:100%"><td colspan="2" style="border-bottom:solid 5px #6c0d10;width:100%"></td></tr>		
</table>		
</htmlpageheader>
<htmlpagefooter name="myFooter2" style="display:none">
<div class="page-footer"><div class="coverAddress">'.$data['config']['coverAddress'].'</div>
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
<htmlpagefooter name="myFooter1" style="display:none">
<div class="page-footer">
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
';
}
else	
echo '<htmlpageheader name="myHeader1" style="display:none">
<div class="page-header page-cover" style="height:60px;background-color:'.$data['config']['headerBG'].'"><div style="padding-top:10px;padding-bottom:10px;padding-right:'.$data['config']['pageLeftRightPadding'].'px;height:60px;" class="header-img"><img src="'.$data['config']['headerImg'].'" /></div></div>
</htmlpageheader>
<htmlpageheader name="myHeader2" style="display:none">
<div class="page-header page-cover" style="height:60px;background-color:'.$data['config']['headerBG'].'"><div style="padding-top:10px;padding-bottom:10px;padding-right:'.$data['config']['pageLeftRightPadding'].'px;height:60px;" class="header-img"><img src="'.$data['config']['headerImg'].'" /></div></div><div id="reportTitle">'.$data['config']['reportTitle'].'</div>
</htmlpageheader>	
<htmlpagefooter name="myFooter2" style="display:none">
<div class="page-footer"><div class="coverAddress">'.$data['config']['coverAddress'].'</div>
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
<htmlpagefooter name="myFooter1" style="display:none">
<div class="page-footer">
<div style="line-height:'.$data['config']['pageNoBarHeight'].'px;" class="page-num">Page {PAGENO}</div><div class="page-footer-inner" style="line-height:'.$data['config']['footerHeight'].'px;background-color:'.$data['config']['footerBG'].';color:'.$data['config']['footerColor'].'">'.$data['config']['footerText'].'</div></div>
</htmlpagefooter>
';
?>
<?php
echo '<div id="total_wrapper">
<div id=reportContainer>';
$pdf = new pdfClass($data['config'],$data['data'],$reportType,$assessment['client_name'],$data['config']['fileName_Student']);

$pdf->generate();
$content = ob_get_clean();
$content .="</div></div>";
//echo '<pre>'.$content.'</pre>';die;
$mpdf=new mPDF('utf-8', 'letter', 0, '', 0, 0, 0, 0, 0, 0,'CalibriBold');
$mpdf->autoScriptToLang = true;
$mpdf->baseScript = 1;	// Use values in classes/ucdn.php  1 = LATIN
$mpdf->autoVietnamese = true;
$mpdf->autoArabic = true;
$mpdf->autoLangToFont = true;
//$mpdf->use_kwt = true;
//$stylesheet = file_get_contents(ROOT . 'public' . DS . 'css' . DS . 'pdfreports.css'); // external css
//$mpdf->setHTMLHeader($pdf->getPdfHeader());
//$mpdf->setFooter($pdf->getPdfFooter());
//$mpdf->setFooter('{PAGENO}');
//$mpdf->WriteHTML($stylesheet,1);
//$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);
$mpdf->SetTitle($pdf->fileName);
//$mpdf->Output($pdf->fileName.'.pdf','I');
//$mpdf->debug = true;
if (!empty($download_all_array) && isset($download_all_array)) {
$mpdf->Output('uploads/download_pdf/'.$pdf->fileName.'.pdf','F');    
}else{
$mpdf->Output($pdf->fileName.'.pdf','I');
}
?>