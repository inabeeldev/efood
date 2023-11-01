@extends('layouts.branch.app')

@section('title', translate('Product Bulk Import'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/bulk_import.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Bulk_Import')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->

        <!-- Content Row -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card card-body">
                    <h2>{{translate('Instructions :')}} </h2>

                    <ol class="order-list">
                        <li>{{translate('Download the format file and fill it with proper data.')}}</li>
                        <li>{{translate('You can download the example file to understand how the data must be filled.')}}</li>
                        <li>{{translate('Once you have downloaded and filled the format file, upload it in the form below and submit.')}}</li>
                        <li>{{\App\CentralLogics\translate("After uploading products you need to edit them and set product's images and choices.")}}</li>
                        <li>{{translate('You can get category and sub-category id from their list, please input the right ids.')}}</li>
                    </ol>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card card-body">
                    <form class="product-form" action="{{route('branch.product.bulk-import')}}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="rest-part">
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                <h4 class="mb-0">{{translate('Do_not_have_the_template')}}?</h4>
                                <a href="{{asset('public/assets/product_bulk_format.xlsx')}}" download=""
                                class="fz-16 btn-link">{{translate('Download_Here')}}</a>
                            </div>
                            <div class="mt-5">
                                <div class="form-group">
                                    <div class="row justify-content-center">
                                        <div class="col-auto">
                                            <div class="upload-file">
                                                <input type="file" id="import-file" name="products_file" accept=".xlsx, .xls" class="upload-file__input">
                                                <div class="upload-file__img_drag upload-file__img">
                                                    <img src="{{asset('public/assets/admin/img/icons/drug_file.png')}}" alt="">
{{--                                                    <img src="{{asset('public/assets/admin/img/icons/excel.png')}}" alt="">--}}
                                                </div>
                                                <div class="file--img"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                                    <button type="submit" class="btn btn-primary">{{translate('Submit')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- The Modal -->
<div class="modal" id="products_modal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Products</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <div class="table-responsive">
                <table id="productsTable" class="table table-bordered">
                    <thead></thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-submit">{{translate('Import')}}</button>

      </div>

    </div>
  </div>
</div>
na
@endsection

@push('script_2')
  <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

    <script>
        $('#import-file').on('change', function(e){
            if($(this)[0].files.length !== 0){
                $('.file--img').empty().append(`<div class="my-2"> <img width="200" src="{{asset('public/assets/admin/img/icons/excel.png')}}" alt=""></div>`)
            }
        })
        $('.product-form').on('reset', function(){
            $('.file--img').empty()
        });
        
        
        $('#pdfFileInput').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
              var reader = new FileReader();
              reader.onload = function(e) {
                var contents = e.target.result;
                convertToThumbnail(contents);
              };
              reader.readAsArrayBuffer(file);
            }
          });
          
          $("#products_modal").on('hidden.bs.modal', function(){
            $('.file--img').empty()
          });
          


    // Display products in the table
     $(document).ready(function() {
      $('#import-file').change(function() {
            var file = $('#import-file').prop('files')[0];
            var reader = new FileReader();


            reader.onload = function(e) {
              var data = new Uint8Array(e.target.result);
              var workbook = XLSX.read(data, { type: 'array' });
        
              var worksheet = workbook.Sheets[workbook.SheetNames[0]];
              var jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
        
              var headerRow = jsonData[0];
              var tableHtml = '<thead><tr>';
              
              for (var h = 0; h < headerRow.length; h++) {
                tableHtml += '<th>' + headerRow[h] + '</th>';
              }
              
              tableHtml += '</tr></thead><tbody>';
        
              for (var i = 1; i < jsonData.length; i++) {
                var row = jsonData[i];
                var rowHtml = '<tr>';
        
                for (var j = 0; j < row.length; j++) {
                  rowHtml += '<td>' + row[j] + '</td>';
                }
        
                rowHtml += '</tr>';
                tableHtml += rowHtml;
              }
        
              tableHtml += '</tbody>';
              $('#productsTable').html(tableHtml);
            };
        
            reader.readAsArrayBuffer(file);

        $('#products_modal').modal('toggle');

      });
    });
    
    $('.btn-submit').on('click',function(e){
        $('.product-form').submit();
    })

    </script>

@endpush
