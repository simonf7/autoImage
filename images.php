<?php
/**
 * images.php
 *
 * This file will allow an image to be automatically created based on the request
 */


/** @var string $imageSource        Where the source images are */
$imageSource = /*__DIR__ . '/'*/ 'http://www.mycareersroom.co.uk/_gfx/images/' ;

/** @var array $allowedSizes        This is a list of sizes that are allowed */
$allowedSizes = array('400x400', '290x180', '150x100', 'm150x100', 'm2000x100');

/** @var int $jpegQuality           Default quality for JPEG images */
$jpegQuality = 85;

/** @var string $passPhrase         Secret passphrases to allow admin functions */
$passPhrase = 'secret_passphrase'; /** Change this! */

/** @var boolean $boolAdm           Was the passphrase sent and correct, this is needed to run any commands */
$boolPP = empty($_REQUEST['pp']) ? false : ($_REQUEST['pp'] == $passPhrase);

/** @var string $reqCmd             Was there a command passed in the URL */
$reqCmd = empty($_REQUEST['cmd']) ? null : $_REQUEST['cmd'];

/** Delete any cached images */
if ($reqCmd == 'delete' && $boolPP===true) {
    /** @var string $reqFN          Was there a filename specified in the passed variables */
    $reqFN = empty($_REQUEST['fn']) ? '' : $_REQUEST['fn'];
    if (!$reqFN) {
        die('Sorry, you must specify the file you want to delete.');
    }
    /** Go through each allowed size and remove the file is one already created */
    foreach ($allowedSizes as $size) {
        /** @var string $checkName  Create the filename to check for based on passed filename and size */
        $checkName = __DIR__ . '/' . $reqFN . '_' . $size;
        if (file_exists($checkName)) {
            unlink($checkName);
        }
    }

    /** Indicate that the command was successful */
    echo 'OK';
    exit(1);
}


    /**
    * Return a 404 file not found error back to the browser
    *
    * @param void
    *
    * @return void
    */
    function showNotFound()
    {
        header('HTTP/1.0 404 Not Found', true, 404);
        echo 'File not found';
        die();
    }


/** @var string $requestUri     Work out what we're asking for */
$requestUri = basename($_SERVER['REQUEST_URI']);

/** @var string $fileName       Split off file name from the URI, discard the query string these will be in the $_REQUEST array */
list($fileName, $queryString) = array_pad(explode('?', $requestUri, 2), 2, null);

/** @var string $fileType           the file extension */
list($nameNoType, $fileType) = array_pad(explode('.', $fileName, 2), 2, null);

/** @var string $actualFile         and actual file name */
/** @var string $reqSize            Split the file name into size */
list($actualFile, $reqSize) = array_pad(explode('_', $nameNoType, 2), 2, null);

/** Make sure the size is listed in the $allowedSizes array */
if ($allowedSizes!==null && !in_array($reqSize, $allowedSizes)) {
    showNotFound();
    die();
}

/** @var boolean $maxSize           If the size request starts with an 'm' we're just choosing the maximum size */
if (substr($reqSize, 0, 1)=='m') {
    $maxSize = true;
    $reqSize = substr($reqSize, 1);
}
else {
    $maxSize = false;
}

/** @var int $newWidth              Dimensions of the new file requested, width x height */
/** @var int $newHeight             and height */
list($newWidth, $newHeight) = explode('x', $reqSize, 2);

/** @var string $actualFile         Create the filename */    
$actualFile = $imageSource . $actualFile . '.' . $fileType;

/** Work out where the source file is and either download or simply read */
if (strpos($actualFile, '//')!==false) {
    /** @var object $ch             The CURL object to download the file from a remote source */
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $actualFile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSLVERSION,3);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $imageData = curl_exec($ch);
    $error = curl_error($ch); 
    curl_close($ch);

    /** @var object $srcImage       Create the image from what's downloaded */
    $srcImage = @imagecreatefromstring($imageData);
    if ($srcImage===false) {
        showNotFound();
        die();
    }
}
else {
    /** Does an original file exist? */
    if (!file_exists($actualFile)) {
        showNotFound();
        die();
    }

    /** Simply load the image from file */
    switch ($fileType) {
        case 'jpg':
        case 'jpeg':
            $srcImage = imagecreatefromjpeg($actualFile);
            break;

        case 'gif':
            $srcImage = imagecreatefromgif($actualFile);
            break;

        case 'png':
            $srcImage = imagecreatefrompng($actualFile);
            break;

        default:
            $srcImage = null;
    }
}

/** If we couldn't read the file, return file not found */
if ($srcImage===null) {
    showNotFound();
    die();
}

/** @var int $srcWidth          Dimensions of the image, width */
/** @var int $srcHeight         and height */
$srcWidth = imagesx($srcImage);
$srcHeight = imagesy($srcImage);

/** If just a maximum size required, simply scale the image */
if ($maxSize) {
    /** Work out the maximum size and scale */
    $srcRatio = $srcWidth/$srcHeight;
    if (($srcWidth/$newWidth)>($srcHeight/$newHeight)) {
        $tempWidth = $newWidth;
        $tempHeight = $newWidth / $srcRatio;
    }
    else {
        $tempHeight = $newHeight;
        $tempWidth = $newHeight * $srcRatio;
    }
    
    /** @var object $newImage       New image to copy the original image to the actual size we want */
    $newImage = imagecreatetruecolor($tempWidth, $tempHeight);
    imagecopyresampled($newImage, $srcImage, 0, 0, 0, 0, $tempWidth, $tempHeight, $srcWidth, $srcHeight);
}
else {
    /** Work out scaling */
    $srcRatio = $srcWidth/$srcHeight;
    $newRatio = $newWidth/$newHeight;
    if ($newRatio > $srcRatio) {
        $tempHeight = $newWidth / $srcRatio;
        $tempWidth = $newWidth;
    }
    else {
        $tempWidth = $newHeight * $srcRatio;
        $tempHeight = $newHeight;
    }

    /** @var object $tempImage      Create temporary image to store the scaled version */
    $tempImage = imagecreatetruecolor(round($tempWidth), round($tempHeight));
    imagecopyresampled($tempImage, $srcImage, 0, 0, 0, 0, $tempWidth, $tempHeight, $srcWidth, $srcHeight);

    /** @var object $newImage       New image to crop the temporary image to the actual size we want */
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($newImage, $tempImage, 0, 0, (($tempWidth >> 1) - ($newWidth >> 1)), (($tempHeight >> 1) - ($newHeight >> 1)), $newWidth, $newHeight, $newWidth, $newHeight);
}

/** Output the image to a file unless requested not to */
if ($reqCmd != 'nocache') {
    switch ($fileType) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($newImage, __DIR__ . '/' . $fileName, $jpegQuality);
            break;        

        case 'gif':
            imagegif($newImage, __DIR__ . '/' . $fileName);
            break;

        case 'png':
            imagepng($newImage, __DIR__ . '/' . $fileName);
            break;
    }
}

/** Send the image to the browser */
switch ($fileType) {
    case 'jpg':
    case 'jpeg':
        header('Content-Type: image/jpeg');
        imagejpeg($newImage);
        break;

    case 'gif':
        header('Content-Type: image/gif');
        imagegif($newImage);
        break;

    case 'png':
        header('Content-Type: image/png');
        imagepng($newImage);
        break;
}
