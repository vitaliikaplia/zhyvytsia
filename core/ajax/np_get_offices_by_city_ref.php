<?php

if(!defined('ABSPATH')){exit;}

function np_get_offices_by_city_ref_action() {

    if(isset($_POST['ref'])){

        $ref = isset($_POST['ref']) ? wp_unslash($_POST['ref']) : '';
        $ref = substr($ref, 0, 60);
        $ref = strip_tags($ref);
        $ref = htmlspecialchars($ref, ENT_QUOTES, 'UTF-8');

        $general_fields = cache_general_fields();

        $np = new NovaPoshtaApi2(
            $general_fields['shop']['nova_poshta_api_key'],
            'ua',
            FALSE,
            'curl'
        );

        $warehouses = $np->getWarehouses($ref);
        $results = array();

        if($warehouses['success'] && !empty($warehouses['data'])){
            foreach ($warehouses['data'] as $office){
                $results[] = array(
                    "id" => $office['Number'],
                    "text" => $office['Description']
                );
            }
        }

        echo json_encode($results);
    }

    exit;

}
add_action( 'wp_ajax_np_get_offices_by_city_ref', 'np_get_offices_by_city_ref_action' );
add_action( 'wp_ajax_nopriv_np_get_offices_by_city_ref', 'np_get_offices_by_city_ref_action' );