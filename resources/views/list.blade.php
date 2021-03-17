<style>
    .files > li {
        float: left;
        width: 150px;
        border: 1px solid #eee;
        margin-bottom: 10px;
        margin-right: 10px;
        position: relative;
    }

    .files > li > .file-select {
        position: absolute;
        top: -4px;
        left: -1px;
    }

    .file-icon {
        text-align: center;
        font-size: 65px;
        color: #666;
        display: block;
        height: 100px;
    }

    .file-info {
        text-align: center;
        padding: 10px;
        background: #f4f4f4;
    }

    .file-name {
        font-weight: bold;
        color: #666;
        display: block;
        overflow: hidden !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
    }

    .file-size {
        color: #999;
        font-size: 12px;
        display: block;
    }

    .files {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .file-icon.has-img {
        padding: 0;
    }

    .file-icon.has-img > img {
        max-width: 100%;
        height: auto;
        max-height: 92px;
    }

</style>

<script data-exec-on-popstate>

    $(function () {
        $('.file-delete').click(function () {

            var id = $(this).data('id');

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
                                'id[]': [id],
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

        $('.file-upload').on('change', function () {
            $('.file-upload-form').submit();
        });

        $('#file-move').on('submit', function (event) {

            event.preventDefault();

            var form = $(this);

            var name = form.find('[name=new]').val();
            var id = form.find('[name=id]').val();

            $.ajax({
                method: 'put',
                url: '{{ route('filesystem-rename') }}',
                data: {
                    id: id,
                    name: name,
                    _token:LA.token,
                },
                success: function (data) {
                    $.pjax.reload('#pjax-container');

                    if (typeof data === 'object') {
                        if (data.status) {
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

        $('.file-select>input').iCheck({checkboxClass: 'icheckbox_minimal-blue'});

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
                    <label class="btn btn-default btn"{{-- data-toggle="modal" data-target="#uploadModal"--}} >
                        <i class="fa fa-upload"></i>&nbsp;&nbsp;{{ trans('admin.upload') }}
                        <form action="{{route('filesystem-upload')}}" method="post" class="file-upload-form"
                              enctype="multipart/form-data" pjax-container>
                            <input type="file" name="file[]" class="hidden file-upload" multiple>
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
                {{--<ol class="breadcrumb" style="margin-bottom: 10px;">

                   <li><a href="{{ route('media-index') }}"><i class="fa fa-th-large"></i> </a></li>

                      @foreach($nav as $item)
                          <li><a href="{{ $item['url'] }}"> {{ $item['name'] }}</a></li>
                      @endforeach
                </ol>--}}
                <ul class="files clearfix">

                    @if ( $files->isEmpty())
                        <li style="height: 200px;border: none;"></li>
                    @else
                        @foreach($files as $item)
                            <li>
                            <span class="file-select">
                                <input type="checkbox" value="{{ $item['id'] }}"/>
                            </span>
                                <div class="file-info">
                                    <a
                                        class="file-name" title="{{ $item['name'] }}">
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
                                    <span class="file-size">
                              {{ $item['file_size'] }}&nbsp;

                                <div class="btn-group btn-group-xs pull-right">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                            data-toggle="dropdown">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#" class="file-rename" data-toggle="modal" data-target="#moveModal"
                                               data-name="{{ $item['name'] }}" data-id="{{ $item['id'] }}">Rename</a></li>
                                        <li><a href="#" class="file-delete"
                                               data-id="{{ $item['id'] }}">Delete</a></li>
                                        <li><a target="_blank" href="{{ $item['url'] }}">Download</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" data-toggle="modal" data-target="#urlModal"
                                               data-url="{{ $item['url'] }}">Url</a></li>
                                    </ul>
                                </div>
                            </span>
                                </div>
                            </li>

                        @endforeach
                    @endif
                </ul>

                {{$files->links()}}
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

<div class="modal fade" id="newFolderModal" tabindex="-1" role="dialog" aria-labelledby="newFolderModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="newFolderModalLabel">New folder</h4>
            </div>
            <form id="new-folder">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name"/>
                    </div>
                    {{ csrf_field() }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
