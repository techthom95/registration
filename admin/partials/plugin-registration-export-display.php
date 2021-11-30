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

<?php if (!empty($errors)): ?>
    <ul class="errors">
    <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="wrap">
    <h1>Exporteer inschrijvingen</h1>

    <?php if(count($years) > 0): ?>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>&noheader=true" method="post">
        <table class="form-table">
            <tbody>
                <tr class="form-field form-required term-name-wrap">
                    <th scope="row">
                        <label for="year">Jaar</label>
                    </th>
                    <td>
                        <select name="year" id="year" class="postform">
                            <?php foreach($years as $object): ?>
                                <option value="<?php echo $object->year; ?>"><?php echo $object->year; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">Selecteer hierboven het jaar waarvan u de inschrijvingen wilt exporteren.</p>
                    </td>
                </tr>
                <tr class="form-field form-required term-name-wrap">
                    <th scope="row">
                        <label for="location">Locatie</label>
                    </th>
                    <td>
                        <select name="location" id="location" class="postform">
                            <option value="bergeijk">Bergeijk</option>
                            <option value="lommel">Lommel</option>
                        </select>
                        <p class="description">Selecteer hierboven de locatie waarvan u de inschrijvingen wilt exporteren.</p>
                    </td>
                </tr>
            </tbody>
        </table>

         <br>

        <input type="submit" name="test" value="Exporteren" class="button button-primary button-large" />
    </form>
    <?php else: ?>
        <br>
        <em>
            Er zijn nog geen inschrijvingen om te exporteren.
        </em>
    <?php endif; ?>
</div>