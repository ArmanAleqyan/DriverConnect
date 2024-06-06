@extends('admin.layouts.default')
@section('title')
Главный экран
    @endsection

@section('content')
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style type="text/css">/* Chart.js */
        @keyframes chartjs-render-animation{from{opacity:.99}to{opacity:1}}.chartjs-render-monitor{animation:chartjs-render-animation 1ms}.chartjs-size-monitor,.chartjs-size-monitor-expand,.chartjs-size-monitor-shrink{position:absolute;direction:ltr;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1}.chartjs-size-monitor-expand>div{position:absolute;width:1000000px;height:1000000px;left:0;top:0}.chartjs-size-monitor-shrink>div{position:absolute;width:200%;height:200%;left:0;top:0}</style>

{{--        <section class="content-header">--}}
{{--            <div class="container-fluid" bis_skin_checked="1">--}}
{{--                <div class="row mb-2" bis_skin_checked="1">--}}
{{--                    <div class="col-sm-6" bis_skin_checked="1">--}}
{{--                        <h1>Главная</h1>--}}
{{--                    </div>--}}
{{--                    <div class="col-sm-6" bis_skin_checked="1">--}}
{{--                        <ol class="breadcrumb float-sm-right">--}}
{{--                            <li class="breadcrumb-item"><a href="{{route('HomePage')}}">Home</a></li>--}}
{{--                            <li class="breadcrumb-item active">Chart</li>--}}
{{--                        </ol>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </section>--}}

    @php

        $users_count = \App\Models\User::wherebetween('created_at',[\Carbon\Carbon::now()->subdays(6)->startOfDay() ,  \Carbon\Carbon::now()->endOfDay()])->count();

        $job_count = \App\Models\Jobs::wherebetween('created_at',[\Carbon\Carbon::now()->subdays(6)->startOfDay() ,  \Carbon\Carbon::now()->endOfDay()]);
            @endphp
        <section class="content">

            <div class="container-fluid" bis_skin_checked="1">
                <div class="row" bis_skin_checked="1">
                    <div class="col-12 col-sm-6 col-md-3" bis_skin_checked="1">
                        <div class="info-box" bis_skin_checked="1">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                            <div class="info-box-content" bis_skin_checked="1">
                                <span class="info-box-text">Количество водителей </span>
                                <span class="info-box-number">
                                    {{$get_working_users_count}}
                                    <small></small>
                                    </span>
                            </div>

                        </div>

                    </div>

                    <div class="col-12 col-sm-6 col-md-3" bis_skin_checked="1">
                        <div class="info-box mb-3" bis_skin_checked="1">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-star"></i></span>
                            <div class="info-box-content" bis_skin_checked="1">
                                <span class="info-box-text">Количество активных </span>
                                <span class="info-box-number">{{$get_latest_week_users_count}}</span>
                            </div>

                        </div>

                    </div>


                    <div class="clearfix hidden-md-up" bis_skin_checked="1"></div>
                    <div class="col-12 col-sm-6 col-md-3" bis_skin_checked="1">
                        <div class="info-box mb-3" bis_skin_checked="1">
                            <span class="info-box-icon bg-success elevation-1"><i class="fa fa-taxi"></i></span>
                            <div class="info-box-content" bis_skin_checked="1">
                                <span class="info-box-text">На линии</span>
                                <span class="info-box-number">{{$get_online_users_count}}</span>
                            </div>

                        </div>

                    </div>

                    <div class="col-12 col-sm-6 col-md-3" bis_skin_checked="1">
                        <div class="info-box mb-3" bis_skin_checked="1">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-briefcase"></i></span>
                            <div class="info-box-content" bis_skin_checked="1">
                                <span class="info-box-text">На заказе</span>
                                <span class="info-box-number">{{$in_order_users_count}}</span>
                            </div>

                        </div>

                    </div>

                </div>
                <div class="row" bis_skin_checked="1">
                    <div class="col-md-6" bis_skin_checked="1">
                        <div class="card card-primary" bis_skin_checked="1">
                            <div class="card-header" bis_skin_checked="1">
                                <h3 class="card-title">Статистика регистраций за неделю` {{$users_count}} </h3>
{{--                                <div class="card-tools" bis_skin_checked="1">--}}
{{--                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">--}}
{{--                                        <i class="fas fa-minus"></i>--}}
{{--                                    </button>--}}
{{--                                    <button type="button" class="btn btn-tool" data-card-widget="remove">--}}
{{--                                        <i class="fas fa-times"></i>--}}
{{--                                    </button>--}}
{{--                                </div>--}}
                            </div>
                            <div class="card-body" bis_skin_checked="1">
                                <div class="chart" bis_skin_checked="1"><div class="chartjs-size-monitor" bis_skin_checked="1"><div class="chartjs-size-monitor-expand" bis_skin_checked="1"><div class="" bis_skin_checked="1"></div></div><div class="chartjs-size-monitor-shrink" bis_skin_checked="1"><div class="" bis_skin_checked="1"></div></div></div>
                                    <canvas id="areaChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 604px;" width="604" height="250" class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>

                        </div>
                        <div class="card card-success" bis_skin_checked="1">
                            <div class="card-header" bis_skin_checked="1">
                                <h3 class="card-title">Поездки `  {{$job_count->count()}} / {{$job_count->sum('job_price_with_bonus')}}</h3>
{{--                                <div class="card-tools" bis_skin_checked="1">--}}
{{--                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">--}}
{{--                                        <i class="fas fa-minus"></i>--}}
{{--                                    </button>--}}
{{--                                    <button type="button" class="btn btn-tool" data-card-widget="remove">--}}
{{--                                        <i class="fas fa-times"></i>--}}
{{--                                    </button>--}}
{{--                                </div>--}}
                            </div>
                            <div class="card-body" bis_skin_checked="1">
                                <div class="chart" bis_skin_checked="1"><div class="chartjs-size-monitor" bis_skin_checked="1"><div class="chartjs-size-monitor-expand" bis_skin_checked="1"><div class="" bis_skin_checked="1"></div></div><div class="chartjs-size-monitor-shrink" bis_skin_checked="1"><div class="" bis_skin_checked="1"></div></div></div>
                                    <canvas id="stackedBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 604px;" width="604" height="250" class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>

                        </div>



                        <div class="card card-danger" bis_skin_checked="1" style="display: none;">
                            <div class="card-header" bis_skin_checked="1">
                                <h3 class="card-title">Donut Chart</h3>
                                <div class="card-tools" bis_skin_checked="1">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body" bis_skin_checked="1"><div class="chartjs-size-monitor" bis_skin_checked="1"><div class="chartjs-size-monitor-expand" bis_skin_checked="1"><div class="" bis_skin_checked="1"></div></div><div class="chartjs-size-monitor-shrink" bis_skin_checked="1"><div class="" bis_skin_checked="1"></div></div></div>
                                <canvas id="donutChart" style="min-height: 250px; height: 0px; max-height: 250px; max-width: 100%; display: block; width: 0px;" width="0" height="0" class="chartjs-render-monitor"></canvas>
                            </div>

                        </div>


                        <div class="card card-danger" bis_skin_checked="1" style="display: none;">
                            <div class="card-header" bis_skin_checked="1">
                                <h3 class="card-title">Pie Chart</h3>
                                <div class="card-tools" bis_skin_checked="1">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body" bis_skin_checked="1"><div class="chartjs-size-monitor" bis_skin_checked="1"><div class="chartjs-size-monitor-expand" bis_skin_checked="1"><div class="" bis_skin_checked="1"></div></div><div class="chartjs-size-monitor-shrink" bis_skin_checked="1"><div class="" bis_skin_checked="1"></div></div></div>
                                <canvas id="pieChart" style="min-height: 250px; height: 0px; max-height: 250px; max-width: 100%; display: block; width: 0px;" width="0" height="0" class="chartjs-render-monitor"></canvas>
                            </div>

                        </div>

                    </div>

                    <div class="col-md-6" bis_skin_checked="1">



                        @php
                            $platform_sum = \App\Models\JobTranzaksion::wherebetween('event_at',[\Carbon\Carbon::now()->subdays(6)->startOfDay() ,  \Carbon\Carbon::now()->endOfDay()])->where('group_id', 'partner_fees')->sum('amount');
                        $platform_sum = abs($platform_sum);
                        @endphp
                        <div class="card card-success" bis_skin_checked="1">
                            <div class="card-header" bis_skin_checked="1">
                                <h3 class="card-title">Доход таксопарка  за неделю` {{$platform_sum}}</h3>
                                <div class="card-tools" bis_skin_checked="1">
{{--                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">--}}
{{--                                        <i class="fas fa-minus"></i>--}}
{{--                                    </button>--}}
{{--                                    <button type="button" class="btn btn-tool" data-card-widget="remove">--}}
{{--                                        <i class="fas fa-times"></i>--}}
{{--                                    </button>--}}
                                </div>
                            </div>
                            <div class="card-body" bis_skin_checked="1">
                                <div class="chart" bis_skin_checked="1"><div class="chartjs-size-monitor" bis_skin_checked="1"><div class="chartjs-size-monitor-expand" bis_skin_checked="1"><div class="" bis_skin_checked="1"></div></div><div class="chartjs-size-monitor-shrink" bis_skin_checked="1"><div class="" bis_skin_checked="1"></div></div></div>
                                    <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 604px;" width="604" height="250" class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>

                        </div>




                    </div>

                </div>

            </div>
        </section>

        <script src="{{asset('admin/plugins/jquery/jquery.min.js')}}"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @php
        use Illuminate\Support\Facades\DB;
        use App\Models\User;
        use App\Models\Jobs;
        use App\Models\JobTranzaksion;



        $currentDate = now();
        $thirtyDaysAgo = now()->subDays(6);

        $dates = [];
$daysAbbr = [
    'Понедельник' => 'Пн',
    'Вторник' => 'Вт',
    'Среда' => 'Ср',
    'Четверг' => 'Чт',
    'Пятница' => 'Пт',
    'Суббота' => 'Сб',
    'Воскресенье' => 'Вс'
];

// Массив с сокращениями месяцев

        // Iterate over the range of dates from $thirtyDaysAgo to $currentDate
        $currentDateTimestamp = strtotime($currentDate);
        $thirtyDaysAgoTimestamp = strtotime($thirtyDaysAgo);

        $i = 0;
        for ($date = $thirtyDaysAgoTimestamp; $date <= $currentDateTimestamp; $date += 86400) {
            // Convert timestamp back to date format
                      $formattedDate2 = date('Y-m-d', $date);

                    $formatter = new \IntlDateFormatter(
            'ru_RU',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::NONE,
            'Europe/Moscow',
            \IntlDateFormatter::GREGORIAN,
            'dd EEEE'
        );
        $formattedDate = $formatter->format($date);
        $formattedDate = mb_convert_case($formattedDate, MB_CASE_TITLE, "UTF-8");
    foreach ($daysAbbr as $full => $abbr) {

        if (stripos($formattedDate, $full) !== false) {
            $formattedDate = str_ireplace($full, $abbr, $formattedDate);
        }
    }

            // Add formatted date to the array
            $users_query = User::wheredate('created_at', $formattedDate2);
            $get_registert_users = $users_query->count();
            $get_job_count = Jobs::wherein('user_id',$users_query->pluck('id')->toarray() )->wheredate('created_at', $formattedDate2)->count();
            $dates[$i]['date'] = $formattedDate;
            $dates[$i]['users_count'] =$get_registert_users;
            $dates[$i]['work_users_count'] = $get_job_count;


        $i++;
        }


        $dates = json_encode($dates);
        $currentDate = null;
$thirtyDaysAgo = null;
        $currentDate = now();
        $thirtyDaysAgo = now()->subDays(6);

        $dates_park = [];

        // Iterate over the range of dates from $thirtyDaysAgo to $currentDate
        $currentDateTimestamp = strtotime($currentDate);
        $thirtyDaysAgoTimestamp = strtotime($thirtyDaysAgo);

        $i= 0;

    for ($date = $thirtyDaysAgoTimestamp; $date <= $currentDateTimestamp; $date += 86400) {

              $formattedDate2 = date('Y-m-d', $date);

                    $formatter = new \IntlDateFormatter(
            'ru_RU',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::NONE,
            'Europe/Moscow',
            \IntlDateFormatter::GREGORIAN,
            'dd EEEE'
        );
        $formattedDate = $formatter->format($date);
        $formattedDate = mb_convert_case($formattedDate, MB_CASE_TITLE, "UTF-8");
    foreach ($daysAbbr as $full => $abbr) {

        if (stripos($formattedDate, $full) !== false) {
            $formattedDate = str_ireplace($full, $abbr, $formattedDate);
        }
    }
        $job_tranzakcion = JobTranzaksion::wheredate('event_at', $formattedDate2)->where('group_id', 'partner_fees')->sum('amount');
            $dates_park[$i]['date'] =$formattedDate;
            $dates_park[$i]['sum'] =preg_replace('/-/', '',$job_tranzakcion);
        $i++;
        }

    $dates_park = json_encode($dates_park);

     $currentDate = now();
        $thirtyDaysAgo = now()->subDays(6);


        // Iterate over the range of dates from $thirtyDaysAgo to $currentDate
        $currentDateTimestamp = strtotime($currentDate);
        $thirtyDaysAgoTimestamp = strtotime($thirtyDaysAgo);

    for ($date = $thirtyDaysAgoTimestamp; $date <= $currentDateTimestamp; $date += 86400) {
      $formattedDate2 = date('Y-m-d', $date);

                    $formatter = new \IntlDateFormatter(
                    'ru_RU',
                    \IntlDateFormatter::FULL,
                    \IntlDateFormatter::NONE,
                    'Europe/Moscow',
                    \IntlDateFormatter::GREGORIAN,
                    'dd EEEE'
        );
        $formattedDate = $formatter->format($date);
        $formattedDate = mb_convert_case($formattedDate, MB_CASE_TITLE, "UTF-8");
    foreach ($daysAbbr as $full => $abbr) {

        if (stripos($formattedDate, $full) !== false) {
            $formattedDate = str_ireplace($full, $abbr, $formattedDate);
        }
    }


        $job_corp_count = Jobs::wheredate('created_at', $formattedDate2)->where('payment_method', 'corp')->where('status','complete' )->sum('job_price_with_bonus');
        $job_cash_count = Jobs::wheredate('created_at', $formattedDate2)->where('payment_method', 'cash')->where('status','complete' )->sum('job_price_with_bonus');
        $job_card_count = Jobs::wheredate('created_at', $formattedDate2)->where('payment_method', 'cashless')->where('status','complete' )->sum('job_price_with_bonus');
            $dates_park_price_category[$i]['date'] =$formattedDate;
            $dates_park_price_category[$i]['job_corp_count'] =$job_corp_count;
            $dates_park_price_category[$i]['job_cash_count'] =$job_cash_count;
            $dates_park_price_category[$i]['job_card_count'] =$job_card_count;
        $i++;
        }



    $dates_park_price_category = json_encode($dates_park_price_category);
    @endphp
    <script>
        $(function () {
            var labels = [];
            var blueData = [];
            var greyData = [];

            var backendData = '{!!  $dates!!}'; // Убедитесь, что нет кавычек вокруг $datesJson

            var dataArray = JSON.parse(backendData);
            for (var i = 0; i < dataArray.length; i++) {
                var date = dataArray[i];
                labels.push(date.date)
                blueData.push(date.work_users_count);
                greyData.push(date.users_count);
            }
            var areaChartCanvas = $('#areaChart').get(0).getContext('2d')

            var areaChartData = {
                labels  : labels,
                datasets: [
                    {
                        label               : 'Сделали заказ',
                        backgroundColor     : 'rgba(60,141,188,0.9)',
                        borderColor         : 'rgba(60,141,188,0.8)',
                        pointRadius          : false,
                        pointColor          : '#3b8bba',
                        pointStrokeColor    : 'rgba(60,141,188,1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data                : blueData
                    },
                    {
                        label               : 'Зарегестрированные',
                        backgroundColor     : 'rgba(210, 214, 222, 1)',
                        borderColor         : 'rgba(210, 214, 222, 1)',
                        pointRadius         : false,
                        pointColor          : 'rgba(210, 214, 222, 1)',
                        pointStrokeColor    : '#c1c7d1',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : greyData
                    },
                ]
            }

            var areaChartOptions = {
                maintainAspectRatio : false,
                responsive : true,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        gridLines : {
                            display : true,
                        }
                    }],
                    yAxes: [{
                        gridLines : {
                            display : true,
                        }
                    }]
                }
            }

            // This will get the first returned node in the jQuery collection.
            new Chart(areaChartCanvas, {
                type: 'line',
                data: areaChartData,
                options: areaChartOptions
            })
            //-------------
            //- BAR CHART -
            //-------------
            var backendData = '{!!  $dates_park!!}'; // Убедитесь, что нет кавычек вокруг $datesJson
            var dataArray = JSON.parse(backendData);
            var labels = [];
            var blueData2 =[];
            for (var i = 0; i < dataArray.length; i++) {
                var date = dataArray[i];
                labels.push(date.date)
                blueData2.push(date.sum);
            }
            var areaChartData2 = {
                labels  : labels,
                datasets: [
                    {
                        label               : '',
                        backgroundColor     : 'rgba(60,141,188,0.9)',
                        borderColor         : 'rgba(60,141,188,0.8)',
                        pointRadius         : false,
                        pointColor          : '#3b8bba',
                        pointStrokeColor    : 'rgba(60,141,188,1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data                : blueData2
                    }
                ]
            }
            var barChartCanvas = $('#barChart').get(0).getContext('2d')
            var barChartData = $.extend(true, {}, areaChartData2)

            var barChartOptions = {
                responsive              : true,
                maintainAspectRatio     : false,
                datasetFill             : false,
                legend: {
                    display: false // Скрыть легенду
                }
            }

            new Chart(barChartCanvas, {
                type: 'bar',
                data: barChartData,
                options: barChartOptions
            })
            //---------------------
            //- STACKED BAR CHART -
            //---------------------
            // $dates_park_price_category
            // $dates_park_price_category[$i]['job_corp_count'] =$job_corp_count;
            // $dates_park_price_category[$i]['job_cash_count'] =$job_cash_count;
            // $dates_park_price_category[$i]['job_card_count'] =$job_card_count;
            var areaChartCanvas = $('#areaChart').get(0).getContext('2d')
            var labels = [];
            var blueData = [];
            var greyData = [];
            var redData = [];

            var backendData3 = '{!! $dates_park_price_category !!}'; // Убедитесь, что нет кавычек вокруг $datesJson
            var dataObject = JSON.parse(backendData3);

            var dataArrays = Object.values(dataObject);


            for (var i = 0; i < dataArrays.length; i++) {

                var date = dataArrays[i];

                labels.push(date.date)
                blueData.push(date.job_corp_count);
                greyData.push(date.job_cash_count);
                redData.push(date.job_card_count);
            }

            var areaChartData = {
                labels  : labels,
                datasets: [
                    {
                        label               : 'Наличные',
                        backgroundColor     : 'rgba(60,141,188,0.9)',
                        borderColor         : 'rgba(60,141,188,0.8)',
                        pointRadius          : false,
                        pointColor          : '#3b8bba',
                        pointStrokeColor    : 'rgba(60,141,188,1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data                :greyData
                    },
                    {
                        label               : 'Карта',
                        backgroundColor     : 'rgba(210, 214, 222, 1)',
                        borderColor         : 'rgba(210, 214, 222, 1)',
                        pointRadius         : false,
                        pointColor          : 'rgba(210, 214, 222, 1)',
                        pointStrokeColor    : '#c1c7d1',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                :  redData
                    },
                    {
                        label               : 'Корпоративная оплата',
                        backgroundColor     : 'rgb(222,55,65)',
                        borderColor         : 'rgb(222,54,55)',
                        pointRadius         : false,
                        pointColor          : 'rgb(222,41,57)',
                        pointStrokeColor    : '#d1454b',
                        pointHighlightFill  : '#ff6462',
                        pointHighlightStroke: 'rgb(220,79,77)',
                        data                :  blueData
                    },
                ]
            }

            var areaChartOptions = {
                maintainAspectRatio : false,
                responsive : true,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        gridLines : {
                            display : true,
                        }
                    }],
                    yAxes: [{
                        gridLines : {
                            display : true,
                        }
                    }]
                }
            }
            var barChartCanvas = $('#barChart').get(0).getContext('2d')
            var barChartData = $.extend(true, {}, areaChartData)
            var temp0 = areaChartData.datasets[0]
            var temp1 = areaChartData.datasets[1]
            barChartData.datasets[0] = temp1
            barChartData.datasets[1] = temp0
            var stackedBarChartCanvas = $('#stackedBarChart').get(0).getContext('2d')
            var stackedBarChartData = $.extend(true, {}, barChartData)

            var stackedBarChartOptions = {
                responsive              : true,
                maintainAspectRatio     : false,
                scales: {
                    xAxes: [{
                        stacked: true,
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                }
            }

            new Chart(stackedBarChartCanvas, {
                type: 'bar',
                data: stackedBarChartData,
                options: stackedBarChartOptions
            })

        })
    </script>



@endsection

