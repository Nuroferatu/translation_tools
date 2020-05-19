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