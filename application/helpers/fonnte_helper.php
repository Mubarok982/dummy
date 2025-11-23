<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('format_nomor_wa'))
{
    function format_nomor_wa($nomor) {
        // Logika pembersihan nomor dari file config Anda
        $nomor = preg_replace('/[^0-9+]/', '', trim($nomor)); 

        if (substr($nomor, 0, 1) === '0') {
            return '62' . substr($nomor, 1);
        } elseif (substr($nomor, 0, 1) === '+') {
            return substr($nomor, 1);
        }

        return $nomor;
    }
}

if ( ! function_exists('kirim_wa_fonnte'))
{
    function kirim_wa_fonnte($target, $pesan) {
        // Token diambil dari file config_fonnte.php Anda
        $token = "Ag9s89dxD8Y4dATL3f8w"; 

        $target = format_nomor_wa($target);
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.fonnte.com/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30, // Timeout disesuaikan
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $pesan,
                'countryCode' => '62', 
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token"
            ),
            CURLOPT_SSL_VERIFYPEER => false, // Bypass SSL untuk localhost
            CURLOPT_SSL_VERIFYHOST => 0,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return "cURL Error #: " . $err;
        } else {
            return $response;
        }
    }
}