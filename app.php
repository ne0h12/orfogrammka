<?php

require_once __DIR__ . '/vendor/autoload.php';

use Orfogrammka\ApiClient;
use Orfogrammka\Orfogrammka;

$client = new ApiClient();
$config = [
    'email'    => 'account@orfogrammka.ru',
    'password' => 'secret'
];
$orfogrammka = new Orfogrammka($client, $config);
$result = $orfogrammka->checkText('<p>Hellow world</p>', 'Hellow world');

/**
 * Output: возможно, ошибка в английском слове
 */
echo $result['annotations']['annotations'][0]['description'] . ' ';

/**
 * Output: Hellow
 */
echo $result['annotations']['annotations'][0]['selection'];