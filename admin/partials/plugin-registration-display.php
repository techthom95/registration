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

<?php foreach ($errors?:array() as $error): ?>
    <div class="error"><p><?php echo $error; ?></p></div>
<?php endforeach; ?>

<div class="wrap">
    <h1>Klasses <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&action=new" class="page-title-action">Nieuw</a></h1>
    <br>
    <?php if (!empty($classes)): ?>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th width="3%" scope="col" id="title" class="manage-column column-title column-primary">#</th>
                    <th width="10%" scope="col" id="title" class="manage-column column-title column-primary">Locatie</th>
                    <th width="70%" scope="col" id="title" class="manage-column column-title column-primary">Klasse naam</th>
                    <th width="15%" scope="col" id="title" class="manage-column column-title column-primary">Maximaal aantal groepen</th>
                    <th width="10%" scope="col" id="title" class="manage-column column-title column-primary">Wijzig</th>
                    <th width="10%" scope="col" id="title" class="manage-column column-title column-primary">Verwijder</th>
                </tr>
            </thead>
            <tbody id="the-list">
            <?php foreach ($classes as $class): ?>
                <tr class="type-post status-publish format-standard hentry">
                    <td><?php echo $class->id; ?></td>
                    <td><?php echo ucfirst($class->location); ?></td>
                    <td><strong><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&action=edit&id=<?php echo $class->id; ?>"><?php echo $class->name; ?></a></strong></td>
                    <td><?php echo $class->max_groups; ?></td>
                    <td><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&action=edit&id=<?php echo $class->id; ?>">Wijzig</a></td>
                    <td><span class="trash"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&action=delete&id=<?php echo $class->id; ?>" onclick="return confirm('Weet je zeker dat je deze klasse wilt verwijderen?');" style="color:#a00;">Verwijder</a></span></td>
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