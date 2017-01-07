<?php
declare(strict_types = 1);

namespace Wilensky\Traits\Exceptions;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
final class BitAddressingException extends \Exception
{
    /**
     * @param string $message
     * @param int $code
     */
    public function __construct($message = '', int $code = 0)
    {
        parent::__construct($message ?: 'Attempt to address bit within wrong range', $code);
    }
}
