<?php


include dirname(__FILE__) . '/../../config/config.php';

function mkThumbnail($src, $width = null, $height = null) {
    global $config;
    $docroot = $_SERVER['DOCUMENT_ROOT'] . $config->shoproot;
    $cacheTime = 15;
    if (!is_file($src)) {
        // 文件不存在，抛出404错误
        header('HTTP/1.1 404 Not Found');
        echo fread(fopen(dirname(__FILE__) . '/image_error.jpg', 'rb'), filesize(dirname(__FILE__) . '/image_error.jpg'));
        return false;
    }
    if (!isset($width) && !isset($height))
        return false;
    if (isset($width) && $width <= 0)
        return false;
    if (isset($height) && $height <= 0)
        return false;

    $size = getimagesize($src);
    if (!$size)
        return false;

    list($src_w, $src_h, $src_type) = $size;
    $src_mime = $size['mime'];
    switch ($src_type) {
        case 1 :
            $img_type = 'gif';
            break;
        case 2 :
            $img_type = 'jpeg';
            break;
        case 3 :
            $img_type = 'png';
            break;
        case 15 :
            $img_type = 'wbmp';
            break;
        default :
            return false;
    }

    // cache
    $cachePath = hash('md4', $src);
    $cacheRoot = $docroot . 'tmp' . DIRECTORY_SEPARATOR . 'img_cache' . DIRECTORY_SEPARATOR;
    $cacheSroot = $cacheRoot . substr($cachePath, 0, 4) . DIRECTORY_SEPARATOR;
    $cacheFile = $cacheSroot . $cachePath . '^' . $width . '-' . $height . '.' . $img_type;
    date_default_timezone_set("Asia/Shanghai");

    header("Cache-Control: private, max-age=10800, pre-check=10800");
    header("Pragma: private");
    header("Expires: " . date(DATE_RFC822, strtotime(" $cacheTime second")));
    header('Content-Type: ' . $src_mime);

    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
        // 读取缓存文件
        echo fread(fopen($cacheFile, 'rb'), filesize($cacheFile));
    } else {

        if (!is_dir($cacheSroot)) {
            mkdir($cacheSroot);
        }

        // 等比縮放
        if (!isset($width))
            $width = $src_w * ($height / $src_h);
        if (!isset($height))
            $height = $src_h * ($width / $src_w);

        $imagecreatefunc = 'imagecreatefrom' . $img_type;

        $src_img = $imagecreatefunc($src);
        $dest_img = imagecreatetruecolor($width, $height);
        imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $width, $height, $src_w, $src_h);
        $imagefunc = 'image' . $img_type;
        if ($src_type == 2) {
            $imagefunc($dest_img, $cacheFile, 90);
            //$imagefunc($dest_img, null, 90);
        } else {
            $imagefunc($dest_img, $cacheFile);
            $imagefunc($dest_img);
        }
        
        imagedestroy($src_img);
        imagedestroy($dest_img);
        
        echo fread(fopen($cacheFile, 'rb'), filesize($cacheFile));
    }
}

$filePath = str_replace('//', "/", $_SERVER['DOCUMENT_ROOT'] . $_GET['p']);
mkThumbnail($filePath, $_GET['w'], $_GET['h']);
