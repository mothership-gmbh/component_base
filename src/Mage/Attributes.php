<?php
/**
 * This file is part of the Mothership GmbH code.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Mothership\Mage;

/**
 * Class Attributes.
 *
 * @author    Don Bosco van Hoi <vanhoi@mothership.de>
 * @copyright 2016 Mothership GmbH
 *
 * @link      http://www.mothership.de/
 */
class Attributes
{
    /**
     * Check if the attribute values exists.
     *
     * @param string $b2csku
     *
     * @return array
     */
    public function getDataBySku($b2csku)
    {
        $connection = \Mage::getSingleton('core/resource')->getConnection('core_write')->getConnection();

        $sql
              = "
            SELECT *
            FROM   (SELECT ce.sku,
               ea.attribute_id,
               ea.attribute_code,
               ea.backend_type,
               CASE ea.backend_type
                 WHEN 'varchar' THEN ce_varchar.value
                 WHEN 'int' THEN ce_int.value
                 WHEN 'text' THEN ce_text.value
                 WHEN 'decimal' THEN ce_decimal.value
                 WHEN 'datetime' THEN ce_datetime.value
                 ELSE ea.backend_type
               END             AS `value`,
               e_eaov.value    AS option_value,
                   CASE ea.backend_type
                 WHEN 'varchar' THEN ce_varchar.value
                 WHEN 'int' THEN e_eaov.value
                 WHEN 'text' THEN ce_text.value
                 WHEN 'decimal' THEN ce_decimal.value
                 WHEN 'datetime' THEN ce_datetime.value
                 ELSE ea.backend_type
               END             AS `combined`,
               CASE ea.backend_type
                 WHEN 'varchar' THEN ce_varchar.store_id
                 WHEN 'int' THEN ce_int.store_id
                 WHEN 'TEXT' THEN ce_text.store_id
                 WHEN 'DECIMAL' THEN ce_decimal.store_id
                 WHEN 'DATETIME' THEN ce_datetime.store_id
               END             AS store_id,
               ea.is_required  AS required
        FROM   catalog_product_entity AS ce
               LEFT JOIN eav_attribute AS ea
                      ON ce.entity_type_id = ea.entity_type_id
               LEFT JOIN catalog_product_entity_varchar AS ce_varchar
                      ON ce.entity_id = ce_varchar.entity_id
                        AND ea.attribute_id = ce_varchar.attribute_id
                        AND ea.backend_type = 'VARCHAR'
               LEFT JOIN catalog_product_entity_int AS ce_int
                      ON ce.entity_id = ce_int.entity_id
                        AND ea.attribute_id = ce_int.attribute_id
                        AND ea.backend_type = 'INT'
               LEFT JOIN catalog_product_entity_text AS ce_text
                      ON ce.entity_id = ce_text.entity_id
                        AND ea.attribute_id = ce_text.attribute_id
                        AND ea.backend_type = 'TEXT'
               LEFT JOIN catalog_product_entity_decimal AS ce_decimal
                      ON ce.entity_id = ce_decimal.entity_id
                        AND ea.attribute_id = ce_decimal.attribute_id
                        AND ea.backend_type = 'DECIMAL'
               LEFT JOIN catalog_product_entity_datetime AS ce_datetime
                      ON ce.entity_id = ce_datetime.entity_id
                        AND ea.attribute_id = ce_datetime.attribute_id
                        AND ea.backend_type = 'DATETIME'
               LEFT JOIN eav_attribute_option_value AS e_eaov
                      ON e_eaov.`option_id` = ce_int.`VALUE`
                        AND e_eaov.`store_id` = ce_int.`store_id`
        WHERE  ce.sku = :sku) AS tab HAVING store_id IS NOT NULL
        ";
        $stmt = $connection->prepare($sql);
        $stmt->execute(
            [
                'sku' => $b2csku,
            ]
        );

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $data = [];
        foreach ($rows as $_row) {
            $data[$_row['attribute_code']][$_row['store_id']] = $_row;
        }

        return $data;
    }
}
