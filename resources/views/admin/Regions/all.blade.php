@extends('admin.layouts.default')
@section('title')
    Регионы
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">


                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Регион</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                                                            <table id="myTable" class="display">
                                                                <thead>
                                                                <tr>
                                                                    <th>id</th>
                                                                    <th>Название</th>
                                                                    <th>Ключ</th>
{{--                                                                                                            <th>Город</th>--}}
                                                                                                            <th>Номер</th>
                                                                    <th>Действия</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($get as $item)
                                                                    <tr>
                                                                        <td>{{$item->id}}</td>
                                                                        <td>{{$item->name}}</td>

                                                                        @if($item->key_id != null)
                                                                        <td><a href="{{route('single_page_key', $item->key_id)}}"> {{ $item->key->name??null}}</a></td>
                                                                        @else
                                                                            <td> {{ $item->key->name??null}}</td>
                                                                            @endif
{{--                                                                                                                    <td>{{$item->city->name}} </td>--}}
                                                                                                                    <td>{{$item->phone}} </td>
                                                                        <td style="    width: 10px;"><a href="{{route('single_page_region', $item->id)}}" class="btn btn-block btn-primary">Редактирование</a> </td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            var table = $('#myTable').DataTable({
                "theme": "black",
                "order": [[0, "asc"]],
                "language": {
                    "sProcessing":   "Подождите...",
                    "sLengthMenu":   "Показать _MENU_ записей",
                    "sZeroRecords":  "Записи отсутствуют.",
                    "sInfo":         "Показаны записи с _START_ по _END_ из _TOTAL_ записей",
                    "sInfoEmpty":    "Записи с 0 до 0 из 0 записей",
                    "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
                    "sInfoPostFix":  "",
                    "sSearch":       "Поиск:",
                    "sUrl":          "",
                    "oPaginate": {
                        "sFirst":    "Первая",
                        "sPrevious": "Предыдущая",
                        "sNext":     "Следующая",
                        "sLast":     "Последняя"
                    },
                    "oAria": {
                        "sSortAscending":  ": активировать для сортировки столбца по возрастанию",
                        "sSortDescending": ": активировать для сортировки столбца по убыванию"
                    }
                }
            });

        });
    </script>
@endsection
