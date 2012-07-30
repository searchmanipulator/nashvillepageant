=== Collapsing Objects ===
Contributors: DrLebowski
tags: Post, Page
Stable tag: 0.2

A plugin that puts a collapsible div in a post or a page.

== Description == 

Collapsing objects wraps anything enclosed in expand tags into a collapsible div. This is a work in progress and may be a little bit messy due to me not having done much with PHP in a while and being new to wordpress. 

Please note that this requires javascript, if you expect visitors with javascript disabled please do NOT use this plugin!

I made a couple of mistakes before when submitting such as leaving a wrong hardcoded path in the script and messing up the versioning due to not knowing how WordPress plugin indexing worked. This is now fixed and I have added a working example on my blog. Sorry for any inconvenience, please inform me if there are still issues

== Installation ==

Copy the collapsing objects folder into wp-content/plugins and activate in the plugins section of the admin panel. 

== Usage ==

Wrap your text/content in [expand] tags with a title eg:

>[expand title=hello]This is my expanding content[/expand]

You MUST specify a title!

== Example ==
http://tinyurl.com/3vjavq

== Changes ==


>**22nd May 2008:**
>
>Fixed expanding or collapsing pushing back to the top of page
>

>**21st May 2008:**
>
>Fixed problems with readme
>