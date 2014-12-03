<?php

namespace NeoParla\DbEscaper\Statement\Binding;


use NeoParla\DbEscaper\Link;

class Field implements Binding {

    private $value;
    public function __construct(Link $link, $value)
    {
        $this->value = $value;
    }

    public function isValid()
    {
        return preg_match('/^[\x{0000}-\x{FFFF}]+$/u', $this->value);
    }

    public function getRealValue()
    {
        if (!$this->isValid()) {
            throw new BindingException('"' . (string) $this->value . '" is not a valid Field value');
        }

        return "`{$this->value}`";
    }
}