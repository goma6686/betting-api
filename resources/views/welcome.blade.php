<!doctype html>
<html lang="en">
  @include('layouts/app')
  <body>
    @include('layouts/topbar')
    @include('layouts/navbar')
    <div class="container">
        <div class="row row-cols-1 row-cols-md-2 g-4">
          @foreach($games as $game)
            <div class="col">
              <div class="col">
                  <div class="card">
                      <img src="..." class="card-img-top" alt="...">
                      <div class="card-body">
                          <h5 class="card-title">{{$game->game_name}}</h5>
                          <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                      </div>
                  </div>
              </div>
            </div>
            @endforeach
          </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>
