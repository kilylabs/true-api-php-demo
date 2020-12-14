<?php

require __DIR__.'/vendor/autoload.php';

try
{

    $client = new GuzzleHttp\Client();
    //$res = $client->request('GET', 'https://int01.gismt.crpt.tech/api/v3/true-api/auth/key', [
    $res = $client->request('GET', 'https://markirovka.crpt.ru/api/v3/true-api/auth/key', [
        'debug'=>true,
    ]);
    $out = json_decode($res->getBody()->__toString());

    $content = $out->data;
    var_dump($out);

    $store = new CPStore();
    $store->Open(CURRENT_USER_STORE,"my",STORE_OPEN_READ_ONLY);

    $certs = $store->get_Certificates();
    $cert = $certs->Item(1);

    $signer = new CPSigner();
    $signer->set_Certificate($cert);

    $sd = new CPSignedData();
    $sd->set_Content($content);

    $sm = $sd->SignCades($signer, CADES_BES , false, ENCODE_BASE64);
    $sm = preg_replace("/[\r\n]/","",$sm);
    echo "\n";

    $client = new GuzzleHttp\Client();
    $json = [
        'uuid'=>$out->uuid,
        'data'=>$sm,
    ];
    echo json_encode($json);
    echo "\n";
    //$res = $client->request('POST', 'https://int01.gismt.crpt.tech/api/v3/true-api/auth/simpleSignIn', [
    $res = $client->request('POST', 'https://markirovka.crpt.ru/api/v3/true-api/auth/simpleSignIn', [
        'debug'=>true,
        'json'=>$json,
    ]);
    echo $res->getBody()->__toString(),"\n";
}
catch (Exception $e)
{
    printf($e->getMessage());
}
