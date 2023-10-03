
        <div class="table-responsive fixed-solution">
            <table class="table table-hover table-striped table-sm">
                <thead class="thead-light">
                <tr>
                    <th style="width: 1px;">
                    <label class="containerchekbox">
                        <input type="checkbox" id="selectAllChekBox" value="">
                        <span class="checkmark"></span>
                    </label>
                    </th>
                    <th> Gateway</th>
                    <th> Data</th>
                    <th> Log</th>
                </tr>
                </thead>
                <tbody class="tbodyCustom">
                <form class="form">
                    {{ csrf_field() }}
                    @foreach($data as $result)
                        <tr>
                            <td>
                                <label class="containerchekbox">
                                    <input type="checkbox" name="selected[]" value="{{$result->id}}">
                                    <span class="checkmark"></span>
                                </label>
                            </td>

                            <td>{{$result->gateway}}</td>
                            <td>{{ date('d/m/Y H:i:s',strtotime($result->created_at))}}</td>
                            <td><button type="button" class="btn btn-sm btn-secondary" data-log-id="{{$result->id}}" id="btn-modal-log">LOG</button></td>
                        </tr>
                    @endforeach
                </form>
                </tbody>
            </table>
        </div>
        <div class="paginate">
            {!! $data->withQueryString()->links('pagination::bootstrap-4') !!}
        </div>

