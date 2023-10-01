<?php

if(!defined('ABSPATH')){exit;}

function check_password_strength($password) {

    // Regular expressions for different password requirements
    $regexLowercase = '/[a-z]/';
    $regexUppercase = '/[A-Z]/';
    $regexDigit = '/[0-9]/';
    $regexSpecialChar = '/[^a-zA-Z0-9]/';

    // Check password length
    if (strlen($password) < 8) {
        return __('Password should be at least 8 characters long.', TEXTDOMAIN);
    }

    // Check lowercase letters
    if (!preg_match($regexLowercase, $password)) {
        return __('Password should contain at least one lowercase letter.', TEXTDOMAIN);
    }

    // Check uppercase letters
    if (!preg_match($regexUppercase, $password)) {
        return __('Password should contain at least one uppercase letter.', TEXTDOMAIN);

    }

    // Check digits
    if (!preg_match($regexDigit, $password)) {
        return __('Password should contain at least one digit.', TEXTDOMAIN);

    }

    // Check special characters
    if (!preg_match($regexSpecialChar, $password)) {
        return __('Password should contain at least one special character.', TEXTDOMAIN);

    }

    // Password meets all requirements
    return 'ok';
}
