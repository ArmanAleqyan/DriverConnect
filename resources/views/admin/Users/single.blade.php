@extends('admin.layouts.default')
@section('title')
Пользватель
@endsection
@section('content')
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
            <h3 class="card-title">ID YANDEX - {{$get->contractor_profile_id}}</h3>
            </div>
        </div>


        <form action="" method="post" class="forms-sample" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_id" value="{{$get->id}}">
            <div style="display: flex; justify-content: space-between">
               
            <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">
                <h4>Данные Водителя</h4>
                <div class="form-group" bis_skin_checked="1">
                    <label for="">Номер Телефона</label>
                    <input     type="text" class="form-control" id=""  name="phone" value="{{$get->phone}}">
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
                <div class="form-group" bis_skin_checked="1">
                    <label for="">Регион</label>
                    <input     type="text" class="form-control" id=""  name="date_of_birth" value="{{$get->park->region->name??null}}">
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="">Статус Работы</label>
                    <input     type="text" class="form-control" id=""  name="date_of_birth" value="{{$get->work_status}}">
                </div>
            </div>
            <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">
                <h4>Данные ВУ</h4>
                @if($get->driver_license_front_photo != null)
                <a target="_blank" href="{{asset('uploads/'.$get->driver_license_front_photo)}}">Передная часть</a> &nbsp;&nbsp;
                @endif
                @if($get->driver_license_back_photo != null)
             <a target="_blank" href="{{asset('uploads/'.$get->driver_license_back_photo)}}">Задная часть</a>
                @endif
                <div class="form-group" bis_skin_checked="1">
                    <label for="">Стаж с </label>
                    <input     type="text" class="form-control" id=""  name="driver_license_experience_total_since_date" value="{{$get->driver_license_experience_total_since_date}}">
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="">Номер ВУ</label>
                    <input     type="text" class="form-control" id=""  name="driver_license_number" value="{{$get->driver_license_number}}">
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="">Страна Выдачи</label>
                    <input     type="text" class="form-control" id=""  name="driver_license_number" value="{{$get->DriverLicenseCountry->name??null}}">
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="">Дата выдачи</label>
                    <input     type="text" class="form-control" id=""  name="driver_license_issue_date" value="{{$get->driver_license_issue_date}}">
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="">Действует до</label>
                    <input     type="text" class="form-control" id=""  name="driver_license_expiry_date" value="{{$get->driver_license_expiry_date}}">
                </div>
            </div>
            </div>

            <div class="card-footer" bis_skin_checked="1">
{{--                <button type="submit" class="btn btn-primary">Сохранить </button>--}}
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
                        <h4>Данные Транспорта</h4>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">Статус</label>
                            <input     type="text" class="form-control" id=""  name="mark_name" value="{{$get_car->status}}">
                        </div>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">Марка</label>
                            <input     type="text" class="form-control" id=""  name="mark_name" value="{{$get_car->mark->name}}">
                        </div>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">Модель</label>
                            <input     type="text" class="form-control" id=""  name="model_name" value="{{$get_car->model->name}}">
                        </div>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">Цвет</label>
                            <input     type="text" class="form-control" id=""  name="color" value="{{$get_car->color}}">
                        </div>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">Год</label>
                            <input     type="text" class="form-control" id=""  name="year" value="{{$get_car->year}}">
                        </div>
                    </div>
                    <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">
                        <h4>Данные СТС</h4>
                        @if($get_car->car_license_front_photo != null)
                            <a target="_blank" href="{{asset('uploads/'.$get_car->car_license_front_photo)}}">Передная часть</a> &nbsp;&nbsp;
                        @endif
                        @if($get_car->car_license_back_photo != null)
                            <a target="_blank" href="{{asset('uploads/'.$get_car->car_license_back_photo)}}">Задная часть</a>
                        @endif
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">Гос.номер</label>
                            <input     type="text" class="form-control" id=""  name="callsign" value="{{$get_car->callsign}}">
                        </div>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">VIN</label>
                            <input     type="text" class="form-control" id=""  name="vin" value="{{$get_car->vin}}">
                        </div>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">Ноиер Кузова</label>
                            <input     type="text" class="form-control" id=""  name="" value="">
                        </div>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">СТС</label>
                            <input     type="text" class="form-control" id=""  name="registration_cert" value="{{$get_car->registration_cert}}">
                        </div>
                        <div class="form-group" bis_skin_checked="1">
                            <label for="">Разрешение</label>
                            <input     type="text" class="form-control" id=""  name="" value="">
                        </div>
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
