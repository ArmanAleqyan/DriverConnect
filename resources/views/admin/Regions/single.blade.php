@extends('admin.layouts.default')
@section('title')
    Регион
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
            <h3 class="card-title">Редактирование Региона</h3>
        </div>


        <form action="{{route('update_region')}}" method="post" class="forms-sample" enctype="multipart/form-data">
            @csrf
            <div class="card-body" bis_skin_checked="1">
                <div class="form-group" bis_skin_checked="1" data-select2-id="29">
                    <label for="exampleSelectGender">Ключи</label>
                                                    <select  name="key_id" class="form-control" id="exampleSelectGender">
                                                        <option value="" {{ is_null($get->key_id) ? 'selected' : '' }}>Не выбрано</option>
                                                        @foreach($get_keys as $key)
                                                            <option value="{{ $key->id }}" {{ $get->key_id == $key->id ? 'selected' : '' }}>{{ $key->name }}</option>
                                                        @endforeach
                                                    </select>
                </div>
                                            <input type="hidden" name="region_id" value="{{$get->id}}">
                                            <div class="form-group" bis_skin_checked="1">
                                                <label for="exampleInputName1">Название</label>
                                                <input  required   type="text" class="form-control" id="exampleInputName1"  name="name" value="{{$get->name}}">
                                            </div>

            </div>

            <div class="card-footer" bis_skin_checked="1">
                <button type="submit" class="btn btn-primary">Сохранить </button>
            </div>
        </form>
    </div>

@endsection
