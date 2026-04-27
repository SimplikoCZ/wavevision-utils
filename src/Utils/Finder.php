<?php declare (strict_types = 1);

namespace Wavevision\Utils;

use Nette\Utils\Finder as NetteFinder;
use SplFileInfo;

/**
 * @implements \IteratorAggregate<string, SplFileInfo>
 */
class Finder extends NetteFinder
{

	public const CASE_INSENSITIVE = 'CASE_INSENSITIVE';

	public const CASE_SENSITIVE = 'CASE_SENSITIVE';

	public const ORDER_ASC = 'ASC';

	public const ORDER_DESC = 'DESC';

	/**
	 * @return static
	 */
	public function setSort(callable $sort): static
	{
		return $this->sortBy($sort);
	}

	/**
	 * @return static
	 */
	public function sortByMTime(string $order = self::ORDER_DESC): static
	{
		return $this->sortBy(
			function (SplFileInfo $f1, SplFileInfo $f2) use ($order): int {
				if ($order === self::ORDER_DESC) {
					return $f2->getMTime() - $f1->getMTime();
				}
				return $f1->getMTime() - $f2->getMTime();
			}
		);
	}

	/**
	 * @return static
	 */
	public function sortByName(string $order = self::ORDER_ASC, string $case = self::CASE_INSENSITIVE): static
	{
		$fn = $case === self::CASE_INSENSITIVE ? 'strcasecmp' : 'strcmp';
		return $this->sortBy(
			function (SplFileInfo $f1, SplFileInfo $f2) use ($fn, $order): int {
				if ($order === self::ORDER_ASC) {
					return $fn(
						Strings::removeAccentedChars($f1->getFilename()),
						Strings::removeAccentedChars($f2->getFilename())
					);
				}
				return $fn(
					Strings::removeAccentedChars($f2->getFilename()),
					Strings::removeAccentedChars($f1->getFilename())
				);
			}
		);
	}

}
