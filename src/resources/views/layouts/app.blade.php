<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>勤怠管理</title>
  <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <a class="header__logo" href="/">
        Atte
      </a>
      <nav class="nav">
          <ul class="nav-list">
            <li class="nav-item"><a href="{{ url('/') }}">ホーム</a></li>
            <li class="nav-item"><a href="{{ url('attendance') }}">日付一覧</a></li>
            <li class="nav-item"><form action="/logout" method="post">
              @csrf
              <button>ログアウト</button></form>
            </li>
          </ul>
      </nav>
    </div>
  </header>

  <main>
    @yield('content')
  </main>

  <footer class="footer">
    <div class="copyright">Atte,inc</div>
  </footer>
</body>

</html>