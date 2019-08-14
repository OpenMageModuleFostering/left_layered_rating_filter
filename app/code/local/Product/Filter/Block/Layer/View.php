<?php
class Product_Filter_Block_Layer_View extends Mage_Catalog_Block_Layer_View
{
    protected $_reviewFilterBlockName;
    
    protected function _initBlocks()
    {
        $this->_stateBlockName              = 'catalog/layer_state';
        $this->_categoryBlockName           = 'catalog/layer_filter_category';
        $this->_attributeFilterBlockName    = 'catalog/layer_filter_attribute';
        $this->_priceFilterBlockName        = 'catalog/layer_filter_price';
        $this->_decimalFilterBlockName      = 'catalog/layer_filter_decimal';
        $this->_reviewFilterBlockName       = 'filter/layer_filter_review';
    }

    protected function _prepareLayout()
    {
        $stateBlock = $this->getLayout()->createBlock($this->_stateBlockName)
            ->setLayer($this->getLayer());

        $categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
            ->setLayer($this->getLayer())
            ->init();

        $this->setChild('layer_state', $stateBlock);
        $this->setChild('category_filter', $categoryBlock);

        $filterableAttributes = $this->_getFilterableAttributes();
        foreach ($filterableAttributes as $attribute) {
            if ($attribute->getAttributeCode() == 'price') {
                $filterBlockName = $this->_priceFilterBlockName;
            } elseif ($attribute->getAttributeCode() == "review") {
                $filterBlockName = $this->_reviewFilterBlockName;
            } elseif ($attribute->getBackendType() == 'decimal') {
                $filterBlockName = $this->_decimalFilterBlockName;
            } else {
                $filterBlockName = $this->_attributeFilterBlockName;
            }

            $this->setChild($attribute->getAttributeCode() . '_filter',
                $this->getLayout()->createBlock($filterBlockName)
                    ->setLayer($this->getLayer())
                    ->setAttributeModel($attribute)
                    ->init());
        }

        $this->getLayer()->apply();

        return $this;
    }
    
    protected function _getFilterableAttributes()
    {
        $attributes = $this->getData('_filterable_attributes');
        if (is_null($attributes)) {
            $attributes = $this->getLayer()->getFilterableAttributes();
            $review = Mage::getSingleton("eav/config")->getAttribute("10", "review");
            if ($attributes && $review) {
                $attributes->addItem($review);
            }
            $this->setData('_filterable_attributes', $attributes);
        }

        return $attributes;
    }
}
