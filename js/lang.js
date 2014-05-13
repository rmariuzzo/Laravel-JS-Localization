/*!
 *  Lang.js for Laravel localization in JavaScript.
 *
 *  @version 1.0.0
 *  @license MIT
 *  @site    https://github.com/rmariuzzo/Laravel-JS-Localization
 *  @author  rmariuzzo
 */

'use strict';

(function(root, factory) {

    if (typeof define === 'function' && define.amd) {
        // AMD support.
        define([], factory);
    } else if (typeof exports === 'object') {
        // NodeJS support.
        module.exports = new (factory())();
    } else {
        // Browser global support.
        root.Lang = new (factory())();
    }

}(this, function() {

    // Default options //

    var defaults = {
        defaultLocale: 'en' /** The default locale if not set. */
    }

    // Constructor //

    var Lang = function(options) {
        options = options || {};
        this.defaultLocale = options.defaultLocale || defaults.defaultLocale;
    };

    // Methods //

    /**
     * Set messages source.
     *
     * @param messages {object} The messages source.
     *
     * @return void
     */
    Lang.prototype.setMessages = function(messages) {
        this.messages = messages;
    };

    /**
     * Returns a translation message.
     *
     * @param key {string} The key of the message.
     * @param replacements {object} The replacements to be done in the message.
     *
     * @return {string} The translation message, if not found the given key.
     */
    Lang.prototype.get = function(key, replacements) {
        if (typeof key !== 'string' || !this.has(key)) {
            return key;
        }
        return this.getMessage(key, replacements);
    };

    /**
     * Returns true if the key is defined on the messages source.
     *
     * @param key {string} The key of the message.
     *
     * @return {boolean} true if the given key is defined on the messages source, otherwise false.
     */
    Lang.prototype.has = function(key) {
        if (typeof key !== 'string' || !this.messages) {
            return false;
        }
        var message = this._getMessage(key);

        return message !== undefined && typeof message === "string";
    };

    /**
     * Set the current locale.
     *
     * @param locale {string} The locale to set.
     *
     * @return void
     */
    Lang.prototype.setLocale = function(locale) {
        this.locale = locale;
    };

    /**
     * Get the current locale.
     *
     * @return {string} The current locale.
     */
    Lang.prototype.getLocale = function() {
        return this.locale || this.defaultLocale;
    };

    /**
     * Parse a message key into components.
     *
     * @param key {string} The message key to parse.
     *
     * @return {object} A key object with main and sub properties.
     */
    Lang.prototype.parseKey = function(key) {
        if (typeof key !== 'string') {
            return null;
        }
        var segments = key.split('.');
        var main = this.getLocale() + '\\' + segments.shift();

        return {
            main: main,
            sub: segments
        };
    };

    /**
     * Returns a translation message. Use `Lang.get()` method instead, this methods assumes the key exists.
     *
     * @param key {string} The key of the message.
     * @param replacements {object} The replacements to be done in the message.
     *
     * @return {string} The translation message for the given key.
     */
    Lang.prototype.getMessage = function(key, replacements) {
        var message = this._getMessage(key);

        for (var replace in replacements) {
            message = message.split(':' + replace).join(replacements[replace]);
        }

        return message;
    };
    
     /**
     * Get nested message.
     *
     * @param key
     * @returns {*}
     * @private
     */
    Lang.prototype._getMessage = function (key) {
        key = this.parseKey(key);

        var length = key.sub.length,
            message = this.messages[key.main],
            i;

        for ( i = 0; i < length; i++ ) {
            message = message[key.sub[i]];
        }

        return message;
    };

    return Lang;

}));
