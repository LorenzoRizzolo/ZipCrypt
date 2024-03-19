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

    public function encryptZipFiles($excluded=[]) {
        $ZipFile = $this->filename;
        $password = $this->password;
        $vector = $this->vector;
        $zip = new ZipArchive();
        $destinationZip = $ZipFile;
        if ($zip->open($ZipFile)) {
            $encryptedZip = new ZipArchive();
            if ($encryptedZip->open($destinationZip, ZipArchive::CREATE) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if(!in_array(basename($filename), $excluded)){
                        $fileContent = $zip->getFromIndex($i);
                        $encryptedContent = openssl_encrypt($fileContent, 'aes-256-cbc', $password, 0, substr($vector, 0, 16));
                        if ($encryptedContent === FALSE) {
                            error_log("Error while encrypting file $filename: " . openssl_error_string());
                            return false;
                        }
                        $encryptedZip->addFromString($filename, $encryptedContent);
                    }
                }
                $encryptedZip->close();
                
                return true;
            } else {
                error_log("Unable to create destination zip archive\n");
                return false;
            }
            $zip->close();
        } else {
            error_log("Unable to open source zip archive\n");
            return false;
        }
    }

    // function for 
    public function decryptZipFiles($excluded=[]) {
        $ZipFile = $this->filename;
        $password = $this->password;
        $vector = $this->vector;
        $zip = new ZipArchive();
        $destinationZip = $ZipFile;
        if ($zip->open($ZipFile)) {
            $encryptedZip = new ZipArchive();
            if ($encryptedZip->open($destinationZip, ZipArchive::CREATE) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if(!in_array(basename($filename), $excluded)){
                        $fileContent = $zip->getFromIndex($i);
                        $encryptedContent = openssl_decrypt($fileContent, 'aes-256-cbc', $password, 0, substr($vector, 0, 16));
                        if ($encryptedContent === FALSE) {
                            error_log("Error while decrypting file $filename: " . openssl_error_string());
                            return false;
                        }
                        $encryptedZip->addFromString($filename, $encryptedContent);
                    }
                }
                $encryptedZip->close();
                
                return true;
            } else {
                error_log("Unable to create destination zip archive\n");
                return false; 
            }
            $zip->close();
        } else {
            error_log("Unable to open source zip archive\n");
            return false; 
        }
    }

    public function encryptAll($new_name = ""){
        $new_name = $new_name=="" ? $this->filename : $new_name;
        $fileContent = file_get_contents($this->filename);
        $encryptedContent = openssl_encrypt($fileContent, 'aes-256-cbc', $this->password, 0, substr($this->vector, 0, 16));
        file_put_contents($new_name, $encryptedContent);
        return file_exists($new_name);
    }

    public function decryptAll($new_name = ""){
        $new_name = $new_name=="" ? $this->filename : $new_name;
        $fileContent = file_get_contents($this->filename);
        $encryptedContent = openssl_decrypt($fileContent, 'aes-256-cbc', $this->password, 0, substr($this->vector, 0, 16));
        file_put_contents($new_name, $encryptedContent);
        return file_exists($new_name);
    }

    // public function addPasswordToZip() {
    //     $zipFilename = $this->filename;
    //     $password = $this->password;
    //     $zip = new ZipArchive();    
    //     if ($zip->open($zipFilename, ZipArchive::CREATE) === TRUE) {
    //         $zip->setPassword($password);
    //         $numFiles = $zip->numFiles;
    //         for ($i = 0; $i < $numFiles; $i++) {
    //             $filename = $zip->getNameIndex($i);
    //             $zip->setEncryptionName($filename, ZipArchive::EM_AES_256);
    //         }
            
    //         // Chiude il file ZIP
    //         $zip->close();
    //         return true;
    //     } else {
    //         // Gestione dell'errore di apertura del file ZIP
    //         return false;
    //     }
    // }
}
