<?php
/*
 * This file is part of the Soccer-Api package.
 *
 * (c) Eduostia Corporation <http://eduostia.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cqrs\Query;


/**
 * @author      Iqbal Maulana <iq.bluejack@gmail.com>
 * @created     4/22/15
 */
interface QueryDispatcherInterface {

	/**
	 * Dispatch query
	 *
	 * @param QueryInterface        $query
	 * @param ViewModelInterface    $viewModel
	 *
	 * @return mixed
	 *
	 * @author Iqbal Maulana <iq.bluejack@gmail.com>
	 */
	public function query(QueryInterface $query, ViewModelInterface $viewModel);
}