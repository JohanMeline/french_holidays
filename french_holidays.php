<?php
/**
 * File: french_holidays.php
 * Author: Johan MÉLINE
 * GitHub: https://github.com/JohanMeline/french_holidays
 * Version: 1.0.0
 * Last modified: May 03, 2024
 * Description: This script check if a given date range contain French holidays.
 */

function isFrenchHoliday($date) {
    $year = date('Y', strtotime($date));

    // Array of fixed holidays with their names
    $fixedHolidays = array(
        '01-01' => "New Year's Day",
        '05-01' => "Labour Day",
        '05-08' => "Victory in Europe Day",
        '07-14' => "Bastille Day",
        '08-15' => "Assumption of Mary",
        '11-01' => "All Saints' Day",
        '11-11' => "Armistice Day",
        '12-25' => "Christmas Day",
    );

    // Easter calculation
    $easterDate = date("Y-m-d", easter_date($year));
    $easterMonday = date("Y-m-d", strtotime("$easterDate +1 day"));
    $ascensionThursday = date("Y-m-d", strtotime("$easterDate +39 days"));
    $pentecostMonday = date("Y-m-d", strtotime("$easterDate +50 days"));

    // Convert Easter-related dates to month-day format
    $easterDate = date("m-d", strtotime($easterDate));
    $easterMonday = date("m-d", strtotime($easterMonday));
    $ascensionThursday = date("m-d", strtotime($ascensionThursday));
    $pentecostMonday = date("m-d", strtotime($pentecostMonday));

    // Add Easter-related holidays
    $easterHolidays = array(
        $easterDate => "Easter Sunday",
        $easterMonday => "Easter Monday",
        $ascensionThursday => "Ascension Thursday",
        $pentecostMonday => "Pentecost Monday"
    );

    // Merge fixed and Easter-related holidays
    $holidays = array_merge($fixedHolidays, $easterHolidays);
    
    // Check if the given date is a holiday
    $formattedDate = date("m-d", strtotime($date));
    if (array_key_exists($formattedDate, $holidays)) {
        return array('is_holiday' => true, 'name' => $holidays[$formattedDate]);
    }
    return array('is_holiday' => false, 'name' => '');
}

// Main code
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    $holidays = array();
    $holiday_count = 0;

    $current_date = date('Y-m-d', strtotime($start_date));
    while ($current_date <= $end_date) {
        $holiday_info = isFrenchHoliday($current_date);
        if ($holiday_info['is_holiday']) {
            $holidays[] = array(
                'date' => $current_date,
                'name' => $holiday_info['name']
            );
            $holiday_count++;
        }
        $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
    }

    // Return JSON response
    $response = array(
        'holiday_count' => $holiday_count,
        'holidays' => $holidays
    );
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Return error if start_date or end_date not provided
    echo json_encode(array('error' => 'start_date and end_date parameters are required'));
}
?>
