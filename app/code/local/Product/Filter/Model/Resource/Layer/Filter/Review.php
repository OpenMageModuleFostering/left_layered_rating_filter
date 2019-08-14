<?php
class Product_Filter_Model_Resource_Layer_Filter_Review extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('filter/review_summary', 'primary_id');
    }

    public function applyFilterToCollection($filter, $value)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $attribute  = $filter->getAttributeModel();
        $connection = $this->_getReadAdapter();
        $tableAlias = $attribute->getAttributeCode() . '_idx';
        $conditions = array(
            "{$tableAlias}.entity_pk_value = e.entity_id",
            $connection->quoteInto("{$tableAlias}.rating_summary > ?", (($value - 1) * 20)),
            $connection->quoteInto("{$tableAlias}.rating_summary <= ?", ($value * 20)),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId())
        );

        $collection->getSelect()->join(
            array($tableAlias => $this->getMainTable()),
            implode(' AND ', $conditions),
            array()
        );

        return $this;
    }

    public function getCount($filter)
    {
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $connection = $this->_getReadAdapter();
        $attribute  = $filter->getAttributeModel();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
        $conditions = array(
            "{$tableAlias}.entity_pk_value = e.entity_id",
            $connection->quoteInto("{$tableAlias}.store_id = ?", $filter->getStoreId()),
        );

        $select
            ->join(
                array($tableAlias => $this->getMainTable()),
                join(' AND ', $conditions),
                array('rating_summary')
            );

        return $connection->fetchAll($select);
    }
}

