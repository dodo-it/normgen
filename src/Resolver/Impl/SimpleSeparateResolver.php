<?php

namespace Minetro\Normgen\Resolver\Impl;

use Doctrine\Common\Inflector\Inflector;
use Minetro\Normgen\Entity\Table;
use Minetro\Normgen\Resolver\IFilenameResolver;

class SimpleSeparateResolver extends SimpleResolver
{

    /**
     * @param Table $table
     * @return string
     */
    public function resolveEntityName(Table $table)
    {
		return Inflector::singularize(Inflector::classify($table->getName())) . 'Entity';
        return $this->normalize(ucfirst($table->getName()) . $this->config->get('entity.name.suffix'));
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveEntityNamespace(Table $table)
    {
        return $this->config->get('entity.namespace');
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveEntityFilename(Table $table)
    {
        return $this->config->get('entity.folder') . DIRECTORY_SEPARATOR . $this->resolveEntityName($table) . '.' . IFilenameResolver::PHP_EXT;
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveRepositoryName(Table $table)
    {
		return Inflector::singularize(Inflector::classify($table->getName())) . 'Repository';
        return $this->normalize(ucfirst($table->getName()) . $this->config->get('repository.name.suffix'));
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveRepositoryNamespace(Table $table)
    {
        return $this->config->get('repository.namespace');
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveRepositoryFilename(Table $table)
    {
        return $this->config->get('repository.folder') . DIRECTORY_SEPARATOR . $this->normalize(ucfirst($table->getName()) . $this->config->get('repository.filename.suffix')) . '.' . IFilenameResolver::PHP_EXT;
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveMapperName(Table $table)
    {
		return Inflector::singularize(Inflector::classify($table->getName())) . 'Mapper';
        return $this->normalize(ucfirst($table->getName()) . $this->config->get('mapper.name.suffix'));
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveMapperFilename(Table $table)
    {
        return $this->config->get('mapper.folder') . DIRECTORY_SEPARATOR . $this->normalize(ucfirst($table->getName()) . $this->config->get('mapper.filename.suffix')) . '.' . IFilenameResolver::PHP_EXT;
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveMapperNamespace(Table $table)
    {
        return $this->config->get('mapper.namespace');
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
        return $this->config->get('facade.namespace');
    }

    /**
     * @param Table $table
     * @return string
     */
    public function resolveFacadeFilename(Table $table)
    {
        return $this->config->get('facade.folder') . DIRECTORY_SEPARATOR . $this->normalize(ucfirst($table->getName()) . $this->config->get('facade.filename.suffix')) . '.' . IFilenameResolver::PHP_EXT;
    }

}
