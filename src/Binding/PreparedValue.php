<?php
/**
 * Created by PhpStorm.
 * User: Pau
 * Date: 21/11/13
 * Time: 23:15
 */

namespace NeoParla\DbEscaper;

interface PreparedValue
{

	public function __construct( &$value );

	public function isValid();

	public function getValue( DbLink $link );
}

class PreparedValue_Exception extends DbStatement_Exception {}