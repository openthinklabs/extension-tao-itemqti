define(['taoQtiItem/core/choices/Choice', 'taoQtiItem/mixin/Container'], function(Choice, Container){

    var ContainerChoice = Choice.extend({
        init : function(serial, attributes){
            this._super(serial, attributes);
        },
        is : function(qtiClass){
            return (qtiClass === 'containerChoice') || this._super(qtiClass);
        }
    });
    
    Container.augment(ContainerChoice);
    
    return ContainerChoice;
});


