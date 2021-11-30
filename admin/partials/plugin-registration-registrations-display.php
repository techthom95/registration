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
    <div class="updated"><p>De wijzigingen zijn succesvol toegepast!</p></div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="error"><p>Er is iets mis gegaan tijdens het opslaan. Probeer het opnieuw.</p></div>
<?php endif; ?>

<div class="wrap">
    <h1>Inschrijvingen</h1>
    <br>
    <?php if (!empty($registrations)): ?>
        <table class="wp-list-table widefat fixed striped posts" id="datatable">
            <thead>
                <tr>
                    <th width="5%" scope="col" id="title" class="manage-column column-title column-primary">Locatie</th>
                    <th width="5%" scope="col" id="title" class="manage-column column-title column-primary">Groep</th>
                    <th width="5%" scope="col" id="title" class="manage-column column-title column-primary">Startnr</th>
                    <th width="10%" scope="col" id="title" class="manage-column column-title column-primary">Klasse</th>
                    <th width="20%" scope="col" id="title" class="manage-column column-title column-primary">Naam</th>
                    <th width="15%" scope="col" id="title" class="manage-column column-title column-primary">Emailadres</th>
                    <th width="15%" scope="col" id="title" class="manage-column column-title column-primary">Afbeeldingen</th>
                    <th width="10%" scope="col" id="title" class="manage-column column-title column-primary">Status</th>
                    <th width="10%" scope="col" id="title" class="manage-column column-title column-primary">Verwijder</th>
                </tr>
            </thead>
            <tbody id="the-list">
            <?php foreach ($registrations as $registration): ?>
                <tr class="type-post status-publish format-standard hentry">
                    <td><?php echo ucfirst($registration->location); ?></td>
                    <td><?php echo $registration->group_id; ?></td>
                    <td><?php echo $registration->start_nr ?: '-'; ?></td>
                    <td><?php echo $registration->name; ?></td>
                    <td><?php echo str_replace('  ', ' ', $registration->first_name .' '. $registration->insertion .' '. $registration->last_name); ?></td>
                    <td><a href="mailto:<?php echo $registration->email; ?>"><?php echo $registration->email; ?></a></td>
                    <td>
                        <?php if ($registration->driving_license_upload): ?>
                            <a href="admin.php?page=registrations&action=download&noheader=true&group_id=<?php echo $registration->group_id ?>">Downloaden</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <form id="registration-<?php echo $registration->id; ?>" action="admin.php?page=registrations&action=changeStatus&noheader=true" method="post">
                            <input type="hidden" name="id" value="<?php echo $registration->group_id; ?>">
                            <select name="status" onchange="if (confirm('Weet je zeker dat je de status wilt veranderen?')) {document.getElementById('registration-<?php echo $registration->id; ?>').submit();}">
                                <option value="1" <?php echo $registration->status == 1 ? 'selected="selected"' : ''; ?>>Betaald</option>
                                <option value="2" <?php echo $registration->status == 2 ? 'selected="selected"' : ''; ?>>Niet betaald</option>
                                <option value="3" <?php echo $registration->status == 3 ? 'selected="selected"' : ''; ?>>In wachtrij</option>
                            </select>
                        </form>
                    </td>
                    <td><span class="trash"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&action=delete&noheader=true&group_id=<?php echo $registration->group_id; ?>" onclick="return confirm('Weet je zeker dat je deze inschrijving wilt verwijderen?');" style="color:#a00;">Verwijder</a></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>

        <em>Geen resultaten gevonden.</em>
    <?php endif; ?>

    <div id="ajax-response"></div>
    <br class="clear">
</div>