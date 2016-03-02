<?php
/**
 * This file is part of the Mothership GmbH code.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Mothership\Mage\Products;

/**
 * Class Mothership\Mage\Products\Entity.
 *
 * @category  Mothership
 *
 * @author    Don Bosco van Hoi <vanhoi@mothership.de>
 * @copyright 2016 Mothership GmbH
 *
 * @link      http://www.mothership.de/
 */
class Entity
{
    /**
     * Retreive a collection of Magento attributes by code and value.
     *
     * @param string $attributeCode The attribute code
     * @param string $attributeValue The attribute value
     * @param string $excludedSku Which Magento-Sku do you want to exclude
     * @param int    $limit MySQL Offset
     *
     * @return mixed
     */
    public function getCollectionByAttributeCodeAndValue(
        $attributeCode,
        $attributeValue = null,
        $excludedSku = null,
        $limit = 100
    ) {
        $connection = \Mage::getSingleton('core/resource')->getConnection('core_write')->getConnection();

        $sql
            = "
        SELECT ce.sku,
                ce.entity_id,
               ce.type_id,
               ea.attribute_code,
               CASE ea.backend_type
                 WHEN 'varchar'  THEN ce_varchar.value
                 WHEN 'int'      THEN ce_int.value
                 WHEN 'text'     THEN ce_text.value
                 WHEN 'decimal'  THEN ce_decimal.value
                 WHEN 'datetime' THEN ce_datetime.value
                 ELSE ea.backend_type
               END AS compiled_value
        FROM   (SELECT sku,
                       entity_type_id,
                       type_id,
                       entity_id
                FROM  catalog_product_entity) AS ce
               LEFT JOIN eav_attribute AS ea
                      ON ce.entity_type_id = ea.entity_type_id
               LEFT JOIN catalog_product_entity_varchar AS ce_varchar
                      ON ce.entity_id = ce_varchar.entity_id
                         AND ea.attribute_id = ce_varchar.attribute_id
                         AND ea.backend_type = 'varchar'
               LEFT JOIN catalog_product_entity_int AS ce_int
                      ON ce.entity_id = ce_int.entity_id
                         AND ea.attribute_id = ce_int.attribute_id
                         AND ea.backend_type = 'int'
               LEFT JOIN catalog_product_entity_text AS ce_text
                      ON ce.entity_id = ce_text.entity_id
                         AND ea.attribute_id = ce_text.attribute_id
                         AND ea.backend_type = 'text'
               LEFT JOIN catalog_product_entity_decimal AS ce_decimal
                      ON ce.entity_id = ce_decimal.entity_id
                         AND ea.attribute_id = ce_decimal.attribute_id
                         AND ea.backend_type = 'decimal'
               LEFT JOIN catalog_product_entity_datetime AS ce_datetime
                      ON ce.entity_id = ce_datetime.entity_id
                         AND ea.attribute_id = ce_datetime.attribute_id
                         AND ea.backend_type = 'datetime'
        WHERE  ea.attribute_code = :attribute_code
        ";

        if (null !== $excludedSku) {
            $sql .= ' AND ce.sku != :excluded_sku';
        }

        if (null !== $attributeValue) {
            $sql .= ' HAVING compiled_value = :attribute_value';
        }

        $sql .= ' LIMIT :limit';

        $sth = $connection->prepare($sql);

        $sth->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $sth->bindParam(':attribute_code', $attributeCode, \PDO::PARAM_STR);

        if (null !== $excludedSku) {
            $sth->bindParam(':excluded_sku', $excludedSku, \PDO::PARAM_STR);
        }

        if (null !== $attributeValue) {
            $sth->bindParam(':attribute_value', $attributeValue, \PDO::PARAM_STR);
        }

        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
}
