'use strict';

var util = require('util');
var Lang = require('../../src/js/lang.js');
var messages = require('./data/messages');

Lang.setMessages(messages);

describe('The Lang.choice() method', function() {

    it('should exists', function() {
        expect(Lang.choice).toBeDefined();
    });

    it('should be a function', function() {
        expect(typeof Lang.choice).toBe('function');
    });

    it('should return the passed key when not found', function() {
        expect(Lang.choice('foo.bar', 1)).toBe('foo.bar');
        expect(Lang.choice(null, 1)).toBe(null);
    });

    it('should return the expected message', function() {
        expect(Lang.choice('messages.plural', 1)).toBe('one apple');
        expect(Lang.choice('messages.plural', 10)).toBe('a million apples');
    });

    it('should count correctly', function() {
        expect(Lang.choice('messages.count', 0)).toBe('There are none');
        expect(Lang.choice('messages.count', 1)).toBe('There are some');
        expect(Lang.choice('messages.count', 10)).toBe('There are some');
        expect(Lang.choice('messages.count', 19)).toBe('There are some');
        expect(Lang.choice('messages.count', 19)).toBe('There are some');
        expect(Lang.choice('messages.count', 20)).toBe('There are many');
        expect(Lang.choice('messages.count', 30)).toBe('There are many');
    });

    it('should return the expected message with replacements', function() {
        expect(Lang.choice('validation.accepted', 1)).toBe('The :attribute must be accepted.');
        expect(Lang.choice('validation.accepted', 1, {
            'attribute': 'foo'
        })).toBe('The foo must be accepted.');
    });

});
