************************************** 
************************************** 
***** brought to you by          ***** 
*****                            ***** 
***** The Wordlist Project       ***** 
***** http://wordlists.cjb.net   ***** 
***** b4a@gmx.net                ***** 
************************************** 
**************************************

**************************** 
*********** INFO *********** 
**************************** 

The English language originated in England and is now widely spoken on six continents. 
It is the primary language of the United States, the United Kingdom, Canada,
Australia, Ireland, New Zealand, and various small island nations in the Caribbean Sea and the Pacific Ocean.<br> 
It is also an official language of India, the Philippines, and many countries in sub-Saharan Africa, including South Africa.
English is a member of the western group of the Germanic languages (itself part of the Indo-European language family) 
and is closely related to Frisian, German, and Netherlandic (Dutch and Flemish).

**************************** 
*********** ABOUT ********** 
**************************** 

This Wordlist was merged from Jorge Stolfis list(see his readme below)
and many other english-wordlists.

I combined all lists to a single wordlist, sorted the
result and removed dupes.
Then I compared it with all other languages for double words and found only a few
thousand doubles(especially common names) - so I have not removed those words from
the english or the other wordlists.

This is maybe the most comprehensive english-wordlist, but not qualified for spellchecking or
similar purposes(because it contains word-combinations, slang, misspellings etc. too).

But it can be very usefull for searching forgotten passwordsand such stuff...

I hope it will be useful!

  Thomas

The Wordlist Project
 --> http://wordlists.cjb.net

The Windows Spying Project
 --> http://spywin.cjb.net

Email
 --> b4a@gmx.net

The Karanet BBS
     --> http://www.karanet.at
     --> telnet://karanet.uni-klu.ac.at
   I am "Fox" on this BBS.

**************************** 
******** WORD-COUNT ******** 
****************************

 694699 Words
7429502  Bytes(Uncompressed, DOS format) 

**************************** 
********* DISCLAIMER ******* 
**************************** 

All publications of "The Wordlist Project" are distributed in the hope 
that they will be useful, but WITHOUT ANY WARRANTY; without even the 
implied warrenty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

I DO NOT guarantee any completeness or linguistic correctness of wordlists
distributed by "The Wordlist Project" and so I can't be blamed for any missing
or false words.

This list as it was distributed by "The Wordlist Project" is for personal
use only!
For commercial use of part of this list you MUST obtain written permission
from ALL authors/creators/editors who may have a copyright on parts of the list. 

If you own the copyright on part of this or other publications of "The Wordlist
Project" and you don't allow redistribution under the terms above please tell me!

**************************** 
** ORIGINAL DOCUMENTATION **
**************************** 


FILE: english.words
VERSION: DEC-SRC-92-04-05

EDITOR

    Jorge Stolfi <stolfi@src.dec.com>
    DEC Systems Research Center
  
AUTHORS OF ORIGIONAL WORDLISTS

    Andy Tanenbaum <ast@cs.vu.nl>
    Barry Brachman <brachman@cs.ubc.ca>
    Geoff Kuenning <geoff@itcorp.com>
    Henk Smit <henk@cs.vu.nl>
    Walt Buehring <buehring%ti-csl@csnet-relay>

DESCRIPTION

    The file english.words is a list  of over 104,000
    English words compiled from several public domain wordlists.  

    The file has one word per line, and is sorted with sort(1)
    in plain ASCII collating sequence.

    The file is supposed to include all verb forms ("-s", "-ed",
    "-ing"), noun plurals and possesives, and forms derived by various
    prefixes and suffixes ("un-", "re-", "-ly", "-er", "-ation", etc.)
    However, the list is still highly incomplete and inconsistent: not
    all stems have all forms, and some forms (notably possesive
    plural) are missing altogether.

    The file is NOT supposed to contain any "proper" names, such as
    the names of ordinary persons, corporations and organizations;
    nations, countries and other geographical names; mythological
    figures; biological genera; and trademarked products.  It is also
    not supposed to contain abbreviations, measurement symbols, and
    acronyms. (Some of these are available in separate files; see
    below).

    The pronoun "I" and its contractions ("I'm", "I've") are
    capitalized as usual; the other words are all in lowercase.
    Besides the letters [a-zA-Z], the file uses only hyphen
    apostrophe, and newline.

