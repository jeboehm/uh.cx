(function() {
  var Clipboard = require('clipboard');
  new Clipboard('.clipboardbutton');

  $('.clipboardbutton').each(function(i, element) {
    var $this = $(element);
    $this
      .popover({
        placement: 'top',
        content: 'The link has been copied to your clipboard.',
        trigger: 'click'
      })
      .on('shown.bs.popover', function() {
        window.setTimeout(function() {
          $this.popover('hide');
        }, 2000);
      });
  });
})();
