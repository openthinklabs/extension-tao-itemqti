<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
namespace oat\taoQtiItem\controller\QTIform\response;

use oat\taoQtiItem\controller\QTIform\response\SliderInteraction;
use oat\taoQtiItem\controller\QTIform\response\Response;
use \tao_helpers_form_FormFactory;

/**
 * Short description of class
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @subpackage actions_QTIform_response
 */
class SliderInteraction
    extends Response
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return mixed
     */
    public function initElements()
    {
        // section 10-13-1-39--1553ee98:12ddcd3839e:-8000:000000000000300E begin
		parent::setCommonElements();
		
		$baseTypeElt = tao_helpers_form_FormFactory::getElement('baseType', 'Radiobox');
		$baseTypeElt->setDescription(__('Response variable type'));
		$options = array(
			'integer' => __('Integer'),
			'float' => __('Float')
		);
		$baseTypeElt->setOptions($options);
		$baseType = $this->response->getAttributeValue('baseType');
		if(!empty($baseType)){
			if(in_array($baseType, array_keys($options))){
				$baseTypeElt->setValue($baseType);
			}else{
				$baseTypeElt->setValue('integer');
			}
		}
		$this->form->addElement($baseTypeElt);
        // section 10-13-1-39--1553ee98:12ddcd3839e:-8000:000000000000300E end
    }

}