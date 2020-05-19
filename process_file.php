#!/usr/bin/php
<?php

require_once "lib/translationfile.php";

try {
    $tfile = new TranslationFile( "translation_enpl.csv" );
    echo "Translate " . $tfile->getSrcLang() . " => " . $tfile->getDestLang() . PHP_EOL;
}
catch( RuntimeException $e ) {
    echo "\n\nException : " . $e;
}
?> 
