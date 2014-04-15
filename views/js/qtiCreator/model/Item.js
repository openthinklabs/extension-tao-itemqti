define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/mixin/editableContainer',
    'taoQtiItem/qtiItem/core/Item',
    'taoQtiItem/qtiCreator/model/ResponseProcessing',
     'taoQtiItem/qtiCreator/model/variables/OutcomeDeclaration',
    'taoQtiItem/qtiCreator/model/feedbacks/ModalFeedback'
], function(_, editable, editableContainer, Item, ResponseProcessing, OutcomeDeclaration, ModalFeedback){
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, editableContainer);
    _.extend(methods, {
        getDefaultAttributes : function(){
            return {
                identifier : 'myItem_1',
                title : 'Item title',
                adaptive : false,
                timeDependent : false
            };
        },
        createResponseProcessing : function(){
            var rp = new ResponseProcessing();
            rp.processingType = 'templateDriven';
            this.setResponseProcessing(rp);
            return rp;
        },
        createOutcomeDeclaration : function(attributes){
            
            var identifier = attributes.identifier || '';
            delete attributes.identifier;
            var outcome = new OutcomeDeclaration(attributes);
            
            this.addOutcomeDeclaration(outcome);
            outcome.buildIdentifier(identifier);
            
            return outcome;
        },
        createModalFeedback : function(attributes){
            
            var identifier = attributes.identifier || '';
            delete attributes.identifier;
            var modalFeedback = new ModalFeedback(attributes);

            this.addModalFeedback(modalFeedback);
            modalFeedback.buildIdentifier(identifier);
            modalFeedback.body('Some feedback text.');

            return modalFeedback;
        }
    });
    return Item.extend(methods);
});