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

<style>
    .add-participant {
        display: block;
        width: 100%;
        line-height: 30px;
        padding: 6px 12px;
        font-weight: bold;
        margin: -21px 0 15px 0;
        border-top:1px solid #ddd !important;
        border-bottom:1px solid #ddd !important;
        text-align:center;
    }
    .add-participant:hover {
        border-bottom:1px solid #ddd !important;
    }

    .remove-participant {
        color:tomato;
        border-bottom:0 !important;
        font-size: 0.7em;
        line-height: 2em;
    }
    .remove-participant:hover {
        color:#A94442;
        border-bottom:0 !important;
    }

    @media screen and (max-width: 768px) {
        .remove-participant {
            font-size:1em;
            line-height: 1em;
        }
    }
</style>
<div id="crazycross">
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>

        <?php if ($queueWarning !== true): ?>
            <hr>
            <?php echo nl2br($class->price_text); ?>
            <hr>
            <?php if (isset($paymentUrl)): ?>
                <a href="<?php echo $paymentUrl ?>" class="btn btn-primary btn-block">Direct betalen</a>
            <?php endif; ?>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <label for="cc-class-select">Selecteer een klasse:*</label>
        <select id="cc-class-select" class="form-control" onchange="$('.cc-form,.hide-after-change').addClass('hidden'); $('#cc-'+this.value).removeClass('hidden');">
            <option value="" disabled="disabled" selected="selected">-- Selecteer een klasse --</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo $class->id; ?>"><?php echo $class->name; ?></option>
            <?php endforeach; ?>
        </select>

        <div class="hide-after-change">
            <br>
            <div class="alert alert-info">
                Selecteer hierboven de klasse waaraan jij mee wil doen.
            </div>
        </div>

        <?php foreach ($classes as $class): ?>
            <form id="cc-<?php echo $class->id; ?>" class="cc-form hidden" method="post" enctype="multipart/form-data">
                <input type="hidden" name="class_id" value="<?php echo $class->id; ?>">

                <h2>Klasse: <?php echo $class->name; ?></h2>
        
                <?php if ($class->intro): ?>
                    <br>
                    <div class="alert alert-info"><?php echo nl2br($class->intro); ?></div>
                <?php endif; ?>

                <?php $dates = json_decode($class->dates); ?>
                <?php if (!empty($dates)): ?>
                    <div class="form-group">
                        Op welke datum(s) sta jij aan de start?*
                        <?php foreach ($dates as $date): ?>
                            <div class="radio">
                                <label><input type="radio" name="date" value="<?php echo $date; ?>" required> <?php echo $date; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <hr>

                <?php for ($i = 0; $i < $class->num_participants; $i++): ?>

                    <div class="participant <?php echo $i > 0 ? 'hidden' : ''; ?>">

                        <?php if ($i == 0): ?>
                            <?php $required = ' required'; ?>
                            <h4>Bestuurder*</h4>
                        <?php else: ?>
                            <?php $required = ''; ?>
                            <h4>Bijrijder <?php echo $i; ?> <a href="#" class="pull-right remove-participant" onclick="$(this).closest('.participant').addClass('hidden').find('.required').removeAttr('required'); $(this).closest('form').find('.add-participant').removeClass('hidden'); return false;">&times Verwijderen</a></h4>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="cc-first-name-<?php echo $class->id .'-'. $i; ?>">Voornaam:<?php if ($required): ?>*<?php endif; ?></label>
                            <input type="text" name="participants[<?php echo $i; ?>][first_name]" class="form-control required" id="cc-first-name-<?php echo $class->id .'-'. $i; ?>" placeholder="Voornaam"<?php echo $required; ?>>
                        </div>
                        <div class="form-group">
                            <label for="cc-insertion-<?php echo $class->id .'-'. $i; ?>">Tussenvoegsel:</label>
                            <input type="text" name="participants[<?php echo $i; ?>][insertion]" class="form-control" id="cc-insertion-<?php echo $class->id .'-'. $i; ?>" placeholder="Tussenvoegsel">
                        </div>
                        <div class="form-group">
                            <label for="cc-last-name-<?php echo $class->id .'-'. $i; ?>">Achternaam:<?php if ($required): ?>*<?php endif; ?></label>
                            <input type="text" name="participants[<?php echo $i; ?>][last_name]" class="form-control required" id="cc-last-name-<?php echo $class->id .'-'. $i; ?>" placeholder="Achternaam"<?php echo $required; ?>>
                        </div>
                        <div class="form-group">
                            <label for="cc-email-<?php echo $class->id .'-'. $i; ?>">Emailadres:<?php if ($required): ?>*<?php endif; ?></label>
                            <input type="email" name="participants[<?php echo $i; ?>][email]" class="form-control required" id="cc-email-<?php echo $class->id .'-'. $i; ?>" placeholder="Emailadres"<?php echo $required; ?>>
                        </div>
                        <div class="form-group">
                            <label for="cc-date-of-birth-<?php echo $class->id .'-'. $i; ?>">Geboortedatum:<?php if ($required): ?>*<?php endif; ?></label>
                            <?php if ($class->age_limit): ?>
                                <br>
                                <div class="alert alert-danger"><strong>Let op!</strong> Je <?php echo $class->age_limit_type == 'min' ? 'mag maximaal' : 'moet minimaal'; ?> <?php echo $class->age_limit; ?> jaar zijn om aan deze klasse mee te doen.</div>
                            <?php endif; ?>
                            <input type="date" name="participants[<?php echo $i; ?>][date_of_birth]" class="form-control required" id="cc-date-of-birth-<?php echo $class->id .'-'. $i; ?>" placeholder="Geboortedatum"<?php echo $required; ?> <?php echo $class->age_limit ? $class->age_limit_type .'="'. date('Y-m-d', strtotime('- '.$class->age_limit.' years', strtotime($settings->crazy_cross_date))) .'"' : ''; ?>>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="cc-address-<?php echo $class->id .'-'. $i; ?>">Adres:<?php if ($required): ?>*<?php endif; ?></label>
                            <input type="text" name="participants[<?php echo $i; ?>][address]" class="form-control required" id="cc-address-<?php echo $class->id .'-'. $i; ?>" placeholder="Adres"<?php echo $required; ?>>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cc-zip-code-<?php echo $class->id .'-'. $i; ?>">Postcode:<?php if ($required): ?>*<?php endif; ?></label>
                                    <input type="text" name="participants[<?php echo $i; ?>][zipcode]" class="form-control required" id="cc-zip-code-<?php echo $class->id .'-'. $i; ?>" placeholder="Postcode"<?php echo $required; ?>>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cc-city-<?php echo $class->id .'-'. $i; ?>">Woonplaats:<?php if ($required): ?>*<?php endif; ?></label>
                                    <input type="text" name="participants[<?php echo $i; ?>][city]" class="form-control required" id="cc-city-<?php echo $class->id .'-'. $i; ?>" placeholder="Woonplaats"<?php echo $required; ?>>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cc-phone-<?php echo $class->id .'-'. $i; ?>">Telefoonnummer:<?php if ($required): ?>*<?php endif; ?></label>
                            <input type="text" name="participants[<?php echo $i; ?>][phonenumber]" class="form-control required" id="cc-phone-<?php echo $class->id .'-'. $i; ?>" placeholder="Telefoonnummer"<?php echo $required; ?>>
                        </div>
                        <br>

                        <?php if ($class->driving_license): ?>
                            <div class="form-group">
                                <label for="cc-driving-license-nr-<?php echo $class->id .'-'. $i; ?>">Rijbewijsnummer:<?php if ($required): ?>*<?php endif; ?></label>
                                <input type="text" name="participants[<?php echo $i; ?>][driving_license_nr]" class="form-control required" id="cc-driving-license-nr-<?php echo $class->id .'-'. $i; ?>" placeholder="Rijbewijsnummer"<?php echo $required; ?>>
                            </div>
                        <?php endif; ?>

                        <?php if ($class->driving_license_upload): ?>
                            <div class="form-group">
                                <label for="cc-driving-license-file-<?php echo $class->id .'-'. $i; ?>">Rijbewijs:<?php if ($required): ?>*<?php endif; ?></label>
                                <input type="file" name="participants[<?php echo $i; ?>][driving_license_file]" class="form-control required" id="cc-driving-license-file-<?php echo $class->id .'-'. $i; ?>" placeholder="Rijbewijs"<?php echo $required; ?>>
                                <span style="font-size: 14px;">De afbeelding mag niet groter zijn dan 1MB. De ondersteunde bestandstypen zijn: jpg, png, gif.</span>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="cc-notice-<?php echo $class->id .'-'. $i; ?>">Opmerking:</label>
                            <textarea class="form-control" name="participants[<?php echo $i; ?>][notice]" id="cc-notice-<?php echo $class->id .'-'. $i; ?>" placeholder="Opmerking"></textarea>
                        </div>
                        <hr>
                    </div>
                <?php endfor; ?>
                
                <?php if ($class->num_participants > 1): ?>
                    <a href="#" class="add-participant" onclick="$(this).parent().find('.participant.hidden:first').removeClass('hidden').find('.required').attr('required', 'required'); if($(this).parent().find('.participant.hidden').size() == 0) { $(this).addClass('hidden'); } return false;">+ Bijrijder toevoegen</a>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="cc-theme-<?php echo $class->id; ?>">Thema:*</label>
                    <p>Welk theme gebruik je/jullie of hoe kunnen we jullie herkennen tijdens de Crazy Cross</p>
                    <textarea class="form-control" name="theme" id="cc-theme-<?php echo $class->id ?>" placeholder="Thema" required></textarea>
                </div>

                <?php if ($class->footer_note): ?>
                    <br>
                    <div class="alert alert-info"><?php echo nl2br($class->footer_note); ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="terms_accepted" required> Door in te schrijven ga jij akkoord met de door de organisatie opgelegde regels. Dit regelement is te vinden op <a href="/deelnemen" target="_blank">www.crazycrossbergeijk.nl/deelnemen</a>*
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary pull-right">Inschrijven</button>
                </div>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="clearfix"></div>
</div>