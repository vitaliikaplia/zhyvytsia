<?php

if(!defined('ABSPATH')){exit;}

function short_phone_format($phone){
    if(strlen($phone) >= 10){
        $onlyNumbers = preg_replace("/[^0-9]/", "", $phone);
        if(mb_substr($onlyNumbers, 0, 1) == '3'){
            $onlyNumbers = ltrim($onlyNumbers, '3');
        }
        if(mb_substr($onlyNumbers, 0, 1) == '8'){
            $onlyNumbers = ltrim($onlyNumbers, '8');
        }
        return $onlyNumbers;
    } else {
        return false;
    }
}

function fix_phone_format($phone){
    if(strlen($phone) >= 10){
        return '+38' . short_phone_format($phone);
    } else {
        return false;
    }
}

function nice_phone_format($phone){

    $num = preg_replace('/[^0-9]/', '', short_phone_format($phone));
    $len = strlen($num);

    if($len == 7) $num = preg_replace('/([0-9]{2})([0-9]{2})([0-9]{3})/', '$1 $2 $3', $num);
    elseif($len == 8) $num = preg_replace('/([0-9]{3})([0-9]{2})([0-9]{3})/', '$1 - $2 $3', $num);
    elseif($len == 9) $num = preg_replace('/([0-9]{3})([0-9]{2})([0-9]{2})([0-9]{2})/', '$1 - $2 $3 $4', $num);
    elseif($len == 10) $num = preg_replace('/([0-9]{1})([0-9]{2})([0-9]{3})([0-9]{2})([0-9]{2})/', '$1 $2 $3-$4-$5', $num);

    return '+38' . $num;

}

function check_phone($phone) {
    $valid_codes = ['050', '066', '095', '099', '067', '068', '096', '097', '098', '063', '073', '093', '091', '092', '094'];
    $phone = fix_phone_format($phone);
    if(strlen($phone) == 13 && in_array(substr($phone, 3, 3), $valid_codes)) {
        return true;
    } else {
        return false;
    }
}
