<?php

if(!defined('ABSPATH')){exit;}

function get_page_link_by_page_option_name($page){
    if($id = get_option($page)){
        return get_the_permalink($id);
    }
}
