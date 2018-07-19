(function ($) {

    $.fn.jalert = function (options) {

        //holds parent element, all alert html will be appended to this.
        var self = this;

        // setup options
        var defaultOptions = {
            type: 'success',
            useTitles: false
        };

        var tmpl = '<div class="alert alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span><%= msg %></span></div>';

        var $alert = $('<div class="alert alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><span></span></div>'); //holds reference to actual alert object, this is checked if it already exists since the alert dismissal will actually remove the alert.



        var settings = $.extend({}, defaultOptions, options);

        var titles = {
            success: 'Success!',
            error: 'Error!',
            info: 'Info!',
            warning: 'Warning!'
        }


        var setHtml = function (_html) {
            $alert.find('> span').html(_html);
            //now append alert to this wrapper.
            //console.log('self', self);
            //console.log('$alert', $alert);
            //if(self.length)
            self.append($alert);
            //self.find('> span').html(_html);
        };

        var setType = function (_type) {
            //fix for previous versions of template...used to be 'error' class, now 'danger', so just replace error with danger so i can still call 'jalert.error()'
            var type = (_type == 'error') ? 'danger' : _type;

            $alert.addClass('alert-' + type);
            //self.removeClass('alert-danger');

            //self.addClass('alert alert-' + type);
            //self.find('strong').html(titles[_type]);
        };

        this.error = function (_html) {
            setHtml(_html);
            setType('error');
            return self;
        };

        this.success = function (_html) {
            setHtml(_html);
            setType('success');
            return self;
        };

        this.info = function (_html) {
            setHtml(_html);
            setType('info');
            return self;
        };

        this.warning = function (_html) {
            setHtml(_html);
            setType('warning');
            return self;
        };

        this.hide = function (speed) {
            self.slideUp(speed);
            return self;
        }

        this.show = function () {

            self.css('display', 'none');
            self.removeClass('hide fade');
            self.slideDown();
            //self.addClass('in').removeClass('hide');
            return self;
        }

        this.autohide = function (_delay) {
            setTimeout(function () {
                self.slideUp();
            }, (_delay) ? _delay : 5000);
        }

        // PUBLIC functions



        //return jquery object
        return this;
    }
})(jQuery);