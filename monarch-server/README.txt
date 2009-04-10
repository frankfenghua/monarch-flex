This is the Andrew branch. Ryan Lin did most of the work in writing
the first version of the Scraper for the Community Analysis Tool. In
viewing the design, I realized that it was important that it remain as
modular as possible, as the scraping must occur for a large number of
varying websites. I devised a StructuredCrawl class that can be used
to define a hierarchical crawl for any website and have worked to
generalize the processing that goes on for any site. There is a single
StructuredCrawl instance and one or more Processor instances defined
for any site that we scrape. Processors should be designed for
websites by inheriting from a more generic processor (such processors
have yet to be defined).
See the full documentation of this project for more information.

Andrew Spencer, 2008


