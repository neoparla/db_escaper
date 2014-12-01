<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 12/1/14
 * Time: 1:09 PM
 */

namespace NeoParla\DbEscaper\Statement;

use NeoParla\DbEscaper\DbEscaperException;
use NeoParla\DbEscaper\Link;
use NeoParla\DbEscaper\Result\DbIterator;
use NeoParla\DbEscaper\Statement\Binding\Binding;
use NeoParla\DbEscaper\Statement\Binding\BindingException;
use NeoParla\DbEscaper\Statement\Binding\Type;

class DbStatement {

    /**
     * @var Link
     */
    private $link;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $label;

    /**
     * Real query to be executed.
     *
     * @var string
     */
    private $real_query;

    /**
     * Parameters to bind.
     *
     * @var array
     */
    private $parameters = array();

    /**
     * @param Link $link
     * @param $query
     * @param $label
     */
    public function __construct(Link $link, $query, $label) {
        $this->link     = $link;
        $this->query    = $query;
        $this->label    = $label;
        $this->real_query  = $query;
    }

    /**
     * @return mixed|DbIterator
     */
    public function execute() {
        $this->link->connect();
        var_dump($this->getRealQuery());
    }

    /**
     * Bind parameter.
     *
     * @param string $to_replace Piece to replace with valid value.
     * @param mixed $value Original value to escape.
     * @param string $type_value Type of value to bind.
     * @return DbStatement
     * @throws BindingException Thrown when invalid binding is used.
     */
    public function bindParam($to_replace, &$value, $type_value) {
        $class = __NAMESPACE__ . '\\Binding\\' . $type_value;
        if (!class_exists($class)) {
            throw new BindingException('Invalid binding type "' . $type_value . '"');
        }

        $this->parameters[] = array(
            'search'    => $to_replace,
            'binding'   => new $class($this->link, $value)
        );

        return $this;
    }

    public function getRealQuery() {
        $this->real_query = $this->query;
        foreach ($this->parameters as $param) {
            $this->real_query = str_replace($param['search'], $param['binding']->getRealValue(), $this->real_query);
        }

        return $this->real_query;
    }

    public function getError() {
        return $this->link->getLastError();
    }
}