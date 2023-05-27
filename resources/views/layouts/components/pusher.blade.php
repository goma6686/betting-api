<script>

  var pusher = new Pusher('225ca3a5888bbcbc0ed1', {
    cluster: 'eu'
  });

  var channel = pusher.subscribe('Balance');
  channel.bind('App\\Events\\UpdateBalance', function(data) {
  const p_element = document.getElementById("buttons").getElementsByClassName("balance")[0];
  var obj = data;

  obj.toJSON = function(){
      return {
          balance: data.balance
      }
  }
  p_element.innerText = (obj.balance)/100 + " €";
  });

</script>