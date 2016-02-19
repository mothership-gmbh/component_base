<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mothership
 * @package   Mothership_Base
 * @author    Don Bosco van Hoi <vanhoi@mothership.de>
 * @copyright Copyright (c) 2016 Mothership GmbH
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.mothership.de/
 */
namespace Mothership\Mage\Attributes;

/**
 * Class Option
 *
 * @category  Mothership
 * @package   Mothership_Option
 * @author    Don Bosco van Hoi <vanhoi@mothership.de>
 * @copyright 2016 Mothership GmbH
 * @link      http://www.mothership.de/
 *
 *            Class Helper to generate Magento attributes
 */
class Option
{
    /**
     * Get the Magento attribute code
     *
     * @param string $attribute_code The attribute code must be a string like 'product_group'
     *
     * @return mixed
     */
    public function getLabels($attribute_code)
    {
        $labels = [];
        $attribute_model        = \Mage::getModel('eav/entity_attribute');
        $attribute_options_model= \Mage::getModel('eav/entity_attribute_source_table') ;

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $attribute_code);
        $attribute              = $attribute_model->load($attribute_code);

        $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        foreach($options as $option)
        {
            $labels[] = $option['label'];
        }
        return $labels;
    }

    /**
     * Create a Magento attribute option. A default value for store_code 0 MUST be set
     *
     * @param string $attribute_code     The attribute code must be a string like 'product_group'
     * @param mixed  $attribute_options  an array with [store_id] => attribute_option_label
     *                                   example: [0] => 'Handtasche'
     *                                           [1] => 'Shopping Bag'
     * @param int    $default_store_code
     *
     * @link http://www.webspeaks.in/2012/05/addupdate-attribute-option-values.html
     *
     * @return void
     */
    public function addOptions($attribute_code, array $attribute_options, $default_store_code = 0)
    {
        if ($attribute_options === null) {
            throw new \Exception('No values given');
        }

        // use the default store code as reference
        $newAttributes = [];
        $labels = $this->getLabels($attribute_code);
        foreach ($attribute_options as $_option) {
            if (in_array($_option[$default_store_code], $labels)) {
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