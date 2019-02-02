<?php

namespace Minetro\Normgen\Resolver\Impl;

use Doctrine\Common\Inflector\Inflector;
use Minetro\Normgen\Entity\Table;
use Minetro\Normgen\Resolver\IFilenameResolver;
use Minetro\Normgen\Utils\Helpers;

class SimpleTogetherResolver extends SimpleResolver
{

    /**
     * @param Table $table
     * @return string
     */
    public function resolveEntityName(Table $table)
    {
    	return Inflector::singularize(Inflector::classify($table)) . 'Entity';
        return $this->normalize(ucfirst($table->getName()) . $this->config->get('entity.name.suffix'));
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveEntityNamespace(Table $table)
    {
        return $this->config->get('orm.namespace') . Helpers::NS . $this->table($table);
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveEntityFilename(Table $table)
    {
        return $this->table($table) . Helpers::DS . $this->normalize(ucfirst($table->getName()) . $this->config->get('entity.filename.suffix')) . '.' . IFilenameResolver::PHP_EXT;
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveRepositoryName(Table $table)
    {
        return $this->normalize(ucfirst($table->getName()) . $this->config->get('repository.name.suffix'));
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveRepositoryNamespace(Table $table)
    {
        return $this->config->get('orm.namespace') . Helpers::NS . $this->table($table);
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveRepositoryFilename(Table $table)
    {
        return $this->table($table) . Helpers::DS . $this->normalize(ucfirst($table->getName()) . $this->config->get('repository.filename.suffix')) . '.' . IFilenameResolver::PHP_EXT;
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveMapperName(Table $table)
    {
        return $this->normalize(ucfirst($table->getName()) . $this->config->get('mapper.name.suffix'));
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveMapperNamespace(Table $table)
    {
        return $this->config->get('orm.namespace') . Helpers::NS . $this->table($table);
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveMapperFilename(Table $table)
    {
        return $this->table($table) . Helpers::DS . $this->normalize(ucfirst($table->getName()) . $this->config->get('mapper.filename.suffix')) . '.' . IFilenameResolver::PHP_EXT;
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveFacadeName(Table $table)
    {
        return $this->normalize(ucfirst($table->getName()) . $this->config->get('facade.name.suffix'));
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveFacadeNamespace(Table $table)
    {
        return $this->config->get('orm.namespace') . Helpers::NS . $this->table($table);
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveFacadeFilename(Table $table)
    {
        return $this->table($table) . Helpers::DS . $this->normalize(ucfirst($table->getName()) . $this->config->get('facade.filename.suffix')) . '.' . IFilenameResolver::PHP_EXT;
    }

}
