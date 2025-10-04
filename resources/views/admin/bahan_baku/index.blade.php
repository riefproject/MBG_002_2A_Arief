@extends('layouts.app')

@section('content')
    @include('admin.bahan_baku.partials.content', ['bahanBakus' => $bahanBakus])
@endsection
