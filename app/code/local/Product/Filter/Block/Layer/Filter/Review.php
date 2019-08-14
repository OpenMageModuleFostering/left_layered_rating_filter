<?php
class Product_Filter_Block_Layer_Filter_Review extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'filter/layer_filter_review';
        $this->setTemplate("ratingfilter/layer/filter.phtml");
    }
    
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
}

