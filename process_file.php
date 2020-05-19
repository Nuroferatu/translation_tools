#!/usr/bin/php
<?php

require_once "lib/translationfile.php";

try {
    $tfile = new TranslationFile( "translation_enpl.csv" );
    echo "Translate " . $tfile->getSrcLang() . " => " . $tfile->getDestLang() . PHP_EOL;

    // while( !$tfile->isEOF() ) {
    //     print_r( $tfile->get() );
    // }

    // Test line processing
    // Correct line
    $tfile->get( "able;zdolny,utalentowany    ,zdatny" );

    // invalid input
    $tfile->get( "" );
    $tfile->get( ";zdolny,utalentowany    ,zdatny" );
    $tfile->get( "zdolny,utalentowany    ,zdatny" );
    $tfile->get( "able;" );
    $tfile->get( "able" );

}
catch( RuntimeException $e ) {
    echo "\n\nException : " . $e;
}
?>