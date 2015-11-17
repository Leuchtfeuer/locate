2015-06-03
Olle Haerstedt
--------------

There's no online manual?

The logger will write to screen, not to file. TODO: Fix this.
To see the logger output, enable _both_ debug=1 and dryRun=1 in Typoscript.

We had the situation belgium-frensh and belgium-dutch. Solve like this:

    30 = \Bitmotion\Locate\Judge\AndCondition
    30.action = redirect_be_fr
    30.matches (
      browserAccepted.lang = fr
      browserAccepted.locale = fr_BE
    )

2013?
Rene Fritz
----------

Feel free to add some documentation or simply add a link to the online manual.
