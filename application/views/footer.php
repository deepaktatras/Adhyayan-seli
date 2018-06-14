
</div>
    </section>
	
	<footer class="nuiFooter">
          <div class="container clearfix">
            <div class="fl">&copy; <?php echo date('Y');?> Adhyayan. All right reserved.</div>
            <div class="fr">&copy; Powered by <a target="_blank" href="http://tatrasdata.com/"><strong>TATRAS</strong> Data Services (P) Ltd.</a></div>
          </div>
    </footer>
	
	
	<!-- Do not play with the below html code -->
	<div id="ajaxLoading"></div>
	<div id="popup_wrapper"></div>
	<div id="login_popup_wrap">
		<div id="login_popup">
			<form method="post" action="<?php echo createUrl(array("controller"=>"login","action"=>"login")); ?>">
				<h3>Login</h3>
                                <div class="confirmMsg danger active" style="display: none;padding: 5px;margin-bottom: 5px;"></div>
				<input type="email" placeholder="Username" name="email" class="form-control mb10" required>
				<input type="password" name="password" placeholder="Password" class="form-control mb10" required>
				<input type="submit" id="loginsubmit" class="btn btn-primary form-control mb10" value="LOGIN" />
                                <div class="row" id="loginconfirm" style="display: none;">
                                <div class="col-sm-6"><input type="submit" class="btn btn-primary form-control mb10" value="CONFIRM" /></div>
                                <div class="col-sm-6"><input type="button" id="logincancel" class="btn btn-primary form-control mb10" value="CANCEL" /></div>
                                </div>
				<input type="hidden" name="_action" value="login" />
                                <input type="hidden" name="actionconfirm" id="actionconfirm" value="0" />
				<div class="ajaxMsg danger active">Session Expired. Please login again.</div>
			</form>
		</div>
	</div>
<?php echo $addToFooter; ?>
<a href="javascript:void(0);" id="scroll" title="Scroll to Top" style="display: none;">Top<span></span></a>
</body>
</html>