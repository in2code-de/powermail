.. include:: ../../../Includes.txt

.. _savingvaluestothirdpartytables:

Saving Values to Third Party Table
----------------------------------

Introduction
""""""""""""

Powermail is able to save the values from a submitted form into a
third-party-table (like news, tt_news, tt_address, tt_content, fe_users,
pages, or something else...).

Note: If you want to use _ifUnique functionality (see below for a description),
your table must have a field "uid"

Example for table tt_address:

.. code-block:: text

	plugin.tx_powermail.settings.setup {
		# Save values to any table (see following example)
		dbEntry {

			#####################################################
			### EXAMPLE for adding values to table tt_address ###
			#####################################################

			1 {
				# Enable or disable db entry for table tt_address
	#			_enable = TEXT
	#			_enable.value = 1

				# Set tableName to "tt_address"
	#			_table = TEXT
	#			_table.value = tt_address

				# Write only if any field is not yet filled with current value (e.g. test if an email is already in database)
					# default: always add new records (don't care about existing values)
					# update: update record if there is an existing entry (e.g. if email is already there)
					# none: no entry if field is filled (do nothing if record already exists)
	#			_ifUnique.email = update

				# optional: add additional where clause (only in mode "update") for search if a record still exists. You could use a plain string (see example below) or a cObject if needed
	#			_ifUniqueWhereClause = AND pid = 123

				# Fill tt_address.email with a static value => mail@mail.com
	#			email = TEXT
	#			email.value = mail@mail.com

				# Fill tt_address.pid with the current pid (e.g. 12)
	#			pid = TEXT
	#			pid.data = TSFE:id

				# Fill tt_address.tstamp with the current time as timestamp (like 123456789)
	#			tstamp = TEXT
	#			tstamp.data = date:U

				# Fill tt_address.address with the current formatted time (like "Date: 20.01.2013")
	#			address = TEXT
	#			address.data = date:U
	#			address.strftime = Date: %d.%m.%Y

				# Fill tt_address.name with the value from powermail {firstname}
	#			name = TEXT
	#			name.field = firstname

				# Fill tt_address.last_name with the value from powermail {lastname}
	#			last_name = TEXT
	#			last_name.field = lastname

				# Fill tt_address.company with the value from powermail {company}
	#			company = TEXT
	#			company.field = company

				# Fill tt_address.position with the uid of the mail record
	#			position = TEXT
	#			position.field = uid


			}


			##############################################################
			### EXAMPLE for building a relation to tt_address_group    ###
			### over the MM table tt_address_group_mm                  ###
			### Add relation to an existing address group with uid 123 ###
			##############################################################

			2 {
				# Enable or disable db entry for table tt_address_group_mm
	#			_enable = TEXT
	#			_enable.value = 1

				# Set tableName to "tt_address_group_mm"
	#			_table = TEXT
	#			_table.value = tt_address_group_mm

				# Fill tt_address_group_mm.uid_local with uid of tt_address record from above configuration 1. (usage .field=uid_[key])
	#			uid_local = TEXT
	#			uid_local.field = uid_1

				# Fill new record of table "tt_address_group_mm" with field "uid_foreign" with uid 123
	#			uid_foreign = TEXT
	#			uid_foreign.value = 123
			}
		}
	}

Best pracitce
"""""""""""""

If you want to enable the function not for every form but for some special cases, the whole world of TypoScript is open
to you

.. code-block:: text

    # Enabe function only if a special marker is given
    plugin.tx_powermail.settings.setup.dbEntry.1._enable = TEXT
    plugin.tx_powermail.settings.setup.dbEntry.1._enable.value = 1
    plugin.tx_powermail.settings.setup.dbEntry.1._enable.if.isTrue.data = GP:tx_powermail_pi1|field|anymarkername

.. code-block:: text

    # Enabe function only if the form is located on a defined PID (e.g. 123 in this case)
    plugin.tx_powermail.settings.setup.dbEntry.1._enable = TEXT
    plugin.tx_powermail.settings.setup.dbEntry.1._enable.value = 1
    plugin.tx_powermail.settings.setup.dbEntry.1._enable.if.value = 123
    plugin.tx_powermail.settings.setup.dbEntry.1._enable.if.equals.data = TSFE:id

Another possibility would be to use a TypoScript condition to enable some lines of TypoScript only if a condition is
true (e.g. on a defined page or if a GET/POST param is set, etc...). Please look at the original TYPO3 TypoScript
reference from some condition examples.
