# Introduction

These are my solutions for the [Advent of Code 2017 event](http://adventofcode.com/2017).
It's meant to show off my knowledge as a software developer and to use as code samples for job interviews.
This year, I've chosen to write my solutions in php.

This project uses a number of modern php features, such as package management and namespaces.
It follows PSR-1, PSR-2, PSR-3 and PSR-4.
It has only been tested on php version 5.6.
The project requires a global install of composer for the examples to work.

you can run the examples by cloning the repository and running:

```shell
composer install
php main.php <advent_number> [additional_arguments...]
```

Look into src/D&lt;advent_number&gt; for instructions on how to run each respective challenge.

One thing to note is that each program can be run with "easy" input (where the answer is given in the challenge)
and "hard" input (where the answer is not given in the challenge).
Some challenges have multiple easy inputs, like challenge 1.
For a given input, the subprogram solves both challenges.

# Todo
I should probably add some tests to be run by phpunit.

The program doesn't handle errors as gracefully as possible at the moment.
Incorrect arguments or unfulfilled prerequisites may cause failures with unhelpful error messages.

The project doesn't follow some conventions that are common among utilities, like having vendor binaries.
If I decide to publish this on packagist, I should probably do something about that.

Right now, I've only written solutions for the following days:
* 1
* 2
* 3
* 4
* 5
* 6
* 7
* 8
* 9
* 10
* 11
* 12
* 13
* 14