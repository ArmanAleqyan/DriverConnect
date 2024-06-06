@extends('admin.layouts.default')
@section('title')
    Кабинеты
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
    @if(session('wrong_key'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Что то  пошло не так попробуйте немного позже</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="card card-primary" bis_skin_checked="1">
        <div class="card-header" bis_skin_checked="1">
            <h3 class="card-title">Кабинеты </h3>
        </div>


        <form action="{{route('create_key')}}" method="post" class="forms-sample" enctype="multipart/form-data">
            @csrf
            <div class="card-body" bis_skin_checked="1">
                                            <div class="form-check" bis_skin_checked="1">
                                                <label class="form-check-label">
                                                    <input name="default" type="checkbox" class="form-check-input"> Default <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-group" bis_skin_checked="1">
                                                <label for="exampleSelectGender">Регион</label>
                                                <select  name="region_id" class="form-control" id="exampleSelectGender">
                                                    @foreach($get_region as $region)
                                                        <option value="{{$region->id}}">{{$region->name}}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                            <div class="form-group" bis_skin_checked="1">
                                                <label for="exampleInputName1">Название</label>
                                                <input  required  type="text" class="form-control" id="exampleInputName1"  name="name" value="">
                                            </div>
                                            <div class="form-group" bis_skin_checked="1">
                                                <label for="exampleInputName1">X_Park_ID</label>
                                                <input required    type="text" class="form-control" id="exampleInputName1"  name="X_Park_ID" value="">
                                            </div>
                                            <div class="form-group" bis_skin_checked="1">
                                                <label for="exampleInputName1">X_Client_ID</label>
                                                <input required    type="text" class="form-control" id="exampleInputName1"  name="X_Client_ID" value="">
                                            </div>
                                            <div class="form-group" bis_skin_checked="1">
                                                <label for="exampleInputName1">X_API_Key</label>
                                                <input required    type="text" class="form-control" id="exampleInputName1"  name="X_API_Key" value="">
                                            </div>

            </div>

            <div class="card-footer" bis_skin_checked="1">
                <button type="submit" class="btn btn-primary">Сохранить </button>
            </div>
        </form>
    </div>

{{--    <div class="content-wrapper" bis_skin_checked="1">--}}
{{--        <br>--}}
{{--        <br>--}}
{{--        <br>--}}
{{--        <div class="row" bis_skin_checked="1">--}}
{{--            <div class="col-12 grid-margin stretch-card" bis_skin_checked="1">--}}
{{--                <div class="card" bis_skin_checked="1">--}}
{{--                    <div class="card-body" bis_skin_checked="1">--}}
{{--                        @if(session('created'))--}}
{{--                            <div class="alert alert-success alert-dismissible fade show" role="alert">--}}
{{--                                <strong>Добавление успешно завершено</strong>--}}
{{--                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                                    <span aria-hidden="true">&times;</span>--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                        @endif--}}
{{--                        <h4 class="card-title">Кабинеты </h4>--}}
{{--                        <form id="myForm" action="{{route('create_key')}}" method="post" class="forms-sample" enctype="multipart/form-data">--}}
{{--                            @csrf--}}
{{--                            <div class="form-check" bis_skin_checked="1">--}}
{{--                                <label class="form-check-label">--}}
{{--                                    <input name="default" type="checkbox" class="form-check-input"> Default <i class="input-helper"></i></label>--}}
{{--                            </div>--}}
{{--                            <div class="form-group" bis_skin_checked="1">--}}
{{--                                <label for="exampleSelectGender">Регион</label>--}}
{{--                                <select style="color: white" name="region_id" class="form-control" id="exampleSelectGender">--}}
{{--                                    @foreach($get_region as $region)--}}
{{--                                        <option value="{{$region->id}}">{{$region->name}}</option>--}}
{{--                                    @endforeach--}}

{{--                                </select>--}}
{{--                            </div>--}}
{{--                            <div class="form-group" bis_skin_checked="1">--}}
{{--                                <label for="exampleInputName1">Название</label>--}}
{{--                                <input  required style="color: #e2e8f0"  required type="text" class="form-control" id="exampleInputName1"  name="name" value="">--}}
{{--                            </div>--}}
{{--                            <div class="form-group" bis_skin_checked="1">--}}
{{--                                <label for="exampleInputName1">X_Park_ID</label>--}}
{{--                                <input required  style="color: #e2e8f0"   type="text" class="form-control" id="exampleInputName1"  name="X_Park_ID" value="">--}}
{{--                            </div>--}}
{{--                            <div class="form-group" bis_skin_checked="1">--}}
{{--                                <label for="exampleInputName1">X_Client_ID</label>--}}
{{--                                <input required style="color: #e2e8f0"   type="text" class="form-control" id="exampleInputName1"  name="X_Client_ID" value="">--}}
{{--                            </div>--}}
{{--                            <div class="form-group" bis_skin_checked="1">--}}
{{--                                <label for="exampleInputName1">X_API_Key</label>--}}
{{--                                <input required style="color: #e2e8f0"   type="text" class="form-control" id="exampleInputName1"  name="X_API_Key" value="">--}}
{{--                            </div>--}}
{{--                            <button type="submit" class="btn btn-inverse-success btn-fw">Сохранить</button>--}}

{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}


@endsection
