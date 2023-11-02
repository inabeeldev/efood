@extends('layouts.branch.app')

@section('title', translate('Funds Report'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/sales.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Funds Report')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->


        <div class="card mt-3">
            <div class="card-body">
                <div class="media flex-column flex-sm-row flex-wrap align-items-sm-center gap-4">
                    <!-- Avatar -->
                    <div class="avatar avatar-xl">
                        <img class="avatar-img" src="{{asset('public/assets/admin')}}/svg/illustrations/credit-card.svg"
                            alt="Image Description">
                    </div>
                    <!-- End Avatar -->

                    <div class="media-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="">
                                <h2 class="page-header-title">{{translate('Funds Report')}} {{translate('overview')}}</h2>

                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span>{{translate('branch')}}:</span>
                                        <a href="#">{{auth('branch')->user()->name}}</a>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span>{{translate('wallet_balance')}}:</span>
                                        <a href="#" class="text-success font-weight-bold">{{auth('branch')->user()->wallet_amount}}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex">
                                <a class="btn btn-icon btn-primary rounded-circle px-2" href="{{route('branch.dashboard')}}">
                                    <i class="tio-home-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <form action="javascript:" id="search-form" method="POST">
                    @csrf
                    <div class="row g-2">
                        {{-- <div class="col-sm-6 col-md-4">
                            <select class="custom-select custom-select" name="branch_id" id="branch_id"
                                    required>
                                <option selected disabled>{{translate('Select Restaurant')}}</option>
                                @php
                                    $loggedInBranch = \App\Model\Branch::find(auth('branch')->id());
                                @endphp

                                <option value="{{ $loggedInBranch->id }}" {{ session('branch_filter') == $loggedInBranch->id ? 'selected' : '' }}>
                                    {{ $loggedInBranch->name }}
                                </option>
                            </select>
                        </div> --}}
                        @php
                            $loggedInBranch = \App\Model\Branch::find(auth('branch')->id());
                        @endphp
                        <input type="hidden" name="branch_id" value="{{ $loggedInBranch->id }}">
                        <div class="col-sm-6 col-md-5">
                            <input type="date" name="from" id="from_date"
                                    class="form-control" required>
                        </div>
                        <div class="col-sm-6 col-md-5">
                            <input type="date" name="to" id="to_date"
                                    class="form-control" required>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <button type="submit"
                                    class="btn btn-primary btn-block">{{translate('show')}}</button>
                        </div>

                        <div class="col-md-6 d-flex flex-column gap-2">
                            <div>
                                <strong>
                                    {{translate('total_Orders')}} :
                                    <span id="order_count"> </span>
                                </strong>
                            </div>
                            <div>
                                <strong>
                                    {{translate('total_Item_Qty')}}
                                    : <span
                                        id="item_count"> </span>
                                </strong>
                            </div>
                            <div>
                                <strong>{{translate('total')}}  {{translate('amount')}} : <span
                                        id="order_amount"></span>
                                </strong>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive datatable_wrapper_row mt-5" id="set-rows">
                    @include('branch-views.fund.partials._table',['data'=>[]])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $('#search-form').on('submit', function () {
            $.post({
                url: "{{route('branch.funds.sale-report-filter')}}",
                data: $('#search-form').serialize(),

                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#order_count').html(data.order_count);
                    $('#order_amount').html(data.order_sum);
                    $('#item_count').html(data.item_qty);
                    $('#set-rows').html(data.view);
                    $('.card-footer').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });

    </script>
    <script>
        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('Invalid date range!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('input').addClass('form-control');
        });

        // INITIALIZATION OF DATATABLES
        // =======================================================
        var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
            dom: 'Bfrtip',
            language: {
                zeroRecords: '<div class="text-center p-4">' +
                    '<img class="mb-3" src="{{asset('public/assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">' +
                    '<p class="mb-0">{{translate('No data to show')}}</p>' +
                    '</div>'
            }
        });
    </script>
@endpush
