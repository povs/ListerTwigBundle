# Lister Twig Bundle

[![Scrutinizer Build Status](https://img.shields.io/scrutinizer/build/g/povs/ListerTwigBundle/master?label=scrutinizer-ci)](https://scrutinizer-ci.com/g/povs/ListerTwigBundle/build-status/master)
[![Travis Build Status](https://img.shields.io/travis/povs/ListerTwigBundle/master?label=travis-ci)](https://travis-ci.com/povs/ListerTwigBundle)
[![Code Coverage](https://scrutinizer-ci.com/g/povs/ListerTwigBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/povs/ListerTwigBundle/?branch=master)
[![Code Quality](https://img.shields.io/scrutinizer/quality/g/povs/ListerTwigBundle/master)](https://scrutinizer-ci.com/g/povs/ListerTwigBundle/?branch=master)

> In development

### [Documentation](https://povs.github.io/ListerTwigBundle)

ListerTwigBundle is an extension for [ListerBundle](https://github.com/povs/ListerBundle) for full stack web applications using twig as template engine.

It provides two list types:
 - TwigListType
 - AjaxListType
 
Those types helps to render html views using themes.
 
#### Themes
Bundle comes with two themes
- `default` renders list as a table. Has no styling classes.
- `bootstrap 4` renders list as a table with bootstrap 4 styling classes.
 
Themes can be easily created, extended and modified.

#### Requirements
- Php `>=7.1`
- Symfony `>=4`
- Doctrine ORM
- Twig