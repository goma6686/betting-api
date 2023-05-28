<div class="col">
    <div class="col">
        <div class="card" style="width: 18rem;">
            <img src="{{ asset('game_banners/' . $game->banner_image) }}" class="card-img-top" alt="...">
            <div class="card-body text-center">
              <h5 class="card-title">{{$game->game_name}}</h5>
              <a href="/BetGames/{{$game->id}}" class="btn btn-danger">PLAY</a>
            </div>
        </div>
    </div>
</div>