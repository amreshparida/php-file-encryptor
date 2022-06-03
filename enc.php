<?php
define('FILE_ENCRYPTION_BLOCKS', 10000);

$req_path = 'encrypted';

if (!file_exists($req_path)) {
  
    mkdir($req_path, 0777, true);
}

function encryptFile($source, $key, $dest)
{
    $key = substr(sha1($key, true), 0, 16);
    $iv = openssl_random_pseudo_bytes(16);

    $error = false;
    if ($fpOut = fopen($dest, 'w')) {
        fwrite($fpOut, $iv);
        if ($fpIn = fopen($source, 'rb')) {
            while (!feof($fpIn)) {
                $plaintext = fread($fpIn, 16 * FILE_ENCRYPTION_BLOCKS);
                $ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                $iv = substr($ciphertext, 0, 16);
                fwrite($fpOut, $ciphertext);
            }
            fclose($fpIn);
        } else {
            $error = true;
        }
        fclose($fpOut);
    } else {
        $error = true;
    }

    return $error ? false : $dest;
}





$dir = "content/";

$all_files = scandir($dir, 1);



foreach($all_files as $f)
{

    if($f!="." && $f!="..")
    {
        $fileName = $dir.$f;
        $key = 'password'; //passphrase for encrypting
        $enc_filename = explode('.', $f);
        encryptFile($fileName, $key, 'encrypted/'.$f . '.nothing');
    }

}

echo "All files have encrypted successfully!!!";

?>