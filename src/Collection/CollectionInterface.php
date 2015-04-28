<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Collection;

use Closure, Countable, IteratorAggregate, ArrayAccess;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/26/15
 */
interface CollectionInterface extends Countable, IteratorAggregate, ArrayAccess {

	/**
	 * Get a native PHP array representation of the collection.
	 *
	 * @param boolean $resortIndex To sort index array.
	 *
	 * @return array
	 */
	public function toArray($resortIndex = false);

	/**
	 * Set the internal iterator to the first element in the collection and returns this element.
	 *
	 * @return mixed
	 */
	public function first();

	/**
	 * Set the internal iterator to the end element in the collection and returns this element.
	 *
	 * @return mixed
	 */
	public function last();

	/**
	 * Get the key/index of the element at the current iterator position.
	 *
	 * @return integer|string
	 */
	public function key();

	/**
	 * Move the internal iterator position to the previous element and returns this element.
	 *
	 * @return mixed
	 */
	public function prev();

	/**
	 * Move the internal iterator position to the next element and returns this element.
	 *
	 * @return mixed
	 */
	public function next();

	/**
	 * Get the element of the collection at the current iterator position.
	 *
	 * @return mixed
	 */
	public function current();

	/**
	 * Check whether the collection is empty (contains no elements).
	 *
	 * @return boolean TRUE if the collection is empty, FALSE otherwise.
	 */
	public function isEmpty();

	/**
	 * Add an element at the end of the collection.
	 *
	 * @param mixed $element The element to add.
	 *
	 * @return void
	 */
	public function add($element);

	/**
	 * Set an element in the collection at the specified key/index.
	 *
	 * @param string|integer $key
	 * @param mixed          $value
	 *
	 * @return void
	 */
	public function set($key, $value);

	/**
	 * Removes the element at the specified index from the collection.
	 *
	 * @param string|integer $key The kex/index of the element to remove.
	 *
	 * @return mixed The removed element or NULL, if the collection did not contain the element.
	 */
	public function remove($key);

	/**
	 * Remove the specified element from the collection, if it is found.
	 *
	 * @param mixed $element
	 *
	 * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
	 */
	public function removeElement($element);

	/**
	 * Check whether the collection contains an element with the specified key/index.
	 *
	 * @param integer|string $key
	 *
	 * @return boolean TRUE if the collection contains an element with the specified key/index,
	 *                 FALSE otherwise.
	 */
	public function containsKey($key);

	/**
	 * Check whether an element is contained in the collection.
	 * This is an O(n) operation, where n is the size of the collection.
	 *
	 * @param mixed $element
	 *
	 * @return boolean TRUE if the collection contains the element, FALSE otherwise.
	 */
	public function contains($element);

	/**
	 * Tests for the existence of an element that satisfies the given predicate.
	 *
	 * @param Closure $p
	 *
	 * @return boolean TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
	 */
	public function exists(Closure $p);

	/**
	 * Get the element at the specified key/index.
	 *
	 * @param string|integer $key
	 *
	 * @return mixed
	 */
	public function get($key);

	/**
	 * Get all keys/indices of the collection.
	 *
	 * @return array
	 */
	public function getKeys();

	/**
	 * Get all values of the collection.
	 *
	 * @return array
	 */
	public function getValues();

	/**
	 * Get values of the collection by keys.
	 *
	 * @param string|integer|null $key
	 *
	 * @return array
	 */
	public function getFieldValues($key = null);

	/**
	 * Applies the given predicate p to all elements of this collection,
	 * returning true, if the predicate yields true for all elements.
	 *
	 * @param Closure $p
	 *
	 * @return CollectionInterface A collection with the results of the filter operation.
	 */
	public function filter(Closure $p);

	/**
	 * Applies the given function to each element in the collection and returns
	 * a new collection with the elements returned by the function.
	 *
	 * @param Closure $p
	 *
	 * @return CollectionInterface
	 */
	public function map(Closure $p);

	/**
	 * Applies the given predicate p to all elements of this collection,
	 * returning true, if the predicate yields true for all elements.
	 *
	 * @param Closure $p
	 *
	 * @return boolean TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
	 */
	public function each(Closure $p);

	/**
	 * Gets the index/key of a given element. The comparison of two elements is strict,
	 * that means not only the value but also the type must match.
	 * For objects this means reference equality.
	 *
	 * @param mixed $element
	 *
	 * @return string|integer|boolean The key/index of the element or FALSE if the element was not found.
	 */
	public function indexOf($element);

	/**
	 * Clear the collection, remove all elements.
	 *
	 * @return void
	 */
	public function clear();

	/**
	 * Extracts a slice of $length elements starting at position $offset from the CollectionInterface.
	 *
	 * If $length is null it returns all elements from $offset to the end of the CollectionInterface.
	 * Keys have to be preserved by this method. Calling this method will only return the
	 * selected slice and NOT change the elements contained in the collection slice is called on.
	 *
	 * @param integer       $offset
	 * @param integer|null  $length
	 *
	 * @return CollectionInterface The sliced CollectionInterface.
	 */
	public function slice($offset, $length);

	/**
	 * Cut current of $length elements starting at position $offset from the CollectionInterface.
	 *
	 * If $length is null it cut all elements from $offset to the end of the CollectionInterface.
	 * Calling this method will change the elements contained in the collection.
	 *
	 * @param integer       $offset
	 * @param integer|null  $length
	 *
	 * @return void
	 */
	public function splice($offset, $length);

	/**
	 * Return values of specific index / field.
	 *
	 * @param string|integer $field
	 *
	 * @return CollectionInterface
	 */
	public function select($field);

	/**
	 * Merge data with a collection.
	 *
	 * @param CollectionInterface $collection
	 *
	 * @return void
	 */
	public function merge(CollectionInterface $collection);
}