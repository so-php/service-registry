<?php


namespace SoPhp\ServiceRegistry\Storage\Mongo;


use MongoClient;
use MongoCollection;
use SoPhp\ServiceRegistry\Entry;
use SoPhp\ServiceRegistry\Storage\StorageInterface;

class Mongo implements StorageInterface {
    /** @var  Config */
    protected $config;
    /** @var  MongoClient */
    protected $client;
    /** @var  MongoCollection */
    protected $collection;

    /**
     * @return MongoClient
     */
    public function getClient()
    {
        if(!$this->client){
            $config = $this->getConfig();
            $cStr = sprintf("mongodb://%s:%s", $config->getHost(), $config->getPort());
            $this->client = new MongoClient($cStr);
        }
        return $this->client;
    }

    /**
     * @param MongoClient $client
     * @return self
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config ?: new Config();
    }

    /**
     * @param Config $config
     * @return self
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return MongoCollection
     */
    protected function getCollection(){
        if(!$this->collection){
            $config = $this->getConfig();
            $client = $this->getClient();
            $db = $client->{$config->getDatabase()};
            $this->collection = $db->{$config->getCollection()};
        }
        return $this->collection;
    }



    public function __construct(Config $config = null){
        $this->setConfig($config);
    }

    /**
     * @param Entry $entry
     */
    public function addEntry(Entry $entry)
    {
        $document = $entry->toStorageArray();
        $data = array(
            'processId' => $entry->getProcessId(),
            'instanceId' => $entry->getInstanceId(),
            'serviceName' => $entry->getServiceName(),
            'endpoint' => $entry->getEndpoint()->toJson(),
        );
        $r = $this->getCollection()->update($data, $document, array('upsert' => true));
    }

    /**
     * @param Entry $entry
     */
    public function removeEntry(Entry $entry)
    {
        $r = $this->getCollection()->remove(array(
            'processId' => $entry->getProcessId(),
            'serviceName' => $entry->getServiceName(),
            'endpoint' => $entry->getEndpoint()->toJson(),
        ));
    }

    /**
     * @param int $processId
     */
    public function removeProcessEntries($processId)
    {
        $this->getCollection()->remove(array(
            'processId' => $processId,
        ));
    }

    /**
     * @param null|string $serviceName
     * @return Entry[]
     */
    public function findEntries($serviceName = null)
    {
        $query = $serviceName ? array('serviceName' => $serviceName) : array();
        $cursor = $this->getCollection()->find($query);
        $entries = array();
        foreach($cursor as $doc){
            $entries[] = Entry::fromStorageArray($doc);
        }
        return $entries;
    }
}