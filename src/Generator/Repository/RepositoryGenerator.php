<?php

namespace Minetro\Normgen\Generator\Repository;

use Minetro\Normgen\Config\Config;
use Minetro\Normgen\Entity\Database;
use Minetro\Normgen\Entity\PhpDoc;
use Minetro\Normgen\Generator\AbstractGenerator;
use Minetro\Normgen\Resolver\IModelResolver;
use Minetro\Normgen\Resolver\IRepositoryResolver;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpNamespace;
use Minetro\Normgen\Resolver\IEntityResolver;
use Minetro\Normgen\Resolver\IMapperResolver;

class RepositoryGenerator extends AbstractGenerator
{

    /** @var IRepositoryResolver */
    private $resolver;
    
    /** @var IEntityResolver */
    private $entityResolver;
    
    /** @var IMapperResolver */
    private $mapperResolver;

    /** @var IModelResolver */
    public $modelResolver;

	/**
	 * @param Config $config
	 * @param IRepositoryResolver $resolver
	 * @param IEntityResolver $entityResolver
	 * @param IMapperResolver $mapperResolver
	 * @param IModelResolver $modelResolver
	 */
    function __construct(Config $config, IRepositoryResolver $resolver, IEntityResolver $entityResolver, IMapperResolver $mapperResolver, IModelResolver $modelResolver)
    {
        parent::__construct($config);

        $this->resolver = $resolver;
        $this->entityResolver = $entityResolver;
        $this->mapperResolver = $mapperResolver;
        $this->modelResolver = $modelResolver;
    }

    /**
     * @param Database $database
     */
    public function generate(Database $database)
    {
        foreach ($database->getTables() as $table) {
            // Create namespace and inner class
            $namespace = new PhpNamespace($this->resolver->resolveRepositoryNamespace($table));
            $class = $namespace->addClass($this->resolver->resolveRepositoryName($table));

            // Detect extends class
            if (($extends = $this->config->get('repository.extends')) !== NULL) {
                $namespace->addUse($extends);
                $class->setExtends($extends);
            }
            
            $entityName = $this->entityResolver->resolveEntityName($table);
            $class->addMethod("getEntityClassNames")
				->addComment("@return array")
	            ->setReturnType('array')
	            ->setVisibility('public')
				->setStatic(true)
				->addBody("return [$entityName::class];");
            
			$mapperName = $this->mapperResolver->resolveMapperName($table);
			$collection = $this->config->get('nextras.orm.class.collection');
			$namespace->addUse($collection);
			$entity = $this->config->get('nextras.orm.class.ientity');
            $namespace->addUse($entity);
			
			$class->addComment("@method $mapperName getMapper()");
			$class->addComment("@method $entityName hydrateEntity(array " . '$data' . ")");
			$class->addComment("@method $entityName attach(IEntity " . '$entity' . ")");
			$class->addComment("@method void detach(IEntity " . '$entity' . ")");
			$class->addComment("@method $entityName|NULL getBy(array " . '$conds' . ")");
			$class->addComment("@method $entityName|NULL getById(" . '$primaryValue' . ")");
			$class->addComment("@method ICollection|" . $entityName . "[] findAll()");
			$class->addComment("@method ICollection|" . $entityName . "[] findBy(array " . '$where' . ")");
			$class->addComment("@method ICollection|" . $entityName . "[] findById(" . '$primaryValues' . ")");
			$class->addComment("@method $entityName|NULL persist(IEntity " . '$entity'. ", " . '$withCascade' . " = true)");
			$class->addComment("@method $entityName|NULL persistAndFlush(IEntity " . '$entity'. ", " . '$withCascade' . " = true)");
			$class->addComment("@method $entityName remove(" . '$entity'. ", " . '$withCascade' . " = true)");
			$class->addComment("@method $entityName removeAndFlush(" . '$entity'. ", " . '$withCascade' . " = true)");

	        $namespace->addUse($this->modelResolver->resolveModelNamespace($table) . \Minetro\Normgen\Utils\Helpers::NS . $this->modelResolver->resolveModelName($table));
	        $modelDoc = new PhpDoc();
	        $modelDoc->setAnnotation("@method");
	        $modelDoc->setType($this->modelResolver->resolveModelName($table) . " getModel(" . '$need' . " = true)");
	        $class->addComment((string)$modelDoc);
            
            // Save file
            $this->generateFile($this->resolver->resolveRepositoryFilename($table), (string)$namespace);
        }

        // Generate abstract base class
        if ($this->config->get('repository.extends') !== NULL) {
            // Create abstract class
            $namespace = new PhpNamespace($this->config->get('repository.namespace'));
            $class = $namespace->addClass(Helpers::extractShortName($this->config->get('repository.extends')));
            $class->setAbstract(TRUE);

            // Add extends from ORM/Repository
            $extends = $this->config->get('nextras.orm.class.repository');
            $namespace->addUse($extends);
            $class->setExtends($extends);

            // Save file
            $this->generateFile($this->resolver->resolveFilename(Helpers::extractShortName($this->config->get('repository.extends')), $this->config->get('repository.folder')), (string)$namespace);
        }
    }

}
