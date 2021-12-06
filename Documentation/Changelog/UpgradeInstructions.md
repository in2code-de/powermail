# Upgrade Instructions

## Version 9.0


| Version | Description                                                                                                                                                     |
|---------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Resources/Private/Partials/Form/Field/Html.html   | Uses now <f:sanitize> instead of <f:format.raw>. This means, that forms which uses the html element, will now clean the HTML for incorrect / possibly bad code. |
