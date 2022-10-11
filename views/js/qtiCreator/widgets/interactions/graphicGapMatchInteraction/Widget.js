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
 * Copyright (c) 2015-2019 (original work) Open Assessment Technologies SA;
 *
 */


/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 'lodash', 'i18n',
    'ui/hider',
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicInteraction/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/states/states'
], function($, _, __, hider, Widget, GraphicWidget, states){

    'use strict';

    /**
     * The Widget that provides components used by the QTI Creator for the GraphicGapMatch Interaction
     *
     * @extends taoQtiItem/qtiCreator/widgets/interactions/Widget
     * @extends taoQtiItem/qtiCreator/widgets/interactions/GraphicInteraction/Widget
     *
     * @exports taoQtiItem/qtiCreator/widgets/interactions/graphicGapMatchInteraction/Widget
     */
    var GraphicGapMatchInteractionWidget = _.extend(Widget.clone(), GraphicWidget, {

        /**
         * Initialize the widget
         * @see {taoQtiItem/qtiCreator/widgets/interactions/Widget#initCreator}
         * @param {Object} options - extra options
         * @param {String} options.baseUrl - the resource base url
         * @param {jQueryElement} options.choiceForm = a reference to the form of the choices
         */
        initCreator : function(options){
            var paper;
            this.baseUrl = options.baseUrl;
            this.choiceForm = options.choiceForm;

            this.registerStates(states);

            //call parent initCreator
            Widget.initCreator.call(this);

            paper = this.createPaper(_.bind(this.scaleGapList, this));
            if(paper){
                this.element.paper = paper;
                this.createChoices();
                this.createGapImgs();
            }
        },

        /**
         * Gracefully destroy the widget
         * @see {taoQtiItem/qtiCreator/widgets/Widget#destroy}
         */
        destroy : function(){

            var $container = this.$original;
            var $item      = $container.parents('.qti-item');

            //stop listening the resize
            $item.off('resize.gridEdit.' + this.element.serial);
            $(window).off('resize.qti-widget.' + this.element.serial);
            $container.off('resize.qti-widget.' + this.element.serial);

            //call parent destroy
            Widget.destroy.call(this);
        },

        /**
         * Called back on paper resize to scale the gap list
         * @param {Number} newSize - the interaction size
         * @param {Number} [factor=1] - scaling factor
         */
        scaleGapList : function(newSize, factor){

            var $container = this.$original;
            var $gapList   = $('ul.source', $container);
            $gapList.css('max-width', newSize + 'px');
            if(factor && factor !== 1){
                $gapList.find('img').each(function(){
                    var $img = $(this);
                    $img.width( $img.attr('width') * factor );
                    $img.height( $img.attr('height') * factor );
                });
                $container.data('factor', factor);
            }
        },

        /**
         * Create the gap images
         */
        createGapImgs : function(){
            var interaction = this.element;
            var $container  = this.$original;
            var $gapList    = $('ul.source', $container);

            $gapList.empty();
            _.forEach(interaction.gapImgs, function(gapImg){
                $gapList.append(gapImg.render());
            });
        },

        /**
         * Display warning message in case matchMax is set to 0 (infinite) and pair is higher that 0
         * @param {String} template
         * @param {Object} choice
         */
        infinityMatchMax (template, choice) {
            const interaction = this.element;
            const response = interaction.getResponseDeclaration();

            // getNormalMaximum() could be boolean or number
            const isInfinitePair = interaction.getNormalMaximum() === false ? true : false;
            const choiceMatchMax = choice && parseInt(choice.attr('matchMax'), 10);

            let isInfinityMatchMax = isInfinitePair;
            const checkTemplates = ['hotspot', 'gapImg'].includes(template);
            if (choiceMatchMax && checkTemplates) {
                isInfinityMatchMax = false;
            } else if (isInfinitePair && checkTemplates) {
                const identifier = choice.attr('identifier');
                isInfinityMatchMax = this.isChoiceInfinitePair(identifier, interaction, response);
            }

            const $panel = response.renderer.getAreaBroker().getPropertyPanelArea();
            hider.toggle($(`.response-matchmax-info.${template}`, $panel), isInfinityMatchMax);
        },

        /**
         * Sort an array of associable choices by its matchMax attr value
         * @param {Array} choiceCollection
         * @returns {Array}
         */
        getMatchMaxOrderedChoices (choiceCollection) {
            return _(choiceCollection)
                .map(function (choice) {
                    var matchMax = parseInt(choice.attr('matchMax'), 10);
                    if (_.isNaN(matchMax)) {
                        matchMax = 0;
                    }
                    return {
                        matchMax: matchMax === 0 ? Infinity : matchMax,
                        id: choice.id()
                    };
                })
                .sortBy('matchMax')
                .reverse()
                .valueOf();
        },

        pairExists (collection, pair) {
            if (pair.length !== 2) {
                return false;
            }
            return collection[pair[0] + ' ' + pair[1]] || collection[pair[1] + ' ' + pair[0]];
        },

        calculatePossiblePairs (interaction) {
            var pairs = [];
            var matchSet1 = this.getMatchMaxOrderedChoices(interaction.getGapImgs());
            var matchSet2 = this.getMatchMaxOrderedChoices(interaction.getChoices());

            _.forEach(matchSet1, choice1 => _.forEach(matchSet2, choice2 => pairs.push([choice1.id, choice2.id])));

            return pairs;
        },

        isChoiceInfinitePair (identifier, interaction, response) {
            const mapEntries = response.mapEntries;
            const mapDefault = parseFloat(response.mappingAttributes.defaultValue) || 0;

            // check possible pairs
            const allPossibleMapEntries = _.clone(mapEntries);
            if (mapDefault && mapDefault > 0) {
                const possiblePairs = this.calculatePossiblePairs(interaction);
                _.forEachRight(possiblePairs, pair => {
                    if (!this.pairExists(allPossibleMapEntries, pair)) {
                        allPossibleMapEntries[pair[0] + ' ' + pair[1]] = mapDefault;
                    }
                });
            }

            let isInfinitePair = false;
            Object.keys(allPossibleMapEntries).forEach(key => {
                if (key.includes(identifier) && parseInt(allPossibleMapEntries[key], 10) > 0) {
                    let choice = interaction.getChoiceByIdentifier(key.replace(identifier, '').trim());
                    if (parseInt(choice.attr('matchMax'), 10) === 0) {
                        isInfinitePair = true;
                    }
                }
            })
            return isInfinitePair;
        }
    });

    return GraphicGapMatchInteractionWidget;
});
