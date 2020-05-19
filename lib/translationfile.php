<?php

class TranslationFile {
    private $handle;
    private $name;
    
    function __construct( $fileName ) {
        $this->name = $fileName;
        $this->handle = @fopen( $fileName, "r" );
        if( !$this->handle ) {
            $this->handle = null;
            throw new RuntimeException( "File '$this->name' does not exist or can not be open" );
        }

        echo "File '${fileName}' opened with handle " . $this->handle . PHP_EOL;
    }

    function __destruct() {
        if( !is_null($this->handle) ) {
            echo "Close file " . $this->name . " with handle " . $this->handle . PHP_EOL;
            fclose( $this->handle );
        }
    }

    function isEOF() {
        if( !is_null($this->handle) ) {
            return feof($this->handle);
        }
        return true;
    }
}
?>
