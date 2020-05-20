<?php

// Proof of concept...

class TColor {
    private const ESCCMD = "\e[";
    private const RED = TColor::ESCCMD . "31m";
    private const CYAN = TColor::ESCCMD . "36m";
    private const RESET = TColor::ESCCMD . "0m";

    private static function fmt( $color, $val ) { return ( $color . $val . Tcolor::RESET ); }

    // Set of decorators...
    static function red( $val )  { return TColor::fmt( Tcolor::RED, $val ); }
    static function cyan( $val ) { return TColor::fmt( Tcolor::CYAN, $val ); }
}

function reportError( string $who, string $errorMessage ) {
    echo TColor::red( "[ERROR]" ) . " at '" . TColor::cyan($who) . "' with message '" . TColor::cyan($errorMessage) . "'" . PHP_EOL;
}

function fatalError( string $errorMessage ) {
    throw new RuntimeException( $errorMessage );
}

function exceptionHandler( $exception) {
    echo TColor::red( "[UNCAUGHT EXCEPTION]: " ) . PHP_EOL . $exception . PHP_EOL;
}

// Should be called at application start to install hander
function initErrorHandler() {
    set_exception_handler('exceptionHandler');
}

class Validate {
    static function fatalOnNull( $var, string $paramName, string $where ) {
        if( !is_null( $var ) )
            return false;
        fatalError( "${paramName} is null. Passed to '${where}'" ); 
    }

    static function fatalOnEmpty( $var, string $paramName, string $where ) {
        if( !empty( $var ) )
            return false;
        fatalError( "${paramName} is empty. Passed to '${where}'" ); 
    }

    static function fatalOnNullOrEmpty( $var, string $paramName, string $where ) {
        fatalOnNull( $var, $paramName, $where );
        fatalOnEmpty( $var, $paramName, $where );
    }
}

?>