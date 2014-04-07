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

require_once('CF7DBPlugin.php');
require_once('CFDBQueryResultIterator.php');

class ExportBase {

    /**
     * @var string
     */
    var $defaultTableClass = 'cf7-db-table';

    /**
     * @var array
     */
    var $options;

    /**
     * @var bool
     */
    var $debug = false;

    /**
     * @var array
     */
    var $showColumns;

    /**
     * @var array
     */
    var $hideColumns;

    /**
     * @var string
     */
    var $htmlTableId;

    /**
     * @var string
     */
    var $htmlTableClass;

    /**
     * @var string
     */
    var $style;

    /**
     * @var array assoc array of column names to display names
     */
    var $headers;

    /**
     * @var CF7DBEvalutator|CF7FilterParser|CF7SearchEvaluator
     */
    var $rowFilter;

    /**
     * @var bool
     */
    var $isFromShortCode = false;

    /**
     * @var bool
     */
    var $showSubmitField;

    /**
     * @var CF7DBPlugin
     */
    var $plugin;

    /**
     * @var CFDBQueryResultIterator
     */
    var $dataIterator;

    function __construct() {
        $this->plugin = new CF7DBPlugin();
    }

    /**
     * This method is the first thing to call after construction to set state for other methods to work
     * @param  $options array|null
     * @return void
     */
    protected function setOptions($options) {
        $this->options = $options;
    }

    protected function setCommonOptions($htmlOptions = false) {

        if ($this->options && is_array($this->options)) {
            if (isset($this->options['debug']) && $this->options['debug'] != 'false') {
                $this->debug = true;
            }

            $this->isFromShortCode = isset($this->options['fromshortcode']) &&
                    $this->options['fromshortcode'] === true;

            if (!isset($this->options['unbuffered'])) {
                $this->options['unbuffered'] = $this->isFromShortCode ? 'false' : 'true';
            }

            if (isset($this->options['showColumns'])) {
                $this->showColumns = $this->options['showColumns'];
            }
            else if (isset($this->options['show'])) {
                $this->showColumns = preg_split('/,/', $this->options['show'], -1, PREG_SPLIT_NO_EMPTY);
            }

            if (isset($this->options['hideColumns'])) {
                $this->hideColumns = $this->options['hideColumns'];
            }
            else if (isset($this->options['hide'])) {
                $this->hideColumns = preg_split('/,/', $this->options['hide'], -1, PREG_SPLIT_NO_EMPTY);
            }


            if ($htmlOptions) {
                if (isset($this->options['class'])) {
                    $this->htmlTableClass = $this->options['class'];
                }
                else {
                    $this->htmlTableClass = $this->defaultTableClass;
                }

                if (isset($this->options['id'])) {
                    $this->htmlTableId = $this->options['id'];
                }
                else {
                    $this->htmlTableId = 'cftble_' . rand();
                }

                if (isset($this->options['style'])) {
                    $this->style = $this->options['style'];
                }
            }

            $filters = array();

            if (isset($this->options['filter'])) {
                require_once('CF7FilterParser.php');
                require_once('DereferenceShortcodeVars.php');
                $aFilter = new CF7FilterParser;
                $aFilter->setComparisonValuePreprocessor(new DereferenceShortcodeVars);
                $aFilter->parseFilterString($this->options['filter']);
                if ($this->debug) {
                    echo '<pre>\'' . $this->options['filter'] . "'\n";
                    print_r($aFilter->tree);
                    echo '</pre>';
                }
                $filters[] = $aFilter;
            }

            if (isset($this->options['search'])) {
                require_once('CF7SearchEvaluator.php');
                $aFilter = new CF7SearchEvaluator;
                $aFilter->setSearch($this->options['search']);
                $filters[] = $aFilter;
            }

            if (isset($this->options['cfilter'])) {
                if (function_exists($this->options['cfilter'])) {
                    require_once('CFDBFunctionEvaluator.php');
                    $aFilter = new CFDBFunctionEvaluator;
                    $aFilter->setFunction($this->options['cfilter']);
                    $filters[] = $aFilter;
                }
                else if (class_exists($this->options['cfilter'])) {
                    require_once('CFDBClassEvaluator.php');
                    $aFilter = new CFDBClassEvaluator;
                    $aFilter->setClassName($this->options['cfilter']);
                    $filters[] = $aFilter;
                }
            }

            $numFilters = count($filters);
            if ($numFilters == 1) {
                $this->rowFilter = $filters[0];
            }
            else if ($numFilters > 1) {
                require_once('CFDBCompositeEvaluator.php');
                $this->rowFilter = new CFDBCompositeEvaluator;
                $this->rowFilter->setEvaluators($filters);
            }

            if (isset($this->options['headers'])) { // e.g. "col1=Column 1 Display Name,col2=Column2 Display Name"
                $headersList = preg_split('/,/', $this->options['headers'], -1, PREG_SPLIT_NO_EMPTY);
                if (is_array($headersList)) {
                    $this->headers = array();
                    foreach ($headersList as $nameEqualValue) {
                        $nameEqualsValueArray = explode('=', $nameEqualValue, 2); // col1=Column 1 Display Name
                        if (count($nameEqualsValueArray) >= 2) {
                            $this->headers[$nameEqualsValueArray[0]] = $nameEqualsValueArray[1];
                        }
                    }
                }
            }
        }
    }

