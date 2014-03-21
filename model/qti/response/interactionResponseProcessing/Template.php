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
namespace oat\taoQtiItem\model\qti\response\interactionResponseProcessing;

use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\Template;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\InteractionResponseProcessing;
use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\response\Template;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\MatchCorrectTemplate;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\MapResponseTemplate;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\MapResponsePointTemplate;
use oat\taoQtiItem\model\qti\exception\ParsingException;

/**
 * Short description of class
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */
abstract class Template
    extends InteractionResponseProcessing
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createByTemplate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string templateUri
     * @param  Response response
     * @param  Item item
     * @return oat\taoQtiItem\model\qti\response\interactionResponseProcessing\InteractionResponseProcessing
     */
    public static function createByTemplate($templateUri,  ResponseDeclaration $response,  Item $item)
    {
        $returnValue = null;

        // section 127-0-1-1--2c448032:1355d3fa7bf:-8000:00000000000037B1 begin
    switch ($templateUri) {
			case Template::MATCH_CORRECT :
				$returnValue = self::create(MatchCorrectTemplate::CLASS_ID, $response, $item);
				break;
			case Template::MAP_RESPONSE :
				$returnValue = self::create(MapResponseTemplate::CLASS_ID, $response, $item);
				break;
			case Template::MAP_RESPONSE_POINT :
				$returnValue = self::create(MapResponsePointTemplate::CLASS_ID, $response, $item);
				break;
			default :
				throw new ParsingException('Cannot create interactionResponseProcessing for unknown Template '.$templateUri);
		}
        // section 127-0-1-1--2c448032:1355d3fa7bf:-8000:00000000000037B1 end

        return $returnValue;
    }

} /* end of abstract class oat\taoQtiItem\model\qti\response\interactionResponseProcessing\Template */

?>