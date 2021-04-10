<?php
namespace MGModule\vultr\models\whmcs\customFields;

use MGModule\vultr as main;

/**
 * Product Custom Fields Colletion
 */
class Repository
{
    private static $configuration;
    public $relationID;
    public $type;
    private $_fields = array();

    /**
     * Load Product Custom Fields
     *
     * @param int $productID
     */
    public function __construct($type, $relationID)
    {
        $this->type = $type;
        $this->relationID = $relationID;
        $result = main\mgLibs\MySQL\Query::select(customField::fieldDeclaration(), customField::tableName(), array('relid' => $this->relationID, 'type' => $this->type));

        while ($row = $result->fetch()) {
            $this->_fields[] = new customField($row['id'], $this->type, $this->relationID, $row);
        }
    }

    public static function setConfiguration(array $configuration)
    {
        self::$configuration = $configuration;
    }

    /**
     * Compare current Fields with Declaration from Module Configuration
     *
     * @param bool $onlyRequired
     * @return array
     */
    public function checkFields(array $configuration = array())
    {
        if (empty($configuration)) {
            $configuration = self::$configuration;
        }

        $missingFields = array();
        foreach ($configuration as $fieldDeclaration) {
            $found = false;
            foreach ($this->_fields as $field) {
                if ($fieldDeclaration->name === $field->name) {
                    $found = true;

                    break;
                }
            }
            if (!$found) {
                $name = (empty($fieldDeclaration->friendlyName)) ? $fieldDeclaration->name : $fieldDeclaration->friendlyName;
                $missingFields[$fieldDeclaration->name] = $name;
            }
        }

        return $missingFields;
    }

    /**
     * Generate Custom Fields Depends on declaration in Module Configuration
     *
     * @param array $configuration
     */
    public function generateFromConfiguration(array $configuration = array())
    {
        if (empty($configuration)) {
            $configuration = self::$configuration;
        }

        foreach ($configuration as $fieldDeclaration) {
            $found = false;
            foreach ($this->_fields as $field) {
                if ($fieldDeclaration->name === $field->name) {
                    $found = true;

                    break;
                }
            }

            if (!$found) {
                $fieldDeclaration->save();
                $this->_fields[] = $fieldDeclaration;
            }
        }
    }

    /**
     * @return customField[]
     */
    public function get()
    {
        return $this->_fields;
    }
}
