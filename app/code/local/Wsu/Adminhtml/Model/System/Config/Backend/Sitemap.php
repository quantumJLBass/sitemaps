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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@wsu.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.wsu.com/ for more information
 * or send an email to sales@wsu.com
 *
 * @category   Wsu
 * @package    Wsu_Adminhtml
 * @copyright  Copyright (c) 2009 Wsu (http://www.wsu.com/)
 * @license    http://www.wsu.com/LICENSE-1.0.html
 */

/**
 * Wsu Adminhtml extension
 *
 * @category   Wsu
 * @package    Wsu_Adminhtml
 * @author     Wsu Dev Team <dev@wsu.com>
 */

class Wsu_Adminhtml_Model_System_Config_Backend_Sitemap extends Mage_Core_Model_Config_Data
{

    protected function _beforeSave()
    {
        $value     = $this->getValue();
        	if ($value < 0 || $value > 1) {
        	    throw new Exception(Mage::helper('sitemaper')->__('Priority must be between 0 and 1'));
        	} elseif (($value == 0) && !($value === '0' || $value === '0.0')) {
        	    throw new Exception(Mage::helper('sitemaper')->__('Priority must be between 0 and 1'));
        	}
        return $this;
    }

}
