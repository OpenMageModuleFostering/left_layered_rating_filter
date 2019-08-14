<?php
class Product_Filter_Model_Layer_Filter_Review extends Mage_Catalog_Model_Layer_Filter_Abstract
{
    protected $_resource;

    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'review';
    }
    
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('filter/layer_filter_review');
        }
        return $this->_resource;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }
        $text = $filter . " " . Mage::helper('catalog')->__("stars");
        if ($filter) {
            $this->_getResource()->applyFilterToCollection($this, $filter);
            $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
            $this->_items = array();
        }
        return $this;
    }

    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $key = $this->getLayer()->getStateKey().'_'.$this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $reviewSummaries = $this->_getResource()->getCount($this);
            $data = array();
            for ($i = 1; $i < 6; $i++) {
                $filteredSummaries = array_filter($reviewSummaries, function($el) use ($i) {
                    return ((($i - 1) * 20) < $el["rating_summary"]) && ($el["rating_summary"] <= ($i * 20));
                });
                
                if ($filteredSummaries) {
                    $data[] = array(
                        'label' => $i,
                        'value' => $i,
                        'count' => sizeof($filteredSummaries)
                    );
                }
            }

            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG.':'.$attribute->getId()
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }
}
