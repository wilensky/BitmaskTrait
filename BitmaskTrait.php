<?php
declare(strict_types = 1);

namespace Wilensky\Traits;

use Wilensky\Traits\Exceptions\BitAddressingException;

/**
 * Provides management of any bitmask
 * @author Gregg Wilensky <https://github.com/wilensky/>
 * @link http://php.net/manual/en/language.operators.bitwise.php
 */
trait BitmaskTrait
{
    /**
     * Returns bitmask for given bits positions
     * @param ...int $bits List of bits positions
     * @return int Compiled mask
     */
    private static function getPositionsBitmask(int ...$bits): int
    {
        $max = count($bits) === 0 ? 0 : max($bits);
        $bin = '';
        
        for ($i = 0; $i<=$max; $i++) {
            $bin .= (string)((int)in_array($i, $bits));
        }
        
        return bindec(strrev($bin));
    }
    
    /**
     * Returns bitmask for given position
     * @param int $position
     * @return int
     */
    private static function getPositionBitmask(int $position): int
    {
        return (int)pow(2, $position);
    }

    /**
     * Performs bitwise OR `|` therefore sets `$bits` as mask
     * @param int $mask
     * @param int $bits Decimal bitmask to set
     * @return int
     */
    protected function setBitmask(int $mask, int $bits): int
    {
        return (int)($mask | $bits);
    }

    /**
     * Performs bitwise OR `|` and set bit(s) on particular position
     * @param int $mask Mask to apply changes to
     * @param int... $positions Variable number of bit positions to set
     * @return int
     */
    protected function setBit(int $mask, int ...$positions): int
    {
        return $this->setBitmask(
            $mask,
            count($positions) === 1
                ? $this->getPositionBitmask(reset($positions))
                : $this->getPositionsBitmask(...$positions)
        );
    }

    /**
     * Excludes provided `$bit` as mask from `$mask`
     * @link http://habrahabr.ru/post/134557/ Nice to read
     * @param int $mask
     * @param int $bits Bitmask to exclude
     * @return int Result mask
     */
    protected function unsetBitmask(int $mask, int $bits): int
    {
        return (int)($mask & ~$bits);
    }

    /**
     * Excludes bit(s) on particular position
     * @param int $mask Mask to unset bits from
     * @param int... $positions Variable number of bit positions to unset
     * @return int Result mask
     */
    protected function unsetBit(int $mask, int ...$positions): int
    {
        return $this->unsetBitmask(
            $mask,
            count($positions) === 1
                ? $this->getPositionBitmask(reset($positions))
                : $this->getPositionsBitmask(...$positions)
        );
    }

    /**
     * Alias method
     * @see isBitSet()
     * @param int $mask
     * @param int $position
     * @return bool
     */
    protected function hasBit(int $mask, int $position): bool
    {
        return $this->isBitSet($mask, $position);
    }

    /**
     * Checks whether bit on position is set
     * @param int $mask
     * @param int $position
     * @return bool
     */
    protected function isBitSet(int $mask, int $position): bool
    {
        return $this->isMaskSet($mask, $this->getPositionBitmask($position));
    }

    /**
     * Checks whether bitmask is set
     * @param int $mask Bitmask to check on
     * @param int $bitmask Bitmask that is needed to be found
     * @return bool
     */
    protected function isMaskSet(int $mask, int $bitmask): bool
    {
        return ($mask & $bitmask) === $bitmask;
    }

    /**
     * Checks whether bit position is in given range
     * @param int $bit Bit to check
     * @param int $start Range start
     * @param int $end Range end
     * @return $this
     * @throws BitAddressingException
     */
    protected function checkBitInRange(int $bit, int $start, int $end)
    {
        if (!($bit >= $start && $bit <= $end)) {
            throw new BitAddressingException();
        }

        return $this;
    }

    /**
     * Manages set/unset of arbitrary bit in the mask
     * @param int $mask Mask to work on
     * @param int $p Bit position to alter
     * @param bool $flag true = bit set | false = bit unset
     * @return int Modified mask
     */
    protected function manageMaskBit(int $mask, int $p, bool $flag = true): int
    {
        return $this->{$flag === true ? 'setBit' : 'unsetBit'}($mask, $p);
    }
}
