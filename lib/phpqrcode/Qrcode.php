<?php

function GenQRCode($data) {
    ini_set('display_errors', 'on');
    $PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'temp/';

    include "qrlib.php";    // QRcode lib  
//$data = '$c44587'; // data  
    $ecc = 'H'; // L-smallest, M, Q, H-best  
    $size = 20; // 1-50  

    $filename = $_SERVER['DOCUMENT_ROOT'] . '/client_qrcode/qrcode_' . $data . '.png';

    QRcode::png($data, $filename, $ecc, $size, 2);
    chmod($filename, 0777);
    return '/client_qrcode/qrcode_' . $data . '.png';
}
