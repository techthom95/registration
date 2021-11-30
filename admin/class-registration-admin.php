<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 * @package    registration
 * @subpackage registration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    registration
 * @subpackage registration/admin
 * @author     Your Name <email@example.com>
 */
class registration_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $registration The ID of this plugin.
     */
    private $registration;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    private $db;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $registration The name of this plugin.
     * @param      string $version      The version of this plugin.
     */
    public function __construct($registration, $version)
    {

        global $wpdb;

        $this->registration = $registration;
        $this->version = $version;
        $this->db = $wpdb;

        add_filter('query', 'wpse_143405_query');

        add_action('admin_menu', array($this, 'load_menu'));

        // set correct timezone
        date_default_timezone_set('Europe/Amsterdam');
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->registration, plugin_dir_url(__FILE__) . 'css/registration-admin.css', array(), $this->version, 'all');
        wp_enqueue_style('datatable', "//cdn.datatables.net/1.10.10/css/jquery.dataTables.min.css", array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         * An instance of this class should be passed to the run() function
         * defined in registration_Loader as all of the hooks are defined
         * in that particular class.
         * The registration_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->registration, plugin_dir_url(__FILE__) . 'js/registration-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->registration, "//cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js", array('dataTables'), $this->version, false);

    }

    public function load_menu()
    {
        add_menu_page('Crazy Cross', 'Crazy Cross', 'manage_options', 'classes', array($this, 'display_admin'),
            'dashicons-clipboard', 25);
        add_submenu_page('classes', 'Klasses', 'Klasses', 'manage_options', 'classes', array($this, 'display_admin'));
        add_submenu_page('classes', 'Inschrijvingen', 'Inschrijvingen', 'manage_options', 'registrations',
            array($this, 'display_registrations'));
        add_submenu_page('classes', 'Exporteren', 'Exporteren', 'manage_options', 'registration_export',
            array($this, 'display_export'));
        add_submenu_page('classes', 'Instellingen', 'Instellingen', 'manage_options', 'registration_settings',
            array($this, 'display_settings'));
    }

    public function display_admin()
    {
        $errors = array();

        if (isset($_GET['action'])) {

            switch ($_GET['action']) {

                case 'new':

                    $errors = array();

                    if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

                        $dates = json_encode(explode("\n", $_POST['dates']));
                        $adult = isset($_POST['adult']) ? 1 : 0;
                        $driving_license = isset($_POST['driving_license']) ? 1 : 0;
                        $driving_license_upload = isset($_POST['driving_license_upload']) ? 1 : 0;

                        if (
                            !empty($_POST['name']) &&
                            !empty($_POST['intro']) &&
                            !empty($_POST['footer_note']) &&
                            !empty($_POST['num_participants']) &&
                            !empty($_POST['max_groups']) &&
                            !empty($_POST['dates']) &&
                            (!empty($_POST['price_per_person']) || !empty($_POST['price_fixed']))
                        ) {

                            if (!isset($_POST['price_text'])) {
                                $_POST['price_text'] = '';
                            }

                            $ageLimit = (int) $_POST['age_limit'];
                            $ageLimitType = $_POST['age_limit_type'];

                            if (!isset($_POST['age_limit_active'])) {
                                $ageLimit = 'NULL';
                                $ageLimitType = 'NULL';
                            }

                            $saved = $this->db->insert(
                                'cc_classes',
                                array(
                                    'location' => $_POST['location'],
                                    'name' => $_POST['name'],
                                    'intro' => $_POST['intro'],
                                    'num_participants' => (int) $_POST['num_participants'],
                                    'max_groups' => (int) $_POST['max_groups'],
                                    'footer_note' => $_POST['footer_note'],
                                    'price_text' => $_POST['price_text'],
                                    'dates' => $dates,
                                    'age_limit' => $ageLimit,
                                    'age_limit_type' => $ageLimitType,
                                    'driving_license' => $driving_license,
                                    'driving_license_upload' => $driving_license_upload,
                                    'price_per_person' => floatval($_POST['price_per_person']),
                                    'price_fixed' => floatval($_POST['price_fixed']),
                                )
                            );

                            if ($saved) {
                                echo "
                                    <script>
                                        window.location.href = '". str_replace('new', 'edit', $_SERVER['REQUEST_URI']).'&id='. $this->db->insert_id ."&success=true';
                                    </script>
                                ";
                            } else {
                                $errors[] = 'Er ging iets mis. Probeer het opnieuw.';
                            }

                        } else {
                            $errors[] = 'Missende onderdelen. Controleer of de volgende velden gevuld zijn: Klasse naam, Intro tekst, Aantal deelnemers, Voet tekst & Datums.';
                        }
                    }

                    require plugin_dir_path(__FILE__) . 'partials/plugin-registration-new-display.php';
                    break;
                case 'edit':

                    $errors = array();

                    if (isset($_GET['id'])) {

                        $id = (int) $_GET['id'];
                        $class = $this->db->get_results('SELECT * FROM `cc_classes` WHERE `id` = ' . $id, OBJECT);

                        if (!empty($class)) {

                            if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

                                $id = (int) $_GET['id'];

                                $dates = json_encode(explode("\n", str_replace("\r", "", $_POST['dates'])));
                                $adult = isset($_POST['adult']) ? 1 : 0;
                                $driving_license = isset($_POST['driving_license']) ? 1 : 0;
                                $driving_license_upload = isset($_POST['driving_license_upload']) ? 1 : 0;

                                if (
                                    !empty($_POST['name']) &&
                                    !empty($_POST['intro']) &&
                                    !empty($_POST['num_participants']) &&
                                    !empty($_POST['max_groups']) &&
                                    !empty($_POST['footer_note']) &&
                                    !empty($_POST['dates']) &&
                                    (!empty($_POST['price_per_person']) || !empty($_POST['price_fixed']))
                                ) {

                                    if (!isset($_POST['price_text'])) {
                                        $_POST['price_text'] = '';
                                    }

                                    $ageLimit = (int) $_POST['age_limit'];
                                    $ageLimitType = $_POST['age_limit_type'];

                                    if (!isset($_POST['age_limit_active'])) {
                                        $ageLimit = 'NULL';
                                        $ageLimitType = 'NULL';
                                    }

                                    $updated = $this->db->update(
                                        'cc_classes',
                                        array(
                                            'location'         => $_POST['location'],
                                            'name'             => $_POST['name'],
                                            'intro'            => $_POST['intro'],
                                            'num_participants' => (int) $_POST['num_participants'],
                                            'max_groups'       => (int) $_POST['max_groups'],
                                            'footer_note'      => $_POST['footer_note'],
                                            'price_text'       => $_POST['price_text'],
                                            'dates'            => $dates,
                                            'age_limit'        => $ageLimit,
                                            'age_limit_type'   => $ageLimitType,
                                            'driving_license'  => $driving_license,
                                            'driving_license_upload'  => $driving_license_upload,
                                            'price_per_person' => floatval($_POST['price_per_person']),
                                            'price_fixed' => floatval($_POST['price_fixed']),
                                        ),
                                        array('id' => $id)
                                    );

                                    if ($updated) {
                                        $success = 'Opgeslagen';
                                    } else {
                                        $errors[] = 'Er ging iets mis. Probeer het opnieuw.';
                                    }

                                    $class = $this->db->get_results('SELECT * FROM `cc_classes` WHERE `id` = ' . $id, OBJECT);
                                }
                            }

                            $class = $class[0];
                            $class->dates = json_decode($class->dates);

                            if ($_GET['success']) {
                                $success = 'Opgeslagen';
                            }

                            require plugin_dir_path(__FILE__) . 'partials/plugin-registration-edit-display.php';
                        }
                    }
                    break;
                case 'delete':

                    $deleted = false;

                    $base_url = explode('?', $_SERVER['REQUEST_URI']);
                    $base_url = $base_url[0];

                    $overview_url = $base_url . '?page=' . $_GET['page'];

                    if (isset($_GET['id'])) {

                        $id = (int) $_GET['id'];
                        if ($this->db->delete('cc_classes', array('id' => $id))) {

                            $deleted = true;
                        }

                        require plugin_dir_path(__FILE__) . 'partials/plugin-registration-delete-display.php';
                    }
                    break;
            }
        } else {

            $classes = $this->db->get_results('SELECT * FROM `cc_classes` ORDER BY `id` DESC', OBJECT);

            require plugin_dir_path(__FILE__) . 'partials/plugin-registration-display.php';
        }
    }

    public function display_registrations()
    {
        if (isset($_GET['action'])) {

            switch ($_GET['action']) {

                case 'changeStatus':

                    if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

                        if (!empty($_POST['id']) && !empty($_POST['status'])) {

                            $startNr = false;

                            $group = $this->db->get_row('
                                SELECT
                                    *
                                FROM
                                    `cc_groups`
                                WHERE
                                    `id` = '. (int) $_POST['id'] .'
                            ', OBJECT);

                            $class = $this->db->get_row('
                                SELECT
                                    *
                                FROM
                                    `cc_classes`
                                WHERE
                                    `id` = (
                                        SELECT
                                            `class_id`
                                        FROM
                                            `cc_groups`
                                        WHERE
                                            `id` = '. $_POST['id'] .'
                                        LIMIT 1
                                    )', OBJECT);

                            $participants = $this->db->get_results('
                                SELECT
                                    *
                                FROM
                                    `cc_participants`
                                WHERE
                                    `group_id` = '. $_POST['id'] .'
                                ORDER BY
                                    `id` ASC', ARRAY_A);

                            $driver = $participants[0];

                            if ($_POST['status'] == 1) {

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
                            }

                            $updated = $this->db->update(
                                'cc_groups',
                                array(
                                    'status' => (int) $_POST['status'],
                                    'start_nr' => $startNr ? $startNr : 'NULL'
                                ),
                                array('id' => (int) $_POST['id'])
                            );

                            if ($updated !== false) {

                                // TODO send mail to customer with starting number
                                if ($_POST['status'] == '1') {

                                    // Require HTML2PDF
                                    if (!class_exists('HTML2PDF')) {

                                        require plugin_dir_path( __FILE__ ) . '../assets/html2pdf/html2pdf.class.php';
                                    }

                                    ob_start();
                                    require plugin_dir_path( __FILE__ ) . 'partials/pdf/filled-out-form.php';
                                    $rawPdf = ob_get_clean();

                                    $pdfPath = plugin_dir_path( __FILE__ ) . '../temp/Crazy Cross deelnameformulier.pdf';

                                    $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', [14,14,14,20]);
                                    $html2pdf->writeHTML($rawPdf);
                                    ob_clean();
                                    $html2pdf->Output($pdfPath, 'F');

                                    ob_start();
                                    require plugin_dir_path( __FILE__ ) . 'partials/mails/payment-approved.php';
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
                                } elseif ($_POST['status'] == '2'
                                    && $group->status == '3'
                                ) {
                                    $paymentUrl = get_permalink(2689) . '?groupId=' . $group->id;

                                    ob_start();
                                    require plugin_dir_path( __FILE__ ) . '../public/partials/mails/payment-request.php';
                                    $mailBody = ob_get_clean();

                                    $mailHeaders = [
                                        "From: Crazy Cross Bergeijk <info@crazycrossbergeijk.nl>",
                                        "Bcc: Crazy Cross Bergeijk <info@crazycrossbergeijk.nl>",
                                    ];

                                    // Mail to customer
                                    wp_mail(
                                        $driver['email'],						    // To
                                        'Mogelijkheid tot betalen - Crazy Cross Bergeijk - '. str_replace('  ', ' ', $driver['first_name'] .' '. $driver['insertion'] .' '. $driver['last_name']), // Subject
                                        $mailBody,								// Body
                                        $mailHeaders							// Headers
                                    );
                                }

                                header('location: admin.php?page=registrations&success');
                            } 
                            else {
                                header('location: admin.php?page=registrations&error');
                            }
                        }
                    }
                    else {
                        header('location: admin.php?page=registrations');
                    }

                break;
                case 'download':

                    $groupId = (int) $_GET['group_id'];
                    $participants = $this->db->get_results('
                                    SELECT
                                        `cc_participants`.*
                                    FROM
                                        `cc_participants`
                                    LEFT JOIN
                                        `cc_groups` ON `cc_groups`.`id` = `cc_participants`.`group_id`
                                    WHERE
                                        `group_id` = '. $groupId .'
                                    ORDER BY
                                        `id` ASC
                                    ', OBJECT);

                    // validate images
                    $files = array_filter($participants, function($participant) {
                        return file_exists($participant->driving_license_path);
                    });

                    // create new zip object
                    $zip = new ZipArchive();

                    // create a temp file & open it
                    $tmp_file = tempnam(plugin_dir_path(__FILE__) . '../temp', '');
                    if (!$zip->open($tmp_file, ZipArchive::CREATE)) {
                        var_dump('Error opening zip!');
                        die();
                    }

                    // add files to zip
                    array_walk($files, function($participant) use (&$zip) {
                        $filename = array_pop(explode('/', $participant->driving_license_path));
                        $extention = array_pop(explode('.', $filename));

                        $newFilename = $participant->group_id . '_' . $participant->first_name . str_replace(' ', '_', $participant->insertion) . $participant->last_name . '_' . $participant->driving_license_nr . '_' . $participant->id . '.' . $extention;

                        $zip->addFile($participant->driving_license_path, $newFilename);
                    });

                    // close zip
                    $zip->close();

                    // send the file to the browser as a download
                    $zipname = date('YmdHis') . '_' . 'afbeeldingen_groep_' . $groupId . '.zip';
                    header('Content-disposition: attachment; filename=' . $zipname);
                    header('Content-type: application/zip');
                    readfile($tmp_file);


                break;
                case 'delete':

                    $groupId = (int) $_GET['group_id'];
                    $participants = $this->db->get_results('
                                    SELECT
                                        `cc_participants`.*
                                    FROM
                                        `cc_participants`
                                    LEFT JOIN
                                        `cc_groups` ON `cc_groups`.`id` = `cc_participants`.`group_id`
                                    WHERE
                                        `cc_participants`.`group_id` = '. $groupId .'
                                    ORDER BY
                                        `id` ASC
                                    ', OBJECT);

                    // remove images
                    array_walk($participants, function($participant) {
                        return @unlink($participant->driving_license_path);
                    });

                    // delete
                    $this->db->delete('cc_payment_transactions', ['group_id' => $groupId]);
                    $this->db->delete('cc_participants', ['group_id' => $groupId]);
                    $this->db->delete('cc_groups', ['id' => $groupId]);

                    header('location: admin.php?page=registrations&success');
                break;
                default:
                    header('location: admin.php?page=registrations');
                break;
            }
        }
        else {

            $year = (int) date('Y');
            $registrations = $this->db->get_results('
                SELECT
                    `cc_groups`.*,
                    `cc_classes`.*,
                    `cc_participants`.*
                FROM
                    `cc_participants`
                LEFT JOIN `cc_groups` ON
                    `cc_groups`.`id` = `cc_participants`.`group_id`
                LEFT JOIN `cc_classes` ON
                    `cc_groups`.`class_id` = `cc_classes`.`id`
                GROUP BY
                    `group_id`
                HAVING
                    `cc_groups`.`year` = '. $year .'
                ORDER BY
                    `cc_groups`.`id` DESC,
                    `cc_participants`.`id` ASC
            ', OBJECT);

            require plugin_dir_path(__FILE__) . 'partials/plugin-registration-registrations-display.php';
        }
    }

    public function display_export()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $year = (int) $_POST['year'];
            $location = (string) $_POST['location'];
            $participants = $this->db->get_results(
                $this->db->prepare('
				SELECT
					`cc_groups`.*,
					`cc_classes`.*,
					`cc_participants`.*
				FROM
					`cc_participants`
				LEFT JOIN `cc_groups` ON
					`cc_groups`.`id` = `cc_participants`.`group_id`
				LEFT JOIN `cc_classes` ON
					`cc_groups`.`class_id` = `cc_classes`.`id`
                WHERE
                    `cc_groups`.`year` = %d AND
                    `cc_classes`.`location` = %s
				ORDER BY
					`cc_groups`.`id` ASC,
					`cc_participants`.`id` ASC
			', [$year, $location]), ARRAY_A);

            if (!count($participants) > 0) {
                echo "
                    <script>
                        history.back();
                    </script>
                ";
                die();
            }

            /**
             * Sort excel column order here
             */
            $columns = array(
                'first_name' => 'Voornaam',
                'insertion' => 'Tussenvoegsel',
                'last_name' => 'Achternaam',
                'email' => 'email',
                'date_of_birth' => 'Geboortedatum',
                'address' => 'Adres',
                'zipcode' => 'Postcode',
                'city' => 'Plaats',
                'phonenumber' => 'Telefoonnummer',
                'driving_license_nr' => 'Rijbewijsnummer',
                'notice' => 'Opmerking',
                'date' => 'Wanneer',
                'name' => 'Klasse',
                'start_nr' => 'Start nummer',
                'group_id' => 'Groep',
                'status' => 'Status',
                'theme' => 'Thema beschrijving',
                'year' => 'Jaar',
                'location' => 'Locatie',
            );

            // collect the right data and sort it
            array_walk($participants, function(&$participant) use ($columns) {
                // remove unnecessary items
                $participant = array_diff_key($participant, array_diff_key($participant, $columns));

                switch($participant['status']) {
                    case 3:
                        $participant['status'] = 'In wachtrij';
                        break;
                    case 1:
                        $participant['status'] = 'Betaald';
                        break;
                    case 2:
                    default:
                        $participant['status'] = 'Niet betaald';
                        break;
                }

                // fix zipcode format
                $participant['zipcode'] = str_replace(' ', '', strtoupper($participant['zipcode']));

                // decode all valeus
                array_walk($participant, function(&$value) {
                    $value = mb_convert_encoding(html_entity_decode($value),'utf-16','utf-8');
                });

                // sort the items via columns
                uksort($participant, function($a, $b) use ($columns) {
                    return array_search($a, array_keys($columns)) > array_search($b, array_keys($columns));
                });
            });

            // append column names to the csv data
            array_unshift($participants, array_values($columns));

            // generate the csv
            return $this->generate_csv($participants);
        }

        $years = $this->db->get_results('
				SELECT
					DISTINCT(`cc_groups`.`year`)
				FROM
					`cc_groups`
				ORDER BY
					`year` DESC
			', OBJECT);

        require plugin_dir_path(__FILE__) . 'partials/plugin-registration-export-display.php';
    }

    public function display_settings()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            
            foreach ($_POST as $name => $setting) {

                $this->db->update(
                    'cc_settings',
                    array(
                        'value' => $setting
                    ),
                    array(
                        'name' => $name
                    )
                );
            }

            header('Location:?page=registration_settings&success');
        }

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

        require plugin_dir_path(__FILE__) . 'partials/plugin-registration-settings-display.php';
    }

    private function generate_csv($input_array)
    {
        $filename = 'crazycross_inschrijvingen_' . date('Ymd_His') . '.csv';

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Type: text/csv; charset=UTF-8', true);

//        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo "sep=|\n";

        foreach ($input_array as $line) {
            echo trim(preg_replace('/\s+/', ' ', implode('|', $line))) . "\n";
        }

        exit;
    }
}

function wpse_143405_query($query)
{
    return str_ireplace("'NULL'", "NULL", $query); 
}
