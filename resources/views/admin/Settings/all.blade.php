@extends('admin.layouts.default')
@section('title')
    Настройки
@endsection
@section('content')
    @if(session('updated'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Добавление успешно завершено</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @php

                $activeTab = session('active_tab', '#cabinets'); // Значение по умолчанию - #user


         $myTabs = [
         '#cabinets',
         '#contacts',
         '#integrations',
         '##payments',
         '#antifraud',
         '#mailings',
         ];
         if (!in_array($activeTab, $myTabs)) {
             $activeTab = '#cabinets';
         }


    @endphp
    <!-- Навигационные вкладки -->
    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == '#cabinets' ? ' active' : '' }}" id="cabinets-tab" data-toggle="tab" href="#cabinets" role="tab" aria-controls="cabinets" aria-selected="true">Кабинеты</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == '#contacts' ? ' active' : '' }}" id="contacts-tab" data-toggle="tab" href="#contacts" role="tab" aria-controls="contacts" aria-selected="false">Контакты</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == '#integrations' ? ' active' : '' }}" id="integrations-tab" data-toggle="tab" href="#integrations" role="tab" aria-controls="integrations" aria-selected="false">Интеграции</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == '#payments' ? ' active' : '' }}" id="payments-tab" data-toggle="tab" href="#payments" role="tab" aria-controls="payments" aria-selected="false">Выплаты</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == '#antifraud' ? ' active' : '' }}" id="antifraud-tab" data-toggle="tab" href="#antifraud" role="tab" aria-controls="antifraud" aria-selected="false">Антифрод</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == '#mailings' ? ' active' : '' }}" id="mailings-tab" data-toggle="tab" href="#mailings" role="tab" aria-controls="mailings" aria-selected="false">Рассылки</a>
        </li>
    </ul>

    <!-- Контент вкладок -->
    <div class="tab-content" id="settingsTabsContent">
        <div class="tab-pane fade {{ $activeTab == '#cabinets' ? ' show active' : '' }} " id="cabinets" role="tabpanel" aria-labelledby="cabinets-tab">
            <!-- Кабинеты (ключи) -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <div style="display: flex; justify-content: space-between; align-items: center">
                                <h3 class="card-title">Кабинеты</h3>
                                <div style="max-width: 20%">
                                    <a href="{{route('create_key_page')}}" class="btn btn-block bg-gradient-warning">Добавить</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="myTables" class="display">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Название</th>
                                    <th>Регион</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($get_keys as $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->region->name}}</td>
                                        <td style="width: 10px;">
                                            <a href="{{route('single_page_key', $item->id)}}" class="btn btn-block btn-primary">Редактирование</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Контакты -->
        <div class="tab-pane fade {{ $activeTab == '#contacts' ? ' show active' : '' }}" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
            <div class="row">
                <div class="col-md-6" bis_skin_checked="1">
                    <div class="card card-primary collapsed-card" bis_skin_checked="1">
                        <div class="card-header" bis_skin_checked="1">
                            <h3 class="card-title">Способы связи</h3>
                            <div class="card-tools" bis_skin_checked="1">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div style="display: none; padding: 9px!important; " class="card-body"  bis_skin_checked="1" >
                            <form action="{{route('update_whattsap_and_telegram')}}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Номер Телефона</label>
                                        <input type="text" value="{{$get_whattsap_and_telegram->phone}}" name="phone" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Эл.почта</label>
                                        <input type="text" value="{{$get_whattsap_and_telegram->email}}" name="email" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Ватсап</label>
                                        <input type="text" value="{{$get_whattsap_and_telegram->whatsapp}}" name="whatsapp" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Телеграм</label>
                                        <input type="text" value="{{$get_whattsap_and_telegram->telegram}}" name="telegram" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Сохранить</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
                <div class="col-md-6" bis_skin_checked="1">
                    <div class="card card-secondary collapsed-card" bis_skin_checked="1">
                        <div class="card-header" bis_skin_checked="1">
                            <h3 class="card-title">Компания</h3>
                            <div class="card-tools" bis_skin_checked="1">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" bis_skin_checked="1" style="display: none;">
                            <form action="{{route('update_company')}}" method="post">
                             @csrf
                            <div class="form-group" bis_skin_checked="1">
                                <label for="inputStatus">Статус ИП/ООО</label>
                                <select name="company_work_status" id="inputStatus" class="form-control custom-select">
                                    <option @if($get_whattsap_and_telegram->company_work_status == 'ИП') selected @endif value="ИП">ИП</option>
                                    <option @if($get_whattsap_and_telegram->company_work_status == 'ООО') selected @endif value=ООО>ООО</option>
                                </select>
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="inputEstimatedBudget">ИНН</label>
                                <input type="text" name="inn" id="inputEstimatedBudget" class="form-control" value="{{$get_whattsap_and_telegram->inn}}" step="1">
                            </div>
                                <div class="form-group" bis_skin_checked="1">
                                <label for="inputEstimatedBudget">Название компании</label>
                                <input type="text" name="company_name" id="" class="form-control" value="{{$get_whattsap_and_telegram->company_name}}" step="1">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="inputEstimatedBudget">ОГРН</label>
                                <input type="text" name="ogrn" id="" class="form-control" value="{{$get_whattsap_and_telegram->ogrn}}" step="1">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="inputEstimatedBudget">Юридический адрес</label>
                                <input type="text" name="ur_address" id="" class="form-control" value="{{$get_whattsap_and_telegram->ur_address}}" step="1">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="inputEstimatedBudget">Руководитель</label>
                                <input type="text" name="director" id="" class="form-control" value="{{$get_whattsap_and_telegram->director}}" step="1">
                            </div>
                                <div style="padding: 0px !important;" class="card-footer">
                                    <button type="submit" class="btn btn-primary">Сохранить</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
                <div class="col-md-6" bis_skin_checked="1">
                    <div class="card card-secondary collapsed-card" bis_skin_checked="1">
                        <div class="card-header" bis_skin_checked="1">
                            <h3 class="card-title">Реквизиты</h3>
                            <div class="card-tools" bis_skin_checked="1">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" bis_skin_checked="1" style="display: none;">
                            <form action="{{route('update_bank')}}" method="post">
                                @csrf
                                <div class="form-group" bis_skin_checked="1">
                                    <label for="inputEstimatedBudget">Бик</label>
                                    <input type="text" name="bic" id="inputEstimatedBudget" class="form-control" value="{{$get_whattsap_and_telegram->bic}}" step="1">
                                </div>
                                <div class="form-group" bis_skin_checked="1">
                                    <label for="inputEstimatedBudget">Банк</label>
                                    <input type="text" name="bank" id="" class="form-control" value="{{$get_whattsap_and_telegram->bank}}" step="1">
                                </div>
                                <div class="form-group" bis_skin_checked="1">
                                    <label for="inputEstimatedBudget">Кор счет</label>
                                    <input type="text" name="kor_schot" id="" class="form-control" value="{{$get_whattsap_and_telegram->kor_schot}}" step="1">
                                </div>
                                <div style="padding: 0px !important;" class="card-footer">
                                    <button type="submit" class="btn btn-primary">Сохранить</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- Интеграции -->
        <div class="tab-pane fade  {{ $activeTab == '#integrations' ? ' show active' : '' }}" id="integrations" role="tabpanel" aria-labelledby="integrations-tab">
            <div class="row">
                <div class="col-md-12" bis_skin_checked="1">
                    <div class="card card-primary collapsed-card" bis_skin_checked="1">
                        <div class="card-header" bis_skin_checked="1">
                            <h3 class="card-title">Сбербанк</h3>
                            <div class="card-tools" bis_skin_checked="1">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div style="display: none; padding: 9px!important; " class="card-body"  bis_skin_checked="1" >
                            <form action="{{route('update_sberbank_data')}}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Логин</label>
                                        <input type="text" value="{{$get_sberbank__settings->client_login}}" name="client_login" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Пароль</label>
                                        <input type="text" value="{{$get_sberbank__settings->client_password}}" name="client_password" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Клиент ID</label>
                                        <input type="text" value="{{$get_sberbank__settings->client_id}}" name="client_id" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Клиент Secret</label>
                                        <input type="text" value="{{$get_sberbank__settings->client_secret}}" name="client_secret" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Scope</label>
                                        <textarea type="text"  name="scope" class="form-control" id="exampleInputEmail1" placeholder="">{{$get_sberbank__settings->scope}}</textarea>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div style="display: flex; justify-content: space-between">
                                    <button style="max-height: 48px" type="submit" class="btn btn-primary">Сохранить</button>
                                        <div  style="max-width: 30%" >
                                    <a target="_blank" class="btn btn-block btn-warning btn-lg" href="{{route('getAuthorizationCode')}}">Получить Код для  работы</a>
                                    <a target="_blank" class="btn btn-block btn-warning btn-lg" href="{{route('get_swagger')}}">Проверка Работы Сертфиикатов</a>
                                        </div>
                                    </div>
                                </div>


                            </form>
                        </div>

                    </div>

                </div>
                <div class="col-md-12" bis_skin_checked="1">
                    <div class="card card-primary collapsed-card" bis_skin_checked="1">
                        <div class="card-header" bis_skin_checked="1">
                            <h3 class="card-title">Смс</h3>
                            <div class="card-tools" bis_skin_checked="1">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div style="display: none; padding: 9px!important; " class="card-body"  bis_skin_checked="1" >
                            <form action="{{route('update_sms_settings')}}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Логин</label>
                                        <input type="text" value="{{$get_sms__settings->login}}" name="login" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Пароль</label>
                                        <input type="text" value="{{$get_sms__settings->password}}" name="password" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div style="display: flex; justify-content: space-between">
                                    <button style="max-height: 48px" type="submit" class="btn btn-primary">Сохранить</button>
                                    </div>
                                </div>


                            </form>
                        </div>

                    </div>

                </div>
                <div class="col-md-12" bis_skin_checked="1">
                    <div class="card card-primary collapsed-card" bis_skin_checked="1">
                        <div class="card-header" bis_skin_checked="1">
                            <h3 class="card-title">Ватсапы/ Телеграммы</h3>
                            <div class="card-tools" bis_skin_checked="1">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div style="display: none; padding: 9px!important; " class="card-body"  bis_skin_checked="1" >
                            <form action="{{route('update_whatsapp_settings')}}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Ватсап ID</label>
                                        <input type="text" value="{{$get_whatsapp__settings->whatsapp_id}}" name="whatsapp_id" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Ватсап Token</label>
                                        <input type="text" value="{{$get_whatsapp__settings->whatsapp_token}}" name="whatsapp_token" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Телеграм ID</label>
                                        <input type="text" value="{{$get_whatsapp__settings->telegram_id}}" name="telegram_id" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Телеграм Token</label>
                                        <input type="text" value="{{$get_whatsapp__settings->telegram_token}}" name="telegram_token" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div style="display: flex; justify-content: space-between">
                                    <button style="max-height: 48px" type="submit" class="btn btn-primary">Сохранить</button>
                                    </div>
                                </div>


                            </form>
                        </div>

                    </div>

                </div>
                <div class="col-md-12" bis_skin_checked="1">
                    <div class="card card-primary collapsed-card" bis_skin_checked="1">
                        <div class="card-header" bis_skin_checked="1">
                            <h3 class="card-title">Яндекс сканнер</h3>
                            <div class="card-tools" bis_skin_checked="1">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div style="display: none; padding: 9px!important; " class="card-body"  bis_skin_checked="1" >
                            <form action="{{route('update_yandex_scanning')}}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Токен</label>
                                        <input type="text" value="{{$get_yandex_scanning_keys->token}}" name="token" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">ID  папки</label>
                                        <input type="text" value="{{$get_yandex_scanning_keys->folder_id}}" name="folder_id" class="form-control" id="exampleInputEmail1" placeholder="">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div style="display: flex; justify-content: space-between">
                                    <button style="max-height: 48px" type="submit" class="btn btn-primary">Сохранить</button>
                                    </div>
                                </div>


                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- Выплаты -->
        <div class="tab-pane fade {{ $activeTab == '#payments' ? ' show active' : '' }}" id="payments" role="tabpanel" aria-labelledby="payments-tab">
            <!-- Добавьте сюда контент для Выплат -->
        </div>

        <!-- Антифрод -->
        <div class="tab-pane fade  {{ $activeTab == '#antifraud' ? ' show active' : '' }}" id="antifraud" role="tabpanel" aria-labelledby="antifraud-tab">
            <!-- Добавьте сюда контент для Антифрода -->
        </div>

        <!-- Рассылки -->
        <div class="tab-pane fade {{ $activeTab == '#mailings' ? ' show active' : '' }}" id="mailings" role="tabpanel" aria-labelledby="mailings-tab">
            <div class="card card-primary">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center">
                        <h3 class="card-title">Рассылки</h3>
                        <div style="max-width: 20%">
                            <a href="{{route('create_letters_page')}}" class="btn btn-block bg-gradient-warning">Добавить</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="myTable" class="display">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Тип</th>
                            <th>Дни</th>
                            <th>Сообщение</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($get_sending_messages as $item)
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>@foreach($types as $key => $value) @if($key == $item->type) {{$value}} @endif @endforeach</td>
                                <td>{{$item->day}}</td>
                                <td>{{$item->message}}</td>
                                <td style="width: 10px;">
                                    <a href="{{route('single_page_letters', $item->id)}}" class="btn btn-block btn-primary">Редактирование</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('page_scripts')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Подключаем jQuery -->
    <script src="jquery-3.7.1.min.js"></script>

    <!-- Подключаем скрипт DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- Подключаем скрипт Bootstrap (для работы модального окна и уведомлений) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Подключаем скрипт для уведомлений Toasts (AdminLTE) -->
    <script src="https://adminlte.io/themes/v3/plugins/toastr/toastr.min.js"></script>
    <script src="{{asset('admin/plugins/moment/moment.min.js')}}"></script>
    <script src="{{asset('admin/plugins/inputmask/jquery.inputmask.min.js')}}"></script>
    <script src="{{asset('admin/plugins/ekko-lightbox/ekko-lightbox.min.js')}}"></script>
    <script src="{{asset('admin/plugins/filterizr/jquery.filterizr.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>


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

            var table2 = $('#myTables').DataTable({
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

        @if(session('success'))

    <script>
        $(document).Toasts('create', {
            class: 'bg-success',
            title: 'Уведомление',
            subtitle: 'Сейчас',
            body: "{{ session('success') }}",
            autohide: true,
            delay: 10000
        });
    </script>
        @endif
    @if(session('error'))
        <script>
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Уведомление',
                subtitle: 'Сейчас',
                body: "{{ session('error') }}",
                autohide: true,
                delay: 10000
            });
        </script>
    @endif
@endsection
