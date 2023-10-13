<?php

if(!defined('ABSPATH')){exit;}

function send_sms($recipient, $message){

    if($recipient && $message && ($sms_username = get_option('sms_username')) && ($sms_password = get_option('sms_password')) && ($sms_alpha_name = get_option('sms_alpha_name')) ){

        $text = iconv('utf-8', 'utf-8', htmlspecialchars($message));
        $description = iconv('utf-8', 'utf-8', htmlspecialchars('Website notifications'));
        $start_time = 'AUTO';
        $end_time = 'AUTO';
        $rate = 1;
        $lifetime = 4;

        $myXML 	 = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $myXML 	.= "<request>";
        $myXML 	.= "<operation>SENDSMS</operation>";
        $myXML 	.= '		<message start_time="'.$start_time.'" end_time="'.$end_time.'" lifetime="'.$lifetime.'" rate="'.$rate.'" desc="'.$description.'" source="'.$sms_alpha_name.'">'."\n";
        $myXML 	.= "		<body>".$text."</body>";
        $myXML 	.= "		<recipient>".$recipient."</recipient>";
        $myXML 	.=  "</message>";
        $myXML 	.= "</request>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD , $sms_username.':'.$sms_password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, 'http://sms-fly.com/api/api.php');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Accept: text/xml"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myXML);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;

    }

}
