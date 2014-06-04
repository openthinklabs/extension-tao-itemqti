define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/Choice',
    'taoQtiItem/qtiCreator/widgets/choices/simpleAssociableChoice/states/Choice',
    'taoQtiItem/qtiItem/core/Element',
    'lodash'
], function(stateFactory, Choice, SimpleAssociableChoice, Element, _){

    var GapTextStateChoice = stateFactory.extend(Choice, function(){

        var _widget = this.widget;

        //listener to other siblings choice mode
        _widget.beforeStateInit(function(e, element, state){

            if(Element.isA(element, 'choice') && _widget.interaction.getBody().getElement(element.serial)){//@todo hottext an

                if(state.name === 'choice' && element.serial !== _widget.serial){
                    _widget.changeState('question');
                }

            }

        }, 'otherActive');

        this.buildEditor();

    }, function(){

        this.destroyEditor();

        this.widget.offEvents('otherActive');
    });

    GapTextStateChoice.prototype.initForm = function(){
        SimpleAssociableChoice.prototype.initForm.call(this);
    };

    GapTextStateChoice.prototype.buildEditor = function(){

        var _widget = this.widget,
            $editableContainer = _widget.$container,
            $tlb = $editableContainer.find('.mini-tlb'),
            $editable;

        $tlb.detach();

        $editableContainer.wrapInner($('<div>', {'class' : 'inner-wrapper'}));
        $editableContainer.append($tlb);
        $editable = $editableContainer.find('.inner-wrapper');

        $editable.attr('contentEditable', true);

        //focus then reset content to have the cursor at the end:
        $editable.focus();
        $editable.html($editable.html());

        $editable.on('keyup.qti-widget', _.throttle(function(){

            //update model
            _widget.element.val($(this).text());

        }, 200)).on('keypress.qti-widget', function(e){

            if(e.which === 13){
                e.preventDefault();
                $(this).blur();
                _widget.changeState('question');
            }

        });
    };

    GapTextStateChoice.prototype.destroyEditor = function(){

        var $container = this.widget.$container;

        $container.find('td').removeAttr('contentEditable');
        $container.children('td:first').off('keyup.qti-widget');
    };

    return GapTextStateChoice;
});