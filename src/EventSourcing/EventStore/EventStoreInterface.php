<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\EventSourcing\EventStore;
use Cqrs\Domain\DomainEventStreamInterface;


/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
interface EventStoreInterface {

	/**
	 * Find events by id in store and make stream
	 *
	 * @param string $table
	 * @param mixed  $id
	 *
	 * @return DomainEventStreamInterface
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function find($table, $id);

	/**
	 * Persist events from DomainEventStream
	 *
	 * @param string $table
	 * @param DomainEventStreamInterface $eventStream
	 *
	 * @return mixed
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function append($table, DomainEventStreamInterface $eventStream);
}