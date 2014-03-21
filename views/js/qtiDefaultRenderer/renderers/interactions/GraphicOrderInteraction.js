define(['tpl!taoQtiDefaultRenderer/tpl/interactions/graphicInteraction', 'taoQtiDefaultRenderer/renderers/interactions/Interaction'], function(tpl, Interaction){
    return {
        qtiClass : 'graphicOrderInteraction',
        template : tpl,
        render : Interaction.render,
        setResponse : Interaction.setResponse,
        getResponse : Interaction.getResponse
    };
});