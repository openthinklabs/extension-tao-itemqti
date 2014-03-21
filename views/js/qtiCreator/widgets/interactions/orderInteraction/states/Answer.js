define([
    'taoQtiItemCreator/widgets/states/factory',
    'taoQtiItemCreator/widgets/interactions/states/Answer',
    'taoQtiCommonRenderer/renderers/interactions/OrderInteraction',
    'taoQtiCommonRenderer/helpers/Helper',
    'lodash',
    'i18n'
], function(stateFactory, Answer, commonRenderer, helper, _, __){

    var OrderInteractionStateAnswer = stateFactory.clone(Answer);

    OrderInteractionStateAnswer.prototype.createResponseWidget = function(){

        var _widget = this.widget,
            interaction = _widget.element,
            response = interaction.getResponseDeclaration(),
            correctResponse = _.values(response.getCorrect());

        helper.appendInstruction(this.widget.element, __('Please define the correct order in the box to the right.'));

        commonRenderer.render(this.widget.element);
        commonRenderer.setResponse(interaction, _formatResponse(correctResponse));

        _widget.$container.on('responseChange.qti-widget', function(e, data){
            response.setCorrect(_unformatResponse(data.response));
        });
    };

    OrderInteractionStateAnswer.prototype.removeResponseWidget = function(){
        commonRenderer.restore(this.widget.element);
        this.widget.$container.off('responseChange.qti-widget');
    };

    var _formatResponse = function(response){
        return {list : {identifier : response}};
    };

    var _unformatResponse = function(formatedResponse){
        var res = [];
        if(formatedResponse.list && formatedResponse.list.identifier){
            res = formatedResponse.list.identifier;
        }
        return res;
    };

    return OrderInteractionStateAnswer;
});