    /**
     * @return bool
     */
    protected function isAuthorized() {
        if (!$this->isFromShortCode) {
            return $this->plugin->canUserDoRoleOption('CanSeeSubmitData');
        }
        else {
            $isAuth = $this->plugin->canUserDoRoleOption('CanSeeSubmitDataViaShortcode');
            if ($isAuth && isset($this->options['role'])) {
                $isAuth = $this->plugin->isUserRoleEqualOrBetterThan($this->options['role']);
            }
            return $isAuth;
        }
    }

    protected function assertSecurityErrorMessage() {
        $showMessage = true;

        if (isset($this->options['role'])) {
            // If role is being used, but default do not show the error message
            $showMessage = false;
        }

        if (isset($this->options['permissionmsg'])) {
            $showMessage = $this->options['permissionmsg'] != 'false';
        }

        $errMsg = $showMessage ? __('You do not have sufficient permissions to access this data.', 'contact-form-7-to-database-extension') : '';
        if ($this->isFromShortCode) {
            echo $errMsg;
        }
        else {
            include_once('CFDBDie.php');
            CFDBDie::wp_die($errMsg);
        }
    }


    /**
     * @param string|array|null $headers mixed string header-string or array of header strings.
     * E.g. Content-Type, Content-Disposition, etc.
     * @return void
     */
    protected function echoHeaders($headers = null) {
        if (!headers_sent()) {
            header('Expires: 0');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            // Hoping to keep the browser from timing out if connection from Google SS Live Data
            // script is calling this page to get information
            header("Keep-Alive: timeout=60"); // Not a standard HTTP header; browsers may disregard

            if ($headers) {
                if (is_array($headers)) {
                    foreach ($headers as $aheader) {
                        header($aheader);
                    }
                }
                else {
                    header($headers);
                }
            }
            flush();
        }
    }

    /**
     * @param  $dataColumns array
     * @return array
     */
    protected function &getColumnsToDisplay($dataColumns) {

        if (empty($dataColumns)) {
            $retCols = array();
            return $retCols;
        }

        //$dataColumns = array_merge(array('Submitted'), $dataColumns);
        $showCols = empty($this->showColumns) ? $dataColumns : $this->matchColumns($this->showColumns, $dataColumns);
        if (empty($this->hideColumns)) {
            return $showCols;
        }

        $hideCols = $this->matchColumns($this->hideColumns, $dataColumns);
        if (empty($hideCols)) {
            return $showCols;
        }

        $retCols = array();
        foreach ($showCols as $aShowCol) {
            if (!in_array($aShowCol, $hideCols)) {
                $retCols[] = $aShowCol;
            }
        }
        return $retCols;
    }

    protected function matchColumns(&$patterns, &$subject) {
        $returnCols = array();
        foreach ($patterns as $pCol) {
            if (substr($pCol, 0, 1) == '/') {
                // Show column value is a REGEX
                foreach($subject as $sCol) {
                    if (preg_match($pCol, $sCol) && !in_array($sCol, $returnCols)) {
                        $returnCols[] = $sCol;
                    }
                }
            }
            else {
                $returnCols[] = $pCol;
            }
        }
        return $returnCols;
    }

