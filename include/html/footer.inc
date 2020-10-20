<style>
.footer{
    color: white;
  }
</style>
<?php if (isset($url)) {?>
<input type="hidden" name="" id="url" value="<?php echo $url ?>">
<?php } ?>
<footer class="footer">(c) www.nci.co.jp</footer>
</div>
</body>
</html>
<script>
  $(function(){
    var url = "#" + $("#url").val();
    var menu = $(url).addClass("active");
  });
</script>
