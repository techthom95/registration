<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    registration
 * @subpackage registration/public/partials
 */
?>

<?php 
if (isset($paymentStatus)):
	echo '<p>';
	switch ($paymentStatus) {
		case 'paid':
			if ($return) {
				echo 'Betaling succesvol afgerond.<br>U ontvant een email met het ingevulde registratieformulier. U kunt deze pagina sluiten, hartelijk bedankt!';
			} else {
				echo 'Betaling is reeds voldaan.<br>Hartelijk bedankt!';
			}
		break;
		case 'canceled':
			echo 'De betaling is geannuleerd.';
		break;
		case 'expired':
			echo 'De betaling is verlopen.';
		break;
		default:
		case 'failed':
			echo 'De betaling is mislukt. Probeer het later opnieuw.';
		break;
	}
	echo '</p>';
else:
	echo '<p> Betalen is niet mogelijk. Er mist informatie om de betaling te starten. </p>';
endif;
?>