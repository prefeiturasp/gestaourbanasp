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

include_once('CF7DBEvalutator.php');

class CFDBFunctionEvaluator implements CF7DBEvalutator {

    var $functionName;

    public function setFunction($functionName) {
        $this->functionName = $functionName;
    }


    /**
     * Evaluate expression against input data. This is intended to mimic the search field on DataTables
     * @param  $data array [ key => value ]
     * @return boolean result of evaluating $data against expression
     */
    public function evaluate(&$data) {
        if ($this->functionName) {
            return call_user_func_array($this->functionName, array(&$data));
        }
        return true;
    }

}