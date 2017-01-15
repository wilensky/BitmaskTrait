<?php
declare(strict_types = 1);

namespace Wilensky\Interfaces;

/**
 * Unobtrusive interface for designating class that uses `\Wilensky\Traits\BitmaskTrait`
 * Can be used in conjunction with trait if required to determine whereas
 *  class uses mentioned trait with `instanceof` construction
 * @see class_uses()
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
interface IBitmaskAware
{
}
