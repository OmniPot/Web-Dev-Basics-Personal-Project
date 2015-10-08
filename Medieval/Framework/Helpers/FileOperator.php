<?php

namespace Medieval\Framework\Helpers;

use DateInterval;
use DateTime;
use Medieval\Config\AppConfig;
use Medieval\Framework\Config\FrameworkConfig;

class FileOperator {

    private static $_builder = null;
    private static $_indent = 4;

    public static function writeFile( $appContents, $actionContents ) {
        if ( !strpos( self::$_builder, '<?php' ) || !self::$_builder ) {
            self::$_builder = "<?php";
        }

        self::appendVariable( 'expires' );
        self::appendExpirationDate();

        self::appendVariable( 'appStructure' );
        self::appendContents( $appContents, true );

        self::appendVariable( 'actionsStructure' );
        self::appendContents( $actionContents, true );

        return self::$_builder;
    }

    private function appendContents( $array, $final = false ) {
        $endLine = $final ? ';' : ',';

        if ( empty( $array ) ) {
            self::$_builder .= "[ ]" . $endLine . "\n";
            self::$_indent -= 4;
        } else {
            self::$_builder .= "[\n";

            foreach ( $array as $key => $value ) {
                self::appendKey( $key );

                if ( is_array( $value ) ) {
                    self::$_indent += 4;

                    if ( end( $array ) == $key ) {
                        self::appendContents( $value, true );
                    } else {
                        self::appendContents( $value );
                    }
                } else {
                    self::appendValue( $value );
                }
            }

            self::$_indent -= 4;
            self::$_builder .= "\n" . str_repeat( ' ', self::$_indent ) . "]$endLine\n";
        }
    }

    private function appendExpirationDate() {
        $expirationTime = new DateTime( 'now', new \DateTimeZone( 'Europe/Sofia' ) );
        $formatted = $expirationTime
            ->add( new DateInterval( FrameworkConfig::APP_STRUCTURE_CONFIG_RENEW_TIME ) )
            ->format( 'Y-m-d H:i:s' );

        self::$_builder .= '\'' . $formatted . '\';';
    }

    private function appendVariable( $name ) {
        self::$_builder .= "\n\n$$name = ";
        self::$_indent = 4;
    }

    private function appendKey( $key ) {
        self::$_builder .= str_repeat( ' ', self::$_indent ) . '\'' . $key . '\' => ';
    }

    private function appendValue( $value ) {
        self::$_builder .= '\'' . $value . '\',' . "\n";
    }
}