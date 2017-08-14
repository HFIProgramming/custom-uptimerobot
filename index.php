<?php
$info = include_once "./config/info.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=11"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="robots" content="all,follow"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico"/>
    <title><?php echo $info['title'] ?></title>
    <base href="<?php echo $info['base_url'] ?>"/>
    <!-- lib js -->
    <script src="https://cdnjs.cat.net/ajax/libs/angular.js/1.5.0-rc.0/angular.min.js"></script>
    <script src="https://cdnjs.cat.net/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    <script src="https://cdnjs.cat.net/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script> <!--min-->
    <script src="https://cdnjs.cat.net/ajax/libs/jqPlot/1.0.8/jquery.jqplot.min.js"></script>
    <script src="//cdn.bootcss.com/flot/0.8.3/jquery.flot.min.js"></script>
    <script src="https://cdnjs.cat.net/ajax/libs/flot/0.8.3/jquery.flot.time.min.js"></script>
    <script src="https://cdnjs.cat.net/ajax/libs/flot/0.8.3/jquery.flot.resize.min.js"></script>
    <script src="https://cdnjs.cat.net/ajax/libs/qtip2/2.2.1/jquery.qtip.min.js"></script>
    <script src="https://cdnjs.cat.net/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
    <!-- lib css -->
    <link href="https://cdnjs.cat.net/ajax/libs/qtip2/2.2.0/jquery.qtip.min.css" rel="stylesheet">
    <!--Local Source-->
    <script src="/public/js/angular-flot.js"></script>
    <script src="/public/js/jquery.truncate.min.js"></script>
    <script src="/public/js/default.js"></script>
    <script src="/public/js/ui-chart.js"></script>
    <script src="/public/js/controllers.js"></script>
    <script src="/public/js/services.js"></script>
    <script src="/public/js/app.js"></script>
    <link rel="stylesheet" href="/public/css/default.css"/>
    <link rel="stylesheet" href="/public/css/main.css"/>

    <script type="text/javascript">
        var noPrefix = 'false' === 'true';
        var pageNumber = parseInt('1', 10);
        var sortType = parseInt('1', 10);
        (function noop() {
        })(noPrefix, pageNumber, sortType);
    </script>

    <script>window.flushHitokoto = function () {
            var hjs = document.createElement("script");
            hjs.setAttribute("src", "https://api.lwl12.com/hitokoto/main/get?encode=json");
            document.body.appendChild(hjs)
        };
        setTimeout(window.flushHitokoto, 1000);
        window.echokoto = function (result) {
            document.getElementsByClassName("hitokoto")[0].innerHTML = result.hitokoto
        };</script>
</head>

<body ng-app="urStatusPage" ng-controller="StatusPageCtrl">
<!--Header start-->
<header id="header">
    <div class="container">
        <img ng-src="https://userfiles.uptimerobot.com/img/{{ psp.logo }}" alt="logo" class="logo"
             ng-if="pspDataLoaded && psp.logo !== null"
        />
        <h2 class="nologo positive" ng-if="pspDataLoaded && psp.logo === null">{{ psp.name }}</h2>

        <div class="current-status">
            <span class="bullet"
                  ng-class="{'bullet-success': psp.downCount === 0, 'bullet-danger': psp.monitorCount/2 < psp.downCount, 'bullet-warning': psp.monitorCount/2 > psp.downCount && psp.downCount !== 0}"></span>
            <strong ng-if="psp.downCount === 0 && hideRefreshRemaining !== true">All clear <span>Refreshing in {{ refreshRemaining }} secs</span></strong>
            <strong ng-if="psp.monitorCount/2 < psp.downCount" style="color: #CC3300">{{ psp.downCount }} monitors down.
                <span ng-if="hideRefreshRemaining !== true">Refreshing in {{ refreshRemaining }} secs</span></strong>
            <strong ng-if="psp.monitorCount/2 > psp.downCount && psp.downCount !== 0" style="color: #f7921e">{{
                psp.downCount }} monitors down. <span ng-if="hideRefreshRemaining !== true">Refreshing in {{ refreshRemaining }} secs</span></strong>
        </div>
    </div>
</header>
<!--Header ebd-->

