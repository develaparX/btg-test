@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-sm-10">
        <div class="card">
            <div class="card-header">
                <h4>Edit USER</h4>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form action="{{ route('users.update', $id) }}" method="POST" id="userForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- User Information -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Masukkan nama anda" value="{{ old('name') ?? $customer['name'] }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="birth_date" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date" 
                               placeholder="Pilih tanggal" value="{{ old('birth_date') ?? date('Y-m-d', strtotime($customer['date_of_birth'])) }}" required>
                        @error('birth_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nationality_id" class="form-label">Kewarganegaraan</label>
                        <select class="form-control" id="nationality_id" name="nationality_id" required>
                            <option value="">Pilih kewarganegaraan</option>
                            @foreach($nationalities as $nationality)
                                <option value="{{ $nationality['id'] }}" 
                                    {{ (old('nationality_id') ?? $customer['nationality_id']) == $nationality['id'] ? 'selected' : '' }}>
                                    {{ $nationality['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('nationality_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               placeholder="Masukkan nomor telepon" value="{{ old('phone') ?? $customer['phone_number'] }}" required>
                        @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Masukkan email" value="{{ old('email') ?? $customer['email'] }}" required>
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Family Section -->
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                        <h5>Keluarga</h5>
                        <button type="button" class="btn-add-family" onclick="addFamilyMember()">
                            + Tambah Keluarga
                        </button>
                    </div>

                    <div id="familyContainer">
                        <!-- Existing family members will be loaded here -->
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Update Data</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let familyIndex = 0;

function addFamilyMember() {
    const container = document.getElementById('familyContainer');
    const familyMember = document.createElement('div');
    familyMember.className = 'family-member';
    familyMember.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-lg-3 col-md-6 col-12">
                <label class="form-label family-label">Nama</label>
                <input type="text" class="form-control family-input" name="family[${familyIndex}][name]" 
                       placeholder="Masukkan Nama" required>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <label class="form-label family-label">Tanggal Lahir</label>
                <input type="date" class="form-control family-input" name="family[${familyIndex}][birth_date]" 
                       placeholder="mm/dd/yyyy" required>
            </div>
            <div class="col-lg-4 col-md-8 col-12">
                <label class="form-label family-label">Hubungan</label>
                <select class="form-control family-input" name="family[${familyIndex}][relation]" required>
                    <option value="">Pilih hubungan</option>
                    <option value="Suami">Suami</option>
                    <option value="Istri">Istri</option>
                    <option value="Anak">Anak</option>
                    <option value="Orang Tua">Orang Tua</option>
                    <option value="Saudara">Saudara</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-12">
                <button type="button" class="btn btn-danger btn-sm family-remove-btn" onclick="removeFamilyMember(this)">
                    Hapus
                </button>
            </div>
        </div>
    `;
    container.appendChild(familyMember);
    familyIndex++;
}

function removeFamilyMember(button) {
    button.closest('.family-member').remove();
}

// Load existing family members from API data
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($customer['family_list']) && count($customer['family_list']) > 0)
        @foreach($customer['family_list'] as $family)
            addFamilyMemberWithData('{{ $family['name'] }}', '{{ $family['date_of_birth'] }}', '{{ $family['relation'] }}');
        @endforeach
    @else
        // Add at least one empty family member form
        addFamilyMember();
    @endif
});

function addFamilyMemberWithData(name, dateOfBirth, relation) {
    const container = document.getElementById('familyContainer');
    const familyMember = document.createElement('div');
    familyMember.className = 'family-member';
    familyMember.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-lg-3 col-md-6 col-12">
                <label class="form-label family-label">Nama</label>
                <input type="text" class="form-control family-input" name="family[${familyIndex}][name]" 
                       placeholder="Masukkan Nama" value="${name}" required>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <label class="form-label family-label">Tanggal Lahir</label>
                <input type="date" class="form-control family-input" name="family[${familyIndex}][birth_date]" 
                       placeholder="mm/dd/yyyy" value="${dateOfBirth}" required>
            </div>
            <div class="col-lg-4 col-md-8 col-12">
                <label class="form-label family-label">Hubungan</label>
                <select class="form-control family-input" name="family[${familyIndex}][relation]" required>
                    <option value="">Pilih hubungan</option>
                    <option value="Suami" ${relation === 'Suami' ? 'selected' : ''}>Suami</option>
                    <option value="Istri" ${relation === 'Istri' ? 'selected' : ''}>Istri</option>
                    <option value="Anak" ${relation === 'Anak' ? 'selected' : ''}>Anak</option>
                    <option value="Orang Tua" ${relation === 'Orang Tua' ? 'selected' : ''}>Orang Tua</option>
                    <option value="Saudara" ${relation === 'Saudara' ? 'selected' : ''}>Saudara</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-12">
                <button type="button" class="btn btn-danger btn-sm family-remove-btn" onclick="removeFamilyMember(this)">
                    Hapus
                </button>
            </div>
        </div>
    `;
    container.appendChild(familyMember);
    familyIndex++;
}
</script>
@endsection