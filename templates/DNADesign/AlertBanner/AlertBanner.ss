<% if $URLSegment != "Security" %>
	<div class="alertBanner-list">
		<% loop $AlertBanners %>
			<div class="alertBanner alertBanner--$ID active alertBanner--$getThemeTitle">
				<div class="alertBanner-container alertBanner-$ContentAlignment.LowerCase">
					<div class="alertBanner-icon">
						&nbsp;
					</div>
					<div class="alertBanner-content">
						<div class="alertBanner-description">
							$Description
						</div>
						<div class="alertBanner-linkwrap">
							<% if $ButtonLink %>
								<% with $ButtonLink %>
									<a href="$LinkURL" class="alertBanner-link">
										$Title
									</a>
								<% end_with %>
							<% end_if %>
						</div>
					</div>
					<div class="alertBanner-dismiss">
						<% if not $DisableDismiss %>
							<button class="alertBanner-dismiss--button" data-banner-id=$ID data-dismiss-banner>Dismiss</button>
						<% end_if %>
					</div>
				</div>
			</div>
		<% end_loop %>
	</div>
<% end_if %>
