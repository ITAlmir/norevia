<!DOCTYPE html>
<html>
<head>
    <title>Download Rate Limit Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body style="font-family: Arial; padding:40px;">

    <h2>Download Throttle Test (10/min)</h2>

    <button onclick="singleDownload()">Download Once</button>
    <button onclick="spamDownload()">Spam 15 Requests</button>

    <pre id="output" style="margin-top:20px; background:#f3f3f3; padding:10px;"></pre>
    <p style="margin-top:20px;">
  <a href="/downloads/test-item" target="_blank">Open download in new tab (HTML)</a>
</p>

<button onclick="spamOpenTabs()">Spam open 15 tabs</button>


<script>
function log(msg){
    document.getElementById('output').innerHTML += msg + "\n";
}

function singleDownload(){
    fetch('/downloads/test-item')
        .then(res => log("Status: " + res.status))
        .catch(err => log("Error"));
}

function spamDownload(){
    for(let i=1;i<=15;i++){
        fetch('/downloads/test-item')
            .then(res => log("Request "+i+" → " + res.status))
            .catch(err => log("Error"));
    }
}

function spamOpenTabs(){
  for(let i=1;i<=15;i++){
    window.open('/downloads/test-item', '_blank');
  }
}

</script>

</body>
</html>
