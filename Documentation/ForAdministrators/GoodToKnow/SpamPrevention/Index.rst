.. include:: Images.txt
.. include:: ../../../Includes.txt

.. _spamprevention:

Spam Prevention
---------------

.. _spamprevention-intro:

Introduction
^^^^^^^^^^^^

|img-87|

We ported some spamcheck from wt\_spamshield in the core of powermail:

- Honeypod
- Linkcheck
- Namecheck
- Sessioncheck
- UniqueValues
- String Blacklist
- IP-Address Blacklist

Every submitted form will be checked with this methods. Every failed
method adds a Spam-Indication-Number to a storage. The sum of the
Spam-Indication-Numbers leads to a Spam-Factor (from 0 to 100%). Per
default every mail with a Spam-Factor of 75% is declined with a
message.


How is a Spam-Number related to the Spam-Factor?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

|img-88|

In this example leads a Spam-Indication from 4 to a 75% chance of spam
in the mail(3: 66%, 12: 92%, etc...)


Configure and enable your Spam Settings with TypoScript
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Reference
"""""""""


.. container::ts-properties

=========================================================== ========================================== ======================= ===============================
Property                                                    Data Type                                  :ref:`t3tsref:stdwrap`  Default
=========================================================== ========================================== ======================= ===============================
:ref:`goodtoknow-enable`                                    0 = disable | 1 = enable                   no                      1
factor_                                                     :ref:`t3tsref:data-type-integer`           no                      75
email_                                                      :ref:`t3tsref:data-type-string`            no                      *empty*
:ref:`goodtoknow-indicatorhoneypod`                         :ref:`t3tsref:data-type-integer`           no                      5
:ref:`goodtoknow-indicatorlink`                             :ref:`t3tsref:data-type-integer`           no                      3
:ref:`goodtoknow-indicatorlinklimit`                        :ref:`t3tsref:data-type-integer`           no                      2
:ref:`goodtoknow-indicatorname`                             :ref:`t3tsref:data-type-integer`           no                      3
:ref:`goodtoknow-indicatorsession`                          :ref:`t3tsref:data-type-integer`           no                      5
:ref:`goodtoknow-indicatorunique`                           :ref:`t3tsref:data-type-integer`           no                      2
:ref:`goodtoknow-indicatorblackliststring`                  :ref:`t3tsref:data-type-integer`           no                      7
:ref:`goodtoknow-indicatorblackliststringvalues`            :ref:`t3tsref:data-type-string`            no                      viagra,sex,porn,p0rn
:ref:`goodtoknow-indicatorblacklistip`                      :ref:`t3tsref:data-type-integer`           no                      7
:ref:`goodtoknow-indicatorblacklistipvalues`                :ref:`t3tsref:data-type-string`            no                      123.132.125.123,123.132.125.124

=========================================================== ========================================== ======================= ===============================

.. _goodtoknow-enable:

\_enable
""""""""

:typoscript:`plugin.tx_powermail.settings.setup.spamshield._enable =` 0 (disable) | 1 (enable)

Enable or disable the spamshield of powermail completely



.. _goodtoknow-factor:

factor
""""""

:typoscript:`plugin.tx_powermail.settings.setup.spamshield.factor =` :ref:`t3tsref:data-type-integer`

Spam Factor Limit in %


.. _goodtoknow-email:

email
"""""

:typoscript:`plugin.tx_powermail.settings.setup.spamshield.email =` :ref:`t3tsref:data-type-string`

Notification Email to Admin if spam recognized


.. _goodtoknow-indicatorhoneypod:

indicator.honeypod
""""""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.indicator.honeypod =` :ref:`t3tsref:data-type-string`

A Honeypod is an invisible (CSS) field which should not filled with
any value. If it's even filled, it could be a machine.

If this check failed - add this indication value to indicator (0
disables this check completely)



.. _goodtoknow-indicatorlink:

indicator.link
""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.indicator.link =` :ref:`t3tsref:data-type-string`

Checks the number of Links in the mail. The number of links is a good
indication of a spammail.

If this check failed - add this indication value to indicator (0
disables this check completely)


