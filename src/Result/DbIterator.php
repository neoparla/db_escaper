<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 12/1/14
 * Time: 12:17 PM
 */

namespace NeoParla\DbEscaper\Result;


use ArrayAccess;
use Countable;
use Iterator;

interface DbIterator extends Iterator, Countable, ArrayAccess {

    /**
     * @return array
     */
    public function toArray();
} 