<!--Main Content Start-->
<div id="content">
    <div id="main" class="container">
        <div class="panel-group" id="announcement" role="tablist" aria-multiselectable="true"
             ng-if="psp.hasActiveAnnounce">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h3>({{ psp.activeAnnouncements }}) Announcements</h3>
                    <h4 ng-if="!announcementsOpened">
                        <b>{{ psp.firstAnnounceTitle }}</b>: {{ psp.firstAnnounceDescInline }}
                    </h4>
                    <a ng-if="psp.announcements.length > 0" ng-click="announcementsPanelToggled()" role="button"
                       data-toggle="collapse" data-parent="#announcement"
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

        <div ng-repeat="monitortype in psp.montypes">
            <div class="row">
                <div class="col-sm-6">
                    <h2>{{ monitortype }}</h2>
                </div>
                <!--div class="col-sm-6 right-align">
                  <a href="#" class="btn btn-primary btn-round">subscribe to all monitors</a>
                </div-->
            </div>
            </header>

            <div class="table-container">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Last 7 Days</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Interval</th>
                        <th ng-repeat="day in days">{{ day }}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <!--<td align="center" ng-repeat="day in days">{{ day }}</td> -->
                    <tr ng-repeat="mon in psp[monitortype].monitors"
                        ng-class="{'down': (mon.allLogs[0].statusStr === 'down')}">
                        <td>
                            <h4 class="{{ mon.oneWeekRange.label }}">{{ mon.oneWeekRange.ratio }}%</h4>
                        </td>
                        <td>
                            <h4 class="mon-name"><a href="{{ mon.statusPageURL }}">{{ mon.friendly_name }}</a></h4>
                        </td>
                        <td>{{ mon.typeStr }}</td>
                        <td>{{ mon.intervalMin }} mins</td>
                        <td ng-repeat="range in mon.customuptimeranges">
                            <span class="label label-{{ range.label }}">{{ range.ratio }}%</span>
                        </td>
                    </tr>


                    </tbody>
                </table>
            </div>
        </div>

        <ul class="pagination pagination-sm" ng-if="pagination.hidePagination !== true">
            <li ng-if="pagination.prevActive === true">
                <a href="{{ pagination.prevHref }}" aria-label="Previous">
                    <span aria-hidden="true">«</span>
                </a>
            </li>
            <li ng-repeat="page in pagination.pages">
                <a href="?page={{page}}" ng-class="{'statuspage-pager-active': page == pageNumber }">
                    {{ page }}
                </a>
            </li>
            <li ng-if="pagination.nextActive === true">
                <a href="{{ pagination.nextHref }}" aria-label="Next">
                    <span aria-hidden="true">»</span>
                </a>
            </li>
        </ul>

        <hr/>

        <div class="row">
            <div class="col-sm-3">
                <h2>Quick Stats</h2>
                <ul class="stats">
                    <li>
                        <span class="bullet bullet-success"></span>
                        <span class="success fl">Up</span>
                        <span class="success fr">{{ pspStats.counts.up }}</span>
                    </li>

                    <li>
                        <span class="bullet bullet-danger"></span>
                        <span class="danger fl">Down</span>
                        <span class="danger fr">{{ pspStats.counts.down }}</span>
                    </li>

                    <li>
                        <span class="bullet bullet-info"></span>
                        <span class="info fl">Paused</span>
                        <span class="info  fr">{{ pspStats.counts.paused }}</span>
                    </li>
                </ul>
            </div>

            <div class="col-sm-4 col-md-push-1 overall-uptime">
                <h2>Overall Uptime</h2>
                <ul class="overall">
                    <li>
                        <strong class="{{ pspStats.ratios.l1.label }}">{{ pspStats.ratios.l1.ratio }}% </strong>
                        <span>(last 24 hours)</span>
                    </li>
                    <li>
                        <strong class="{{ pspStats.ratios.l7.label }}">{{ pspStats.ratios.l7.ratio }}%</strong>
                        <span>(last 7 days)</span>
                    </li>
                    <li>
                        <strong class="{{ pspStats.ratios.l30.label }}">{{ pspStats.ratios.l30.ratio }}%</strong>
                        <span>(last 30 days)</span>
                    </li>
                </ul>
            </div>

            <div class="col-sm-3 col-md-push-1 latest-downtime">
                <h2>Latest Downtime</h2>
                <p>{{ latestDownTimeStr }}</p>
            </div>
        </div>
    </div>

</div>
<!-- Main Content END -->

<!--Footer start-->
<footer id="footer">
    <div class="container" ng-if="showURLinks">
        <div class="container-left" style="float: left;line-height: 35px;">
            <a href="<?php echo $info['owner_url'] ?>"><span
                        class="provided"><?php echo $info['owner_name'] ?></span></a><span><a
                        href="https://lwl.moe/" target="_blank">本页面衍生自 LWL的自由天空 旗下监控页</a> | </span><a
                    href="https://github.com/HFIProgramming/custom-uptimerobot"><span> 项目地址 | </span></a><span
                    class="hitokoto" id="hitokoto">Loading...</span>
        </div>
        <span class="provided">Provided by:</span>
        <a href="https://uptimerobot.com" rel="nofollow"><img src="/public/images/uptime-logo.png" alt="logo"/></a>
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
<?php
if (!empty($info['google_analytics'])) {
	echo '<script>(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');
    ga(\'create\', \'' . $info['google_analytics'] . '\', \'auto\');
    ga(\'send\', \'pageview\');</script>';
}
?>
</body>

</html>
