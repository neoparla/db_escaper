<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 12/1/14
 * Time: 1:46 PM
 */

namespace NeoParla\DbEscaper\Statement\Binding;


use NeoParla\DbEscaper\Link;

class Double implements Binding {

    private $value;
    public function __construct(Link $link, $value)
    {
        $this->value = $value;
    }

    public function isValid()
    {
        if (
            is_numeric($this->value)
            && $this->value == doubleval($this->value)
        )
        return true;
    }

    public function getRealValue()
    {
        if (!$this->isValid()) {
            throw new BindingException('"' . (string) $this->value . '" is not a valid Double value');
        }

        return doubleval($this->value);
    }
}