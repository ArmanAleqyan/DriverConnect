@extends('admin.layouts.default')
@section('title')
    Заявки
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{asset('admin/plugins/toastr/toastr.min.css')}}">
    @endsection
@section('content')
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Вы действительно хотите удалить эту запись?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Удалить</button>
                </div>
            </div>
        </div>
    </div>


    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div style="display: flex; justify-content: space-between;align-items: center">
                            <h3 class="card-title">Заявки</h3>
                                <a style="max-width: 15%" class="btn btn-block btn-warning" href="{{route('create_user_page')}}">Добавить</a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">



                            <table id="myTable" class="display">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Имя</th>
                                    <th>Фамилия</th>
                                    <th>Номер</th>
                                    <th>Регион</th>
                                    <th style="width: 49px">Действия</th>
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
                                        <td style="display: flex; justify-content: space-between; align-items: center;">
                                            <a style="margin: 0 2px; display: flex; justify-content: center; align-items: center;" href="{{route('single_page_user', $item->id)}}">
                                                <i class="nav-icon fa fa-cogs"></i>
                                            </a>
                                            <a style="margin: 0 2px; display: flex; justify-content: center; align-items: center;" href="{{ route('delete_new_user', $item->id) }}" class="delete-button">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
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

    @endsection

@section('page_scripts')
    <!-- Подключаем jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Подключаем скрипт DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- Подключаем скрипт Bootstrap (для работы модального окна и уведомлений) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Подключаем скрипт для уведомлений Toasts (AdminLTE) -->
    <script src="https://adminlte.io/themes/v3/plugins/toastr/toastr.min.js"></script>

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

            var deleteUrl = '';
            var deleteRow = null;

            document.querySelectorAll('.delete-button').forEach(function(button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    deleteUrl = event.currentTarget.getAttribute('href');
                    deleteRow = event.currentTarget.closest('tr');
                    $('#deleteModal').modal('show');
                });
            });

            document.getElementById('confirmDelete').addEventListener('click', function () {
                if (deleteUrl) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'GET', // Измените на 'DELETE', если используете метод DELETE
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                table.row(deleteRow).remove().draw(false);
                                $('#deleteModal').modal('hide');
                                $(document).Toasts('create', {
                                    class: 'bg-danger',
                                    title: 'Уведомление',
                                    subtitle: 'Сейчас',
                                    body: 'Запись успешно удалена.',
                                    autohide: true,
                                    delay: 10000
                                });
                            } else {
                                alert('Ошибка при удалении записи.');
                            }
                        },
                        error: function() {
                            alert('Ошибка при удалении записи.');
                        }
                    });
                }
            });
        });
    </script>
@endsection