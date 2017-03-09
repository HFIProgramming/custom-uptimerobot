<?php
if (empty($_GET['monit'])) {
	Header("Location: /");
	exit;
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=11" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="robots" content="all,follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NoticeBoard Status -
        Details
    </title>
    <!-- angular.min.js -->
    <base href="https://status.hfi.me" />
    <script type="text/javascript" src="/js/angular.min.js"></script>
    <script src="/js/jquery-1.10.1.min.js"></script>
    <script src="/js/bootstrap.js"></script>
    <script src="/js/jquery.jqplot.js"></script>
    <script src="/js/lib/jquery.flot.min.js"></script>
    <script src="/js/lib/jquery.flot.time.js"></script>
    <script src="/js/lib/jquery.flot.resize.js"></script>
    <script src="/js/lib/jquery.qtip.min.js"></script>
    <script src="/js/lib/angular-flot.js"></script>
    <script src="/js/lib/jquery.truncate.min.js"></script>
    <link rel="stylesheet" href="/css/jquery.qtip.min.css" />
    <link rel="stylesheet" href="/css/default.css" />
    <script src="/js/default.js"></script>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" href="/css/main.css" />
    <script src="/js/ui-chart.js"></script>
    <script src="/js/controllers.js"></script>
    <script src="/js/services.js"></script>
    <script src="/js/lib/bootbox.min.js"></script>
    <script type="text/javascript">
    var monitorID = "<?php echo $_GET['monit']?>";
    (function() {})(monitorID);
  </script>
    <script src="/js/app.js"></script>
    <script>window.flushHitokoto=function(){var hjs=document.createElement("script");hjs.setAttribute("src","https://api.lwl12.com/hitokoto/main/get?encode=json");document.body.appendChild(hjs)};setTimeout(window.flushHitokoto,1000);window.echokoto=function(result){document.getElementsByClassName("hitokoto")[0].innerHTML=result.hitokoto};</script>
</head>

<body ng-app="urStatusPage" ng-controller="MonitorPageCtrl">

    <!--Header start-->
    <header id="header">
        <div class="container">

            <img ng-src="https://userfiles.uptimerobot.com/img/{{ psp.logo }}" alt="logo" class="logo" ng-if="pspDataLoaded && psp.logo !== null"
            />
            <h2 class="nologo positive" ng-if="pspDataLoaded && psp.logo === null">{{ psp.name }}</h2>

            <a href="/" class="back">&lt;-- Back</a>
        </div>
    </header>
    <!--Header ebd-->

    <!--Main Content Start-->
    <div id="content">
        <div id="main" class="container">
            <div class="panel-group" id="announcement" role="tablist" aria-multiselectable="true" ng-if="psp.hasActiveAnnounce">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h3>({{ psp.activeAnnouncements }}) Announcements</h3>
                        <h4 ng-if="!announcementsOpened">
                            <b>{{ psp.firstAnnounceTitle }}</b>: {{ psp.firstAnnounceDescInline }}
                        </h4>
                        <a ng-if="psp.announcements.length > 0" ng-click="announcementsPanelToggled()" role="button" data-toggle="collapse" data-parent="#announcement"
                            href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" class="arrow">
                        </a>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <ul>
                                <li ng-repeat="announce in psp.announcements">
                                    <span>{{ announce.title }}</span>
                                    <p>{{ announce.desc }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


            <header class="monitor-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h2 class="success">{{ monitor.friendly_name }}</h2>
                        <h4>({{ monitor.typeStr }} - Checked every {{ monitor.intervalMin }} mins)</h4>
                        <strong>Uptime: <span>Last 7 Days</span></strong>
                    </div>
                    <div class="col-sm-6 right-align">
                        <!--a href="#" class="btn btn-primary btn-round">subscribe to all monitors</a-->
                        <div class="current-status">
                            <h3>Current Status</h3>
                            <span class="bullet" ng-class="{
                      'bullet-success': lastLogs[0].statusStr !== 'down' && monitor.status !== 8 && monitor.status !== 9,
                      'bullet-danger': lastLogs[0].statusStr === 'down' || monitor.status === 8 || monitor.status === 9
					          }"></span>
                            <strong>{{ lastLogs[0].statusStr }}<span ng-if="hideRefreshRemaining !== true">Refreshing in {{ refreshRemaining }} secs</span></strong>
                        </div>
                    </div>
                </div>
            </header>

            <div class="table-container">
                <table class="table monitor-table">
                    <tbody>
                        <tr>
                            <td align="center" ng-repeat="day in days">{{ day }}</td>
                        </tr>
                        <tr>
                            <td ng-repeat="range in monitor.customuptimeranges">
                                <span class="label label-{{ range.label }}">{{ range.ratio }}%</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h4>Response Time</h4>

            <flot dataset="flotData" options="flotOptions" height="194px"></flot>

            <h4>Latest Events for {{ monitor.friendly_name }}</h4>

            <div class="events">
                <div class="row">
                    <div class="col-xs-3">
                        <h4>Event</h4>
                        <ul>
                            <li ng-repeat="log in lastLogs">
                                <span class="label label-{{ log.label }}">{{ log.statusStr }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="col-xs-3">
                        <h4>Date-Time</h4>
                        <ul>
                            <li ng-repeat="log in lastLogs">{{ log.dateTimeStr }}</li>
                        </ul>
                    </div>

                    <div class="col-xs-3">
                        <h4>Reason</h4>
                        <ul>
                            <li ng-repeat="log in lastLogs">
                                <span class="{{ log.label }}" title="{{ log.reasonTitle }}">

								{{ log.reasonStr }}

							</span>
                            </li>
                        </ul>
                    </div>

                    <div class="col-xs-3">
                        <h4>Duration</h4>
                        <ul>
                            <li ng-repeat="log in lastLogs">{{ log.durationStr }}</li>
                        </ul>
                    </div>

                </div>

                <div class="row top-buffer">
                    <div class="center">
                        <button href="#" class="btn" ng-class="{'disabled': allLogs.length === 0}" ng-click="loadLogs()">Load More Logs</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--Main Content END-->

    <!--Footer start-->
    <footer id="footer">
        <div class="container" ng-if="showURLinks">
            <div class="container-left" style="float: left;line-height: 35px;">
                <a href="http://hfiprogramming.club"><span class="provided">HFIProgramming</span></a><span><a href="https://lwl.moe/" target="_blank">本页面衍生自 LWL的自由天空 旗下监控页</a> | </span><span class="hitokoto" id="hitokoto">Loading...</span>
            </div>
            <span class="provided">Provided by:</span>
            <a href="https://uptimerobot.com" rel="nofollow"><img src="/images/uptime-logo.png" alt="logo" /></a>
        </div>
    </footer>
    <!--Footer END-->
    <div id="loader-overlay" class="fadeMe">

        <div class='loader-container'>
            <div class='loader'>
                <div class='loader--dot'></div>
                <div class='loader--dot'></div>
                <div class='loader--dot'></div>
                <div class='loader--dot'></div>
                <div class='loader--dot'></div>
                <div class='loader--dot'></div>
                <div class='loader--text'></div>
            </div>
        </div>

    </div>

</body>

</html>