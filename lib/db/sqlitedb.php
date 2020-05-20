<?php


/*
$srcTableName = "lang_".$tfile->getSrcLang();
$sql = "CREATE TABLE IF NOT EXISTS ${srcTableName} ( id integer PRIMARY KEY, word text NOT NULL );";
$dbHandle->exec($sql);

$srcTableName = "lang_".$tfile->getDestLang();
$sql = "CREATE TABLE IF NOT EXISTS ${srcTableName} ( id integer PRIMARY KEY, word text NOT NULL );";
$dbHandle->exec($sql);

$dbHandle = null;
*/

class SQLiteDB {
    private $dbHandle = null;

    function isOpen() {
        return !is_null( $this->dbHandle );
    }

    function open( string $dbName ) {
        Validate::fatalOnEmpty( $dbName, "dbName", "SQLiteDB::open" );
        echo "Open database '" . TColor::cyan( $dbName ) . "'" . PHP_EOL;
        $this->dbHandle = new PDO( "sqlite:${dbName}");
    }

    function close() {
        if( $this->isOpen() )
            echo "Close database" . PHP_EOL;
    }

    function getHandle() {
        return $this->dbHandle;
    }

    function __destruct() {
        $this->close();
    }
}

?>