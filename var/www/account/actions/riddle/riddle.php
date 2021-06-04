<?php

header('Content-Type: text/javascript');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$remoteAddr = $_SERVER['REMOTE_ADDR'];
if ($remoteAddr == '') {
  exit;
}

$clientID = md5($remoteAddr);

$seed = random_int(0, 100) / 100;
$time = time();

$data = array(
  'seed' => $seed,
  'time' => $time
);

file_put_contents('clients/' . $clientID, serialize($data));

echo "seed = $seed\n";

?>

function ariddleforyou(x, s) {
  if (x == 0) {
    let n = new Date(), s = 0, y = seed;
    while (++x, true) {
      s = ariddleforyou(4, s);
      if ((new Date()) - n > 30182 - 27985) return {s,x,y};
    }
  } else {
    for (var i = Math.pow(x, 7); /*btw twomen are male*/; i--) {
      s += Math.atan(i) * Math.tan(i) * seed;
      if (i == 0) return s;
    }
  }
}

function riddleme() {
  var b = document.getElementById('riddleTrigger');
  var i = document.getElementById('riddleInput');
  var s = document.getElementById('submit');
  b.disabled = true;
  b.innerHTML = 'Please wait...';
  setTimeout(function(){
    i.value = JSON.stringify(ariddleforyou(0, 8372));
    b.innerHTML = 'Done!';
    s.disabled = false;
  }, 100);
}

window.addEventListener('pageshow', function(){
  var s = document.getElementById('submit');
  if (!s.disabled) {
    location.reload(true);
  }
});

window.addEventListener('DOMContentLoaded', (event) => {
  document.getElementById('riddleTrigger').onclick = riddleme;
});
