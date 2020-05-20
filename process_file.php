#!/usr/bin/php
<?php

require_once "lib/errorutils.php";
require_once "lib/translationfile.php";
require_once "lib/db/sqlitedb.php";

initErrorHandler();

$tfile = new TranslationFile( "translation_enpl.csv" );
echo "Translate " . $tfile->getSrcLang() . " => " . $tfile->getDestLang() . PHP_EOL;

// Database fun... first time with PDO
// Create SQlite database called translate_[srclangdestlang].sq3
$dbName = "translate_" . $tfile->getSrcLang() . $tfile->getDestLang() . ".sq3";

$db = new SQLiteDB();
$db->open( $dbName );

$db = null;

?>