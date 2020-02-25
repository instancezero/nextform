<?php
namespace Abivia\NextForm\Tools;

use Abivia\NextForm\Data\Property;
use Abivia\NextForm\Data\Schema;
use Abivia\NextForm\Data\Segment;
use Abivia\NextForm\Data\Store;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Types as DbTypes;
/**
 *
 */
class SchemaManager
{
    /**
     * Map DBAL types to our types, with a size specification if known.
     * @var array
     */
    static $dbalTypeMap = [
        DbTypes::BIGINT => ['int', 21],
        DbTypes::BINARY => ['binary'],
        DbTypes::BOOLEAN => ['int', 1],
        DbTypes::DATE_IMMUTABLE => ['date'],
        DbTypes::DATE_MUTABLE => ['date'],
        DbTypes::DATETIMETZ_IMMUTABLE => ['datetime'],
        DbTypes::DATETIMETZ_MUTABLE => ['datetime'],
        DbTypes::DATETIME_IMMUTABLE => ['datetime'],
        DbTypes::DATETIME_MUTABLE => ['datetime'],
        DbTypes::DECIMAL => ['decimal'],
        DbTypes::FLOAT => ['float'],
        DbTypes::GUID => ['string'],
        DbTypes::INTEGER => ['int', 11],
        DbTypes::SMALLINT => ['int', 6],
        DbTypes::STRING => ['string'],
        DbTypes::TEXT => ['string'],
        DbTypes::TIME_IMMUTABLE => ['time'],
        DbTypes::TIME_MUTABLE => ['time'],
    ];

    /**
     * The database we will inspect.
     *
     * @var Connection
     */
    protected $dbc;

    /**
     * The Property we're currently working with.
     *
     * @var Property
     */
    protected $property;

    /**
     * The NextForm Schema structure we plan to manipulate.
     *
     * @var Schema
     */
    protected $schema;

    /**
     * The segment we're currently working with.
     *
     * @var Segment
     */
    protected $segment;

    public function __construct(Schema $schema = null)
    {
        $this->schema = $schema;
    }

    public function build($params, $tableFilters = []) : Schema
    {
        $this-> dbc = DriverManager::getConnection($params);

        $except = $tableFilters['except'] ?? null;
        $only = $tableFilters['only'] ?? null;
        $this->schema = new Schema();
        foreach ($this->dbc->getSchemaManager()->listTables() as $table) {
            $segName = $table->getName();
            if ($except !== null && in_array($segName, $except)) {
                continue;
            }
            if ($only !== null && !in_array($segName, $only)) {
                continue;
            }
            $this->updateSegment($table);
        }

        return $this->schema;
    }

    public function connect($params)
    {
        // This throws a DBALException on failure
        $this-> dbc = DriverManager::getConnection($params);

        return $this;
    }

    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function update($tableFilters = [])
    {
        $except = $tableFilters['except'] ?? null;
        $only = $tableFilters['only'] ?? null;
        foreach ($this->dbc->getSchemaManager()->listTables() as $table) {
            $segName = $table->getName();
            if ($except !== null && in_array($segName, $except)) {
                continue;
            }
            if ($only !== null && !in_array($segName, $only)) {
                continue;
            }
            $this->updateSegment($table);
        }
    }

    /**
     * Add or update a property in the segment that corresponds to a
     * database column.
     *
     * @param \Doctrine\DBAL\Schema\Column $column
     * @return void
     */
    protected function updateProperty(\Doctrine\DBAL\Schema\Column $column) {
        $propName = $column->getName();
        if (!($this->property = $this->segment->getProperty($propName))) {
            $newProperty = true;
            $this->property = new Property();
            $this->property->setName($propName);
            $this->property->setDescription($column->getComment());
            $this->property->setStore(new Store());
        } else {
            $newProperty = false;
        }
        $store = $this->property->getStore();
        $dbType = $column->getType();
        $dbTypeName = $dbType->getName();

        // If this type isn't something you'd find on a form, skip it
        if (!isset(self::$dbalTypeMap[$dbTypeName])) {
            return;
        }
        $typeInfo = self::$dbalTypeMap[$dbTypeName];
        $store->setType($typeInfo[0]);
        switch($typeInfo[0]) {
            case 'binary':
            case 'string':
                $store->setSize((string) ($column->getLength() ?? 255));
                break;
            case 'decimal':
                $store->setSize($column->getPrecision() . ',' . $column->getScale());
                break;
            case 'float':
                break;
            case 'int':
                $store->setSize((string) ($column->getPrecision() ?? $typeInfo[1]));
                break;
            default:
                $store->setSize(null);
                break;
        }
        if ($newProperty) {
            $this->segment->setProperty($this->property);
        }
    }

    /**
     * Add or update a segment in the schema that corresponds to a
     * database table.
     *
     * @param \Doctrine\DBAL\Schema\Table $table
     */
    protected function updateSegment(\Doctrine\DBAL\Schema\Table $table)
    {
        $segName = $table->getName();
        if (!($this->segment = $this->schema->getSegment($segName))) {
            $newSegment = true;
            $this->segment = new Segment();
            $this->segment->setName($segName);
        } else {
            $newSegment = false;
        }
        foreach ($table->getColumns() as $column) {
            $this-> updateProperty($column);
        }
        if (($pk = $table->getPrimaryKey())) {
            $this->segment->setPrimary($pk->getColumns());
        }
        if ($newSegment) {
            $this->schema->setSegment($segName, $this->segment);
        }
    }

}
