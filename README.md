# Lister Bundle

[![Scrutinizer Build Status](https://img.shields.io/scrutinizer/build/g/povs/ListerBundle/master?label=scrutinizer-ci)](https://scrutinizer-ci.com/g/povs/ListerBundle/build-status/master)
[![Travis Build Status](https://img.shields.io/travis/povs/ListerBundle/master?label=travis-ci)](https://travis-ci.com/povs/ListerBundle)
[![Code Coverage](https://scrutinizer-ci.com/g/povs/ListerBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/povs/ListerBundle/?branch=master)
[![Code Quality](https://img.shields.io/scrutinizer/quality/g/povs/ListerBundle/master)](https://scrutinizer-ci.com/g/povs/ListerBundle/?branch=master)

### [Documentation]()

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