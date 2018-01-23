.. include:: ../Includes.txt
.. include:: Images.txt

.. _faq:

FAQ
===

.. contents:: Table of Contents
   :depth: 1

.. _caniuseoldforms:

Can I use old forms from powermail 2.x in powermail 3.x?
--------------------------------------------------------

Yes. But old tables must be converted (..forms => ..form, ..fields => ..field, etc...).
You can simply open the converter script in the Extension Manager.
Note: New tables must be empty or non-existing.


.. _caniuseoldmails:

Can I use old mails from powermail 2.x  in powermail 3.x?
---------------------------------------------------------

Yes. But old tables must be converted (..mails => ..mail, ..answers => ..answer, etc...).
You can simply open the converter script in the Extension Manager.
Note: New tables must be empty or non-existing.


.. _howcaniusebootstrapcss:

How can I use bootstrap CSS for powermail forms?
------------------------------------------------

You have to add the related static template and a bootstrap.css

See :ref:`addBootstrapClassesAndCssToPowermail`


.. _howcaniuserows:

How can I use responsive columns in powermail?
----------------------------------------------

Since powermail 4.0 it's possible to use wrapping containers for (e.g.) every 2/3/4 fields to get a markup like
you already may know from bootstrap:

::

    <div class="row">
        <div class="form-group col-md-6">
            <label>Firstname</label>
            <input type="text" ... />
        </div>
        <div class="form-group col-md-6">
            <label>Lastname</label>
            <input type="text" ... />
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label>Email</label>
            <input type="text" ... />
        </div>
        <div class="form-group col-md-6">
            <label>Phone</label>
            <input type="text" ... />
        </div>
    </div>

Have a look into static TypoScript template BootstrapClassesAndLayout for some bootstrap classes examples.
Example TypoScript configuration:

::

    plugin.tx_powermail.settings.setup {
        styles {
            numberOfColumns = 2
            framework {
                rowClasses = row
                fieldAndLabelWrappingClasses = form-group col-md-6
            }
        }
    }

How does the magic work? There is a viewhelper in Page.html partial, that adds containers after x fields:

::

    <vh:misc.createRowTags
        columns="{settings.styles.numberOfColumns}"
        class="{settings.styles.framework.rowClasses}"
        iteration="{iteration}"
        additionalAttributes="{data-foo:'bar'}
        tagName="div">

        <f:render partial="Field ..." />
    </vh:misc.createRowTags>

columns:              Number of columns - 0 disables creation of new containers completely
class:                Class name(s) for the new tag
iteration:            The iteration array from a foreach viewhelper
additionalAttributes: Any additional attributes for the new tags (must be type of array)
tagName:              Tagname for the new containers ("div" if not given)


.. _howtosolvespf:

How to solve SPF defiance?
--------------------------

