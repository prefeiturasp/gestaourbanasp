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

require_once('ShortCodeLoader.php');

class CFDBShortcodeExportUrl extends ShortCodeLoader {

    /**
     * @param  $atts array of short code attributes
     * @return string JSON. See ExportToJson.php
     */
    public function handleShortcode($atts) {
        $params = array();
        $params[] = admin_url('admin-ajax.php');
        $params[] = '?action=cfdb-export';
        if (isset($atts['form'])) {
            $params[] = '&form=' . urlencode($atts['form']);
        }
        if (isset($atts['show'])) {
            $params[] = '&show=' . urlencode($atts['show']);
        }
        if (isset($atts['hide'])) {
            $params[] = '&hide=' . urlencode($atts['hide']);
        }
        if (isset($atts['limit'])) {
            $params[] = '&limit=' . urlencode($atts['limit']);
        }
        if (isset($atts['search'])) {
            $params[] = '&search=' . urlencode($atts['search']);
        }
        if (isset($atts['filter'])) {
            $params[] = '&filter=' . urlencode($atts['filter']);
        }
        if (isset($atts['enc'])) {
            $params[] = '&enc=' . urlencode($atts['enc']);
        }

        $url = implode($params);

        if (isset($atts['urlonly']) && $atts['urlonly'] == 'true') {
            return $url;
        }

        $linkText = __('Export', 'contact-form-7-to-database-extension');
        if (isset($atts['linktext'])) {
            $linkText = $atts['linktext'];
        }

        return sprintf('<a href="%s">%s</a>', $url, $linkText);
    }
}
