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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 */
/**
 * @author Christophe Noël <christophe@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/Active',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/static/printedVariable',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/static/helpers/inline'
], function($, _, __, stateFactory, Active, formTpl, formElement, inlineHelper){
    'use strict';

    var PrintedVariableStateActive = stateFactory.extend(Active, function(){

        this.initForm();

    }, function(){

        this.widget.$form.empty();
    });

    PrintedVariableStateActive.prototype.initForm = function(){

        var _widget = this.widget,
            $printedVariable = _widget.$original,
            $form = _widget.$form,
            printedVariable = _widget.element,
            relatedItem = printedVariable.getRelatedItem(),
            outcomes;

        outcomes = (relatedItem.data('outcomes') || []).map(function(entry) {
            var selected = (printedVariable.attr('identifier') === entry);
            return {
                value: entry,
                name: entry,
                selected: selected
            };
        });

        $form.html(formTpl({
            outcomes: outcomes
        }));

        //... init standard ui widget
        formElement.initWidget($form);

        //init data change callbacks
        formElement.setChangeCallbacks($form, printedVariable, {
            identifier: function(pv, value, name) {
                printedVariable.attr(name, value);
                $printedVariable.html(value);
                inlineHelper.togglePlaceholder(_widget);
            }
        });

    };

    return PrintedVariableStateActive;
});
