<?php

class CurlController {

    /*PETICIONES A LA API*/

    public static function requestEstandar($url, $method) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL =>  'https://servicios.siesacloud.com/api/connekta/v3/ejecutarconsulta?' . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                'ConniKey: a3e1f7ae5d9a22640f349486116d7471',
                'ConniToken: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1laWRlbnRpZmllciI6IjI1NjRlNjJkLWU0MTctNDJmYS05YjU0LWNhMDFmOTE2MWUyZCIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcHJpbWFyeXNpZCI6IjMxMzc0N2Q4LTdhY2EtNGUwZi1iYWZlLTlkZTgyOWJmZjk1OCJ9.3ZWqrum9QPxUizi_qvN5pc0EIlxMPnSRCb5mVCPOzWU'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        return $response;
    }

    public static function requestEstandarV2($url, $method) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://serviciosqa.siesacloud.com/api/connekta/v3/ejecutarconsulta?' . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                'ConniKey: c853d3ed75016b5c3617f5b52a9b1973',
                'ConniToken: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1laWRlbnRpZmllciI6IjI1NjRlNjJkLWU0MTctNDJmYS05YjU0LWNhMDFmOTE2MWUyZCIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcHJpbWFyeXNpZCI6IjlmYWQzNjNkLTE1NmMtNDBmZi1iZGZmLWFiMGY0ZDc3ZjE1ZSJ9.1pBVoKx67nIydtX1ogErfoQqyaocKO5AW5vM8rjZjNE'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($response);
        return $response;
    }
}
