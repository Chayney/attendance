@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attend.css') }}">
@endsection

@section('content')

<div class="contact-form__content">
  <div class="contact-form__heading">
    <h3>月日</h3>
  </div>
  <div class="attend-table">
    <table class="attend-table__inner">
      <tr class="attend-table__row">
        <th class="attend-table__header">名前</th>
        <th class="attend-table__header">勤務開始</th>
        <th class="attend-table__header">勤務終了</th>
        <th class="attend-table__header">休憩時間</th>
        <th class="attend-table__header">勤務時間</th>
      </tr>
      <tr class="attend-table__row">
        @foreach ($attends as $attend)
        <td class="attend-table__item">
          {{ $attend['time']['name'] }}
        </td>
        <td class="attend-table__item">
          {{ $attend['start_work'] }}
        </td>
        <td class="attend-table__item">
          {{ $attend['end_work'] }}
        </td>
        <td class="attend-table__item">
          {{ $attend['break_in'] }}
        </td>
        <td class="attend-table__item">
          {{ $attend['worktime'] }}
        </td>
      </tr>
      @endforeach
    </table>
  </div>
</div>
@endsection