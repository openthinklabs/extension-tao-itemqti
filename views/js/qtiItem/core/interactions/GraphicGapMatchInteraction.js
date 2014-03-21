define(['taoQtiItem/core/interactions/GraphicInteraction', 'taoQtiItem/core/Element', 'lodash'], function(GraphicInteraction, Element, _){

    var GraphicGapMatchInteraction = GraphicInteraction.extend({
        qtiClass : 'graphicGapMatchInteraction',
        gapImgs : {},
        addGapImg : function(gapImg){
            if(Element.isA(gapImg, 'gapImg')){
                gapImg.setRelatedItem(this.getRelatedItem() || null);
                this.gapImgs[gapImg.getSerial()] = gapImg;
            }
        },
        removeGapImg : function(gapImg){
            var serial = '';
            if(typeof(gapImg) === 'string'){
                serial = gapImg;
            }else if(Element.isA('gapImg')){
                serial = gapImg.getSerial();
            }
            delete this.gapImgs[serial];
            return this;
        },
        getGapImgs : function(){
            var gapImgs = {};
            for(var id in this.gapImgs){
                gapImgs[id] = this.gapImgs[id];
            }
            return gapImgs;
        },
        getComposingElements : function(){
            var elts = this._super();
            //recursive to choices:
            for(var serial in this.gapImgs){
                elts[serial] = this.gapImgs[serial];
                elts = _.extend(elts, this.gapImgs[serial].getComposingElements());
            }
            return elts;
        },
        find : function(serial){
            var found = this._super(serial);
            if(!found){
                if(this.gapImgs[serial]){
                    found = {'parent' : this, 'element' : this.gapImgs[serial]};
                }
            }
            return found;
        },
        render : function(data, $container){
            var defaultData = {
                'gapImgs' : []
            };

            //note: no choice shuffling option available for graphic gap match
            var gapImgs = this.getGapImgs();
            for(var serial in gapImgs){
                if(Element.isA(gapImgs[serial], 'choice')){
                    defaultData.gapImgs.push(gapImgs[serial].render());
                }
            }

            var tplData = _.merge(defaultData, data || {});

            return this._super(tplData, $container);
        },
        toArray : function(){
            var arr = this._super();
            arr.gapImgs = {};
            var gapImgs = this.getGapImgs();
            for(var serial in gapImgs){
                arr.gapImgs[serial] = gapImgs[serial].toArray();
            }
            return arr;
        }
    });

    return GraphicGapMatchInteraction;
});