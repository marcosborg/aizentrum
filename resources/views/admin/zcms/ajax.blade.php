<div class="card">
    <div class="card-header">
        Resultados
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped datatable" data-info='@json($newArray)' id="zcm_data">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>Tipo de Chamada</th>
                    <th>Status</th>
                    <th>Carro</th>
                    <th>Produto</th>
                    <th>Solicitação</th>
                    <th>Recusa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($newArray as $line)
                <tr>
                    <td>{{ $line['id'] }}</td>
                    <td>{{ $line['date'] }}</td>
                    <td>{{ $line['client'] }}</td>
                    <td>{{ $line['salesman'] }}</td>
                    <td>{{ $line['calltype'] }}</td>
                    <td>{{ $line['status'] }}</td>
                    <td>{{ $line['car'] }}</td>
                    <td>{{ $line['product'] }}</td>
                    <td>{{ $line['request'] }}</td>
                    <td>{{ $line['declined'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
