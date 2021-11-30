<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Nieuwe aanmelding</title>
</head>
<body>
    <img src="<?php echo PLUGIN_DIR. 'img/cc-logo.png'; ?>" style="width:275px;" alt="Crazy Cross Bergeijk logo"><br>
    <br>
    Dag,<br>
    <br>
    Er is een nieuwe aanmelding voor Crazy Cross:<br>
    <br>
    <table width="100%">
        <tr>
            <th width="15%"></th>
            <th></th>
        </tr>
        <tr>
            <td valign="top">Locatie:</td>
            <td><?php echo ucfirst($class->location); ?></td>
        </tr>
        <tr>
            <td valign="top">Klasse:</td>
            <td><?php echo $class->name; ?></td>
        </tr>
        <tr>
            <td valign="top">Datum:</td>
            <td><?php echo $_POST['date']; ?></td>
        </tr>
        <tr>
            <td valign="top">Thema:</td>
            <td><?php echo $_POST['theme']; ?></td>
        </tr>
        <?php for ($i = 0; $i < count($participants); $i++): ?>
            <?php $participant = $participants[$i]; ?>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td colspan="2"><b><?php echo $i == 0 ? 'Bestuurder' : 'Bijrijder '.$i; ?></b></td>
            </tr>
            <tr>
                <td valign="top">Naam:</td>
                <td><?php echo str_replace('  ', ' ', $participant['first_name'] . ' ' . $participant['insertion'] . ' ' . $participant['last_name']); ?></td>
            </tr>
            <tr>
                <td valign="top">Adres:</td>
                <td><?php echo $participant['address']; ?></td>
            </tr>
            <tr>
                <td valign="top">Postcode + plaats:</td>
                <td><?php echo $participant['zipcode'] . ' ' . $participant['city']; ?></td>
            </tr>
            <tr>
                <td valign="top">Geboortedatum:</td>
                <td><?php echo date('d-m-Y', strtotime($participant['date_of_birth'])); ?></td>
            </tr>
            <tr>
                <td valign="top">Emailadres:</td>
                <td><a href="mailto:<?php echo $participant['email']; ?>"><?php echo $participant['email']; ?></a></td>
            </tr>
            <?php if (!empty($participant['driving_license_nr'])): ?>
                <tr>
                    <td valign="top">Rijbewijsnummer:</td>
                    <td><?php echo $participant['driving_license_nr']; ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td valign="top">Telefoonnummer:</td>
                <td><?php echo $participant['phonenumber']; ?></td>
            </tr>
            <tr>
                <td valign="top">Opmerking:</td>
                <td><?php echo $participant['notice']; ?></td>
            </tr>
        <?php endfor; ?>
    </table>
    <br>
    Met vriendelijke groet,<br>
    Het Crazy Cross team
</body>
</html>