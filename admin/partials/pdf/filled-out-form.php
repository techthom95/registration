<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Deelnameformulier</title>
    <style>
        table {
            border-bottom:1px solid #D41409;
        }
        td {
            padding:8px;
        }
        tr.heading td {
            border-bottom:2px solid #D41409;
            font-size:16px;
        }
        tr.odd td {
            background-color:#F4B5B0;
        }
        
        table.important {
            border:none;
        }
        table.important td {
            padding:3px;
            font-size:16px;
        }

        .max-width {
            width:418px;
            max-width:418px;
        }
        table.important .max-width {
            width:488px;
            max-width:488px;
        }
    </style>
</head>
<body>
    <img src="<?php echo PLUGIN_DIR. 'img/cc-logo.png'; ?>" style="max-width:50%; float:left;" alt="Crazy Cross Bergeijk logo">
    <h1 style="text-align:center; font-size:36px; line-height:44px; color:#fc6310;">Deelname<br><?php echo date('Y'); ?></h1><br>
    <br>
    <div>
        <table cellpadding="0" cellspacing="0" class="important">
            <tr><td width="130">&nbsp;</td><td width="488">&nbsp;</td></tr>
            <tr>
                <td valign="top"><b>Locatie:</b></td>
                <td><b><?php echo ucfirst($class->location); ?></b></td>
            </tr>
            <tr>
                <td valign="top"><b>Klasse:</b></td>
                <td><b><?php echo $class->name; ?></b></td>
            </tr>
            <tr>
                <td valign="top"><b>Startnummer:</b></td>
                <td><b><?php echo $startNr; ?></b></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td valign="top">Deelnemen op:</td>
                <td><?php echo ucfirst($group->date); ?></td>
            </tr>
            <tr>
                <td valign="top">Thema:</td>
                <td style="word-wrap:break-word;" class="max-width"><?php echo nl2br($group->theme); ?></td>
            </tr>
        </table>
    </div>
    
    <?php for ($i = 0; $i < count($participants); $i++): ?>
    <div>
        <table cellpadding="0" cellspacing="0">
            <?php $participant = $participants[$i]; ?>
            <tr><td width="200">&nbsp;</td><td width="418">&nbsp;</td></tr>
            <tr class="heading">
                <td colspan="2"><b><?php echo $i == 0 ? 'Bestuurder' : 'Bijrijder '.$i; ?></b></td>
            </tr>
            <tr class="odd">
                <td valign="top">Naam:</td>
                <td class="max-width"><?php echo str_replace('  ', ' ', $participant['first_name'] . ' ' . $participant['insertion'] . ' ' . $participant['last_name']); ?></td>
            </tr>
            <tr class="even">
                <td valign="top">Adres:</td>
                <td class="max-width"><?php echo $participant['address']; ?></td>
            </tr>
            <tr class="odd">
                <td valign="top">Postcode + plaats:</td>
                <td class="max-width"><?php echo $participant['zipcode'] . ' ' . $participant['city']; ?></td>
            </tr>
            <tr class="even">
                <td valign="top">Geboortedatum:</td>
                <td class="max-width"><?php echo date('d-m-Y', strtotime($participant['date_of_birth'])); ?></td>
            </tr>
            <tr class="odd">
                <td valign="top">Emailadres:</td>
                <td class="max-width"><a href="<?php echo $participant['email']; ?>"><?php echo $participant['email']; ?></a></td>
            </tr>
            <tr class="even">
                <td valign="top">Telefoonnummer:</td>
                <td class="max-width"><?php echo $participant['phonenumber']; ?></td>
            </tr>
            <?php if ($class->driving_license): ?>
                <tr class="odd">
                    <td valign="top">Rijbewijsnummer:</td>
                    <td class="max-width"><?php echo $participant['driving_license_nr']; ?></td>
                </tr>
            <?php endif; ?>
            <tr class="<?php echo $class->driving_license ? 'even' : 'odd'; ?>">
                <td valign="top">Opmerking:</td>
                <td class="max-width"><?php echo $participant['notice']; ?></td>
            </tr>
        </table>
    </div>
    <?php endfor; ?>
</body>
</html>