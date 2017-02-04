.. include:: ../../../Includes.txt

.. _dateConverterUserFunc:

Convert a date into another format with a TypoScript UserFunc
-------------------------------------------------------------

Powermail delivers a userFunc that could be used from administrators to convert date formats with TypoScript.
This userFunc is not needed for the normal work of the extension.
It is just an add-on for your special needs.


Example Use-Case:

* Powermail with a date field is in use
* After submit the date should be stored in a third-party-table (e.g. tx_ext_domain_model_table.endtime or tt_content.endtime)
* The problem is, if someone would get the POST param from date field, it will be a readable string like "2015-12-31" but a timestamp is needed


.. code-block:: text

	# Convert 31.12.2015 to 2015-12-31
	lib.test = USER
	lib.test {
		userFunc = In2code\Powermail\UserFunc\DateConverter->convert
		includeLibs = EXT:powermail/Classes/UserFunc/DateConverter.php

		input = TEXT
		input.value = 31.12.2015

		inputFormat = TEXT
		inputFormat.value = d.m.Y

		outputFormat = TEXT
		outputFormat.value = Y-m-d
	}



.. code-block:: text

	# Convert 2015-12-31 into 1451516400
	lib.test = USER
	lib.test {
		userFunc = In2code\Powermail\UserFunc\DateConverter->convert
		includeLibs = EXT:powermail/Classes/UserFunc/DateConverter.php

		input = TEXT
		input.value = 2015-12-31

		inputFormat = TEXT
		inputFormat.value = Y-m-d

		outputFormat = TEXT
		outputFormat.value = U
	}



.. code-block:: text

	# Convert 2015-12-31 into 1451516400
	plugin.tx_powermail.settings.setup.dbEntry.1 {
		# enable db entry
		_enable = TEXT
		_enable.value = 1

		# set table name
		_table = TEXT
		_table.value = tt_content

		# Fill with static value
		pid = TEXT
		pid.value = 123

		# Fill with current timestamp
		crdate = TEXT
		crdate.data = date:U

		# Fill header from powermail field with marker {header}
		header = TEXT
		header.data = GP:tx_powermail_pi1|field|header

		# Fill header from powermail field with marker {date} from Y-m-d to a unix timestamp
		starttime = USER
		starttime {
			userFunc = In2code\Powermail\UserFunc\DateConverter->convert
			includeLibs = EXT:powermail/Classes/UserFunc/DateConverter.php

			input = TEXT
			input.data = GP:tx_powermail_pi1|field|date

			inputFormat = TEXT
			inputFormat.value = Y-m-d

			outputFormat = TEXT
			outputFormat.value = U
		}
	}
