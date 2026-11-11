@extends('layouts.global')

@section('title', 'Riwayat Pengajuan Dokumen')

@section('content')
@include('partials.riwayat-dokumen-tabel', ['riwayat' => $riwayat, 'backRoute' => 'rt.persetujuan-dokumen'])
@endsection
