<?php

namespace Minetro\Normgen\Generator\Entity\Decorator;

use Minetro\Normgen\Entity\Column;
use Minetro\Normgen\Utils\ColumnTypes;
use Minetro\Normgen\Utils\Helpers;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Nette\Utils\Strings;

class ColumnMapper implements IDecorator
{

    /**
     * @param PhpNamespace $namespace
     * @param ClassType $class
     * @param Column $column
     * @return void
     */
    public function doDecorate(Column $column, ClassType $class, PhpNamespace $namespace)
    {

	    $name = Strings::upper('COL_' . $column->getName());
    	$class->addConstant($name, Helpers::camelCase($column->getName()));

        switch ($column->getType()) {

            // Map: DateTime
            case ColumnTypes::TYPE_DATETIME:
                $column->setType('DateTimeImmutable');

                if ($column->getDefault() !== NULL) {
                    $column->setDefault('now');
                }

                $namespace->addUse('Nextras\Dbal\Utils\DateTimeImmutable');
                break;

            // Map: Enum
            case ColumnTypes::TYPE_ENUM:

                foreach ($column->getEnum() as $enum) {
                    $name = Strings::upper($column->getName()) . '_' . $enum;
                    $class->addConstant($name, $enum);
                }

                if ($column->getDefault() !== NULL) {
                    $column->setDefault(Strings::upper($column->getName()) . '_' . $column->getDefault());
                }

                break;
        }

    }

}