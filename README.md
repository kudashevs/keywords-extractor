# Keywords Extractor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kudashevs/keywords-extractor.svg)](https://packagist.org/packages/kudashevs/keywords-extractor)
[![Run Tests](https://github.com/kudashevs/keywords-extractor/actions/workflows/run-tests.yml/badge.svg)](https://github.com/kudashevs/keywords-extractor/actions/workflows/run-tests.yml)
[![License MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.md)

The `keywords-extractor` is a flexible and customizable PHP library that extracts relevant keywords from text.


## Installation

You can install the package via composer:
```bash
composer require kudashevs/keywords-extractor
```


## Usage

The key feature of the `keywords-extractor` library is the possibility to extract not only individual nouns, but meaningful
sequences of words that make more sense in some contexts. This possibility is provided by the [RAKE PHP](https://github.com/kudashevs/rake-php)
library that is used as the default extraction algorithm. If this library doesn't suit your needs, it can be easily
substituted with something more appropriate or relevant.

The usage of the `KeywordsExtractor` class is pretty straightforward. To extract keywords call the `extract` method:
```php
use Kudashevs\KeywordsExtractor\KeywordsExtractor;

$extractor = new KeywordsExtractor([
    'add_words' => 'some',
    'remove_words' => 'interesting'
]);

$keywords = $extractor->extract('this is some interesting text');

print_r($keywords); // some, text
```

To make this library even more convenient to use in some specific cases, it provides a fluent interface. 
```php
use Kudashevs\KeywordsExtractor\KeywordsExtractor;

$extractor = new KeywordsExtractor();

$keywords = $extractor->addWords(['this', 'example'])
    ->extract('this is a usage example');
    
print_r($keywords); // usage example, this
```

### Words collections

Sometimes, there may be a necessity to provide a big number of words to be excluded or included. It can be done with the
`add_words` and `remove_words` options. In some cases, the options are not convenient to use. For these cases the library
introduces a concept of [words collections](Collections/WordsCollection.php) and correspondent asset files. To start using
them, provide an `assets_path` option to the `KeywordsExtractor` class. The instantiation process is going to create two
different folders in the provided `assets` folder (once created, these files won't be modified):
- `words` folder - contains default files used for words exclusions and words inclusions
- `cache` folder - contains cached words collections (clean it whenever the `words` folder is updated)

If you want to include some words to the generated keywords, update the `rake_exclude.txt` file. If you want to exclude
some words from the generated keywords, update the `rake_include.txt` file. The naming logic may seem wierd, but it is
because the words are included to a list of stop words and excluded from a list of stop words.

### Result length

By default, the returning result is limitless, meaning that the length of the result is not limited. However, in some
cases the length of the result does matter. For these cases the package introduces the concept of a [Limiter](Limiters/Limiter.php).

The library provides two possibilities to limit the result:
- using the `limit_length` option (for more information please refer to [options](#options))
- using a custom Limiter with a pre-defined max length (the library contains a `LengthLimiter` class that limits by length
and a `PercentLimiter` class that limits by a text's percent that can be configured)


## Options

The `KeywordsExtractor` class accepts some configuration options:
```
'extractor'                     # An Extractor instance that does all of the extraction work.
'assets_path'                   # A string with a valid path with write permissions to keep assets.
'add_words' => value            # A string or an array of words to add to the result (if they are ignored by an Extractor).
'remove_words' => value         # A string or an array of words to remove from the result (if they are not ignored by an Extractor).
'limiter'                       # A Limiter instance that limits the length of the end result.
'limit_length'                  # An integer defines the maximum length of the result (is used only when a Limiter is not provided).
```

**Note:** At the moment of instantiation, the `KeywordsExtractor` class can throw a few exceptions: `InvalidOptionType`,
`InvalidOptionValue`. These exceptions extend a built-in `InvalidArgumentException` class, so they are easy to deal with.


## Testing

```bash
composer test
```


## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

 **Note:** Please make sure to update tests as appropriate.


## License

The MIT License (MIT). Please see the [License file](LICENSE.md) for more information