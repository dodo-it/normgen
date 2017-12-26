<?php

namespace Minetro\Normgen\Resolver;


use Minetro\Normgen\Entity\Table;

interface IModelResolver extends IFilenameResolver
{
	/**
	 * @param Table $table
	 * @return string
	 */
	function resolveModelName(Table $table = null);

	/**
	 * @param Table $table
	 * @return string
	 */
	function resolveModelNamespace(Table $table = null);

	/**
	 * @param Table $table
	 * @return string
	 */
	function resolveModelFilename(Table $table = null);
}