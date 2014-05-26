/**
 * Define the location of all qti classes used in the QTI Creator
 */
define(['lodash', 'taoQtiItem/qtiItem/core/qtiClasses'], function(_, qtiClasses){
    
    //clone the qtiClasses instead of modifying it by direct extend:
    return _.defaults({
        'assessmentItem' : 'taoQtiItem/qtiCreator/model/Item',
        '_container' : 'taoQtiItem/qtiCreator/model/Container',
        'img' : 'taoQtiItem/qtiCreator/model/Img',
        'math' : 'taoQtiItem/qtiCreator/model/Math',
        'rubricBlock' : 'taoQtiItem/qtiCreator/model/RubricBlock',
        'modalFeedback' : 'taoQtiItem/qtiCreator/model/feedbacks/ModalFeedback',
        'choiceInteraction' : 'taoQtiItem/qtiCreator/model/interactions/ChoiceInteraction',
        'orderInteraction' : 'taoQtiItem/qtiCreator/model/interactions/OrderInteraction',
        'associateInteraction' : 'taoQtiItem/qtiCreator/model/interactions/AssociateInteraction',
        'matchInteraction' : 'taoQtiItem/qtiCreator/model/interactions/MatchInteraction',
        'inlineChoiceInteraction' : 'taoQtiItem/qtiCreator/model/interactions/InlineChoiceInteraction',
        'simpleChoice' : 'taoQtiItem/qtiCreator/model/choices/SimpleChoice',
        'simpleAssociableChoice' : 'taoQtiItem/qtiCreator/model/choices/SimpleAssociableChoice',
        'inlineChoice' : 'taoQtiItem/qtiCreator/model/choices/InlineChoice',
        'textEntry' : 'taoQtiItem/qtiCreator/model/choices/TextEntry',
        'mediaInteraction' : 'taoQtiItem/qtiCreator/model/interactions/MediaInteraction',
        'sliderInteraction' : 'taoQtiItem/qtiCreator/model/interactions/SliderInteraction',
        'hotspotInteraction' : 'taoQtiItem/qtiCreator/model/interactions/HotspotInteraction',
        'selectPointInteraction' : 'taoQtiItem/qtiCreator/model/interactions/SelectPointInteraction',
        'graphicInteraction' : 'taoQtiItem/qtiCreator/model/interactions/GraphicOrderInteraction',
        'graphicAssociateInteraction' : 'taoQtiItem/qtiCreator/model/interactions/GraphicAssociateInteraction',
        'graphicGapMatchInteraction' : 'taoQtiItem/qtiCreator/model/interactions/GraphicGapMatchInteraction',
        'graphicOrderInteraction' : 'taoQtiItem/qtiCreator/model/interactions/GraphicOrderInteraction',
        'hotspotChoice' : 'taoQtiItem/qtiCreator/model/choices/HotspotChoice',
        'gapImg' : 'taoQtiItem/qtiCreator/model/choices/GapImg',
        'associableHotspot' : 'taoQtiItem/qtiCreator/model/choices/AssociableHotspot',
        'responseDeclaration' : 'taoQtiItem/qtiCreator/model/variables/ResponseDeclaration',
        'responseProcessing' : 'taoQtiItem/qtiCreator/model/ResponseProcessing'
    }, qtiClasses);

});