More and more email providers turn on SPF for their mailboxes
(see https://en.wikipedia.org/wiki/Sender_Policy_Framework for details).
Web forms should not send mails with the visitors email address as sender email address but with a server email address.
Nevertheless powermail uses automatic reply email address from the sender.

To set a sender email address for the main email (to receiver), you could use this TypoScript:

::

	plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail = TEXT
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderEmail.value = server@domain.org
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderName = TEXT
	plugin.tx_powermail.settings.setup.receiver.overwrite.senderName.value = Server from domain.org

To set a sender email address for the confirmation email (to sender), you could use this TypoScript:

::

	plugin.tx_powermail.settings.setup.sender.overwrite.senderEmail = TEXT
	plugin.tx_powermail.settings.setup.sender.overwrite.senderEmail.value = server@domain.org
	plugin.tx_powermail.settings.setup.sender.overwrite.senderName = TEXT
	plugin.tx_powermail.settings.setup.sender.overwrite.senderName.value = Server from domain.org

Please ask your server administrator for a valid email address.


.. _mailcouldnotbesent:

Failure, mail could not be sent! What does this mean?
-----------------------------------------------------

If a mail could not be sent this message is coming up. In addition GeneralUtility::sysLog() is called with the
exception message. To see this message, please use $GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLog'] or look into
the install tool how to store logs on your system.


.. _canisueanothercaptcha:

Can I use another Captcha Extension than the integrated calculating captcha?
----------------------------------------------------------------------------

Yes. At the moment we support a build-in calculating captcha in the powermail core and the extension **captcha**.


.. _canisavetoothertables:

Can I save values to tt_address, fe_users, tt_news, etc...?
-----------------------------------------------------------

Yes. It's very easy to save values to a third-party-table – see manual part

For Administrators / Best Practice / Saving Values to Third Party Table :ref:`savingvaluestothirdpartytables`


.. _caniwritemyownvalidator:

Can I write my own javascript/php validator?
--------------------------------------------

Yes. Write your own validator – see manual part
For Developers / Write own JavaScript Validator and For Developers / Write own PHP Validation :ref:`newvalidators`



.. _caniattachfilestoanymail:

Can I attach files to any mail?
-------------------------------

Yes. You can simply add some files to any mail via TypoScript cObject – see TypoScript Main Template for details.

Short example:

.. code-block:: text

	plugin.tx_powermail.settings.setup.sender {
		addAttachment = TEXT
		addAttachment.value = fileadmin/file.pdf
	}


.. _howadminconfirmdoubleoptinmail:

How can the admin confirm a mail from Double-Opt-In?
----------------------------------------------------

Yes. Per default the confirmation Email (if Double-Opt-In is enabled) will be
sent to the sender.

You can overwrite it via TypoScript. See TypoScript Main Template for details.

Short example:

.. code-block:: text

	plugin.tx_powermail.settings.setup.optin {
		overwrite {
			email = TEXT
			email.value = admin@domain.org
		}
	}



.. _howtopreventspam:

How to prevent Spam or to change the Spam-Prevention-Settings?
--------------------------------------------------------------

Yes. Powermail in version 2 and higher comes with a lot of spam-prevention-methods along.
You can use the integrated spamshield (configuration via constants and typoscript), write your
own spam prevention methods or simply use a captcha. See :ref:`spamprevention`



.. _howtosetadvancedemailsettings:

How can I set some advanced mail settings (like priority or returnPath, etc...)?
--------------------------------------------------------------------------------

You can change following settings for the mail to the receiver and to
the sender completely via TypoScript. See :ref:`mainTypoScript` for
details.

- email
- name
- senderName
- senderEmail
- subject
- cc Receivers
- bcc Receivers
- returnPath
- reply to email
- reply to name
- priority


How to change the style selector with my own values (In Forms, Pages or Fields)?
--------------------------------------------------------------------------------

.. code-block:: html

	<select>
		<option value=”layout1”>Layout1</option>
		<option value=”layout2”>Layout2</option>
		<option value=”layout3”>Layout3</option>
	</select>



Changing values via page tsconfig
'''''''''''''''''''''''''''''''''

.. code-block:: text

	TCEFORM {
		tx_powermail_domain_model_form {
			css {
				removeItems = layout1, layout2, layout3
				addItems {
					formgrey = Form grey
					form2cols = Form with 2 columns
					default = Default Form
				}
			}
		}
		tx_powermail_domain_model_page < .tx_powermail_domain_model_form
		tx_powermail_domain_model_field < .tx_powermail_domain_model_form
	}




This configuration produces this output:
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: html

	<select>
		<option value=”formgrey”>Form grey</option>
		<option value=”form2cols”>Form with 2 columns</option>
		<option value=”default”>Default Form</option>
	</select>

And adds the class “formgrey”, "form2cols" or “default” to all forms, pages and fields in
the Frontend.

|faq_style|


.. _howtohidefieldsforeditors:

How to hide fields for editors?
-------------------------------

Hiding field from tables form, page or field
''''''''''''''''''''''''''''''''''''''''''''

For editors (not administrators) fields can be disabled in the right management of TYPO3 (see TYPO3 documentation how to show or hide fields for editors).

Another way is to hide fields for editors (and administrators) via Page TSConfig:

.. code-block:: text

	TCEFORM {
		tx_powermail_domain_model_form {
			css.disabled = 1
		}
		tx_powermail_domain_model_page {
			css.disabled = 1
		}
		tx_powermail_domain_model_field {
			css.disabled = 1
			feuser_value.disabled = 1
			placeholder.disabled = 1
		}
	}
	
You may also restrict this and other settings to non-admin users with a TypoScript condition (see https://docs.typo3.org/typo3cms/TSconfigReference/Conditions/Index.html).

Hiding fields from FlexForm
'''''''''''''''''''''''''''

If you add a powermail plugin, you will see some options in FlexForm. If you want to hide some of these
fields (for editors and administrators), you can also do it via Page TSConfig:

.. code-block:: text

	TCEFORM {
		tt_content {
			pi_flexform {
				powermail_pi1 {
					main {
						settings\.flexform\.main\.moresteps.disabled = 1
						settings\.flexform\.main\.optin.disabled = 1
						settings\.flexform\.main\.confirmation.disabled = 1
						settings\.flexform\.main\.pid.disabled = 1
					}

					receiver {
						settings\.flexform\.receiver\.fe_group.disabled = 1
					}

					thx {
						settings\.flexform\.thx\.redirect.disabled = 1
					}
				}
			}
		}
	}


.. _howtoremovefieldtypes:

How to remove field types?
--------------------------

If you want to completely remove fieldtypes (e.g. if you do not need a captcha field or other types),
you can do this with a simple line of Page TSConfig:

.. code-block:: text

	TCEFORM.tx_powermail_domain_model_field.type.removeItems = captcha,location,typoscript



.. _howtoprefillfields:

How to prefill a field in the powermail form?
---------------------------------------------

Prefilling or preselecting of fields is very easy - see :ref:`prefillOrPreselectAField`
With this possibility you can fill a hidden or a visible field with a value or you can
preselect a select, radio or checkbox.


.. _howdonotincludejquery:

How can I include jQuery?
-------------------------

You have to enable this feature via Constants:

.. code-block:: text

	plugin.tx_powermail.settings {
		javascript {
			addJQueryFromGoogle = 1
			powermailJQuery = //ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js
		}
	}


.. _howcaniuset3jquery:

Can I use t3jquery?
-------------------

No.

.. _javascriptvalidationdoesnotwork:

JavaScript validation does not work – what's wrong?
---------------------------------------------------

Powermail loads jQuery (if you activated it with TypoScript) from googleapis.com.
You can change that behaviour with constants or typoscript.

It's importand to have the correct ordering of the JavaScript files.
First you need the libraries (jQuery, Datepicker, Parsley) and after that your JavaScript.

Check the correct including of your JavaScript in the HTML source –
example Footer could be:

.. code-block:: html

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
	<script src="typo3conf/ext/powermail/Resources/Public/JavaScripts/Libraries/jquery.datetimepicker.min.js?1400758352" type="text/javascript"></script>
	<script src="typo3conf/ext/powermail/Resources/Public/JavaScripts/Libraries/parsley.min.js?1400758352" type="text/javascript"></script>
	<script src="typo3conf/ext/powermail/Resources/Public/JavaScripts/Powermail/Tabs.min.js?1400758352" type="text/javascript"></script>
	<script src="typo3conf/ext/powermail/Resources/Public/JavaScripts/Powermail/Form.min.js?1400758352" type="text/javascript"></script>
	<script src="typo3conf/ext/powermail/Resources/Public/JavaScripts/Powermail/Marketing.min.js?1400758352" type="text/javascript"></script>

.. _datetimepickernotworking:

The datetimepicker is not working – what's wrong?
-------------------------------------------------

First of all, check if there are all JavaScript loaded correctly (see :ref:`javascriptvalidationdoesnotwork`).
In addition the datetimepicker needs some CSS to get shown in frontend. You can try to add the demo.css in Static TypoScript Template section.

If you want to get more information to the used datetimepicker (JavaScript, CSS, examples, etc...) see:

* http://xdsoft.net/jqplugins/datetimepicker
* https://github.com/xdan/datetimepicker

*Note:* There is a check, if the browser supports fields like input[type="date"] (e.g. Chrome).
Per default the datetimepicker is disabled in this case.
If you want to enforce datetimepicker for all browsers, you can enable this via TypoScript Constants.

.. _marketinginformationnotworking:

Marketing Information are not working – what's wrong?
-----------------------------------------------------

Did you include the marketing static template on the root page of your
domain?

.. _addnewfieldtype:

I want to add a new Field Type to powermail – how can I do this
---------------------------------------------------------------

Yes, you can add a new Fieldtype (in record tx_powermail_domain_model_field) with some Page TSConfig.

See following example to add a new fieldtype with Partial Newfield.html
(also see Documentation/ForDevelopers/NewField for an advanced example)

.. code-block:: text

    tx_powermail.flexForm.type.addFieldOptions.newfield = New Field Name
    tx_powermail.flexForm.type.addFieldOptions.new.dataType = 0
    tx_powermail.flexForm.type.addFieldOptions.new.export = 1

.. _additionalAttributesInFieldPartials:

I want to use additionalAttributes in a field partial, but it's already in use
------------------------------------------------------------------------------

All Field partials are original stored under EXT:powermail/Resources/Private/Partials/Form/Field/*
Most of them are already using the parameter additionalAttributes to set data-attributes for clientside validation, etc...
In some case you need to set your own additionalAttributes - see following code examples.

.. code-block:: text

	<!-- Original textfield example -->
	<f:form.textfield
		...
		additionalAttributes="{vh:Validation.ValidationDataAttribute(field:field)}"
		... />

	<!-- Modified textfield example -->
	<f:form.textfield
		...
		additionalAttributes="{vh:Validation.ValidationDataAttribute(field:field, additionalAttributes:'{autocomplete:\'off\',data-something:\'true\'}')}"
		... />

.. _howtouseajax:

How can I use ajax in powermail?
--------------------------------

Simply turn ajax via TypoScript constants on:

.. code-block:: text

    plugin.tx_powermail.settings.misc.ajaxSubmit = 1

.. _howtoaddacallbackfunctiononajaxsubmit:

How can I add a callback function on ajax submit?
-------------------------------------------------

See this example JavaScript how to add an own callback function in JavaScript when an ajaxsubmit is done
(e.g. to close a lightbox or something else).

.. code-block:: text

    // Note: this event fires on the .tx-powermail element, since its inner html is replaced
    $('.tx-powermail').on('submitted.powermail.form', function(){
        console.log('ajax form was submitted');
    })

.. _whitepageafterupgrade:

I upgraded powermail and a white page comes up
----------------------------------------------

See explanation in part "For Administrators" and "Upgrade". If you make an upgrade, only deleting the cache
files in typo3temp may not help.
Please clean all caches in the install tool and try again.

.. _powermailwithnews:

I want to use powermail on a news-detail-page, but the error **Reason: No news entry found.** comes up
------------------------------------------------------------------------------------------------------

If you want to send a newstitle or something with powermail on a newsdetailpage, a form submit leads
to a pagereload. But per default, powermail does not send the params &tx_news_pi1[news] again which
leads to an error from the extension news.

This is easy to handle, just add this line of TypoScript to your **Constants**:

.. code-block:: text

	plugin.tx_powermail.settings.misc.addQueryString = 1

.. _powermailconditionplugin:

I want to include CSS or JavaScript only if there is a powermail plugin on this page
------------------------------------------------------------------------------------

The recommended way is to include your JavaScript via require.js or with a simple jQuery.getScript().
An alternative to include JS or CSS could be via a simple condition in TypoScrip **Setup**:

.. code-block:: text

	[In2code\Powermail\Condition\IsPluginOnCurrentPageCondition]
	// do something but only if Plugin1 of powermail is on the same page
	[end]

The condition can be used for every plugin and every extension. Example for a defined tt_contentlist_type:

.. code-block:: text

	[In2code\Powermail\Condition\IsPluginOnCurrentPageCondition = powermail_pi1, = powermail_pi2]
	// do something but only if Pi1 or Pi2 of powermail is on the same page
	[end]

.. _powermailconditionsubmitted:

If you want to do something in TypoScript if powermail was just submitted
-------------------------------------------------------------------------

If you want to listen if a powermail form was just submitted via conditions, you can use a globalVar (e.g.) condition
or simply use the build in condition from powermail.
Note: This will probably not work on AJAX submit and if you are using a redirect on submit

.. code-block:: text

    [In2code\Powermail\Condition\IsPowermailSubmittedCondition]
    // do something, but only if powermail was submitted
    [end]

.. _ihaveaproblem:

I have a problem, what can I do?
--------------------------------

- Did you read the manual?
- Turning on the Debug Output in Powermail (via TypoScript) can help you to find a solution (please use extension devlog to look into the debug arrays)
- Try to get free help from the slack channel https://typo3.slack.com/messages/ext-powermail/
- Try to get free help from a TYPO3 Forum like (forum.typo3.org, typo3.net or typo3forum.net)
- Do you need paid support? Please write to http://www.in2code.de
- Did you find a bug? Report it to https://github.com/einpraegsam/powermail/issues
- Did you miss a feature? Feel free to write it down on https://github.com/einpraegsam/powermail/issues
