{literal}<?xml version="1.0" encoding="UTF-8"?>{/literal}
<!--
Copyright(c) 2010 1rstWAP. All rights reserved.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>{$siteTitle}</title>
                <link href="js/select2/css/select2.css" type="text/css" rel="stylesheet" />
		<link href="skin/style.css" type="text/css" rel="stylesheet" />
                <link href="js/datatable/jquery.dataTables.css" type="text/css" rel="stylesheet"/>
		<link href="skin/jquery.ui/jquery-ui.css" type="text/css" rel="stylesheet" />
                <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script> 
		<script type="text/javascript" src="js/ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/ui/i18n/jquery.ui.datepicker-en-GB.min.js"></script>
		<script type="text/javascript" src="js/firstwap/firstwap.js"></script>
		<script type="text/javascript" src="js/ui.hourglass/jquery.ui.hourglass.min.js"></script>
		<script type="text/javascript" src="js/app/app.js"></script>
		<script type="text/javascript" src="skin/skin.js"></script>
                <script type="text/javascript" src="js/select2/js/select2.min.js"></script>
                <script type="text/javascript" src="js/jquery.form-validator.min.js"></script>
		<script type="text/javascript" src="js/jquery.fileDownload.js"></script>
		<script type="text/javascript" src="js/jquery.mask.js"></script>
                <script type="text/javascript" src="js/datatable/jquery.dataTables.min.js"></script>
            {literal}
		<script type="text/javascript">
                    $(document).ready(function() {
                        
                        $.validate({
                            errorMessagePosition : 'inline',
                        });
                        
                        $( "#dialog" ).dialog({
                            height: 100,
                            modal: true,
                            open: function(event, ui){
                             setTimeout("$('#dialog').dialog('close')",3000);
                            }
                        });
                        
                        loadUserDetail();

                        $("#list-user").select2({
                            placeholder: "Select a user"
                        });
                        
                        $('.btn-download').click(function(){
                            var linktoDownload = '/services/billling.downloadMessageContentBasedReport.php',
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
                                                 alert('File doesn\'t exist!')
                                             }
                                }

                            });
                        });
                    
                    });
                
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
    </head>
    <body>
		<div id="container">
			<div id="pageHeader">
				<div id="siteTitle">{$siteTitle}</div>
			</div>
			<div id="navPanel">
				<div id="welcomeMessage" class="panel"></div>
				{*<div id="menuPanel" class="panel"></div>*}
			</div>
			<div id="titlePanel" class="panel"></div>
			<div id="contentPanel">
                            {if !$isLogin}
				{include "login.form.tpl"}
                            {else}
                            <!-- Add content for report -->
                            <form id="msgContent-form" class="admin-xform" action="report.php" method="post" enctype="multipart/form-data">
                                    <div class="panel">
                                            <div class="panel-header"><img src="skin/images/icon-history.png" class="icon-image" alt=""><span>Message Content Based Report</span></div>
                                            <div class="panel-body">
                                                    <div class="panel-content">
                                                        
                                                        <fieldset class="float-centre" style="padding-bottom: 10px;padding-top: 10px;">
                                                            {if isset($message)}
                                                            <div id="dialog" title="Message">
                                                                {if $status == 404}
                                                                    <p style='font-size:10px;color:red'>{$message}</p>
                                                                {else}
                                                                    <p>{$message}</p>
                                                                {/if}
                                                            </div>
                                                            {/if}
                                                            <span class="ui-helper-clearfix"></span>
                                                            <div>
                                                                <label>Users</label>
                                                                    <select id="list-user" name="user[]" data-validation="required">
                                                                    </select>
                                                            </div>
                                                            <span class="ui-helper-clearfix"></span>
                                                            <div style="padding-top: 5px;">
                                                                <label>Message Content File</label>
                                                                <input type="file" name ="msgContentFile" id="msgContentFile" data-validation="required"></input>
                                                            </div>
                                                            <span class="ui-helper-clearfix"></span>
                                                            <div style="margin-top: 10px;">
                                                                <button type="submit" class="form-button" style="margin: 5px;">Process</button>
                                                            </div>
                                                            
                                                            <span class="ui-helper-clearfix"></span>
                                                            
                                                        </fieldset>
                                                        <fieldset class="float-centre" style="padding-bottom: 10px;padding-top: 10px;">
                                                            <!-- Message Content Based Report Table -->
                                                            <table class="admin-table">
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
                                                                    {if isset($reportFiles) && !empty($reportFiles)}
                                                                        {section name=list loop=$reportFiles}
                                                                            <tr>
                                                                                <td class="type-text">{$reportFiles[list]->userAPI}</td>
                                                                                <td class="type-text">{$reportFiles[list]->createdAt}</td>
                                                                                <td class="type-text">{$reportFiles[list]->reportName}</td>
                                                                                <td class="type-action">
                                                                                        {if ($reportFiles[list]->isDone)}
                                                                                            <a href="#" title="Download" class="form-button btn-download" data="{$reportFiles[list]->reportPackage}" id="downloadReport"><img src="skin/images/download.png" class="icon-image" alt="" /></a>
                                                                                        {else}
                                                                                            <img src="skin/images/wheel.gif"  title="Report on progress" />
                                                                                        {/if}
                                                                                </td>   
                                                                            </tr>
                                                                            {/section}
                                                                    {/if}
                                                                </tbody>
                                                            </table>
                                                            <!-- End of Message Content based Report Table -->
                                                        </fieldset>
                                                    </div>
                                            </div>
                                    </div>
                            </form>
                            <!-- End of report -->
                            {/if}
			</div>
			<div id="pageFooter">
				<div id="siteCopyright">&copy; 2010 1rstWAP. All rights reserved.</div>
			</div>
		</div>
    </body>
</html>