# Auto Git Ignore by Novusvetus #

## Overview ##
A post-update-cmd script to automatically add Composer managed packages to .gitignore.


## Installation ##
### Add the following to your composer.json: ###
```json
"scripts": {
    "post-update-cmd": "Novusvetus\\AutoGitIgnore\\GitIgnoreBuilder::go"
}
```

### Add it to your project with: ###
```shell
composer require novusvetus/autogitignore
```

### Optional - Exclude only dev packages: ###
#### Add to the following to your composer.json ####
```json
"extra": {
    "autogitignore": "devOnly"
}
```

## License ##
3-clause BSD license
See [License](LICENSE)


## Bugtracker ##
Bugs are tracked in the issues section of this repository on GitHub.
Please read over existing issues before submitting an issue to ensure yours is unique.

[Create a new issue](../../issues/new)
- Describe the steps to reproduce your issue.
- Describe the expected and the actual outcome.
- Describe your environment as detailed as possible.


## Development and contribution ##
Feature requests can also be made by [creating a new issue](../../issues/new).
If you would like to make contributions to this repository, feel free to [create a fork](../../fork) and submit a pull request.


## Versioning ##
This project follows [Semantic Versioning](http://semver.org) paradigm. That is:

> Given a version number MAJOR.MINOR.PATCH, increment the:
>  1. MAJOR version when you make incompatible API changes,
>  2. MINOR version when you add functionality in a backwards-compatible manner, and
>  3. PATCH version when you make backwards-compatible bug fixes.
> Additional labels for pre-release and build metadata are available as extensions to the MAJOR.MINOR.PATCH format.


## Links ##
* [ReindeerWeb](https://www.reindeer-web.de)
* [Novusvetus](https://www.novusvetus.de)
* [License](./LICENSE)
* [Contributing](./CONTRIBUTING.md)


## Thanks to ##
* [mickaelperrin / Mickaël PERRIN](https://github.com/mickaelperrin)