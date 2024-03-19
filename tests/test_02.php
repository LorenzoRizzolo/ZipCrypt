<?php

require_once __DIR__."/../src/ZipCrypt.php";

use ZipCrypt\ZipCrypt\ZipCrypt;


$zipcrypt = new ZipCrypt(__DIR__."/../file.zip", "secret", "kjbvclhfxdjvdhzcf");

$zipcrypt->decryptZipFiles();