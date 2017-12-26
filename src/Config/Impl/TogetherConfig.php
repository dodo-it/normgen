<?php

namespace Minetro\Normgen\Config\Impl;

use Minetro\Normgen\Config\Config;

class TogetherConfig extends Config
{

    /** @var array */
    protected $defaults = [
        // Output folder
        'output' => NULL,
        // Generator
        'generator.generate.strategy' => Config::STRATEGY_TOGETHER,
        'generator.generate.entities' => TRUE,
        'generator.generate.repositories' => TRUE,
        'generator.generate.mappers' => TRUE,
        'generator.generate.facades' => TRUE,
        'generator.entity.exclude.primary' => FALSE,
	    'generator.generate.model' => TRUE,
        // NextrasORM
        'nextras.orm.class.entity' => 'Nextras\Orm\Entity\Entity',
        'nextras.orm.class.repository' => 'Nextras\Orm\Repository\Repository',
        'nextras.orm.class.mapper' => 'Nextras\Orm\Mapper\Mapper',
    	'nextras.orm.class.collection' => 'Nextras\Orm\Collection\ICollection',
        'nextras.orm.class.ientity' => 'Nextras\Orm\Entity\IEntity',
	    'nextras.orm.class.model' => 'Nextras\Orm\Model\Model',
        // ORM
        'orm.namespace' => 'App\Model',
	    'orm.filename.suffix' => 'Orm',
        // Entity
        'entity.folder' => NULL,
        'entity.namespace' => 'App\Model',
        'entity.extends' => 'App\Model\AbstractEntity',
        'entity.name.suffix' => NULL,
        'entity.filename.suffix' => NULL,
        // Repository
        'repository.folder' => NULL,
        'repository.namespace' => 'App\Model',
        'repository.extends' => 'App\Model\AbstractRepository',
        'repository.name.suffix' => 'Repository',
        'repository.filename.suffix' => 'Repository',
        // Mapper
        'mapper.folder' => NULL,
        'mapper.namespace' => 'App\Model',
        'mapper.extends' => 'App\Model\AbstractMapper',
        'mapper.name.suffix' => 'Mapper',
        'mapper.filename.suffix' => 'Mapper',
        // Facade
        'facade.folder' => NULL,
        'facade.namespace' => 'App\Model',
        'facade.extends' => 'App\Model\AbstractFacade',
        'facade.name.suffix' => 'Facade',
        'facade.filename.suffix' => 'Facade',
    ];

}
