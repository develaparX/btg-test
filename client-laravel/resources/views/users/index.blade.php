@extends('layouts.app')

@section('title', 'Users List')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-md-11">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar User</h4>
                <a href="{{ route('users.create') }}" class="btn btn-light">
                    Tambah User
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Tanggal Lahir</th>
                                <th>Kewarganegaraan</th>
                                <th>Jumlah Keluarga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($customers) > 0)
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>{{ $customer['id'] }}</td>
                                        <td>{{ $customer['name'] }}</td>
                                        <td>{{ $customer['email'] }}</td>
                                        <td>{{ date('d/m/Y', strtotime($customer['date_of_birth'])) }}</td>
                                        <td>{{ $customer['nationality']['name'] ?? 'N/A' }}</td>
                                        <td>{{ count($customer['family_list'] ?? []) }} orang</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('users.show', $customer['id']) }}" class="btn btn-info btn-sm">Lihat</a>
                                                <a href="{{ route('users.edit', $customer['id']) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="{{ route('users.destroy', $customer['id']) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <em>Belum ada data customer</em>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection