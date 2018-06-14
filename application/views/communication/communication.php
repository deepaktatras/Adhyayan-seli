
<div class="filterByAjax user-list" data-action="<?php echo $this->_action; ?>" data-controller="<?php echo $this->_controller; ?>">
    <h1 class="page-title">
        MyCommunications 
        <div class="clr"></div>
    </h1>
    <div class="asmntTypeContainer">						
        <?php
        $ajaxFilter = new ajaxFilter();
        $ajaxFilter->addTextbox("name", $filterParam["name_like"], "Name");
        $ajaxFilter->addTextbox("client", $filterParam["client_like"], "Client Name");
       // $ajaxFilter->addDateBox("fdata", $filterParam["fdata_like"], "From Date","checkDate(this.value,'fdata')");
       // $ajaxFilter->addDateBox("edate", $filterParam["edate_like"], "End Date","checkDate(this.value,'edate')");
        $ajaxFilter->addDateBox("fdate", $filterParam["fdate_like"], "From Date");
        $ajaxFilter->addDateBox("edate", $filterParam["edate_like"], "End Date");
        $ajaxFilter->generateFilterBar(1);
        ?>
        <script type="text/javascript">
        // function for change the end date according to from date on 28-07-2016 by Mohit Kumar
        $(function() {
            $('.fdate').datetimepicker({format: 'YYYY-MM-DD', maxDate: new Date, pickTime: false}).off('focus')
                .click(function () {
                    $(this).data("DateTimePicker").show();
                });
            $('.edate').datetimepicker({format: 'YYYY-MM-DD', maxDate: new Date, pickTime: false}).off('focus')
                .click(function () {
                    $(this).data("DateTimePicker").show();
                });
            $(".fdate").on("dp.change", function (e) {
                $('.edate').data("DateTimePicker").setMinDate(e.date);
            });
            $(".edate").on("dp.change", function (e) {
                $('.fdate').data("DateTimePicker").setMaxDate(e.date);
            });
        });
        </script>
        <form name="frm" action="" method="post">
            <div class="tableHldr">

                <table class="cmnTable">
                    <thead>
                        <tr>
                            <th data-value="name" class="sort <?php echo $orderBy == "name" ? "sorted_" . $orderType : ""; ?>">Name</th>
                            <th data-value="client_name" class="sort <?php echo $orderBy == "client_name" ? "sorted_" . $orderType : ""; ?>">School Name</th>
                            <th data-value="date" class="sort <?php echo $orderBy == "date" ? "sorted_" . $orderType : ""; ?>">Sent Date</th>
                             

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($users))
                            foreach ($users as $userDetail) {
                                ?>
                                <tr align="center">
                                    <td class="tdUserClass" style="text-align: center ! important;"><?php echo $userDetail['name']; ?></td>
                                    <td class="tdUserClass" style="text-align: center ! important;"><?php echo $userDetail['client_name']; ?></td>
                                    <td class="tdUserClass" style="text-align: center ! important;">
                                        <?php echo date('Y-m-d',  strtotime($userDetail['date'])); ?>
                                    </td>                         
                                </tr>
                                <?php
                            } else {
                            ?>
                            <tr>
                                <td colspan="5">No user found</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </form>
        <?php echo $this->generateAjaxPaging($pages, $cPage); ?>

        <div class="ajaxMsg"></div>
    </div>
</div>