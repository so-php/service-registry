<?php

use SoPhp\Amqp\EndpointDescriptor;
use SoPhp\Rpc\Client;
use SoPhp\Rpc\Exception\Server\TimeoutException;
use SoPhp\ServiceRegistry\Entry;
use SoPhp\ServiceRegistry\ServiceRegistry;

require_once 'bootstrap.php';

if(count($argv) < 2){
    echo "Usage:\n\tphp client.php <name>\n\n";
}
$name = @$argv[1] ?: uniqid('john_');

$registry = new ServiceRegistry($ch, $mongo);
$registrations = $registry->queryForName('Greet');

foreach($registrations as $registration)
{
    $endpoint = $registration->getService()->getEndpoint();
    try {
        echo "Attempt to call 'hello' on `{$registration->getServiceName()}`` for instance {$registration->getProcessId()}\n";
        echo $registration->getService()->call('hello', $name) . PHP_EOL;
    } catch (TimeoutException $e){
        echo "Unregistered {$registration->getServiceName()} for instance ".$registration->getProcessId()."\n";
        $registration->unregister();
    }
}
echo "done";

unset($registry);