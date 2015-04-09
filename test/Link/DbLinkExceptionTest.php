<?php
/**
 * Created by PhpStorm.
 * User: pau.perez
 * Date: 4/9/15
 * Time: 8:03 PM
 */

namespace NeoParla\DbEscaper\Link;


use PHPUnit_Framework_TestCase;

class DbLinkExceptionTest  extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function correctInheritance()
    {
        $this->assertInstanceOf(
            'NeoParla\DbEscaper\DbEscaperException',
            new DbLinkException
        );
    }

} 