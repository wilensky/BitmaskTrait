<?php
declare(strict_types = 1);

namespace Wilensky\Tests\Traits;

use Wilensky\Traits\{
    BitmaskTrait, Exceptions\BitAddressingException as BAE
};

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
final class BitmaskTraitTest extends \PHPUnit_Framework_TestCase
{
    use BitmaskTrait;

    public function bitmaskDP(): array
    {
        return [
            [4],
            [28],
            [95],
            [2013],
            [3648]
        ];
    }

    private function unfoldMask(int $mask)
    {
        return strrev(decbin($mask));
    }

    /**
     * @dataProvider bitmaskDP
     * @param int $mask
     */
    public function testHasBit(int $mask)
    {
        $m = $this->unfoldMask($mask);
        $l = count($m); // length

        for ($i = 0; $i < $l; $i++) {
            $isSet = (bool)$m[$i];

            $this->assertEquals(
                $isSet, $this->hasBit($mask, $i),
                'Failed asserting that bit #' . $i . ' is' . ($isSet ? null : ' not') . ' set in mask `' . $m . '`'
            );
        }
    }

    public function testSetBit()
    {
        for ($i = 0; $i < 29; $i++) {
            $m = $this->setBit(0, $i, $i+1, $i+2);

            $this->assertTrue(
                $this->hasBit($m, $i),
                'Failed asserting that bit #' . $i . ' was set in mask `' . $m . '`'
            );
            $this->assertTrue(
                $this->hasBit($m, $i+1),
                'Failed asserting that bit #' . ($i+1) . ' was set in mask `' . $m . '`'
            );
            $this->assertTrue(
                $this->hasBit($m, $i+2),
                'Failed asserting that bit #' . ($i+2) . ' was set in mask `' . $m . '`'
            );
        }
    }

    public function testUnsetBit()
    {
        $mask = bindec(strrev(str_repeat('1', 32)));

        for ($i = 0; $i < 29; $i++) {
            $m = $this->unsetBit($mask, $i, $i+1, $i+2);

            $this->assertFalse(
                $this->hasBit($m, $i),
                'Failed asserting that bit #' . $i . ' was unset in mask `' . $m . '`'
            );
            $this->assertFalse(
                $this->hasBit($m, $i+1),
                'Failed asserting that bit #' . ($i+1) . ' was unset in mask `' . $m . '`'
            );
            $this->assertFalse(
                $this->hasBit($m, $i+2),
                'Failed asserting that bit #' . ($i+2) . ' was unset in mask `' . $m . '`'
            );
        }
    }
    
    public function testGetPositionBitmask()
    {
        $decs = [
            1, 2, 4, 8, 16, 32, 64, 128, 256, 512, 1024, 2048, 4096, 8192, 16384, 32768
        ];
        
        $bits = count($decs);
        
        for ($bit=0; $bit < $bits; $bit++) {
            $this->assertEquals(
                $decs[$bit],
                self::getPositionBitmask($bit),
                'Failed asserting that bit #'.$bit.' equals '.$decs[$bit]
            );
        }
    }
    
    public function positionsBitmaskDP(): array
    {
        return [
            [0],
            [1, 0],
            [2, 1],
            [3, 0, 1],
            [4, 2],
            [5, 0, 2],
            [15, 0, 1, 2, 3],
            [234, 1, 3, 5, 6, 7],
            [761, 0, 3, 4, 5, 6, 7, 9],
            [5049, 0, 3, 4, 5, 7, 8, 9, 12],
            [2020, 2, 5, 6, 7, 8, 9, 10]
        ];
    }
    
    /**
     * @dataProvider positionsBitmaskDP
     * @param int $mask
     * @param int $bits,...
     */
    public function testGetPositionsBitmask(int $mask, int ...$bits)
    {
        $this->assertEquals(
            $mask,
            forward_static_call_array([$this, 'getPositionsBitmask'], $bits),
            'Failed asserting that compiled mask `'.$mask.'` equals to expected'
        );
    }
    
    public function bitRangeDP(): array
    {
        return [
            [0, 0, 5],
            [3, 0, 5],
            [5, 0, 5],
            [6, 0, 5, false],
            [20, 20, 32],
            [32, 20, 32],
            [25, 20, 32],
            [33, 20, 32, false],
            [9, 20, 32, false],
        ];
    }
    
    /**
     * @dataProvider bitRangeDP
     */
    public function testIsBitInRange(int $bit, int $start, int $end, bool $isInRange = true)
    {
        $isInRange ? null : $this->setExpectedException(BAE::class) ;
        $this->isBitInRange($bit, $start, $end);
    }
}
