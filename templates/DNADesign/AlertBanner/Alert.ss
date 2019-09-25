<div class="alertBanner active alertBanner--$Scheme">
	<div class="alertBanner-container">
			<div class="alertBanner-icon">
			</div>
			<div class="alertBanner-content">
				<% if not HideTitle %>
					<h3 class="alertBanner-title">$Title</h3>
				<% end_if %>
				<div class="alertBanner-description">
					$Description
				</div>
			</div>
			<div class="alertBanner-linkwrap">
				<% if $ButtonLink %>
					<% with $ButtonLink %>
						<a href="$URL" class="alertBanner-link pure-button">$Title</a>
					<% end_with %>
				<% end_if %>
			</div>
			<div class="alertBanner-dismiss">
				<button class="alertBanner-dismiss--button" data-banner-id=$ID data-dismiss-banner>Dismiss</button>
			</div>
		</div>
	</div>
</div>
