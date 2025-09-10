@extends('layouts.app')

@section('content')
    <div class="detail-container">
        <p class="error-message">Data yang Anda cari tidak ditemukan.</p>
        <a href="{{ route('project.index') }}" class="back-button">&larr; Kembali ke Daftar</a>
    </div>
@endsection