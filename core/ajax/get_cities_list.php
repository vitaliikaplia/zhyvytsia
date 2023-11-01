<?php

if(!defined('ABSPATH')){exit;}

use LisDev\Delivery\NovaPoshtaApi2;

function get_cities_list_action() {

    if(isset($_POST['search'])){

        $search = isset($_POST['search']) ? wp_unslash($_POST['search']) : '';
        $search = substr($search, 0, 50);
        $search = strip_tags($search);
        $search = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');

        $general_fields = cache_general_fields();

        $np = new NovaPoshtaApi2(
            $general_fields['shop']['nova_poshta_api_key'],
            'ua',
            FALSE,
            'curl'
        );

        $cities = $np->getCities(0, $search);
        $results = array();

        if($cities['success'] && !empty($cities['data'])){
            foreach ($cities['data'] as $city){
                $results[$city['Ref']] = $city['Description'] . ' (' . $city['AreaDescription'] . ')';
            }
        }

        echo json_encode($results);
    }

    exit;

}
add_action( 'wp_ajax_get_cities_list', 'get_cities_list_action' );
add_action( 'wp_ajax_nopriv_get_cities_list', 'get_cities_list_action' );
