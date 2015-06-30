<?php
/*
 * This file is part of the Eduostia package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Query;
use Cqrs\ReadModel\ReadModelInterface;
use Illuminate\Container\Container;

/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/21/15
 */
class QueryDispatcher implements QueryDispatcherInterface {

	private $_container;

	private $_queryNamespace;
	private $_handlerNamespace;

	/**
	 * @param Container $container
	 * @param string    $queryNamespace
	 * @param string    $handlerNamespace
	 */
	public function __construct(Container $container, $queryNamespace, $handlerNamespace) {

		$this->_queryNamespace      = $queryNamespace;
		$this->_handlerNamespace    = $handlerNamespace;
		$this->_container           = $container;
	}

	/**
	 * {@inheritDoc}
	 */
	public function query(QueryInterface $query, ViewModelInterface $viewModel) {

		$handler    = $this->_container->make($this->getHandlerClass($query));
		$response   = call_user_func( [$handler, 'handle'], $query, $viewModel );
		$data       = array();

		if (is_array($response)) {

			foreach($response as $record) {

				$data[] = $this->serialize($record, $viewModel);
			}
		}
		else if ($response instanceof ReadModelInterface) {

			$data = $this->serialize($response, $viewModel);
		}

		return $data;
	}

	/**
	 * Map handler name
	 *
	 * @param QueryInterface $query
	 *
	 * @return string
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	protected function getHandlerClass(QueryInterface $query) {

		$query = str_replace($this->_queryNamespace, '', get_class($query));

		return $this->_handlerNamespace.'\\'.trim($query, '\\').'Handler';
	}

	/**
	 * @param ReadModelInterface|array|null $record
	 * @param ViewModelInterface            $viewModel
	 *
	 * @return array
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	protected function serialize($record, ViewModelInterface $viewModel) {

		if ($record instanceof ReadModelInterface) {

			$record = $record->serialize();
		}

		if (is_array($record)) {

			return $this->filterViewModel($record, $viewModel);
		}

		return array();
	}

	/**
	 * Filter data with view model.
	 *
	 * @param array              $record
	 * @param ViewModelInterface $viewModel
	 *
	 * @return array
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	protected function filterViewModel(array $record, ViewModelInterface $viewModel) {

		$fields = $viewModel->fields();
		$data   = array();

		if ( ! $fields) {

			$fields = array_keys($record);
		}

		foreach($fields as $field) {

			if ( ! isset($record[$field])) {

				throw new \RuntimeException(sprintf('Field "%s" not found in: %s.', $field, json_encode($record)));
			}

			$method = sprintf('get%s', ucfirst($field));

			if (method_exists($viewModel, $method)) {

				$data[$field] = $viewModel->{$method}($record[$field], $record);
			}
			else {

				$data[$field] = $record[$field];
			}
		}

		return $data;
	}
}