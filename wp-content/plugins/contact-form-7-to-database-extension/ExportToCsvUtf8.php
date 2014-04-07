<?php
/*
    "Contact Form to Database" Copyright (C) 2011-2013 Michael Simpson  (email : michael.d.simpson@gmail.com)

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

require_once('ExportBase.php');
require_once('CFDBExport.php');

class ExportToCsvUtf8 extends ExportBase implements CFDBExport {

    var $useBom = false;
    var $useShiftJIS = false; // for Japanese
    var $bak = false;

    public function setUseBom($use) {
        $this->useBom = $use;
    }

    public function setUseShiftJIS($use) {
        // If mb_convert_encoding function is not enabled (mb_string module is not installed),
        // then converting cannot be done.
        if ($use && !function_exists('mb_convert_encoding')) {
            $this->useShiftJIS = false;
        }
        else {
            $this->useShiftJIS = $use;
        }
    }

    public function export($formName, $options = null) {

        if (isset($options['bak']) && $options['bak'] == 'true') {
            $this->bak = true;
            $options['hide'] = 'Submitted';
            $options['show'] = 'submit_time,/.*/';
            $options['unbuffered'] = 'true';
        }

        $this->setOptions($options);
        $this->setCommonOptions();

        // Security Check
        if (!$this->isAuthorized()) {
            $this->assertSecurityErrorMessage();
            return;
        }

        if ($this->options && !$this->bak && is_array($this->options)) {
            if (isset($this->options['bom'])) {
                $this->useBom = $this->options['bom'] == 'true';
            }
        }

        // Headers
        $charSet = 'UTF-8';
        if ($this->useShiftJIS) {
            $charSet = 'Shift_JIS';
        }
        $this->echoHeaders(
            array("Content-Type: text/csv; charset=$charSet",
                 "Content-Disposition: attachment; filename=\"$formName.csv\""));

        $this->echoCsv($formName);
    }

    /**
     * Convert Shift-JIS (Standard Encoding for Japanese Applications) to UTF-8.
     * @param $str string
     * @return string
     */
    public function japanese_convert_utf8_to_sjis($str) {
        $utf_escape_patterns_revert = array(
            // The code number of Japanese two-byte character "ãƒ¼" is separated by Japanese encoding types.
            '/\xE2\x80\x93/' => "\xE2\x88\x92", // Hyphen
            '/\xE2\x80\xA2/' => "\xE3\x83\xBB" // Centered dot
        );

        $str = preg_replace(
            array_keys($utf_escape_patterns_revert),
            array_values($utf_escape_patterns_revert),
            $str);

        return $str;
    }


    public function echoCsv($formName) {
        if ($this->useBom) {
            // File encoding UTF-8 Byte Order Mark (BOM) http://wiki.sdn.sap.com/wiki/display/ABAP/Excel+files+-+CSV+format
            echo chr(239) . chr(187) . chr(191);
        }

        $eol = "\n";

        // Query DB for the data for that form
        $submitTimeKeyName = 'Submit_Time_Key';
        $this->setDataIterator($formName, $submitTimeKeyName);


        // Column Headers
        if (isset($this->options['header']) && $this->options['header'] != 'true') {
           // do not output column headers
        }
        else  {
            foreach ($this->dataIterator->displayColumns as $aCol) {
                printf('"%s",', str_replace('"', '""', $aCol));
            }
            echo $eol;
        }

        // Rows
        $showFileUrlsInExport = $this->plugin->getOption('ShowFileUrlsInExport') == 'true';
        while ($this->dataIterator->nextRow()) {
            $fields_with_file = null;
            if ($showFileUrlsInExport &&
                    isset($this->dataIterator->row['fields_with_file']) &&
                    $this->dataIterator->row['fields_with_file'] != null) {
                $fields_with_file = explode(',', $this->dataIterator->row['fields_with_file']);
            }
            foreach ($this->dataIterator->displayColumns as $aCol) {
                $cell = isset($this->dataIterator->row[$aCol]) ? $this->dataIterator->row[$aCol] : '';
                if ($showFileUrlsInExport &&
                        $fields_with_file &&
                        $cell &&
                        in_array($aCol, $fields_with_file)) {
                    $cell = $this->plugin->getFileUrl($this->dataIterator->row[$submitTimeKeyName], $formName, $aCol);
                }
                if ($this->useShiftJIS) {
                    printf('"%s",', str_replace('"', '""', mb_convert_encoding($this->japanese_convert_utf8_to_sjis($cell), "SJIS-win", "utf-8")));
                }
                else {
                    printf('"%s",', str_replace('"', '""', $cell));
                }
            }
            echo $eol;
        }
    }


}