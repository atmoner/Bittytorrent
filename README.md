![Bittytorrent](https://i.imgur.com/pYv0Q9b.png) Bittytorrent
=============

* * *

*   [What is BitTorrent?](#what-is-bittorrent "What is BitTorrent?")
*   [What is BittyTorrent?](#what-is-bittytorrent "What is Bittytorrent?")
*   [How to install Bittytorrent?](#how-to-install-bittytorrent "How to install Bittytorrent?")
*   [All plugins Bittytorrent](http://forum.bittytorrent.pw/plugins-vf16.html "All plugins Bittytorrent")
*   [All Themes Bittytorrent](http://forum.bittytorrent.pw/themes-vf13.html "All Themes Bittytorrent")
*   [Help us on dev or try beta](http://git.bittytorrent.pw/atmoner/Bittytorrent "Help us on dev")  
*   [Demonstration](#demonstration "Demonstration")


### What is BitTorrent? ###

BitTorrent is file sharing software. You need four things to have BitTorrent work: a BitTorrent client, a BitTorrent tracker, a file to share, and a torrent file (made from the file to share.) The torrent file is placed where others can have access to it (i.e. a website.)  

The clients use the torrent file to connect to the tracker, this allows the client to find other peers to get the file from. This is the simplest explanation

### What is BittyTorrent? ###

Bittytorrent is a bittorrent tracker, this is a script that allows you to deploy a website for sharing torrents.  
Its bittorrent php tracker. 

### How to install Bittytorrent? ###

To install Bittytorrent, you do not need much!  

1.  Web server  
2.  Mysql database  
3.  mod_rewrite enabled on your apache web server (see it in your phpinfo())  

To begin, create the file `db.php` in the folder `libs`, send your file on ftp and open your browser to the selected domain name. You should see the installation page!
Follow the installation instructions.

Once the installation is complete, you must delete the install.php file, libs/db.sql and put a chmod 644 on the file libs/db.php

### Demonstration ###

[Demonstration](http://demo.bittytorrent.pw/ "Demonstration")
 
### Script used ###

Script used for the creation of Bittytorrent:

*   Bootstrap (http://getbootstrap.com/)  
*   Bootstrap-editable (http://vitalets.github.io/bootstrap-editable/)  
*   Select2 (http://ivaynberg.github.io/select2/)  
*   Lightbox (https://github.com/ashleydw/lightbox)  
*   Justgage (http://justgage.com/)
