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
 * Meta information of DomainMessage
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
class Metadata implements SerializableInterface {

	/**
	 * Meta data information
	 *
	 * @var array
	 */
	private $_payload = array();

	/**
	 * @param array $values
	 */
	public function __construct(array $values = array()) {

		$this->_payload = $values;
	}

	/**
	 * Merge current metadata to other metadata
	 *
	 * @param Metadata $metadata
	 *
	 * @return Metadata
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function merge(Metadata $metadata) {

		return new Metadata(array_merge($this->_payload, $metadata->_payload));
	}

	/**
	 * {@inheritDoc}
	 */
	public static function deserialize(ParameterBag $data) {

		return new Metadata($data->all());
	}

	/**
	 * {@inheritDoc}
	 */
	public function serialize() {

		return $this->_payload;
	}
}