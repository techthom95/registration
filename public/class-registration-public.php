<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    registration
 * @subpackage registration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    registration
 * @subpackage registration/public
 * @author     Your Name <email@example.com>
 */
class registration_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $registration    The ID of this plugin.
	 */
	private $registration;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $db;
	/**
	 * @var $mollie \Mollie\Api\MollieApiClient
	 */
	private $mollie;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $registration       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $registration, $version ) {

		global $wpdb;

		$this->registration = $registration;
		$this->version = $version;
		$this->db = $wpdb;

		define('PLUGIN_DIR', plugins_url('/public/', dirname(__FILE__)));

		add_shortcode('crazycross', array($this, 'display'));
		add_shortcode('crazycross-betalen', array($this, 'display_payment'));
		add_action( 'rest_api_init', function () {
			register_rest_route('crazycross', '/mollie-webhook', [
				'methods' => 'GET, POST, PUT, PATCH',
				'callback' => [$this, 'mollie_webhook']
			]);
		});
		add_action('phpmailer_init', array($this, 'setMailHTML'));

		// set correct timezone
		date_default_timezone_set('Europe/Amsterdam');

		require_once plugin_dir_path( __FILE__ ) . '../assets/mollie-api-php/vendor/autoload.php';

		$mollieKey = $this->db->get_var("SELECT `value` FROM `cc_settings` WHERE `name` = 'mollie_key_bergeijk'");
		$this->mollie = new \Mollie\Api\MollieApiClient();
		$this->mollie->setApiKey($mollieKey);

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in registration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The registration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->registration, "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css", array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in registration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The registration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->registration, "https://code.jquery.com/jquery-1.11.3.min.js", array( 'jquery' ), $this->version, false );

	}

	public function mollie_webhook() {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

		if ($id && $transaction = $this->db->get_row($this->db->prepare("SELECT * FROM `cc_payment_transactions` WHERE `mollie_id` = %s", [$id]))) {
			try {
				$payment = $this->mollie->payments->get($transaction->mollie_id);
				$updated = $this->db->update('cc_payment_transactions', ['status' => $payment->status], ['id' => $transaction->id]);
				$group = $this->db->get_row($this->db->prepare("SELECT * FROM `cc_groups` WHERE `cc_groups`.`id` = %d", [$transaction->group_id]));
				$class = $this->db->get_row($this->db->prepare("SELECT * FROM `cc_classes` WHERE `cc_classes`.`id` = %d", [$group->class_id]));
				$participants = $this->db->get_results('
					SELECT
						*
					FROM
						`cc_participants`
					WHERE
						`group_id` = '. $group->id .'
					ORDER BY
						`id` ASC', ARRAY_A);
				$driver = $participants[0];

				if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
				
					// Determine startnrs
					$startNrs = array_column($this->db->get_results('
						SELECT
							`start_nr`
						FROM
							`cc_groups`
						WHERE
							`class_id` = '. $class->id .'
								AND
							`status` = 1
								AND
							`start_nr` IS NOT NULL
								AND
							`year` = '. date('Y') .'
						ORDER BY
							`start_nr` ASC', ARRAY_A), 'start_nr') ?: [];

					for ($i = 1; $i <= $class->max_groups; $i++) {
						if (!in_array($i, $startNrs)) {
							$startNr = $i;
							break;
						}
					}

					$this->db->update(
						'cc_groups',
						array(
							'status' => '1', // betaald
							'start_nr' => $startNr,
						),
						array('id' => $group->id)
					);
					
					// Generate pdf and mail
					if (!class_exists('HTML2PDF')) {

						require plugin_dir_path( __FILE__ ) . '../assets/html2pdf/html2pdf.class.php';
					}

					ob_start();
					require plugin_dir_path( __FILE__ ) . '../admin/partials/pdf/filled-out-form.php';
					$rawPdf = ob_get_clean();

					$pdfPath = plugin_dir_path( __FILE__ ) . '../temp/Crazy Cross deelnameformulier.pdf';

					$html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', [14,14,14,20]);
					$html2pdf->writeHTML($rawPdf);
					ob_clean();
					$html2pdf->Output($pdfPath, 'F');

					ob_start();
					require plugin_dir_path( __FILE__ ) . '../admin/partials/mails/payment-approved.php';
					$mailBody = ob_get_clean();

					$mailHeaders = [
						"From: Crazy Cross Bergeijk <info@crazycrossbergeijk.nl>",
						"Bcc: Crazy Cross Bergeijk <info@crazycrossbergeijk.nl>",
					];

					// Mail to customer
					wp_mail(
						$driver['email'],                         // To
						'Betaling geaccepteerd - '. str_replace('  ', ' ', $driver['first_name'] .' '. $driver['insertion'] .' '. $driver['last_name']), // Subject
						$mailBody,                              // Body
						$mailHeaders,                           // Headers
						$pdfPath
					);
					
				} elseif($payment->isFailed() || $payment->isExpired() || $payment->isCanceled() || $payment->hasRefunds() || $payment->hasChargebacks()) {

					// Add group to classe's queue
					$this->db->update('cc_groups', ['status' => '3'], ['id' => $group->id]);

					// Send email out about the failed payment
					$paymentUrl = get_permalink(2689) . '?groupId=' . $group->id;
					ob_start();
					require plugin_dir_path( __FILE__ ) . 'partials/mails/payment-request.php';
					$mailBody = ob_get_clean();

					$mailHeaders = [
						"From: Crazy Cross Bergeijk <info@crazycrossbergeijk.nl>",
						"Bcc: Crazy Cross Bergeijk <info@crazycrossbergeijk.nl>",
					];

					wp_mail(
						$driver['email'],						// To
						'Betaling mislukt - Crazy Cross Bergeijk - '. str_replace('  ', ' ', $driver['first_name'] .' '. $driver['insertion'] .' '. $driver['last_name']),
						$mailBody,								// Body
						$mailHeaders							// Headers
					);

				}
			} catch (\Mollie\Api\Exceptions\ApiException $e) {
				echo "API call failed: " . htmlspecialchars($e->getMessage());
			}
			exit();
		}
	}

	public function display_payment($attr, $tag) {
		if (isset($_GET['errors'])) {
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		}

		$groupId = isset($_GET['groupId']) ? $_GET['groupId'] : false;
		$group = $this->db->get_row(
			$this->db->prepare("SELECT `cc_groups`.*, COUNT(`cc_participants`.`id`) AS `num_participants` FROM `cc_groups` LEFT JOIN `cc_participants` ON `cc_participants`.`group_id` = `cc_groups`.`id` WHERE `cc_groups`.`id` = %d", [$groupId]),
			OBJECT
		);

		if (!$group || !$class = $this->db->get_row($this->db->prepare("SELECT * FROM `cc_classes` WHERE `id` = %d", [$group->class_id]))) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
			require plugin_dir_path( __FILE__ ) . 'partials/plugin-registration-display-payment.php';
			exit();
		}

		$skipPayment = false;
		$return = isset($_GET['return']);
		$mollieId = $this->db->get_var(
			$this->db->prepare("SELECT `mollie_id` FROM `cc_payment_transactions` WHERE `group_id` = %d ORDER BY id DESC", [
				$group->id
			])
		);
		
		if ($mollieId) {
			$payment = $this->mollie->payments->get($mollieId);
			$paymentStatus = $payment->status;
			if ($payment->isPaid()) {
				$skipPayment = true;
			}
		}

		if (!$skipPayment && !$return) {
			$totalAmount = round(($class->price_per_person * max((int) $group->num_participants, 1)) + $class->price_fixed, 2);
			
			/*
			* Determine the url parts to these example files.
			*/
			$protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
			$hostname = $_SERVER['HTTP_HOST'];
			$path = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);
					
			/*
			* Payment parameters:
			*   amount        Amount in EUROs. This example creates a € 10,- payment.
			*   description   Description of the payment.
			*   redirectUrl   Redirect location. The customer will be redirected there after the payment.
			*   webhookUrl    Webhook location, used to report when the payment changes state.
			*   metadata      Custom metadata that is stored with the payment.
			*/
			$payment = $this->mollie->payments->create([
				"amount" => [
					"currency" => "EUR",
					"value" => number_format($totalAmount, 2) // You must send the correct number of decimals, thus we enforce the use of strings
				],
				"description" => "Inschrijving Crazy Cross #{$group->id}",
				"redirectUrl" => "{$protocol}://{$hostname}{$path}/?groupId={$group->id}&return=true",
				"webhookUrl" => "{$protocol}://{$hostname}/wp-json/crazycross/mollie-webhook?groupId={$group->id}",
				"metadata" => [
					"id" => $group->id,
				],
			]);

			$saved = $this->db->insert(
				'cc_payment_transactions',
				array(
					'group_id' => $group->id,
					'status' => $payment->status,
					'currency' => $payment->amount->currency,
					'amount' => $totalAmount,
					'mollie_id' => $payment->id,
				)
			);

			if ($saved) {
				/*
				* Send the customer off to complete the payment.
				* This request should always be a GET, thus we enforce 303 http response code
				*/
				$this->redirect($payment->getCheckoutUrl());
			} else {
				$errors[] = 'Er ging iets mis. Probeer het opnieuw.';
			}
		}
		
		// Return the view
		require plugin_dir_path( __FILE__ ) . 'partials/plugin-registration-display-payment.php';
	}
	
	public function redirect($url = false)
	{
	  if(headers_sent())
	  {
		$destination = ($url == false ? 'location.reload();' : 'window.location.href="' . $url . '";');
		echo die('<script>' . $destination . '</script>');
	  }
	  else
	  {
		$destination = ($url == false ? $_SERVER['REQUEST_URI'] : $url);
		header('Location: ' . $destination);
		die();
	  }    
	}

	public function display($atts, $tag)
	{
		// normalize attribute keys, lowercase
		$atts = array_change_key_case((array) $atts, CASE_LOWER);
		$atts = shortcode_atts([
			'locatie' => 'bergeijk',
		], $atts, $tag);
		$location = $atts['locatie'];

        $settingsRaw = $this->db->get_results("
            SELECT
                *
            FROM
                `cc_settings`
        ", OBJECT);

        $settings = [];
        foreach ($settingsRaw as $setting) {
            $settings[$setting->name] = $setting->value;
        }
		$settings = (object) $settings;

		// Check if post
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

			$class_id = (int) $_POST['class_id'];

			// Check if given class id exists
			if ($class = $this->db->get_row("SELECT * FROM `cc_classes` WHERE `id` = $class_id")) {

				if ($_POST['terms_accepted']) {

                    $numGroups = $this->db->get_var(
						$this->db->prepare("SELECT COUNT(`id`) FROM `cc_groups` WHERE `year` = %d AND `location` = %s AND `class_id` = %d", [
							date('Y'),
							$location,
							$class->id
						])
					);
                    $status = $numGroups >= $class->max_groups ? 3 : 2;

					$participants = array();
					foreach ($_POST['participants'] as $key => $participant) {

						foreach ($participant as $key => $data) {
							$participant[$key] = htmlentities(trim($data));
						}

						if ($participant['email'] == '') {
							continue;
						}

						$imageKey = array_search($participant, $_POST['participants']);

                        $error = false;

						//if they DID upload a file...
						if ($class->driving_license_upload && $_FILES['participants']['name'][$imageKey]) {

							//if no errors...
							if (!$_FILES['participants']['error'][$imageKey]['driving_license_file']) {

								//now is the time to modify the future file name and validate the file
								$new_file_name = strtolower($_FILES['participants']['tmp_name'][$imageKey]['driving_license_file']); //rename file
								if ($_FILES['participants']['size'][$imageKey]['driving_license_file'] > (1024000)) {
									$valid_file = false;
									$error = 'Het bestand is te groot. (' . $_FILES['participants']['name'][$imageKey]['driving_license_file'] . ')';
								}

                                // validate file type
                                $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, 'application/pdf');
                                $detectedType = exif_imagetype($_FILES['participants']['tmp_name'][$imageKey]['driving_license_file']);

                                if (!in_array($detectedType, $allowedTypes)) {
                                    $valid_file = false;
                                    $error = 'Het bestand type is onjuist. (' . $_FILES['participants']['name'][$imageKey]['driving_license_file'] . ')';
                                }

								//if the file has passed the test
								if ($valid_file !== false) {
									$extention = array_pop(explode('.', $_FILES['participants']['name'][$imageKey]['driving_license_file']));
									$filename = plugin_dir_path(__FILE__) . 'uploads/' . $this->makeRandomString(12) . '.' . $extention;

									//move it to where we want it to be
									move_uploaded_file($_FILES['participants']['tmp_name'][$imageKey]['driving_license_file'], $filename);
								}
							} else {
								//set that to be the returned message
								$error = 'Er is een fout opgetreden, error code:  '.$_FILES['participants']['error'][$imageKey]['driving_license_file'];
							}
						}


						$participant_data = array(
								'first_name' => ucfirst(strtolower($participant['first_name'])),
								'insertion' => strtolower($participant['insertion']),
								'last_name' => ucfirst(strtolower($participant['last_name'])),
								'address' => ucwords(strtolower($participant['address'])),
								'zipcode' => strtoupper($participant['zipcode']),
								'city' => ucwords(strtolower($participant['city'])),
								'date_of_birth' => date('Y-m-d', strtotime($participant['date_of_birth'])),
								'notice' => ucfirst($participant['notice']),
								'phonenumber' => $participant['phonenumber'],
								'email' => strtolower($participant['email']),
						);

						if ($class->driving_license) {
                            $participant_data['driving_license_nr'] = $participant['driving_license_nr'];
                        }

                        if ($class->driving_license_upload) {
							$participant_data['driving_license_path'] = $filename;
						}

                        $participants[] = $participant_data;
					}


                    if ($error === false) {
                        // Insert group
                        $group = $this->db->insert('cc_groups',
                            array(
                                'class_id' => htmlentities($class->id),
                                'theme' => htmlentities(ucfirst(trim($_POST['theme']))),
                                'date' => htmlentities(trim($_POST['date'])),
                                'year' => date('Y'),
                                'location' => $location,
                                'status' => $status,
                            )
                        );
                    }

                    if ($group && $error === false) {
                        $group_id = $this->db->insert_id;

                        // save participants
                        $unsaved = array_walk($participants, function($participant) use ($group_id) {
                            $participant['group_id'] = $group_id;
                             return !$this->db->insert('cc_participants', $participant);
                        });

                        $saved = true;
                    } else {
                        $saved = false;
                    }

					$queueWarning = false;
                    if ($status == 3 && $saved) {
                        $queueWarning = true;
                        $success = "Uw aanmelding is succesvol ontvangen! <br><br><b>De klasse waarvoor u zich heeft aangemeld zit echter vol. U bent op de wachtlijst geplaatst.</b>";
                    } elseif ($saved) {
                        $success = 'Uw aanmelding is succesvol ontvangen!';
                    } elseif ($error === false) {
                        $error = 'Er is iets mis gegaan tijdens het opslaan. Probeer het opnieuw.';
                    }

                    $driver = array_shift($_POST['participants']);

					// Redirect to payment if class is not full
					if (!$queueWarning && $saved) {
						$paymentUrl = get_permalink(2689) . 'betalen/?groupId=' . $group_id;
						$this->redirect($paymentUrl);
						// header('Location: ' . $paymentUrl, true, 302);
					}


					if ($saved) {
						// Mail to Crazy Cross
						ob_start();
						require plugin_dir_path( __FILE__ ) . 'partials/mails/new-registration.php';
						$mailBody = ob_get_clean();
						$mailHeaders = [
							"From: Crazy Cross Bergeijk <info@crazycrossbergeijk.nl>",
						];

						wp_mail(
							'info@crazycrossbergeijk.nl',			// To
							'Nieuwe aanmelding - '. str_replace('  ', ' ', $driver['first_name'] .' '. $driver['insertion'] .' '. $driver['last_name']), // Subject
							$mailBody,								// Body
							$mailHeaders							// Headers
						);
					}
				}
			}
		}

		// Load all the stuff
		$classes = $this->db->get_results(
			$this->db->prepare('SELECT * FROM `cc_classes` WHERE `location` = %s', [$location]),
			OBJECT
		);

		// Return the view
		require plugin_dir_path( __FILE__ ) . 'partials/plugin-registration-display.php';
	}

    private function makeRandomString($max=6) {
        $i = 0; //Reset the counter.
        $possible_keys = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $keys_length = strlen($possible_keys);
        $str = ""; //Let's declare the string, to add later.
        while($i<$max) {
            $rand = mt_rand(1,$keys_length-1);
            $str.= $possible_keys[$rand];
            $i++;
        }
        return $str;
    }

	public function setMailHTML($phpmailer) {
		$phpmailer->isHTML(true);
        $phpmailer->AltBody = strip_tags($phpmailer->Body);
	}
}
