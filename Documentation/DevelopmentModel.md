# EAP, development and branching model

## Early Access Program (EAP)

The base for this development model is the **E**arly **A**ccess **P**rogram (EAP). The EAP provides a private, early
access to a version of Powermail, which is compatible with the youngest TYPO3 LTS version. The reason for an EAP program
is, that EXT:powermail and related extensions are considered feature complete and we need at least a partial
re-financing of the development efforts.

The code, developed while the EAP period, will become public nine months after the TYPO3 LTS was released. If you want
to learn more about in2codes EAP program or have questions, please head over to
[in2code EAP](https://www.in2code.de/en/agency/typo3-extensions/early-access-program/) or ask in the
[#ext-powermail channel on TYPO3 slack](https://typo3.slack.com/archives/C06P2DQG2).


## Which versions get which updates?

| TYPO3 - compatibility                     | Support/Development                                                           | Example (TYPO3 version)     | Branch name          |
|-------------------------------------------|-------------------------------------------------------------------------------|-----------------------------|----------------------|
| upcoming TYPO3 LTS                        | Security, Bugfixes, Features, Breaking changes                                | v14                         | master               |
| current TYPO3 LTS                         | Security, Bugfixes, Features, Breaking changes (until powermail release 13.0) | v13 (released October 2025) | master               |
| old TYPO3 LTS (before EAP becomes public) | Security, Bugfixes, Features                                                  | v12 (until July 2025)       | typo3_v12            |
| old TYPO3 LTS (after EAP becomes public)  | Security, Bugfixes                                                            | v12 (after July 2025)       | typo3_v12            |
| TYPO3 ELTS                                | Security, Bugfixes (paid)                                                     | v11, v10, v09               | typo3_v11, typo3_v10 |

## Branching Model

The development for the youngest (or upcoming) TYPO3 LTS compatible version happens in branch `master`. Each older
version has its own compatibility branch. The branch name has nothing to do with the powermail version.

When the development for a recently published or upcoming TYPO3 LTS version starts a new branch for "LTS - 1" is
created. For example, when the development for TYPO3 v13 compatibility starts, a new branch `typo3-v12` is created.
At this moment this will be the default branch for the *public* repository "for the time being". The base of all open
pull requests (against `master`) will be changed to the new branch by the maintainer.

Development takes place in branches `docs`, `feature` and `bugfix`. Before merging they must meet following conditions:

- necessary tests are added to the test suite
- documentation is updated
- branch is rebased onto the default branch
- all tests are green: unit, acceptance, phpstan, codestyle

If everything is fine, the branch is merged as fast forward. This ensures a nice history and makes it possible to have
pretty releases at github with all solved issues and attributions to contributors.

The development for public code takes place in the public repo. The private repository acts a mirror of these branches.

The development for the upcoming compatibility release happens in branch `master` of the private repository. Branch
`master` is blocked for everyone (even the maintainer) in the public repo until the EAP development becomes public
(TYPO3 LTS release + 9 months).

When the EAP phase is over, the code of branch `master` will be pushed as is to the public repo (including tags) and
also available via packagist and the TYPO3 TER.



