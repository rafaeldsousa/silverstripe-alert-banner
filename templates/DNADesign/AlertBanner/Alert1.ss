<div class="banner active <% if $First %><% else %>banner--bordered<% end_if %>">
	<div class="banner-wrap <% if $Emergency == 1 %>banner--emergency <% else %>banner--small banner--alert<% end_if %>"
		 data-toggle-self>
		<div class="banner-grid">
			<div class="banner-message">
				<h2 class="banner-heading">
						$Title
				</h2>
				<p class="banner-details">
					$Description
				</p>
			</div>

			<% include DNADesign\AlertBanner\AlertLink AlertID=$Top.ID %>
		</div>
	</div>
</div>
