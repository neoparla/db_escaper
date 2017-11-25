<?php

namespace NeoParla\DbEscaper\Statement\Binding;

use NeoParla\DbEscaper\Link;

class StringBinder implements Binding {

    private $value;
    private $link;
    public function __construct(Link $link, $value)
    {
        $this->value = $value;
        $this->link = $link;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return is_string($this->value);
    }

    /**
     * @return string
     * @throws BindingException Thrown when invalid type
     */
    public function getRealValue()
    {
        if (!$this->isValid()) {
            throw new BindingException('Not a valid String value');
        }

        return '\'' . $this->link->realEscape($this->value) . '\'';
    }
}