<nav class="navbar navbar-top navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="/">
        <i class="fa fa-superpowers" aria-hidden="true"></i>
        RandomBET
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
        <div class="text-end">
          @guest
            <a class="btn btn-outline-light me-2" href="/login">Login</a>
            <a class="btn btn-outline-light me-2" href="/register">Register</a>
          @else
          <div class="dropdown" id="buttons">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
              {{Auth::user()->username }}
            </button>
            <button type="button" class="btn btn-secondary balance" data-id="{{Auth::user()->id}}" data-bs-toggle="modal" data-bs-target="#balance">
              {{Auth::user()->balance/100}} €
            </button>
            
            @include('layouts.components.modal')
            <ul class="dropdown-menu">
              <li>
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <a style="color: black;" class="dropdown-item secondary" href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                                  this.closest('form').submit();">
                  Logout
                </a>
                </form>
              </li>
            </ul>
          </div>
          @endguest
      </div>
    </div>
  </nav>