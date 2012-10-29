sort\_of\_face
==============

A Twitter Gibberish Bot

by [Scott Smitelli](mailto:scott@smitelli.com)
  
Installation and Requirements
-----------------------------  
  
sort\_of\_face is developed and tested on PHP 5.3 machines. I am almost positive
it will not run on 5.2 or lower. I never tested it on anything higher, either.
The `curl` extension is required for the Twitter OAuth stuff, and the `mbstring`
extension is required because of some JavaScript scraping shenanigans.

###To install:

1.  Dump all the files somewhere. It really doesn't matter where.

2.  `cp config.ini-sample config.ini`

3.  Edit `config.ini` to suit your fancy. You'll have to put your own Twitter
    OAuth keys in there, unless you don't want any tweeting to take place.

4.  `chmod a+x sort_of_face.sh`

5.  `./sort_of_face.sh`

That's it. The shell script is designed to never output anything, so you can add
it in a cron job without worrying about spamming root's inbox with junk. A file
called `debug.log` will be created (and appended) by the shell script, which can
tell you more than you ever cared to know about how the gibberish is built.

Acknowledgements
----------------

This package includes Abraham Williams' `twitteroauth` library.
<https://github.com/abraham/twitteroauth>

Doesn't This Violate YouTube's Terms Of Service?
------------------------------------------------

It most certainly does.