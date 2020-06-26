Photo Gallery Application: a web app to allow users upload images, enter details associated with the images and then display them back to the user, in more than  one size format.
------------------------------------------------------
Author:  nkatz01 13128128 
Date:	06/08/2019
Site address: http://titan.dcs.bbk.ac.uk/~nkatz01/w1fma/index.php

Before deploying...

Configuration
-------------
1. Change values defined in "includes/config.php" to
match current environment.

Installation
------------
2. After uploading the files to the server, the permissions on the uploads
directory, the media/thumbs directory and media/large direcotry, as defined in includes/config.inc.php , should be set to 777. 

Note: images can be uploaded from any location. In the current instance, the images used to test this application reside the media folder.

3. To set up minimal database import "sql/createImgTbl.sql"

Deployment
-------------
4. Unzip the nkatz01_w1fma and drop or paste all content, as it is, from inside the top most parent folder (that is nkatz01_w1fma), into the public_www folder/directory on the mapped drive or server named: naktz01(\\fileserv) (H:), so that index.php is directly accessible from within the public_www directory

Language
------------
To switch between the languages English or Yiddish, the following options are available:
	insert into the address bar, from the main page, following https://titan.dcs.bbk.ac.uk/~nkatz01/w1fma/index.php one of the following: 
	
	?lang=yi.php
	?lang=en.php
	?page=home&lang=yi.php
	?page=home&lang=en.php
	
note: whether you don't put any variable at all or you put ?page=home only, the default language will be set to English.

JSON
------------
To get the details of a given image (such as title, filename description  etc), displayed on the front page gallery, in json format:

	* Either hover on the image with your mouse to see the image id and put in that id in your address bar like this: 
	?image=id ('id' replaced by the id number)
	
	
	* Alternatively, hit on the image to see the larger version of the image and in the address bar, at the end of the query string, you'll see in the third parameter, the id associated with that image. 
	- Still in the address bar, delete up to index.php and insert right after, as before, ?image=id ('id' replaced by the id number) to get the details of the image in json.
