@extends('admin.layouts.default')
@section('title')
     Зарегистрированные
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"> Зарегистрированные</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- Добавляем выпадающий список для фильтрации по региону -->
                            {{--                            <div class="mb-3">--}}
                            {{--                                <label for="region-filter" class="form-label">Выберите регион:</label>--}}
                            {{--                                <select id="region-filter" class="form-select">--}}
                            {{--                                    <option value="">Все регионы</option>--}}
                            {{--                                    @foreach($regions as $region)--}}
                            {{--                                        <option value="{{$region->name}}">{{$region->name}}</option>--}}
                            {{--                                    @endforeach--}}
                            {{--                                </select>--}}
                            {{--                            </div>--}}

                            <table id="myTable" class="display">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Имя</th>
                                    <th>Фамилия</th>
                                    <th>Номер</th>
                                    <th>Регион</th>
                                    <th>VIN</th>
                                    <th>Гос Номер</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($get_users as $item)


                                    <tr @if($item->sended_in_yandex_status == 0) style="color: red" @endif>
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{ $item->surname}}</td>
                                        <td>{{$item->phone}} </td>
                                        <td>{{$item->park->region->name??null}} </td>
                                        <td>{{$item->car[0]->vin??null}} </td>
                                        <td>{{$item->car[0]->callsign??null}} </td>
                                        <td><a href="{{route('single_page_user', $item->id)}}" class="btn btn-block btn-primary">Редактирование  </a>  </td>
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
            // Инициализируем DataTables
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

            // Обработчик изменения выбранного региона
            $('#region-filter').on('change', function() {
                var regionId = $(this).val(); // Получаем ID выбранного региона

                // Фильтруем данные в таблице по выбранному региону
                table.column(4).search(regionId).draw();
            });
        });
    </script>
@endsection
