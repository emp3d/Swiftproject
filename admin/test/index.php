<div id="download-linux" hidden></div>
<div id="version" hidden></div>
<br><button type="button" onclick="getloc();">click me</button>
<script src="http://1fxmod.org/download/version.js"></script>
<script src="https://code.jquery.com/jquery-2.1.4.js"></script>
<script>
function getloc() {

  var href = "http://1fxmod.org/download/" + $('a').attr('href');
  
  location.href=href;
}
</script>
