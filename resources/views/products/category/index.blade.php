@extends('layouts.master')
@section('page_title', __('Category'))
@section('content')
	<div class="block-header">
		<h2>
			{{__('PRODUCTS')}}
			<a href="javascript:void(0);" class="btn new-record bg-purple pull-right">
				{{__('ADD')}}
			</a>
			<small class="clearfix">
				{{__('Categories')}}
			</small>
		</h2>
	</div>
	
	<div class="row clearfix">
		<div class="col-md-12">
			<div class="card">
				<div class="header">
					<div class="box-title">
						<h2>
							{{__('CATEGORIES')}}
							<small>{{__('Set categories with which you want to add your products.')}}</small>
						</h2>
					</div>
					<ul class="header-dropdown m-r--5">
						<li>
							<a href="javascript:void(0);" class="refresh-toggle">
								<i class="material-icons">loop</i>
							</a>
						</li>
					</ul>
				</div>
				<div class="body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-hover js-basic-example dataTable" id="table_data">
							<thead>
							<tr>
								<th class="all">{{__('ID')}}</th>
								<th>{{__('Name')}}</th>
								<th>{{__('Description')}}</th>
								<th>{{__('Products')}}</th>
								<th class="all">{{__('Actions')}}</th>
							</tr>
							</thead>
							<tfoot>
							<tr>
								<th class="all">{{__('ID')}}</th>
								<th>{{__('Name')}}</th>
								<th>{{__('Description')}}</th>
								<th>{{__('Products')}}</th>
								<th class="all">{{__('Actions')}}</th>
							</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="alter-row" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="modal-form">
					<div class="modal-header">
						<h4 class="modal-title" id="modal-label"></h4>
					</div>
					<div class="modal-body">
						<div class="form-group row">
							{!! Form::label('name', __('Name'), array('class' => 'col-md-3')); !!}
							<div class="form-float col-md-9">
								<div class="form-line {{ $errors->has('name') ? ' error ' : '' }}">
									{!! Form::text('name', null, array('id' => 'name', 'class' => 'form-control', 'maxlength' => 50, 'placeholder' => __('(Max: 50)'), 'required')) !!}
								</div>
							</div>
						</div>
						<div class="form-group row">
							{!! Form::label('description', __('Description'), array('class' => 'col-md-3')); !!}
							<div class="form-float col-md-9">
								<div class="form-line {{ $errors->has('description') ? ' error ' : '' }}">
									{!! Form::textarea('description', null, array('id' => 'description', 'class' => 'form-control', 'maxlength' => 200, 'placeholder' => __('(Max: 200)'), 'required', 'rows' => '5')) !!}
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" id="form-submit" class="btn bg-purple waves-effect">{{__('SUBMIT')}}</button>
						<button type="button" class="btn btn-link waves-effect" data-dismiss="modal">{{__('CLOSE')}}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@push('js')
	<script>
        var Page = function(){
            var dataTable, formModalElement = $('#alter-row');
	        
            var handleNewRecord = function(){
                var label = formModalElement.find('#modal-label');
                var form = formModalElement.find('#modal-form');
                var formAction = window.location.href;
	            
	            $('.new-record').click(function(){
                    label.html('{{__('Add Record')}}');

                    Global.resetForm(form);
                    Global.prepareForm(form);
                    
                    formModalElement.modal('show');
                    
		            Global.bindModalAjaxForm(form, formAction, 'POST', dataTable);
	            });
	        };
	        
            var handleDatatable = function () {
                var exportable = [0, 1, 2, 3];
                
                dataTable = $('#table_data')
                    .on('processing.dt', function (e, settings, processing) {
                        var card = $(this).closest('.card');

                        if(processing){
                            card.waitMe({
                                effect: 'rotation',
                                text: '{{__('Loading')}}...',
                                bg: 'rgba(255,255,255,0.50)',
                                color: ['#555', '#9C27B0']
                            });
                        }else{
                            card.waitMe('hide');
                        }
                    }).DataTable({
                        processing: false,
                        serverSide: true,
                        "ajax": {
                            "async": true,
                            "type": "POST",
                            "url": '{{route('products.category.data')}}',
                            "data": {}
                        },
                        rowId: 'id',

                        dom: "<'row'<'col-md-4 col-sm-6 hidden-xs'l><'col-md-4 hidden-sm hidden-xs'B><'col-md-4 col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                        buttons: [
                            {extend: 'csv', exportOptions: {columns: exportable}},
                            {extend: 'print', exportOptions: {columns: exportable}},
                            {extend: 'excel', exportOptions: {columns: exportable}},
                        ],

                        columns: [
                            {data: 'id'},
                            {data: 'name'},
                            {data: 'description'},
                            {data: 'products'},
	                        {data: 'action', orderable: false}
                        ],

                        "language": {
                            "aria": {
                                "sortAscending": ": {{__('activate to sort column ascending')}}",
                                "sortDescending": ": {{__('activate to sort column descending')}}"
                            },
                            "emptyTable": "{{__('No data available in table')}}",
                            "info": "{{__('Showing').' _START_ '.__('to').' _END_ '.__('of').' _TOTAL_ '.__('entries')}}",
                            "infoEmpty": "{{__('No entries found')}}",
                            "infoFiltered": "{{'(filtered1 '.__('from').' _MAX_ '.__('total entries').')'}}",
                            "lengthMenu": "{{'_MENU_ '.__('entries')}}",
                            "search": "{{__('Search:')}}",
                            "zeroRecords": "{{__('No matching records found')}}"
                        },

                        responsive: true,

                        "order": [
                            [0, 'desc']
                        ],

                        "lengthMenu": [
                            [20, 50, 100, 500, -1],
                            [20, 50, 100, 500, "All"] // change per page values here
                        ],

                        "pageLength": 50,
                    });

                dataTable.on('draw.dt', function () {
                    Page.listenRowEdit();
                    Page.listenRowDelete();
                });

                $('.refresh-toggle').click(function(){
                    dataTable.ajax.reload();
                });
            };
            
            return {
                init: function(){
                    handleNewRecord();
                    handleDatatable();
                },

                listenRowEdit: function() {
                    var id, formAction, input, name;
                    var label = formModalElement.find('#modal-label');
                    var form = formModalElement.find('#modal-form');

                    $('.edit-row').click(function(e){
                        e.preventDefault();

                        if(id = $(this).data('id')){
                            label.html('{{__('Edit Record')}}');
                            formAction = $(this).attr('href');

                            $.each(dataTable.row('#'+id).data(), function(key, value){
                                value = value ? value : '';

                                if(input = form.find(':input[name="' + key + '"]')){
                                    $.each(input, function(aKey, aValue){
                                        $(aValue).val(value);
                                    });
                                }

                                if(input = form.find(':input[name="' + key + '[]"]')){
                                    var multipleValues = value.split(",");

                                    $.each(input, function(aKey, aValue){
                                        if(!$(aValue).is('select')){
                                            if(!$(aValue).is('input[type="text"], input[type="hidden"], textarea')){
                                                if($.inArray($(aValue).val(), multipleValues)){
                                                    $(aValue).prop("checked", true);
                                                }else{
                                                    $(aValue).prop("checked", false);
                                                }
                                            }else{
                                                $(aValue).val(value);
                                            }
                                        }else{
                                            $(aValue).find('option:selected').prop("selected", false);

                                            $.each(multipleValues, function(bKey, bValue){
                                                $(aValue).find('option[value="' + bValue + '"]').prop("selected", true);
                                            });
                                        }
                                    });
                                }
                            });

                            Global.prepareForm(form);
                            formModalElement.modal('show');

                            Global.bindModalAjaxForm(form, formAction, 'PUT', dataTable);
                        }

                        return false;
                    });
                },

                listenRowDelete: function () {
                    var id, link;

                    $('.delete-row').click(function(e){
                        e.preventDefault();
                        
                        if(id = $(this).data('id')){
                            link = $(this).attr('href');
                            
                            swal({
                                title: "{{__('Are you sure?')}}",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "{{__('Yes, remove it!')}}",
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            }, function () {
                                $.ajax({
                                    url: link,
                                    method: 'DELETE',
                                    success: function(response) {
                                        var title = "{{__('Successful')}}";
                                        
                                        swal(title, response, "success");
                                        
                                        dataTable.ajax.reload();
                                    },
                                    error: function(xhr){
                                        swal.close();
                                        
                                        var response = xhr.responseJSON;

                                        if($.isPlainObject(response)){
                                            $.each(response.errors, function(key, value){
                                                Global.notifyDanger(value[0]);
                                            });
                                        }else{
                                            Global.notifyDanger(response);
                                        }
                                    }
                                });
                            });
                        }

                        return false;
                    });
                }
            }
        }();

        $(document).ready(function(){
            Page.init();
        });
	</script>
@endpush