		</div>
    </section>
	
	<footer class="nuiFooter">
          <div class="nuiWrapper clearfix">
            <div class="fl">&copy; 2013 Adhyayan all right reserved</div>
            <div class="fr">&copy; Powered by <a target="_blank" href="http://algorithmicinsight.com/">Algorithmic insight (p) ltd.</a></div>
          </div>
    </footer>
	
	
	<!-- Do not play with the below html code -->
	<div id="ajaxLoading"></div>
	<div id="popup_wrapper"></div>
	<div id="login_popup_wrap">
		<div id="login_popup">
			<form method="post" action="<?php echo createUrl(array("controller"=>"login","action"=>"login")); ?>">
				<h3>Login</h3>
				<input type="email" placeholder="Username" name="email" class="form-control mb10" required>
				<input type="password" name="password" placeholder="Password" class="form-control mb10" required>
				<input type="submit" class="btn btn-primary form-control mb10" value="LOGIN" />
				<input type="hidden" name="_action" value="login" />
				<div class="ajaxMsg danger active">Session Expired. Please login again.</div>
			</form>
		</div>
	</div>
<?php echo $addToFooter; ?>
</body>
</html>