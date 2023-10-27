<?php

if(!defined('ABSPATH')){exit;}

function render_working_days(array $days) {
    $allDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
    $translatedDays = [
        'mon' => 'Понеділок',
        'tue' => 'Вівторок',
        'wed' => 'Середа',
        'thu' => 'Четвер',
        'fri' => 'Пʼятниця',
        'sat' => 'Субота',
        'sun' => 'Неділя'
    ];
    $translatedDaysGenitive = [
        'mon' => 'Понеділка',
        'tue' => 'Вівторка',
        'wed' => 'Середи',
        'thu' => 'Четверга',
        'fri' => 'Пʼятниці',
        'sat' => 'Суботи',
        'sun' => 'Неділі'
    ];
    $translatedDaysAccusative = [
        'mon' => 'Понеділок',
        'tue' => 'Вівторок',
        'wed' => 'Середу',
        'thu' => 'Четвер',
        'fri' => 'Пʼятницю',
        'sat' => 'Суботу',
        'sun' => 'Неділю'
    ];

    if (count($days) == 7) {
        return "Усі дні неділі";
    }

    if (count($days) == 1) {
        return $translatedDays[$days[0]];
    }

//    if (count($days) == 2 && count(array_intersect($days, ['sat', 'sun'])) == 2) {
//        return "Вихідні дні";
//    }

    $startIndex = array_search($days[0], $allDays);
    $endIndex = array_search($days[count($days) - 1], $allDays);

    if ($endIndex - $startIndex + 1 == count($days)) {
        if (count($days) == 2) {
            return $translatedDays[$days[0]] . " та " . $translatedDays[$days[1]];
        }
        if ($endIndex > $startIndex) {
            return "з " . $translatedDaysGenitive[$days[0]] . " по " . $translatedDaysAccusative[$days[count($days) - 1]];
        } else {
            return "з " . $translatedDaysAccusative[$days[0]] . " до " . $translatedDaysGenitive[$days[count($days) - 1]];
        }
    }

    $translatedSelectedDays = array_map(function($day) use ($translatedDays) {
        return $translatedDays[$day];
    }, $days);

    return implode(", ", $translatedSelectedDays);
}
