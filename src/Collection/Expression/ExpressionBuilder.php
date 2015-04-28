<?php
/*
 * This file is part of the laralve-cqrs package.
 *
 * (c) Eduostia Corporation <http://eagle.eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Collection\Expression;
use Cqrs\Collection\ArrayCollection;

/**
 * ExpressionBuilder Class
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/26/15
 */
class ExpressionBuilder {

    /**
     * Create a conjunction of the given boolean expressions.
     *
     * @param ExpressionInterface $expr
     *
     * @return CompositeExpression
     */
    public function logicalAnd(ExpressionInterface $expr) {

        return new CompositeExpression(CompositeExpression::LOGICAL_AND, func_get_args());
    }

    /**
     * Create a disjunction of the given boolean expressions.
     *
     * @param ExpressionInterface $expr
     *
     * @return CompositeExpression
     */
    public function logicalOr(ExpressionInterface $expr) {

        return new CompositeExpression(CompositeExpression::LOGICAL_OR, func_get_args());
    }

    /**
     * Create an equality comparison expression with the given arguments.
     *
     * @param string|integer    $field
     * @param mixed             $value
     *
     * @return Comparison
     */
    public function equal($field, $value) {

        return new Comparison($field, Comparison::EQUAL, $value);
    }

    /**
     * Create a non equality comparison expression with the given arguments.
     *
     * @param string|integer    $field
     * @param mixed             $value
     *
     * @return Comparison
     */
    public function notEqual($field, $value) {

        return new Comparison($field, Comparison::NOT_EQUAL, $value);
    }

    /**
     * Create a lower-than comparison expression with the given arguments.
     *
     * @param string|integer $field
     * @param mixed             $value
     *
     * @return Comparison
     */
    public function lowerThan($field, $value) {

        return new Comparison($field, Comparison::LOWER_THAN, $value);
    }

    /**
     * Create a lower-than-equal comparison expression with the given arguments.
     *
     * @param string|integer $field
     * @param mixed             $value
     *
     * @return Comparison
     */
    public function lowerThanEqual($field, $value) {

        return new Comparison($field, Comparison::LOWER_THAN_EQUAL, $value);
    }

    /**
     * Create a greater-than comparison expression with the given arguments.
     *
     * @param string|integer $field
     * @param mixed             $value
     *
     * @return Comparison
     */
    public function greaterThan($field, $value) {

        return new Comparison($field, Comparison::GREATER_THAN, $value);
    }

    /**
     * Create a greater-than-equal comparison expression with the given arguments.
     *
     * @param string|integer $field
     * @param mixed             $value
     *
     * @return Comparison
     */
    public function greaterThanEqual($field, $value) {

        return new Comparison($field, Comparison::GREATER_THAN_EQUAL, $value);
    }

    /**
     * Create an IS NULL expression with the given arguments.
     *
     * @param string|integer $field
     *
     * @return Comparison
     */
    public function isNull($field) {

        return new Comparison($field, Comparison::IS_NULL, null);
    }

    /**
     * Create an IS NOT NULL expression with the given arguments.
     *
     * @param string|integer $field
     *
     * @return Comparison
     */
    public function isNotNull($field) {

        return new Comparison($field, Comparison::IS_NOT_NULL, null);
    }

    /**
     * Create a IN comparison expression with the given arguments.
     *
     * @param string|integer    $field
     * @param array             $values
     *
     * @return Comparison
     */
    public function in($field, array $values) {

        return new Comparison($field, Comparison::IN, $values);
    }

    /**
     * Create a NOT IN comparison expression with the given arguments.
     *
     * @param string|integer    $field
     * @param array             $values
     *
     * @return Comparison
     */
    public function notIn($field, array $values) {

        return new Comparison($field, Comparison::NOT_IN, $values);
    }

    /**
     * Create a EXISTS comparison expression with the given arguments.
     *
     * @param mixed $values
     *
     * @return Comparison
     */
    public function exists($values) {

        if ($values instanceof ArrayCollection) {

            $values = $values->toArray();
        }

        return new Comparison(null, Comparison::EXISTS, $values);
    }

    /**
     * Create a NOT EXISTS comparison expression with the given arguments.
     *
     * @param mixed $values
     *
     * @return Comparison
     */
    public function notExists($values) {

        if ($values instanceof ArrayCollection) {

            $values = $values->toArray();
        }

        return new Comparison(null, Comparison::NOT_EXISTS, $values);
    }

    /**
     * Create a LIKE comparison expression with the given arguments.
     *
     * @param string|integer    $field
     * @param mixed             $value
     *
     * @return Comparison
     */
    public function like($field, $value) {

        return new Comparison($field, Comparison::LIKE, $value);
    }

    /**
     * Create a NOT LIKE comparison expression with the given arguments.
     *
     * @param string|integer    $field
     * @param mixed             $value
     *
     * @return Comparison
     */
    public function notLike($field, $value) {

        return new Comparison($field, Comparison::NOT_LIKE, $value);
    }
} 