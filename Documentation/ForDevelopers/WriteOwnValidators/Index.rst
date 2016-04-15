.. include:: Images.txt

.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. ==================================================
.. DEFINE SOME TEXTROLES
.. --------------------------------------------------
.. role::   underline
.. role::   typoscript(code)
.. role::   ts(typoscript)
   :class:  typoscript
.. role::   php(code)


.. _newvalidators:

Add new Validators
^^^^^^^^^^^^^^^^^^

Introduction
""""""""""""

Since powermail 2.1 a combination of different validation types is possible:

- Serverside Validation (PHP)
- Clientside Validation (JavaScript)
- Clientside Validation (Native with HTML5)

You can enable or disable or combine some of the validation via TypoScript

::

   plugin.tx_powermail.settings.setup {
   	    validation {
			native = 1
			client = 1
			server = 1
   	    }
   }

Parsley.js allows us to have a robust solution for JavaScript and Native HTML5 Validation

|developer_new_validation2|

Serverside Validation Example


|developer_new_validation1|

Clientside Validation Example

Want to learn more about Parsley.js?
`http://parsleyjs.org/ <http://parsleyjs.org/>`_

**Add own validators**

.. toctree::
   :maxdepth: 1
   :titlesonly:
   :glob:

   ValidationType/Index
   ServersideValidation/Index
   ClientsideValidation/Index
