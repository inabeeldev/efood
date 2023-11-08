@extends('layouts.admin.app')

@section('title', translate('Customer Incentives'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/deliveryman.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Customer Incentives')}}
                </span>
            </h2>
        </div>

        <!-- End Page Header -->


        <div class="row g-2">
            <div class="col-12">
                <form action="{{route('admin.customer.customer-incentives.store')}}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 d-flex align-items-center gap-2 mb-0">
                                <i class="tio-user"></i>
                                {{translate('General_Information')}}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Choose')}} {{translate('User')}}</label>
                                        <select name="user_id" class="form-control js-select2-custom">
                                            <option value="" selected>---{{translate('select')}}---</option>
                                            @foreach($customers as $customer)
                                                <option value="{{$customer['id']}}">{{$customer['f_name']}} {{$customer['l_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Notification')}}</label>
                                        <select name="notification_id" class="form-control js-select2-custom" >
                                            <option value="">---{{translate('select')}}---</option>
                                            @foreach($notifications as $notification)
                                                <option value="{{$notification['id']}}">{{$notification['title']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Status')}}</label>
                                        <select name="status" class="form-control js-select2-custom" >
                                            <option value="">---{{translate('select')}}---</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inctive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group text-center">
                                        <button type="reset" id="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                                        <button type="submit" class="btn btn-primary">{{translate('Submit')}}</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-top px-card pt-4">
                        <div class="row justify-content-between align-items-center gy-2">
                            <div class="col-sm-4 col-md-6 col-lg-8">
                                <h5 class="d-flex align-items-center gap-2 mb-0">
                                    {{translate('Notification_Table')}}
                                    <span class="badge badge-soft-dark rounded-50 fz-12">{{ $customerIncentives->count() }}</span>
                                </h5>
                            </div>

                        </div>
                    </div>


                    <div class="py-3">
                        <div class="table-responsive datatable-custom">
                            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('image')}}</th>
                                    <th>{{translate('Incentive')}}</th>
                                    <th>{{translate('User')}}</th>
                                    <th>{{translate('status')}}</th>
                                    <th class="text-center">{{translate('action')}}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($customerIncentives as $key=>$customerIncentive)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            @if($customerIncentive['image']!=null)
                                                <img class="img-vertical-150"
                                                     onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                                                     src="{{asset('storage/app/public/notification')}}/{{$customerIncentive->notification['image']}}">
                                            @else
                                                <label class="badge badge-soft-warning">{{translate('No')}} {{translate('image')}}</label>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="max-w300 text-wrap">
                                                {{substr($customerIncentive->notification['title'],0,25)}} {{strlen($customerIncentive->notification['title'])>25?'...':''}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="max-w300 text-wrap">
                                                {{$customerIncentive->user['f_name']}} {{$customerIncentive->user['l_name']}}
                                            </div>
                                        </td>
                                        <td>
                                            <label class="switcher">
                                                <input class="switcher_input" type="checkbox" onclick="status_change(this)" id="{{$customerIncentive['id']}}"
                                                    data-url="{{route('admin.customer.customer-incentives.update',[$customerIncentive['id'],0])}}" {{$customerIncentive['status'] == 1? 'checked' : ''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <!-- Dropdown -->
                                            <div class="d-flex justify-content-center gap-2">

                                                <button type="button" class="btn btn-outline-danger btn-sm delete square-btn"
                                                onclick="$('#notification-{{$customerIncentive['id']}}').submit()"><i class="tio-delete"></i></button>
                                            </div>
                                            <form
                                                action="{{route('admin.customer.customer-incentives.delete',[$customerIncentive['id']])}}"
                                                method="post" id="notification-{{$customerIncentive['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive mt-4 px-3">
                            <div class="d-flex justify-content-lg-end">
                                <!-- Pagination -->
                                {!! $customerIncentives->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>



<script>
    $(document).on('ready', function () {
        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });
    });
</script>
@endpush

