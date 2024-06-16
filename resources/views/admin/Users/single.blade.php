@extends('admin.layouts.default')
@section('title')
    Пользователь
@endsection

@section('page_css')
    <link rel="stylesheet" href="{{asset('admin/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/ekko-lightbox/ekko-lightbox.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endsection

@section('content')
    <style>
        /* Исправление стилей для Select2 */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container .select2-dropdown {
            z-index: 9999;
        }
        .select2-dropdown {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
        }
    </style>
{{--    @if(session('created'))--}}
{{--        <div class="alert alert-success alert-dismissible fade show" role="alert">--}}
{{--            <strong>Добавление успешно завершено</strong>--}}
{{--            <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                <span aria-hidden="true">&times;</span>--}}
{{--            </button>--}}
{{--        </div>--}}
{{--    @endif--}}
    @php
        if($get->job_category_id  == 3){
             $activeTab = "#user";
             }else{
                $activeTab = session('active_tab', '#user'); // Значение по умолчанию - #user
             }

         $myTabs = [
         '#user',
         '#car',
         '#payments'
         ];
         if (!in_array($activeTab, $myTabs)) {
             $activeTab = '#user';
         }


    @endphp

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link{{ $activeTab == '#user' ? ' active' : '' }}" id="user-tab" data-toggle="tab" href="#user" role="tab" aria-controls="user" aria-selected="{{ $activeTab == '#user' ? 'true' : 'false' }}">Пользователь</a>
        </li>
        @if($get->job_category_id  != 3)
        <li class="nav-item" role="presentation">
            <a class="nav-link{{ $activeTab == '#car' ? ' active' : '' }}" id="car-tab" data-toggle="tab" href="#car" role="tab" aria-controls="car" aria-selected="{{ $activeTab == '#car' ? 'true' : 'false' }}">Автомобиль</a>
        </li>
        @endif
        <li class="nav-item" role="presentation">
            <a class="nav-link{{ $activeTab == '#payments' ? ' active' : '' }}" id="payments-tab" data-toggle="tab" href="#payments" role="tab" aria-controls="payments" aria-selected="{{ $activeTab == '#payments' ? 'true' : 'false' }}">Выплаты</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show {{ $activeTab == '#user' ? ' active' : '' }}" id="user" role="tabpanel" aria-labelledby="user-tab">
            <div class="card card-primary" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <div style="display: flex; justify-content: space-between">
                        <h3 class="card-title">Пользватель</h3>
                        @if(isset($get->contractor_profile_id))
                            <h3 class="card-title">ID YANDEX - {{$get->contractor_profile_id}}</h3>
                        @endif
                    </div>
                </div>



                <form  id="user_data" action="   @if($get->contractor_profile_id == null)   {{route('create_user_in_yandex')}} @else {{route('update_user')}} @endif" method="post" class="forms-sample" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$get->id}}">
                    <div style="display: flex; justify-content: space-between">
                        @if($get->contractor_profile_id == null)


