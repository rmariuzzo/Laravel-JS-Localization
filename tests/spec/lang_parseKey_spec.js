'use strict';

var util = require('util');
var Lang = new(require('../../js/lang.js'))();
var messages = require('./data/messages');

Lang.setMessages(messages);

describe('The Lang.parseKey() method', function() {

    it('should exists', function() {
        expect(Lang.parseKey).toBeDefined();
    });

    it('should be a function', function() {
        expect(typeof Lang.parseKey).toBe('function');
    });

    it('should parse keys correctly', function() {
        expect(Lang.parseKey(null)).toBe(null);
        expect(Lang.parseKey('foo')).toEqual({
            main: 'en',
            sub: 'foo'
        });
        expect(Lang.parseKey('foo.bar')).toEqual({
            main: 'en.foo',
            sub: 'bar'
        });
    });

});
