<form id="apiuser-editform" class="report-tabform" action="services/downloadAllBillingReport.php" method="GET" style="width: 100%;">
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
                    {php}
                    	for($year=date('Y'); $year > 2015; $year--){
                            echo '<optgroup label="'.$year.'">';
                            $startMonth = date('Y') == $year ? date('m') : 12;
                            for($month  = $startMonth; $month>0; $month--){
                                
                                echo   '<option' 
                                            .' value="'.$year.'-'.sprintf('%02d', $month).'"'
                                            . (!( sprintf('%02d', $month) == date('m') && $year == date('Y') )  ?  '' : 'selected')
                                            .' style="padding-left: 15px;"'
                                        .'>'
                                            .(DateTime::createFromFormat('m', $month)->format('F'))
                                        .'</option>';
                            }

                            echo '</optgroup>';
                        }
                    {/php}>
                </select>
            </label>
        </div>
        <span class="ui-helper-clearfix"></span><br><br>
    </fieldset>

</form>
