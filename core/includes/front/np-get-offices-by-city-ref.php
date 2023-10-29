<?php

if(!defined('ABSPATH')){exit;}

function np_get_offices_by_city_ref($ref) {

    if($ref){

        $general_fields = cache_general_fields();

        $np = new NovaPoshtaApi2(
            $general_fields['shop']['nova_poshta_api_key'],
            'ua',
            FALSE,
            'curl'
        );

        $warehouses = $np->getWarehouses($ref);
        $results = array();

        $ids_arr = array_filter(explode(".", wp_unslash(stripslashes($_COOKIE['cart']))));
        if(!empty($ids_arr)){
            foreach ($ids_arr as $cid){
                $weight += get_field('weight_in_grams', $cid);
            }
            $weight = ceil( ( $weight + intval($general_fields['shop']['reserve_weight_in_grams']) ) / 1000);
        }

        if($warehouses['success'] && !empty($warehouses['data'])){
            foreach ($warehouses['data'] as $office){
                if($office['PlaceMaxWeightAllowed'] != 0){
                    $maxWeightAllowed = intval($office['PlaceMaxWeightAllowed']);
                }
                if($office['TotalMaxWeightAllowed'] != 0){
                    $maxWeightAllowed = intval($office['TotalMaxWeightAllowed']);
                }
                if($maxWeightAllowed > $weight){
                    $results[] = array(
                        "id" => $office['Number'],
                        "text" => $office['Description']
                    );
                }
            }
        }

        return $results;

    }

}
