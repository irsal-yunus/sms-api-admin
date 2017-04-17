<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

<html>
<head>
<title>SMS Billing</title>
<style type="text/css">
div {
left: 250px;
top: 200px;
width: 280px;
padding: 10px;
color: black;
display: none;
}
</style>
<script language="JavaScript">

function setVisibilityBillNo(id) {
    if(document.getElementById('idbillno').value=='HideBillNo'){
        document.getElementById('idbillno').value = 'ShowBillNo';
        document.getElementById(id).style.display = 'none';
    }else{
        document.getElementById('idbillno').value = 'HideBillNo';
        document.getElementById(id).style.display = 'inline';
    }
}

function setVisibilityDeliv(id) {
    if(document.getElementById('bt2').value=='Hide Layer'){
        document.getElementById('bt2').value = 'Show Layer';
        document.getElementById(id).style.display = 'none';
    }else{
        document.getElementById('bt2').value = 'Hide Layer';
        document.getElementById(id).style.display = 'inline';
    }
}

function ShowProvider(id_provider) {
    if(document.getElementById('idProvider').value=='Hide Layer'){
        document.getElementById('idProvider').value = 'Show Layer';
        document.getElementById(id_provider).style.display = 'none';
    }else{
        document.getElementById('idProvider').value = 'Hide Layer';
        document.getElementById(id_provider).style.display = 'inline';
    }
}

</script>
</head>
<body>
        <a href="index.php">Back</a>
        <p>Nama File  : <?php echo $_GET['filename']; ?></p></p>
        <p>Filter By * : </p>
        <input type="Checkbox" name="billsms" value ="Firefox">BILLED_SMS(Y)<br>
        <input type=checkbox name="billsms" id='idbillno' value='ShowBillNo' onclick="setVisibilityBillNo('subidBillno');";>BILLED_SMS(N)<br>
        <div id="subidBillno"><input type="text" name="textline" size="20"/><br></div>
        <INPUT TYPE="Checkbox" Name= "Browser" Value ="Opera ">ERROR_CODE<br>
        <br>
        Header:<br>
        <table height="10">
            <tr>
                <td valign="top">
        <input type="Checkbox" Name="Browser" Value ="Firefox">UNKNOWN<br>
        <input type="Checkbox" Name="Browser" Value ="Firefox">PENDING<br>
        <input type="Checkbox" Name="Browser" Value ="Firefox">UNDELIVERED<br>
        <input type=checkbox name=type id='bt2' value='Show Layer' onclick="setVisibilityDeliv('del');";>DELIVERED
        <div id="del"><table>
                <tr>
                    <td align="right">
                        <font size="2" color="black">Include</font>
                    </td>
                </tr>
                <tr>
                        <td>
                            <input type="text" name="textline" size="15"/>
                        </td>
                    </tr>
                </table><br></div>
             </td>

                <td width="30%"></td>
                <td valign="top">
        <input type="Checkbox" Name="Browser" Value ="Firefox">TOTAL SMS<br>
        <input type="Checkbox" Name="Browser" Value ="Firefox">TOTAL CHARGE<br>
        <input type=checkbox name=type id='idProvider' value='Show Layer' onclick="ShowProvider('id_provider');";>PROVIDER

        <div id="id_provider" >
          <table size="5"><tr><td >
            <input type="Checkbox" Name="Browser" Value ="Firefox">Tsel<br>
            <input type="Checkbox" Name="Browser" Value ="Firefox">Satelindo<br>
            <input type="Checkbox" Name="Browser" Value ="Firefox">IM3<br>
            <input type="Checkbox" Name="Browser" Value ="Firefox">XL<br>
            </td>
            <td>
            <input type="Checkbox" Name="Browser" Value ="Firefox">Three<br>
            <input type="Checkbox" Name="Browser" Value ="Firefox">Fren<br>
        <input type="Checkbox" Name="Browser" Value ="Firefox">Axis<br>
        <input type="Checkbox" Name="Browser" Value ="Firefox">Smart<br>
        </div>
                </td>

        </tr>
        </table>

        </td>
        </tr>
        </table>
        <table>
            <tr>
                <td width="100"></td>
                <td width="300"></td>
                <td align="right">
                <input type="submit" id = "submit" value="EXPORT"/><br>
                </td>
            </tr>
        </table>
        <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
      <script src="http://jqueryvalidation.org/files/dist/jquery.validate.min.js"></script>
      <script src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
      <script>
      // avoids form submit
      $("#submit").on("click",function(){
          if (($("input[name*='billsms']:checked").length)<=0) {
              alert("You must check at least 1 of Billed SMS");
          }
          return true;
      });
      </script>
</body>
</html>
