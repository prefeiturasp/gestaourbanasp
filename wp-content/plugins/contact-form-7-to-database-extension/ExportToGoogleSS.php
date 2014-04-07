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

require_once('CFDBCheckZendFramework.php');
require_once('ExportToCsvUtf8.php');
require_once('ExportBase.php');
require_once('CFDBExport.php');
require_once('CFDBDie.php');

class ExportToGoogleSS extends ExportBase implements CFDBExport {

    public function export($formName, $options = null) {
        $this->setOptions($options);

        // Security Check
        if (!$this->isAuthorized()) {
            $this->assertSecurityErrorMessage();
            return;
        }

        // Headers
        $this->echoHeaders('Content-Type: text/html; charset=UTF-8');

        if (!CFDBCheckZendFramework::checkIncludeZend()) {
            return;
        }

        Zend_Loader::loadClass('Zend_Gdata');
        Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
        //Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
        Zend_Loader::loadClass('Zend_Gdata_App_AuthException');
        Zend_Loader::loadClass('Zend_Http_Client');
        Zend_Loader::loadClass('Zend_Gdata_Docs');

        $guser = $options['guser'];
        $gpwd = $options['gpwd'];
        try {
            $client = Zend_Gdata_ClientLogin::getHttpClient(
                $guser, $gpwd,
                Zend_Gdata_Docs::AUTH_SERVICE_NAME); //Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME
        }
        catch (Zend_Gdata_App_AuthException $ae) {
            CFDBDie::wp_die("<p>Login failed for: '$guser' </p><p>Error: " . $ae->getMessage() . '</p>',
                            __('Login Failed', 'contact-form-7-to-database-extension'),
                            array('response' => 200, 'back_link' => true));
        }

        try {
            // Generate CSV file contents into buffer
            $exporter = new ExportToCsvUtf8;
            $exporter->setOptions($options);
            $exporter->setCommonOptions();
            $exporter->setUseBom(false);

            ob_start();
            $exporter->echoCsv($formName);
            $csvFileContents = ob_get_contents();
            ob_end_clean();

            // Put the contents in a tmp file because Google upload API only reads from a file
            $tmpfname = tempnam(sys_get_temp_dir(), "$formName.csv");
            $handle = fopen($tmpfname, 'w');
            fwrite($handle, $csvFileContents);
            fclose($handle);

            // Upload the tmp file to Google Docs
            $docs = new Zend_Gdata_Docs($client);
            $newDocumentEntry = $docs->uploadFile($tmpfname, $formName, 'text/csv');
            unlink($tmpfname); // delete tmp file

            // Get the URL of the new Google doc
            $alternateLink = '';
            foreach ($newDocumentEntry->link as $link) {
                if ($link->getRel() === 'alternate') {
                    $alternateLink = $link->getHref();
                    break;
                }
            }

            //header("Location: $alternateLink");
            //$title = $newDocumentEntry->title;

            $title = __('New Google Spreadsheet', 'contact-form-7-to-database-extension');
            $output =
                    utf8_encode("$title: <a target=\"_blank\" href=\"$alternateLink\">") .
                            $formName .
                            utf8_encode('</a>');
            CFDBDie::wp_die($output, $title,  array('response' => 200, 'back_link' => true));
        }
        catch (Exception $ex) {
            CFDBDie::wp_die($ex->getMessage() . '<pre>' . $ex->getTraceAsString() . '</pre>',
                            __('Error', 'contact-form-7-to-database-extension'),
                            array('back_link' => true));
        }
    }
}
