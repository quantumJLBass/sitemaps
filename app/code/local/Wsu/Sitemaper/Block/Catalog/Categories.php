<?php
/**
 * Wsu
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Wsu EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.wsu.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.wsu.com/ for more information
 *
 * @category   Wsu
 * @package    Wsu_Sitemaper
 * @copyright  Copyright (c) 2012 Wsu (http://www.wsu.com/)
 * @license    http://www.wsu.com/LICENSE-1.0.html
 */

/**
 * Extended Sitemap extension
 *
 * @category   Wsu
 * @package    Wsu_Sitemaper
 * @author     Wsu Dev Team
 */

class Wsu_Sitemaper_Block_Catalog_Categories extends Mage_Core_Block_Template
{
    const XML_PATH_SHOW_PRODUCTS = 'wsu_seo/sitemaper/show_products';
    const XML_PATH_SORT_ORDER = 'wsu_seo/sitemaper/sort_order';
    const XML_PATH_CATEGORY_ANCHOR = 'wsu_seo/sitemaper/category_anchor';

    protected $_storeRootCategoryPath = '';
    protected $_storeRootCategoryLevel = 0;
    protected $_categories = array();

    protected function _prepareLayout()
    {
        $parent = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load(Mage::app()->getStore()->getRootCategoryId());
        $this->_storeRootCategoryPath = $parent->getPath();
        $this->_storeRootCategoryLevel = $parent->getLevel();
        //$collection = $this->getTreeCollection();
        $this->getTreeCollection();
        //$this->setCollection($collection);
        return $this;
    }

    public function getCategories()
    {
        return $this->_categories;
    }

    public function getTreeCollection()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('name')
            ->joinUrlRewrite()
            //->addPathsFilter($this->_storeRootCategoryPath . '/')
            //->addLevelFilter($this->_storeRootCategoryLevel + 1)
            ->addAttributeToFilter('is_active', 1) //->addIsActiveFilter()
            ->setOrder('level', 'ASC') //->addOrderField('path')
            ->setOrder(Mage::getStoreConfig(self::XML_PATH_SORT_ORDER), 'ASC')
            ;
        if (Mage::getStoreConfigFlag(self::XML_PATH_CATEGORY_ANCHOR)){
            $collection->addAttributeToSelect('is_anchor');
        }

        // Magento v1.2.0.2 Compatibility
        $collection->getSelect()->where('e.path LIKE ?', $this->_storeRootCategoryPath . '/%');

        foreach ($collection->getItems() as $item){
            if (!isset($level)){
                $level = $item->getLevel();
            }
            if ($item->getLevel() == $level){
                $this->_categories[] = $item;
                if ($item->getChildrenCount()){
                    $this->_addChildren($item->getId(), $collection);
                }
            }
        }
        return $collection;
    }

    protected function _addChildren($parentId, $collection)
    {
        foreach ($collection->getItems() as $item){
            if ($item->getParentId() == $parentId){
                $this->_categories[] = $item;
                if ($item->getChildrenCount()){
                    $this->_addChildren($item->getId(), $collection);
                }
            }
        }
    }

    public function getLevel($item, $delta = 1)
    {
        return (int) ($item->getLevel() - $this->_storeRootCategoryLevel - 1) * $delta;
    }

    public function getItemUrl($category)
    {
        $helper = Mage::helper('catalog/category');
        /* @var $helper Mage_Catalog_Helper_Category */
        return $helper->getCategoryUrl($category);
    }

    public function showProducts()
    {
        if (!isset($this->_showProducts)){
            $this->_showProducts = Mage::getStoreConfigFlag(self::XML_PATH_SHOW_PRODUCTS);
        }
        return $this->_showProducts;
    }
}
