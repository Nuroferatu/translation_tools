#!/usr/bin/php
<?php

require_once "lib/errorutils.php";
require_once "lib/translationfile.php";
require_once "lib/db/sqlitedb.php";

initErrorHandler();

$tfile = new TranslationFile( "translation_enpl.csv" );
echo "Translate " . $tfile->getSrcLang() . " => " . $tfile->getDestLang() . PHP_EOL;

// hmmm why not use Sqlite3 class?
$dbName = "translate_" . $tfile->getSrcLang() . $tfile->getDestLang() . ".sq3";

$db = new Sqlite3( $dbName );
var_dump( $db );
$db = null;

?>