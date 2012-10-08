#!/bin/sh

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
/usr/bin/php $DIR/sort_of_face.php >> $DIR/debug.log 2>&1