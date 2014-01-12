---
layout: post
title: "From Dotclear to Octopress"
date: 2013-12-29 22:07:24 +0100
comments: true
categories: GeekTime
---
After more than four years of inactivity on my personal blog
(I've been busy [blogging professionally](https://techtalk.intersec.com)
in the meantime), I've decided to migrate my old blog to
[Octopress](http://octopress.org).

Several reasons for this. The first one is mostly that I don't believe
[Dotclear](http://dotclear.org) has a future anymore. At the time I chose
Dotclear over Wordpress, it was in very active development, going from 1.x
to 2.x with regular release and a very active community: it was a
promising software that could have fought side by side with wordpress.
Morevoer, it was a french software.

However, Dotclear is probably to much of a carbon copy of wordpress: same
technology (PHP), same admin architecture, ... to make the difference.
Wordpress is backed by a dedicated compagny, and Dotclear which is made as a
hobby project just can't catch up. As a consequence, there is no technical
reason to use dotclear over wordpress anymore: the later has a much more
active development pace, a wide ecosystem and a very active community...
Dotclear has been completely forgotten: only its own community care about
it. Tools such as Disqus don't provide any integrated solution for dotclear,
leaving users to hack the code of their theme if they wish to integrate
that kind of service. Even tools that aims at providing migration utility
to other blogging platform don't list Dotclear as a source platform.

The second reason is that I find it completely overkill to run some PHP
to serve a near-static content. My blog has not been update for 4 years, but
every time someone reads a page, it goes through some PHP. PHP may be (have
been) great for dynamic sites, but not for delivering content: it consumes
resources and may contain security hole. The sole dynamic part of the site
that is user-facing is the comment system... however nowadays services like
Disqus, Google or Facebook can be integrated in your site for this in just
a few lines of HTML/javascript (and by selling your soul to the devil):
you may have an interactive site with only static pages.

So, its done, my site has been migrated to a new static platform and is
hosted by github (which wasn't mandatory but frees me from a bit of system
administration). I couldn't find any packaged tool to perform the migration
so I wrote one [small script](https://gist.github.com/Fruneau/8174826) by
myself. If the script is of interest for you here is how to proceed:

* export the database and the media library of your blog from Dotclear maintenance
  page
* unpack the media library in the `source/assets` directory of Octopress
* run the Python script on the database export (which should be named
  `python dotclear-octopress.py <date>-<blogname>-<backup>.txt` (the scripts
  runs in Python and requires `pip install phpserialize`)
* the script should have generated a new directory that contains a series of
  `.csv` file and a subdirectory called `_posts` that contains the markdown
  pages for your old blog posts.
* copy the content of `_posts` to `source/_posts` of Octopress.

At this point, the result is not perfect (but I'm sure the script can be improved),
you'll still have to go through your posts and check that they were properly
_markdownified_ (Dotclear source text is in a Dotclear-specific wiki syntax,
the script tries to do the conversion, but it is not 100% accurate and will
miss some corner cases).

The script also dump a file `rss.xml` that contains the extended RSS format that
can be used to import discussions on [Disqus](http://disqus.com).
