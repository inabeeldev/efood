@extends('layouts.admin.app')

@section('title', translate('Membership Add'))

@push('css_or_js')
{{--    <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>--}}
{{--    <meta name="csrf-token" content="{{ csrf_token() }}">--}}
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/employee.png')}}" alt="">
            <span class="page-header-title">
                {{translate('add_package')}}
            </span>
        </h2>
    </div>
    <!-- End Page Header -->

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <form action="{{route('admin.rpackage.add-new')}}" method="post" enctype="multipart/form-data"
                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                @csrf
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2"><span class="tio-user"></span> {{translate('general_Information')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">{{translate('title')}}</label>
                                    <input type="text" name="title" class="form-control" id="title"
                                        placeholder="{{translate('Ex')}} : {{translate('Permium')}}" value="{{old('name')}}" required>
                                </div>
                            </div>
            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">{{translate('price')}}</label>
                                    <input type="number" name="price" value="{{old('price')}}" class="form-control" id="price"
                                        placeholder="{{translate('Ex')}} : 20" required>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 mt-1">
                                <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                                    <h5 class="mb-0 c1">
                                        {{translate('Status')}}
                                    </h5>
        
                                    <label class="switcher ml-auto mb-0">
                                        <input type="checkbox" class="switcher_input" name="status">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">{{translate('Description')}}</label>
                                    <textarea required class="ckeditor form-control" id="description" rows="10" name="description">{{old('description')}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <button type="reset" id="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                    <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });
    </script>
@endpush
