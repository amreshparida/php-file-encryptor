<?php
define('FILE_ENCRYPTION_BLOCKS', 10000);

function decryptFile($source, $key, $dest)
{
    $key = substr(sha1($key, true), 0, 16);

    $error = false;
    if ($fpOut = fopen($dest, 'w')) {
        if ($fpIn = fopen($source, 'rb')) {
            // Get the initialzation vector from the beginning of the file
            $iv = fread($fpIn, 16);
            while (!feof($fpIn)) {
                $ciphertext = fread($fpIn, 16 * (FILE_ENCRYPTION_BLOCKS + 1)); // we have to read one block more for decrypting than for encrypting
                $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                // Use the first 16 bytes of the ciphertext as the next initialization vector
                $iv = substr($ciphertext, 0, 16);
                fwrite($fpOut, $plaintext);
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







$dir = "encrypted/";

$all_files = scandir($dir, 1);

foreach($all_files as $f)
{

    if($f!="." && $f!="..")
    {
        
        $fileName = $dir.$f;
        $key = 'password'; //passphrase for decrypting
        $dec_filename = explode('.', $f);
        decryptFile($fileName, $key, 'decrypted/'.$dec_filename[0]."." . $dec_filename[1]);
        
    }

}

echo "All files have decrypted successfully!!!";




?>