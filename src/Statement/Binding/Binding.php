<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 12/1/14
 * Time: 1:45 PM
 */

namespace NeoParla\DbEscaper\Statement\Binding;


use NeoParla\DbEscaper\Link;

interface Binding {

    const PARAM_STRING  = 'String';
    const PARAM_INTEGER = 'Integer';
    const PARAM_FIELD   = 'Field';
    const PARAM_TUPLE   = 'Tuple';
    const PARAM_DOUBLE  = 'Double';

    public function __construct(Link $link, $value);
    public function isValid();
    public function getRealValue();
} 