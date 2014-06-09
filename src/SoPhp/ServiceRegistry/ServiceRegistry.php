<?php


namespace SoPhp\ServiceRegistry;
use PhpAmqpLib\Channel\AMQPChannel;
use SoPhp\Amqp\EndpointDescriptor;
use SoPhp\Rpc\Client;
use SoPhp\Rpc\Server;
use SoPhp\Rpc\ServiceInterface;
use SoPhp\ServiceRegistry\Storage\StorageInterface;

/**
 * Class ServiceRegistry
 * @package SoPhp\ServiceRegistry
 */
class ServiceRegistry implements ServiceRegistryInterface {
    /** @var  AMQPChannel */
    protected $channel;
    /** @var string  */
    protected $instanceId;
    /** @var  StorageInterface */
    protected $storage;
    /** @var  ServiceRegistration[] */
    protected $localRegistrations;

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

    public function __construct(AMQPChannel $channel, StorageInterface $storage){
        $this->instanceId = uniqid();
        $this->localRegistrations = array();
        $this->setStorage($storage);
        $this->setChannel($channel);
    }

    function __destruct()
    {
        $this->unregisterAll();
    }

    // TODO
    public function unregisterAll(){
        $this->storage->removeProcessEntries(getmypid());
        foreach($this->localRegistrations as $localReg)
        {
            //$localReg->getService()->stop();
        }

        $this->localRegistrations = array();
    }


    /**
     * @param string $serviceName
     * @param ServiceInterface $instance
     * @return ServiceRegistration
     */
    public function register($serviceName, $instance)
    {
        $server = $this->serveInstance($instance);

        $registration = new ServiceRegistration($serviceName, $server, $this);
        $this->localRegistrations[] = $registration;
        $this->getStorage()->addEntry($registration->toEntry());

        return $registration;
    }

    /**
     * @param ServiceRegistration $registration
     */
    public function unregister(ServiceRegistration $registration)
    {
        foreach($this->localRegistrations as $index => $localRegistration) {
            if ($registration->getInstanceId() == $localRegistration->getInstanceId()) {
                unset($this->localRegistrations[$index]);
            }
        }
        $this->getStorage()->removeEntry($registration->toEntry());
    }

    /**
     * @return Entry[]
     */
    public function query()
    {
        return $this->queryForName();
    }

    /**
     * @param string $serviceName
     * @return ServiceRegistration[]
     */
    public function queryForName($serviceName = null)
    {
        $matches = array();
        foreach($this->localRegistrations as $localReg)
        {
            if($serviceName == null || $localReg->getServiceName() == $serviceName)
            {
                $matches[] = $localReg;
            }
        }

        foreach($this->getStorage()->findEntries($serviceName) as $entry) {
            $client = $this->proxyInstance($entry->getEndpoint());
            $remoteReg = new ServiceRegistration($entry->getServiceName(), $client, $this);
            $remoteReg->setProcessId($entry->getProcessId());
            $matches[] = $remoteReg;
        }

        return $matches;
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
     * @param EndpointDescriptor $endpoint
     * @return Client
     */
    protected function proxyInstance(EndpointDescriptor $endpoint)
    {
        $proxy = new Client($endpoint, $this->getChannel());
        return $proxy;
    }

}