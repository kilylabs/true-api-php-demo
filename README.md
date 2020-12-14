# Пример интеграции с системой Честный Знак (TRUE API) для PHP проектов

В данном репозитории будут выкладываться примеры кода для интеграции с внезаптно свалившейся на наши (Kily Labs) головы системой Честный Знак. 

## Общий алгоритм для интеграции

1. Получить УКЭП (Усиленная квалифицированная электронная подпись) ((заказать можно здесь)[https://sberbank-bs.ru/markirovka]) ~ 3000руб.
2. Установить КриптоПРО 5.0 для Linux. Для этого нужно скачать сам дистрибутив (https://cryptopro.ru/products/csp/downloads) и купить лицензию ([заказать можно здесь](https://www.cryptopro.ru/order/?online=true)) ~3000 руб.
3. Установить расширение браузера [CryptoPro Extension](https://chrome.google.com/webstore/detail/cryptopro-extension-for-c/iifchhfnnmpdbibifmljnfjhpififfog?hl=ru)
4. Скомпилировать расширение libphpcades, применим патч для PHP 7
5. Зарегистрироваться в системе [Честный Знак](https://xn--80ajghhoc2aj1c8b.xn--p1ai/)
6. Использовать пример кода из файла demo.php этого репозитория
7. ...
8. Profit!!!

Ниже будем приводить более детальное описание каждого шага.

## Пример кода авторизации

Самым сложным моментом во всей истории интеграции с Честным Знаком был подбор правильных параметров расширения CADES PHP, которое недокументировано от слова совсем. Вот пример:

```ppp
...
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
...
```

Полный пример смотрите в фале demo.php этого репозитория.
