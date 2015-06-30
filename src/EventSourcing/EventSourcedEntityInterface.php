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
use Cqrs\Domain\DomainEventInterface;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
interface EventSourcedEntityInterface {

	/**
	 * Recursively handle domain event
	 *
	 * @param DomainEventInterface $event
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function handleRecursively(DomainEventInterface $event);

	/**
	 * Register an AggregateRoot to EventSourcedEntity to make it Bounded
	 *
	 * @param AbstractEventSourcedAggregateRoot $aggregateRoot
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function registerAggregateRoot(AbstractEventSourcedAggregateRoot $aggregateRoot);
}