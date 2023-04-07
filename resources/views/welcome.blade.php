<!DOCTYPE html>
<html lang="en">
  @include('layouts/header')
  <body>
    @include('layouts/topbar')
    @include('layouts/navbar')
    <div class="container">
        <div class="row row-cols-1 row-cols-md-3 g-4">
          @foreach($games as $game)
            @include('layouts/components/card')
          @endforeach
          </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>
