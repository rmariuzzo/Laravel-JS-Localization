'use strict';

(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['b'], factory);
    } else {
        // Browser globals
        root.Lang = factory(root.b);
    }
}(this, function() {

    var Lang = function() {};

    Lang.prototype.setMessages = function(json) {
        this.messages = json;
    };

    Lang.prototype.get = function(key, placeholder) {
        if (!this.has(key)) {
            return key;
        }
        return this.getLine(key, placeholder);
    };

    return Lang;

}));
