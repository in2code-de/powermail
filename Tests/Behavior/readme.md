# How to start behaviour tests for powermail?

## Using docker and ddev

Run the following commands in the project root

- `ddev start`
- `ddev initialize`

Then ssh into ddev `ddev ssh` and then run the behat tests via composer

`composer run test:behaviour`