{{--                            <script>--}}
{{--                                $(document).ready(function() {--}}
{{--                                    // Сделать все поля формы не редактируемыми, кроме телефона и статуса работы--}}
{{--                                    $('#user_data .form-control').each(function() {--}}
{{--                                        if ($(this).attr('name') !== 'phone' && $(this).attr('name') !== 'work_status') {--}}
{{--                                            $(this).prop('disabled', true);--}}
{{--                                        }--}}
{{--                                    });--}}
{{--                                });--}}
{{--                            </script>--}}
                        @endif
                        <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">

                            @if($get->contractor_profile_id == null)
                                <div class="form-group" bis_skin_checked="1">
                                    <label>Тип Работы</label>
                                    <select name="job_category_ids" class="form-control">
                                        @foreach($get_job_category as $job_category)

                                        <option @if($get->job_category_id == $job_category->id || old('job_category_ids') ==$job_category->id ) selected @endif value="{{$job_category->id}}">{{$job_category->name}}</option>
                                            @endforeach
                                    </select>
                                </div>

                            @endif
                            <div class="form-group">
                                @php
                                    $region_park_id =$get->park->region->id??null;
                                @endphp
                                <label for="">Регион</label>
                                <select  name="region_id" class="form-control select2" style="width: 100%;">
                                    @foreach ($get_regions as $region)
                                        @if($region_park_id == $region->id || old('region_id') == $region->id)
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
                                            data-inputmask="'mask': '+7 (999) 999-99-99'" data-mask required placeholder="+7 (999) 999-99-99" value="{{old('phone')??$get->phone }}">
                                    @error('phone')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Имя @if($get->sended_in_yandex_status == 0 )&nbsp;&nbsp;&nbsp;&nbsp; <span style="color: red"> {{$get->scanning_name}}</span> @endif</label>
                                <input     type="text" class="form-control" id=""  name="name" value="{{old('name')??$get->name}}">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Фамилия @if($get->sended_in_yandex_status == 0)&nbsp;&nbsp;&nbsp;&nbsp;   <span style="color: red">{{$get->scanning_surname}}</span>@endif </label>
                                <input     type="text" class="form-control" id=""  name="surname" value="{{old('surname')??$get->surname}}">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Отчетво  @if($get->sended_in_yandex_status == 0)&nbsp;&nbsp;&nbsp;&nbsp; <span style="color: red">{{$get->scanning_middle_name}}</span>@endif </label>
                                <input     type="text" class="form-control" id=""  name="middle_name" value="{{old('middle_name')??$get->middle_name}}">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Дата Рождения  @if($get->sended_in_yandex_status == 0)&nbsp;&nbsp;&nbsp;&nbsp; <span style="color: red"> {{$get->scanning_birth_date}}</span>@endif</label>
                                <input     type="date" class="form-control" id=""  name="birth_date" value="{{old('birth_date')??\Carbon\Carbon::parse($get->date_of_birth)->format('Y-m-d')}}">
                            </div>
                        </div>
                            @if(isset($get->contractor_profile_id))
                            <input type="hidden" name="send_in_yandex" value="true">
                            @endif
                        <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">
                            <div class="form-group">
                                <label for="year">Стаж с</label>
                                <select  name="driver_license_experience_total_since_date" class="form-control select2" style="width: 100%;">
                                    @for ($year = now()->year; $year >= 1980; $year--)
                                        @if($get->driver_license_experience_total_since_date == $year || old('driver_license_experience_total_since_date')==$year)
                                            <option selected value="{{ $year }}">{{ $year }}</option>
                                        @else
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endif

                                    @endfor
                                </select>
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Номер ВУ</label>
                                <input     type="text" class="form-control" id=""  name="driver_license_number" value="{{old('driver_license_number')??$get->driver_license_number}}">
                            </div>
                            <div class="form-group">
                                <label for="">Страна Выдачи</label>
                                <select  name="driver_license_country_id" class="form-control select2" style="width: 100%;">
                                    @foreach ($get_country_drive_licenze as $country)
                                        @if($get->driver_license_country_id == $country->id || old('driver_license_country_id') == $country->id )
                                            <option selected value="{{ $country->id }}">{{$country->name}}</option>
                                        @else
                                            <option value="{{ $country->id }}">{{$country->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Дата выдачи</label>
                                <input     type="date" class="form-control" id="" max="{{\Carbon\Carbon::now()->format('Y-m-d')}}" min="{{\Carbon\Carbon::now()->subYears(20)->format('Y-m-d')}}"  name="driver_license_issue_date" value="{{old('driver_license_issue_date')??\Carbon\Carbon::parse($get->driver_license_issue_date)->format('Y-m-d')}}">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Действует до</label>
                                <input     type="date" class="form-control" id=""
{{--                                           min="{{\Carbon\Carbon::now()->format('Y-m-d')}}"--}}
                                           max="{{\Carbon\Carbon::now()->addYears(20)->format('Y-m-d')}}"   name="driver_license_expiry_date" value="{{ old('driver_license_expiry_date') ?? Carbon\Carbon::parse($get->driver_license_expiry_date)->format('Y-m-d') }}">
                            </div>

                            @if($get->contractor_profile_id != null && $get->work_status != null)
                            <div class="row" bis_skin_checked="1">
                                <div class="col-sm-12" bis_skin_checked="1">
                                    <div class="form-group" bis_skin_checked="1">
                                        <label>Статус Работы</label>
                                        <select class="form-control" name="work_status">
                                            @foreach($get_yandex_worrk_status as $work_status)
                                                @if($work_status->name == $get->work_status || old('work_status') == $work_status->name)
                                            <option selected value="{{$work_status->name}}">{{$work_status->show_name}}</option>
                                               @else
                                                    <option value="{{$work_status->name}}">{{$work_status->show_name}}</option>

                                                @endif
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif
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
                    <div class="card-footer" bis_skin_checked="1" >
                        <div style="display: flex; justify-content: space-between">
                        @if(!isset($get->contractor_profile_id))
                            <button type="submit" class="btn btn-primary">Потверждения заявки</button>
                                <a href="{{route('delete_new_user', $get->id)}}" style="max-width: 20%" class="btn btn-block btn-danger btn-lg" >Удалить Заявку</a>
                        @else
                            <button type="submit" class="btn btn-primary">Сохранить </button>
                            @if($get->work_status != 'fired')
                                <a href="{{route('add_user_in_archive', $get->id)}}" style="max-width: 20%" class="btn btn-block btn-danger btn-lg" >Архивировать</a>
                            @endif
                                @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-pane fade show{{ $activeTab == '#car' ? ' active' : '' }}" id="car" role="tabpanel" aria-labelledby="car-tab">
            @if($get_car != null)
                <div class="card card-primary" bis_skin_checked="1">
                    <div class="card-header" bis_skin_checked="1">
                        <div style="display: flex; justify-content: space-between">
                            <h3 class="card-title">Данные Машины</h3>
                            <h3 class="card-title">ID YANDEX - {{$get_car->yandex_car_id }}</h3>
                        </div>
                    </div>
                    <form action="{{route('update_car')}}" id="carForm" method="post" class="forms-sample" enctype="multipart/form-data">

                        @csrf

                        <input type="hidden" name="car_id" value="{{$get_car->id}}">
                        <div style="display: flex; justify-content: space-between">
                            <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">

                                @if($get_car->status != null)



                                    <div class="form-group">
                                        <label>Статус</label>
                                        <select name="car_work_status" class="form-control">
                                            @foreach($car_working_status as $key => $car_work_stat)
                                            <option @if($get_car->status == $key) selected @endif value="{{$key}}">{{$car_work_stat}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                @endif

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
                                <div class="form-group">
                                    <label>Тарифы</label>
                                    <div class="select2-primary">
                                        <select name="car_category_id[]"  class="select2" multiple="multiple" data-placeholder="Тарифы" data-dropdown-css-class="select2-primary" style="width: 100%;">
                                            @foreach($get_car_category as $car_category)
                                                @if($get_car->categories->contains($car_category->id))
                                            <option         selected  value="{{$car_category->id}}"> {{$car_category->show_name}}</option>
                                                    @else
                                                    <option  value="{{$car_category->id}}"> {{$car_category->show_name}}</option>
                                                @endif
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Брендинг </label>
                                    <div class="select2-primary">
                                        <select name="car_amenities[]" class="select2" multiple="multiple" data-placeholder="Брендинг" data-dropdown-css-class="select2-primary" style="width: 100%;">
                                            @foreach($get_car_amenities as $car_amenities )
                                                <option @if($get_car->amenities->contains($car_amenities->id)) selected @endif value="{{$car_amenities->id}}"> {{$car_amenities->show_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
                                                <button type="submit" class="btn btn-primary">Обновить  </button>
                        </div>
                    </form>
                </div>

            @else
                <div class="card card-primary">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between">
                            <h3 class="card-title">Нет привязанных машин</h3>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="tab-pane fade show{{ $activeTab == '#payments' ? ' active' : '' }}" id="payments" role="tabpanel" aria-labelledby="payments-tab">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Выплаты</h3>
                </div>
                <div class="card-body">
                    <table id="paymentsTable" class="table table-bordered table-striped datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Дата</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $payments =[];
                        @endphp
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->date }}</td>
                                <td>{{ $payment->amount }}</td>
                                <td>{{ $payment->status }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page_scripts')
    <script src="{{asset('admin/plugins/moment/moment.min.js')}}"></script>
    <script src="{{asset('admin/plugins/inputmask/jquery.inputmask.min.js')}}"></script>
    <script src="{{asset('admin/plugins/ekko-lightbox/ekko-lightbox.min.js')}}"></script>
    <script src="{{asset('admin/plugins/filterizr/jquery.filterizr.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Подключаем скрипт для уведомлений Toasts (AdminLTE) -->
    <script src="https://adminlte.io/themes/v3/plugins/toastr/toastr.min.js"></script>
{{--    ce08d4de07437f934055c92907de1ec6--}}
{{--    @if($get->contractor_profile_id != null)--}}
    <script>
        $(document).ready(function() {
            // Сделать все поля формы только для чтения, кроме статуса автомобиля и других исключений
            $('#carForm').find('input, select, textarea').not('[name="_token"]').not('[name="car_category_id[]"]').not('[name="car_work_status"]').not('[name="car_amenities[]"]').each(function() {
                if ($(this).is('select')) {
                    $(this).prop('disabled', true); // Для select используем disabled
                } else {
                    $(this).prop('readonly', true); // Для input и textarea используем readonly
                }
            });
        });
    </script>
{{--    @endif--}}


    @if($get->contractor_profile_id != null)
        <script>
            $(document).ready(function() {
                // Сделать все поля формы только для чтения, кроме телефона и статуса работы
                $('#user_data .form-control').not('[name="_token"]').not('[name="phone"]').not('[name="work_status"]').each(function() {
                    var originalName = $(this).attr('name');
                    if ($(this).is('select')) {
                        // Скрытый инпут для select
                        $('<input>').attr({
                            type: 'hidden',
                            name: originalName,
                            value: $(this).val()
                        }).appendTo('#user_data');
                        $(this).prop('disabled', true); // Для select используем disabled
                    } else {
                        // Скрытый инпут для input и textarea
                        $('<input>').attr({
                            type: 'hidden',
                            name: originalName,
                            value: $(this).val()
                        }).appendTo('#user_data');
                        $(this).prop('readonly', true); // Для input и textarea используем readonly
                    }
                });
            });
        </script>
    @endif
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
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        var modelSelect = $("select[name='model_id']");
                        modelSelect.empty();
                        $.each(response.data, function(key, model) {
                            modelSelect.append(new Option(model.name, model.id));
                        });
                        modelSelect.trigger('change');
                    }
                });
            });

            $('.select2').select2();

            $('.datatable').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

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
        });
    </script>
    @if (session('created'))
        <script>
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Уведомление',
                subtitle: "{{ session('created') }}",
                body:  "{{ session('created') }}" ,
                autohide: true,
                delay: 10000
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Уведомление',
                subtitle: '',
                body:  "{{ session('error') }}",
                autohide: true,
                delay: 10000
            });
        </script>
    @endif
@endsection
