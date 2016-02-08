

# Introduction #

This page includes everything that users need to know to use Monarch productively.


# Installation #

In order to install and use Monarch you need the [Adobe AIR runtime](http://get.adobe.com/air/). You will then be able to install our application using the installer available under [downloads](http://code.google.com/p/monarch-flex/downloads/list).

# Adding a Website #

The website creation wizard will tell you everything you need to know. Try it out!

## Regular Expression Syntax ##

Adapter creation requires you to construct PCRE regular expressions. Your adapter provides a schema that our servers can overlay on existing pages and new pages in order to make sense out of the online communities that you are interested in. There are many great resources available for learning more about regular expressions.
A few:
[Printable cheat sheet](http://www.phpguru.org/downloads/PCRE%20Cheat%20Sheet/PHP%20PCRE%20Cheat%20Sheet.pdf)
[Where else](http://en.wikipedia.org/wiki/PCRE)
[Hmm..yes](http://xkcd.com/208/)

## Explanation of Crawling Preferences ##

Once you have completed the "Add Website" process our servers will schedule information collection tasks to gather up information from your website. The following pieces of information provided by you serve as guides to scheduling these events.

**Minutes to wait between crawls** - our servers schedule tasks on a minute granularity and depending on how often new content is available on a site this parameter may be adjusted so no new stuff slips through the cracks. However, if you get too greedy just know that justice will be served.

**Number of top-level pages to explore per crawl** - our software abstraction of a crawl, or information gathering session, assumes that the pages of a website are organized into tree-like structures where more general pages link to more specific pages. Pages within the same level may also link to each other. Typically, a site will have a set of pages with lists of threads of conversation or lists of headlines. We refer to these pages as "top-level". As top-level pages are explored, the more specific pages below them in the hierarchy are also explored. The total scope of exploration is therefore defined by the limit set on the number of top-level pages.

Generally, it is better to set a low number of top-level pages to explore with a relatively low period between crawls as this ensures that statistics will be updated most frequently.

# Bug Reporting #

This is a very early beta so there are sure to be bugs and usability issues. Please help us by reporting issues [here](http://code.google.com/p/monarch-flex/issues/entry).