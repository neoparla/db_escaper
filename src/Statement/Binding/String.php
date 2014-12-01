<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 12/1/14
 * Time: 1:46 PM
 */

namespace NeoParla\DbEscaper\Statement\Binding;


use NeoParla\DbEscaper\Link;

class String implements Binding {

    private $value;
    private $link;
    public function __construct(Link $link, $value)
    {
        $this->value = $value;
        $this->link = $link;
    }

    public function isValid()
    {
        return is_string($this->value);
    }

    public function getRealValue()
    {
        if (!$this->isValid()) {
            throw new BindingException('"' . (string) $this->value . '" is not a valid String value');
        }

        return '\'' . $this->link->realEscape($this->value) . '\'';
    }
}