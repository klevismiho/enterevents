<html>

<head>
    <title>3D PAY</title>
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="now">
</head>

<body>
    <?php
    $clientId = "520039319";
    $amount = "10";
    $oid = rand();
    $okUrl = "https://enterevents.al/bkt-res.php";
    $failUrl = "https://enterevents.al/bkt-res.php";
    $rnd = microtime();
    $trantype = "Auth";
    $storekey = "SKEY1596";
    $hashstr = $clientId . $oid . $amount . $okUrl . $failUrl . $trantype . $rnd . $storekey;
    $hash = base64_encode(pack('H*', sha1($hashstr)));
    ?>
    <center>
        <form method="post" action="https://pgw.bkt.com.al/fim/est3Dgate">
            <table>
                <tr>
                    <td>Credit Card Number</td>
                    <td><input type="text" name="pan" size="20" />
                </tr>

                
                <tr>
                    <td>CVV</td>
                    <td><input type="text" name="cv2" size="4" value="" /></td>
                </tr>
                <tr>
                    <td>Expiration Date Year</td>
                    <td><input type="text" name="Ecom_Payment_Card_ExpDate_Year" value="" /></td>
                </tr>
                <tr>
                    <td>Expiration Date Month</td>
                    <td><input type="text" name="Ecom_Payment_Card_ExpDate_Month" value="" /></td>
                </tr>
                <tr>
                    <td>Choosing Visa Master Card</td>
                    <td><select name="cardType">
                            <option value="1">Visa</option>
                            <option value="2">MasterCard</option>
                        </select>
                </tr>
                <tr>
                    <td align="center" colspan="2">
                        <input type="submit" value="Complete Payment" />
                    </td>
                </tr>
            </table>
            <input type="hidden" name="clientid" value="<?php echo $clientId ?>">
            <input type="hidden" name="amount" value="<?php echo $amount ?>">
            <input type="hidden" name="oid" value="<?php echo $oid ?>">
            <input type="hidden" name="okUrl" value="<?php echo $okUrl ?>">
            <input type="hidden" name="failUrl" value="<?php echo $failUrl ?>">
            <input type="hidden" name="rnd" value="<?php echo $rnd ?>">
            <input type="hidden" name="hash" value="<?php echo $hash ?>">
            <input type="hidden" name="trantype" value="<?php echo $trantype ?>">
            <input type="hidden" name="storetype" value="3d_pay_hosting">
            <input type="hidden" name="lang" value="en">
            <input type="hidden" name="currency" value="008">
            <input type="hidden" name="encoding" value="utf-8" />
        </form>
    </center>
</body>

</html>