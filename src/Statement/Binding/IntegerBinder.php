<?php

namespace NeoParla\DbEscaper\Statement\Binding;

use NeoParla\DbEscaper\Link;

class IntegerBinder implements Binding {

    private $value;
    public function __construct(Link $link, $value)
    {
        $this->value = $value;
    }

    public function isValid()
    {
        return (
            is_numeric($this->value)
            && $this->value == intval($this->value)
        );
    }

    public function getRealValue()
    {
        if (!$this->isValid()) {
            throw new BindingException('Not a valid Integer value');
        }

        return intval($this->value);
    }
}