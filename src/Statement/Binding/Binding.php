<?php

namespace NeoParla\DbEscaper\Statement\Binding;


use NeoParla\DbEscaper\Link;

interface Binding {

    const STRING  = 'String';
    const INTEGER = 'Integer';
    const FIELD   = 'Field';
    const TUPLE   = 'Tuple';
    const DOUBLE  = 'Double';

    public function __construct(Link $link, $value);
    public function isValid();
    public function getRealValue();
} 