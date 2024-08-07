@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attend.css') }}">
@endsection

@section('content')

<div class="contact-form__content">
  <div class="contact-form__heading">
    @if (Auth::check())
    <h3>{{ Auth::user()->name }}さんの勤怠一覧</h3>
    @endif
  </div>
  <div class="attend-table">
    <table class="attend-table__inner">
      <tr class="attend-table__row">
        <th class="attend-table__header">月日</th>
        <th class="attend-table__header">勤務開始</th>
        <th class="attend-table__header">勤務終了</th>
        <th class="attend-table__header">休憩時間</th>
        <th class="attend-table__header">勤務時間</th>
      </tr>
      <tr class="attend-table__row">
      @foreach ($times as $time)
        <td class="attend-table__item">
          {{ $time['date']->format('Y-m-d') }}
        </td>
        <td class="attend-table__item">
          {{ $time['start_work']->format('H:i:s') }}
        </td>
        <td class="attend-table__item">
          {{ $time['end_work']->format('H:i:s') }}
        </td>
        <td class="attend-table__item">
          {{ $time['breaktime'] }}
        </td>
        <td class="attend-table__item">
          {{ $time['worktime'] }}
        </td>
      </tr>
      @endforeach
    </table>
      {{ $times->appends(request()->query())->links('pagination::custom')}}
  </div>
</div>
@endsection