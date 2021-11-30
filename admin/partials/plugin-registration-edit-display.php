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

<?php if (!empty($success)): ?>
    <div class="updated"><p><?php echo $success; ?></p></div>
<?php endif; ?>

<?php foreach ($errors?:array() as $error): ?>
    <div class="error"><p><?php echo $error; ?></p></div>
<?php endforeach; ?>

<div class="wrap">
    <h1>Klasse wijzigen</h1>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content" style="position: relative;">
                    <div id="titlediv">
                        <div id="titlewrap">
                            <label for="title"><strong>Klasse naam</strong></label>
                            <input type="text" name="name" size="30" value="<?php echo $class->name;?>" id="title" spellcheck="true" autocomplete="off" placehoder="Klasse naam" required>
                        </div>
                    </div><br /><br />
                    <div>
                        <label for="input-intro"><strong>Intro tekst</strong></label>
                        <?php wp_editor( $class->intro, 'intro', array('editor_height' => '125px', 'media_buttons' => false) ); ?>
                    </div><br /><br />
                    <div>
                        <label for="input-footer"><strong>Voet tekst</strong></label>
                        <?php wp_editor( $class->footer_note, 'footer_note', array('editor_height' => '125px', 'media_buttons' => false) ); ?>
                    </div><br /><br />
                    <div>
                        <label for="input-price"><strong>Prijs tekst</strong></label>
                        <?php wp_editor( $class->price_text, 'price_text', array('editor_height' => '125px', 'media_buttons' => false) ); ?>
                    </div>
                </div>
                <div id="postbox-container-1" class="postbox-container">
                    <div id="s" class="meta-box-sortables ui-sortable" style="">
                        <div id="submitdiv" class="postbox ">
                            <h3 class="hndle"><span>Publiceren</span></h3>

                            <div class="inside">
                                <div class="submitbox" id="submitpost">
                                    <div id="misc-publishing-actions">
                                        <div class="misc-pub-section">
                                            Staat alles goed?
                                        </div>
                                    </div>
                                    <div id="major-publishing-actions">
                                        <div id="delete-action">
                                            <?php 
                                                $clean_url = explode('?', $_SERVER['REQUEST_URI']); 
                                                $clean_url = $clean_url[0];
                                            ?>
                                            <a class="submitdelete deletion" href="<?php echo $clean_url; ?>?page=<?php echo $_GET['page']; ?>&action=delete&id=<?php echo $class->id; ?>" onclick="return confirm('Weet je zeker dat je deze klasse wilt verwijderen?');">Verwijderen</a>
                                        </div>
                                        <div id="publishing-action">
                                            <span class="spinner"></span>
                                            <input name="save" type="submit" class="button button-primary button-large" id="publish" value="Wijzig">
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="postbox ">
                        <h3 class="hndle ui-sortable-handle"><span>Overig</span></h3>
                        <div class="inside">
                            <div id="post-formats-select">
                                <fieldset>
                                    <legend class="screen-reader-text">Overige opties</legend>
                                    <label for="input-num-participants"><strong>Aantal rijders</strong></label><br />
                                    <input id="input-num-participants" type="number" name="num_participants" value="<?php echo (int) max($class->num_participants, 1); ?>" style="width:100%;" min="1" required><br />
                                    <label for="input-price-per-person"><strong>Prijs P.P.</strong></label><br />
                                    <input id="input-price-per-person" type="number" name="price_per_person" value="<?php echo $class->price_per_person; ?>" style="width:100%;" min="0" step="0.01" required><br />
                                    <label for="input-price-fixed"><strong>Vast Bedrag</strong></label><br />
                                    <input id="input-price-fixed" type="number" name="price_fixed" value="<?php echo $class->price_fixed; ?>" style="width:100%;" min="0" step="0.01" required><br />

                                    <label for="input-location"><strong>Locatie</strong></label><br />
                                    <select name="location" id="input-location" required style="width:100%;">
                                        <option value="bergeijk" <?php echo $class->location == 'bergeijk' ? 'selected="selected"' : ''; ?>>Bergeijk</option>
                                        <option value="lommel" <?php echo $class->location == 'lommel' ? 'selected="selected"' : ''; ?>>Lommel</option>
                                    </select><br />
                                    <label for="input-dates"><strong>Datums</strong></label><small> (1 datum per regel)</small><br />
                                    <textarea id="input-dates" name="dates" style="width:100%; min-height:100px;" required><?php echo implode("\n", $class->dates); ?></textarea>
                                    <br />
                                    <label for="input-max-groups"><strong>Maximaal aantal deelnemers</strong></label><br />
                                    <input id="input-max-groups" type="number" name="max_groups" value="<?php echo (int) max($class->max_groups, 1); ?>" style="width:100%;" min="1" required><br />
                                    <br />
                                    <label><input type="checkbox" <?php echo $class->age_limit ? 'checked="checked"' : ''; ?> name="age_limit_active" onchange="var a = this.checked ? 'block' : 'none'; document.getElementById('age-limit').style.display = a;"> Leeftijds limiet</label><br />
                                    <div id="age-limit" style="<?php echo !$class->age_limit ? 'display:none;' : ''; ?> background-color:#EEE; margin:6px -12px; padding:12px 12px;">
                                        <select name="age_limit_type" style="width:100%;">
                                            <option value="max">Minimaal</option>
                                            <option value="min" <?php echo $class->age_limit_type == 'min' ? 'selected="selected"' : ''; ?>>Maximaal</option>
                                        </select><br />
                                        <label for="input-age-limit"><strong>Limiet in jaren</strong></label>
                                        <input id="input-age-limit" type="number" name="age_limit" min="1" value="<?php echo $class->age_limit ?: 1; ?>" style="width:100%;">
                                    </div>
                                    <label><input type="checkbox" name="driving_license" <?php echo boolval($class->driving_license) ? 'checked="checked"' : ''; ?>> Moet in het bezit zijn van een rijbewijs</label>
                                    <label><input type="checkbox" name="driving_license_upload" <?php echo boolval($class->driving_license_upload) ? 'checked="checked"' : ''; ?>> Moet een rijbewijs uploaden</label>                     
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>