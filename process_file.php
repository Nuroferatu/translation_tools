#!/usr/bin/php
<?php

require_once "lib/errorutils.php";
require_once "lib/translationfile.php";
require_once "lib/debugutils.php";
require_once "lib/langtable.php";

class Dictionary {
    private     $db;
    private     $tableName;
    private     $insertStm;

    function __construct( Sqlite3 $dbHandle, string $srcLang, string $dstLang ) {
        Validate::fatalOnNull( $dbHandle, "dbHandle", __CLASS__."::".__FUNCTION__ );
        Validate::fatalOnEmpty( $srcLang, "srcLang", __CLASS__."::".__FUNCTION__ );
        Validate::fatalOnEmpty( $dstLang, "dstLang", __CLASS__."::".__FUNCTION__ );

        $this->db = $dbHandle;
        $this->tableName = "dictionary_" . $srcLang . $dstLang;
        // TODO: Error handling - we must thro here if any of this exec fails
        $this->db->exec( "DROP TABLE IF EXISTS " . $this->tableName . ";" );
        $this->db->exec( "CREATE TABLE  " . $this->tableName . " ( src_id integer, dst_id integer );" );

        $this->insertStm = $this->db->prepare( "INSERT INTO " . $this->tableName . " (src_id,dst_id) VALUES (?,?);" );
    }

    function insert(int $srcId, array $dstIdArray) {
        $this->insertStm->bindValue( 1, $srcId, SQLITE3_INTEGER );
        foreach( $dstIdArray as $destId ) {
            $this->insertStm->bindValue( 2, $destId, SQLITE3_INTEGER );
            $res = $this->insertStm->execute();
            if( $res === false )
                fatalError( "Failed to insert ${srcId} => ${destId} to dictionary table" );
        }
        $res->finalize();
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
// TODO: move create to ctor
$srcLangTable = new LangTable( $db, $tfile->getSrcLang() );
$srcLangTable->create();

$dstLangTable = new LangTable( $db, $tfile->getDestLang() );
$dstLangTable->create();

$dictionary = new Dictionary( $db, $tfile->getSrcLang(), $tfile->getDestLang() );

// Lets process what we get from our translation file...
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
        echo " Trans Array: ${srcId} => [" . implode(",", $dstIdArray ) . "]" . PHP_EOL;
        $dictionary->insert( $srcId, $dstIdArray );
    }
}

$dstLangTable= null;
$srcLangTable = null;
$db = null;

?>