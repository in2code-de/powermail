# Templates

## Using your own templates

Powermail brings a lot of templates, layouts and partials to your
system. You can add additional paths via **TypoScript Setup**.
If you want to overwrite single files (e.g. Resources/Private/Templates/Form/Form.html)
you can copy this single file to your sitepackage or to a fileadmin folder or else where (see values with "1" below).
"0" is defined as fallback folder by default for the non-existing files in your defined folder:

```
plugin.tx_powermail {
    view {
        templateRootPaths {
            0 = EXT:powermail/Resources/Private/Templates/
            1 = fileadmin/templates/powermail/Templates/
        }
        partialRootPaths {
            0 = EXT:powermail/Resources/Private/Partials/
            1 = fileadmin/templates/powermail/Partials/
        }
        layoutRootPaths {
            0 = EXT:powermail/Resources/Private/Layouts/
            1 = fileadmin/templates/powermail/Layouts/
        }
    }
}
```

Because constants are used for .1 in setup by default, you can also use **TypoScript Constants** like:

```
plugin.tx_powermail.view {
    templateRootPath = fileadmin/templates/powermail/Templates/
    partialRootPath = fileadmin/templates/powermail/Partials/
    layoutRootPath = fileadmin/templates/powermail/Layouts/
}
```

Do not change the original templates of an extension, otherwise it's hard to update the extension!

## Using Variables (former known as Markers)

In Fluid you can use all available fields (that you see in the backend)

```
Dear Admin,

there is a new mail from {firstname} {lastname}

all values:
{powermail_all}

The subject of this mail is {mail.subject}
{label_firstname}: {firstname}
```

See the hints in the template files or do a debug output with the
debug ViewHelper

```
<f:debug>{firstname}</f:debug>
<f:debug>{mail}</f:debug>
<f:debug>{_all}</f:debug>
```

You can also use the variables in the RTE fields in backend:

```
Dear {firstname} {lastname},
thank you for your mail.

Your text was:
{text -> f:format.nl2br()}

All transmitted values are:
{powermail_all}
```

## Using TypoScript in Templates or RTE fields

Do you need some dynamic values from TypoScript in your Template or
RTE? Use a cObject ViewHelper:

`{f:cObject(typoscriptObjectPath:'lib.test')}`

## Using ViewHelpers in Templates of RTE fields

Instead of TypoScript it is also possible to use an own ViewHelper in the templates.
To avoid escaping the tag of the ViewHelper, the inline notation should be used.

`{namespace yournamespace=Vendor\Extension\ViewHelpers} {yournamespace:doMagic()}`

`{namespace yournamespace=Vendor\Extension\ViewHelpers} {yournamespace:doMagicAndPassValue(data:'{firstname}')}`


Look at the official documentation how to add own ViewHelpers to your sitepackage.
