<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPConnection;
use SoPhp\PubSub\PubSub;

// configured for vagrantfile that is included in the project
$conn = new AMQPConnection('localhost', 5672, 'guest', 'guest');
$ch = $conn->channel();

define('EXCHANGE', 'foo-exchange');

$pubSub = new PubSub($ch, EXCHANGE);

$mongo = new \SoPhp\ServiceRegistry\Storage\Mongo\Mongo();