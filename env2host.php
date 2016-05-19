#!/usr/bin/env php
<?php
define( "KUBE_MASTER", "192.168.255.4");

$pods = file_get_contents( "http://" . KUBE_MASTER . ":8080/api/v1/pods/" );
$pods = json_decode( $pods );
$hostname = gethostname();

if( isset( $pods->items ) )
{
    foreach( $pods->items as $pod )
    {
        if( $pod->metadata->name == $hostname )
        {
            $namespace = $pod->metadata->namespace;
            break;
        }
    }
}

if( isset( $namespace ) )
{
    $services = file_get_contents( "http://" . KUBE_MASTER . ":8080/api/v1/namespaces/" . $namespace . "/services" );
    $services = json_decode( $services );
    $hostsAppend = "";
    $hosts = array();

    exec( 'cat /etc/hosts', $lines );
    foreach( $lines as $line )
    {
        $hosts[] = preg_split("/\s+/", $line, 2, PREG_SPLIT_NO_EMPTY );
    }

    if( isset( $services->items ) )
    {
        foreach( $services->items as $service )
        {
            $present = false;
            foreach( $hosts as $host )
            {
                if( $host[1] == $service->metadata->name )
                {
                    $present = true;
                    break;
                }
            }

            if($present != true)
            {
                $hostsAppend .= $service->spec->clusterIP . "    " . $service->metadata->name . "\n";
            }
        }
        file_put_contents( "/etc/hosts", $hostsAppend, FILE_APPEND | LOCK_EX );
    }
}
exit(0);