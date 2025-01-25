Keywords Extractor
==========================

The `keywords-extractor` is a flexible and customizable PHP library that extracts relevant keywords from text.


## Installation

You can install the package via composer:
```bash
composer require kudashevs/keywords-extractor
```


## Options

The `KeywordsExtractor` class accepts some configuration options:
```
'add_words' => value            # A string or an array of words to add to the result (if they are ignored by an Extractor).
'remove_words' => value         # A string or an array of words to remove from the result (if they are not ignored by an Extractor).
```

**Note:** At the moment of instantiation, the `KeywordsExtractor` class may throw an `InvalidOptionType` exception. This
exception extends a built-in `InvalidArgumentException` class, so it is easy to deal with.


## Testing

```bash
composer test
```


## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

 **Note:** Please make sure to update tests as appropriate.


## License

The MIT License (MIT). Please see the [License file](LICENSE.md) for more information