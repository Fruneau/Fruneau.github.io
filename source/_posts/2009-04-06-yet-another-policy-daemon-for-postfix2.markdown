---
layout: post
title: "Yet another policy daemon for postfix"
date: 2009-04-06 22:04:00
comments: true
categories: GeekTime
---
[MadCoder announced it months ago](http://blog.madism.org/index.php/2007/08/29/136-postfix-and-srs), he has been working on the pfixtools. The second tool of the [postfix](http://www.postfix.com)-related toolsuite is named __postlicyd__.

postlicyd is a versatile [policy daemon](http://www.postfix.org/SMTPD_POLICY_README.html) written in C. It does greylisting (far faster than postgrey), it performs R(H)BL access (both locally directly from rbldns zone files and remotely by using DNS), ... So, it can be used as a replacement for [whitelister](http://packages.qa.debian.org/w/whitelister.html) and [postgrey](http://postgrey.schweikert.ch/) with a significant improvement of the performances.

On the same server (with the same email trafic), postlicyd is more than 20 times faster than postgrey:

*   Process load: postgrey ~20% CPU, postlicyd less than 1% CPU
*   Data base cleanup for 1M entries: postgrey 20 minutes, postlicyd 40 seconds

Moreover, it is aware of the 'session' feature of the POLICY protocol. Thus, you can write complex configurations and define policies that do not depend on a single SMTP command (like RCPT) but on the whole SMTP transaction...

More informations: [http://pfixtools.mymind.fr]
