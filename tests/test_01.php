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

class ZipCrypt {

    public string $filename;
    public string $password;
    public string $vector;

    public function __construct($ZipFile, $password, $vector)
    {
        $this->filename = $ZipFile;
        $this->password = $password;
        $this->vector = $vector;
    }

    public function encryptZip() {
        $ZipFile = $this->filename;
        $password = $this->password;
        $vector = $this->vector;
        $zip = new ZipArchive();
        $destinationZip = $ZipFile;
        if ($zip->open($ZipFile) === TRUE) {
            $encryptedZip = new ZipArchive();
            if ($encryptedZip->open($destinationZip, ZipArchive::CREATE) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $fileContent = $zip->getFromIndex($i);
                    $encryptedContent = openssl_encrypt($fileContent, 'aes-256-cbc', $password, 0, substr($vector, 0, 16));
                    if ($encryptedContent === FALSE) {
                        echo "Error during encryption of file $filename: " . openssl_error_string() . "\n";
                        return false;
                    }
                    $encryptedZip->addFromString($filename, $encryptedContent);
                }
                $encryptedZip->close();
                
                return true;
            } else {
                echo "Unable to create destination zip archive\n";
                return false;
            }
            $zip->close();
        } else {
            echo "Unable to open source zip archive\n";
            return false;
        }
    }
    public function decryptZip() {
        $ZipFile = $this->filename;
        $password = $this->password;
        $vector = $this->vector;
        $zip = new ZipArchive();
        $destinationZip = $ZipFile;
        if ($zip->open($ZipFile) === TRUE) {
            $encryptedZip = new ZipArchive();
            if ($encryptedZip->open($destinationZip, ZipArchive::CREATE) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $fileContent = $zip->getFromIndex($i);
                    $encryptedContent = openssl_decrypt($fileContent, 'aes-256-cbc', $password, 0, substr($vector, 0, 16));
                    if ($encryptedContent === FALSE) {
                        echo "Error during decryption of file $filename: " . openssl_error_string() . "\n";
                        return false;
                    }
                    $encryptedZip->addFromString($filename, $encryptedContent);
                }
                $encryptedZip->close();
                
                return true;
            } else {
                echo "Unable to create destination zip archive\n";
                return false; 
            }
            $zip->close();
        } else {
            echo "Unable to open source zip archive\n";
            return false; 
        }
    }
}

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
