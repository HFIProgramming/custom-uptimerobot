function askAuth($http, getDataFromRemote) {
  bootbox.prompt({
    title: "Please enter the password:",
    inputType: 'password',
    callback: function (result) {
      if (result === null)
        return;

      console.log('result:', result);
      $http.post("/api/?function=auth", { pass: result })
        .then(success)
        .catch(fail);
      function success(data) {
        console.log('success', data);
        getDataFromRemote();
      }
      function fail(data) {
        // password is wrong
        // continue asking password to the user until he/she enter
        // correct password text
        if (data.status === 401)
          return askAuth($http, getDataFromRemote);
        if (data.status === 404)
          return bootbox.alert('Public status page not found.');
        console.log('fail', data);
        bootbox.alert('Internal server error. Please try again.');
        window.setTimeout(function () {
          bootbox.hideAll();
          location.reload(true);
        }, 3000);
      }
    }
  })
}

angular
  .module('urStatusPage.controllers', [])
  .controller('StatusPageCtrl', ['$scope', '$http', '$timeout', 'Repeater', function ($scope, $http, $timeout, Repeater) {
    var errmsg = 'An error occured while getting ';
    errmsg += 'status page\'s data. ';
    errmsg += 'Please try again later.';

    var MONITORS_PER_PAGE = 20;

    var firstRun = true;

    // pageID defined at html, globally
    //$scope.pageID = pageID;
    $scope.pageNumber = pageNumber;
    $scope.showURLinks = true;

    // functions
    $scope.announcementsPanelToggled = announcementsPanelToggled;

    getDataFromRemote();

    function getDataFromRemote() {
      $http
        .get("api/?function=status" + '&pagenumber=' + pageNumber + '&sorttype=' + sortType)
        .then(processRemoteData)
        .catch(function (data) {
          console.log('error:', data);
          if (data.status === 401) {
            // not authenticated to see this page
            // ask for password and try again
            askAuth($http, getDataFromRemote);
            return;
          }
          if (data.status === 404)
            return bootbox.alert('Page not found.');
          //alert(errmsg);
          bootbox.alert('Internal server error. Please try again.');
          window.setTimeout(function () {
            bootbox.hideAll();
            location.reload(true);
          }, 3000);
        });
    }

    function processRemoteData(resp) {
      $scope.psp = resp.data.psp;
      $scope.days = resp.data.days;
      $scope.pspDataLoaded = true;
      $scope.showURLinks = $scope.psp.hide_ur_links !== true;

      // formatMonitors($scope.psp);

      $scope.psp.montypes = [];
      for (var x in $scope.psp.monitors) {
        var namesplit = $scope.psp.monitors[x].friendly_name.split("/");
        var montype = namesplit[1];
        $scope.psp.montypes[namesplit[2]] = montype;
        $scope.psp.monitors[x].friendly_name = namesplit[0];
        if (typeof $scope.psp[montype] == "undefined") {
          $scope.psp[montype] = {};
          $scope.psp[montype].monitors = [];
        }
        $scope.psp[montype].monitors.push($scope.psp.monitors[x]);
        $scope.psp[montype].monitors.sort(function (a, b) {
          return a.friendly_name.length - b.friendly_name.length;
        });
      }

      $('title').text($scope.psp.name);

      $scope.latestDownTimeStr = resp.data.psp.latestDownTimeStr.replace(/\/.*\)/, "");
      $scope.pspStats = resp.data.psp.pspStats;

      $scope.psp.monitors.forEach(function (mon) {
        mon.statusPageURL = noPrefix
          ? '/' + mon.id
          : '/' + 'monit' + '?monit=' + mon.id;
      });

      // truncate first announcement's title
      setTimeout(function () {
        $('#headingOne h4').truncate();
      }, 100);

      createPagerObject();

      var monitors = resp.data.psp.monitors;
      for (var i = 0; i < monitors.length; i++) {
        if (monitors[i].allLogs[0].statusStr === 'down') {
          $('tbody>.ng-scope').eq(i).addClass('down');
        }
      }

      if (firstRun) {
        firstRun = false;
        var x = 1;

        function r() {
          x -= 0.1;
          $('#loader-overlay').css('opacity', x);

          if (x <= 0)
            return $('#loader-overlay').hide(0);

          setTimeout(r, 20);
        }

        r();

        $scope.hideRefreshRemaining = true;
        $scope.hideRefreshRemaining = false;
        Repeater.start($scope, 'refreshRemaining', 60, getDataFromRemote);

        function fowidth() {
          for (var j = 0; j < $('.table').eq(0).find('thead th').length; j++) {
            var maxWidth = 0;
            for (var i = 0; i < $('.table').length; i++) {
              var width = $('.table').eq(i).find('td:nth-child(' + (j + 1) + ')').width();
              if (width > maxWidth) {
                maxWidth = width;
              }
            }
            $('.table td:nth-child(' + (j + 1) + ')').width(maxWidth);
          }
        }

        $(document).ready(function () {
          $(window).resize(fowidth);
        });

        setTimeout(fowidth, 50);

      }
    }

    function createPagerObject() {
      // example pagination object
      $scope.pagination = {
        prevHref: '#',
        nextHref: '#',
        nextActive: true,
        prevActive: true,
        pages: [1, 2, 3, 4]
      };

      var pagination = $scope.psp.pagination;
      pagination = {
        total: $scope.psp.monitorCount,
        curr: pageNumber
      };

      // get total monitor count
      var total = pagination.total;
      // get current page
      var curr = pagination.curr;
      // calculate total pages
      var totalPages = Math.ceil(total / MONITORS_PER_PAGE);
      // calculate start page
      var start = curr <= 5
        ? 1
        : curr - 5;
      // calculate end page
      var end = totalPages > curr + 5
        ? curr + 5
        : totalPages;
      // set prevActive
      var prevActive = curr > start;
      // set prevHref
      var prevHref = prevActive
        ? '?page=' + (curr - 1)
        : '#';
      // set nextActive
      var nextActive = curr < totalPages;
      // set nextHref
      var nextHref = nextActive
        ? '?page=' + (curr + 1)
        : '#';
      // set pages array
      var pages = [];
      for (var i = start; i < end + 1; i++)
        pages.push(i);

      $scope.pagination = {
        prevHref: prevHref,
        nextHref: nextHref,
        prevActive: prevActive,
        nextActive: nextActive,
        pages: pages,
        hidePagination: totalPages === 1
      };
    }

    function announcementsPanelToggled() {
      var ms = 10;

      if ($scope.announcementsOpened)
        ms = 250;

      $timeout(function () {
        $scope.announcementsOpened = !$scope.announcementsOpened;
      }, ms);
    }

  }])
  .controller('MonitorPageCtrl', ['$scope', '$http', '$timeout', 'Repeater', function ($scope, $http, $timeout, Repeater) {
    // pageID and monitorID defined at html, globally
    var url = 'https://status.hfi.me/api/?function=monit&monit=' + monitorID;

    var firstRun = true;

    // functions
    $scope.announcementsPanelToggled = announcementsPanelToggled;
    $scope.loadLogs = loadLogs;
    $scope.showURLinks = true;

    $scope.lastLogs = [];
    $scope.allLogs = [];
    console.log('$scope.allLogs:', $scope.allLogs);

    init();

    function init() {
      $scope.announcementsOpened = false;
      initializeChart();
      getDataFromRemote();
      $scope.hideRefreshRemaining = true;
      $scope.hideRefreshRemaining = false;
      Repeater.start($scope, 'refreshRemaining', 60, getDataFromRemote);
    }

    function initializeChart() {
      $scope.flotData = [{
        data: [],
        yaxis: 1,
        xaxis: {
          mode: "time"
        },
        label: "Milliseconds"
      }];

      $scope.flotOptions = {
        colors: ["#edc240", "#5EB95E"],
        legend: {
          show: true,
          noColumns: 2, // number of colums in legend table
          labelFormatter: null, // fn: string -> string
          labelBoxBorderColor: false,
          container: null, // container (as jQuery object) to put legend in, null means default on top of graph
          margin: 8,
          backgroundColor: false
        },
        series: {
          lines: {
            show: true,
            lineWidth: 4,
            fill: true
          },
          points: {
            show: false,
            fillColor: "rgba(0,0,0,0.35)",
            radius: 3.5,
            lineWidth: 1.5
          },
          grow: {
            active: false
          }
        },
        xaxis: {
          mode: "time",
          font: {
            weight: "bold"
          },
          color: "#D6D8DB",
          tickColor: "rgba(237,194,64,0.25)",
          //min: "1446128276000",
          //max: "1446214676000",
          tickLength: 5
        },
        selection: {
          mode: "x"
        },
        grid: {
          color: "#D6D8DB",
          tickColor: "rgba(255,255,255,0.05)",
          borderWidth: 0,
          clickable: true,
          hoverable: true
        }
      };
    }

    function generateChartData(responseTimes) {
      var arr = [];

      responseTimes.forEach(function (responseTime, i) {
        arr.push([
          responseTime.datetime * 1000,
          responseTime.value
        ]);
      });

      // sort from small to high
      // first element is alert log's datetime value
      //  in milliseconds
      // second element is alert log's value
      arr.sort(function (arrA, arrB) {
        if (arrA[0] === arrB[0])
          return 0;

        if (arrA[0] > arrB[0])
          return 1;

        return -1; // arrA[0] < arrB[0]
      });

      return arr;
    }

    function bindQtip() {
      var elem = $('flot div');
      // Create a tooltip on our chart
      elem.qtip({
        prerender: true,
        content: 'Loading...', // Use a loading message primarily
        position: {
          viewport: $(window), // Keep it visible within the window if possible
          target: 'mouse', // Position it in relation to the mouse
          adjust: {
            x: 7
          } // ...but adjust it a bit so it doesn't overlap it.
        },
        show: false, // We'll show it programatically, so no show event is needed
        style: {
          classes: 'ui-tooltip-shadow ui-tooltip-tipsy',
          tip: false // Remove the default tip.
        }
      });

      // Bind the plot hover
      elem.bind("plothover", function (event, coords, item) {
        // Grab the API reference
        var self = $(this),
          api = $(this).qtip(),
          previousPoint, content,

          // Setup a visually pleasing rounding function
          round = function (x) {
            return Math.round(x * 1000) / 1000;
          };

        // If we weren't passed the item object, hide the tooltip and remove cached point data
        if (!item) {
          api.cache.point = false;
          return api.hide(event);
        }

        // Proceed only if the data point has changed
        previousPoint = api.cache.point;
        if (previousPoint !== item.dataIndex) {
          // Update the cached point data
          api.cache.point = item.dataIndex;

          // Setup new content
          content = round(item.datapoint[1]) + ' milliseconds';
          //content = round(item.datapoint[1]) + ' miliseconds ' + moment(item.datapoint[0]).format("MMMM Do YYYY, HH:mm:ss");

          // Update the tooltip content
          api.set('content.text', content);

          // Make sure we don't get problems with animations
          api.elements.tooltip.stop(1, 1);

          // Show the tooltip, passing the coordinates
          api.show(coords);
        }
      });
    }

    function getDataFromRemote() {
      $http.get(url)
        .then(processRemoteData)
        .catch(function (data) {
          console.log('error:', data);
          if (data.status === 401)
            return askAuth($http, getDataFromRemote);
          if (data.status === 404)
            return bootbox.alert('Page not found.');
          bootbox.alert('Internal server error. Please try again.');
          window.setTimeout(function () {
            bootbox.hideAll();
            location.reload(true);
          }, 3000);
        });
    }

    function processRemoteData(resp) {
      $scope.psp = resp.data.psp;
      $scope.days = resp.data.days;
      $scope.pspDataLoaded = true;
      $scope.showURLinks = $scope.psp.hide_ur_links !== true;

      // formatMonitors($scope.psp);

      $scope.psp.monitors[0].friendly_name = $scope.psp.monitors[0].friendly_name.split("/")[0];
      var monitorFriendlyName = $scope.psp.monitors[0].friendly_name;
      var pspFriendlyName = $scope.psp.name;

      $('title').text(monitorFriendlyName + ' - ' + pspFriendlyName);

      $scope.latestDownTimeStr = resp.data.psp.latestDownTimeStr;
      $scope.pspStats = resp.data.psp.pspStats;
      $scope.monitor = $scope.psp.monitors[0];

      $scope.flotData[0].data = generateChartData($scope.monitor.response_times);
      bindQtip();

      // truncate firs announcement's title
      setTimeout(function () {
        $('#headingOne h4').truncate();
      }, 100);

      if (firstRun) {
        var x = 1;
        firstRun = false;

        $scope.allLogs = $scope.monitor.allLogs;
        loadLogs();

        r();

        function r() {
          x -= 0.1;
          $('#loader-overlay').css('opacity', x);

          if (x <= 0)
            return $('#loader-overlay').hide(0);

          setTimeout(r, 20);
        }
      }

    }

    function announcementsPanelToggled() {
      var ms = 10;

      if ($scope.announcementsOpened)
        ms = 250;

      $timeout(function () {
        $scope.announcementsOpened = !$scope.announcementsOpened;
      }, ms);
    }

    function loadLogs() {
      for (var i = 0; i < 5 && $scope.allLogs.length > 0; i++)
        $scope.lastLogs.push($scope.allLogs.shift());
    }

  }]);
