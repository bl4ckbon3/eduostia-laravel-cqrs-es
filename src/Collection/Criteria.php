<?php
/*
 * This file is part of the laravel-cqrs package.
 *
 * (c) Eduostia Corporation <http://eagle.eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Collection;
use Cqrs\Collection\Expression\CompositeExpression;
use Cqrs\Collection\Expression\ExpressionBuilder;
use Cqrs\Collection\Expression\ExpressionInterface;

/**
 * Criteria Class
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/26/15
 */
class Criteria {

    /**
     * Ordering constant.
     */
    const ORDER_ASC   = "ASC";
    const ORDER_DESC  = "DESC";

    /**
     * Return static ExpressionBuilder
     *
     * @var ExpressionBuilder|null
     */
    private static $_expressionBuilder;

    /**
     * Return single or collection of ExpressionInterface.
     *
     * @var ExpressionInterface|null
     */
    private $_expression;

    /**
     * @var array|null
     */
    private $_orderings;

    /**
     * @var integer
     */
    private $_firstResult;

    /**
     * @var integer|null
     */
    private $_maxResults;

    /**
     * Private constructor and only can be initialize
     * from Criteria::create().
     */
    private function __construct() {}

    /**
     * Initialize new instance.
     *
     * @return Criteria
     */
    public static function create() {

        return new static();
    }

    /**
     * Return the expression builder and make it static.
     *
     * @return ExpressionBuilder
     */
    public static function expr() {

        if ( ! self::$_expressionBuilder) {

            self::$_expressionBuilder = new ExpressionBuilder();
        }

        return self::$_expressionBuilder;
    }

    /**
     * Append or set the where expression to evaluate when this Criteria is searched for
     * using an AND with previous expression.
     *
     * @param ExpressionInterface $expression
     *
     * @return Criteria
     */
    public function where(ExpressionInterface $expression) {

        if ($this->_expression === null) {

            $this->_expression = $expression;
        }
        else {

            $this->_expression = new CompositeExpression(
                CompositeExpression::LOGICAL_AND,
                array(
                     $this->_expression,
                     $expression
                )
            );
        }

        return $this;
    }

    /**
     * Gets the expression attached to this Criteria.
     *
     * @return ExpressionInterface|null
     */
    public function getExpression() {

        return $this->_expression;
    }

    /**
     * Set the ordering of the result of this Criteria.
     *
     * Keys are field and values are the order, being either ASC or DESC.
     *
     * @param string|integer    $field      Field to be ordered.
     * @param string            $orderType  Default order is Criteria::ORDER_ASC
     *
     * @return Criteria
     */
    public function orderBy($field, $orderType = Criteria::ORDER_ASC) {

        $this->_orderings[$field] = $orderType;

        return $this;
    }

    /**
     * Get the current orderings of this Criteria.
     *
     * @return array|null
     */
    public function getOrderings() {

        return $this->_orderings;
    }

    /**
     * Specifies a limit over the results of collection.
     *
     * @param integer       $length
     * @param integer|null  $offset
     *
     * @return Criteria
     */
    public function limit($length, $offset = null) {

        $this->_firstResult = $offset;
        $this->_maxResults  = $length;

        return $this;
    }

    /**
     * Get the current first result option of this Criteria.
     *
     * @return integer|null
     */
    public function getFirstResult() {

        return $this->_firstResult;
    }

    /**
     * Get max results.
     *
     * @return integer|null
     */
    public function getMaxResults() {

        return $this->_maxResults;
    }
} 