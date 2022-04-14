define([
    'services/features',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/Active',
    'taoQtiItem/qtiCreator/editor/ckEditor/htmlEditor',
    'taoQtiItem/qtiCreator/editor/gridEditor/content',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/static/text',
    'taoQtiItem/qtiCreator/widgets/static/helpers/itemScrollingMethods'
], function (features, stateFactory, Active, htmlEditor, content, formElement, formTpl, itemScrollingMethods) {
    'use strict';

    const wrapperCls = 'custom-text-box';

    const taoTooltipOption = 'TaoTooltip';

    const TextActive = stateFactory.extend(
        Active,
        function () {
            this.buildEditor();
            this.initForm();
        },
        function () {
            this.destroyEditor();
            this.widget.$form.empty();
        }
    );

    TextActive.prototype.buildEditor = function () {
        const widget = this.widget;
        const $editableContainer = widget.$container;
        const container = widget.element;
        const changeCallback = content.getChangeCallback(container);

        $editableContainer.attr('data-html-editable-container', true);

        const defaultEditorOptions = {
            change: function (data) {
                changeCallback.call(this, data);
                if (!data) {
                    widget.$form.find('[name="textBlockCssClass"]').val('');
                }
            },
            blur: function () {
                widget.changeState('sleep');
            },
            data: {
                widget: widget,
                container: container
            }
        }

        const getEditorOptions = function() {
            const editorOptions = {};
            const removePlugins = [];
    
            if(!features.isVisible(taoTooltipOption)) {
                removePlugins.push('taotooltip'); 
            }
            editorOptions.removePlugins = removePlugins.join(',');
            return Object.assign({}, defaultEditorOptions, editorOptions);
        }
        
        if (!htmlEditor.hasEditor($editableContainer)) {
            htmlEditor.buildEditor($editableContainer, getEditorOptions());
        }
    };

    TextActive.prototype.destroyEditor = function () {
        htmlEditor.destroyEditor(this.widget.$container);
    };

    TextActive.prototype.initForm = function () {
        const widget = this.widget,
            $form = widget.$form,
            $wrap = widget.$container.find(`.${wrapperCls}`),
            blockCls = itemScrollingMethods.cutScrollClasses($wrap.attr('class') || ''),
            isScrolling = itemScrollingMethods.isScrolling($wrap),
            selectedHeight = itemScrollingMethods.selectedHeight($wrap);

        $form.html(
            formTpl({
                textBlockCssClass: blockCls.replace(wrapperCls, '').trim(),
                scrolling: isScrolling,
                scrollingHeights: itemScrollingMethods.options()
            })
        );

        formElement.initWidget($form);

        formElement.setChangeCallbacks($form, widget.element, changeCallbacks(widget));

        itemScrollingMethods.initSelect($form, isScrolling, selectedHeight);
    };

    const changeCallbacks = function (widget) {
        return {
            textBlockCssClass: function (element, value) {
                let $wrap = widget.$container.find(`.${wrapperCls}`);

                value = value.trim();
                if (value === wrapperCls) {
                    value = '';
                }

                if (!$wrap.length) {
                    $wrap = widget.$container.find('[data-html-editable="true"]').wrapInner('<div />').children();
                }

                $wrap.attr('class', wrapperCls + ' ' + value);
            },
            scrolling: function (element, value) {
                itemScrollingMethods.wrapContent(widget, value, 'inner');
            },
            scrollingHeight: function (element, value) {
                itemScrollingMethods.setScrollingHeight(widget.$container.find(`.${wrapperCls}`), value);
            }
        };
    };

    return TextActive;
});
