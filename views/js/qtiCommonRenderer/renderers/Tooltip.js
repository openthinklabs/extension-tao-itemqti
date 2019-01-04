/**
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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
/**
 * @author Christophe Noël <christophe@taotesting.com>
 */
define([
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/tooltip',
    'taoQtiItem/qtiCommonRenderer/helpers/container',
    'ui/tooltip'
], function(tpl, containerHelper, tooltip){
    'use strict';

    return {
        qtiClass : '_tooltip',
        template : tpl,
        getContainer : containerHelper.get,
        render: function render(tooltipDOM) {
            var $container = containerHelper.get(tooltipDOM);
            var renderedTooltip = tooltip.create($container, {
                theme: 'default',
                title: tooltipDOM.content(),
                placement: 'top'
            });

            if($container.data('$tooltip')){
                $container.data('$tooltip').dispose();
                $container.removeData('$tooltip');
            }
            $container.data('$tooltip', renderedTooltip);

        }
    };
});
