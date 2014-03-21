define(['taoQtiItem/core/IdentifiedElement', 'taoQtiItem/mixin/Container'], function(IdentifiedElement, Container){
    
    var ModalFeedback = IdentifiedElement.extend({
        'qtiClass' : 'modalFeedback',
        is : function(qtiClass){
            return (qtiClass === 'feedback') || this._super(qtiClass);
        }
    });
    
    Container.augment(ModalFeedback);
    
    return ModalFeedback;
});