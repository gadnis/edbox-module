<?php

namespace Edbox\PSModule\EdboxModule\Concerns;

use Language;

/**
 * Used inside PrestaShop ObjectModel to fill all properties with the given attributes
 *
 * Example of usage:
 *     $product = new Product($request->request->getInt('product_id'));
 *     $product->fill($request->request->all());
 *     $product->validateFields();
 */
trait FillableObjectModel
{
    /**
     * These values will be skipped from mass assignment
     *
     * @var array
     */
    protected static $skipFieldNames = ['date_upd', 'date_add'];

    /**
     * Fill multilingual object model values.
     * Skips guarded and adds defined attributes values
     *
     * @param  array  $attributes
     *
     * @return this
     */
    public function fill(array $attributes)
    {
        if (empty($attributes))
            return $this;

        foreach ($this->getDefinitionFields() as $field => $params) {

            // Look for multilingual definition values and apply values
            if (array_key_exists('lang', $params) && $params['lang']) {
                foreach (Language::getIDs(false) as $languageId) {
                    $languageId = (int)$languageId;
                    $fieldKey = $field.'_'.$languageId;

                    // skip if not in attributes array
                    if (! isset($attributes[ $fieldKey ])) {
                        continue;
                    }

                    $this->{$field}[$languageId] = $attributes[ $fieldKey ];
                }
            } else {

                if ($this->shouldSkipMassAssignment($field, $attributes)) {
                    continue;
                }

                $this->{$field} = $attributes[ $field ];
            }
        }

        return $this;
    }

    /**
     * Get all definition fields
     *
     * @return array
     */
    protected function getDefinitionFields()
    {
        $definition = self::getDefinition($this);
        return isset($definition['fields'])
            ? $definition['fields']
            : [];
    }

    /**
     * Check if value should be skipped from mass assignment
     *
     * @param  string $field      Field name
     * @param  array  $attributes [description]
     *
     * @return boolean
     */
    protected function shouldSkipMassAssignment(string $field, array $attributes)
    {
        // Skip if not in attributes
        // or not in model property
        // or table identifier
        // or in $skipFieldNames
        if (! isset($attributes[ $field ])
            || ! array_key_exists($field, $this)
            || $field == $this->identifier
            || in_array($field, self::$skipFieldNames)
        ) {
            return true;
        }
        return false;
    }
}