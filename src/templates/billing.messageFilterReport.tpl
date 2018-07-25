{literal}
    <script type="text/javascript">
        $(document).ready(function() {

            var checkReportInterval = setInterval(checkReport, 3000);
            checkReport();

            for(var i = 0; i < 3; i++ ){
                var month = moment().subtract(i, 'months');
                $('#month').append('<option value="'+month.format('MM-YYYY')+'">'+month.format('MMM')+'</option>');
            }

            function checkReport(){
                $.ajax({
                    type    : "POST",
                    url     : "services/billing.messageFilterReport.php",
                    data    : { action : 'getManifest' },
                    dataType: 'JSON',
                    success : function (response) {
                       // refresh table
                       var html = '';
                       $.each(response, function(key, value){
                           html += '<tr><td class="type-text">'+value.userAPI+'</td>'
                                        +'<td class="type-text">'+value.createdAt+'</td>'
                                        +'<td class="type-text">'+value.reportName+'</td>'
                                        +'<td class="type-action">';

                            if(value.isDone){
                                html += '<a href="#" title="Download" class="form-button btn-download" data="'+value.reportPackage+'" id="downloadReport">'
                                        +'<img src="skin/images/download.png" class="icon-image" alt="" /></a>';
                            } else {
                                html += '<img src="skin/images/wheel.gif"  title="Report on progress" />';
                            }
                            html += '</td> </tr>'
                       });

                       $('#report-table tbody').empty();
                       $('#report-table tbody').append(html);
                    },
                    error: function (e) {
                        console.log("ERROR : ", e);
                    }
                });

            }
            $.validate({
                errorMessagePosition : 'inline',
            });

            loadUserDetail();

            $("#list-user").select2({
                placeholder: "Select a user"
            });

            /*
             this function to check the file extension after user input the file
             */
             var verificationFile = false;
             $("#msgContentFile").change(function(e){
                e.preventDefault();
                var fileName  = document.getElementById("msgContentFile").value;
                var extension = fileName.substring(fileName.lastIndexOf('.')+1, fileName.length) || fileName;
                 if (extension!=="odt"&&extension!=="xls"&&extension!=="xlsx"&&extension!=="csv") {
                     $('#typeValidationMessages').text("extension file is not suppported");
                     verificationFile = false;
                 }
                 else{
                     $('#typeValidationMessages').text("");
                     verificationFile = true;
                 }
            });

             /*
               sumit function
              */
            $('#msgContent-form').submit(function(e){
                    e.preventDefault();
                    /*it will check the extension file , after user click the submit button
                     * if the file is odt,xls,or csv ,then it will continue the process
                     * if the file extension is not odt,xls,xlsx or csv, then it will show up the validation message
                     */
                    if (verificationFile) {
                        var user = $('#list-user').val(),
                        file = $('#msgContentFile')[0].files[0],
                        report_month = $('#month').val();

                        var data = new FormData();
                        data.append("file", file);
                        data.append("date", report_month);
                        data.append("user", user);

                        $.ajax({
                            type        : "POST",
                            enctype     : 'multipart/form-data',
                            url         : "services/billing.messageFilterReport.php",
                            data        : data,
                            processData : false,
                            contentType : false,
                            cache       : false,
                            timeout     : 600000,
                            success     : function (response) {
                                var html = "";
                                $('#dialog').html('');
                                switch(response){
                                    //the report is processed
                                    case '200':
                                        html += "<p>Your report is being generated</p>";
                                        break;

                                    //the report for the user is doesnt exist
                                    case '404':
                                        html += "<p style='font-size:10px;color:red'>Billing report for user "+user+" doesn't exist</p>";
                                        break;

                                    //the default case if json doesnt return back 200 or 404 code .
                                    default:
                                        html += "<p style='font-size:10px;color:red'>Internal error occured. Please try again!</p>";
                                }
                                html += "</div>";
                                $('#dialog').append(html);
                                //for showing up dialog notifictaion
                                $( "#dialog" ).dialog({
                                    height: 100,
                                    modal: true,
                                    open: function(event, ui){
                                     setTimeout("$('#dialog').dialog('close')",1000);
                                    }
                                });
                                $('#msgContent-form')[0].reset();
                                $("#list-user").val('').trigger("change");
                            },
                            error: function (e) {
                                console.log("ERROR : ", e);
                            }
                        });
                    }
                else{
                    //show error validation messages
                   $('#typeValidationMessages').text("extension file is not suppported");
                   return false;
                }
            });

            /*
              this function for download the rport
             */
            $(document)
                .on('click', '.btn-download', function(e){
                    var linktoDownload = 'services/billling.downloadMessageFilterReport.php',
                    report = $(this).attr('data');
                    $.ajax({
                        url         : linktoDownload,
                        type        : 'GET',
                        data        : {report : report, check: true},
                        dataType    : 'JSON',
                        success     : function(response){
                                     if(response == 200){
                                         window.location = linktoDownload+"?report="+report;
                                     } else {
                                         alert('File doesn\'t exist! It might be deleted by system.')
                                     }
                        }

                    });

                }
            );

        });

        /*
         this function is for load the user detail
         */
        function loadUserDetail(){
            $.ajax({
                url: 'services/billing.getUserDetail.php',
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    var html        = '';
                    var defaultData = [];

                    $.each(data, function(k,v){
                        if($.inArray(v.USER_ID, defaultData ) == -1)
                            html += '<option value="'+v.USER_NAME+'">'+v.USER_NAME+'</option>';
                    });
                    $('#list-user').append(html);
                },
            });
        }
    </script>
{/literal}
<!-- Add content for report -->
<div class="panel">
        <div class="panel-header"><img src="skin/images/icon-history.png" class="icon-image" alt=""><span>Message Content Based Report</span></div>
        <div class="panel-body">
                <div class="panel-content">
                    <div id='dialog' title='Message'></div>
                    <form id="msgContent-form" class="admin-xform" method="post" enctype="multipart/form-data">
                        <fieldset class="float-centre" style="padding-bottom: 10px;padding-top: 10px;">
                            <span class="ui-helper-clearfix"></span>
                            <div>
                                <label>SMS API user</label>
                                    <select id="list-user" name="user" data-validation="required">
                                    </select>
                            </div>
                            <span class="ui-helper-clearfix"></span>
                            <div style="padding-top: 5px;">
                                <label>Message Content File</label>
                                <input required type="file" name ="msgContentFile" id="msgContentFile"
                                accept=".csv, .odt, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                <p id="typeValidationMessages" style="color:red; font-size: 90%"></p>
                                </input>
                            </div>
                            <span class="ui-helper-clearfix"></span>
                            <div  style="padding-top: 5px;">
                                <label>Report For Month</label>
                                <select name="month" id="month" data-validation="required" style="font-size: 8pt; width: 45px;">
                                </select>
                            </div>
                            <span class="ui-helper-clearfix"></span>
                            <div style="padding-top: 10px;" class="form-fieldset-submission">
                                <button type="submit" class="ui-button btn-process">Process</button>
                            </div>

                            <span class="ui-helper-clearfix"></span>

                        </fieldset>
                    </form>
                    <form class="admin-xform" method="post">
                        <fieldset class="float-centre" style="padding-bottom: 10px;padding-top: 10px;">
                            <!-- Message Content Based Report Table -->
                            <table class="admin-table" id="report-table">
                                <thead>
                                        <tr>
                                            <th style="width: 20%;">User API</th>
                                            <th style="width: 20%;">Created At</th>
                                            <th style="width: 15%;">Report name</th>
                                            <th style="width: 20%;">Action</th>
                                        </tr>
                                </thead>
                                <tfoot>
                                        <tr>
                                            <th colspan="9">&nbsp;</th>
                                        </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                            <!-- End of Message Content based Report Table -->
                        </fieldset>
                    </form>
                </div>
        </div>
</div>