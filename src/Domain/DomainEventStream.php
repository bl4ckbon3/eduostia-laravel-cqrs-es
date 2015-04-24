<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Domain;

/**
 * Simple domain event stream.
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
class DomainEventStream implements DomainEventStreamInterface {

	private $_events;

	/**
	 * Instantiate new instance and bind events.
	 *
	 * @param array $events
	 */
	public function __construct(array $events) {

		$this->_events = $events;
	}

	/**
	 * @return \ArrayIterator
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function getIterator() {

		return new \ArrayIterator($this->_events);
	}

	/**
	 * {@inheritDoc}
	 */
	public function count() {

		return count($this->_events);
	}
}