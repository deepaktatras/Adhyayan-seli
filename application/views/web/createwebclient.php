				<h1 class="page-title">School Registration Form</h1>
				<div class="clr"></div>
				<div class="">
					<div class="ylwRibbonHldr">
						<div class="tabitemsHldr"></div>
					</div>
					<div class="subTabWorkspace pad26 webFormPane">
						<div class="row">
							<div class="col-md-7">
								<div class="form-stmnt">
									<form method="post" id="create_web_school_form" action="">
										<div class="boxBody">
											<dl class="fldList">
												<dt>School Name<span class="astric">*</span>:</dt>
												<dd class="the-basics"><input type="text" class="form-control typeahead tt-query" value="" name="client_name" required /></dd>
											</dl>
											
											<dl class="fldList">
												<dt>School Address<span class="astric">*</span>:</dt>
												<dd>
													<input type="text" class="form-control mb5" placeholder="Street" value="" name="street" required />
													<input type="text" class="form-control mb5" placeholder="City" value="" name="city" required />
													<!--<input type="text" class="form-control" placeholder="State" value="" name="state" required />-->
													<select class="form-control" name="state" required>
														<option value=""> - Select State - </option>
														<?php
														foreach($states as $state)
															echo "<option value=\"".$state['state_id']."\">".$state['state_name']."</option>\n";
														?>
													</select>
												</dd>
											</dl>
		
											<dl class="fldList">
												<dt>Principal Name<span class="astric">*</span>:</dt>
												<dd><input type="text" class="form-control" value="" name="principal_name" required /></dd>
											</dl>
											
											<dl class="fldList">
												<dt>Email ID<span class="astric">*</span>:</dt>
												<dd><input type="email" class="form-control" value="" placeholder="this will be the username" name="email" required /></dd>
											</dl>
											
											<dl class="fldList">
												<dt>Phone Number<span class="astric">*</span>:</dt>
												<dd><input type="text" class="form-control mask_ph" value="" name="phone" required /></dd>
											</dl>
											
											<dl class="fldList">
												<dt>Password<span class="astric">*</span>:</dt>
												<dd><input type="password" class="form-control pwd" value="" name="password" required /></dd>
											</dl>
											<dl class="fldList">
												<dt>Confirm Password<span class="astric">*</span>:</dt>
												<dd><input type="password" class="form-control cpwd" value="" required /></dd>
											</dl>
											
											<dl class="fldList">
												<dt>Remarks:</dt>
												<dd><textarea name="remarks" class="form-control" ></textarea></dd>
											</dl>
		
											
											<dl class="fldList">
												<dd class="nobg"><br><input type="submit" value="Add School" class="btn btn-primary">
												</dd>
											</dl>
										</div>
										<div class="ajaxMsg"></div>
										<input type="hidden" class="isAjaxRequest" name="isAjaxRequest" value="<?php echo $ajaxRequest; ?>" />
										<input type="hidden" name="autologin" action="autologin" />
									</form>
								</div>
							</div>
							<div class="col-md-5">
								<h2>Welcome...</h2>
								<p class="mb20">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
								<div class="embed-responsive embed-responsive-4by3">
								 
								  <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/jQw-hIGiK_0?showinfo=0&rel=0" allowfullscreen></iframe>
								</div>
							</div>
						</div>

					</div>
				</div>
				<script type="text/javascript">
				$(document).ready(function(){
					$('.mask_ph').mask("(+99) 999-9999-999");
				});
				</script>
				<script>
$(document).ready(function(){
		var schools=[];
		<?php foreach($schools as $school){ ?>
			schools.push('<?php echo addslashes($school['client_name']); ?>');
		<?php } ?>
        var substringMatcher = function(strs) {
        return function findMatches(q, cb) {			
        var matches, substringRegex;		
          matches = [];		
			//console.log(s);
          substrRegex = new RegExp("^"+q.trim(), 'i');
          $.each(strs, function(i, str) {
            if (substrRegex.test(str)) {
              matches.push(str);
            }
          });
          cb(matches);
        };
      };	  
     
      $('.the-basics .typeahead').typeahead({
        hint: true,
        highlight: true,
        minLength: 3		
      },
      {
        name: 'schools',
        source: substringMatcher(schools),
		limit:30
      });
	  
	  $('.typeahead').bind('typeahead:select', function(ev, suggestion) {	
		$('.typeahead').typeahead('val','');
		 alert("This school already exists! Please login.");
		 location.href = '<?php echo SITEURL?>';		
		 
	  });
		/*$('.typeahead').bind('typeahead:render', function(ev, suggestion) {			
			console.log('render')		
		});	*/				
}); 		
</script>