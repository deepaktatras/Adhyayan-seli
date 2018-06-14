				<div class="ad_page_title">
					<span>
						Create School Review
					</span>
				</div>
				<div class="clr"></div>
				<div class="form-stmnt">
					<table class="form-table">
							<tr>
								<td>Network<span class="astric">*</span>:</td>
								<td>
									<select class="client_id" name="client_id" required>
										<option value=""> - Select School - </option>
										<?php
										foreach($clients as $client)
											echo "<option value=\"".$client['client_id']."\">".$client['client_name'].($client['street']!=""?", ".$client['street']:'').($client['city']!=""?", ".$client['city']:'').($client['state']!=""?", ".$client['state']:'')."</option>\n";
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td>Internal Assessor<span class="astric">*</span>:</td>
								<td>
									<select class="internal_assessor_id" name="internal_assessor_id" required>
										<option value=""> - Select Internal Assessor - </option>
									</select>
								</td>
							</tr>
							<tr>
								<td>Diagnostic<span class="astric">*</span>:</td>
								<td>
									<select name="diagnostic_id" required>
										<option value=""> - Select Diagnostic - </option>
										<?php
										foreach($diagnostics as $diagnostic)
											echo "<option value=\"".$diagnostic['diagnostic_id']."\">".$diagnostic['name']."</option>\n";
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td>Tier<span class="astric">*</span>:</td>
								<td>
									<select name="tier_id" required>
										<option value=""> - Select Tier - </option>
										<?php
										foreach($tiers as $tier)
											echo "<option value=\"".$tier['standard_id']."\">".$tier['standard_name']."</option>\n";
										?>
									</select>
								</td>
							</tr>

							<tr>
								<td></td>
								<td>
									<br>
									<input type="submit" value="Create Review" class="submit-div">
								</td>
							</tr>
						</table>
				</div>