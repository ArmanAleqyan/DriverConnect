@extends('admin.layouts.default')
@section('title')
    Пользватель
@endsection

@section('page_css')
    <link rel="stylesheet" href="{{asset('admin/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/ekko-lightbox/ekko-lightbox.css')}}">
@endsection
@section('content')
    <style>
        /* Исправление стилей для Select2 */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem; /* Добавление вертикальных отступов для центрирования текста */
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            /*line-height: 1.5rem; !* Установка высоты линии для центрирования текста *!*/
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px; /* Установка высоты стрелки */
        }
        .select2-container .select2-dropdown {
            z-index: 9999; /* Убедитесь, что выпадающий список отображается поверх других элементов */
        }
        .select2-dropdown {
            position: absolute !important; /* Убедитесь, что выпадающий список правильно позиционируется */
            top: 100% !important; /* Корректировка положения выпадающего списка */
            left: 0 !important; /* Выравнивание выпадающего списка по левому краю */
        }
    </style>
    @if(session('created'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Добавление успешно завершено</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="card card-primary" bis_skin_checked="1">
        <div class="card-header" bis_skin_checked="1">
            <div style="display: flex; justify-content: space-between">
                <h3 class="card-title">Пользватель</h3>
                @if(isset($get->contractor_profile_id))
                    <h3 class="card-title">ID YANDEX - {{$get->contractor_profile_id}}</h3>
                @endif
            </div>
        </div>


        <form action="" method="post" class="forms-sample" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_id" value="{{$get->id}}">
            <div style="display: flex; justify-content: space-between">

                <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">
                    <h4>Данные Водителя</h4>
                    <div class="form-group">
                        <label for="">Регион</label>
                        <select  name="region_id" class="form-control select2" style="width: 100%;">
                            @foreach ($get_regions as $region)
                                @if($get->park->region->id??null == $region->id)
                                    <option selected value="{{ $region->id }}">{{$region->name}}</option>
                                @else
                                    <option value="{{ $region->id }}">{{$region->name}}</option>
                                @endif

                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phone">Номер Телефона</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            </div>
                            <input  type="text" class="form-control" id="phone" name="phone"
                                    data-inputmask="'mask': '+7 (999) 999-99-99'" data-mask required placeholder="+7 (999) 999-99-99" value="{{ $get->phone }}">
                            @error('phone')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group" bis_skin_checked="1">
                        <label for="">Имя @if($get->sended_in_yandex_status == 0 )&nbsp;&nbsp;&nbsp;&nbsp; <span style="color: red"> {{$get->scanning_name}}</span> @endif</label>
                        <input     type="text" class="form-control" id=""  name="name" value="{{$get->name}}">
                    </div>
                    <div class="form-group" bis_skin_checked="1">
                        <label for="">Фамилия @if($get->sended_in_yandex_status == 0)&nbsp;&nbsp;&nbsp;&nbsp;   <span style="color: red">{{$get->scanning_surname}}</span>@endif </label>
                        <input     type="text" class="form-control" id=""  name="surname" value="{{$get->surname}}">
                    </div>
                    <div class="form-group" bis_skin_checked="1">
                        <label for="">Отчетво  @if($get->sended_in_yandex_status == 0)&nbsp;&nbsp;&nbsp;&nbsp; <span style="color: red">{{$get->scanning_middle_name}}</span>@endif </label>
                        <input     type="text" class="form-control" id=""  name="middle_name" value="{{$get->middle_name}}">
                    </div>
                    <div class="form-group" bis_skin_checked="1">
                        <label for="">Дата Рождения  @if($get->sended_in_yandex_status == 0)&nbsp;&nbsp;&nbsp;&nbsp; <span style="color: red"> {{$get->scanning_birth_date}}</span>@endif</label>
                        <input     type="date" class="form-control" id=""  name="date_of_birth" value="{{$get->date_of_birth}}">
                    </div>
                </div>
                <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">
                    <div class="form-group">
                        <label for="year">Стаж с</label>
                        <select  name="driver_license_experience_total_since_date" class="form-control select2" style="width: 100%;">
                            @for ($year = now()->year; $year >= 1980; $year--)
                                @if($get->driver_license_experience_total_since_date == $year)
                                    <option selected value="{{ $year }}">{{ $year }}</option>
                                @else
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endif

                            @endfor
                        </select>
                    </div>
                    <div class="form-group" bis_skin_checked="1">
                        <label for="">Номер ВУ</label>
                        <input     type="text" class="form-control" id=""  name="driver_license_number" value="{{$get->driver_license_number}}">
                    </div>
                    <div class="form-group">
                        <label for="">Страна Выдачи</label>
                        <select  name="driver_license_country_id " class="form-control select2" style="width: 100%;">
                            @foreach ($get_country_drive_licenze as $country)
                                @if($get->driver_license_country_id == $country->id)
                                    <option selected value="{{ $country->id }}">{{$country->name}}</option>
                                @else
                                    <option value="{{ $country->id }}">{{$country->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" bis_skin_checked="1">
                        <label for="">Дата выдачи</label>
                        <input     type="date" class="form-control" id="" max="{{\Carbon\Carbon::now()->format('Y-m-d')}}" min="{{\Carbon\Carbon::now()->subYears(20)->format('Y-m-d')}}"  name="driver_license_issue_date" value="{{\Carbon\Carbon::parse($get->driver_license_issue_date)->format('Y-m-d')}}">
                    </div>
                    <div class="form-group" bis_skin_checked="1">
                        <label for="">Действует до</label>
                        <input     type="date" class="form-control" id=""   min="{{\Carbon\Carbon::now()->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->addYears(20)->format('Y-m-d')}}"   name="driver_license_expiry_date" value="{{\Carbon\Carbon::parse($get->driver_license_expiry_date)->format('Y-m-d') }}">
                    </div>
                    @if($get->driver_license_front_photo != null && $get->driver_license_back_photo != null)
                        <div class="card-body" bis_skin_checked="1">
                            <div class="row" bis_skin_checked="1">
                                <div class="col-sm-2" bis_skin_checked="1">
                                    <a href="{{asset('uploads/'.$get->driver_license_front_photo)}}" data-toggle="lightbox" data-title="Передная часть" data-gallery="gallery" bis_size="{&quot;x&quot;:285,&quot;y&quot;:889,&quot;w&quot;:197,&quot;h&quot;:20,&quot;abs_x&quot;:285,&quot;abs_y&quot;:889}">
                                        <img src="{{asset('uploads/'.$get->driver_license_front_photo)}}" class="img-fluid mb-2" alt="white sample" bis_size="{&quot;x&quot;:285,&quot;y&quot;:798,&quot;w&quot;:197,&quot;h&quot;:197,&quot;abs_x&quot;:285,&quot;abs_y&quot;:798}" bis_id="bn_twu1l86gwnohddnxnnrlvi">
                                    </a>
                                </div>
                                <div class="col-sm-2" bis_skin_checked="1">
                                    <a href="{{asset('uploads/'.$get->driver_license_back_photo)}}" data-toggle="lightbox" data-title="Задная часть" data-gallery="gallery" bis_size="{&quot;x&quot;:285,&quot;y&quot;:889,&quot;w&quot;:197,&quot;h&quot;:20,&quot;abs_x&quot;:285,&quot;abs_y&quot;:889}">
                                        <img src="{{asset('uploads/'.$get->driver_license_back_photo)}}" class="img-fluid mb-2" alt="white sample" bis_size="{&quot;x&quot;:285,&quot;y&quot;:798,&quot;w&quot;:197,&quot;h&quot;:197,&quot;abs_x&quot;:285,&quot;abs_y&quot;:798}" bis_id="bn_twu1l86gwnohddnxnnrlvi">
                                    </a>
                                </div>

                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-footer" bis_skin_checked="1">
                <button type="submit" class="btn btn-primary">Сохранить </button>
            </div>
        </form>
    </div>
    @if($get_car != null)
        <div class="card card-primary" bis_skin_checked="1">
            <div class="card-header" bis_skin_checked="1">
                <div style="display: flex; justify-content: space-between">
                    <h3 class="card-title">Данные Машины</h3>
                    <h3 class="card-title">ID YANDEX - {{$get_car->yandex_car_id }}</h3>
                </div>
            </div>
            <form action="" method="post" class="forms-sample" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="car_id" value="{{$get_car->id}}">
                <div style="display: flex; justify-content: space-between">
                    <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">Статус</label>
                            <input     type="text" class="form-control" id=""  name="mark_name" value="{{$get_car->status}}">
                        </div>

                        <div class="form-group">
                            <label for="">Марка</label>
                            <select  name="mark_id" class="form-control select2" style="width: 100%;">
                                @foreach ($get_marks as $mark)
                                    @if($mark->id == $get_car->mark_id)
                                        <option selected value="{{ $mark->id }}">{{$mark->name}}</option>
                                    @else
                                        <option value="{{ $mark->id }}">{{$mark->name}}</option>
                                    @endif
                                @endforeach
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="">Модель</label>
                            <select  name="model_id" class="form-control select2" style="width: 100%;">
                                @foreach ($get_marks->where('id', $get_car->mark_id)->first()->model as $model)
                                    @if($model->id == $get_car->model_id)
                                        <option selected value="{{ $model->id }}">{{$model->name}}</option>
                                    @else
                                        <option value="{{ $model->id }}">{{$model->name}}</option>
                                    @endif
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="">Цвет</label>
                            <select  name="color_id" class="form-control select2" style="width: 100%;">
                                @foreach ($get_colors as $color)
                                    @if($color->name == $get_car->color)
                                        <option selected value="{{ $color->name }}">{{$color->name}}</option>
                                    @else
                                        <option value="{{ $color->name}}">{{$color->name}}</option>
                                    @endif
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="year">Год</label>
                            <select  name="year" class="form-control select2" style="width: 100%;">
                                @for ($year = now()->year; $year >= 1980; $year--)
                                    @if($get_car->year == $year)
                                        <option selected value="{{ $year }}">{{ $year }}</option>
                                    @else
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endif

                                @endfor
                            </select>

                        </div>
                    </div>
                    <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">Гос.номер</label>
                            <input     type="text" class="form-control" id=""  name="callsign" value="{{$get_car->callsign}}">
                        </div>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">VIN</label>
                            <input     type="text" class="form-control" id=""  name="vin" value="{{$get_car->vin}}">
                        </div>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">СТС</label>
                            <input     type="text" class="form-control" id=""  name="registration_cert" value="{{$get_car->registration_cert}}">
                        </div>
                        {{--                        <div class="form-group" bis_skin_checked="1">--}}
                        {{--                            <label for="">Разрешение</label>--}}
                        {{--                            <input     type="text" class="form-control" id=""  name="" value="">--}}
                        {{--                        </div>--}}

                        @if($get_car->car_license_front_photo != null  && $get_car->car_license_back_photo != null)
                            <div class="card-body" bis_skin_checked="1">
                                <div class="row" bis_skin_checked="1">
                                    <div class="col-sm-2" bis_skin_checked="1">
                                        <a href="{{asset('uploads/'.$get_car->car_license_front_photo)}}" data-toggle="lightbox" data-title="Передная часть" data-gallery="gallery" bis_size="{&quot;x&quot;:285,&quot;y&quot;:889,&quot;w&quot;:197,&quot;h&quot;:20,&quot;abs_x&quot;:285,&quot;abs_y&quot;:889}">
                                            <img src="{{asset('uploads/'.$get_car->car_license_front_photo)}}" class="img-fluid mb-2" alt="white sample" bis_size="{&quot;x&quot;:285,&quot;y&quot;:798,&quot;w&quot;:197,&quot;h&quot;:197,&quot;abs_x&quot;:285,&quot;abs_y&quot;:798}" bis_id="bn_twu1l86gwnohddnxnnrlvi">
                                        </a>
                                    </div>
                                    <div class="col-sm-2" bis_skin_checked="1">
                                        <a href="{{asset('uploads/'.$get_car->car_license_back_photo)}}" data-toggle="lightbox" data-title="Задная часть" data-gallery="gallery" bis_size="{&quot;x&quot;:285,&quot;y&quot;:889,&quot;w&quot;:197,&quot;h&quot;:20,&quot;abs_x&quot;:285,&quot;abs_y&quot;:889}">
                                            <img src="{{asset('uploads/'.$get_car->car_license_back_photo)}}" class="img-fluid mb-2" alt="white sample" bis_size="{&quot;x&quot;:285,&quot;y&quot;:798,&quot;w&quot;:197,&quot;h&quot;:197,&quot;abs_x&quot;:285,&quot;abs_y&quot;:798}" bis_id="bn_twu1l86gwnohddnxnnrlvi">
                                        </a>
                                    </div>

                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-footer" bis_skin_checked="1">
                    {{--                    <button type="submit" class="btn btn-primary">Сохранить </button>--}}
                </div>
            </form>
        </div>
    @else
        <div class="card card-primary" bis_skin_checked="1">
            <div class="card-header" bis_skin_checked="1">
                <div style="display: flex; justify-content: space-between">
                    <h3 class="card-title">Нет привязонных машин</h3>
                </div>
            </div>
        </div>
    @endif

@endsection
@section('page_scripts')
    <script src="{{asset('admin/plugins/moment/moment.min.js')}}"></script>
    <script src="{{asset('admin/plugins/inputmask/jquery.inputmask.min.js')}}"></script>
    <script src="{{asset('admin/plugins/ekko-lightbox/ekko-lightbox.min.js')}}"></script>
    <script src="{{asset('admin/plugins/filterizr/jquery.filterizr.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $("#phone").inputmask({
                mask: "+7 (999) 999-99-99",
                placeholder: "+7 (999) 999-99-99",
                showMaskOnHover: false,
                showMaskOnFocus: true,
            });

            $("form").on("submit", function(e) {
                var phone = $("#phone").inputmask("unmaskedvalue");
                if (phone.length !== 10) {
                    e.preventDefault();
                    alert("Пожалуйста, введите номер телефона в правильном формате.");
                }
            });
            $("select[name='mark_id']").change(function() {
                var mark_id = $(this).val();
                $.ajax({
                    url: '{{ url('/api/get_car_model') }}',
                    type: 'GET',
                    data: {
                        mark_id: mark_id,
                        _token: '{{ csrf_token() }}' // Laravel CSRF токен
                    },
                    success: function(response) {
                        console.log(response);
                        var modelSelect = $("select[name='model_id']");
                        modelSelect.empty(); // Очистка текущих опций
                        $.each(response.data, function(key, model) {
                            modelSelect.append(new Option(model.name, model.id));
                        });
                        modelSelect.trigger('change'); // Обновление select2
                    }
                });
            });
        });
    </script>
    <script>

        $('[data-mask]').inputmask()
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

    </script>
    <script>
        $(function () {
            $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox({
                    alwaysShowClose: true
                });
            });

            $('.filter-container').filterizr({gutterPixels: 3});
            $('.btn[data-filter]').on('click', function() {
                $('.btn[data-filter]').removeClass('active');
                $(this).addClass('active');
            });
        })
    </script>
@endsection