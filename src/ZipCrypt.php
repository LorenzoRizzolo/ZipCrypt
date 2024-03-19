<?php

namespace ZipCrypt\ZipCrypt;

use ZipArchive;

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
                        echo "Error while encrypting file $filename: " . openssl_error_string() . "\n";
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
                        echo "Error while decrypting file $filename: " . openssl_error_string() . "\n";
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
