@extends('admin.layouts.default')
@section('title')
    Настройки
@endsection

@section('content')


    @error('newpassword')

    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{$message}}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @enderror
    @error('oldpassword')
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{$message}}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @enderror
    @if(session('succses'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{'Вы удачно изменили пароль'}}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('nopassword'))

        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{'Неправильный старый пароль'}}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif



    <div class="card card-primary" bis_skin_checked="1">
        <div class="card-header" bis_skin_checked="1">
            <h3 class="card-title">Редактирование пароля</h3>
        </div>


        <form action="{{route('updatePassword')}}" method="post" class="forms-sample">
            @csrf
            <div class="card-body" bis_skin_checked="1">
            <div class="form-group">
                <label for="exampleInputPassword1">Старый пароль</label>
                <input value="{{ old('oldpassword') }}" name="oldpassword" type="password" class="form-control" id="exampleInputPassword1" placeholder="Пароль">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Новый пароль</label>
                <input  name="newpassword" type="password" class="form-control" id="exampleInputPassword1" placeholder="Пароль">
            </div>

            <div class="form-group">
                <label for="exampleInputPassword1">Повторите новый пароль</label>
                <input  name="newpassword_confirmation" type="password" class="form-control" id="exampleInputPassword1" placeholder="Пароль">
            </div>

                <div class="card-footer" bis_skin_checked="1">
                    <button type="submit" class="btn btn-primary">Сохранить </button>
            </div>
            </div>
        </form>
    </div>

@endsection