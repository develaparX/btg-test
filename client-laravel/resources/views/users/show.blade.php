@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9 col-sm-11">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Detail USER</h4>
                <div>
                    <a href="{{ route('users.edit', $customer['id']) }}" class="btn btn-light btn-sm me-2">
                        Edit
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm">
                        Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informasi Personal</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $customer['id'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama:</strong></td>
                                <td>{{ $customer['name'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $customer['email'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Lahir:</strong></td>
                                <td>{{ date('d/m/Y', strtotime($customer['date_of_birth'])) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nomor Telepon:</strong></td>
                                <td>{{ $customer['phone_number'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kewarganegaraan:</strong></td>
                                <td>{{ $customer['nationality']['name'] ?? 'N/A' }} ({{ $customer['nationality']['code'] ?? 'N/A' }})</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Informasi Keluarga</h5>
                        @if(isset($customer['family_list']) && count($customer['family_list']) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Hubungan</th>
                                            <th>Tanggal Lahir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customer['family_list'] as $family)
                                            <tr>
                                                <td>{{ $family['name'] }}</td>
                                                <td>{{ $family['relation'] }}</td>
                                                <td>{{ date('d/m/Y', strtotime($family['date_of_birth'])) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Tidak ada data keluarga</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection