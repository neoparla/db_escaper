<?php

namespace NeoParla\DbEscaper;

use NeoParla\DbEscaper\Link\MySql;
use NeoParla\DbEscaper\Statement\DbStatement;

class DbEscaper {

    /**
     * @var DbEscaper[]
     */
    private static $instances = array();

    /**
     * @var Link
     */
    private $link;

    private function __construct($connection_data) {
        $this->link = new MySql();
        $this->link->setConnectionData($connection_data);
    }

    /**
     * @param array $connection_data
     * @return DbEscaper
     */
    public static function init(array $connection_data) {
        asort($connection_data);
        $key = json_encode($connection_data);

        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self($connection_data);
        }

        return self::$instances[$key];
    }

    /**
     * @param $query
     * @return mixed|Result\DbIterator
     */
    public function query($query) {
        $this->link->connect();
        return $this->link->query($query);
    }

    /**
     * @return DbStatement
     */
    public function prepare($query, $label) {
        return new DbStatement($this->link, $query, $label);
    }

    /**
     * @return Link
     */
    public function getLink()
    {
        return $this->link;
    }
} 