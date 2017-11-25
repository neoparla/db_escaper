<?php

namespace NeoParla\DbEscaper\Link;

use mysqli;
use mysqli_result;
use NeoParla\DbEscaper\Link;
use NeoParla\DbEscaper\Result\DbIterator;
use NeoParla\DbEscaper\Result\MysqlIterator;

class MySqlDbEscaper implements Link
{

    const DEFAULT_PORT = 3306;
    const DEFAULT_CHARSET = 'utf8';

    private $host;
    private $user;
    private $pass;
    private $schema;
    private $port = self::DEFAULT_PORT;

    /**
     * @var Mysqli
     */
    private $link;

    /**
     * @var boolean
     */
    private $is_connected = false;

    /**
     * @var string[]
     */
    private $errors = array();

    public function __construct()
    {
        $this->link = new mysqli();
    }

    public function getShellCommand()
    {
        return <<<COMMAND
mysql -h{$this->host} -u{$this->user} -p{$this->pass} -P{$this->port} {$this->schema}
COMMAND;

    }

    /**
     * Do real connection to Db server.
     *
     * @throws DbLinkException Thrown when no connection is possible.
     */
    public function connect()
    {
        if (!$this->is_connected) {
            $this->link->real_connect($this->host, $this->user, $this->pass, $this->schema, $this->port);
            if ($this->link->connect_error) {
                $this->errors[] = $this->link->connect_error;
                throw new DbLinkException($this->link->connect_error, $this->link->connect_errno);
            }

            $this->link->set_charset(self::DEFAULT_CHARSET);
            $this->is_connected = true;
        }
    }

    /**
     * Using native connector, query against Db server.
     *
     * @param string $query Query to be performed.
     * @return mixed|DbIterator
     * @throws DbLinkException Thrown when an error occur on querying.
     */
    public function query($query)
    {
        $result = $this->link->query($query);
        if (false === $result) {
            $message = <<<ERROR
Error on '{$this->user}@{$this->host}' when executing
{$query}

Message error: {$this->link->error}
ERROR;

            $this->errors[] = $message;
            throw new DbLinkException($message);
        }

        if (!$result instanceof \mysqli_result) {
            return $result;
        }

        return new MysqlIterator($result);
    }

    /**
     * Close current connection.
     */
    public function close()
    {
        $this->link->close();
    }

    /**
     * Using native connector, string provided will be escaped.
     *
     * @param string $string String to be escaped.
     * @return string
     */
    public function realEscape($string)
    {
        return $this->link->real_escape_string($string);
    }

    /**
     * If method exists on native connector, will call it.
     *
     * @param string $name Method to be called.
     * @param array $arguments Arguments to use on method call.
     *
     * @return mixed|null
     */
    public function __call($name, array $arguments)
    {
        if (is_callable(array($this->link, $name))) {
            return call_user_func_array(array($this->link, $name), $arguments);
        } else {
            $this->errors[] = get_class($this->link) . '::' . $name . ' is not callable method';
            return null;
        }
    }

    /**
     * Set connection data at once.
     *
     * @param string[] $data Data needed to connect to Db server.
     */
    public function setConnectionData(array $data)
    {
        $this->host = $data['host'];
        $this->user = $data['user'];
        $this->pass = $data['pass'];

        $this->schema = $data['schema'];

        if (isset($data['port'])) {
            $this->port = $data['port'];
        }
    }

    /**
     * Get last error.
     *
     * @return string
     */
    public function getLastError()
    {
        $i = count($this->errors) - 1;

        return ($i >= 0)
            ? $this->errors[$i]
            : null;
    }

    /**
     * Get all errors.
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get connection data.
     *
     * @return array
     */
    public function getConnectionData()
    {
        return array(
            'host' => $this->host,
            'pass' => $this->pass,
            'port' => $this->port,
            'schema' => $this->schema,
            'user' => $this->user,
        );
    }
}