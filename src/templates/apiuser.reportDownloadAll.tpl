<form id="apiuser-editform" class="report-tabform" action="services/billing.downloadAllReport.php" method="GET" style="width: 100%;">
    <fieldset class="float-centre">
        <span class="ui-helper-clearfix"></span><br>
        <div>
            <p style="padding: 5px;">
              Download all available reports for clients,<br>
              not including SMS sent in the last 3 days.
            </p>
          <span class="ui-helper-clearfix"></span>

          <label class="form-flag-required">
                 Select the billing period:
                 <select name="period" style="float:none;display: inline; width:100px;">
                    {foreach from="$availablePeriods" item="months" key="year"}
                        <optgroup label="{$year}">
                            {foreach from=$months item="label" key="value"}
                                <option value="{$value}"  style="padding-left: 15px;" >{$label}</option>
                            {/foreach}
                        </optgroup>
                    {/foreach}
                </select>
            </label>
            <span class="ui-helper-clearfix"></span><br>

            </div>
            <span class="ui-helper-clearfix"></span><br><br>
    </fieldset>

</form>
