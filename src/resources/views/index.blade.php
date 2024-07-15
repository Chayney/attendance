@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<div class="contact-form__content">
  @if (session('message'))
  <div class="alert--success">
    {{ session('message') }}
  </div>
  @endif
  @if ($errors->any())
  <div class="alert--danger">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <div class="contact-form__heading">
    @if (Auth::check())
    <h3>{{ Auth::user()->name }}さんお疲れ様です！</h3>
    @endif
  </div>
  <div class="parent__container">
    <div class="child__container">
      <form action="/workin" method="post">
        @csrf
      <button type="submit" name="created_at" value="{{ old('created_at') }}">
        <p>勤務開始</p>
      </button>
      </form>
    </div>
    <div class="child__container">
      <form action="/workout" method="post">
      @method('PATCH')
      @csrf
      <button type="submit" name="created_at" value="{{ old('created_at') }}">
        <p>勤務終了</p>
      </button>
      </form>
    </div>
    <div class="child__container">
      <form action="/breakin" method="post">
      @method('PATCH')
      @csrf
      <button type="submit" name="break_in" value="">
      <input type="hidden" name='user_id' value="">
        <p>休憩開始</p>
      </button>
      </form>
    </div>
    <div class="child__container">
      <form action="/breakout" method="post">
      @method('PATCH')
      @csrf
      <button type="submit" name="updated_at" value="{{ old('updated_at') }}">
        <p>休憩終了</p>
      </button>
      </form>
    </div>
  </div>
</div>
@endsection