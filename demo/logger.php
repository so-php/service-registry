<?php
use SoPhp\Rpc\Server;
use SoPhp\ServiceRegistry\PubSub\Logger;

require_once 'bootstrap.php';

$logger = new Logger();
$logger->subscribe($pubSub);

echo "Service Registry Logger Started. ctrl+c to quit. \n";

while(count($ch->callbacks)){
    $ch->wait();
}
