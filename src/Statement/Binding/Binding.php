<?php

namespace NeoParla\DbEscaper\Statement\Binding;

use NeoParla\DbEscaper\Link;

interface Binding {

    const STRING  = 'StringBinder';
    const INTEGER = 'IntegerBinder';
    const FIELD   = 'FieldBinder';
    const TUPLE   = 'TupleBinder';
    const DOUBLE  = 'DoubleBinder';

    public function __construct(Link $link, $value);
    public function isValid();
    public function getRealValue();
} 