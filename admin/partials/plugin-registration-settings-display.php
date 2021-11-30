<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    registration
 * @subpackage registration/admin/partials
 */
?>

<?php if (isset($_GET['success'])): ?>
    <div class="updated"><p>De instellingen zijn opgeslagen!</p></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <ul class="errors">
    <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="wrap">
    <h1>Instellingen</h1>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>&noheader=true" method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <td scope="row">
                        <label for="crazy-cross-date"><strong>Crazy Cross datum</strong></label><br />
                        <small>De datum wanneer de volgende Crazy Cross plaats vindt.<br />
                        Dit wordt gebruikt om de leeftijd van deelnemers te controlleren.</small>
                    </td>
                    <td><input name="crazy_cross_date" type="date" id="crazy-cross-date" value="<?php echo $settings->crazy_cross_date ?: date('Y-m-d'); ?>" class="regular-text ltr"></td>
                </tr>
                <tr>
                    <td scope="row">
                        <label for="mollie-key-bergeijk"><strong>Mollie Key - Bergeijk</strong></label><br />
                        <small>Deze key wordt gebruikt om de connectie te leggen met Mollie.<br>Doormiddel van Mollie worden de betalingen verwerkt van de inschrijvingen.</small>
                    </td>
                    <td><input name="mollie_key_bergeijk" type="text" id="mollie-key-bergeijk" value="<?php echo $settings->mollie_key_bergeijk; ?>" class="regular-text ltr"></td>
                </tr>
            </tbody>
        </table>
        <br>
        <input type="submit" value="Opslaan" class="button button-primary button-large" />
    </form>
</div>