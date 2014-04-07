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

class CFDBCheckZendFramework {

    /**
     * Checks for the existence of the Zend Framework. If not found, prints out some (hopefully) helpful information
     * @return bool true if Zend is found, *but* if not found calls wp_die()
     */
    public static function checkIncludeZend() {
        if (!(include 'Zend/Loader.php')) {
            ob_start();
            ?>
            <h1>Missing Zend Framework</h1>
            <p>
                This function requires part of the Zend framework that interacts with Google. <br/>
                It appears that either:
            </p>
            <ol>
                <li>The Zend Framework is not on the include_path or</li>
                <li>You do not have the Zend Framework installed</li>
            </ol>
            <p>
                <code>include_path="<?php echo(ini_get('include_path'));?>"</code><br/>
                <code>php.ini file is
                    "<?php $phpInfo = CFDBCheckZendFramework::getPhpInfo(); echo($phpInfo['Loaded Configuration File']);?>
                    "</code><br/>
            </p>
            <ol>
                <li>locate the the <span style="font-weight: bold;">Zend</span> directory on your computer</li>
                <li>If found, here is one way to put it on the include path</li>
                <ol>
                    <li style="list-style: lower-roman">copy the <span style="font-weight: bold;">php.ini</span> file to your WordPress installation to
                        <span style="font-weight: bold;">[wp-dir]/wp-content/plugins/contact-form-7-to-database-extension/php.ini</span>
                    </li>
                    <li style="list-style: lower-roman">add a line to this new file:<br/>
                        <code>include_path="<?php echo(ini_get('include_path') . PATH_SEPARATOR . "[Zend-parent-directory]");?>"</code>
                    </li>
                </ol>
                <li>If not found, install and configure Zend version 1.11.11 (or contact or administrator or host provider)<br/>
                    See: <a target="_blank" href="http://code.google.com/apis/gdata/articles/php_client_lib.html">Getting
                        Started
                        with the Google Data PHP Client Library</a><br/>
                    To download the part of Zend 1.11.11 required, see: <a target="_blank"
                                                                   href="http://framework.zend.com/download/gdata/">Zend
                        GData</a>
                </li>
            </ol>
            <?php
            $errorHtml = ob_get_contents();
            ob_end_clean();
            include_once('CFDBDie.php');
            CFDBDie::wp_die($errorHtml,
                             __('Missing Zend Framework', 'contact-form-7-to-database-extension'),
                             array('response' => 200, 'back_link' => true));

            // Doesn't actually return because we call wp_die
            return false;
        }
        return true;
    }


    /**
     * Taken from: http://www.php.net/manual/en/function.phpinfo.php#87214
     * @return array key => array(values) from phpinfo call
     */
    private static function getPhpInfo() {
        ob_start();
        phpinfo(INFO_GENERAL);
        $phpinfo = array('phpinfo' => array());
        if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
            foreach ($matches as $match)
                if (strlen($match[1]))
                    $phpinfo[$match[1]] = array();
                elseif (isset($match[3]))
                    $phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
                else
                    $phpinfo[end(array_keys($phpinfo))][] = $match[2];
        return $phpinfo['phpinfo'];
    }
}