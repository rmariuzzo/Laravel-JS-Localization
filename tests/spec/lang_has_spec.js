'use strict';

var util = require('util');
var Lang = require('../../js/lang.js');
var messages = require('./data/messages');

Lang.setMessages(messages);

describe('The Lang.has() method', function() {

    it('should exists', function() {
        expect(Lang.has).toBeDefined();
    });

    it('should be a function', function() {
        expect(typeof Lang.has).toBe('function');
    });

    it('should return false when the given key is no defined', function() {
        expect(Lang.has('foo.bar')).toBe(false);
        expect(Lang.has(null)).toBe(false);
    });

    it('should return true when the given key is defined', function() {
        expect(Lang.has('messages.home')).toBe(true);
        expect(Lang.has('validation.accepted')).toBe(true);
    });

});
