<?php

namespace Minetro\Normgen\Generator\Model;

use Minetro\Normgen\Config\Config;
use Minetro\Normgen\Entity\Database;
use Minetro\Normgen\Entity\PhpDoc;
use Minetro\Normgen\Generator\AbstractGenerator;
use Minetro\Normgen\Resolver\IEntityResolver;
use Minetro\Normgen\Resolver\IModelResolver;
use Minetro\Normgen\Resolver\IRepositoryResolver;
use Minetro\Normgen\Utils\Helpers;
use Nette\PhpGenerator\PhpNamespace;
use Tracy\Debugger;

class ModelGenerator extends AbstractGenerator
{
	/** @var IModelResolver */
	private $modelResolver;

	/** @var IRepositoryResolver */
	private $repositoryResolver;

	/** @var IEntityResolver */
	private $entityResolver;

	/**
	 * @param Config $config
	 * @param IModelResolver $resolver
	 * @param IRepositoryResolver $repositoryResolver
	 * @param IEntityResolver $entityResolver
	 */
	function __construct(Config $config, IModelResolver $resolver, IRepositoryResolver $repositoryResolver, IEntityResolver $entityResolver)
	{
		parent::__construct($config);

		$this->modelResolver = $resolver;
		$this->repositoryResolver = $repositoryResolver;
		$this->entityResolver = $entityResolver;
	}

	/**
	 * @param Database $database
	 * @return void
	 */
	function generate(Database $database)
	{
		$namespaceName = $this->modelResolver->resolveModelNamespace(null);
		if ($namespaceName === null) {
			return;
		}
		$namespace = new PhpNamespace($namespaceName);
		$namespace->addUse($this->config->get("nextras.orm.class.model"));

		$class = $namespace->addClass($this->modelResolver->resolveModelName(null));
		$class->setExtends($this->config->get("nextras.orm.class.model"));

		foreach ($database->getTables() as $table) {
			$namespace->addUse($this->repositoryResolver->resolveRepositoryNamespace($table) . Helpers::NS . $this->repositoryResolver->resolveRepositoryName($table));
			$doc = new PhpDoc();
			$doc->setAnnotation("@property-read");
			$doc->setType($this->repositoryResolver->resolveRepositoryName($table));
			$doc->setVariable(Helpers::camelCase(lcfirst($this->entityResolver->resolveEntityName($table))));
			$class->addComment((string)$doc);
		}

		$this->generateFile($this->modelResolver->resolveModelFilename(null), (string)$namespace);
	}
}