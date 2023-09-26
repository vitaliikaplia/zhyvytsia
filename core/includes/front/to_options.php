<?php

if(!defined('ABSPATH')){exit;}

function to_options($text){
    if($text){
        $arr_opts = explode("\r\n", $text);
        if(!empty($arr_opts)){
            $to_return = "";
            foreach ($arr_opts as $line){
                $to_return .= '<option value="'.$line.'">' . $line . '</option>';
            }
            return $to_return;
        }
    }
}
