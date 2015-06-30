<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eagle.eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Collection\Expression;
use ArrayAccess;
use Closure;

/**
 * Walks an expression graph and turns it into a PHP closure.
 *
 * This closure can be used with {@Collection#filter()}.
 *
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/26/15
 */
class ClosureExpressionVisitor extends ExpressionVisitor {

    /**
     * Accesses the field of a given object. This field has to be public
     * directly or indirectly (through an accessor get*, is*, or a magic
     * method, __get, __call).
     *
     * @param object|array|ArrayAccess  $object
     * @param string                    $field
     *
     * @return mixed
     */
    public static function getObjectFieldValue($object, $field) {

        $accessors  = array('get', 'is');
        $value      = null;
        $skip       = false;

        foreach($accessors as $accessor) {

            $accessor .= $field;

            if ( ! method_exists($object, $accessor)) {

                continue;
            }

            $value  = $object->$accessor();
            $skip   = true;
            break;
        }

        if ($skip === false) {

            $accessor = $accessors[0] . $field;

            if (method_exists($object, '__call')) {

                $value = $object->$accessor();
            }
            else if ($object instanceof \ArrayAccess || is_array($object)) {

                $value = $object[$field];
            }
            else {

                $value = $object->$field;
            }
        }

	    if (is_object($value) && method_exists($value, '__toString')) {

		    return (string) $value;
	    }

        return $value;
    }

    /**
     * Helper for sorting arrays of objects based on multiple fields + orientations.
     *
     * @param string|integer    $name
     * @param integer           $orientation
     * @param Closure           $next
     *
     * @return Closure
     */
    public static function sortByField($name, $orientation = 1, Closure $next = null) {

        if ( ! $next ) {
            $next = function() {
                return 0;
            };
        }

        return function ($a, $b) use ($name, $next, $orientation) {
            $aValue = ClosureExpressionVisitor::getObjectFieldValue($a, $name);
            $bValue = ClosureExpressionVisitor::getObjectFieldValue($b, $name);

            if ($aValue === $bValue) {
                return $next($a, $b);
            }

            return (($aValue > $bValue) ? 1 : -1) * $orientation;
        };
    }

    /**
     * Find keyword in string.
     *
     * @param string $string
     * @param string $keyword
     *
     * @return boolean TRUE, if found and ELSE otherwise.
     */
    public static function findString($string, $keyword) {

        if($keyword=="") return false;

        $vi         = explode("%",$keyword);
        $tieneini   = null;
        $offset     = 0;

        for($n = 0; $n < count($vi); $n++) {

            if($vi[$n] == "") {
                if($vi[0] == "") {
                    $tieneini = 1;
                }
            } else {
                $newoff = strpos($string, $vi[$n], $offset);

                if($newoff !== false) {

                    if( ! $tieneini) {

                        if($offset != $newoff) {
                            return false;
                        }
                    }

                    if($n == count($vi) - 1) {
                        if($vi[$n] != substr($string, strlen($string) - strlen($vi[$n]), strlen($vi[$n]))) {
                            return false;
                        }

                    } else {
                        $offset = $newoff + strlen($vi[$n]);
                    }
                } else {

                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Convert a comparison expression into the target query language output.
     *
     * @param Comparison $comparison
     *
     * @return callable
     *
     * @throws ExpressionException
     */
    public function walkComparison(Comparison $comparison) {

        $field      = $comparison->getField();
        $value      = $comparison->getValue();

        switch($comparison->getOperator()) {

            case Comparison::EQUAL:
            case Comparison::IS_NULL:
                return function($object) use ($field, $value) {

                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) === $value;
                };

            case Comparison::NOT_EQUAL:
            case Comparison::IS_NOT_NULL:
                return function($object) use ($field, $value) {

                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) !== $value;
                };

            case Comparison::LOWER_THAN:
                return function($object) use ($field, $value) {

                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) < $value;
                };

            case Comparison::LOWER_THAN_EQUAL:
                return function($object) use ($field, $value) {

                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) <= $value;
                };

            case Comparison::GREATER_THAN:
                return function($object) use ($field, $value) {

                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) > $value;
                };

            case Comparison::GREATER_THAN_EQUAL:
                return function($object) use ($field, $value) {

                    return ClosureExpressionVisitor::getObjectFieldValue($object, $field) >= $value;
                };

            case Comparison::IN:
                return functioN($object) use ($field, $value) {

                    return in_array(ClosureExpressionVisitor::getObjectFieldValue($object, $field), $value);
                };

            case Comparison::NOT_IN:
                return function($object) use ($field, $value) {

                    return ! in_array(ClosureExpressionVisitor::getObjectFieldValue($object, $field), $value);
                };

            case Comparison::EXISTS:
                return function($object) use ($value) {

                    foreach($value as $row) {

                        if ($row !== $object || serialize($row) === serialize($object)) {

                            return true;
                        }
                    }

                    return false;
                };

            case Comparison::NOT_EXISTS:
                return function($object) use ($value) {

                    foreach($value as $row) {

                        if ($row !== $object || serialize($row) === serialize($object)) {

                            return false;
                        }
                    }

                    return true;
                };

            case Comparison::LIKE:
                return function($object) use ($field, $value) {

                    return ClosureExpressionVisitor::findString(
                        ClosureExpressionVisitor::getObjectFieldValue($object, $field),
                        $value
                    );
                };

            case Comparison::NOT_LIKE:
                return function($object) use ($field, $value) {

                    return ! ClosureExpressionVisitor::findString(
                        ClosureExpressionVisitor::getObjectFieldValue($object, $field),
                        $value
                    );
                };

            default:
                throw ExpressionException::unknownExpressionOperator($comparison);
        }
    }

    /**
     * Convert a composite expression into the target query language output.
     *
     * @param CompositeExpression $expr
     *
     * @return mixed
     *
     * @throws ExpressionException
     */
    public function walkCompositeExpression(CompositeExpression $expr) {

        $expressionList = array();

        foreach($expr->getExpressionList() as $expression) {

            $expressionList[] = $this->dispatch($expression);
        }

        switch($expr->getLogical()) {

            case CompositeExpression::LOGICAL_AND:
                return $this->andExpression($expressionList);

            case CompositeExpression::LOGICAL_OR:
                return $this->orExpression($expressionList);

            default:
                throw ExpressionException::unknownCompositeLogical($expr->getLogical());
        }
    }

    /**
     * Run composite expression with conjunction.
     *
     * @param array $expressions
     *
     * @return callable
     */
    private function andExpression($expressions) {

        return function($object) use ($expressions) {

            foreach($expressions as $expression) {

                if ( ! $expression($object)) {

                    return false;
                }
            }

            return true;
        };
    }

    /**
     * Run composite expression with disjunction.
     *
     * @param array $expressions
     *
     * @return callable
     */
    private function orExpression($expressions) {

        return function($object) use ($expressions) {

            foreach($expressions as $expression) {

                if ($expression($object)) {

                    return true;
                }
            }

            return false;
        };
    }
}