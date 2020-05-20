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

?>