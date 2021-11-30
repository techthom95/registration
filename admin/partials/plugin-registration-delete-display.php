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

<?php if ($deleted): ?>
    
    <br />
    <div class="updated"><p>De klasse is succesvol verwijderd!</p></div><br />
    <a href="<?php echo $overview_url; ?>" class="button">Terug naar overzicht</a>
<?php else: ?>

    <br />
    <div class="error"><p>Er is iets mis gegaan tijdens het verwijderen. Probeer het later opnieuw.</p></div><br />
    <a href="<?php echo $overview_url; ?>" class="button">Terug naar overzicht</a>
<?php endif; ?>