define(['taoQtiItem/core/interactions/Interaction', 'taoQtiItem/core/interactions/Prompt', 'lodash'], function(Interaction, Prompt, _){

    var BlockInteraction = Interaction.extend({
        init : function(serial, attributes){
            this._super(serial, attributes);
            this.prompt = new Prompt('');
        },
        is : function(qtiClass){
            return (qtiClass === 'blockInteraction') || this._super(qtiClass);
        },
        getComposingElements : function(){
            var elts = this._super();
            elts = _.extend(elts, this.prompt.getComposingElements());
            elts[this.prompt.getSerial()] = this.prompt;
            return elts;
        },
        find : function(serial){
            return this._super(serial) || this.prompt.find(serial);
        },
        render : function(data, $container){
            var defaultData = {
                'prompt' : this.prompt.render()
            };
            var tplData = _.merge(defaultData, data || {});
            return this._super(tplData, $container);
        },
        postRender : function(data){
            this.prompt.postRender();
            this._super(data);
        },
        toArray : function(){
            var arr = this._super();
            arr.prompt = this.prompt.toArray();
            return arr;
        }
    });
    return BlockInteraction;
});