    /**
     * @return bool
     */
    protected function getShowSubmitField() {
        $showSubmitField = true;
        if ($this->hideColumns != null && is_array($this->hideColumns) && in_array('Submitted', $this->hideColumns)) {
            $showSubmitField = false;
        }
        else if ($this->showColumns != null && is_array($this->showColumns)) {
            $showSubmitField = in_array('Submitted', $this->showColumns);
        }
        return $showSubmitField;
    }

    /**
     * Execute the query and set up the results iterator
     * @param string|array $formName (if array, must be array of string)
     * @param null|string $submitTimeKeyName
     * @return void
     */
    protected function setDataIterator($formName, $submitTimeKeyName = null) {
        $submitTimes = null;

        if (isset($this->options['random'])) {
            $numRandom = intval($this->options['random']);
            if ($numRandom > 0) {
                // Digression: query for n unique random submit_time values
                $justSubmitTimes = new ExportBase();
                $justSubmitTimes->setOptions($this->options);
                $justSubmitTimes->setCommonOptions();
                unset($justSubmitTimes->options['random']);
                $justSubmitTimes->showColumns = array('submit_time');
                $jstSql = $justSubmitTimes->getPivotQuery($formName);
                $justSubmitTimes->setDataIterator($formName, 'submit_time');
                $justSubmitTimes->dataIterator->query(
                    $jstSql,
                    $justSubmitTimes->rowFilter);

                $allSubmitTimes = null;
                while ($justSubmitTimes->dataIterator->nextRow()) {
                    $allSubmitTimes[] = $justSubmitTimes->dataIterator->row['submit_time'];
                }
                if (!empty($allSubmitTimes)) {
                    if (count($allSubmitTimes) < $numRandom) {
                        $submitTimes = $allSubmitTimes;
                    }
                    else {
                        shuffle($allSubmitTimes); // randomize
                        $submitTimes = array_slice($allSubmitTimes, 0, $numRandom);
                    }
                }
            }
        }


        $sql = $this->getPivotQuery($formName, false, $submitTimes);
        $this->dataIterator = new CFDBQueryResultIterator();
//        $this->dataIterator->fileColumns = $this->getFileMetaData($formName);

        $queryOptions = array();
        if ($submitTimeKeyName) {
            $queryOptions['submitTimeKeyName'] = $submitTimeKeyName;
        }
        if (!empty($this->rowFilter) && isset($this->options['limit'])) {
            // have data iterator apply the limit if it is not already
            // being applied in SQL directly, which we do when there are
            // no filter constraints.
            $queryOptions['limit'] = $this->options['limit'];
        }
        if (isset($this->options['unbuffered'])) {
            $queryOptions['unbuffered'] = $this->options['unbuffered'];
        }

        $this->dataIterator->query($sql, $this->rowFilter, $queryOptions);
        $this->dataIterator->displayColumns = $this->getColumnsToDisplay($this->dataIterator->columns);
    }

//    protected function &getFileMetaData($formName) {
//        global $wpdb;
//        $tableName = $this->plugin->getSubmitsTableName();
//        $rows = $wpdb->get_results(
//            "select distinct `field_name`
//from `$tableName`
//where `form_name` = '$formName'
//and `file` is not null");
//
//        $fileColumns = array();
//        foreach ($rows as $aRow) {
//            $files[] = $aRow->field_name;
//        }
//        return $fileColumns;
//    }

