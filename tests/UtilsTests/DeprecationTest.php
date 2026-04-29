<?php declare(strict_types=1);

namespace Wavevision\UtilsTests;

use PHPUnit\Framework\TestCase;
use Wavevision\Utils\Arrays;

#[\PHPUnit\Framework\Attributes\CoversClass(\Wavevision\Utils\Arrays::class)]
class DeprecationTest extends TestCase
{

	public function testFirstItemEmpty(): void
	{
		$this->assertNull(Arrays::firstItem([]));
	}

	public function testLastItemEmpty(): void
	{
		$this->assertNull(Arrays::lastItem([]));
	}
}
