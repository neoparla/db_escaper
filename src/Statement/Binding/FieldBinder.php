<?php

namespace NeoParla\DbEscaper\Statement\Binding;

use NeoParla\DbEscaper\Link;

class FieldBinder implements Binding {

    private $value;
    public function __construct(Link $link, $value)
    {
        $this->value = $value;
    }

    public function isValid()
    {
        return (
            (
                is_string($this->value)
                && preg_match('/^[\x{0001}-\x{FFFF}]+$/u', $this->value)
            )
            && !preg_match('@\s$@', $this->value)
            && !preg_match('@(/|\\\|\.|:|;|-)@', $this->value)
        );
    }

    public function getRealValue()
    {
        if (!$this->isValid()) {
            throw new BindingException('Not a valid Field value');
        }

        return "`{$this->value}`";
    }
}