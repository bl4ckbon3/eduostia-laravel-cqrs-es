<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Domain;
use Rhumsaa\Uuid\Uuid;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
class UuidIdentifier implements Identifier {

	/**
	 * @var Uuid
	 */
	protected $value;

	/**
	 * @param Uuid $uuid
	 */
	public function __construct(Uuid $uuid) {

		$this->value = $uuid;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return static
	 */
	public static function generate() {

		return new static(Uuid::uuid4());
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return static
	 */
	public static function fromString($id) {

		return new static(Uuid::fromString($id));
	}

	/**
	 * {@inheritDoc}
	 */
	public function equals(Identifier $identifier) {

		return $this === $identifier;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toString() {

		return (string) $this->value;
	}

	/**
	 * String type cast magic method
	 *
	 * @return string|void
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function __toString() {

		return $this->toString();
	}
}