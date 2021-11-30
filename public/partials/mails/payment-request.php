<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Aanmelding Crazy Cross Bergeijk</title>
</head>
<body>
    <img src="<?php echo PLUGIN_DIR . 'img/cc-logo.png'; ?>" style="width:275px;" alt="Crazy Cross Bergeijk logo"><br>
    <br>
    Dag <?php echo $driver['first_name']; ?>,<br>
    <p>
        Bedankt voor je aanmelding voor Crazy Cross Bergeijk.
    </p>
    <p>
        <?php echo nl2br($class->price_text); ?>
    </p>

    <?php
        if (isset($paymentUrl)):
    ?>
        <p>
            <a href="<?php echo $paymentUrl; ?>"><?php echo $paymentUrl; ?></a>
        </p>
    <?php
        endif;
    ?>

    Met vriendelijke groet,<br>
    Het Crazy Cross team
</body>
</html>