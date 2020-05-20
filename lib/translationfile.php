<?php

class TranslationFile {
    private $handle;
    private $name;
    private $srcLang;
    private $destLang;

    // FatalError means... this class object is usles, we may as well terminate application
    private function fatalError( $message ) {
        throw new RuntimeException( $message . PHP_EOL );
    }

    // Simlle switch to validate lang code
    private function isValidLangCode($langCode) {
        switch( $langCode ) {
            case "en":
            case "pl":
                return true;
        }
        return false;
    }

    // First line (header) must contain language pair in format: srcLang;destLang
    private function processHeader() {
        $firstLine = fgets( $this->handle );
        $firstLine = str_replace(array("\n", "\r", "\t", " "), '', $firstLine);
        $langCodes = explode( ";", $firstLine );

        if( count($langCodes) < 2 )
            $this->fatalError( "Syntax error in file '" . $this->name . "' Invalid header - expected lang pair" . PHP_EOL . "Found: " . $firstLine );

        $this->setSrcLang( $langCodes[0] );
        $this->setDestLang( $langCodes[1] );
    }

    function __construct( $fileName ) {
        $this->name = $fileName;
        $this->handle = @fopen( $fileName, "r" );
        if( !$this->handle ) {
            $this->handle = null;
            $this->fatalError( "File '$this->name' does not exist or can not be open" );
        }

        $this->processHeader();
    }

    function __destruct() {
        if( !is_null($this->handle) ) {
            fclose( $this->handle );
        }
    }

    function isEOF() {
        if( !is_null($this->handle) ) {
            return feof($this->handle);
        }
        return true;
    }

    // Returns Key=>Val array where key is source lang word, val is array of destination translations.
    // On error returns null.
    // Sample line from translation file:
    // able;zdolny,utalentowany    ,zdatny
    // -----------------------------------------------------------------------
    // TODO: Better error handling - errorLog array with line numbers as final report would be osom
    function get() {
        $line = fgets( $this->handle );
        $line = str_replace( array("\n", "\r", "\t", " "), '', $line );

        if( empty( $line ) )
            return null;

        $keyValArray = explode(";", $line );
        if( count($keyValArray) < 2 ) {
            echo "* Error in translation file. Invalid format" . PHP_EOL;
            return null;
        }

        if( empty( $keyValArray[0] ) ) {
            echo "* Error in translation file. Missing source lang word" . PHP_EOL;
            return null;
        }

        if( empty( $keyValArray[1] ) ) {
            echo "* Error in translation file. Missing destination translation words" . PHP_EOL;
            return null;
        }

        return $keyValArray;
    }

    function getSrcLang()  {
        return $this->srcLang;
    }

    function setSrcLang( $langCode ) {
        $langCode = strtolower( $langCode );
        if( !$this->isValidLangCode( $langCode ) )
            $this->fatalError( "Source lang code '${langCode}' is Unknown or Unsuported" );

        $this->srcLang = $langCode;
    }

    function getDestLang() {
        return $this->destLang;
    }

    function setDestLang( $langCode ) {
        $langCode = strtolower( $langCode );
        if( !$this->isValidLangCode( $langCode ) )
            $this->fatalError( "Destination lang code '${langCode}' is Unknown or Unsuported" );
        $this->destLang = $langCode;
    }
}

?>