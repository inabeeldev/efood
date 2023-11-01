@extends('layouts.branch.app')

@section('title', translate('Reservation List'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/employee.png')}}" alt="">
            <span class="page-header-title">
                {{translate('Reservation List')}}
            </span>
        </h2>
    </div>
    <!-- End Page Header -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-top px-card pt-4">
                    <div class="d-flex flex-column flex-md-row flex-wrap gap-3 justify-content-md-between align-items-md-center">
                        <h5 class="d-flex gap-2">
                            {{translate('reservation_list')}}
                            <span class="badge badge-soft-dark rounded-50 fz-12">{{$reservations->total()}}</span>
                        </h5>

                        <div class="d-flex flex-wrap justify-content-md-end gap-3">
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('Search by name')}}" aria-label="Search" value="" required="" autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">{{translate('Search')}}</button>
                                    </div>
                                </div>
                            </form>
                            <div>
                                <button type="button" class="btn btn-outline-primary text-nowrap" data-toggle="dropdown" aria-expanded="false">
                                    <i class="tio-download-to"></i>
                                    Export
                                    <i class="tio-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a type="submit" class="dropdown-item d-flex align-items-center gap-2" href="{{route('branch.reservation.excel_import')}}">
                                            <img width="14" src="{{asset('public/assets/admin/img/icons/excel.png')}}" alt="">
                                            {{ translate('Excel') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="py-3">
                    <div class="table-responsive">
                        <table id="datatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('User Name')}}</th>
                                    <th>{{translate('Number of Reservations')}}</th>
                                    <th>{{translate('Date')}}</th>
                                    <th>{{translate('Time')}}</th>
                                    <th>{{translate('Order Status')}}</th>
                                    <th >{{translate('Status')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($reservations as $k=>$reservation)
                                <tr>
                                    <td>{{$k+1}}</td>
                                    <td>
                                        <div><label class="text-dark">{{$reservation['f_name']." ".$reservation['l_name']}}</label></div>
                                    </td>
                                    <td><span class="badge badge-soft-info py-1 px-2">{{$reservation['number_of_reservations']}}</span></td>
                                    <td><span class="badge badge-soft-info py-1 px-2">{{$reservation['date']}}</span></td>
                                    <td><span class="badge badge-soft-info py-1 px-2">{{$reservation['time']}}</span></td>
                                    <td>
                                        <form action="{{route('branch.reservation.order-status',[$reservation['id']])}}" method="post">
                                            @csrf
                                            <select {{$reservation['status'] == 0 ? 'disabled' : ''}}  class="form-control order_status_change" name="order_status">
                                                <option {{$reservation['order_status'] == "Pending" ? 'selected':''}} value="Pending">Pending</option>
                                                <option {{$reservation['order_status'] == "Waiting" ? 'selected':''}} value="Waiting">Waiting</option></option>
                                                <option {{$reservation['order_status'] == "Approved" ? 'selected':''}} value="Approved">Approved</option>
    
                                            </select>
                                        </form>
                                    
                                    </td>

                                    <td>
                                        <label class="switcher">
                                            <input class="switcher_input" type="checkbox" onclick="location.href='{{route('branch.reservation.status',[$reservation['id'],$reservation['status']?0:1])}}'" {{$reservation['status']?'checked':''}}>
                                            <span class="switcher_control"></span>
                                        </label>
                                    </td>

                                    {{-- <td>
                                        <form action="{{route('branch.membership.delete')}}" method="post" id="membership-{{$reservation->id}}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id" value="{{$reservation->id}}">
                                        </form>
                                    </td> --}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive mt-4 px-3">
                        <div class="d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {{$reservations->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <!-- Page level plugins -->
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
        $('.order_status_change').on('change',function(e){
            $(this).parent().submit();
        });
        
        var pendingOrders = "{{$reservationsPending}}";
        if(pendingOrders > 0){
            var message = "You have <b class='text-danger'><strong>"+pendingOrders+"</strong></b> pending reservations";
            Swal.fire({
                title: '{{ translate('Pending Reservations ') }}',
                html: message,
                type: 'warning',
                showCancelButton: false,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                reverseButtons: true
            });
            
        }
    </script>
@endpush
