<?php
/*
    "Contact Form to Database" Copyright (C) 2011-2012 Michael Simpson  (email : michael.d.simpson@gmail.com)

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
require_once('CFDBExport.php');
include_once('CFDBDie.php');

class ExportToGoogleLiveData implements CFDBExport {

    public function export($formName, $options = null) {
        $plugin = new CF7DBPlugin();
        if (!$plugin->canUserDoRoleOption('CanSeeSubmitData')) {
            CFDBDie::wp_die(__('You do not have sufficient permissions to access this page.', 'contact-form-7-to-database-extension'));
        }
        header('Expires: 0');
        header('Cache-Control: no-store, no-cache, must-revalidate');

        $pluginUrlDir = $plugin->getPluginDirUrl();
        $scriptLink = $pluginUrlDir . 'CFDBGoogleSSLiveData.php';
        $imageUrlDir = $pluginUrlDir . "help";
        $siteUrl = get_option('home');
        $search = isset($options['search']) ? $options['search'] : '';

        ob_start();
        ?>
        <style type="text/css">
            *.popup-trigger {
                position: relative;
                z-index: 0;
            }

            *.popup-trigger:hover {
                background-color: transparent;
                z-index: 50;
            }

            *.popup-content {
                position: absolute!important;
                background-color: #ffffff;
                padding: 5px;
                border: 2px gray;
                visibility: hidden!important;
                color: black;
                text-decoration: none;
                min-width:400px;
                max-width:600px;
                overflow: auto;
            }

            *.popup-trigger:hover *.popup-content {
                visibility: visible!important;
                top: 50px!important;
                left: 50px!important;
            }
        </style>
        Setting up a Google Spreadsheet to pull in data from WordPress requires these manual steps:
        <table cellspacing="15px" cellpadding="15px">
            <tbody>
            <tr>
                <td>
                    <div class="popup-trigger">
                        <a href="<?php echo $imageUrlDir ?>/GoogleNewSS.png">
                            <img src="<?php echo $imageUrlDir ?>/GoogleNewSS.png" alt="Create a new spreadsheet" height="100px" width="61px"/>

                            <div class="popup-content">
                                <img src="<?php echo $imageUrlDir ?>/GoogleNewSS.png" alt="Create a new spreadsheet"/>
                            </div>
                        </a>
                    </div>
                </td>
                <td><p>Log into Google Docs and create a new Google Spreadsheet</p></td>
            </tr>
            <tr>
                <td>
                    <div class="popup-trigger">
                        <a href="<?php echo $imageUrlDir ?>/GoogleOpenScriptEditor.png">
                            <img src="<?php echo $imageUrlDir ?>/GoogleOpenScriptEditor.png" alt="Create a new spreadsheet" height="69px" width="100px"/>

                            <div class="popup-content">
                                <img src="<?php echo $imageUrlDir ?>/GoogleOpenScriptEditor.png" alt="Create a new spreadsheet"/>
                            </div>
                        </a>
                    </div>
                </td>
                <td><p>Go to <b>Tools</b> menu -> <b>Scripts</b> -> <b>Script Editor...</b></p></td>
            </tr>
            <tr>
                <td>
                    <div class="popup-trigger">
                        <a href="<?php echo $imageUrlDir ?>/GooglePasteScriptEditor.png">
                            <img src="<?php echo $imageUrlDir ?>/GooglePasteScriptEditor.png" alt="Paste script text" height="68px" width="100px"/>

                            <div class="popup-content">
                                <img src="<?php echo $imageUrlDir ?>/GooglePasteScriptEditor.png" alt="Paste script text"/>
                            </div>
                        </a>
                    </div>
                </td>
                <td>
                    <p>Delete any text that is already there</p>
                    <p><b>Copy</b> the text from <a target="_gscript" href="<?php echo($scriptLink) ?>">THIS SCRIPT FILE</a> and <b>paste</b> it
                    into the Google script editor</p>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="popup-trigger">
                        <a href="<?php echo $imageUrlDir ?>/GoogleSaveScriptEditor.png">
                            <img src="<?php echo $imageUrlDir ?>/GoogleSaveScriptEditor.png" alt="Create a new spreadsheet" height="100px" width="83px"/>

                            <div class="popup-content">
                                <img src="<?php echo $imageUrlDir ?>/GoogleSaveScriptEditor.png" alt="Create a new spreadsheet"/>
                            </div>
                        </a>
                    </div>
                </td>
                <td>
                    <p><b>Save</b> and <b>close</b> the script editor.</p>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="popup-trigger">
                        <a href="<?php echo $imageUrlDir ?>/GoogleEnterFormula.png">
                            <img src="<?php echo $imageUrlDir ?>/GoogleEnterFormula.png" alt="Create a new spreadsheet" height="43px" width="100px"/>

                            <div class="popup-content">
                                <img src="<?php echo $imageUrlDir ?>/GoogleEnterFormula.png" alt="Create a new spreadsheet"/>
                            </div>
                        </a>
                    </div>
                </td>
                <td>
                    <p>Click on a cell A1 in the Spreadsheet (or any cell)</p>
                    <p>Enter in the cell the formula:</p>
                    <p><code><?php echo("=CF7ToDBData(\"$siteUrl\", \"$formName\", \"$search\", \"user\", \"pwd\")") ?></code></p>
                    <p>Replacing <b>user</b> and <b>pwd</b> with your <u>WordPress</u> site user name and password</p>
                </td>
            </tr>
            <tr>

            </tr>
            </tbody>
        </table>
        <span style="color:red; font-weight:bold;">
            WARNING: since you are putting your login information into the Google Spreadsheet, be sure not to share
        the spreadsheet with others.</span>
        <?php
            $html = ob_get_contents();
        ob_end_clean();
        CFDBDie::wp_die($html,
               __('How to Set up Google Spreadsheet to pull data from WordPress', 'contact-form-7-to-database-extension'),
               array('response' => 200, 'back_link' => true));
    }
}
