'use strict';
(function ($) {
    $.validator.addMethod("validateCity", function(value, element) {
        return this.optional(element) || value != 'default' ;
    }, " Please City Option")

    $.validator.addMethod(
        "phoneNumber",
        function(value, element) {
            console.log('value', value, element, element);
            return false;
        }
    );
    $.validator.addMethod(
        "userNameStrong",
        function(value, element) {
            console.log('value', value, element, element);
            return false;
        }
    );
    $.validator.passwordStrong = function( value, element ) {
        return this.optional( element ) || /[a-z]+@[a-z]+\.[a-z]+/.test( value );
    }

    $.validator.methods.email = function( value, element ) {
        return this.optional( element ) || /[a-z]+@[a-z]+\.[a-z]+/.test( value );
    }

});