AUXILIARY LISTS

    In the same directory as englis.words there are a few
    complementary word lists, all derived from the same sources [1--8]
    as the main list:

    english.names

        A list of common English proper names and their derivatives.
        The list includes: person names ("John", "Abigail",
        "Barrymore"); countries, nations, and cities ("Germany",
        "Gypsies", "Moscow"); historical, biblical and mythological
        figures ("Columbus", "Isaiah", "Ulysses"); important
        trademarked products ("Xerox", "Teflon"); biological genera
        ("Aerobacter"); and some of their derivatives ("Germans",
        "Xeroxed", "Newtonian").
    
    misc.names

        A list of foreign-sounding names of persons and places
        ("Antonio", "Albuquerque", "Balzac", "Stravinski"), extracted
        from the lists [1--8].  (The distinction betweeen
        "English-sounding" and "foreign-sounding" is of course rather
        arbitrary).

    org.names

        A short lists names of corporations and other institutions
        ("Pepsico", "Amtrak", "Medicare"), and a few derivatives.  

        The file also includes some initialisms --- acronyms and
        abbreviations that are generally pronounced as words rather
        than spelled out ("NASA", "UNESCO").

    english.abbrs

        A list of common abbreviations ("etc.", "Dr.", "Wed."),
        acronyms ("A&M", "CPU", "IEEE"), and measurement symbols
        ("ft", "cm", "ns", "kHz").

    english.trash
                
        A list of words from the original wordlists
        that I decided were either wrong or unsuitable for inclusion
        in the file english.words or any of the other auxiliary 
        lists. It includes
        
          typos ("accupy", "aquariia", "automatontons")
          spelling errors ("abcissa", "alleviater", "analagous")
          bogus derived forms ("homeown", "unfavorablies", "catched")
          uncapitalized proper names ("afghanistan", "algol", "decnet")
          uncapitalized acronyms ("apl", "ccw", "ibm")
          unpunctuated abbreviations ("amp", "approx", "etc")
          British spellings ("advertize", "archaeology")
          archaic words ("bedight")
          rare variants ("babirousa")
          unassimilated foreign words ("bambino", "oui", "caballero")
          mis-hyphenated compounds ("babylike", "backarrows")
          computer keywords and slang ("lconvert", "noecho", "prog"), 

        (I apologize for excluding British spellings.  I should have
        split the list in three sublists--- common English, British,
        American---as ispell does.  But there are only so many hours
        in a day...)

    english.maybe

        A list of about 5,000 lowercase words from the "mts.dict"
        wordlist [6] that weren't included in english.words.

        This list seems to include lots of "trash", like uncapitalized
        proper names and weird words.  It would take me several days
        to sort this mess, so I decided to leave it as a separate
        file.  Use at your own risk...
        
