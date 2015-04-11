<?php

namespace NeoParla\DbEscaper;

use NeoParla\DbEscaper\Link\DbLinkException;
use NeoParla\DbEscaper\Result\DbIterator;

interface Link
    {

    /**
     * Do real connection to Db server.
     *
     * @return boolean Returns TRUE if connection is done, FALSE otherwise.
     */
    public function connect();

    /**
     * Using native connector, query against Db server.
     *
     * @param string $query Query to be performed.
     * @return mixed|DbIterator
     * @throws DbLinkException Thrown when an error occur on querying.
     */
    public function query($query);

    /**
     * Close current connection.
     */
    public function close();

    /**
     * Using native connector, string provided will be escaped.
     *
     * @param string $string String to be escaped.
     * @return string
     */
    public function realEscape($string);

    /**
     * If method exists on native connector, will call it.
     *
     * @param string $name Method to be called.
     * @param array $arguments Arguments to use on method call.
     *
     * @return mixed
     */
    public function __call($name, array $arguments);

    /**
     * Set connection data at once.
     *
     * @param string[] $data Data needed to connect to Db server.
     */
    public function setConnectionData(array $data);

    /**
     * Get last error.
     *
     * @return string
     */
    public function getLastError();

    /**
     * Get shell command.
     *
     * @return string
     */
    public function getShellCommand();

    /**
     * Get connection data.
     *
     * @return array
     */
    public function getConnectionData();

} 