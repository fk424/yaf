#!/bin/sh
rm -rf htdoc/doc
phpdoc run -d . -t htdoc/doc --template=clean
