<form action="services/invoice.downloadAll.action.php" method="POST" class="admin-xform">
    <fieldset class="float-centre">
        <legend>Download Invoice</legend>
        <label class="form-flag-required">Invoice Date</label>
        <select name="period" size="1" class="flexible-width">
            {html_options options=$dates selected=$current}
        </select>
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
