/* global jQuery */
(function wpCxtInstance($) {
  var wpCxt = {

    /**
     * Register event handlers
     */
    initialize() {
      var self = this;
      // bind to the chosen ready and change events to refresh the multiselect visibility
      $('.chosen-select.display-terms-parent').on('chosen:ready change', self.setMultiSelectState);
      self.initializeChosen();
    },

    /**
     * Set the visbility of the multiselect chosen boxes
     * based on whether or not the taxonomy is customizable
     */
    setMultiSelectState(event, params) {
      // if the select box has defined a related term selector
      if (typeof event.target.dataset.termSelector !== 'undefined') {
        var childSelector = '.' + event.target.dataset.termSelector;
        if (
          params.selected === 'some'
          || (typeof event.target.value !== 'undefined' && event.target.value === 'some')
        ) {
          $(childSelector).parents('tr').show();
        } else {
          $(childSelector).parents('tr').hide();
        }
      }
    },

    /**
     * Initialize all of the chosen selects
     */
    initializeChosen() {
      $('.chosen-select').chosen({
        width: '225px',
      });
    },

  };

  $(function() {
    wpCxt.initialize();
  });
}(jQuery));
