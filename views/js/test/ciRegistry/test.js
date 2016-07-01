define([
    'jquery',
    'lodash',
    'taoQtiItem/portableElementRegistry/ciRegistry',
    'taoQtiItem/test/ciRegistry/data/testProvider',
    'taoQtiItem/qtiCreator/helper/qtiElements'
], function($, _, ciRegistry, testProvider, qtiElements){

    QUnit.module('Custom Interaction Registry');

    var testReviewApi = [
        {name : 'addProvider', title : 'addProvider'},
        {name : 'getAllVersions', title : 'getAllVersions'},
        {name : 'getRuntime', title : 'getRuntime'},
        {name : 'getCreator', title : 'getCreator'},
        {name : 'getBaseUrl', title : 'getBaseUrl'},
        {name : 'loadRuntimes', title : 'loadRuntimes'},
        {name : 'loadCreators', title : 'loadCreators'},
        {name : 'getAuthoringData', title : 'getAuthoringData'},
    ];

    QUnit
        .cases(testReviewApi)
        .test('instance API ', function (data, assert){
            assert.equal(typeof ciRegistry[data.name], 'function', 'The registry exposes a "' + data.title + '" function');
        });

    QUnit.asyncTest('load creator', function(assert){

        ciRegistry.addProvider(testProvider).loadCreators(function(creators){

            console.log(arguments, qtiElements.isBlock('customInteraction.samplePci'));
            assert.ok(_.isPlainObject(creators), 'creators loaded');
            assert.ok(_.isObject(creators.samplePci), 'sample ci creator loaded');

            assert.ok(qtiElements.isBlock('customInteraction.samplePci'), 'sample ci loaded into model');

            QUnit.start();
        });


    });

});

