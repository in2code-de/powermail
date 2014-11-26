.. include:: ../../../Includes.txt

.. _savingvaluestothirdpartytables:

Saving Values to Third Party Table
----------------------------------

Powermail is able to save the values from a submitted form into a
third-party-table (like tt\_news, tt\_address, tt_content, fe_users,
etc...).

This feature and its TypoScript settings are nearly the same as you
may know from powermail < 2.0

Example for tt\_address:

.. code-block:: text

	plugin.tx_powermail.settings.setup {
		# Save values to any table (example for tt_adress)
		dbEntry {
			#####################################################
				### EXAMPLE for adding values to table tt_address ###
				#####################################################

				# Enable or disable db entry for table tt_address
				tt_address._enable = TEXT
				tt_address._enable.value = 1

				# Write only if any field is not yet filled with current value (e.g. test if an email is already in database)
					# default: always add new records (don't care about existing values)
					# update: update record if there is an existing entry (e.g. if email is already there)
					# none: no entry if field is filled (do nothing if record already exists)
				tt_address._ifUnique.email = update

				# Fill new record of table "tt_address" with field "email" with a static value => mail@mail.com
				tt_address.email = TEXT
				tt_address.email.value = mail@mail.com

				# Fill new record of table "tt_address" with field "pid" with the current pid (e.g. 12)
				tt_address.pid = TEXT
				tt_address.pid.data = TSFE:id

				# Fill new record of table "tt_address" with field "tstamp" with the current time as timestamp (like 123456789)
				tt_address.tstamp = TEXT
				tt_address.tstamp.data = date:U

				# Fill new record of table "tt_address" with field "address" with the current formatted time (like "Date: 20.01.2013")
				tt_address.address = TEXT
				tt_address.address.data = date:U
				tt_address.address.strftime = Date: %d.%m.%Y

				# Fill new record of table "tt_address" with field "name" with the value from powermail {firstname}
				tt_address.name = TEXT
				tt_address.name.field = firstname

				# Fill new record of table "tt_address" with field "last_name" with the value from powermail {lastname}
				tt_address.last_name = TEXT
				tt_address.last_name.field = lastname

				# Fill new record of table "tt_address" with field "company" with the value from powermail {company}
				tt_address.company = TEXT
				tt_address.company.field = company



				##############################################################
				### EXAMPLE for adding values to table tt_address_group_mm ###
				### Add relation to an existing address group with uid 123 ###
				##############################################################

				# Enable or disable db entry for table tt_address_group_mm
				tt_address_group_mm._enable = TEXT
				tt_address_group_mm._enable.value = 1

				# Fill new record of table "tt_address_group_mm" with field "uid_local" with uid of tt_address record that was just created before with .field=uid_[tablename]
				tt_address_group_mm.uid_local = TEXT
				tt_address_group_mm.uid_local.field = uid_tt_address

				# Fill new record of table "tt_address_group_mm" with field "uid_foreign" with uid 123
				tt_address_group_mm.uid_foreign = TEXT
				tt_address_group_mm.uid_foreign.value = 123
		}
	}
