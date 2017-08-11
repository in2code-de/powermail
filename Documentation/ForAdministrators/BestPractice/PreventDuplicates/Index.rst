.. include:: ../../../Includes.txt

.. _preventduplicates:

Prevent Email duplicates
------------------------

.. _preventduplicates-intro:

Introduction
^^^^^^^^^^^^

In some use cases it could happen, that visitors double- (or triple-) click the submitbutton.
Powermail is not able to prevent duplicates automatically and will store those mails/answers twice or even more.
If you dig into the issue "Preventing double-click in html forms" in general it will turn out that it's not that simple
to solve.
Nevertheless I will give you some hints how to get rid of those problems in powermail or mail-forms.


1. Solving usability issues
^^^^^^^^^^^^^^^^^^^^^^^^^^^

We took over a large TYPO3 instance of an insurance company with a lot of powermail forms on there webpage.
Facts like an old TYPO3 and PHP version, a small server and some bad caching configuration pulled the performance down.
In addition there was no feedback for the visitors on a form submit.

Going into details: If the visitor clicked submit, he could not see if a validation rule stops the submit-process or
if the server is simply too slow to answer within 5 seconds.
It turned out, that the visitors wanted to pass huge forms with a couple of validation rules and tried several times to
submit, but clientside validation prevented sending the form. Because the visitors where more and more bothered, they
tend to click submit multiple times.

How to improve mail form usabilty on web projects:

* Show your visitors what validation rules are needed to pass a form before they click on submit
* Give a quick feedback on submit (probably a loading image in submit button, etc...)
* If you have large forms, split them into smaller parts
* Think about enabling the submitbutton if all validation rules are passed (e.g. change color from grey to blue)
* Think about removing useless fields in your forms. Large Forms reduced your conversion rate.


2. Prevent duplicates with serverside technology
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Beside the usability stuff, it must be possible to use some magic to prevent duplicates.
You could use validation.unique settings in powermail TypoScript to prevent duplicated emails.
If a form would have a hidden field, that is filled automatically on form-load with a timestamp (or even better with
a random value), a validation rule should check on submit if this value is unique otherwise show a message
(see TypoScript example below how to enable it).

Con: On slow servers/instances you will see that if you double-click a submit very fast, that the second request will
be handled even before the first request was stored into the database, so a serverside validation may be the wrong way.

::

    plugin.tx_powermail.settings.setup {
        # Prefill all fields {timestamppreventduplicates} with current timestamp
        prefill.timestamppreventduplicates = TEXT
        prefill.timestamppreventduplicates.data = date:U

        # turn unique validation off per default
        validation.unique.timestamppreventduplicates = 0

        # remove timestamp from powermail all variable
        excludeFromPowermailAllMarker {
            confirmationPage.excludeFromMarkerNames = timestamppreventduplicates
            submitPage.excludeFromMarkerNames = timestamppreventduplicates
            receiverMail.excludeFromMarkerNames = timestamppreventduplicates
            senderMail.excludeFromMarkerNames = timestamppreventduplicates
            optinMail.excludeFromMarkerNames = timestamppreventduplicates
        }
    }

    # turn validation on if GET/POST param with field timestamppreventduplicates exists
    [globalVar = GP:tx_powermail_pi1|field|timestamppreventduplicates > 0]
    plugin.tx_powermail.settings.setup.validation.unique.timestamppreventduplicates = 1
    [end]



3. Prevent duplicates with clientside technology
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you face issues like I described it in 2. you will think about a faster solution to prevent duplicates. That was
the time when JavaScript came into my mind. A played a lot and it was still not that simple to solve the issues on
different browsers with different forms with different validation settings.

After some tests and proves on a live system the best solution was to disable the submit button if it's clicked more
then one time and enabling it after some seconds again (probably some validation rules have to be satisfied before
submitting)

See a possible solution for an inline JavaScript in the Submit-Partial of powermail:

::

    <div id="powermail_fieldwrap_{field.uid}"
        class="powermail_fieldwrap powermail_fieldwrap_submit powermail_fieldwrap_{field.uid} {field.css}">

        <script>var submitAmount = 0;</script>
        <style type="text/css">input[disabled]{background-color: #f2f2f2 !important;}</style>
        <f:form.submit
            value="{field.title}"
            class="powermail_field powermail_submit"
            additionalAttributes="{onmousedown:'submitAmount++;var field=this;if(submitAmount>1)field.disabled=true;setTimeout(function enable(){submitAmount=0;field.disabled=false;}, 6000);'}" />
    </div>
