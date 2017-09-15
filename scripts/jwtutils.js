(function (window) {

    /**
     * JSON WEB Token Utility
     */

    window.JWT = function(){
        var self = this;
        
        this.parse = function (token) {
            if (token == null || token == 'null' || !token || token == false) { return false; }
            var base64Url = token.split('.')[1];
            var base64 = base64Url.replace('-', '+').replace('_', '/');
            return JSON.parse(window.atob(base64));
        }

        return this;
    }();

})(window);