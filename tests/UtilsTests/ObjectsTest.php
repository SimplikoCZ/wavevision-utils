<?php declare (strict_types = 1);

namespace Wavevision\UtilsTests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Wavevision\Utils\Objects;
use Wavevision\Utils\Tokenizer\Tokenizer;

#[\PHPUnit\Framework\Attributes\CoversClass(\Wavevision\Utils\Objects::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Wavevision\Utils\Strings::class)]
class ObjectsTest extends TestCase
{

	public function testGetClassName(): void
	{
		$this->assertEquals('Tokenizer', Objects::getClassName(new Tokenizer()));
	}

	public function testGetIfNotNull(): void
	{
		$mock = new class {
			public function getYoMama(): string
			{
				return 'mama';
			}
		};
		$this->assertEquals('mama', Objects::getIfNotNull($mock, 'yoMama'));
		$this->assertNull(Objects::getIfNotNull(null, 'yoMama'));
	}

	public function testGetNamespace(): void
	{
		$this->assertEquals('Wavevision\Utils', Objects::getNamespace(new Tokenizer()));
	}

	public function testHasGetter(): void
	{
		$o = new class {
			public function getSomething(): void
			{
			}
		};
		$this->assertTrue(Objects::hasGetter($o, 'something'));
		$this->assertFalse(Objects::hasGetter(new stdClass(), 'something'));
	}

	public function testHasSetter(): void
	{
		$o = new class {
			public function setSomething(): void
			{
			}
		};
		$this->assertTrue(Objects::hasSetter($o, 'something'));
		$this->assertFalse(Objects::hasSetter(new stdClass(), 'something'));
	}

	public function testSet(): void
	{
		$mock = new class {
			public function setYoMama($val): self
			{
				return $this;
			}
		};
		$this->assertSame($mock, Objects::set($mock, 'yoMama', null));
	}

	public function testToArray(): void
	{
		$n2 = new class {
			public function getN2(): string
			{
				return '42';
			}
		};
		$mock = new class($n2) {
			public function __construct(private object $n2) {}
			public function getYoMama(): string { return 'chewbacca'; }
			public function getYo(): string { return 'yo'; }
			public function getN1(): object { return $this->n2; }
			public function getPope(): ?string { return null; }
		};
		$this->assertEquals(
			[
				'yoMama' => 'chewbacca',
				'yoPapa' => 'kenobi',
				'yo' => 'yo',
				'n1.n2' => '42',
				'n12' => '42',
				'pope.of.nope' => null,
			],
			Objects::toArray(
				$mock,
				['yoMama', ['n1', 'n2'], 'n12' => ['n1', 'n2'], ['pope', 'of', 'nope']],
				[
					'yoPapa' => 'kenobi',
					'yo' => function ($yo) {
						return $yo;
					},
				]
			)
		);
	}

	public function testGetNested(): void
	{
		$object = new stdClass();
		$this->assertEquals($object, Objects::getNested($object));
	}

	public function testCopyAttributes(): void
	{
		$source = new class {
			public function getA(): string
			{
				return '1';
			}
		};
		$target = new class {
			public string $a;

			public function setA(string $a): void
			{
				$this->a = $a;
			}
		};
		Objects::copyAttributes($source, $target, ['A']);
		$this->assertEquals('1', $target->a);
	}

}
