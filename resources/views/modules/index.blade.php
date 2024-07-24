<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Manage Modules</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ URL('/') }}">Home</a></li>
        </ol>
    </div>
</div>
<br>
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Modules</h3>
        </div>
        <br>
        <div class="card-body p-0">
            <table class="table table-striped projects" id="id_table">
                <thead>
                    <tr>
                        <th style="width: 10%">
                            Name
                        </th>
                        <th style="width: 15%">
                            Install Npm
                        </th>
                        <th style="width: 15%">
                            Remove Npm
                        </th>
                        <th style="width: 15%">
                            Install Composer
                        </th>
                        <th style="width: 15%">
                            Remove Composer
                        </th>
                        <th style="width: 15%">
                            Enable Module
                        </th>
                        <th style="width: 15%">
                            Disable Module
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($modules as $key => $module)
                        <tr>
                            <td style="width: 10%">
                                {{ $key }}
                            </td>
                            <td style="width: 15%">
                                <a class="btn btn-info" href="{{ route('installModuleNpm', $key) }}">Here</a>
                            </td>
                            <td style="width: 15%">
                                <a class="btn btn-info" href="{{ route('removeModuleNpm', $key) }}">Here</a>
                            </td>
                            
                            <td style="width: 15%">
                                <a class="btn btn-info" href="{{ route('installModulecomposer', $key) }}">Here</a>
                            </td>

                            <td style="width: 15%">
                                <a class="btn btn-info" href="{{ route('removeModuleComposer', $key) }}">Here</a>
                            </td>

                            <td style="width: 15%">
                                <a class="btn btn-info" href="{{ route('enableModule', $key) }}">Here</a>
                            </td>

                            <td style="width: 15%">
                                <a class="btn btn-info" href="{{ route('disableModule', $key) }}">Here</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <span>Status Output: {{' '.$output}}</span>
        </div>
    </div>
</section>
@section('css')
    <link rel="stylesheet" href="/css/app.css">
@stop