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

$registry = new ServiceRegistry($ch, $mongo, $pubSub);
$entries = $registry->queryForName('Greet');
var_dump($entries);

foreach($entries as $entry){
    /** @var Entry $entry */
    $endpoint = $entry->getEndpoint();
    $client = new Client($endpoint, $ch);
    try {
        echo "Attempt to call 'hello' on `{$entry->getServiceName()}`` for instance {$entry->getProcessId()}\n";
        echo $client->call('hello', $name) . PHP_EOL;
    } catch (TimeoutException $e){
        echo "Unregistered {$entry->getServiceName()} for instance ".$entry->getProcessId()."\n";
        $registry->unregisterEntry($entry);
    }
}
echo "done";

unset($registry);