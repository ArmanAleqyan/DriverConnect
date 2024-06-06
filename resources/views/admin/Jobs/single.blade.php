@extends('admin.layouts.default')
@section('title')
    Поездка @endsection
@section('page_css')
    <style>
        .content-wrapper{
            height: auto !important;
            overflow: auto!important;
        }
        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .details-header h3 {
            margin: 0;
        }
        .details-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
        }
        .details-item:last-child {
            border-bottom: none;
        }
        .details-value {
            display: flex;
            align-items: center;
        }
        .details-value span {
            margin-right: 10px;
        }
        .details-value svg {
            cursor: pointer;
        }
        .sheet-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .sheet-header h3 {
            margin: 0;
            display: flex;
            align-items: center;
        }
        .sheet-header svg {
            margin-left: 10px;
            transition: transform 0.3s;
        }
        .sheet-header svg.expanded {
            transform: rotate(180deg);
        }
        .sheet-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }
        .sheet-content {
            display: none;
        }
        .trip-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .trip-info div {
            margin-right: 20px;
        }
        .trip-info div:last-child {
            margin-right: 0;
        }
        #map {
            width: 100%;
            height: 300px;
        }
        body {
            overflow-x: hidden;
        }
        .container {
            max-width: 1200px;
        }
        .toggle-icon.expanded {
            transform: rotate(180deg);
        }
    </style>
    @endsection

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <h1 style="color: #2f81ff">Поездка в яндекс #{{$get_job->short_id}} От {{\Carbon\Carbon::parse($get_job->created_at)->format('d-m-Y H:i:s')}}</h1>
                    <div class="container mt-5">
                        <div class="trip-info">
                            <p><b>{{$get_job->user->name}} {{$get_job->user->surname}}</b> <br> Водитель</p>
                            <p><b>{{$get_job->car->number}}</b> <br> Авто</p>
                            <p><b>Яндекс Grot</b> <br> Кабинет</p>
                        </div>
                        <div class="details-item">
                            <span>Тариф</span>
                            <div class="details-value">
                                <span>{{$get_job->category->show_name??'??'}}</span>
                            </div>
                        </div>
                        @php
                            $paymentTypes = [
                                'cashless' => '<i class="fa fa-credit-card"></i>',
                                'cash' => '<i class="fa fa-money"></i>',
                                'corp' => '<i class="fa fa-building"></i>'
                            ];
                            $paymentTypeIcon = isset($paymentTypes[$get_job->payment_method]) ? $paymentTypes[$get_job->payment_method] : '';
                        @endphp
                        <div class="details-item">
                            <span>Тип оплаты</span>
                            <div class="details-value">
                                <span>{!! $paymentTypeIcon !!}</span>
                            </div>
                        </div>

                        <div class="details-item">
                            <span>Откуда</span>
                            <div class="details-value">
                                <span>{!! $get_job->address_from !!}</span>
                            </div>
                        </div>
                        @if( $get_job->routes->count() > 1)
                                @foreach($get_job->routes->slice(0, -1) as $route)
                                <div class="details-item">
                                    <span></span>
                                    <div class="details-value">
                                        <span>{!! $route->address!!}</span>
                                    </div>
                                </div>
                                @endforeach

                        @endif
                        <div class="details-item">
                            <span>Куда</span>
                            <div class="details-value">
                                <span>{!! $get_job->routes->last()->address !!}</span>
                            </div>
                        </div>

                        <div class="sheet-header">
                            <h3><i class="fa-solid fa-money-check-dollar"></i> &nbsp; Стоимость заказа</h3>
                            <div>
                                {{$get_job->job_price_with_bonus}}
                                <svg class="toggle-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.05011 9.67309C6.86264 9.86061 6.75732 10.1149 6.75732 10.3801C6.75732 10.6452 6.86264 10.8996 7.05011 11.0871L11.2931 15.3301C11.4806 15.5176 11.7349 15.6229 12.0001 15.6229C12.2653 15.6229 12.5196 15.5176 12.7071 15.3301L16.9501 11.0871C17.043 10.9942 17.1166 10.8839 17.1668 10.7625C17.217 10.6412 17.2429 10.5111 17.2428 10.3797C17.2428 10.2484 17.2169 10.1183 17.1665 9.997C17.1162 9.87567 17.0425 9.76543 16.9496 9.67259C16.8567 9.57974 16.7464 9.50611 16.625 9.45588C16.5037 9.40566 16.3736 9.37984 16.2423 9.37988C16.1109 9.37993 15.9809 9.40585 15.8595 9.45615C15.7382 9.50646 15.628 9.58018 15.5351 9.67309L12.0001 13.2081L8.46411 9.67309C8.27658 9.48561 8.02227 9.3803 7.75711 9.3803C7.49195 9.3803 7.23764 9.48561 7.05011 9.67309Z" fill="currentColor"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="sheet-content">
                            @foreach($get_job->job_details as $job_details)
                                <div class="sheet-item">
                                    <dt>{{$job_details->category_name}}</dt>
                                    <dd>{{$job_details->amount}}</dd>
                                </div>
                            @endforeach
                        </div>

                        <div class="sheet-header">
                            <h3><i class="fas fa-tasks"></i>&nbsp; Детали</h3>
                            <svg class="toggle-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.05011 9.67309C6.86264 9.86061 6.75732 10.1149 6.75732 10.3801C6.75732 10.6452 6.86264 10.8996 7.05011 11.0871L11.2931 15.3301C11.4806 15.5176 11.7349 15.6229 12.0001 15.6229C12.2653 15.6229 12.5196 15.5176 12.7071 15.3301L16.9501 11.0871C17.043 10.9942 17.1166 10.8839 17.1668 10.7625C17.217 10.6412 17.2429 10.5111 17.2428 10.3797C17.2428 10.2484 17.2169 10.1183 17.1665 9.997C17.1162 9.87567 17.0425 9.76543 16.9496 9.67259C16.8567 9.57974 16.7464 9.50611 16.625 9.45588C16.5037 9.40566 16.3736 9.37984 16.2423 9.37988C16.1109 9.37993 15.9809 9.40585 15.8595 9.45615C15.7382 9.50646 15.628 9.58018 15.5351 9.67309L12.0001 13.2081L8.46411 9.67309C8.27658 9.48561 8.02227 9.3803 7.75711 9.3803C7.49195 9.3803 7.23764 9.48561 7.05011 9.67309Z" fill="currentColor"></path>
                            </svg>
                        </div>
                        <div class="sheet-content">
                            <div class="sheet-item">
                                <dt>Время поездки</dt>
                                <dd>{{$get_job->timeDifferenceFormatted}} мин</dd>
                            </div>
                            <div class="sheet-item">
                                <dt>Расстояние по заказу</dt>
                                <dd>{{$get_job->work_km}} км</dd>
                            </div>
                            <div class="sheet-item">
                                <dt>Стоимость минуты заказа</dt>
                                <dd>{{$get_job->price_in_minute}} ₽/мин</dd>
                            </div>
                            <div class="sheet-item">
                                <dt>Стоимость километра</dt>
                                <dd>{{$get_job->price_in_km}} ₽/км</dd>
                            </div>
                            <div class="sheet-item">
                                <dt>Подача</dt>
                                <dd>{{\Carbon\Carbon::parse($get_job->created_at)->format('d-m-Y H:i:s')}}</dd>
                            </div>
                            <div class="sheet-item">
                                <dt>Средная скорость</dt>
                                <dd>{{number_format($get_job->tracker->avg( 'speed'),2)}} Км/ч</dd>
                            </div>
                        </div>


