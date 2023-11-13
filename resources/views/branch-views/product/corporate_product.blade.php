@extends('layouts.branch.app')

@section('title', translate('Corporate Products'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/deliveryman.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Corporate Products')}}
                </span>
            </h2>
            <span class="badge badge-soft-dark rounded-50 fz-14">{{ $cps->count() }}</span>
        </div>

        <!-- End Page Header -->


        <div class="row g-2">

            <div class="col-12">
                <div class="card">
                    <div class="card-top px-card pt-4">
                        <div class="row justify-content-between align-items-center gy-2">
                            <div class="col-sm-4 col-md-6 col-lg-4">
                                <h5 class="d-flex align-items-center gap-2 mb-0">
                                    {{translate('Corporate Products')}}
                                    <span class="badge badge-soft-dark rounded-50 fz-12"></span>
                                </h5>
                            </div>
                            <div class="col-lg-8">
                                <div class="d-flex gap-3 justify-content-end text-nowrap flex-wrap">

                                    <a href="{{route('branch.product.add-corporate-product')}}" class="btn btn-primary">
                                        <i class="tio-add"></i> {{translate('add_New_Corporate_Product')}}
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="py-3">
                        <div class="table-responsive datatable-custom">
                            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('Image')}}</th>
                                    <th>{{translate('Title')}}</th>
                                    <th>{{translate('status')}}</th>
                                    <th>{{translate('Actions')}}</th>

                                </tr>
                                </thead>

                                <tbody>
                                @foreach($cps as $key=>$cp)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <div class="media align-items-center gap-3">
                                                <div class="avatar">
                                                    <img src="{{asset('storage/app/public/corporate')}}/{{$cp['image']}}" class="rounded img-fit"
                                                        onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="max-w300 text-wrap">
                                                {{$cp['title']}}
                                            </div>
                                        </td>

                                        <td>
                                            <div>
                                                <label class="switcher">
                                                    <input id="{{$cp['id']}}" class="switcher_input" type="checkbox" {{$cp['status']==1? 'checked' : ''}} data-url="{{route('branch.product.status-corporate-product',[$cp['id'],0])}}" onchange="status_change(this)">

                                                    <span class="switcher_control"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-outline-danger btn-sm delete square-btn"
                                                onclick="form_alert('product-{{$cp['id']}}','{{translate('Want to delete this item ?')}}')"><i class="tio-delete"></i></button>
                                            </div>
                                            <form action="{{route('branch.product.delete-corporate-product',[$cp['id']])}}"
                                                method="post" id="product-{{$cp['id']}}">
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
                                {!! $cps->links() !!}
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

