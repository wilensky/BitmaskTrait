<?php
declare(strict_types = 1);

namespace Wilensky\Tests\Interfaces;

use Wilensky\Interfaces\IBitmaskAware;
use Wilensky\Traits\BitmaskTrait;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
final class IBitmaskAwareTest extends \PHPUnit_Framework_TestCase
{
    public function testIBitmaskAware()
    {
        $instance = new class implements IBitmaskAware {
            use BitmaskTrait;
        };
        
        $msg = 'Failed asserting that class is of '.IBitmaskAware::class.' instance';
        
        $this->isInstanceOf($instance, IBitmaskAware::class, $msg);
        $this->assertTrue($instance instanceof IBitmaskAware, $msg);
    }
}
