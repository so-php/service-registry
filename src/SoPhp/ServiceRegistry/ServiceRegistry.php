<?php


namespace SoPhp\ServiceRegistry;
use PhpAmqpLib\Channel\AMQPChannel;
use SoPhp\Amqp\EndpointDescriptor;
use SoPhp\PubSub\Event;
use SoPhp\PubSub\PubSub;
use SoPhp\Rpc\Server;
use SoPhp\ServiceRegistry\PubSub\Event\EjectEvent;
use SoPhp\ServiceRegistry\PubSub\Event\GoodbyeEvent;
use SoPhp\ServiceRegistry\PubSub\Event\RegisterEvent;
use SoPhp\ServiceRegistry\PubSub\Event\UnregisterEvent;
use SoPhp\ServiceRegistry\PubSub\SubscriberAbstract;
use SoPhp\ServiceRegistry\Storage\StorageInterface;

/**
 * Class ServiceRegistry
 * @package SoPhp\ServiceRegistry
 */
class ServiceRegistry extends SubscriberAbstract implements ServiceRegistryInterface {
    /** @var  AMQPChannel */
    protected $channel;
    /** @var string  */
    protected $instanceId;
    /** @var  Server[] */
    protected $servers;
    /** @var  Entry[] */
    protected $services;
    /** @var  PubSub */
    protected $pubSub;
    /** @var  StorageInterface */
    protected $storage;
    /** @var array */
    protected $instanceEndpointLookup = array();

    /**
     * @return AMQPChannel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param AMQPChannel $channel
     * @return self
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }


    /**
     * @return PubSub
     */
    public function getPubSub()
    {
        return $this->pubSub;
    }

    /**
     * @param PubSub $pubSub
     * @return self
     */
    public function setPubSub($pubSub)
    {
        $this->pubSub = $pubSub;
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param StorageInterface $storage
     * @return self
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    public function __construct(AMQPChannel $channel, StorageInterface $storage, PubSub $pubSub){
        $this->instanceId = uniqid();
        $this->servers = array();
        $this->services = array();
        $this->setStorage($storage);
        $this->setPubSub($pubSub);
        $this->subscribe($this->getPubSub());
        $this->setChannel($channel);
    }

    function __destruct()
    {
        $this->unregisterAll();
    }

    public function unregisterAll(){
        $this->storage->removeProcessEntries($this->getInstanceId());

        foreach($this->services as $entry){
            /** @var Entry $entry */
            if($entry->getRpcServer()){
                // $entry->getRpcServer()->stop();
            }
        }
        $this->services = array();
    }


    /**
     * @param string $serviceName
     * @param mixed $instance
     * @return EndpointDescriptor
     */
    public function register($serviceName, $instance)
    {
        $server = $this->serveInstance($instance);

        $entry = $this->addService($serviceName, $server->getEndpoint(), getmypid(), $server);
        $this->getStorage()->addEntry($entry);

        // TODO remove, no longer necessary?
        $this->pubSub->publish(new RegisterEvent($serviceName, $server->getEndpoint()));

        return $server->getEndpoint();
    }

    /**
     * @param string $serviceName
     * @param null|mixed $instance
     */
    public function unregister($serviceName, $instance = null)
    {
        foreach($this->services as $index => $entry) {
            /** @var $entry Entry */
            $this->getStorage()->removeEntry($entry);
            $server = $entry->getRpcServer();
            if($instance == null || ($server && $entry->getRpcServer()->getDelegate() == $instance))
            {
                // $server->stop(); // TODO
                $this->pubSub->publish(new UnregisterEvent($entry->getServiceName(), $entry->getEndpoint()));
                $this->removeService($entry->getServiceName(), $entry->getEndpoint());
            }
        }
    }

    /**
     * @param Entry $entry
     */
    public function unregisterEntry(Entry $entry){
        $this->getStorage()->removeEntry($entry);
        $this->pubSub->publish(new UnregisterEvent($entry->getServiceName(), $entry->getEndpoint()));
        $this->removeService($entry->getServiceName(), $entry->getEndpoint());
    }

    /**
     * @return Entry[]
     */
    public function query()
    {
        return $this->getStorage()->findEntries();
    }

    /**
     * @param string $serviceName
     * @return Entry[]
     */
    public function queryForName($serviceName)
    {
        return $this->getStorage()->findEntries($serviceName);
    }

    /**
     * @param mixed $instance
     * @return string
     */
    protected function toIndex($instance){
        return spl_object_hash($instance);
    }

    /**
     * @param $instance
     * @return Server
     */
    protected function serveInstance($instance)
    {
        $server = new Server($this->getChannel());
        $server->serve($instance);
        $server->start();
        return $server;
    }

    /**
     * @param Event $e
     */
    public function onHello(Event $e)
    {
        // TODO: Implement onHello() method.
    }

    /**
     * @param Event $e
     */
    public function onGoodbye(Event $e)
    {
        /** @var GoodbyeEvent $e */
        $e = GoodbyeEvent::fromJson($e->toJson());
        $this->removeServices($e->getProcessId());

    }

    /**
     * @param Event $e
     */
    public function onEject(Event $e)
    {
        /** @var EjectEvent $e */
        $e = EjectEvent::fromJson($e->toJson());
        $this->removeServices($e->getProcessId());

    }

    /**
     * @param Event $e
     */
    public function onRegister(Event $e)
    {
        /** @var RegisterEvent $e */
        $e = RegisterEvent::fromJson($e->toJson());
        $this->addService($e->getServiceName(), $e->getEndpoint(), $e->getProcessId());
    }

    /**
     * @param Event $e
     */
    public function onUnregister(Event $e)
    {
        /** @var UnregisterEvent $e */
        $e = UnregisterEvent::fromJson($e->toJson());
        $this->removeService($e->getServiceName(), $e->getEndpoint());
    }

    /**
     * @param Event $e
     */
    public function onSync(Event $e)
    {
        // TODO: Implement onSync() method.
    }

    /**
     * @param string $serviceName
     * @param EndpointDescriptor $endpoint
     * @param int $processId
     * @param null|Server $server
     * @return Entry
     */
    protected function addService($serviceName, $endpoint, $processId, $server = null)
    {
        $entry = new Entry();
        $entry->setServiceName($serviceName);
        $entry->setEndpoint($endpoint);
        $entry->setProcessId($processId);
        $entry->setRpcServer($server);
        $this->services[] = $entry;
        return $entry;
    }

    /**
     * @param $serviceName
     * @param null $endpoint
     */
    protected function removeService($serviceName, $endpoint = null)
    {
        foreach($this->services as $index => $entry)
        {
            if($entry->getServiceName() == $serviceName
                && ($entry->getEndpoint() == $endpoint || $endpoint == null))
            {
                unset($this->services[$index]);
            }
        }
    }

    /**
     * @param int $processId
     */
    protected function removeServices($processId)
    {
        foreach($this->services as $index => $entry)
        {
            if($entry->getProcessId() == $processId)
            {
                unset($this->services[$index]);
            }
        }
    }

}