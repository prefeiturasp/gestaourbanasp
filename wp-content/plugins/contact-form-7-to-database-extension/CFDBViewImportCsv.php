<?php
/*
    "Contact Form to Database" Copyright (C) 2013 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This file is part of Contact Form to Database.

    Contact Form to Database is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Contact Form to Database is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database.
    If not, see <http://www.gnu.org/licenses/>.
*/

require_once('CF7DBPlugin.php');
require_once('CFDBView.php');

class CFDBViewImportCsv extends CFDBView
{

    /**
     * @param  $plugin CF7DBPlugin
     * @return void
     */
    function display(&$plugin)
    {

        if ($plugin == null) {
            $plugin = new CF7DBPlugin;
        }

        $forms = $plugin->getForms();
        $importUrl = admin_url('admin-ajax.php') . '?action=cfdb-importcsv';


        ?>
        <h2><?php _e('Import CSV File into Form', 'contact-form-7-to-database-extension'); ?></h2>
        <form enctype="multipart/form-data" action="<?php echo $importUrl; ?>" method="post">
            <table>
                <tbody>
                <tr>
                    <td><label for="file"><?php _e('File', 'contact-form-7-to-database-extension'); ?></label></td>
                    <td><input type="file" name="file" id="file" size="50"></td>
                </tr>
                <tr>
                    <td><input type="radio" name="into" id="new" value="new" checked> <?php _e('New Form', 'contact-form-7-to-database-extension'); ?></td>
                    <td><input type="text" name="newformname" id="newformname" size="50"/></td>
                </tr>
                <tr>
                    <td><input type="radio" name="into" id="existing" value="into"> <?php _e('Existing Form', 'contact-form-7-to-database-extension'); ?></td>
                    <td>
                        <select name="form" id="form">
                            <option value=""></option>
                            <?php
                            foreach ($forms as $formName) {
                                echo "<option value=\"$formName\">$formName</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="submit" name="<?php _e('Import', 'contact-form-7-to-database-extension'); ?>" id="importsubmit" value="import">
        </form>

        <script type="text/javascript">
                jQuery('#file').change(function () {
                    var val = jQuery(this).val();
                    val = val.substring(val.lastIndexOf('/') + 1);
                    val = val.substring(val.lastIndexOf('\\') + 1);
                    val = val.replace(/\.([^\.])*$/, "");
                    jQuery('#newformname').val(val);
                });
        </script>

        <h2><?php _e('Backup Form to CSV File', 'contact-form-7-to-database-extension'); ?></h2>
        <ul>
            <li><?php _e('Backup a form into a CSV file that can be re-imported without loss of data.', 'contact-form-7-to-database-extension'); ?></li>
            <li><?php _e('Limitation: this will not export file uploads.', 'contact-form-7-to-database-extension'); ?></li>
            <li><?php _e('Limitation: extremely large numbers of records in your form may cause the export operation on your server to run out of memory, thereby not giving you all the rows.', 'contact-form-7-to-database-extension'); ?></li>
        </ul>
        <form method="get" action="<?php echo $plugin->getPluginDirUrl() ?>export.php">
            <input type="hidden" name="enc" value="CSV"/>
            <input type="hidden" name="bak" value="true"/>
            <select name="form">
                <option value=""></option>
                <?php
                foreach ($forms as $formName) {
                    echo "<option value=\"$formName\">$formName</option>";
                }
                ?>
            </select>
            <input type="submit" name="<?php _e('Export', 'contact-form-7-to-database-extension'); ?>" value="export">
        </form>
    <?php

    }
}