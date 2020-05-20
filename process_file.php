#!/usr/bin/php
<?php

require_once "lib/errorutils.php";
require_once "lib/translationfile.php";

class LangTable {
    private     $db;
    private     $langCode;
    private     $tableName;
    private     $insertStm;
    private     $selectStm;

    function __construct( Sqlite3 $dbHandle, string $langCode ) {
        Validate::fatalOnNull( $dbHandle, "dbHandle", __CLASS__."::".__FUNCTION__ );
        Validate::fatalOnEmpty( $langCode, "langCode", __CLASS__."::".__FUNCTION__ );
        $this->db = $dbHandle;
        $this->langCode = $langCode;
        $this->tableName = "lang_" . $this->langCode;
        $this->insertStm = $this->db->prepare( "INSERT INTO " . $this->tableName . "(word) VALUES (?);" );
    }

    function __destruct() {
    }

    function create() {
        // We need to drop table first then create it again, subiect to change in future
        $sql = "DROP TABLE IF EXISTS " . $this->tableName . ";";
        $this->db->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS " . $this->tableName . " ( id integer PRIMARY KEY, word text NOT NULL );";
        $this->db->exec($sql);
    }

    function insert( string $word ) {
        $this->insertStm->bindValue(1, $word, SQLITE3_TEXT);
        $res = $this->insertStm->execute();
        $res->finalize();
        return $this->db->lastInsertRowid();
    }
}

// Main Start here :D
initErrorHandler();

$tfile = new TranslationFile( "translation_enpl.csv" );
echo "Translate " . $tfile->getSrcLang() . " => " . $tfile->getDestLang() . PHP_EOL;

// hmmm why not use Sqlite3 class?
$dbName = "translate_" . $tfile->getSrcLang() . $tfile->getDestLang() . ".sq3";

$db = new Sqlite3( $dbName );

// Create source table...
$srcLangTable = new LangTable( $db, $tfile->getSrcLang() );
$srcLangTable->create();

$dstLangTable = new LangTable( $db, $tfile->getDestLang() );
$dstLangTable->create();

// Lets process what we get from our translation file...
while( !$tfile->isEOF() ) {
    $dataLine = $tfile->get();
    if( !is_null($dataLine) ) {
        $srcId = $srcLangTable->insert( $dataLine[0] );
        // $dstIdArray = array();
        // var_dump( $dataLine[1] );
        // Place all translations into dstLangTable
        // forrach( $dataLine[1] as $val ) {
        //     // We should find translated word first
        //     $destId = $dstLangTable->insert( $val );
        // }

        // With this information we can fill translation table
    }
}

$dstLangTable= null;
$srcLangTable = null;
$db = null;

?>