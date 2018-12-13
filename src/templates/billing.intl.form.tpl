<form action="billing.intl.store" class="admin-xform">
    {if isset($price)}
        <input type="hidden" name="{$price->keyName()}" value="{$price->key()}" />
    {/if}
    <fieldset class="float-centre">
        <legend>International Price</legend>
        <label class="form-flag-required">Country</label>
        {if isset($price) and $price->countryCodeRef === $price::DEFAULT_PRICE_COUNTRY_CODE}
            <select name="countryCodeRef" class="flexible-width" id="countryCode" disabled>
                <option value="{$price->countryCodeRef}"> DEFAULT </option>
            </select>
            <input type="hidden" name="countryCodeRef" value="{$price->countryCodeRef}" />
        {else}
            <select name="countryCodeRef" class="flexible-width" id="countryCode">

                {foreach $countries as $country}

                    <option
                        value="{$country->COUNTRY_CODE_REF}"
                        {if isset($price) && $price->countryCodeRef === $country->COUNTRY_CODE_REF} selected {/if}
                        {if $country->BILLING_INTERNATIONAL_PRICE_ID !== null} disabled {/if}
                    >
                    {$country->COUNTRY_NAME} (+{$country->PHONE_CODE})
                    </option>
                {/foreach}
            </select>
        {/if}
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">SMS Price (IDR)</label>
        <input name="unitPrice" data-mask="000,000,000,000,000.00" value="{(isset($price))?$price->unitPrice : ''}" type="text" maxlength="15" />
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
