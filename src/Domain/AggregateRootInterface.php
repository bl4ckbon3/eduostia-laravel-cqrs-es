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
use Cqrs\Serializer\SerializableInterface;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
interface AggregateRootInterface extends SerializableInterface {

	/**
	 * @return string
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function getAggregateRootId();

	/**
	 * @return DomainEventStreamInterface
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function getUncommittedEvents();
}