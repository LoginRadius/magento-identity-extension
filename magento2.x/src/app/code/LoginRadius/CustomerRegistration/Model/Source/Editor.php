<?php
/**
 * Copyright Â© 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */

namespace LoginRadius\CustomerRegistration\Model\Source;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
 
class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    )
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }
 
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setWysiwyg(true);
        $element->setConfig($this->_wysiwygConfig->getConfig($element));
        return parent::_getElementHtml($element);
    }
}
