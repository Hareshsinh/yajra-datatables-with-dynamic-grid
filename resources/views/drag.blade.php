<!DOCTYPE html>
<html>
<head>
    <title>Laravel 7 Datatables Tutorial - ItSolutionStuff.com</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/jeffreydwalter/ColReorderWithResize@9ce30c640e394282c9e0df5787d54e5887bc8ecc/ColReorderWithResize.js"></script>
    <style>
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid blue;
            border-right: 16px solid green;
            border-bottom: 16px solid red;
            border-left: 16px solid pink;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
            <div style="display:none" class="alert alert-success col-sm-12"></div>
            <h1>Yajra datatable ordering and resizing</h1>
            <input type="hidden" id="fieldwidth" value="{{ $fieldwidth }}">
            <br/>
            <div class="loader" style="display:none;position:absolute;top:50%;left:50%;padding:2px; z-index: 10"></div>
            <div class="container-fluidr">
                <div class="row ">
                    <div class="col-md-3 border border-secondary rounded align-items-center">
                        <div class="checkbox-data" style="margin-top: 3px;">
                        @foreach($checkedFields as $value)
                                <input type="checkbox" data-id="{{ $config[$value] }}" name='hide_columns[]' {{ (in_array($value,$checkedFields)) ? 'checked': '' }} value='{{ $value }}'> {{ $config[$value] }}
                                @php unset($config[$value]) @endphp
                            @endforeach
                            @foreach($config as $key=>$val)
                                    <input type="checkbox" data-id="{{ $val }}" name='hide_columns[]' {{ (in_array($key,$checkedFields)) ? 'checked': '' }} value='{{ $key }}'> {{ $val }}
                                @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <input type="button" class="btn btn-success btn-sm" id="but_showhide" value='save grids'>
                    </div>
                </div>

        </div>
        <table class="table table-bordered data-table">
            <thead>
            <tr class="data-id-info">
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <script type="text/javascript">
        $(function () {
            $(document).ajaxStart(function(){
                $("#wait").css("display", "block");
            });
            $(document).ajaxComplete(function(){
                $("#wait").css("display", "none");
            });
            var selected_arr = [];
            var tempCheckBox = [];
            var storeWidth = $("#fieldwidth").val().split(',')
            var storeWidthArr = []
            $.each(storeWidth, function (key,value) {
                storeWidthArr.push({ "width": value, "aTargets": [ key ] });
            });
            function getHeaderResize() {
                var headerResize = [];
                $.each($('.data-table thead tr th'), function (key, value) {
                    headerResize.push($(this).width());
                });
                return headerResize;
            }
            function setColumns() {
                $(".data-id-info").html('');
                selected_arr = [];
                $.each($('input[type="checkbox"]:checked'), function (key, value) {
                    if(this.value=='DT_RowIndex') {
                        selected_arr.push({data: this.value, name: this.value})
                    } else if(this.value=='action') {
                        selected_arr.push({data: this.value, name: this.value, orderable: false, searchable: false})
                    } else {
                        selected_arr.push({data: this.value, name: this.value});
                    }
                    $(".data-id-info").append("<th data-value="+this.value+" data-name="+$(this).attr('data-id')+">"+$(this).attr('data-id')+"</th>");
                });
            }
            setColumns();
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.drags') }}",
                columns: selected_arr,
                dom: 'Rlfrtip',
                aoColumnDefs:storeWidthArr,

            });
            // Hide & show columns
            $('#but_showhide').click(function(){
                var checked_arr = [];var unchecked_arr = [];

                // Read all checked checkboxes
                $.each($('input[type="checkbox"]:checked'), function (key, value) {
                    checked_arr.push(this.value);
                });

                // Read all unchecked checkboxes
                $.each($('input[type="checkbox"]:not(:checked)'), function (key, value) {
                    unchecked_arr.push(this.value);
                });
                console.log(checked_arr);
                // Hide the checked columns
                table.columns(checked_arr).visible(false);

                // Show the unchecked columns
                table.columns(unchecked_arr).visible(true);
                updateColsInfo();
                location.reload();
                table.ajax.reload( null, false );
            });

            $('.data-table') .on('mousedown.ColReorder touchstart.ColReorder', function(event) {
                setTimeout(function(){
                    $.each($('input[type="checkbox"]:not(:checked)'), function (key, value) {
                        tempCheckBox.push({value: this.value, name: $(this).attr('data-id')})
                    })
                    $(".checkbox-data").empty();
                    $.each($('.data-table thead tr th'), function (key, value) {
                        $(".checkbox-data").append(" <input type='checkbox' data-id="+$(this).attr('data-name')+" name='hide_columns[]' checked value="+$(this).attr('data-value')+">  "+$(this).attr('data-name'));
                    });
                    $.each(tempCheckBox, function (v,a) {
                        $(".checkbox-data").append(" <input type='checkbox' data-id="+a.name+" name='hide_columns[]' value="+a.value+">  "+a.name);
                    });
                    tempCheckBox = [];
                    updateColsInfo();
                }, 2000);
                // User has clicked on a column and is currently holding the mouse button down.
            })

            function updateColsInfo() {
                var checked_arr = [];var unchecked_arr = [];var checked_arrTr = [];
                // Read all checked checkboxes
                $.each($('input[type="checkbox"]:checked'), function (key, value) {
                    checked_arr.push(this.value);
                    checked_arrTr.push($(this).attr('data-id'));
                });
                $.ajax({
                    url: "{{ url('/post-column-list/users') }}",
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        fields: checked_arr,
                        fieldname: checked_arrTr,
                        widthData: getHeaderResize(),
                        user_id: 1,
                    },
                    beforeSend: function() {
                        $('.alert-success,.alert-danger').html('');
                        $('.alert-success,.alert-danger').hide();
                        $('.loader').show();
                    },
                    success: function(data){
                        $('.loader').hide();
                        if(data.code=='200') {
                            // $('.alert-success').show();
                            // $('.alert-success').append(data.status);
                        } else {
                            $('.alert-danger').show();
                            $('.alert-danger').append(data.status);
                        }
                    },
                    error: function (jqXHR) {
                        $('.loader').hide();
                        $('.alert-danger').show();
                        $('.alert-danger').append(JSON.parse(jqXHR.responseText).message);
                    }
                });
            }
        });
    </script>
</body>
</html>
