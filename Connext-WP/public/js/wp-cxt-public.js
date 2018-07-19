/* global jQuery Connext WP_CXT */
(function wpCxtInstance($) {
  var wpCxt = {

    initialize: function() {
      Connext.init({
        siteCode: WP_CXT.siteCode,
        configCode: WP_CXT.configCode,
        debug: (WP_CXT.debug === 'false') ? false : true,
        settingsKey: WP_CXT.settingsKey,
        environment: WP_CXT.environment,
        attr: WP_CXT.attr,
        silentmode: (WP_CXT.silentMode === 'false') ? false : true,
        clientCode: WP_CXT.clientCode
      });
    }

  };
  $(function() {
    wpCxt.initialize();
  });
}(jQuery));
