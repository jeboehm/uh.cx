(function() {
  $('#bookmarklet')
    .on({
      'click': (function(e) {
        e.preventDefault();
      })
    })
    .popover({ placement: 'bottom' });
})();
