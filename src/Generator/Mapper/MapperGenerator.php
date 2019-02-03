<?php

namespace Minetro\Normgen\Generator\Mapper;

use Minetro\Normgen\Config\Config;
use Minetro\Normgen\Entity\Database;
use Minetro\Normgen\Entity\PhpDoc;
use Minetro\Normgen\Generator\AbstractGenerator;
use Minetro\Normgen\Resolver\IMapperResolver;
use Minetro\Normgen\Resolver\IModelResolver;
use Minetro\Normgen\Resolver\IRepositoryResolver;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpNamespace;

class MapperGenerator extends AbstractGenerator
{

    /** @var IMapperResolver */
    private $resolver;

	/** @var IRepositoryResolver */
	private $repositoryResolver;

	/** @var IModelResolver */
	public $modelResolver;

	/**
	 * @param Config $config
	 * @param IMapperResolver $resolver
	 * @param IRepositoryResolver $repositoryResolver
	 * @param IModelResolver $modelResolver
	 */
    function __construct(Config $config, IMapperResolver $resolver, IRepositoryResolver $repositoryResolver, IModelResolver $modelResolver)
    {
        parent::__construct($config);

        $this->resolver = $resolver;
	    $this->repositoryResolver = $repositoryResolver;
	    $this->modelResolver = $modelResolver;
    }

    /**
     * @param Database $database
     */
    public function generate(Database $database)
    {
        foreach ($database->getTables() as $table) {
            // Create namespace and inner class
            $namespace = new PhpNamespace($this->resolver->resolveMapperNamespace($table));
            $class = $namespace->addClass($this->resolver->resolveMapperName($table));

            // Detect extends class
            if (($extends = $this->config->get('mapper.extends')) !== NULL) {
                $namespace->addUse($extends);
                $class->setExtends($extends);
            }
			$class->addMethod('getTableName')
				->setBody('return \'' . $table->getName() . '\';')
				->setReturnType('string')
				->setVisibility('public');

            $primaryCount = 0;
			foreach ($table->getColumns() as $column) {
				$primaryCount += (int) $column->isPrimary();
			}
			if($primaryCount === 1) {
				foreach ($table->getColumns() as $column) {
					if ($column->isPrimary() && $column->getName() != 'id') {
						$class->addMethod('createStorageReflection')
							->setVisibility('protected')
							->addBody('$reflection = parent::createStorageReflection();')
							->addBody('$reflection->addMapping(\'' . $column->getName() . '\', \'id\');')
							->addBody('return $reflection;');
					}
				}
			}


	        $namespace->addUse($this->repositoryResolver->resolveRepositoryNamespace($table) . \Minetro\Normgen\Utils\Helpers::NS . $this->repositoryResolver->resolveRepositoryName($table));
	        $repositoryDoc = new PhpDoc();
	        $repositoryDoc->setAnnotation("@method");
	        $repositoryDoc->setType($this->repositoryResolver->resolveRepositoryName($table) . " getRepository(" . '$need' . " = true)");
	        $class->addComment((string)$repositoryDoc);

	        $namespace->addUse($this->modelResolver->resolveModelNamespace($table) . \Minetro\Normgen\Utils\Helpers::NS . $this->modelResolver->resolveModelName($table));
	        $modelDoc = new PhpDoc();
	        $modelDoc->setAnnotation("@method");
	        $modelDoc->setType($this->modelResolver->resolveModelName($table) . " getModel(" . '$need' . " = true)");
	        $class->addComment((string)$modelDoc);

            // Save file
            $this->generateFile($this->resolver->resolveFilename($this->resolver->resolveMapperName($table), $this->config->get('mapper.folder')), (string)$namespace);
        }

        // Generate abstract base class
        if ($this->config->get('mapper.extends') !== NULL) {
            // Create abstract class
            $namespace = new PhpNamespace($this->config->get('mapper.namespace'));
            $class = $namespace->addClass(Helpers::extractShortName($this->config->get('mapper.extends')));
            $class->setAbstract(TRUE);

            // Add extends from ORM/Mapper
            $extends = $this->config->get('nextras.orm.class.mapper');
            $namespace->addUse($extends);
            $class->setExtends($extends);

            // Save file
            $this->generateFile($this->resolver->resolveFilename(Helpers::extractShortName($this->config->get('mapper.extends')), $this->config->get('mapper.folder')), (string)$namespace);
        }
    }

}
