<!doctype html>
<html lang="en">
  @include('layouts/header')
  <body>
    @include('layouts/topbar')
    @include('layouts/navbar')
    <div class="iframe-container" style="margin:0 auto;">
      <div id="betgames_iframe"></div>
    </div>
    <script>
      const clientUrl = '';
      const script = document.createElement('script');

      script.onload = function () {
        window.BetGames.setup({
          containerId: 'betgames_iframe',
          clientUrl: clientUrl,
          apiUrl: '',
          partnerCode: '',
          partnerToken: '',
          language: 'en',
          isMobile: '0',
          defaultGame: '1',
        });
      };
      script.type = 'text/javascript';
      script.src = clientUrl + '/public/betgames.js' + '?' + Date.now();

      document.head.appendChild(script);
    </script>
  </body>
</html>