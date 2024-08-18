@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<div class="contact-form__content">
  <div class="contact-form__heading">
    @if (Auth::check())
    <h3>{{ Auth::user()->name }}さんお疲れ様です！</h3>
    @endif
  </div>
  <div class="parent__container">
    <div class="child__container">
      <form action="/workin" method="post">
        @csrf
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
        @if ($time == true)
        <button class="attendance__button-submit" type="submit">
          <p>勤務開始</p>
        </button>
        @else
        <button class="attendance__button-submitdisabled" type="submit" disabled>
          <p>勤務開始</p>
        </button>
        @endif
      </form>
    </div>
    <div class="child__container">
      <form action="/workout" method="post">
        @method('PATCH')
        @csrf
        @if ($timeend == true)
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
        <button class="attendance__button-submit" type="submit">
          <p>勤務終了</p>
        </button>
        @else
        <button class="attendance__button-submitdisabled" type="submit" disabled>
          <p>勤務終了</p>
        </button>
        @endif
      </form>
    </div>
    <div class="child__container">
      <form action="/breakin" method="post">
        @method('PATCH')
        @csrf
        <input type="hidden" name="time_id" value="{{ Auth::user()->id }}">
        @if ($timeworking == true)
        <button class="attendance__button-submit" type="submit">
          <p>休憩開始</p>
        </button>
        @else
        <button class="attendance__button-submitdisabled" type="submit" disabled>
          <p>休憩開始</p>
        </button>
        @endif
      </form>
    </div>
    <div class="child__container">
      <form action="/breakout" method="post">
        @method('PATCH')
        @csrf
        <input type="hidden" name="time_id" value="{{ Auth::user()->id }}">
        @if ($restend == true)
        <button class="attendance__button-submit" type="submit">
          <p>休憩終了</p>
        </button>
        @else
        <button class="attendance__button-submitdisabled" type="submit" disabled>
          <p>休憩終了</p>
        </button>
        @endif
      </form>
    </div>
  </div>
</div>
@endsection