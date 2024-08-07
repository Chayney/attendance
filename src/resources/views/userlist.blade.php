@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attend.css') }}">
@endsection

@section('content')
<div class="contact-form__content">
  <div class="contact-form__heading">
    <h3>ユーザー一覧</h3>
  </div>
  <div class="attend-table">
    <table class="attend-table__inner">
      <tr class="attend-table__row">
        <th class="attend-table__header">名前</th>
        <th class="attend-table__header">メールアドレス</th>
      </tr>
      <tr class="attend-table__row">
      @foreach ($users as $user)
      <form action="/userlist" method="get">
        <td class="attend-table__item">
          {{ $user['name'] }}
        </td>
        <td class="attend-table__item">
          {{ $user['email'] }}
        </td>  
      </tr>
      <input type="hidden" name="id" value="{{ $user['id'] }}">
      </form>
      @endforeach
    </table>
      {{ $users->appends(request()->query())->links('pagination::custom')}}
  </div>
</div>
@endsection