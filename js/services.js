angular
.module('urStatusPage.services', [])
.service('Repeater', function ($timeout) {
  this.start = start;

  function start(scope, variableName, repeatSecs, cb) {
    var remaining = repeatSecs;
    var reloadCount = 0;

    schedule();

    function schedule() {
      assign();

      $timeout(routine, 1000);
    }

    function assign() {
      scope[variableName] = remaining;
    }

    function routine() {
      remaining--;

      if (remaining <= 0) {
        remaining = repeatSecs;

        reloadCount++;

        // refresh the tab
        if (reloadCount >= 60) {
          window.location.reload();
        }

        cb();
      }

      schedule();
    }
  }
});
