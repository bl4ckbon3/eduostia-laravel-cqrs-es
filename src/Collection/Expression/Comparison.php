<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eagle.eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Collection\Expression;

/**
 * Comparison of a field with a value by the given operator.
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/26/15
 */
class Comparison implements ExpressionInterface {

    /**
     * Operator Constant
     */
    const EQUAL                 = "=";
    const NOT_EQUAL             = "<>";
    const LOWER_THAN            = "<";
    const LOWER_THAN_EQUAL      = "<=";
    const GREATER_THAN          = ">";
    const GREATER_THAN_EQUAL    = ">=";
    const IS_NULL               = "IS NULL";
    const IS_NOT_NULL           = "IS NOT NULL";
    const IN                    = "IN";
    const NOT_IN                = "NOT IN";
    const EXISTS                = "EXISTS";
    const NOT_EXISTS            = "NOT EXISTS";
    const LIKE                  = "LIKE";
    const NOT_LIKE              = "NOT LIKE";

    /**
     * Field to be compare.
     *
     * @var string
     */
    private $field;

    /**
     * One of logical operator.
     *
     * @var string
     */
    private $operator;

    /**
     * Value to be compare.
     *
     * @var mixed
     */
    private $value;

    /**
     * @param string|integer $field
     * @param string $operator
     * @param mixed $value
     */
    public function __construct($field, $operator, $value) {

        $this->field    = $field;
        $this->operator = $operator;
        $this->value    = $value;
    }

    /**
     * @return integer|string
     */
    public function getField() {

        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator() {

        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue() {

        return $this->value;
    }
} 