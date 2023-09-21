<?php

if(!defined('ABSPATH')){exit;}

function greetings($greetings){
    if(!empty($greetings)){
        $ga = [];
        foreach ($greetings as $g){
            $ga[] = $g['item'];
        }
        return implode(',',$ga);
    }
}
