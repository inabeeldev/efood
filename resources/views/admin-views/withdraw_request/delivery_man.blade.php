@extends('layouts.admin.app')

@section('title', translate('Received Funds History'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/deliveryman.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Received Funds History')}}
                </span>
            </h2>
        </div>

        <!-- End Page Header -->


        <div class="row g-2">

            <div class="col-12">
                <div class="card">
                    <div class="card-top px-card pt-4">
                        <div class="row justify-content-between align-items-center gy-2">
                            <div class="col-sm-4 col-md-6 col-lg-8">
                                <h5 class="d-flex align-items-center gap-2 mb-0">
                                    {{translate('received_funds_Table')}}
                                    <span class="badge badge-soft-dark rounded-50 fz-12">{{ $dwr->count() }}</span>
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
                                    <th>{{translate('Delivery Man')}}</th>
                                    <th>{{translate('Bank Name')}}</th>
                                    <th>{{translate('Account No')}}</th>
                                    <th>{{translate('Withdraw Amount')}}</th>
                                    <th>{{translate('status')}}</th>
                                    <th>{{translate('change status')}}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($dwr as $key=>$wr)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <div class="max-w300 text-wrap">
                                                {{$wr->deliveryMan['f_name']}} {{$wr->deliveryMan['l_name']}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="max-w300 text-wrap">
                                                {{$wr['bank_name']}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="max-w300 text-wrap">
                                                {{ str_repeat('*', strlen($wr['account_no']) - 4) . substr($wr['account_no'], -4) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="max-w300 text-wrap">
                                                {{$wr['amount']}}
                                            </div>
                                        </td>
                                        <td>
                                            @if($wr['status']=='paid')
                                                <span class="badge-soft-success px-2 py-1 rounded">{{translate('Transfer Completed')}}</span>
                                            @elseif($wr['status']=='unpaid')
                                                <span class="badge-soft-warning px-2 py-1 rounded">{{translate('In Process')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <select name="status" onchange="route_alert('{{route('admin.withdraw_requests.delivery-payment-status',['id'=>$wr['id']])}}'+'&status='+ this.value,'{{\App\CentralLogics\translate("Change status to ")}}' + this.value)" class="status custom-select" data-id="100147">
                                                <option value="paid" {{$wr['status'] == 'paid'? 'selected' : ''}}> {{translate('paid')}}</option>
                                                <option value="unpaid" {{$wr['status'] == 'unpaid'? 'selected' : ''}}>{{translate('unpaid')}} </option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive mt-4 px-3">
                            <div class="d-flex justify-content-lg-end">
                                <!-- Pagination -->
                                {!! $dwr->links() !!}
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

