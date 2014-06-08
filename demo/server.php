<?php
use SoPhp\Rpc\Server;

require_once 'bootstrap.php';

class Greet {
    public function hello($name){
        return "Hello $name, how are you?";
    }
}

$registry = new \SoPhp\ServiceRegistry\ServiceRegistry($ch, $mongo);
$endpoint = $registry->register('Greet', new Greet());

echo "RPC Server started, point clients to " . $endpoint . PHP_EOL;

while(count($ch->callbacks)){
    $ch->wait();
}
