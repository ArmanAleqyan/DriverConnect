@extends('admin.layouts.default')
@section('title')
    Пользователь
@endsection

@section('page_css')
    <link rel="stylesheet" href="{{asset('admin/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/ekko-lightbox/ekko-lightbox.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endsection

@section('content')
    <style>
        /* Исправление стилей для Select2 */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container .select2-dropdown {
            z-index: 9999;
        }
        .select2-dropdown {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
        }
    </style>
    {{--    @if(session('created'))--}}
    {{--        <div class="alert alert-success alert-dismissible fade show" role="alert">--}}
    {{--            <strong>Добавление успешно завершено</strong>--}}
    {{--            <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
    {{--                <span aria-hidden="true">&times;</span>--}}
    {{--            </button>--}}
    {{--        </div>--}}
    {{--    @endif--}}
    @php

                $activeTab =  '#user'; // Значение по умолчанию - #user


         $myTabs = [
         '#user',
         '#car',
         '#payments'
         ];
         if (!in_array($activeTab, $myTabs)) {
             $activeTab = '#user';
         }


    @endphp

{{--    <ul class="nav nav-tabs" id="myTab" role="tablist">--}}
{{--        <li class="nav-item" role="presentation">--}}
{{--            <a class="nav-link{{ $activeTab == '#user' ? ' active' : '' }}" id="user-tab" data-toggle="tab" href="#user" role="tab" aria-controls="user" aria-selected="{{ $activeTab == '#user' ? 'true' : 'false' }}">Пользователь</a>--}}
{{--        </li>--}}
{{--    </ul>--}}
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show {{ $activeTab == '#user' ? ' active' : '' }}" id="user" role="tabpanel" aria-labelledby="user-tab">
            <div class="card card-primary" bis_skin_checked="1">
                <div class="card-header" bis_skin_checked="1">
                    <div style="display: flex; justify-content: space-between">
                        <h3 class="card-title">Добавить пользвателя</h3>
                    </div>
                </div>



                <form  id="user_data" action="{{route('create_new_user')}}" method="post" class="forms-sample" enctype="multipart/form-data">
                                @csrf
                    <div style="display: flex; justify-content: space-between">
                        <div class="card-body" bis_skin_checked="1"  style="max-width: 40%">


                                <div class="form-group" bis_skin_checked="1">
                                    <label>Тип Работы</label>
                                    <select name="job_category_ids" class="form-control">
                                        @foreach($get_job_category as $job_category)

                                            <option value="{{$job_category->id}}">{{$job_category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>


                            <div class="form-group">
                                <label for="">Регион</label>
                                <select  name="region_id" class="form-control select2" style="width: 100%;">
                                    @foreach ($get_regions as $region)
                                        @if(old('region_id') == $region->id)
                                            <option selected value="{{ $region->id }}">{{$region->name}}</option>
                                        @else
                                            <option value="{{ $region->id }}">{{$region->name}}</option>
                                        @endif

                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="phone">Номер Телефона</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input  type="text" class="form-control" id="phone" name="phone"
                                            data-inputmask="'mask': '+7 (999) 999-99-99'" data-mask required placeholder="+7 (999) 999-99-99" value="{{old('phone')}}">
                                    @error('phone')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Имя</label>
                                <input   required  type="text" class="form-control" id=""  name="name" value="{{old('name')}}">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Фамилия  </label>
                                <input   required  type="text" class="form-control" id=""  name="surname" value="{{old('surname')}}">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Отчетво </label>
                                <input   required  type="text" class="form-control" id=""  name="middle_name" value="{{old('middle_name')}}">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Дата Рождения  </label>
                                <input required    type="date" class="form-control" id=""  name="birth_date" value="{{old('birth_date')}}">
                            </div>
                        </div>

                        @if(old('job_category_ids') == 3)
                        <div  class="card-body no_driver"  bis_skin_checked="1"  style="max-width: 40%;  display: none">
                    @else
                            <div  class="card-body no_driver"  bis_skin_checked="1"  style="max-width: 40%">
                            @endif

                            <div class="form-group">
                                <label for="year">Стаж с</label>
                                <select  name="driver_license_experience_total_since_date" class="form-control select2" style="width: 100%;">
                                    @for ($year = now()->year; $year >= 1980; $year--)
                                        @if(old('driver_license_experience_total_since_date')==$year)
                                            <option selected value="{{ $year }}">{{ $year }}</option>
                                        @else
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endif

                                    @endfor
                                </select>
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Номер ВУ</label>
                                <input     type="text" class="form-control" id=""  name="driver_license_number" value="{{old('driver_license_number')}}">
                            </div>
                            <div class="form-group">
                                <label for="">Страна Выдачи</label>
                                <select  name="driver_license_country_id" class="form-control select2" style="width: 100%;">
                                    @foreach ($get_country_drive_licenze as $country)
                                        @if(old('driver_license_country_id') == $country->id )
                                            <option selected value="{{ $country->id }}">{{$country->name}}</option>
                                        @else
                                            <option value="{{ $country->id }}">{{$country->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Дата выдачи</label>
                                <input     type="date" class="form-control" id="" max="{{\Carbon\Carbon::now()->format('Y-m-d')}}" min="{{\Carbon\Carbon::now()->subYears(20)->format('Y-m-d')}}"  name="driver_license_issue_date" value="{{old('driver_license_issue_date')}}">
                            </div>
                            <div class="form-group" bis_skin_checked="1">
                                <label for="">Действует до</label>
                                <input     type="date" class="form-control" id=""
                                           {{--                                           min="{{\Carbon\Carbon::now()->format('Y-m-d')}}"--}}
                                           max="{{\Carbon\Carbon::now()->addYears(20)->format('Y-m-d')}}"   name="driver_license_expiry_date" value="{{ old('driver_license_expiry_date')}}">
                            </div>


                        </div>
                    </div>
                    <div class="card-footer" bis_skin_checked="1" >
                        <div style="display: flex; justify-content: space-between">

                                <button type="submit" class="btn btn-primary">Добавить</button>



                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@section('page_scripts')
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Подключаем скрипт для уведомлений Toasts (AdminLTE) -->
    <script src="https://adminlte.io/themes/v3/plugins/toastr/toastr.min.js"></script>
    {{--    ce08d4de07437f934055c92907de1ec6--}}
    {{--    @if($get->contractor_profile_id != null)--}}
    <script>
        $(document).ready(function() {
            $('select[name="job_category_ids"]').on('change', function() {
                if ($(this).val() == '3') {
                    $('.no_driver').hide();
                } else {
                    $('.no_driver').show();
                }
            });
        });
        $(document).ready(function() {
            // Сделать все поля формы только для чтения, кроме статуса автомобиля и других исключений
            $('#carForm').find('input, select, textarea').not('[name="_token"]').not('[name="car_category_id[]"]').not('[name="car_work_status"]').not('[name="car_amenities[]"]').each(function() {
                if ($(this).is('select')) {
                    $(this).prop('disabled', true); // Для select используем disabled
                } else {
                    $(this).prop('readonly', true); // Для input и textarea используем readonly
                }
            });
        });
    </script>
    {{--    @endif--}}


    <script>
        $(document).ready(function() {
            $("#phone").inputmask({
                mask: "+7 (999) 999-99-99",
                placeholder: "+7 (999) 999-99-99",
                showMaskOnHover: false,
                showMaskOnFocus: true,
            });

            $("form").on("submit", function(e) {
                var phone = $("#phone").inputmask("unmaskedvalue");
                if (phone.length !== 10) {
                    e.preventDefault();
                    alert("Пожалуйста, введите номер телефона в правильном формате.");
                }
            });

            $("select[name='mark_id']").change(function() {
                var mark_id = $(this).val();
                $.ajax({
                    url: '{{ url('/api/get_car_model') }}',
                    type: 'GET',
                    data: {
                        mark_id: mark_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        var modelSelect = $("select[name='model_id']");
                        modelSelect.empty();
                        $.each(response.data, function(key, model) {
                            modelSelect.append(new Option(model.name, model.id));
                        });
                        modelSelect.trigger('change');
                    }
                });
            });

            $('.select2').select2();

            $('.datatable').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

            $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox({
                    alwaysShowClose: true
                });
            });

            $('.filter-container').filterizr({gutterPixels: 3});
            $('.btn[data-filter]').on('click', function() {
                $('.btn[data-filter]').removeClass('active');
                $(this).addClass('active');
            });
        });
    </script>
    @if (session('created'))
        <script>
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Уведомление',
                subtitle: "{{ session('created') }}",
                body:  "{{ session('created') }}" ,
                autohide: true,
                delay: 10000
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Уведомление',
                subtitle: '',
                body:  "{{ session('error') }}",
                autohide: true,
                delay: 10000
            });
        </script>
    @endif
@endsection
