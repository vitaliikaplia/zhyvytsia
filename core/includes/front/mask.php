<?php

if(!defined('ABSPATH')){exit;}

function mask_email($email){

    // Розділення email на ім'я користувача і домен
    list($user, $domain) = explode('@', $email);

    // Розділення домену на базовий домен та суфікс
    $parts = explode('.', $domain);
    $tld = array_pop($parts);  // .ua
    $baseDomain = array_pop($parts);  // baa
    $subdomain = implode('.', $parts);  // (if exists) subdomains

    // Приховуємо частину імені користувача зірочками
    $userMasked = substr($user, 0, 2) . str_repeat('*', strlen($user) - 3) . substr($user, -1);

    // Приховуємо частину базового домену
    $baseDomainMasked = substr($baseDomain, 0, 1) . str_repeat('*', strlen($baseDomain) - 1);

    // Маскуємо піддомен (якщо він існує)
    if ($subdomain) {
        $subdomainMasked = substr($subdomain, 0, 1) . str_repeat('*', strlen($subdomain) - 1);
    }

    // Формуємо маскований домен
    $maskedDomain = $subdomain ? $subdomainMasked . '.' . $baseDomainMasked : $baseDomainMasked;

    // Повертаємо замаскований email
    return $userMasked . '@' . $maskedDomain . '.' . $tld[0] . '**';

}

function mask_string($str) {
    $len = mb_strlen($str, 'UTF-8');
    if ($len <= 2) {
        return '**';
    }
    return mb_substr($str, 0, 2, 'UTF-8') . str_repeat('*', $len - 2) . mb_substr($str, -1, 1, 'UTF-8');
}

function mask_phone_number($str) {
    // Шифруємо номер
    $hiddenPart = substr($str, 0, 6) . '*' . ' ' . '***' . '-' . '**' . '-' . '*' . substr($str, -1);
    return $hiddenPart;
}
