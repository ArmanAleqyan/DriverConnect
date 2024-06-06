@extends('admin.layouts.default')
@section('title')
    Рассылки
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">


                    <div class="card">
                        <div class="card-header">
                            <div style="display: flex; justify-content: space-between">
                                <h3 class="card-title">Рассылки</h3>
                                <div style="max-width: 20%">
                                    <a href="" class="btn btn-block bg-gradient-warning">Добавить</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="myTable" class="display">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Тип</th>
                                    <th>Дни</th>
                                    <th>Собшение</th>
                                    {{--                                    <th>Регион</th>--}}
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($get as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>@foreach($types as $key => $value) @if($key == $item->type) {{$value}} @endif @endforeach</td>
                                        <td>{{$item->day}}</td>
                                        <td>{{$item->message}}</td>
                                        {{--                                        <td>{{$item->region->name}}</td>--}}
                                        <td style="    width: 10px;"><a href="" class="btn btn-block btn-primary">Редактирование</a> </td>
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
                "order": [[0, "desc"]],
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
