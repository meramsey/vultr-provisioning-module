<?php

namespace MGModule\vultr\mgLibs\MySQL;

/**
 * MySQL Results Class
 */
class Result
{
    /**
     * @var PDOStatement
     */
    private $result;

    /**
     * Constructor
     *
     * @param PDOStatement $result
     * @param int $id
     */
    public function __construct(PDOStatement $result, $id = null)
    {
        $this->result = $result;
        $this->id = $id;
    }

    /**
     * Fetch All Records
     *
     * @return array
     */
    public function fetchAll(): array
    {
        return $this->result->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Fetch one record
     *
     * @return array
     */
    public function fetch()
    {
        return $this->result->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Fetch One Column From First Record
     *
     * @param string $name
     * @return array
     */
    public function fetchColumn($name = null): array
    {
        $data = $this->result->fetch(\PDO::FETCH_BOTH);

        if ($name) {
            return $data[$name];
        } else {
            return $data[0];
        }
    }

    /**
     * Get ID Last Inserted Record
     *
     * @return int
     */
    public function getID(): ?int
    {
        return $this->id;
    }
}
