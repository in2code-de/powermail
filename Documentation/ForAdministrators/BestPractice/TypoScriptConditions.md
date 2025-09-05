# TypoScript Conditions

Powermail offers 2 conditions, that could make your live easier

## If a powermail form was submitted

```
[isPowermailSubmitted()]
   // Yes, powermail just submitted, add own stuff
[end]
```

## If a powermail plugin is included into current page

```
[isPowermailOnCurrentPage()]
    // Powermail plugin Pi1 (Form) is on the current page, add own stuff (include CSS or JS, etc...)
[end]
```
