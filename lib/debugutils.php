<?php

function printVar( $var, string $varName ) {
    echo "DBG: '" . TColor::cyan($varName) . "' ";
    echo "null[" . ( is_null($var) ? "T" : "F" ) . "] ";
    echo "set[" . ( isset($var) ? "T" : "F" ) . "] ";
    echo "empty[" . ( empty($var) ? "T" : "F" ) . "] ";
    if( !is_null($var) ) {
        if( is_object( $var ) || is_array( $var ) ) {
            print_r( $var );
        }
        else {
            echo "type[" . TColor::cyan( gettype($var) ) . "]=";
            if( is_bool($var) )
                echo Tcolor::green( ($var === true) ? "true" : "false" );
            else    // TODO: Add rest of types
                print_r( $var );
        }
    }
    echo PHP_EOL;
}

?>