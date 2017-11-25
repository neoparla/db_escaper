<?php

namespace NeoParla\DbEscaper\Statement\Binding;

use NeoParla\DbEscaper\Link;
use NeoParla\DbEscaper\Statement\DbTuple;

class TupleBinder implements Binding {

    /**
     * @var DbTuple
     */
    private $value;

    /**
     * @var Link
     */
    private $link;

    public function __construct(Link $link, $value)
    {
        $this->link = $link;
        $this->value = $value;
    }

    public function isValid()
    {
        return $this->value instanceof DbTuple;
    }

    public function getRealValue()
    {
        if (!$this->isValid()) {
            throw new BindingException('Not valid tuple definition');
        }

        return $this->value->buildValues($this->link);
    }
}