{{--                        <div class="sheet-header">--}}
{{--                            <h3><i class="fas fa-comment-alt"></i> Сообщения</h3>--}}
{{--                            <svg class="toggle-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.05011 9.67309C6.86264 9.86061 6.75732 10.1149 6.75732 10.3801C6.75732 10.6452 6.86264 10.8996 7.05011 11.0871L11.2931 15.3301C11.4806 15.5176 11.7349 15.6229 12.0001 15.6229C12.2653 15.6229 12.5196 15.5176 12.7071 15.3301L16.9501 11.0871C17.043 10.9942 17.1166 10.8839 17.1668 10.7625C17.217 10.6412 17.2429 10.5111 17.2428 10.3797C17.2428 10.2484 17.2169 10.1183 17.1665 9.997C17.1162 9.87567 17.0425 9.76543 16.9496 9.67259C16.8567 9.57974 16.7464 9.50611 16.625 9.45588C16.5037 9.40566 16.3736 9.37984 16.2423 9.37988C16.1109 9.37993 15.9809 9.40585 15.8595 9.45615C15.7382 9.50646 15.628 9.58018 15.5351 9.67309L12.0001 13.2081L8.46411 9.67309C8.27658 9.48561 8.02227 9.3803 7.75711 9.3803C7.49195 9.3803 7.23764 9.48561 7.05011 9.67309Z" fill="currentColor"></path>--}}
{{--                            </svg>--}}
{{--                        </div>--}}
{{--                        <div class="sheet-content">--}}
{{--                            <div class="sheet-item">--}}
{{--                                <dt>Яндекс</dt>--}}
{{--                                <dd>Поступило уведомление об оплате картой на сумму 587,00 ₽</dd>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="map" class="trip-map"></div>
                    <br>
                    <div class="sheet-header">
                        <h3><i class="fa-solid fa-road"></i> &nbsp; Событие</h3>
                        <svg class="toggle-icon expanded" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.05011 9.67309C6.86264 9.86061 6.75732 10.1149 6.75732 10.3801C6.75732 10.6452 6.86264 10.8996 7.05011 11.0871L11.2931 15.3301C11.4806 15.5176 11.7349 15.6229 12.0001 15.6229C12.2653 15.6229 12.5196 15.5176 12.7071 15.3301L16.9501 11.0871C17.043 10.9942 17.1166 10.8839 17.1668 10.7625C17.217 10.6412 17.2429 10.5111 17.2428 10.3797C17.2428 10.2484 17.2169 10.1183 17.1665 9.997C17.1162 9.87567 17.0425 9.76543 16.9496 9.67259C16.8567 9.57974 16.7464 9.50611 16.625 9.45588C16.5037 9.40566 16.3736 9.37984 16.2423 9.37988C16.1109 9.37993 15.9809 9.40585 15.8595 9.45615C15.7382 9.50646 15.628 9.58018 15.5351 9.67309L12.0001 13.2081L8.46411 9.67309C8.27658 9.48561 8.02227 9.3803 7.75711 9.3803C7.49195 9.3803 7.23764 9.48561 7.05011 9.67309Z" fill="currentColor"></path>
                        </svg>
                    </div>
                    @php
                        $eventTypes = [
                            'cancelled' => 'Отменённый',
                            'complete' => 'Завершённый',
                            'transporting' => 'Транспортировка',
                            'waiting' => 'Ожидание',
                            'driving' => 'На заказе'
                        ];
                    @endphp
                    <div class="sheet-content" style="display: block;">
                        @foreach($get_job->events as $event)
                            @php
                                $event_type = isset($eventTypes[$event->order_status]) ? $eventTypes[$event->order_status] : '??';
                            @endphp
                            <div class="sheet-item">
                                <dt>{{$event_type}}</dt>
                                <dd>{{\Carbon\Carbon::parse($event->event_at)->format('H:i:s')}}</dd>
                            </div>
                        @endforeach
                    </div>

                    {{--                    <div class="sheet-header">--}}
{{--                        <h3 style="font-size: 13px"><i class="fa-solid fa-location-dot"></i> &nbsp; Адресс подачи</h3>--}}
{{--                        <div>--}}
{{--                            {{$get_job->address_from}}--}}
{{--                            <svg class="toggle-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.05011 9.67309C6.86264 9.86061 6.75732 10.1149 6.75732 10.3801C6.75732 10.6452 6.86264 10.8996 7.05011 11.0871L11.2931 15.3301C11.4806 15.5176 11.7349 15.6229 12.0001 15.6229C12.2653 15.6229 12.5196 15.5176 12.7071 15.3301L16.9501 11.0871C17.043 10.9942 17.1166 10.8839 17.1668 10.7625C17.217 10.6412 17.2429 10.5111 17.2428 10.3797C17.2428 10.2484 17.2169 10.1183 17.1665 9.997C17.1162 9.87567 17.0425 9.76543 16.9496 9.67259C16.8567 9.57974 16.7464 9.50611 16.625 9.45588C16.5037 9.40566 16.3736 9.37984 16.2423 9.37988C16.1109 9.37993 15.9809 9.40585 15.8595 9.45615C15.7382 9.50646 15.628 9.58018 15.5351 9.67309L12.0001 13.2081L8.46411 9.67309C8.27658 9.48561 8.02227 9.3803 7.75711 9.3803C7.49195 9.3803 7.23764 9.48561 7.05011 9.67309Z" fill="currentColor"></path>--}}
{{--                            </svg>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="sheet-content">--}}
{{--                        @foreach($get_job->routes as $routes)--}}
{{--                            <div class="sheet-item">--}}
{{--                                <dt>{{$routes->address}}</dt>--}}
{{--                                <dd>{{$job_details->amount}}</dd>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}

                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@^3.1/dist/js/adminlte.min.js"></script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=593e73fd-5aa1-4a2f-ae4b-ec963b008e1a&lang=ru_RU" type="text/javascript"></script>
{{--    <script src="https://api-maps.yandex.ru/v3/?apikey=593e73fd-5aa1-4a2f-ae4b-ec963b008e1a&lang=en_US" type="text/javascript"></script>--}}


    @php
       // $all_routess = json_encode($all_routes);


    @endphp
    <script>
        $(document).ready(function() {
            $('.sheet-header').on('click', function() {
                var content = $(this).next('.sheet-content');
                content.slideToggle(300);
                $(this).find('.toggle-icon').toggleClass('expanded');
            });
        });
    </script>


    <script type="text/javascript">
        ymaps.ready(init);
        function init() {
            var myMap = new ymaps.Map("map", {
                center: [{{$get_job->address_from_lat}}, {{$get_job->address_from_long}}], // Центр карты
                zoom: 10
            });

            var backendData = @json($all_routes);

            // Проверка и преобразование данных
            var dataArrays = Array.isArray(backendData) ? backendData : JSON.parse(backendData);
            let datas = [];

            var points = dataArrays.map(function(point) {
                datas.push([parseFloat(point.lat), parseFloat(point.long)]);
            });

            // Получаем только первую и последнюю точки для маршрута
            let referencePoints = [];
            if (datas.length > 1) {
                referencePoints = [datas[0], datas[datas.length - 1]];
            } else if (datas.length === 1) {
                referencePoints = [datas[0]];
            }

            // Создаем и добавляем метки для точек А и Б
            var pointA = new ymaps.Placemark(referencePoints[0], {
                hintContent: 'Точка А',
                balloonContent: 'Точка А'
            }, {
                preset: 'islands#manIcon',
                draggable: true
            });

            var pointB = new ymaps.Placemark(referencePoints[1], {
                hintContent: 'Точка Б',
                balloonContent: 'Точка Б'
            }, {
                preset: 'islands#finishFlagIcon',
                draggable: true
            });

            myMap.geoObjects.add(pointA).add(pointB);

            // Функция обновления маршрута
            function updateRoute() {
                ymaps.route([pointA.geometry.getCoordinates(), pointB.geometry.getCoordinates()], {
                    routingMode: 'auto' // Режим маршрута (авто)
                }).then(function (route) {
                    myMap.geoObjects.add(route);
                }, function (error) {
                    alert('Возникла ошибка: ' + error.message);
                });
            }

            // Обновляем маршрут при перемещении меток
            pointA.events.add('dragend', updateRoute);
            pointB.events.add('dragend', updateRoute);

            // Инициализируем маршрут
            updateRoute();
        }
    </script>


{{--    <script type="text/javascript">--}}
{{--        ymaps.ready(init);--}}
{{--        function init() {--}}
{{--            var myMap = new ymaps.Map("map", {--}}
{{--                center: [{{$get_job->address_from_lat}} ,{{$get_job->address_from_long}}], // Центр карты--}}
{{--                zoom: 10--}}
{{--            });--}}
{{--                                var backendData = @json($all_routes);--}}

{{--                                // Проверка и преобразование данных--}}
{{--                                var dataArrays = Array.isArray(backendData) ? backendData : JSON.parse(backendData);--}}
{{--            let datas = [];--}}

{{--            var points = dataArrays.map(function(point) {--}}
{{--                datas.push([parseFloat(point.lat), parseFloat(point.long)]);--}}
{{--            });--}}

{{--            let referencePoints = [];--}}
{{--            if (datas.length > 1) {--}}
{{--                referencePoints = [datas[0], datas[datas.length - 1]];--}}
{{--            } else if (datas.length === 1) {--}}
{{--                referencePoints = [datas[0]];--}}
{{--            }--}}

{{--            var multiRoute = new ymaps.multiRouter.MultiRoute({--}}
{{--                referencePoints: datas--}}
{{--                ,--}}
{{--                params: {--}}
{{--                    routingMode: 'auto'--}}
{{--                }--}}
{{--            }, {--}}
{{--                boundsAutoApply: true // Автоматическое масштабирование карты--}}
{{--            });--}}

{{--            myMap.geoObjects.add(multiRoute);--}}


{{--        }--}}
{{--    </script>--}}














@endsection
