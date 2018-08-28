<form id="apiuser-editform" class="report-tabform" action="services/billing.downloadReport.php" method="get" style="width: 100%;">
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
                    {html_options options=$dates selected=(date('m'))}
                </select>
            </label>

            <label class="form-flag-required">Year
                 <select name="year" style="float:none;display: inline">
                    <!--<option value="">- Select -</option>-->
                    {php}
                        for($year = 2017 ; $year <= date('Y'); $year++){
                            $selected = $year === date('Y');
                            echo "<option value='$year' selected='$selected'>$year</option>";
                        }
                    {/php}>
                </select>
            </label>
        </div>
        <span class="ui-helper-clearfix"></span><br>
        <span class="ui-helper-clearfix"></span><br>
        <br>
    </fieldset>

</form>
