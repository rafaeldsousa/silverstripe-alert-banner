<% if $URLSegment != "Security" %>
	<div class="alertBanner-list">
		<% loop $AlertBanners %>
			<div class="alertBanner active" id="$ID" <% if $BgColor %>style="background-color: $BgColor !important"<% end_if %>>
				<div class="alertBanner-container">
					<% if $Icon %>
							<div class="alertBanner-icon">
								$Icon
							</div>
					<% end_if %>
					<div class="alertBanner-content" <% if $FontColor %>style="color: $FontColor !important"<% end_if %>>
						<% if $FontColor %>
							<style>
								#{$ID}.alertBanner-description > * {
									color: $FontColor !important;
								}
							</style>
						<% end_if %>
						<div class="alertBanner-description">
							$Description
						</div>
						<div class="alertBanner-linkwrap">
							<% if $ButtonLink %>
								<% if $FontColor %>
										<style>
											#{$ID}.alertBanner-link {
												color: $FontColor !important;
												border-color: $FontColor !important;
											}
										</style>
										<% if $BgColor %>
											<style>
												#{$ID}.alertBanner-link:hover {
													color: $BgColor !important;
													background-color: $FontColor !important;
												}
												#{$ID}.alertBanner-link:focus {
													color: $BgColor !important;
													background-color: $FontColor !important;
												}
											</style>
										<% end_if %>
									<% end_if %>
								<% with $ButtonLink %>
									<a href="$LinkURL" class="alertBanner-link">$Title</a>
								<% end_with %>
							<% end_if %>
						</div>
					</div>
					<div class="alertBanner-dismiss">
					<% if $FontColor %>
						<style>
							#{$ID}.alertBanner-dismiss--button:before, .alertBanner-dismiss--button:after {
								background-color: $FontColor !important;
							}
						</style>
					<% end_if %>
						<button class="alertBanner-dismiss--button" data-banner-id=$ID data-dismiss-banner>Dismiss</button>
					</div>
				</div>
			</div>
		<% end_loop %>
	</div>
<% end_if %>