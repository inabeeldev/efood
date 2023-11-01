@extends('layouts.admin.app')

@section('title', translate('Employee List'))

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
                {{translate('members_list')}}
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
                            {{translate('members_list')}}
                            <span class="badge badge-soft-dark rounded-50 fz-12">{{$members->total()}}</span>
                        </h5>

                        <div class="d-flex flex-wrap justify-content-md-end gap-3">
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{translate('Search by name, email or phone')}}" aria-label="Search" value="" required="" autocomplete="off">
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
                                        <a type="submit" class="dropdown-item d-flex align-items-center gap-2" href="{{route('admin.employee.excel-export')}}">
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
                                    <th>{{translate('User info')}}</th>
                                    <th>{{translate('Package')}}</th>
                                    <th>{{translate('Status')}}</th>
                                    <th class="text-center">{{translate('action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($members as $k=>$member)
                                <tr>
                                    <td>{{$k+1}}</td>
                                    <td >
                                        <div>
                                            <strong>
                                                <label>{{$member['f_name']." ".$member['l_name']}}</label>
                                            </br>
                                                <label href="mailto:{{$member['email']}}" class="mb-0 text-dark bold fz-12"">{{$member['email']}}</a>
                                            </strong>
                                            <br>
                                            <a href="tel:{{$member['phone']}}" class="text-dark fz-12">{{$member['phone']}}</a>
                                        </div>
                                    </td>

                                    <td>{{$member['package_name']}}</td>
                                    <td><span class="badge badge-soft-info py-1 px-2">{{$member['status']}}</span></td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <!--<a href="{{route('admin.membership.update',[$member['id']])}}"-->
                                            <!--class="btn btn-outline-info btn-sm square-btn"-->
                                            <!--title="{{translate('Edit')}}">-->
                                            <!--    <i class="tio-edit"></i>-->
                                            <!--</a>-->
                                            <a onclick="form_alert('membership-{{$member['id']}}', '{{translate('want_to_delete_this_membership?')}}')"
                                               class="btn btn-outline-danger btn-sm delete square-btn"
                                               title="{{translate('delete')}}">
                                                <i class="tio-delete"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.membership.delete')}}" method="post" id="membership-{{$member->id}}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id" value="{{$member['id']}}">
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
                            {{$members->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
@endpush