.. _goodtoknow-indicatorlinklimit:

indicator.linkLimit
"""""""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.indicator.linkLimit =` :ref:`t3tsref:data-type-string`

Limit of links allowed. If there are more links than allowed, the check fails.


.. _goodtoknow-indicatorname:

indicator.name
""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.indicator.name =` :ref:`t3tsref:data-type-integer`

Compares fields with marker “firstname” and “lastname” (or “vorname”
and “nachname”). The value may not be the same.

if this check failes - add this indication value to indicator (0
disables this check completely)

.. _goodtoknow-indicatorsession:

indicator.session
"""""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.indicator.session =` :ref:`t3tsref:data-type-integer`

If a user opens the form a timestamp is set in a browser-session. If
the session is empty on submit, it could be a machine.

if this check failes - add this indication value to indicator (0
disables this check completely)


.. _goodtoknow-indicatorunique:

indicator.unique
""""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.indicator.unique =` :ref:`t3tsref:data-type-integer`

Compares the values of all fields. If different fields have the same
value, this could be spam.

If this check failes - add this indication value to indicator (0
disables this check completely)


.. _goodtoknow-indicatorblackliststring:

indicator.blacklistString
"""""""""""""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.indicator.blacklistString =` :ref:`t3tsref:data-type-integer`

Checks mails to not allowed string values.

If this check failes - add this indication value to indicator (0
disables this check completely)


.. _goodtoknow-indicatorblackliststringvalues:

indicator.blacklistStringValues
"""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.indicator.blacklistStringValues =` :ref:`t3tsref:data-type-string`

Define the string that are not allowed.

Blacklisted values (default values should be extended with your experience)



.. _goodtoknow-indicatorblacklistip:

indicator.blacklistIp
"""""""""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.indicator.blacklistIp =` :ref:`t3tsref:data-type-integer`

Checks if the sender is not in the IP-Blacklist.

If this check failes - add this indication value to indicator (0
disables this check completely)



.. _goodtoknow-indicatorblacklistipvalues:

indicator.blacklistIpValues
"""""""""""""""""""""""""""

:typoscript:`plugin.tx_powermail.settings.setup.indicator.blacklistIpValues =` :ref:`t3tsref:data-type-string`

Define the IP-Addreses that are not allowed.

Blacklisted values (default values should be extended with your
experience)

Comprehensive Example
^^^^^^^^^^^^^^^^^^^^^

.. code-block:: text

	plugin.tx_powermail {
		settings.setup {
			spamshield {
				_enable = 1
				factor = 75
				email = administrator@domain.org

				indicator {
					honeypod = 5
					link = 3
					linkLimit = 2
					name = 3
					session = 5
					unique = 2
					blacklistString = 7
					blacklistStringValues = viagra,sex,porn,p0rn
					blacklistIp = 7
					blacklistIpValues = 123.132.125.123
				}
			}
		}
	}



Debug and finetune the Spamsettings
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Its usefull to activate a adminmail (for an initial time period e.g.)
if a mail failed (see TypoScript Settings before). In the mail, you
see which checks failed and the overall Spam Factor.

::

   Possible spam in powermail form on page with PID 3

   Spamfactor of this mail: 92%


   Failed Spamchecks:
   0: nameCheck failed
   1: uniqueCheck failed
   2: blacklistStringCheck failed


   Given Form variables:
   2: Alex
   9: Alex
   10: alexander.kellner@in2code.de
   3: Viagra and Free P0rn
   See link on http://freeporn.de or http://freeporn.com

   Senders IP address: 155.233.10.8

You can also enable the Spamshield Debug to see the Methods
which are failed above the form. Enable with TypoScript setup (Use extension devlog to see this settings):

::

	plugin.tx_powermail.settings.setup.debug.spamshield = 1


|img-89|


Captcha
^^^^^^^

Using a captcha extension also helps to prevent spam. You can simply add a new field of type captcha. A build-in calculating captcha will be shown in frontend.
If you want to use another extension, you can install the extension "captcha" from TER and configure powermail to use this extension for every captcha:

::

	plugin.tx_powermail.settings.setup.captcha.use = captcha
