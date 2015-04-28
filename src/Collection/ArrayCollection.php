<?php
/*
 * This file is part of the Soccer-Api package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Collection;
use Cqrs\Collection\Expression\ClosureExpressionVisitor;
use Closure;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/26/15
 */
class ArrayCollection implements CollectionInterface {

	/**
	 * An array containing the entries of this collection.
	 *
	 * @var array
	 */
	private $_elements;

	/**
	 * Initialize new ArrayCollection
	 *
	 * @param array $elements
	 */
	public function __construct(array $elements = array()) {

		$this->_elements = $elements;
	}

	/**
	 * Helper to combine array, stdClass or object of class.
	 *
	 * @param mixed $destination
	 * @param mixed $source
	 *
	 * @return mixed
	 */
	public static function combine($destination, $source) {

		if ( ! is_string(key($source))) {

			return $destination;
		}

		if ($destination instanceof \ArrayAccess || is_array($destination)) {

			return array_merge($destination, (array) $source);
		}

		foreach($source as $field => &$value) {

			$accessor = 'set' . $field;

			if (method_exists($destination, $accessor)) {

				$destination->$accessor($value);
			}
			if (method_exists($destination, '__call')) {

				$destination->$accessor($value);
			}
			else if (isset($destination->$field)) {

				$destination->$field = $value;
			}
		}

		return $destination;
	}

	/**
	 * Helper to convert nested native PHP array to stdClass.
	 *
	 * @param mixed $elements
	 *
	 * @return object|object[]
	 */
	public static function convertToObject($elements) {

		if (is_array($elements)) {

			return (object) array_map(array("self", __METHOD__), $elements);
		}

		return $elements;
	}

	/**
	 * {@inheritdoc}
	 */
	public function toArray($resortIndex = false) {

		return $resortIndex === true ? array_values($this->_elements) : $this->_elements;
	}

	/**
	 * {@inheritdoc}
	 */
	public function toObject($resortIndex = false) {

		return self::convertToObject($this->toArray($resortIndex));
	}

	/**
	 * {@inheritdoc}
	 */
	public function first() {

		return reset($this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function last() {

		return end($this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function key() {

		return key($this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function next() {

		return next($this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function prev() {

		return prev($this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function current() {

		return current($this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function add($element) {

		$this->_elements[] = $element;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set($key, $value) {

		$this->_elements[$key] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove($key) {

		if (isset($this->_elements[$key]) || array_key_exists($key, $this->_elements)) {

			$removed = $this->_elements[$key];
			unset($this->_elements[$key]);

			return $removed;
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function removeElement($element) {

		$key = array_search($element, $this->_elements, true);

		if ($key !== false) {

			$this->remove($key);

			return true;
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function contains($element) {

		return in_array($element, $this->_elements, true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function containsKey($key) {

		return isset($this->_elements[$key]) || array_key_exists($key, $this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists(Closure $p) {

		foreach($this->_elements as $key => $element) {

			if ($p($key, $element)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isEmpty() {

		return ! $this->_elements;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($key) {

		if (isset($this->_elements[$key])) {

			return $this->_elements[$key];
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getKeys() {

		return array_keys($this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValues() {

		return array_values($this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFieldValues($field = null) {

		$fields     = func_get_args();
		$elements   = array();

		if ($fields || ( isset($this->_elements[0]) && is_array($this->_elements[0]) )) {

			$this->each(function($element) use (&$elements, $fields) {

				if (is_array($element)) {

					if ($fields) {
						foreach($fields as $field) {

							if (array_key_exists($field, $element)) {

								$elements[] = $element[$field];
							}
						}
					}
					else {
						$elements = array_merge($elements, array_values($element));
					}
				}
			});


			return $elements;
		}

		return array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function filter(Closure $p) {

		return new static(array_filter($this->_elements, $p));
	}

	/**
	 * {@inheritdoc}
	 */
	public function map(Closure $p) {

		return new static(array_map($p, $this->_elements));
	}

	/**
	 * {@inheritdoc}
	 */
	public function each(Closure $p) {

		foreach($this->_elements as $key => $element) {

			if ($p($element, $key, $this) === false) {

				return false;
			}
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function indexOf($element) {

		return array_search($element, $this->_elements, true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function clear() {

		$this->_elements = array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {

		return new \ArrayIterator($this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists($offset) {

		return $this->containsKey($offset);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($offset) {

		return $this->get($offset);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetSet($offset, $value) {

		if ( ! isset($offset)) {
			$this->add($value);
		}
		else {
			$this->set($offset, $value);
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetUnset($offset) {

		return $this->remove($offset);
	}

	/**
	 * {@inheritdoc}
	 */
	public function count() {

		return count($this->_elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function slice($offset, $length = null) {

		return new static(array_slice($this->_elements, $offset, $length, true));
	}

	/**
	 * {@inheritdoc}
	 */
	public function splice($offset, $length = null) {

		array_splice($this->_elements, $offset, $length);
	}

	/**
	 * {@inheritdoc}
	 */
	public function merge(CollectionInterface $collection) {

		$this->_elements = array_merge($this->_elements, $collection->toArray());
	}

	/**
	 * {@inheritdoc}
	 */
	public function select($fields) {

		$elements = array();
		$fields   = func_get_args();

		$this->each(function($element) use (&$elements, $fields) {

			$temp = array();
			foreach($fields as $field) {

				$alias = $field;

				if (is_array($field) && ! is_int(key($field))) {

					$alias  = reset($field);
					$field  = key($field);
				}

				$value = ClosureExpressionVisitor::getObjectFieldValue($element, $field);
				if ($value) {

					$temp[$alias] = $value;
				}
			}

			if ($temp) {
				$elements[] = $temp;
			}

		});

		return new static($elements);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return ArrayCollection
	 */
	public function matching(Criteria $criteria) {

		$elements   = $this->_elements;

		if ($expression = $criteria->getExpression()) {

			$visitor    = new ClosureExpressionVisitor();
			$filter     = $visitor->dispatch($expression);
			$elements   = array_filter($elements, $filter);
		}

		if ($orderings = $criteria->getOrderings()) {

			$next = null;
			foreach(array_reverse($orderings) as $field => $ordering) {

				$next = ClosureExpressionVisitor::sortByField(
					$field,
					$ordering == Criteria::ORDER_DESC ? -1 : 1,
					$next
				);
			}

			usort($elements, $next);
		}

		$offset = $criteria->getFirstResult();
		$length = $criteria->getMaxResults();

		if ($offset || $length) {

			$elements = array_splice($elements, (int) $offset, $length);
		}

		return new static($elements);
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete(Criteria $criteria) {

		$find = $this->matching($criteria);
		foreach($find as $index => $element) {

			$this->remove($index);
		}

		return $find->count();
	}

	/**
	 * {@inheritdoc}
	 */
	public function update($element, Criteria $criteria) {

		$find = $this->matching($criteria);
		foreach($find as $index => $row) {

			if (is_array($element)) {
				$element = self::combine($row, $element);
			}

			$this->set($index, $element);
		}

		return $find->count();
	}
}