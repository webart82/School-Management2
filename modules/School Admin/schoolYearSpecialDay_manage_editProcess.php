<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

include '../../functions.php';
include '../../config.php';

//New PDO DB connection
$pdo = new Gibbon\sqlConnection();
$connection2 = $pdo->getConnection();

@session_start();

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]['timezone']);

$gibbonSchoolYearSpecialDayID = $_GET['gibbonSchoolYearSpecialDayID'];
$URL = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_POST['address']).'/schoolYearSpecialDay_manage_edit.php&gibbonSchoolYearSpecialDayID='.$gibbonSchoolYearSpecialDayID.'&gibbonSchoolYearID='.$_POST['gibbonSchoolYearID'];

if (isActionAccessible($guid, $connection2, '/modules/School Admin/schoolYearSpecialDay_manage_edit.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    //Check if special day specified
    if ($gibbonSchoolYearSpecialDayID == '') {
        $URL .= '&return=error1';
        header("Location: {$URL}");
    } else {
        try {
            $data = array('gibbonSchoolYearSpecialDayID' => $gibbonSchoolYearSpecialDayID);
            $sql = 'SELECT * FROM gibbonSchoolYearSpecialDay WHERE gibbonSchoolYearSpecialDayID=:gibbonSchoolYearSpecialDayID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        if ($result->rowCount() != 1) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
        } else {
            //Validate Inputs
            $type = $_POST['type'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $gibbonSchoolYearID = $_POST['gibbonSchoolYearID'];
            $schoolOpen = null;
            if (is_numeric($_POST['schoolOpenH']) and is_numeric($_POST['schoolOpenM'])) {
                $schoolOpen = $_POST['schoolOpenH'].':'.$_POST['schoolOpenM'].':00';
            }
            $schoolStart = null;
            if (is_numeric($_POST['schoolStartH']) and is_numeric($_POST['schoolStartM'])) {
                $schoolStart = $_POST['schoolStartH'].':'.$_POST['schoolStartM'].':00';
            }
            $schoolEnd = null;
            if (is_numeric($_POST['schoolEndH']) and is_numeric($_POST['schoolEndM'])) {
                $schoolEnd = $_POST['schoolEndH'].':'.$_POST['schoolEndM'].':00';
            }
            $schoolClose = null;
            if (is_numeric($_POST['schoolCloseH']) and is_numeric($_POST['schoolCloseM'])) {
                $schoolClose = $_POST['schoolCloseH'].':'.$_POST['schoolCloseM'].':00';
            }

            if ($type == '' or $name == '' or $gibbonSchoolYearID == '') {
                $URL .= '&return=error3';
                header("Location: {$URL}");
            } else {
                //Write to database
                try {
                    $data = array('type' => $type, 'name' => $name, 'description' => $description, 'schoolOpen' => $schoolOpen, 'schoolStart' => $schoolStart, 'schoolEnd' => $schoolEnd, 'schoolClose' => $schoolClose, 'gibbonSchoolYearSpecialDayID' => $gibbonSchoolYearSpecialDayID);
                    $sql = 'UPDATE gibbonSchoolYearSpecialDay SET type=:type, name=:name, description=:description,schoolOpen=:schoolOpen, schoolStart=:schoolStart, schoolEnd=:schoolEnd, schoolClose=:schoolClose WHERE gibbonSchoolYearSpecialDayID=:gibbonSchoolYearSpecialDayID';
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                } catch (PDOException $e) {
                    $URL .= '&return=error2';
                    header("Location: {$URL}");
                    exit();
                }

                $URL .= '&return=success0';
                header("Location: {$URL}");
            }
        }
    }
}
