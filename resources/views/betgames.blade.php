<!DOCTYPE html>
<html lang="en">
  @include('layouts/header')
  <body>
    @include('layouts/topbar')
    @include('layouts/navbar')
    <div class="iframe-container" style="margin:0 auto;">
      <div id="iframe-content" style="margin:0 auto;">
        <div id="betgames_div_iframe"></div>
        <script>
            const clientUrl = 'https://integrations01-webiframe.betgames.tv';

            const script = document.createElement('script');
    
            script.onload = function () {
                window.BetGames.setup({
                    containerId: 'betgames_div_iframe',
                    clientUrl: clientUrl,
                    apiUrl: 'integrations01.betgames.tv',
                    partnerCode: 'goda_test',
                    partnerToken: '{{ $token }}',
                    language: 'en',
                    timezone: '3',
                    defaultPage: '',
                    defaultGame: '{{ $game_id }}',
                });
            };
            script.type = 'text/javascript';
            script.src = clientUrl + '/public/betgames.js' + '?' + Date.now();
    
            document.head.appendChild(script);
        </script>
    </div>
  </body>
</html>