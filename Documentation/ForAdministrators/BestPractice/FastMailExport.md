# Speed up your own export templates

## Background

While rendering an export file, powermail used to look for the answer of every exported column by walking
through **all** answers of the current mail. With 30 exported columns and 30 answers per mail that is 900
comparisons per mail, of which only 30 ever match. The larger the form and the more mails are exported, the
more time is spent on comparisons that lead nowhere.

Since version 13.2 the templates shipped with powermail look up the answers of a column directly, through
`{mail.answersGroupedByFieldUid}`. The resulting export file is exactly the same, it is just built a lot
faster.

Measured with 1000 mails, 30 answers each and 30 exported columns:

| Export | Before | Now    |
|--------|--------|--------|
| CSV    | 31.0 s | 10.5 s |
| XLSX   | 34.2 s | 13.5 s |

## Do I have to do anything?

No - unless you use **your own export templates**. If you copied `ExportCsv.html` or `ExportXls.html` to your
own `templateRootPaths` (see [Scheduler Tasks](/ForAdministrators/SchedulerTasks.md)), your copy keeps working
exactly as before, but it also keeps the old, slow lookup.

To profit from the faster lookup in your own template, replace

```html
<f:for each="{mail.answers}" as="answer">
    <f:if condition="{fieldUid} == {answer.field.uid}">
        ...
    </f:if>
</f:for>
```

with

```html
<f:for each="{vh:misc.variableInVariable(obj:mail.answersGroupedByFieldUid, prop:fieldUid)}" as="answer">
    ...
</f:for>
```

`{mail.answersGroupedByFieldUid}` returns all answers of a mail grouped by the uid of their field, and the
index is built only once per mail. A field with more than one answer still renders all of them, in the same
order as before.
