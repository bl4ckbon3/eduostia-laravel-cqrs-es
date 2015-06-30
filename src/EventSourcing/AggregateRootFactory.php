<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\EventSourcing;
use Cqrs\Domain\DomainEventStreamInterface;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
class AggregateRootFactory {

	/**
	 * Factory create AggregateRoot by class name
	 *
	 * @param string                     $aggregateRootClass
	 * @param DomainEventStreamInterface $eventStream
	 *
	 * @return AbstractEventSourcedAggregateRoot
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public static function create($aggregateRootClass, DomainEventStreamInterface $eventStream) {

		/** @var AbstractEventSourcedAggregateRoot $aggregateRoot */
		$aggregateRoot = new $aggregateRootClass();
		$aggregateRoot->initialize($eventStream);

		return $aggregateRoot;
	}
}