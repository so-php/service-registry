<?php


namespace SoPhp\ServiceRegistry\Storage;


use SoPhp\ServiceRegistry\Entry;

interface StorageInterface {
    /**
     * @param Entry $entry
     */
    public function addEntry(Entry $entry);

    /**
     * @param Entry $entry
     */
    public function removeEntry(Entry $entry);

    /**
     * @param int $processId
     */
    public function removeProcessEntries($processId);

    /**
     * @param null|string $serviceName
     * @return Entry[]
     */
    public function findEntries($serviceName = null);
} 