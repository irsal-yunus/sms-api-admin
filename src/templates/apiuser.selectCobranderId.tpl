{literal}
    <script type="text/javascript">
        function selectCobranderID(data){
                $('#cobranderId').val(data);
                $('.containerDialog').remove();
            };
        $(document).ready(function() {
            $('#tableCobrander').dataTable({
                "sPaginationType": "full_numbers",
                "bLengthChange": false,
                "bInfo": false
            });
        });
    </script> 
{/literal}    

<table id="tableCobrander">
    <thead>
        <tr>
            <th>Cobrander Id</th>
            <th>Country</th>
            <th>Company</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        {section name=data loop=$datas}
            <tr>
                <td>{$datas[data].cobranderId}</td>
                <td>{$datas[data].cobranderCountry}</td>
                <td>{$datas[data].companyName}</td>
                <td><button onclick="selectCobranderID('{$datas[data].cobranderId}')" style="height: auto;">Select</button></td>
            </tr>
        {/section}
    </tbody>
</table>