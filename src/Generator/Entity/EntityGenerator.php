<?php

namespace Minetro\Normgen\Generator\Entity;

use Minetro\Normgen\Config\Config;
use Minetro\Normgen\Entity\Database;
use Minetro\Normgen\Entity\PhpDoc;
use Minetro\Normgen\Generator\AbstractGenerator;
use Minetro\Normgen\Generator\Entity\Decorator\ColumnDocumentor;
use Minetro\Normgen\Generator\Entity\Decorator\ColumnMapper;
use Minetro\Normgen\Generator\Entity\Decorator\IDecorator;
use Minetro\Normgen\Resolver\IEntityResolver;
use Minetro\Normgen\Resolver\IModelResolver;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpNamespace;
use Minetro\Normgen\Resolver\IRepositoryResolver;

class EntityGenerator extends AbstractGenerator
{

    /** @var IEntityResolver */
    private $resolver;
    
    /** @var IRepositoryResolver */
    private $repositoryResolver;

    /** @var IModelResolver */
    public $modelResolver;

    /** @var IDecorator[] */
    private $decorators = [];

	/**
	 * @param Config $config
	 * @param IEntityResolver $resolver
	 * @param IRepositoryResolver $repositoryResolver
	 * @param IModelResolver $modelResolver
	 */
    function __construct(Config $config, IEntityResolver $resolver, IRepositoryResolver $repositoryResolver, IModelResolver $modelResolver)
    {
        parent::__construct($config);

        $this->resolver = $resolver;
		$this->repositoryResolver = $repositoryResolver;
		$this->modelResolver = $modelResolver;
        
        $this->decorators[] = new ColumnMapper();
        $this->decorators[] = new ColumnDocumentor($resolver);
    }

    /**
     * @param ColumnMapper $columnMapper
     */
    public function setColumnMapper($columnMapper)
    {
        $this->columnMapper = $columnMapper;
    }

    /**
     * @param ColumnDocumentor $columnDocumentor
     */
    public function setColumnDocumentor($columnDocumentor)
    {
        $this->columnDocumentor = $columnDocumentor;
    }

    /**
     * @param Database $database
     */
    public function generate(Database $database)
    {
        foreach ($database->getTables() as $table) {
            // Create namespace and inner class
            $namespace = new PhpNamespace($this->resolver->resolveEntityNamespace($table));
            $class = $namespace->addClass($this->resolver->resolveEntityName($table));

            // Detect extends class
            if (($extends = $this->config->get('entity.extends')) === NULL) {
                $extends = $this->config->get('nextras.orm.class.entity');
            }

            // Add namespace and extends class
            $namespace->addUse($extends);
            $class->setExtends($extends);

            // Add table columns
            foreach ($table->getColumns() as $column) {

                if ($this->config->get('generator.entity.exclude.primary')) {
                    if ($column->isPrimary()) continue;
                }

                foreach ($this->decorators as $decorator) {
                    $decorator->doDecorate($column, $class, $namespace);
                }
            }

            //Add annotation method
	        $namespace->addUse($this->repositoryResolver->resolveRepositoryNamespace($table) . \Minetro\Normgen\Utils\Helpers::NS . $this->repositoryResolver->resolveRepositoryName($table));
	        $repositoryDoc = new PhpDoc();
	        $repositoryDoc->setAnnotation("@method");
	        $repositoryDoc->setType($this->repositoryResolver->resolveRepositoryName($table) . " getRepository(" . '$need' . " = true)");
	        $class->addComment((string)$repositoryDoc);

            // Save file
            $this->generateFile($this->resolver->resolveEntityFilename($table), (string)$namespace);
        }

        // Generate abstract base class
        if ($this->config->get('entity.extends') !== NULL) {
            // Create abstract class
            $namespace = new PhpNamespace($this->config->get('entity.namespace'));
            $class = $namespace->addClass(Helpers::extractShortName($this->config->get('entity.extends')));
            $class->setAbstract(TRUE);

            // Add extends from ORM/Entity
            $extends = $this->config->get('nextras.orm.class.entity');
            $namespace->addUse($extends);
            $class->setExtends($extends);

            // Save file
            $this->generateFile($this->resolver->resolveFilename(Helpers::extractShortName($this->config->get('entity.extends')), $this->config->get('entity.folder')), (string)$namespace);
        }
    }

}
