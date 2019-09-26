define([
    'jquery',
    'underscore',
    'ko',
    'uiComponent'
], function ($, _, ko, Component) {
    'use strict';
    
    return Component.extend({
        defaults: {
            template:      'Mirasvit_SeoContent/component/template-syntax',
            childSelector: '.mst-seo-content__global-template-syntax input,' +
                           '.mst-seo-content__global-template-syntax textarea',
            wrapperClass:  'mst-seo-content__component-template-syntax'
        },
        
        initialize: function () {
            this._super();
            
            var templatesTimer = setInterval(function () {
                if ($(this.childSelector).length) {
                    clearInterval(templatesTimer);
                    
                    this.attachEvents();
                }
            }.bind(this), 250);
            
            return this;
        },
        
        attachEvents: function () {
            var html = $('.mst-seo-content__component-template-syntax-wrapper').html();
            var div = $('<div/>').addClass(this.wrapperClass).html(html);
            
            _.each($(this.childSelector), function (item) {
                $(item).on('focus', function () {
                    $(item).parent().append(div);
                }.bind(this));
                
                $(item).on('blur', function () {
                    $('.' + this.wrapperClass).remove();
                }.bind(this))
            }.bind(this));
        }
    });
});
