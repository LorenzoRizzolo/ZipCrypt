<?php

function readhidden($t){
    echo $t;
    system('stty -echo');
    $value = trim(fgets(STDIN));
    system('stty echo');
    echo $value != "" ? "\xE2\x9C\x94" : "\xE2\x9D\x8C";
    echo "\n";
    return $value;
}

require_once "vendor/autoload.php";

use ZipCrypt\ZipCrypt\ZipCrypt;

$ZipFile = readline("Zip file name: ");
$option = strtolower(readline("Operation [[E]ncrypt/[D]ecrypt]: "));
$password = readhidden("Password for ".($option=="d"?"decryption":"encryption").": ");
$ZipCrypt = new ZipCrypt($ZipFile, $password, "dlneakfrshbjvldjawbufgkvew");

if(file_exists($ZipFile)){
    switch($option){
        case "e":
            if ($ZipCrypt->encryptZip()) {
                echo "Zip archive encrypted successfully!";
            } else {
                echo "An error occurred during encryption of the zip archive.";
            }
            break;
        case "d":
            $decryptedFiles = $ZipCrypt->decryptZip();
            if ($decryptedFiles !== false) {
                echo "Zip file decrypted successfully";
            } else {
                echo "An error occurred during decryption of the zip archive.";
            }
            break;
        default:
            echo "exit...";
    }

}else{
    echo "Error: Zip file $ZipFile doesn't exists";
}
?>
