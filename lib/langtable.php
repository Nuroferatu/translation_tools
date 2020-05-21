<?php

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

?>