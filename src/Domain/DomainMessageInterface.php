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

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
interface DomainMessageInterface {

	/**
	 * Return identity of domain message
	 *
	 * @return string
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function getId();

	/**
	 * Return current version of domain message.
	 *
	 * @return int
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function getVersion();

	/**
	 * Get domain message meta information
	 *
	 * @return Metadata
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function getMetadata();

	/**
	 * Return payload full class name
	 *
	 * @return string
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function getType();

	/**
	 * Return DomainMessage created time
	 *
	 * @return \DateTime
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function getCreated();

	/**
	 * Return payload object
	 *
	 * @return DomainEventInterface|object
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function getPayload();
}