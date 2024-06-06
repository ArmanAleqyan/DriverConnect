@extends('admin.layouts.default')
@section('title')
    FAQ
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
            <h3 class="card-title">FAQ</h3>
        </div>


        <form action="{{route('update_faq')}}" method="post" class="forms-sample" enctype="multipart/form-data">
            @csrf
            <div class="card-body" bis_skin_checked="1">
                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleInputName1">Вопрос</label>
                    <input  required  type="text" class="form-control" id="exampleInputName1"  name="faq" value="{{$get->faq}}">
                </div>


                <input type="hidden" name="faq_id" value="{{$get->id}}">
                <div class="form-group" bis_skin_checked="1">
                    <label for="exampleInputName1">Ответ</label>
                    <textarea  type="text" class="form-control" id="exampleInputName1"  name="replay" >{{$get->replay}}</textarea>
                </div>

            </div>

            <div class="card-footer" bis_skin_checked="1" >
                <div style="display: flex; justify-content: space-between;">
                <button type="submit" class="btn btn-primary">Сохранить </button>
                <a href="{{route('delete_faq', $get->id)}}" type="submit" class="btn  btn-danger ">Удалить </a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.tiny.cloud/1/0rt25rv9jwzdnat56e9phi3bbnmcmxis23keq02k5p5rgmrn/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <script>



        tinymce.init({
            selector: 'textarea',
            plugins: 'emoticons | code undo redo | bold italic underline strikethrough | link  | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat',
            toolbar: 'emoticons | forecolor backcolor | code undo redo | formatselect | bold italic underline strikethrough | link  | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat',
            convert_urls: false,
            {{--images_upload_handler: function (blobInfo, success, failure) {--}}
            {{--    var xhr, formData;--}}
            {{--    xhr = new XMLHttpRequest();--}}
            {{--    xhr.withCredentials = false;--}}
            {{--    xhr.open('POST', url);--}}
            {{--    var token = '{{ csrf_token() }}';--}}
            {{--    xhr.setRequestHeader("X-CSRF-Token", token);--}}
            {{--    xhr.onload = function () {--}}
            {{--        try {--}}
            {{--            var json = JSON.parse(xhr.responseText);--}}

            {{--            if (json && typeof json.location === 'string') {--}}
            {{--                success(json.location);--}}
            {{--            } else {--}}
            {{--                failure('Invalid JSON: ' + xhr.responseText);--}}
            {{--            }--}}
            {{--        } catch (error) {--}}
            {{--            failure('Error parsing JSON: ' + error.message);--}}
            {{--        }--}}
            {{--    };--}}
            {{--    xhr.onerror = function () {--}}
            {{--        failure('Network error occurred');--}}
            {{--    };--}}
            {{--    formData = new FormData();--}}
            {{--    formData.append('file', blobInfo.blob(), blobInfo.filename());--}}
            {{--    xhr.send(formData);--}}
            {{--}--}}
        });

    </script>
@endsection
