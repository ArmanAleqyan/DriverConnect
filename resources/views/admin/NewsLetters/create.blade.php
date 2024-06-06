@extends('admin.layouts.default')
@section('title')
    Рассылки
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
            <h3 class="card-title">Рассылки</h3>
        </div>


        <form action="{{route('create_letters')}}" method="post" class="forms-sample" enctype="multipart/form-data">
            @csrf
            <div class="card-body" bis_skin_checked="1">
                <div style="display: flex; gap: 25px">
                    <p>{name}</p>
                    <p>{surname}</p>
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleSelectGender">Тип</label>
                    <select  name="type" class="form-control" id="exampleSelectGender">
                        @foreach($types as $key => $type)
                                <option value="{{$key}}"  selected>{{$type}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleInputName1">Дни</label>
                    <input     type="number" class="form-control" id="exampleInputName1"  name="day" value="">
                </div>

                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleInputName1">Собшения</label>
                    <textarea     type="text" class="form-control" id="exampleInputName1"  name="message" ></textarea>
                </div>
                <div style="display: flex; justify-content: space-between">
                    <button style="max-width: 20%" type="submit" class="btn btn-block btn-primary">Сохранить</button>
{{--                    <a style="max-width: 20%" href="{{route('delete_letters', $get->id)}}" class="btn btn-block btn-danger">Удалить</a>--}}
                </div>

            </div>

        </form>
    </div>


@endsection
