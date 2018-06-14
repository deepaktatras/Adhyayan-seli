(function($){
	$.reportround2Class=function(conf,theData){
		var reportTitleHeight=55;
		var cl= {
			pageCount:0,
			blockCount:0,
			rowCount:0,
			config:conf,
			data:theData,
			container:$("#"+conf.containerID),
			currentPage:null,
			currentBlock:null,
			currentBlockStyle:'',
			currentBlockConfig:{},
			currentSectionIndex:0,
			rowNoInCurrentBlock:0,
			
			maxHeightPageBody:(conf.pageHeight-conf.headerHeight-conf.footerHeight-conf.pageNoBarHeight-2*conf.bodyTopBottomPadding),
			tempVar:0,
			maxHeightCoverPageBody:(conf.pageHeight-conf.coverHeaderHeight-conf.footerHeight-conf.pageNoBarHeight-2*conf.bodyTopBottomPadding-reportTitleHeight),
			generate:function(){
				//Call this function to generate report
				//this.addCoverPage();
				
				if(this.data!=undefined && this.data!=null){
					var section_count=this.data.length;
					for(this.currentSectionIndex=0;this.currentSectionIndex<section_count;this.currentSectionIndex++){
						this.addSection(this.data[this.currentSectionIndex]);
					}
				}
			},
			/*addCoverPage:function(){
				var t=this.maxHeightPageBody;
				this.config.coverHeaderHeight;
			},*/
			addSection:function(section){
				//Adds a new section in the report
				this.addPage();
				if(section.indexKey!=undefined && section.indexKey!=""){
					$("#indexKey-"+section.indexKey).html(this.pageCount);
				}
				if(section.sectionHeading!=undefined && section.sectionHeading.text!=undefined && section.sectionHeading.text!=""){
					this.addSectionHeading(section.sectionHeading);
				}
				if(section.sectionBody!=undefined && section.sectionBody.length>0){
					var block_count=section.sectionBody.length;
					for(var i=0;i<block_count;i++){
						this.addBlock(section.sectionBody[i]);
					}
				}
			},
			addBlock:function(block){
				//Adds a new block in the current page
				block.config=block.config==undefined?{}:block.config;
				if(block.config.startNewPage!=undefined && block.config.startNewPage==1){
					this.addPage();
				}
				if(block.indexKey!=undefined && block.indexKey!=""){
					$("#indexKey-"+block.indexKey).html(this.pageCount);
				}
				this.currentBlockStyle=block.style;
				this.currentBlockConfig=block.config;
				this.addNewTable(block.indexKey);
				this.rowNoInCurrentBlock=0;
				if(block.blockHeading!=undefined && block.blockHeading.data!=undefined && block.blockHeading.data.length>0){
					this.addRow(block.blockHeading.data,1,(block.blockHeading.style==undefined?'':block.blockHeading.style));
				}
				if(block.blockBody!=undefined && block.blockBody.dataArray!=undefined && block.blockBody.dataArray.length>0){
					var dataArray_count=block.blockBody.dataArray.length;
					var style=block.blockBody.style!=undefined?block.blockBody.style:'';
					for(var i=0;i<dataArray_count;i++){
						this.addRow(block.blockBody.dataArray[i],0,style);
					}
				}
			},
			addRow:function(data,isHead,style){
				//Adds a new row to the current block
				this.rowCount++;
				var h='<tr class="'+style+' '+(isHead==1?"head-row":"body-row rowNo-"+this.rowNoInCurrentBlock)+'" id="row-'+this.rowCount+'">',col_count=data.length;
				for(var i=0;i<col_count;i++){
					h+=data[i]==null?'<td></td>':'<td'+(data[i].cSpan==undefined?'':' colspan="'+data[i].cSpan+'"')+(data[i].rSpan==undefined?'':' rowspan="'+data[i].rSpan+'"')+' class="'+(data[i].style==undefined?'':data[i].style)+(isHead?'':' colNo-'+i)+'">'+(data[i].text==undefined?data[i]:data[i].text)+'</td>';
				}
				h+='</tr>';
				this.currentBlock.append(h);
				var pH=this.currentPage.height();				
				if(pH>this.maxHeightPageBody){
					//Page height exceeded, Time to break the page on the bases of our rules 
					var cRow=this.currentBlock.find("tr."+(isHead?"head-row":"body-row")+":last");
					
					var withHead=this.currentBlockConfig.withHead!=undefined && this.currentBlockConfig.withHead==1?true:false;
					var minRows=this.currentBlockConfig.minRows!=undefined && this.currentBlockConfig.minRows>0?this.currentBlockConfig.minRows:1;
					var groupby=this.currentBlockConfig.groupby!=undefined?this.currentBlockConfig.groupby:'';
					var rowsPrinted=this.currentBlock.find(".body-row").length;
					
					var cHtml="";
					
					var groupBlocks=[];
					var groupHeight=0;
					if(groupby!=''){
						//If group by condition exists then create array of elements that need to be grouped and calculate there height
						var t=this.currentBlock;
						while(t.data('groupby')==groupby){
							groupBlocks.unshift(t);
							groupHeight+=t.innerHeight()+this.getVerticalMarginAndBorder(t);
							t=t.prev();
						}
						groupHeight-=this.getCssPropSize(this.currentBlock,"margin-bottom");
					}
					if(groupBlocks.length>0 && groupHeight<=this.maxHeightPageBody && this.currentPage.find(".the-block").length>groupBlocks.length){
						//Shifting a group of blocks to next page
						for(var i=0;i<groupBlocks.length;i++){
							cHtml+=groupBlocks[i].wrap('<div></div>').parent().html();
							groupBlocks[i].parent().remove();
						}
						this.addPageAndMayBeSectionHeading();
						this.currentPage.append(cHtml);
						this.currentBlock=$("#block-"+this.blockCount);
						var tmp=this.currentPage.find(".the-block");
						for(var i=0;i<tmp.length;i++){
							var ik=$(tmp[i]).data('indexkey');
							if(ik!=undefined && ik!=""){
								$("#indexKey-"+ik).html(this.pageCount);
							}
						}
					}else if((groupBlocks.length>0 || rowsPrinted<=minRows) && (this.currentBlock.innerHeight()+this.getVerticalBorder(this.currentBlock)+this.getCssPropSize(this.currentBlock,"margin-top"))<=this.maxHeightPageBody && this.currentPage.find(".the-block").length>1){
						//Shifting current block to the next page 
						cHtml=this.currentBlock.wrap('<div></div>').parent().html();
						this.currentBlock.parent().remove();
						this.addPageAndMayBeSectionHeading();
						this.currentPage.append(cHtml);
						this.currentBlock=$("#block-"+this.blockCount);
						var ik=this.currentBlock.data('indexkey');
						if(ik!=undefined && ik!=""){
							$("#indexKey-"+ik).html(this.pageCount);
						}
					}else{
						//shifting current row to the next page
						if(withHead && this.currentBlock.find(".head-row").length>0){
							cHtml=this.currentBlock.find(".head-row").wrap('<div></div>').parent().html();
							this.currentBlock.find(".head-row").unwrap();
						}
						cHtml+=cRow.wrap('<div></div>').parent().html();
						cRow.parent().remove();
						this.addPageAndMayBeSectionHeading();
						this.addNewTable();
						this.currentBlock.append(cHtml);
						if(!isHead){
							this.currentBlock.find(".body-row").removeClass('rowNo-'+this.rowNoInCurrentBlock).addClass("rowNo-0");
							this.rowNoInCurrentBlock=0;
						}
					}
				}
				if(!isHead)
					this.rowNoInCurrentBlock++;
			},
			addNewTable:function(index){
				//Adds a new block in the current page
				this.blockCount++;
				this.currentPage.append('<table '+(index!=undefined && index!=""?'data-indexkey="'+index+'"':'')+' '+(this.currentBlockConfig.groupby!=undefined?'data-groupby="'+this.currentBlockConfig.groupby+'"':'')+' id="block-'+this.blockCount+'" class="the-block '+(this.currentBlockStyle==undefined?'':this.currentBlockStyle)+'"></table>');
				this.currentBlock=$("#block-"+this.blockCount);
				//(this.currentBlockConfig!=undefined && (typeof this.currentBlockConfig).toLowerCase()=="object")?this.currentBlock.data(this.currentBlockConfig):'';
			},
			addSectionHeading:function(sectionHeading){
				//Adds section heading to the current page
				this.currentPage.append('<div class="section_head '+(sectionHeading.style!=undefined?sectionHeading.style:'')+'">'+sectionHeading.text+'</div>');
			},
			addPageAndMayBeSectionHeading:function(){
				this.addPage();
				var sec=this.data[this.currentSectionIndex];
				if(sec.sectionHeading!=undefined && sec.sectionHeading.config!=undefined && sec.sectionHeading.config.repeatHead!=undefined && sec.sectionHeading.config.repeatHead && sec.sectionHeading.text!=undefined){
					this.addSectionHeading(sec.sectionHeading);
				}
			},
			addPage:function(){
				//Adds a new page in the report
				this.pageCount++;
				
				this.container.append('<div id="page-'+this.pageCount+'" class="pageContainer" style="height:'+this.config.pageHeight+'px;width:'+this.config.pageWidth+'px;">'+
					this.getHeader()+this.getBody()+this.getFooter()
				+'</div>');
				
				//below page is to adjust cover page height
				if(this.pageCount==1){
					this.tempVar=this.maxHeightPageBody;
					this.maxHeightPageBody=this.maxHeightCoverPageBody - $(".coverAddress").innerHeight();
				}else
					this.maxHeightPageBody=this.tempVar;

				this.currentPage=$("#page-"+this.pageCount+" .page-inner");
				return this.pageCount;
			},
			getHeader:function(){
				//returns HTML of header for a page
				var h=this.pageCount==1?this.config.coverHeaderHeight:this.config.headerHeight;
				var pTpBm=this.pageCount==1?this.config.coverHeaderPadding:this.config.headerPadding;
				//	return '<div class="page-header '+(this.pageCount==1?'page-cover':'')+'" style="height:'+h+'px;background-color:'+this.config.headerBG+';"><div style="padding-top:'+pTpBm+'px;padding-bottom:'+pTpBm+'px;padding-right:'+this.config.pageLeftRightPadding+'px;height:'+(h-pTpBm*2)+'px" class="header-img"><img src="'+this.config.headerImg+'" /></div></div>'+(this.pageCount==1?'<div id="reportTitle" style="line-height:'+reportTitleHeight+'px">'+this.config.reportTitle+'</div>':'');
				if(this.config.isChildProt)
					return this.pageCount==1?'<table class="pdfHdr broad"> <tr><td class="halfSec fl" align="left"><a href=""><img src="'+this.config.headerImgAdh+'" alt="" style="height:58px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'+this.config.childProtImg+'" alt=""></a></td></tr> <tr><td colspan="2" style="text-align:center;padding:2px;font-weight:bold;">The Teacher Performance Review has been initiated and validated by '+this.config.schoolName+'</td></tr></table>':'<table class="pdfHdr thin"><tr><td class="halfSec fl" align="left"><a href=""><img src="'+this.config.headerImgAdh+'" alt="" style="height:52px;"></a></td><td class="halfSec fr" align="right"><a href=""><img style="height:57px;" src="'+this.config.childProtImg+'" alt=""></a></td></tr></table>';
                               
                                if(this.config.isShishuvanTeacherReview)
					return this.pageCount==1?'<table class="pdfHdr broad"> <tr><td class="halfSec fl" align="left"><a href=""><img src="'+this.config.headerImgAdh+'" alt="" style="height:58px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'+this.config.isShishuvanTeacherImg+'" alt=""></a></td></tr> <tr><td colspan="2" style="text-align:center;padding:2px;font-weight:bold;">The Teacher Performance Review has been initiated and validated by '+this.config.schoolName+'</td></tr></table>':'<table class="pdfHdr thin"><tr><td class="halfSec fl" align="left"><a href=""><img src="'+this.config.headerImgAdh+'" alt="" style="height:52px;"></a></td><td class="halfSec fr" align="right"><a href=""><img style="height:57px;" src="'+this.config.isShishuvanTeacherImg+'" alt=""></a></td></tr></table>';
                                if(this.config.isDominicSavioTeacherReview)
					return this.pageCount==1?'<table class="pdfHdr broad"> <tr><td class="halfSec fl" align="left"><a href=""><img src="'+this.config.headerImgAdh+'" alt="" style="height:58px;"></a></td><td class="halfSec fr" align="right"><a href=""><img  style="height:80px;" src="'+this.config.isDominicSavioTeacherImg+'" alt=""></a></td></tr> <tr><td colspan="2" style="text-align:center;padding:2px;font-weight:bold;">The Teacher Performance Review has been initiated and validated by '+this.config.schoolName+'</td></tr></table>':'<table class="pdfHdr thin"><tr><td class="halfSec fl" align="left"><a href=""><img src="'+this.config.headerImgAdh+'" alt="" style="height:60px;"></a></td><td class="halfSec fr" align="right"><a href=""><img style="height:60px;" src="'+this.config.isDominicSavioTeacherImg+'" alt=""></a></td></tr></table>';
                               
                                if(this.config.isStudentReview)
					return this.pageCount==1?'<table class="pdfHdrS broad"> <tr><td class="thirdSec fl" align="left"><a href=""><img src="'+this.config.headerStudentImgAdh+'" alt="" ></a></td><td style="text-align:center;color:#00b050;font-size:22px;font-weight:bold;"  class="thirdhSec">Career Readiness for Individuals</td><td class="thirdSec fr" align="right"><a href=""><img src="'+this.config.isStudentReviewImg+'" alt=""></a></td></tr></table><div class="blankBar green"></div><div class="blankBar yellow"></div>':'<table class="pdfHdrS thin"><tr><td class="hSec fl" align="left"><a href=""><img src="'+this.config.headerStudentImgAdh+'" alt="" ></a></td><td class="hSec fr" align="right"><a href=""><img src="'+this.config.isStudentReviewImg+'" alt="" ></a></td></tr></table><div class="blankBar green"></div><div class="blankBar yellow"></div>';    
                                else if(this.config.isChangeMaker)
					return this.pageCount==1?'<table class="pdfHdr broad"> <tr><td class="halfSec fl" align="left"><a href=""><img src="'+this.config.headerImgAdh+'" alt="" style="height:80px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'+this.config.headerImgCh+'" style="height:75px;" alt=""></a></td></tr></table>':'<table class="pdfHdr thin"><tr><td class="halfSec fl" align="left"><a href=""><img src="'+this.config.headerImgAdh+'" alt="" style="height:52px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'+this.config.headerImgChsmall+'" alt=""></a></td></tr> </table>';
				else if(this.config.isCoBranded)
					return this.pageCount==1?'<table class="pdfHdr broad"> <tr><td class="halfSec fl" align="left"><a href=""><img src="'+this.config.headerImgAdh+'" alt="" style="height:80px;"></a></td><td class="halfSec fr" align="right"><a href=""><img src="'+this.config.coBrandedImg+'" style="height:80px;" alt=""></a></td></tr><tr style="text-align:center;padding:2px;font-weight:bold;"><td colspan="2">Don Bosco School Self-Review and Evaluation Programme (DBSSRE) conducted under the aegis of \'Don Bosco for Excellence\'</td></tr> </table>':'<div class="page-header" style="height:'+h+'px;background-color:'+this.config.headerBG+';"><div style="padding-top:'+pTpBm+'px;padding-bottom:'+pTpBm+'px;padding-right:'+this.config.pageLeftRightPadding+'px;height:'+(h-pTpBm*2)+'px" class="header-img"><img src="'+this.config.headerImg+'" /></div></div>';
				else if(this.config.iscollegeReview)
					return this.pageCount==1?'<table class="pdfHdrS broad"> <tr><td class="thirdSec fl" align="left"><a href=""><img src="'+this.config.headerCollegeImgAdh+'" alt="" ></a></td><td style="text-align:center;color:#00b050;font-size:22px;font-weight:bold;"  class="thirdhSec">Career Readiness Review for Colleges</td><td class="thirdSec fr" align="right"><a href=""><img src="'+this.config.isCollegeReviewImg+'" alt=""></a></td></tr></table><div class="blankBar green"></div><div class="blankBar yellow"></div>':'<table class="pdfHdrS thin"><tr><td class="hSec fl" align="left"><a href=""><img src="'+this.config.headerCollegeImgAdh+'" alt="" ></a></td><td class="hSec fr" align="right"><a href=""><img src="'+this.config.isCollegeReviewImg+'" alt="" ></a></td></tr></table><div class="blankBar green"></div><div class="blankBar yellow"></div>';
				else
					return '<div class="page-header '+(this.pageCount==1?'page-cover':'')+'" style="height:'+h+'px;background-color:'+this.config.headerBG+';"><div style="padding-top:'+pTpBm+'px;padding-bottom:'+pTpBm+'px;padding-right:'+this.config.pageLeftRightPadding+'px;height:'+(h-pTpBm*2)+'px" class="header-img"><img src="'+this.config.headerImg+'" /></div></div>'+(this.pageCount==1?'<div id="reportTitle" style="line-height:'+reportTitleHeight+'px">'+this.config.reportTitle+'</div>':'');
			},
			getFooter:function(){
				//returns HTML of footer for a page
                                if(this.config.isStudentReview){
                                return '<div class="page-footer">'+(this.pageCount==1?'<table border="0"  class="pdfFtr broad"> <tr><td class="halfSec fl" align="center">'+this.config.coverAddressAntarang+'</td><td class="halfSec fr" align="center">'+this.config.coverAddressAdhyayanFoundation+'</td></tr></table>':'')+'<div style="line-height:'+this.config.pageNoBarHeight+'px;" class="page-num">Page '+this.pageCount+'</div><div class="page-footer-inner" style="line-height:'+this.config.footerHeight+'px;background-color:'+this.config.footerBG+';color:'+this.config.footerColor+'">'+this.config.footerText+'</div></div>';
                                }else if(this.config.iscollegeReview){
                                return '<div class="page-footer">'+(this.pageCount==1?'<table border="0"  class="pdfFtr broad"> <tr><td class="halfSec fl" align="center">'+this.config.coverAddressAntarang+'</td><td class="halfSec fr" align="center">'+this.config.coverAddressAdhyayanFoundation+'</td></tr></table>':'')+'<div style="line-height:'+this.config.pageNoBarHeight+'px;" class="page-num">Page '+this.pageCount+'</div><div class="page-footer-inner" style="line-height:'+this.config.footerHeight+'px;background-color:'+this.config.footerBG+';color:'+this.config.footerColor+'">'+this.config.footerText+'</div></div>';
                                }else{
				return '<div class="page-footer">'+(this.pageCount==1?'<div class="coverAddress">'+this.config.coverAddress+'</div>':'')+'<div style="line-height:'+this.config.pageNoBarHeight+'px;" class="page-num">Page '+this.pageCount+'</div><div class="page-footer-inner" style="line-height:'+this.config.footerHeight+'px;background-color:'+this.config.footerBG+';color:'+this.config.footerColor+'">'+this.config.footerText+'</div></div>';
                            }
			},
			getBody:function(){
				//returns HTML of inner body for a page
				return '<div class="page-inner" style="padding:'+this.config.bodyTopBottomPadding+'px '+this.config.pageLeftRightPadding+'px;"></div>';
			},
			getVerticalMarginAndBorder:function(el){
				//returns total size of top-bottom margin and border of an element in px
				return this.getCssPropSize(el,"margin-top")+this.getCssPropSize(el,"margin-bottom")+this.getCssPropSize(el,"border-bottom-width")+this.getCssPropSize(el,"border-top-width");
			},
			getVerticalBorder:function(el){
				// returns size of top and bottom border in px
				return this.getCssPropSize(el,"border-bottom-width")+this.getCssPropSize(el,"border-top-width");
			},
			getCssPropSize:function(el,pr){
				// returns size of given css property "pr" of an element "el" in px
				var a=el.css(pr);
				return (a==undefined || a==""?0:parseInt(a.replace("px","")));
			}
			
			
			
		};
		cl.generate();
		return cl;
	};
})(jQuery);
