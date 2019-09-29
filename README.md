# Silverstripe Alert Banner

Alert banner module for Silverstripe v3 and v4.
Adds Alerts Modal Admin to your site where you can add multiple alerts, either global (which you can add exceptions) or page specific.

## Enabling Alert Banner

In your extensions yml file include

```yml
#SS3
Page_Controller:
  extensions:
    - AlertBannerPageControllerExtension

```

Include the alert banner template where you'd like it to be displayed.

E.g. `Page.ss`

```ss
<body>
  <% include AlertBanner %>
  ...
</body>
```

The above should display the alert banner as the very top of your site. (As long as there are no fixed/absolute elements to the top)

## Styling Alert Banner

In your Alert Banner element, click on the style tab and choose the background color, font color and Icon that the banner should use.

## CMS

## Testing

## References
