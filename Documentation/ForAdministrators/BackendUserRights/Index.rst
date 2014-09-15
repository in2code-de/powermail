.. include:: Images.txt
.. include:: ../../Includes.txt

Backend User Rights
-------------------

Introduction
^^^^^^^^^^^^

A backend user without administration rights is not able to manage all fields in a powermail form by default.
Maybe the Plugin looks like:

|missingrights|

Solution
^^^^^^^^

The user is not able to see the powermail fields. The problem is simple to solve.
The admin should have a look into "Allowed excludefields" within tab "Access list" in the user or usergroup record. See section "Page Content".

|beuserrights|

After having all access rights, the plugin will look like:

|fullaccessrights|