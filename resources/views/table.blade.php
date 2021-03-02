<style>
    .files > li {
        float: left;
        width: 150px;
        border: 1px solid #eee;
        margin-bottom: 10px;
        margin-right: 10px;
        position: relative;
    }

    .file-icon {
        text-align: left;
        font-size: 25px;
        color: #666;
        display: block;
        float: left;
    }

    .action-row {
        text-align: center;
    }

    .file-name {
        font-weight: bold;
        color: #666;
        display: block;
        overflow: hidden !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
        float: left;
        margin: 7px 0px 0px 10px;
    }

    .file-icon.has-img > img {
        max-width: 100%;
        height: auto;
        max-height: 30px;
    }

</style>

<script data-exec-on-popstate>

    $(function () {
        $('.file-delete').click(function () {

            var path = $(this).data('id');

            swal({
                title: "{{ trans('admin.delete_confirm') }}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{ trans('admin.confirm') }}",
                showLoaderOnConfirm: true,
                closeOnConfirm: false,
                cancelButtonText: "{{ trans('admin.cancel') }}",
                preConfirm: function () {
                    return new Promise(function (resolve) {

                        $.ajax({
                            method: 'delete',
                            url: '{{ route('filesystem-delete') }}',
                            data: {
                                'id[]': [path],
                                _token: LA.token
                            },
                            success: function (data) {
                                $.pjax.reload('#pjax-container');

                                resolve(data);
                            }
                        });

                    });
                }
            }).then(function (result) {
                var data = result.value;
                if (typeof data === 'object') {
                    if (data.status) {
                        swal(data.message, '', 'success');
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            });
        });

        $('#moveModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var name = button.data('name');

            var modal = $(this);
            modal.find('[name=id]').val(button.data('id'))
            modal.find('[name=new]').val(name)
        });

        $('#urlModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var url = button.data('url');

            $(this).find('input').val(url)
        });


        // closeModal();

        $('.file-upload').on('change', function () {
            $('.file-upload-form').submit();
        });

        $('#file-move').on('submit', function (event) {

            event.preventDefault();

            var form = $(this);

            var id = form.find('[name=id]').val();
            var name = form.find('[name=new]').val();

            $.ajax({
                method: 'put',
                url: '{{ route('filesystem-rename') }}',
                data: {
                    id: id,
                    name: name,
                    _token: LA.token,
                },
                success: function (data) {
                    $.pjax.reload('#pjax-container');

                    if (typeof data === 'object') {
                        if (data.code === 0) {
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    }
                }
            });

            closeModal();
        });

        function closeModal() {
            $("#moveModal").modal('toggle');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        }

        $('.media-reload').click(function () {
            $.pjax.reload('#pjax-container');
        });

        $('.files-select-all').on('ifChanged', function (event) {
            if (this.checked) {
                $('.grid-row-checkbox').iCheck('check');
            } else {
                $('.grid-row-checkbox').iCheck('uncheck');
            }
        });

        $('.file-select input').iCheck({checkboxClass: 'icheckbox_minimal-blue'}).on('ifChanged', function () {
            if (this.checked) {
                $(this).closest('tr').css('background-color', '#ffffd5');
            } else {
                $(this).closest('tr').css('background-color', '');
            }
        });

        $('.file-select-all input').iCheck({checkboxClass: 'icheckbox_minimal-blue'}).on('ifChanged', function () {
            if (this.checked) {
                $('.file-select input').iCheck('check');
            } else {
                $('.file-select input').iCheck('uncheck');
            }
        });

        $('.file-delete-multiple').click(function () {
            var files = $(".file-select input:checked").map(function () {
                return $(this).val();
            }).toArray();

            if (!files.length) {
                return;
            }

            swal({
                title: "{{ trans('admin.delete_confirm') }}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{ trans('admin.confirm') }}",
                showLoaderOnConfirm: true,
                closeOnConfirm: false,
                cancelButtonText: "{{ trans('admin.cancel') }}",
                preConfirm: function () {
                    return new Promise(function (resolve) {

                        $.ajax({
                            method: 'delete',
                            url: '{{ route('filesystem-delete') }}',
                            data: {
                                'id[]': files,
                                _token: LA.token
                            },
                            success: function (data) {
                                $.pjax.reload('#pjax-container');

                                resolve(data);
                            }
                        });

                    });
                }
            }).then(function (result) {
                var data = result.value;
                if (typeof data === 'object') {
                    if (data.status) {
                        swal(data.message, '', 'success');
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            });
        });

        $('table>tbody>tr').mouseover(function () {
            $(this).find('.btn-group').removeClass('hide');
        }).mouseout(function () {
            $(this).find('.btn-group').addClass('hide');
        });
    });


</script>

<div class="row">
    <!-- /.col -->
    <div class="col-md-12">
        <div class="box box-primary">

            <div class="box-body no-padding">

                <div class="mailbox-controls with-border">
                    <div class="btn-group">
                        <a href="" type="button" class="btn btn-default btn media-reload" title="Refresh">
                            <i class="fa fa-refresh"></i>
                        </a>
                        <a type="button" class="btn btn-default btn file-delete-multiple" title="Delete">
                            <i class="fa fa-trash-o"></i>
                        </a>
                    </div>
                    <!-- /.btn-group -->
                    <label class="btn btn-default btn"{{-- data-toggle="modal" data-target="#uploadModal"--}}>
                        <i class="fa fa-upload"></i>&nbsp;&nbsp;{{ trans('admin.upload') }}
                        <form action="{{ route('filesystem-upload') }}" method="post" class="file-upload-form"
                              enctype="multipart/form-data" pjax-container>
                            <input type="file" name="files[]" class="hidden file-upload" multiple>
                            {{ csrf_field() }}
                        </form>
                    </label>

                    <!-- /.btn-group -->

                    <div class="btn-group">
                        <a href="{{ route('filesystem-index', array_merge(request()->all(),[ 'view' => 'table']) ) }}"
                           class="btn btn-default {{ request('view') == 'table' ? 'active' : '' }}"><i
                                class="fa fa-list"></i></a>
                        <a href="{{ route('filesystem-index', array_merge(request()->all(),[ 'view' => 'list'])) }}"
                           class="btn btn-default {{ request('view') == 'list' ? 'active' : '' }}"><i
                                class="fa fa-th"></i></a>
                    </div>

                    {{--<form action="{{ $url['index'] }}" method="get" pjax-container>--}}
                    <div class="input-group input-group-sm pull-right goto-url" style="width: 250px;">
                        <input type="text" name="path" class="form-control pull-right"
                               value="{{ request('name','') }}">

                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-arrow-right"></i></button>
                        </div>
                    </div>
                    {{--</form>--}}

                </div>

                <!-- /.mailbox-read-message -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer">

                @if ($files->isNotEmpty())
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th width="40px;">
                            <span class="file-select-all">
                            <input type="checkbox" value=""/>
                            </span>
                            </th>
                            <th>{{ trans('admin.name') }}</th>
                            <th></th>
                            <th width="200px;">{{ trans('admin.time') }}</th>
                            <th width="100px;">{{ trans('admin.size') }}</th>
                        </tr>
                        @foreach($files as $item)
                            <tr>
                                <td style="padding-top: 15px;">
                            <span class="file-select">
                            <input type="checkbox" value="{{ $item['id'] }}"/>
                            </span>
                                </td>
                                <td>
                                    <a class="file-name" title="{{ $item['name'] }}">
                                        @if($item['type']=='image')
                                            <span class="file-icon has-img">
                                                <img src=" {{ $item['url'] }}" alt="Attachment"/>
                                            </span>
                                        @else
                                            <span class="file-icon">
                                                <i class=" {{ $item['icon']??'fa fa-file' }}"></i>
                                            </span>
                                        @endif
                                        {{ basename($item['name']) }}
                                    </a>
                                </td>

                                <td class="action-row">
                                    <div class="btn-group btn-group-xs hide">
                                        <a class="btn btn-default file-rename" data-toggle="modal"
                                           data-target="#moveModal" data-name="{{ $item['name'] }}"
                                           data-id="{{ $item['id'] }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a class="btn btn-default file-delete" data-id="{{ $item['id'] }}">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <a target="_blank" href="{{ $item['url'] }}" class="btn btn-default">
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <a class="btn btn-default" data-toggle="modal" data-target="#urlModal"
                                           data-url="{{ $item['url'] }}">
                                            <i class="fa fa-internet-explorer"></i>
                                        </a>
                                    </div>

                                </td>
                                <td>{{ $item['created_at'] }}&nbsp;</td>
                                <td>{{ $item['file_size'] }}&nbsp;</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$files->links()}}
                @endif

            </div>
            <!-- /.box-footer -->
            <!-- /.box-footer -->
        </div>
        <!-- /. box -->
    </div>
    <!-- /.col -->
</div>

<div class="modal fade" id="moveModal" tabindex="-1" role="dialog" aria-labelledby="moveModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="moveModalLabel">Rename</h4>
            </div>
            <form id="file-move">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Name:</label>
                        <input type="text" class="form-control" name="new"/>
                    </div>
                    <input type="hidden" name="id"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="urlModal" tabindex="-1" role="dialog" aria-labelledby="urlModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="urlModalLabel">Url</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
