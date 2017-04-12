<form class="admin-tabform" action="#">
	<div class="panel">
		<div class="panel-header"></div>
		<div class="panel-body">
			<div class="panel-content">
				<fieldset class="float-centre">
					<dl class="admin-definitions">
						<dt>User Name</dt><dd>{$user.userName}</dd>
						<dt>Client Company</dt><dd>{$user.clientCompanyName}</dd>
						<dt>Cobrander ID</dt><dd>{$user.cobranderID}</dd>
						<dt>Balance</dt><dd>{number_format($user.userCredit)}</dd>
					</dl>
				</fieldset>
				<fieldset class="form-fieldset-submission float-centre">
					<a href="#" class="form-button" onclick="$app.module('credit').manageUserCredit({$userID});">
						<img src="skin/images/icon-refresh.png" class="form-button-image" alt="" />
						<span class="form-button-text">Refresh</span>
					</a>
					<a href="#" class="form-button" onclick="$app.module('credit').purchase({$userID});">
						<img src="skin/images/icon-topup.png" class="form-button-image" alt="" />
						<span class="form-button-text">Top Up</span>
					</a>
					<a href="#" class="form-button" onclick="$app.module('credit').deduct({$userID});">
						<img src="skin/images/icon-deduct.png" class="form-button-image" alt="" />
						<span class="form-button-text">Deduct</span>
					</a>
				</fieldset>
			</div>
		</div>
	</div>
	<div id="apiuser-editform-tabs" class="panel-tabs">
		<ul>
			<li><a href="{$smarty.const.SMSAPIADMIN_SERVICE_URL}credit.history.php?userID={$userID}"><img src="skin/images/icon-history.png" class="icon-image icon-size-small" alt="" /><span>Credit History</span></a></li>
		</ul>
	</div>
</form>