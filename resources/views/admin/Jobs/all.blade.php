@extends('admin.layouts.default')
@section('title')
    Поездки
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div style="display: flex; justify-content: space-between">
                                <h3 class="card-title">Поездки</h3>
                                <div style="max-width: 20%">
{{--                                    <a href="" class="btn btn-block bg-gradient-warning">Добавить</a>--}}
                                </div>
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
                                    <th>Адрес подачи</th>
                                    <th>Тип оплаты</th>
                                    <th>Сумма Заказа</th>
                                    <th>Сумма с Комиссией</th>
                                    <th>Статус</th>
                                    <th style="width: 10px">Действия</th>
                                </tr>
                                </thead>
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
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('getData') }}",
                "columns": [
                    { "data": "short_id" },
                    { "data": "user.name" }, // Замените на ваши колонки
                    { "data": "user.surname" },
                    { "data": "address_from" },
                    { "data": "payment_type", orderable: false, searchable: false }, // Новый столбец для типа оплаты
                    { "data": "job_price_with_bonus" },
                    { "data": "job_price_minus_fee" },
                    { "data": "status" },
                    { "data": "action", orderable: false, searchable: false }
                ],
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
