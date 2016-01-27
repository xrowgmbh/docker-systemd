#!/usr/bin/php
<?php

exec( '/usr/bin/env', $lines );
$key=false;
$value=false;
foreach( $lines as $line )
{
    list( $key, $value ) = preg_split("/[=]+/", $line, 2 );

    if ( strpos($key, '_SERVICE_HOST') !== false and preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $value) ){
        $key = str_ireplace( '_SERVICE_HOST', "", $key);
        file_put_contents( "/etc/hosts", $value . " $key\n", FILE_APPEND | LOCK_EX);
    }
}
exit(0);