ORIGINAL LISTS 

    The original wordlists from which those files were compiled are
    listed below.  They were obtained by anonymous FTP on 92-Feb-10.

    [1] file: ispell/ispell/english.lrg
        size: 690778 bytes
        contact: Walt Buehring <buehring%ti-csl@csnet-relay>
        from: phloem.uoregon.edu: /pub/src/ispell.3.0.tar.Z

          * The (unexpanded) "large" english wordlist for ispell 3.0.

    [2] file: ispell/ispell/english.sml+
        size: 575226 bytes
        contact: Walt Buehring <buehring%ti-csl@csnet-relay>
        from: phloem.uoregon.edu: /pub/src/ispell.3.0.tar.Z

          * The (expanded) "small" english wordlist for ispell 3.0.

    [3] file: words.english.Z
        size: 217119 bytes (479261 bytes uncompressed)
        contact: Henk Smit <henk@cs.vu.nl>
        from: donau.et.tudelft.nl: /pub/words/

          * From the README file on ftp.cs.vu.nl:

                This list is made out of 2 lists,
                  the normal /usr/dict/words on most Unix systems,
                  TeX english wordlist (available at archive.cs.ruu.nl)

    [4] file: dict.2
        size:   274848 bytes
        contact: H Morrow Long <long-morrow@CS.YALE.EDU>
        from: bulldog.cs.yale.edu: /pub/dict.shar

          * According to H. Morrow, it came with some version
            of the "ispell" package.

    [5] file: minix.dict
        size: 357226 bytes
        author: Andy Tanenbaum <ast@cs.vu.nl>
        from: cs.ubc.ca: /pub/wordlists-1.0.tar.Z

          * From the README file:

            Article 1997 of comp.os.minix:
            From: ast@botter.UUCP
            Subject: A spelling checker for MINIX
            Date: 6 Jan 88 22:28:22 GMT
            Reply-To: ast@cs.vu.nl (Andy Tanenbaum)
            Organization: VU Informatica, Amsterdam

            This dictionary is NOT based on the UNIX dictionary so it
            is free of AT&T copyright.

            I built the dictionary from three sources.  First, I
            started by sorting and uniq'ing some public domain
            dictionaries.  Second, as some of you probably know, I
            have written somewhere between 3 and 6 books (depending on
            precisely what you count) and an additional 50 published
            papers on operating systems, networks, compilers,
            languages, etc.  This data base, which is online, is
            nonnegligible :-) Finally, I added a number of words that
            I thought ought to be in the dictionary including all the
            U.S. states, all the European and some other major
            countries, principal U.S. and world cities, and a bunch of
            technical terms.  I don't want my spelling checker to barf
            on arpanet, diskless, modem, login, internetwork,
            subdirectory, superuser, vlsi, or winchester just because
            Webster wouldn't approve of them.

            All in all, the dictionary is over 40,000 words.  If you
            have any suggestions for additions or deletions, please
            post them.  But please be sure you are not infringing on
            anyone's copyright in doing so.

              Andy Tanenbaum (ast@cs.vu.nl)

    [6] file: mts.dict
        size: 346983 bytes
        contact: Barry Brachman <brachman@cs.ubc.ca>
        from: cs.ubc.ca: /pub/wordlists-1.0.tar.Z

          * From the README file:

            These word lists were collected by Barry Brachman
            <brachman@cs.ubc.ca> at the University of British
            Columbia.  They may be freely distributed as long as this
            notice accompanies them.

            mts.dict contains only words that are not in
            /usr/dict/words.  [But note that your version of
            /usr/dict/words may be different from mine!  Use "sort -u"
            to get a list of unique words. ]

              From wc:

              24259   24259  198596 /usr/dict/words
              35475   35475  346992 mts.dict
              -----   ----- -------
              59734   59734  545588 total


    [7] file: words.english.Z
        size: 288385 bytes (644217 bytes uncompressed)
        from: ftp.hawaii.edu: /pub/editors/LEXICAL/word-lists/
        author: unknown.

    COMMENTS: The "large" list from ispell 3.0 [1] is the most
    complete, and contains almost all the words of the "small" ispell
    list [2], of Andy Tannenbaum's list minix.dict [5], and of the
    lists from Delft and Yale [3, 4], as well as /usr/dict/words. It
    leaves out some 500--1000 words from each of these lists.

    On the other hand, the file mts.dict from UBC [6] contains some 7000
    words that are not in the ispell list [1].  Therefore, mts.dict
    seems to be largely orthogonal to the list [1--5].

    The file words.english from Hawaii [7] seems to be the union of
    mts.dict [6], Andy's file minix.dict [5], and /usr/dict/words,
    except that it omits some 250 words from the latter.

COMPILATION PROCESS

    The file english.words is a slightly cleaned-up version of
    the "large" english wordlist [1] that comes with the ispell
    3.0 package, which is available from phloem.uoregon.edu.  

    First, I expanded the prefixes and suffixes using "isexpand" and
    some Gnuemacs hacking, and removed all words with capitals or
    periods.  Then I compared the result with other publicly available
    wordlists [2--7], and did a little bit of manual cleanup.  That
    meant removing some 8500 words that were obviously wrong or
    inappropriate, and adding about 4800 new words.  Those 8500
    words were largely distributed among the other lists.

    The table below gives the number of lowercase words in each
    original list ("lcase"), and how many of such words were included
    ("accept") and not included ("reject") in the final file
    english.words:

      ref  site: file                lcase  accept  reject
      ---  ----------------------  -------  ------  ------
      [1]  uoregon: english.lrg     103124  102000    1124
      [2]  uoregon: english.sml+     56694   56223     471
      [3]  tudelft: words.english    48150   47305     845
      [4]  yale: dict.2              47355   46577     778
      [5]  ubc: minix.dict           38699   38394     305
      [6]  ubc: mts.dict             35215   28874    6341
      [7]  hawaii: words.english     65165   57558    7607

(NON-)COPYRIGHT STATUS

  To the best of my knowledge, all the files I used to build these
  wordlists were available for public distribution and use, at least
  for non-commercial purposes.  I have confirmed this assumption with
  the authors of the lists, whenever they were known.
  
  Therefore, it is safe to assume that the wordlists in this package
  can also be freely copied, distributed, modified, and used for
  personal, educational, and research purposes.  (Use of these files in
  commercial products may require written permission from DEC and/or
  the authors of the original lists.)
  
  Whenever you distribute any of these wordlists, please distribute
  also the accompanying README file.  If you distribute a modified
  copy of one of these wordlists, please include the original README
  file with a note explaining your modifications.  Your users will
  surely appreciate that.

(NO-)WARRANTY DISCLAIMER

  These files, like the original wordlists on which they are based,
  are still very incomplete, uneven, and inconsitent, and probably
  contain many errors.  They are offered "as is" without any warranty
  of correctness or fitness for any particular purpose.  Neither I nor
  my employer can be held responsible for any losses or damages that
  may result from their use.
