<?php
//print_r($_REQUEST);
//print_r($currQuestions);
//array_values($currQuestions);
//print_r($currQuestions);
$diagnosticId = empty($_GET['diagnosticId']) ? 0 : trim($_GET['diagnosticId']);

$kpaId = empty($kpaId) ? 0 : trim($kpaId);
$kqId = empty($kqId) ? 0 : trim($kqId);
$cqId = empty($cqId) ? 0 : trim($cqId);
//print_r($currQuestions);
if($parentId>0 && $equivalenceId>0 && $langIdOriginal>0){
?>
<form id="addmoreform" namne="addmoreform" enctype="multipart/form-data" method="post" action="#">
<h4 class="page-title row">Translate <?php echo $formTitle; ?>s</h4> 

    <!--<div class="modal-body pad0">-->
    <div class="ylwRibbonHldr">
        <div class="tabitemsHldr">&nbsp;</div>
    </div>
    <div class="subTabWorkspace pad26 sortable-form" role="document" data-type="<?php echo $type; ?>" data-assessmentid="<?php echo $assessmentId; ?>" data-id="<?php echo $kpaId; ?>" data-kqid="<?php echo $kqId; ?>" data-cqid="<?php echo $cqId; ?>" data-for="<?php echo $for; ?>" >

    <div class="queryBoxWrapper">
        <div class="row">
                <div class="col-md-11">
        <ol id="sortableR_new" class="connectedSortable">
                               <?php
                               
                                foreach ($selectedQuestionsOriginal as $key => $val):
                                    
                                    $val = array_values($val);
                                    $val_old = array_values(isset($selectedQuestions[$val[0]])?$selectedQuestions[$val[0]]:array());
                                   // print_r($val_old);
                                    $new_lang_text=isset($val_old['1'])?$val_old['1']:'';
                                    
                                    $data_id=isset($val_old['1'])?$val[0]:0;
                                ?>
                                    <li ><span class='vtip' title='<?php echo $val[1] ?>' style='font-weight:bold'><?php echo $val[1] ?></span>
                                            
                                        <div><input type="text"  data-id='<?php echo $data_id ?>' name="<?php echo $val[0] ?>" class="form-control" value="<?php echo $new_lang_text ?>" placeholder="Enter text here"></div>
                           
                                                    
                                    </li>
                                <?php
                                endforeach;
                                ?>    
        </ol>
                    <div class="text-right mb10">
                        
                            <button type="submit" class="btn btn-primary" id="btn_kpa_save"><i class="fa fa-floppy-o"></i>Save</button>
                        	
                    </div>
                    <div class="ajaxMsg"></div>
                    </div>

    </div>
    </div>

    </div>
</form>
<?php
}else{
?>
<form id="addmoreform" namne="addmoreform" enctype="multipart/form-data" method="post" action="#">

    <h4 class="page-title row">Choose <?php echo $formTitle; ?>s</h4> 

    <!--<div class="modal-body pad0">-->
    <div class="ylwRibbonHldr">
        <div class="tabitemsHldr">&nbsp;</div>
    </div>
    <div class="subTabWorkspace pad26 sortable-form" role="document" data-type="<?php echo $type; ?>" data-assessmentid="<?php echo $assessmentId; ?>" data-id="<?php echo $kpaId; ?>" data-kqid="<?php echo $kqId; ?>" data-cqid="<?php echo $cqId; ?>" data-for="<?php echo $for; ?>" >

        <div class="queryBoxWrapper">
            <dl class="searchbar text-center fldList">
                <dd class="the-basics">
                    <input id="searchbox" type="text" class="form-control typeahead tt-query" placeholder="Search .." autocomplete="off" spellcheck="false" >
                    <a href="javascript:void(0);" id="clrBtn" class="vtip" title="Clear"><i class="fa fa-times-circle"></i></a>                            
                </dd>
            </dl>

            <div class="row">
                <div class="col-md-6"> 
                    <div class="leftQuestHldr">
                        <h3>Existing <?php echo $formTitle; ?>s</h3>
                        <div class="vertScrollArea">
                            <ul id="sortableL" class="connectedSortable" style="min-height:300px;">
                                <?php
                                foreach ($currQuestions as $key => $val):
                                    $val = array_values($val);
                                    if (isset($val[2]) && ($val[2] == $diagnosticId))
                                        continue;

                                    //echo "<li class='vtip' title='".$val[1]."' data-id='".$val[0]."'><i class='fa fa-question-circle'></i><span>".$val[1]."</span><a href='#' class='edit'><i class='fa fa-pencil vtip' title='Edit question'></i></a></li>";
                                    echo '<li class="vtip" data-id="' . $val[0] . '"><i class="fa fa-question-circle"></i><span class="vtip" title="' . $val[1] . '">' . $val[1] . '</span></li>';
                                endforeach;
                                ?>                                                                 
                            </ul>
                        </div>								
                        <div class="addQstBox" id="editKpaBox">
                            <input type="text" name="editQ" placeholder="Enter text here">
                            <button type='button' class='addQuest btn btn-primary editQbtn'><i class="fa fa-pencil"></i>Save</button>
                            <!--<div class="text-right padT6"><a href="#" class="closeBox">Close <i class="fa fa-times-circle"></i></a></div>-->
                        </div>
                        <div class="addQstBox" id="addKpaBox">
                            <input type="text" name="addQ" placeholder="Enter text here">
                            <button type='button' class='addQuest btn btn-primary addQbtn'><i class="fa fa-plus"></i>Add</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="sortInfoIcon text-center"><i class="fa fa-arrows-h" style="padding-top:194px;"></i></div>
                </div>
                <div class="col-md-5">                    
                    <div class="rightConfirmedQueryBox" style="padding-bottom:9px;">
                        <h3>Selected <?php echo $formTitle; ?>s</h3>
                        <div class="vertScrollArea">
                            <ul id="sortableR" class="connectedSortable" style="min-height:300px;">
                                <?php
                                foreach ($selectedQuestions as $key => $val):
                                    $val = array_values($val);
                                    echo "<li class='vtip' data-id='" . $val[0] . "'><i class='fa fa-question-circle'></i><span class='vtip' title='" . $val[1] . "'>" . $val[1] . "</span></li>";
                                endforeach;
                                ?> 
                            </ul>
                        </div>
                        <div class="addQstBox mt10">
                            <div class="attachment">
                                <?php if ($type == 'kpa') { ?>                        
                                    <div class="clearfix">                                    
                                        <div class="fr mtM clearfix">
                                            <?php if (isset($image_name) && $image_name != '') { ?>
                                                <div class="fl fileStat">                      
                                                    <img src="<?php echo UPLOAD_URL_DIAGNOSTIC . '' . $image_name ?>" alt="<?php echo $image_name; ?>" class="resizable ui-widget-content" style="height:27px;">
                                                </div>
                                            <?php } ?>
                                            <div class="fl">
                                                <div class="fileUpload filt-nupload btn btn-primary mr0" style="z-index: 9;">
                                                    <i class="glyphicon glyphicon-folder-open"></i> <span>Attach Image</span>
                                                    <input type="file" autocomplete="off" id="dig_image" name="dig_image" title="" class="upload uploadBtn">
                                                    <input type="hidden" autocomplete="off" id="dig_image" name="dig_image_id" value="<?php echo isset($dig_image_id)?$dig_image_id:'' ?>">
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="padT5 r4"><p class="note text-right"><strong>Preferred Size:</strong> W - 180 &nbsp; H - 60</p></div>                          
                                <?php } ?>                               

                            </div>
                        </div>
                    </div>

                    <div class="text-right mb10">
                        <?php if (!$isDiagnosticPublished) { ?>
                            <button type="submit" class="btn btn-primary" id="btn_kpa_save"><i class="fa fa-floppy-o"></i>Save</button>
                        <?php } ?>	
                    </div>
                    <div class="ajaxMsg"></div>
                </div>
            </div>                                            

        </div>

    </div>
</form> 
<?php
}
?>
<!-- </div>-->
<!-- Diagnostic addition script -->        
<script>
    $(function () {
        var editedElementTitle = '';
        $(".vertScrollArea").mCustomScrollbar({theme: "dark"});
        $("#sortableL, #sortableR").sortable({
            connectWith: ".connectedSortable"
        }).disableSelection();

        $(".addQbtn").click(function (e) {
            var leftbox = '';
            e.preventDefault();
            var text = $("input[name='addQ']").val().trim();
            console.log("addqtext: " + text);
            var duplicateFlag = 0;
            if (text == '')
                alert("Please enter text!");
            else
            {
                //$("ul#sortableL.connectedSortable li span")
                leftbox = $("ul.connectedSortable li span");
                leftbox.each(function () {
                    //console.log($(this).text());
                    if (text.toLowerCase().trim() == $(this).text().toLowerCase().trim()) {
                        alert('This already exists. Please enter new text.');
                        duplicateFlag = 1;
                    }

                });
                if (!duplicateFlag)
                {
                    var addTxt = "<i class='fa fa-question-circle'></i><span class='vtip' title='" + text + "'>" + text + "</span><a href='#' class='edit'><i class='fa fa-pencil vtip' title='Edit <?php echo $formTitle; ?>'></i></a>";
                    var $li = $("<li data-id='0' class='vtip' />").html(addTxt);
                    $("#sortableL").append($li);
                    $("#sortableL").sortable('refresh');
                    //$("#sortableR").sortable('refresh');
                    $("input[name='addQ']").val('');
                    $(".vertScrollArea").mCustomScrollbar("update");
                    $(".vertScrollArea").mCustomScrollbar("scrollTo", "bottom");
                }
            }

        });
        $('#editKpaBox').hide();
        $(document).on('click', 'ul.connectedSortable li a.edit', function (e) {
            e.preventDefault();
            try {
                var editableElement = $(this);
                console.log(editableElement.parent().text())
                $('#editKpaBox').show();
                $('#addKpaBox').hide();
                //get text of your questions
                var qId = editableElement.parent('li').data('id');
                var qText = editableElement.parent().find('span').text();
                console.log(qId);
                console.log(qText);
                $("#editKpaBox input[name='editQ']").val(qText);
                editedElementTitle = qText;
                console.log("edited eleme title: " + editedElementTitle);
            } catch (err)
            {
                alert("Something went wrong!");
            }

        });
        $(".editQbtn").click(function (e) {
            var elementToUpdate = $("ul.connectedSortable li span[title='" + editedElementTitle + "']");
            var valueToUpdate = $("#editKpaBox input[name='editQ']").val().trim();
            var leftbox = '';
            var duplicateFlag = 0;
            if (valueToUpdate == '')
            {
                alert("Please enter text!");
                return;
            }
            leftbox = $("ul.connectedSortable li span");
            leftbox.each(function () {
                //console.log($(this).text());
                if (valueToUpdate.toLowerCase().trim() == $(this).text().toLowerCase().trim()) {
                    alert('This already exists. Please enter new text.');
                    duplicateFlag = 1;
                }
            });
            if (duplicateFlag == 0)
            {
                elementToUpdate.attr('title', valueToUpdate);
                elementToUpdate.html(valueToUpdate);
                $('#editKpaBox').hide();
                $('#addKpaBox').show();
            }
        });

        // Type Head (Suggestion Box)           
        var substringMatcher = function (strs) {
            return function findMatches(q, cb) {
                var matches, substringRegex;
                matches = [];
                substrRegex = new RegExp(q, 'i');
                $.each(strs, function (i, str) {
                    if (substrRegex.test(str)) {
                        matches.push(str);
                    }
                });
                cb(matches);
            };
        };
        var questions = [];
        $('#sortableL li span').each(function (i, elem) {
            var title = $(this).attr('title');
            questions.push(title);
        });
        $('.the-basics .typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 3
        },
                {
                    name: 'questions',
                    source: substringMatcher(questions),
                    limit: 30
                });

        $('.typeahead').bind('typeahead:select', function (ev, suggestion) {
            //var id = $('#sortableL li[title="'+suggestion+'"]').data('id');
            var title;
            $('#sortableL li span').each(function (i, elem) {
                //console.log($(this).attr('title'));
                suggestion = suggestion.replace("'", "");
                title = $(this).attr('title');
                title = title.replace("'", "");
                if (suggestion != title)
                    $(this).parent().hide();
                else
                    $(this).parent().show();
            });
        });

        $('.typeahead').bind('typeahead:render', function (ev, suggestion) {
            var text = '';
            $('#sortableL li').hide();
            $(".tt-dataset.tt-dataset .tt-suggestion").each(function () {
                text = $(this).text();
                $('#sortableL').find('li span[title="' + text + '"]').parent().show();
            });

        });
        $(document).on('keyup', '#searchbox', function (e) {
            if (e.which == '13')
                return false;
            if ($("#searchbox").val() == '')
            {
                $('#sortableL li span').each(function () {
                    $(this).parent().show();
                });
            }
        });
        $(document).on('click', "#clrBtn", function () {
            $(document).trigger("clrBtnClick");

        })
        $(document).on('clrBtnClick', function () {
            $('#sortableL li span').each(function () {
                $(this).parent().show();
            });
            $("#searchbox").val('');
        });
    });
</script>
