<!--<form id="apiuser-editform" class="report-tabform" action="services/generateReportUser.php" method="post" style="width: 100%;">-->
<form id="apiuser-editform" class="report-tabform" action="services/generateReportUser.php" method="get" style="width: 100%;">
    <input type="hidden" name="userID" value="{$details.userID}" />
    <input type="hidden" name="userName" value="{$details.userName}" />
    <fieldset class="float-centre">

        <dl class="admin-definitions">
            <input type="hidden" name="clientID" value="{$details.clientID}" />
            <dt>Client Company</dt><dd><a href="#" onclick="$app.module('client').viewClient({$details.clientID})">{$details.clientCompanyName}</a></dd>	
            <dt>Username</dt><dd>{$details.userName}</dd>

        </dl><br>

        <span class="ui-helper-clearfix"></span><br>
        <div>
            <label class="form-flag-required">
                Month 
                <select name="month" style="float:none;display: inline">
                    <!--<option value="">- Select -</option>-->
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </label>
            
{*            <input type="text" name="fromDate" value="{$details.paymentDate}" class="form-datepicker" maxlength="10"/><span class="ui-helper-clearfix"></span>*}
            <label class="form-flag-required">Year  
                 <select name="year" style="float:none;display: inline">
                    <!--<option value="">- Select -</option>-->
                    {php}
                        for($year = 2010 ; $year <= date('Y'); $year++){
                          echo "<option value='$year'>$year</option>";
                        }
                    {/php}>
                </select>
            </label>
{*            <input type="text" name="endDate" value="{$details.paymentDate}" class="form-datepicker" maxlength="10"/><span class="ui-helper-clearfix"></span>*}
        </div>
        <span class="ui-helper-clearfix"></span><br>
        <div>
            <input style="float: none;display: inline;margin:0; margin-left: 5px" type="checkbox" name="sms_dr" /> Include SMS awaiting DR
        </div>
        <span class="ui-helper-clearfix"></span><br>
        <br>
    </fieldset>

</form>