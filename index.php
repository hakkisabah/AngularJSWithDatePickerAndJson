
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Datepicker</title>
    <!-- bower:css -->
    <link rel="stylesheet" href="src/css/bootstrap/bootstrap.min.css" />
    <link rel="stylesheet" href="src/css/bootstrap/daterangepicker.min.css" />
    <!-- endbower -->
</head>
<body>
<!-- bower:js -->
<script src="src/js/jquery/jquery-2.1.4.min.js"></script>
<script src="src/js/ajax/angular.min.js"></script>
<script src="src/js/bootstrap/bootstrap.min.js"></script>
<script src="src/js/ajax/moment.min.js"></script>
<script src="src/js/ajax/daterangepicker.min.js"></script>
<!-- endbower -->
<script src="src/js/angular-daterangepicker.min.js"></script>
<script src="src/js/bootstrap/ui-bootstrap-tpls-1.2.5.js"></script>
<div class="container">
    <h1>Datepicker for hackathon by hakkı sabah</h1>
    <div class="row">
        <div class="col-md-6" ng-controller="TestCtrl">
            <!--min and max container -->
            <div class="container">
                <img  ng-show="loader.loading" src="loading.gif">
                <!-- repeat block -->
                <div class="row">
                    <textarea class="form-control" ng-show="thenresult.ld" rows="30">{{pickerprint | json }}</textarea>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6" ng-repeat="cust in filteredpickerdata">
                        <div class="card">
                            <div class="card-block">
                                <h4 class="card-title">{{cust._id | json}} {{cust.key | json}}</h4>
                                <p class="card-text">{{cust.createdAt| json}}{{cust.totalCount| json }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end repeat block -->
                <!-- pagination section -->
                <div class="container">

                    <div class="row" data-ng-show="pickerdata.length > 0">
                        <div class="col-span-12">
                            <uib-pagination ng-model="currentPage" total-items="pickerdata.length" max-size="maxSize" items-per-page="numPerPage" boundary-links="true"></uib-pagination>
                        </div>
                    </div>
                </div>
                <!-- end pagination section -->
            </div>
            <!-- end min and max container -->

            <!-- Datepicker Area -->
            <form name="dateForm" class="form-horizontal">
                <div class="form-inline">
                    <div class="form-group">
                        <label class="sr-only">minCount</label>
                     <input ng-model="minCount" class="form-control" placeholder="minCount" required />
                    </div>
                    <div></div>
                    <div class="form-group">
                        <label class="sr-only">maxCount</label>
                        <input ng-model="maxCount" class="form-control" placeholder="maxCount" required/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="daterange5" class="control-label">Select Date Range</label>
                    <input date-range-picker name="daterange5" id="daterange5" class="form-control date-picker" type="text"
                           ng-model="date" options="{locale: {format: 'MMMM D, YYYY'}}" required/>
                </div>
                <input type="submit" value="Send" ng-click="postdata(date.startDate,date.endDate,minCount,maxCount)" /> <br/><br/>
            </form>
            <!-- end Datepicker Area -->
        </div>
    </div>
</div>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var elements = document.getElementsByTagName("INPUT");
        for (var i = 0; i < elements.length; i++) {
            elements[i].oninvalid = function(e) {
                e.target.setCustomValidity("");
                if (!e.target.validity.valid) {
                    e.target.setCustomValidity("Bu alanları doldurmadığınız sürece veri akışı sağlanmayacak lütfen sayfayı yenileyin");
                }
            };
            elements[i].oninput = function(e) {
                e.target.setCustomValidity("");
            };
        }
    });
    exampleApp = angular.module('example', ['daterangepicker','ui.bootstrap']);
    exampleApp.controller('TestCtrl', function($scope,$http,$filter) {
        //define variables..
        $scope.pickerdata = [];
        $scope.pickerdataraw = [];
        $scope.rawfirst;
        $scope.pickerdatalast;
        $scope.pickerprint = [];
        $scope.filteredpickerdata = [];
        $scope.currentPage = 1;
        $scope.numPerPage = 10;
        $scope.maxSize = 5;
        //define ng-show variables..
        $scope.loader = {
            loading: false,
        };
$scope.thenresult =  {
    ld: false,
};
//$http.post function trigger from html and using parameter like ; " ng-click="postdata(date.startDate,date.endDate,minCount,maxCount)" "
        $scope.postdata = function (sdate,edate,min,max) {
            var reqdata = {
                "startDate": sdate,
                "endDate": edate,
                "minCount" : min,
                "maxCount" : max
            };
            //loading gif show when click send button
            $scope.loader.loading = true ;
            $http.post('https://getir-bitaksi-hackathon.herokuapp.com/searchRecords', JSON.stringify(reqdata)).then(function (response) {
                $scope.pickerdata = response.data.records;
                $scope.pickerdataraw = response;

                $scope.pickerdata = $filter('orderBy')($scope.pickerdata, 'totalCount');
                //example json textarea turn true when getting success data
                $scope.thenresult.ld = ($scope.pickerdata.length > 1)? true:false;
                $scope.loader.loading = false ;
                //updating pagination list when HTTP post ok.
                updateFilteredItems();
            });

        };

        //Watcher listener..
        $scope.$watch('currentPage + numPerPage', updateFilteredItems);

        //pagination list update function
        function updateFilteredItems() {
            var begin = (($scope.currentPage - 1) * $scope.numPerPage),
                end = begin + $scope.numPerPage;

            $scope.filteredpickerdata = $scope.pickerdata.slice(begin, end);
            $scope.pickerdatalast = $scope.pickerdata.slice(-1);
            $scope.rawfirst = $filter('orderBy')($scope.pickerdataraw.data.records, 'totalCount');
            $scope.pickerprint = {"code":0, "msg":"Success", records:[$scope.rawfirst[0],$scope.pickerdatalast[0]]}
        }

        //after from this lines related datepicker plugin
        $scope.date = {
            startDate: moment().subtract(2, "year"),
            endDate: moment()
        };
        $scope.singleDate = moment();

        $scope.opts = {
            locale: {
                applyClass: 'btn-green',
                applyLabel: "Apply",
                fromLabel: "From",
                format: "YYYY-MM-DD",
                toLabel: "To",
                cancelLabel: 'Cancel',
                customRangeLabel: 'Custom range'
            },
            ranges: {
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()]
            }
        };

        $scope.setStartDate = function () {
            $scope.date.startDate = moment().subtract(4, "days").toDate();
        };

        $scope.setRange = function () {
            $scope.date = {
                startDate: moment().subtract(5, "days"),
                endDate: moment()
            };
        };

        //Watch for date changes
        $scope.$watch('date', function(newDate) {
            console.log('New date set: ', newDate);
        }, false);

    });

    angular.bootstrap(document, ['example']);</script>

</html>
