<?php
/**
 * This file is part of the Mothership GmbH code.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Mothership\Mage\Attributes;

/**
 * Class Mothership\Mage\Attributes\Option.
 *
 * @category  Mothership
 *
 * @author    Don Bosco van Hoi <vanhoi@mothership.de>
 * @copyright 2016 Mothership GmbH
 *
 * @link      http://www.mothership.de/
 */
class Option
{
    /**
     * Get the Magento attribute code.
     *
     * @param string $attribute_code The attribute code must be a string like 'product_group'
     *
     * @return mixed
     */
    public function getLabels($attribute_code)
    {
        $labels                  = [];
        $attribute_model         = \Mage::getModel('eav/entity_attribute');
        $attribute_options_model = \Mage::getModel('eav/entity_attribute_source_table');

        $attribute_code = $attribute_model->getIdByCode('catalog_product', $attribute_code);
        $attribute      = $attribute_model->load($attribute_code);

        $attribute_options_model->setAttribute($attribute);
        $options = $attribute_options_model->getAllOptions(false);

        foreach ($options as $option) {
            $labels[$option['value']] = $option['label'];
        }

        return $labels;
    }

    /**
     * Create a Magento attribute option. A default value for store_code 0 MUST be set.
     *
     * @param string $attribute_code     The attribute code must be a string like 'product_group'
     * @param mixed  $attribute_options  an array with [store_id] => attribute_option_label
     *                                   example: [0] => 'Handtasche'
     *                                   [1] => 'Shopping Bag'
     * @param int    $default_store_code
     *
     * @link http://www.webspeaks.in/2012/05/addupdate-attribute-option-values.html
     */
    public function addOptions($attribute_code, array $attribute_options, $default_store_code = 0)
    {
        if ($attribute_options === null) {
            throw new \Exception('No values given');
        }

        // use the default store code as reference
        $labels = $this->getLabels($attribute_code);
        foreach ($attribute_options as $_store => $label) {
            if (in_array($label, $labels)) {
                // do not create the attribute if it exists
                return;
            }
        }

        $attr_model = \Mage::getModel('catalog/resource_eav_attribute');
        $attr       = $attr_model->loadByCode('catalog_product', $attribute_code);
        $attr_id    = $attr->getAttributeId();

        $option['attribute_id'] = $attr_id;
        foreach ($attribute_options as $_store => $_label) {
            $option['value']['any_option_name'][$_store] = $_label;
        }

        $setup = new \Mage_Eav_Model_Entity_Setup('core_setup');
        $setup->addAttributeOption($option);
    }
}
