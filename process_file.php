#!/usr/bin/php
<?php

require_once "lib/translationfile.php";

try {
    $tfile = new TranslationFile( "translation_enpl.csv" );
}
catch( RuntimeException $e ) {
    echo "\n\nException : " . $e;
}
?>