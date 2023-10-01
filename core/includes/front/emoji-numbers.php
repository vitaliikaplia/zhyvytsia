<?php

if(!defined('ABSPATH')){exit;}

function emoji_numbers($number){
    return str_replace(array('0','1','2','3','4','5','6','7','8','9'), array('0️⃣','1️⃣','2️⃣','3️⃣','4️⃣','5️⃣','6️⃣','7️⃣','8️⃣','9️⃣'), strval($number));
}
