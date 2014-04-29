'use strict';

var util = require('util');
var Lang = require('../../js/lang.js');
var messages = require('./data/messages');

Lang.setMessages(messages);

describe('The Lang\'s locale methods', function() {

    it('should have a getLocale', function() {
        expect(Lang.getLocale).toBeDefined();
        expect(typeof Lang.getLocale).toBe('function');
    });

    it('should have a setLocale', function() {
        expect(Lang.setLocale).toBeDefined();
        expect(typeof Lang.setLocale).toBe('function');
    });

    it('should return the default locale', function() {
        expect(Lang.getLocale()).toBe('en');
    });

    it('should return the locale specified', function() {
        Lang.setLocale('es');
        expect(Lang.getLocale()).toBe('es');
    });

    it('should affect messages', function() {
        Lang.setLocale('es');
        var es = Lang.get('messages.home');
        Lang.setLocale('en');
        var en = Lang.get('messages.home');
        expect(es).not.toBe(en);
    });

});
