@extends('admin.layouts.default')
@section('title')
    Редактирование ключ
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
            <h3 class="card-title">Редактирование ключа</h3>
        </div>


        <form action="{{route('update_key')}}" method="post" class="forms-sample" enctype="multipart/form-data">
            @csrf
            <div class="card-body" bis_skin_checked="1">
                <div class="form-check" bis_skin_checked="1">
                    <label class="form-check-label">
                        <input name="default" type="checkbox" class="form-check-input" @if($get->default == 1) checked=""  @endif> Default <i class="input-helper"></i></label>
                </div>

                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleSelectGender">Регион</label>
                    <select  name="region_id" class="form-control" id="exampleSelectGender">
                        @foreach($get_region as $region)
                            @if($get->region_id == $region->id)
                                <option value="{{$region->id}}"  selected>{{$region->name}}</option>
                            @else
                                <option value="{{$region->id}}"  >{{$region->name}}</option>

                            @endif
                        @endforeach

                    </select>
                </div>
                <input type="hidden" name="key_id" value="{{$get->id}}">
                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleInputName1">Название</label>
                    <input    required type="text" class="form-control" id="exampleInputName1"  name="name" value="{{$get->name}}">
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleInputName1">X_Park_ID</label>
                    <input    type="text" class="form-control" id="exampleInputName1"  name="X_Park_ID" value="{{$get->X_Park_ID}}">
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleInputName1">X_Client_ID</label>
                    <input   type="text" class="form-control" id="exampleInputName1"  name="X_Client_ID" value="{{$get->X_Client_ID}}">
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleInputName1">X_API_Key</label>
                    <input  type="text" class="form-control" id="exampleInputName1"  name="X_API_Key" value="{{$get->X_API_Key}}">
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleSelectGender">Условия Работы</label>
                    <select  name="work_rule_id" class="form-control" id="exampleSelectGender">
                        @foreach($get->work_rule as $work_rule)
                            @if($work_rule->default == 1)
                                <option value="{{$work_rule->id}}"  selected>{{$work_rule->name}}</option>
                            @else
                                <option value="{{$work_rule->id}}"  >{{$work_rule->name}}</option>

                            @endif
                        @endforeach

                    </select>
                </div>
                <div style="display: flex; justify-content: space-between">
                    <button style="max-width: 20%" type="submit" class="btn btn-block btn-primary">Сохранить</button>
                    <a style="max-width: 20%" href="{{route('delete_key', $get->id)}}" class="btn btn-block btn-danger">Удалить</a>
                </div>

            </div>

        </form>
    </div>


@endsection
