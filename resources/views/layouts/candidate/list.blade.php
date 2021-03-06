@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <span class="float-left">
                            <h5><i class="fa fa-align-justify"></i>{{ __('Оролцогчид') }}</h5>
                        </span> <span class="float-right">

                    </div>

                    <div class="card-body">
                        @include('layouts.shared.alert')

                        <form action="{{ route('candidate.index')}}" method="GET" id="group">
                            <div class="form-group col-md-4">
                                <label for="selectgroup">Груп</label>
                                <select class="form-control" id="selectgroup" name="group_id">
                                    <option value="0">Сонгох...</option>
                                    @foreach ($groups as $key)
                                    <option @if($group_id==$key->id) {{ 'selected'}} @endif
                                        value="{{$key->id}}">{{$key->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- <div class="form-group col-md-4">
                            <label for="email">Имэйл хаяг:</label>
                            <input type="text" name="email" id="email">
                        </div>
                        <button type="submit" class="btn btn-primary">Хайх</button> -->

                        </form>

                        <table class="table table-bordered yajra-datatable user_table " id="user_table"
                            style="width: 100%; font-size:13.5px;">
                            <thead>
                                <tr>
                                    <th width="3px"><input type="checkbox" id="selectAll" /></th>
                                    <!-- <th width="5px">#</th> -->
                                    <th>Нэвтрэх нэр</th>
                                    <th>Нэр</th>
                                    <th>Овог</th>
                                    <th>Имэйл</th>
                                    <th>Бүлэг</th>
                                    <th>Үйлдэл</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($canditateList)
                                @foreach($canditateList as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->login }}</td>
                                    <td>{{ $item->firstname }}</td>
                                    <td>{{ $item->lastname }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>
                                        @foreach ($item->groups as $group )
                                        {{ $group->name }}
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="/candidate/assessments/{{$item->id}}" class="btn btn-primary"
                                            title="Өгсөн тестүүдийг харах"><i
                                                class="text-white cil-list-numbered"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                                @endif

                            </tbody>
                        </table>
                        <div class="modal fade" id="groupModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog .modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Групп-д нэмэх</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <form method="POST" action="">
                                                @csrf
                                                <input type="hidden" name="user_id" id="user_id">
                                                <div class="form-group row">
                                                    <label for="groups"
                                                        class="col-md-2 col-form-label text-md-right">{{ __('Групп') }}</label>
                                                    <div class="col-md-8">
                                                        <group {{--:selected="{{ $group_names->pluck('name') }}"--}}
                                                            class="@error('groups') is-invalid @enderror"></group>
                                                        @error('groups')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Хадгалах</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- {{ $users->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection
    @section('javascript')
    <style>
        div.dataTables_wrapper div.dataTables_length select {
            width: 60px !important;
            display: inline-block;
        }

    </style>

    <script>
        $(function () {

            $("#selectgroup").select2();

            $('#selectgroup').on('select2:select', function (e) {
                var data = e.params.data;
                console.log(data.id);
                // ajax sent
                $("#group").submit();
            });
        });


        $(document).ready(function () {
            $('body').on('click', '.addToGroup', function () {
                var id = $(this).data('id');
                $('#user_id').val(id);
                $('#groupModal').modal('show');
                // alert(id);
            })
        });

        $(document).ready(function () {
            $('body').on('click', '#selectAll', function () {
                if ($(this).hasClass('allChecked')) {
                    $('input[type="checkbox"]', '#user_table').prop('checked', false);
                } else {
                    $('input[type="checkbox"]', '#user_table').prop('checked', true);
                }
                $(this).toggleClass('allChecked');
            })
        });

        $(document).on('click', '#deleteMultiple', function () {
            var id = [];
            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Тийм',
                cancelButtonText: 'Үгүй'
            }).then((result) => {
                if (result.value) {
                    console.log(result.value)
                }
            })
        });


        $('body').on('click', '.delete', function () {
            var id = $(this).data("id");
            //  var firstname = $(this).data("firstname");
            //  console.log("participant id - " + id);
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Тийм',
                cancelButtonText: 'Үгүй'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "get",
                        url: "participants/destroy/" + id,
                        success: function (data) {
                            setTimeout(function () {
                                $('#confirmModal').modal('hide');
                                $('#user_table').DataTable().ajax.reload();
                            });
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                    Swal.fire(
                        'Deleted!',
                        'Амжилттай устгагдлаа',
                        'success'
                    )
                }
            })

            $('#select_all').click(function (event) {
                var $that = $(this);

                $(':checkbox').each(function () {
                    this.checked = $that.is(':checked');
                });
            });


            function addPost() {
                $('#add-group-modal').modal('show');
            }


        });

    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    @endsection