    /**
     * @param string|array $formName (if array, must be array of string)
     * @param bool $count
     * @param $submitTimes array of string submit_time values that are to be specifically queried
     * @return string
     */
    public function &getPivotQuery($formName, $count = false, $submitTimes = null) {
        global $wpdb;
        $tableName = $this->plugin->getSubmitsTableName();

        $formNameClause = '1=1';
        if (is_array($formName)) {
            $formNameArray = $this->escapeAndQuoteArrayValues($formName);
            $formNameClause = '`form_name` in ( ' . implode(', ', $formNameArray) . ' )';
        }
        else if ($formName !== null && $formName != '*') { // * => all forms
            if (strpos($formName, ',') !== false) {
                $formNameArray = explode(',', $formName);
                $formNameArray = $this->escapeAndQuoteArrayValues($formNameArray);
                $formNameClause = '`form_name` in ( ' . implode(', ', $formNameArray) . ' )';
            }
            else {
                $formNameClause =  "`form_name` = '". mysql_real_escape_string($formName) . "'";
            }
        }

        $submitTimesClause = '';
        if (is_array($submitTimes) && !empty($submitTimes)) {
            $submitTimesClause = 'AND submit_time in ( ' . implode(', ', $submitTimes) . ' )';
        }

        //$rows = $wpdb->get_results("SELECT DISTINCT `field_name`, `field_order` FROM `$tableName` WHERE $formNameClause ORDER BY field_order"); // Pagination bug
        $rows = $wpdb->get_results("SELECT DISTINCT `field_name` FROM `$tableName` WHERE $formNameClause ORDER BY field_order");
        $fields = array();
        foreach ($rows as $aRow) {
            $fields[] = $aRow->field_name;
        }
        $sql = '';
        if ($count) {
            $sql .= 'SELECT count(*) as count FROM (';
        }
        $sql .= "SELECT `submit_time` AS 'Submitted'";
        foreach ($fields as $aCol) {
            // Escape single quotes in column name
            $aCol = mysql_real_escape_string($aCol);
            $sql .= ",\n max(if(`field_name`='$aCol', `field_value`, null )) AS '$aCol'";
        }
        if (!$count) {
            $sql .= ",\n GROUP_CONCAT(if(`file` is null or length(`file`) = 0, null, `field_name`)) AS 'fields_with_file'";
        }
        $sql .=  "\nFROM `$tableName` \nWHERE $formNameClause $submitTimesClause \nGROUP BY `submit_time` ";
        if ($count) {
            $sql .= ') form';
        }
        else {
            $orderBys = array();
            if ($this->options && isset($this->options['orderby'])) {
                $orderByStrings = explode(',', $this->options['orderby']);
                foreach ($orderByStrings as $anOrderBy) {
                    $anOrderBy = trim($anOrderBy);
                    $ascOrDesc = null;
                    if (strtoupper(substr($anOrderBy, -5)) == ' DESC'){
                        $ascOrDesc = " DESC";
                        $anOrderBy = trim(substr($anOrderBy, 0, -5));
                    }
                    else if (strtoupper(substr($anOrderBy, -4)) == ' ASC'){
                        $ascOrDesc = " ASC";
                        $anOrderBy = trim(substr($anOrderBy, 0, -4));
                    }
                    if ($anOrderBy == 'Submitted') {
                        $anOrderBy = 'submit_time';
                    }
                    if (in_array($anOrderBy, $fields) || $anOrderBy == 'submit_time') {
                        $orderBys[] = '`' . $anOrderBy . '`' . $ascOrDesc;
                    }
                    else {
                        // Want to add a different collation as a different sorting mechanism
                        // Actually doesn't work because MySQL does not allow COLLATE on a select that is a group function
                        $collateIdx = stripos($anOrderBy, ' COLLATE');
                        if ($collateIdx > 0) {
                            $collatedField = substr($anOrderBy, 0, $collateIdx);
                            if (in_array($collatedField, $fields)) {
                                $orderBys[] = '`' . $collatedField . '`' . substr($anOrderBy, $collateIdx) . $ascOrDesc;
                            }
                        }
                    }
                }
            }
            if (empty($orderBys)) {
                $sql .= "\nORDER BY `submit_time` DESC";
            }
            else {
                $sql .= "\nORDER BY ";
                $first = true;
                foreach ($orderBys as $anOrderBy) {
                    if ($first) {
                        $sql .= $anOrderBy;
                        $first = false;
                    }
                    else {
                        $sql .= ', ' . $anOrderBy;
                    }
                }
            }

            if (empty($this->rowFilter) && $this->options && isset($this->options['limit'])) {
                // If no filter constraints and have a limit, add limit to the SQL
                $sql .= "\nLIMIT " . $this->options['limit'];
            }
        }
        //echo $sql; // debug
        return $sql;
    }

    /**
     * @param $anArray array
     * @return array of quoted mysql_real_escape_string values
     */
    public function escapeAndQuoteArrayValues($anArray) {
        $retArray = array();
        foreach ($anArray as $aValue) {
            $retArray[] = '\'' . mysql_real_escape_string($aValue) . '\'';
        }
        return $retArray;
    }

    /**
     * @param string|array $formName (if array, must be array of string)
     * @return int
     */
    public function getDBRowCount($formName) {
        global $wpdb;
        $count = 0;
        $rows = $wpdb->get_results($this->getPivotQuery($formName, true));
        foreach ($rows as $aRow) {
            $count = $aRow->count;
            break;
        }
        return $count;
    }
}
