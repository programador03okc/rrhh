@include('layout.head')
@include('layout.menu_rrhh')
@include('layout.body')

<div class="page-main" type="planilla">
    <legend><h2>Resultado de Envío de Boletas</h2></legend>
    <div class="row">
        <div class="col-md-6">
            <div class="alert alert-success alert-dismissible">
                <h4><i class="icon fa fa-check"></i> Lista de Correos Recibidos!</h4>
                <ul>
                    @foreach ($recib as $itemR)
                        <li>{{ $itemR }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-danger alert-dismissible">
                <h4><i class="icon fa fa-ban"></i> Lista de Correos No Recibidos!</h4>
                <ul>
                    @foreach ($error as $itemE)
                        <li>{{ $itemE }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <a href="planilla" class="btn btn-sm btn-primary">Volver a la Planilla de Remuneraciones</a>
        </div>
        <div class="col-md-6">
            <a href="utilidades" class="btn btn-sm btn-primary">Volver a la Planilla de Utilidades</a>
        </div>
    </div>
</div>