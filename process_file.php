#!/usr/bin/php
<?php

require_once "lib/errorutils.php";
require_once "lib/translationfile.php";

require_once "lib/debugutils.php";

class LangTable {
    private     $db;
    private     $langCode;
    private     $tableName;
    private     $insertStm;
    private     $findStm;

    function __construct( Sqlite3 $dbHandle, string $langCode ) {
        Validate::fatalOnNull( $dbHandle, "dbHandle", __CLASS__."::".__FUNCTION__ );
        Validate::fatalOnEmpty( $langCode, "langCode", __CLASS__."::".__FUNCTION__ );
        $this->db = $dbHandle;
        $this->langCode = $langCode;
        $this->tableName = "lang_" . $this->langCode;
        $this->insertStm = $this->db->prepare( "INSERT INTO " . $this->tableName . " (word) VALUES (?);" );
        $this->findIDStm = $this->db->prepare( "SELECT id FROM " . $this->tableName . " where word = ?;" );
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

    function find( string $word ) {
        $this->findIDStm->bindValue(1, $word, SQLITE3_TEXT);
        $res = $this->findIDStm->execute();
        $row = $res->fetchArray( SQLITE3_NUM );
        if( $row === false )
            return null;
        return $row[0];
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

// Create source table... if table exist it will be destroyed and recreated
$srcLangTable = new LangTable( $db, $tfile->getSrcLang() );
$srcLangTable->create();

$dstLangTable = new LangTable( $db, $tfile->getDestLang() );
$dstLangTable->create();

// Lets process what we get from our translation file...
$transArray = array();
while( !$tfile->isEOF() ) {
    $dataLine = $tfile->get();
    if( !is_null($dataLine) ) {
        // Try to find already added item...
        $srcId = $srcLangTable->find( $dataLine[0] );
        if( is_null( $srcId ) )
            $srcId = $srcLangTable->insert( $dataLine[0] );

        // Debug:
        echo "Word: '" . TColor::cyan($dataLine[0]) . "' with id=" . TColor::green($srcId);
        echo " Translations: ";

        $dstIdArray = array();
        // Place all translations into dstLangTable
        foreach( $dataLine[1] as $val ) {
            $destId = $dstLangTable->find( $val );
            if( is_null($destId) )
                $destId = $dstLangTable->insert( $val );
            echo "[${destId}:${val}]";
            array_push($dstIdArray, $destId);
        }
        // Later we use this to create translation table
        echo " Trans Array: ${srcId} => [" . implode(",", $dstIdArray ) . "]";
        $transArray[$srcId] = $dstIdArray;
        echo PHP_EOL;
    }
}

$dstLangTable= null;
$srcLangTable = null;
$db = null;

?>