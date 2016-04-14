.. include:: ../../../Includes.txt

.. _saveSession:

Save values to a session
------------------------

As you may know from powermail 1.x you can save values after a submit to a session.
This could be helpful if a user should not fill out a form field twice.
So a form could be prefilled with values that a user submitted before.

There is a difference to the old function:
When it was activated by default in powermail 1.x, you have to enable
this feature by hand for every field in powermail 2.x.
In addition the new function works for all fields with the same markername,
no matter what form.

You can also use TypoScript stdWrap functionallity to manipulate those values.

See following example:

.. code-block:: text

	plugin.tx_powermail.settings.setup {

		# Save submitted values in a session to prefill forms for further visits. Define each markername for all forms.
		saveSession {
			# Method "temporary" means as long as the browser is open. "permanently" could be used together with a frontend-user session. If method is empty, saveSession is deactivated.
			_method = temporary

			firstname = TEXT
			firstname.field = firstname

			lastname = TEXT
			lastname.field = lastname
		}
	}
