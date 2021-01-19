# Silverstripe Alert Banner

Alert banner module for Silverstripe v3 and v4.
Adds Alerts Modal Admin to your site where you can add multiple alerts, either global (which you can add exceptions) or page specific.

## Enabling Alert Banner

Include the alert banner template where you'd like it to be displayed.

E.g. `Page.ss`

```ss
<body>
  <% include DNADesign/AlertBanner/AlertBanner %>
  ...
</body>
```

The above should display the alert banner as the very top of your site. (As long as there are no fixed/absolute elements to the top)

## Styling Alert Banner

You can define your own styles in yml.

```yml
DNADesign\AlertBanner\AlertBanner:
  Themes:
    Default:
      Title: 'Default Theme'
      FontColor: '#000000'
      BGColor: '#FFFFFF'
      Icon: 'cross-red'
```

## CMS

## Testing

## References
