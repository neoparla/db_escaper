<?php

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
        return (
            is_numeric($this->value)
            && $this->value == doubleval($this->value)
        );
    }

    public function getRealValue()
    {
        if (!$this->isValid()) {
            throw new BindingException('Not a valid Double value');
        }

        return doubleval($this->value);
    }
}