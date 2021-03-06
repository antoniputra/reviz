# Reviz

[![Build Status](https://app.travis-ci.com/antoniputra/reviz.svg?branch=master)](https://app.travis-ci.com/antoniputra/reviz)
[![codecov](https://codecov.io/gh/antoniputra/reviz/branch/master/graph/badge.svg?token=YKFF0CBTNJ)](https://codecov.io/gh/antoniputra/reviz)
[![Total Downloads](http://poser.pugx.org/antoniputra/reviz/downloads)](https://packagist.org/packages/antoniputra/reviz)
[![Latest Stable Version](http://poser.pugx.org/antoniputra/reviz/v)](https://packagist.org/packages/antoniputra/reviz)
[![License](http://poser.pugx.org/antoniputra/reviz/license)](https://packagist.org/packages/antoniputra/reviz)

Easy way to record and rollback any changes of your Eloquent Entities.

## Feature

- √ Monitor your Eloquent changes.
- √ Filter specific fields to be monitored.
- √ Single Rollback to specific state
- √ Group rollback by batch
- [soon] GUI


## How to use

Just put `RevizTrait` to your desired Eloquent Models. e.g:

```php
namespace App;

use Antoniputra\Reviz\RevizTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes, RevizTrait;

    ...
}
```