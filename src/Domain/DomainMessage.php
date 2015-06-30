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
use DateTime;

/**
 * Represent messaging in domain.
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/14/15
 */
class DomainMessage implements DomainMessageInterface {

	/**
	 * @var int
	 */
	private $_version;

	/**
	 * @var Metadata
	 */
	private $_metadata;

	/**
	 * @var mixed
	 */
	private $_payload;

	/**
	 * @var string
	 */
	private $_id;

	/**
	 * @var DateTime
	 */
	private $_created;

	/**
	 * Initialize new instance
	 *
	 * @param string   $id
	 * @param int      $version
	 * @param Metadata $metadata
	 * @param mixed    $payload
	 * @param DateTime $created
	 */
	public function __construct($id, $version, Metadata $metadata, $payload, DateTime $created) {

		$this->_id          = $id;
		$this->_version     = $version;
		$this->_metadata    = $metadata;
		$this->_payload     = $payload;
		$this->_created     = $created;
	}


	/**
	 * {@inheritDoc}
	 */
	public function getId() {

		return $this->_id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getVersion() {

		return $this->_version;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMetadata() {

		return $this->_metadata;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType() {

		return get_class($this->_payload);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCreated() {

		return $this->_created;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPayload() {

		return $this->_payload;
	}

	/**
	 * Create domain message with different metadata
	 *
	 * @param Metadata $otherMetadata
	 *
	 * @return static
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function appendMetadata(Metadata $otherMetadata) {

		$metadata = $this->_metadata->merge($otherMetadata);

		return new static($this->_id, $this->_version, $metadata, $this->_payload, $this->_created);
	}

	/**
	 * Factory create domain message
	 *
	 * @param string   $id
	 * @param int      $version
	 * @param Metadata $metadata
	 * @param object   $payload
	 *
	 * @return static
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public static function create($id, $version, Metadata $metadata, $payload) {

		return new static($id, $version, $metadata, $payload, new DateTime());
	}
}