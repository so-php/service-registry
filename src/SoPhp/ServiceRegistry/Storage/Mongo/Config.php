<?php


namespace SoPhp\ServiceRegistry\Storage\Mongo;


class Config {
    const DEFAULT_HOST = 'localhost';
    const DEFAULT_PORT = 27017;
    const DEFAULT_DATABASE = 'sophp';
    const DEFAULT_COLLECTION = 'serviceregistry';

    /** @var  string */
    protected $host;
    /** @var  int */
    protected $port;
    /** @var  string */
    protected $database;
    /** @var  string */
    protected $collection;

    /**
     * @param string $host
     * @param int $port
     * @param string $database
     * @param string $collection
     */
    public function __construct($host = self::DEFAULT_HOST,
                                $port = self::DEFAULT_PORT,
                                $database = self::DEFAULT_DATABASE,
                                $collection = self::DEFAULT_COLLECTION)
    {
        $this->setHost($host);
        $this->setPort($port);
        $this->setDatabase($database);
        $this->setCollection($collection);
    }

    /**
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param string $collection
     * @return self
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param string $database
     * @return self
     */
    public function setDatabase($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return self
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return self
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

} 