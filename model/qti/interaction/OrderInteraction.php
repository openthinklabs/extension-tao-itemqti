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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\taoQtiItem\model\qti\interaction;

use oat\taoQtiItem\model\qti\interaction\OrderInteraction;
use oat\taoQtiItem\model\qti\interaction\BlockInteraction;

/**
 * QTI Order Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10291

 */
class OrderInteraction extends BlockInteraction
{
    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'orderInteraction';
    protected static $choiceClass = 'oat\\taoQtiItem\\model\\qti\\choice\\SimpleChoice';
    protected static $baseType = 'identifier';

    protected function getUsedAttributes()
    {
        return array_merge(
            parent::getUsedAttributes(),
            [
            'oat\\taoQtiItem\\model\\qti\\attribute\\Shuffle',
            'oat\\taoQtiItem\\model\\qti\\attribute\\MaxChoicesOrderInteraction',
            'oat\\taoQtiItem\\model\\qti\\attribute\\MinChoicesOrderInteraction',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Orientation'
                ]
        );
    }
}
