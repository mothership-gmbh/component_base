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
            $label[] = $option['label'];
        }
        return $labels;
    }

    /**
     * Create a Magento attribute optin.
     *
     * @param string $attribute_code    The attribute code must be a string like 'product_group'
     * @param mixed  $attribute_options an array with [store_id] => attribute_option_label
     *                                  example: [0] => 'Handtasche'
     *                                           [1] => 'Shopping Bag'
     *
     * @return void
     */
    protected function _addAttributeOption($attribute_code, array $attribute_options)
    {
        if ($attribute_options === null) {
            throw new \Exception('No values given');
        }

        $attr_model = Mage::getModel('catalog/resource_eav_attribute');
        $attr       = $attr_model->loadByCode('catalog_product', $attribute_code);
        $attr_id    = $attr->getAttributeId();

        $option['attribute_id'] = $attr_id;
        $option['value']['any_option_name'][0] = $attribute_options;

        if (null !== $attribute_option_value_en) {
            $option['value']['any_option_name'][2] = $attribute_option_value_en;
        }

        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        $setup->addAttributeOption($option);
    }
}