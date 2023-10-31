<?php

if(!defined('ABSPATH')){exit;}

function custom_order_statuses(){
    return array(
        'new_order' => 'Нове замовлення',
        'confirmed' => 'Підтверджено',
        'processed' => 'Обробляється',
        'ready_to_ship' => 'Готове до відправки',
        'sent' => 'Відправлено',
        'in_the_way' => 'В дорозі',
        'delivered_to_the_delivery_point' => 'Доставлено до пункту видачі',
        'awaiting_confirmation_of_receipt' => 'В очікуванні підтвердження отримання',
        'received_by_the_customer' => 'Отримано клієнтом',
        'on_the_way_back' => 'В поверненні',
        'returned_to_the_store' => 'Повернено магазину',
        'money_returned' => 'Гроші повернено',
        'awaiting_payment' => 'Очікує оплати',
        'paid' => 'Оплачено',
        'awaiting_packing' => 'Очікує пакування',
        'awaiting_additional_information_from_the_client' => 'В очікуванні додаткової інформації від клієнта',
        'awaiting_shipment_from_the_supplier_warehouse' => 'Очікує відправку зі складу постачальника',
        'payment_problem' => 'Проблема з оплатою',
        'the_order_has_been_stopped' => 'Замовлення зупинено',
        'cancelled' => 'Скасовано',
        'done' => 'Виконано',
        'archived' => 'Архівне'
    );
}
