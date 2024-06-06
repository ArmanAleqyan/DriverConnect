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
                            @php
                                $activeTab = session('active_tab', '#new-users'); // Значение по умолчанию - #new-users
                                $myTabs = [
                                    '#new-users',
                                    '#active-users',
                                    '#inactive-users',
                                    '#archived-users'
                                ];
                                if (!in_array($activeTab, $myTabs)) {
                                    $activeTab = '#new-users';
                                }
                            @endphp

                            <ul class="nav nav-tabs" id="userTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link{{ $activeTab == '#new-users' ? ' active' : '' }}" id="new-users-tab" data-toggle="tab" href="#new-users" role="tab" aria-controls="new-users" aria-selected="{{ $activeTab == '#new-users' ? 'true' : 'false' }}">Новые (до 50 поездок)</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link{{ $activeTab == '#active-users' ? ' active' : '' }}" id="active-users-tab" data-toggle="tab" href="#active-users" role="tab" aria-controls="active-users" aria-selected="{{ $activeTab == '#active-users' ? 'true' : 'false' }}">Активные (более 50 поездок)</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link{{ $activeTab == '#inactive-users' ? ' active' : '' }}" id="inactive-users-tab" data-toggle="tab" href="#inactive-users" role="tab" aria-controls="inactive-users" aria-selected="{{ $activeTab == '#inactive-users' ? 'true' : 'false' }}">Отток (2 недели без поездок)</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link{{ $activeTab == '#archived-users' ? ' active' : '' }}" id="archived-users-tab" data-toggle="tab" href="#archived-users" role="tab" aria-controls="archived-users" aria-selected="{{ $activeTab == '#archived-users' ? 'true' : 'false' }}">Архив</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="userTabsContent">
                                <div class="tab-pane fade {{ $activeTab == '#new-users' ? ' show active' : '' }}" id="new-users" role="tabpanel" aria-labelledby="new-users-tab">
                                    <table class="display">
                                        <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Имя</th>
                                            <th>Фамилия</th>
                                            <th>Номер</th>
                                            <th>Регион</th>
                                            <th>Гос Номер</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($new_users as $item)
                                            <tr @if($item->sended_in_yandex_status == 0) style="color: red" @endif>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->surname }}</td>
                                                <td>{{ $item->phone }}</td>
                                                <td>{{ $item->park->region->name ?? null }}</td>
                                                @if(!isset($item->car[0]->registration_cert) || $item->car[0]->registration_cert == null)
                                                    <td>Courier</td>
                                                @else
                                                    <td>{{ $item->car[0]->callsign ?? null }}</td>
                                                @endif
                                                <td style="display: flex; justify-content: center; align-items: center;">            <a style="margin: 0 2px; display: flex; justify-content: center; align-items: center;" href="{{route('single_page_user', $item->id)}}">
                                                        <i class="nav-icon fa fa-cogs"></i>
                                                    </a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade {{ $activeTab == '#active-users' ? ' show active' : '' }}" id="active-users" role="tabpanel" aria-labelledby="active-users-tab">
                                    <table class="display">
                                        <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Имя</th>
                                            <th>Фамилия</th>
                                            <th>Номер</th>
                                            <th>Регион</th>
                                            <th>Гос Номер</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($active_users as $item)
                                            <tr @if($item->sended_in_yandex_status == 0) style="color: red" @endif>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->surname }}</td>
                                                <td>{{ $item->phone }}</td>
                                                <td>{{ $item->park->region->name ?? null }}</td>
                                                @if(!isset($item->car[0]->registration_cert) || $item->car[0]->registration_cert == null)
                                                    <td>Courier</td>
                                                @else
                                                    <td>{{ $item->car[0]->callsign ?? null }}</td>
                                                @endif
                                                <td style="display: flex; justify-content: center; align-items: center;">            <a style="margin: 0 2px; display: flex; justify-content: center; align-items: center;" href="{{route('single_page_user', $item->id)}}">
                                                        <i class="nav-icon fa fa-cogs"></i>
                                                    </a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade {{ $activeTab == '#inactive-users' ? ' show active' : '' }}" id="inactive-users" role="tabpanel" aria-labelledby="inactive-users-tab">
                                    <table class="display">
                                        <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Имя</th>
                                            <th>Фамилия</th>
                                            <th>Номер</th>
                                            <th>Регион</th>
                                            <th>Гос Номер</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($inactive_users as $item)
                                            <tr @if($item->sended_in_yandex_status == 0) style="color: red" @endif>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->surname }}</td>
                                                <td>{{ $item->phone }}</td>
                                                <td>{{ $item->park->region->name ?? null }}</td>
                                                @if(!isset($item->car[0]->registration_cert) || $item->car[0]->registration_cert == null)
                                                    <td>Courier</td>
                                                @else
                                                    <td>{{ $item->car[0]->callsign ?? null }}</td>
                                                @endif
                                                <td style="display: flex; justify-content: center; align-items: center;">            <a style="margin: 0 2px; display: flex; justify-content: center; align-items: center;" href="{{route('single_page_user', $item->id)}}">
                                                        <i class="nav-icon fa fa-cogs"></i>
                                                    </a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade {{ $activeTab == '#archived-users' ? ' show active' : '' }}" id="archived-users" role="tabpanel" aria-labelledby="archived-users-tab">
                                    <table class="display">
                                        <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Имя</th>
                                            <th>Фамилия</th>
                                            <th>Номер</th>
                                            <th>Регион</th>
                                            <th>Гос Номер</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($archived_users as $item)
                                            <tr @if($item->sended_in_yandex_status == 0) style="color: red" @endif>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->surname }}</td>
                                                <td>{{ $item->phone }}</td>
                                                <td>{{ $item->park->region->name ?? null }}</td>
                                                @if(!isset($item->car[0]->registration_cert) || $item->car[0]->registration_cert == null)
                                                    <td>Courier</td>
                                                @else
                                                    <td>{{ $item->car[0]->callsign ?? null }}</td>
                                                @endif
                                                <td style="display: flex; justify-content: center; align-items: center;">            <a style="margin: 0 2px; display: flex; justify-content: center; align-items: center;" href="{{route('single_page_user', $item->id)}}">
                                                        <i class="nav-icon fa fa-cogs"></i>
                                                    </a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
            $('.tab-pane table').each(function() {
                $(this).DataTable({
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
        });
    </script>
@endsection
