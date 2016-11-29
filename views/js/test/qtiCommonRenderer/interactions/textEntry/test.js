define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/text-entry-noconstraint.json',
    'json!taoQtiItem/test/samples/json/text-entry-length.json',
    'json!taoQtiItem/test/samples/json/text-entry-pattern.json'
], function ($, _, qtiItemRunner, textEntryData, textEntryLengthConstrainedData, textEntryPatternConstrainedData) {
    'use strict';

    var runner;
    var fixtureContainerId = 'item-container';
    var outsideContainerId = 'outside-container';

    module('Text Entry Interaction', {
        teardown: function () {
            if (runner) {
                runner.clear();
            }
        }
    });

    function getTooltipContent($input){
        var qtip = getTooltip($input);
        if(qtip){
            return qtip.find('.qtip-content').html();
        }
    }

    function getTooltip($input){
        var qtip = $input.data('qtip');
        if(qtip && qtip.tooltip && qtip.tooltip.length){
            return $(qtip.tooltip[0]);
        }
    }

    QUnit.asyncTest('Lenght constraint', function (assert) {

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', textEntryLengthConstrainedData)
            .on('render', function () {
                var $input = $container.find('.qti-interaction.qti-textEntryInteraction');

                assert.equal($input.length, 1, 'the container contains a text entry interaction .qti-textEntryInteraction');

                $input.val('');
                $input.focus();
                assert.equal(getTooltipContent($input), '5 chars allowed', 'the instruction message is correct');
                assert.ok(getTooltip($input).is(':visible'), 'info tooltip is visible');

                $input.val('123');
                $input.keyup();
                assert.equal(getTooltipContent($input), '3 of 5 chars maximum', 'the instruction message is correct');
                assert.ok(getTooltip($input).is(':visible'), 'info tooltip is visible');

                $input.val('12345');
                $input.keyup();
                assert.equal(getTooltipContent($input), 'maximum chars reached', 'the warning message is correct');
                assert.ok(getTooltip($input).is(':visible'), 'warning tooltip is visible');

                QUnit.start();
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('Pattern constraint - incorrect', function (assert) {

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', textEntryPatternConstrainedData)
            .on('render', function () {
                var $input = $container.find('.qti-interaction.qti-textEntryInteraction');

                assert.equal($input.length, 1, 'the container contains a text entry interaction .qti-textEntryInteraction');

                $input.val('');
                $input.focus();
                assert.equal(getTooltipContent($input), undefined);

                $input.val('123');
                $input.keyup();

            }).on('responsechange', function(state){
                var $input = $container.find('.qti-interaction.qti-textEntryInteraction');
                assert.equal(getTooltipContent($input), 'This is not a valid answer', 'the error message is correct');
                assert.ok(getTooltip($input).is(':visible'), 'the error tooltip is visible after an invalid response');
                QUnit.start();
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('Pattern constraint - correct', function (assert) {

        var $container = $('#' + fixtureContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', textEntryPatternConstrainedData)
            .on('render', function () {
                var $input = $container.find('.qti-interaction.qti-textEntryInteraction');

                assert.equal($input.length, 1, 'the container contains a text entry interaction .qti-textEntryInteraction');

                $input.val('');
                $input.focus();
                assert.equal(getTooltipContent($input), undefined);

                $input.val('PARIS');
                $input.keyup();

            }).on('responsechange', function(state){
                var $input = $container.find('.qti-interaction.qti-textEntryInteraction');
                assert.equal(getTooltip($input), undefined, 'the error tooltip is hidden after a correct response');
                console.log(JSON.stringify(state));
                QUnit.start();
            })
            .init()
            .render($container);
    });

    QUnit.asyncTest('set/get response', function(assert){

        var $container = $('#' + fixtureContainerId);
        var state = {"RESPONSE":{response:{"base":{"string":"PARIS"}}}};

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        runner = qtiItemRunner('qti', textEntryData)
            .on('render', function(){

                var $input = $container.find('.qti-interaction.qti-textEntryInteraction');

                assert.equal($input.length, 1, 'the container contains a text entry interaction .qti-textEntryInteraction');
                assert.equal($input.val(), '', 'the text input is initially empty');

                this.setState(state);

                assert.equal($input.val(), 'PARIS', 'the text input has been correctly set');

                var retrivedState = this.getState(state);
                console.log(retrivedState);

                assert.deepEqual(retrivedState, state, 'get state is correct');
                QUnit.start();

            })
            .init()
            .render($container);
    });

    module('Visual Test');

    QUnit.asyncTest('Display and play', function (assert) {
        QUnit.expect(3);

        var $container = $('#' + outsideContainerId);

        assert.equal($container.length, 1, 'the item container exists');
        assert.equal($container.children().length, 0, 'the container has no children');

        qtiItemRunner('qti', textEntryLengthConstrainedData)
            .on('render', function () {

                assert.equal($container.find('.qti-interaction.qti-textEntryInteraction').length, 1, 'the container contains a text entry interaction .qti-textEntryInteraction');

                QUnit.start();
            })
            .init()
            .render($container);
    });
});

