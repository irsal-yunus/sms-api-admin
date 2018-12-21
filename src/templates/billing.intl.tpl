<fieldset class="content">
    <table class="admin-simpletable invoice-table border-inside" id="billing-intl-table">
        <thead>
            <tr>
                <th class="type-nav" colspan="5">
                    <a class="form-button" href="#" onclick="$app.module('billing').formIntlPrice();">
                        <span class="form-button-text">
                            New Price
                        </span>
                    </a>
                </th>
            </tr>
            <tr>
                <th style="width: 100px;">
                    Country Code
                </th>
                <th>
                    Country Name
                </th>
                <th style="width: 100px;">
                    Prefix number
                </th>
                <th style="width: 20%;">
                    Price
                </th>
                <th style="width: 30%;">
                    Action(s)
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th colspan="9">
                </th>
            </tr>
        </tfoot>
        <tbody>
            <tr class="{cycle values=" zebra-odd,zebra-even "}">
                <td class="type-action">
                    DEFAULT
                </td>
                <td class="type-text">
                    DEFAULT
                </td>
                <td class="type-action">
                    -
                </td>
                <td class="type-status">
                    {$defaultPrice->unitPrice}
                </td>
                <td class="type-action">
                    <a class="form-button" href="#" onclick="$app.module('billing').formIntlPrice({$defaultPrice->key()})" title="Edit">
                        <img alt="" class="icon-image" src="skin/images/icon-edit.png"/>
                    </a>
                </td>
            </tr>
            {foreach $prices as $index => $price}
            <tr class="{cycle values=" zebra-odd,zebra-even "}">
                <td class="type-action">
                    {$price->countryCode}
                </td>
                <td class="type-text">
                    {$price->countryName}
                </td>
                <td class="type-action">
                    +{$price->callingCode}
                </td>
                <td class="type-status">
                    {$price->unitPrice}
                </td>
                <td class="type-action">
                    <a class="form-button" href="#" onclick="$app.module('billing').formIntlPrice({$price->key()})" title="Edit Price">
                        <img alt="" class="icon-image" src="skin/images/icon-edit.png"/>
                    </a>
                    <a class="form-button" href="#" onclick="$app.module('billing').deleteIntlPrice({$price->key()})" title="Delete Price">
                        <img alt="" class="icon-image" src="skin/images/icon-remove.png"/>
                    </a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</fieldset>
