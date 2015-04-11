<?php

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