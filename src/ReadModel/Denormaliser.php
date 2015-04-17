<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\ReadModel;
use Cqrs\Domain\DomainEventInterface;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
abstract class Denormaliser {

	/**
	 * @param DomainEventInterface $event
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function handle(DomainEventInterface $event) {

		$method = $this->getApplyMethodName($event);

		if ( ! method_exists($this, $method)) {

			return;
		}

		$this->{$method}($event);
	}

	/**
	 * Return method handler name
	 *
	 * @param DomainEventInterface $event
	 *
	 * @return string
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	private function getApplyMethodName(DomainEventInterface $event) {

		$parts = explode('\\', get_class($event));

		return 'apply' . end($parts);
	}
}