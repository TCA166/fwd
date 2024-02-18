# fwd

PHP webapp for viewing FWD test results that I created as a part of a larger project a while back.
All of this code got thrown out in a peak example of why you should have the client secured before you start development.
So since nobody is using this and I wrote all of this might as well upload this here.
Also in hindsight writing this in PHP was a mistake (writing things in PHP is always a mistake) though that wasn't my call.

## What it does

The idea was to accept FWD(Falling Weight Deflectometer) test result files, parse them, and then display them to more easily determine the status of the road.
This part worked pretty well, unfortunately FWD files don't have to contain location data, which complicated things, and also trying to assign test results to specific roads proved nearly impossible in PHP.
Additionally FWD files didn't contain certain types of data unlike mdb files, which the fwd device also outputs, but due to PHP magic we weren't able to parse those because mdb is a very good file format that you should definetly use in your embedded device.
These design flaws dragged the project down, untill it died.

## License

All of this was written by me, except for the jquery-3.6.3.min.js, bootstrap-treeview.js and bootstrap-treeview.css files in the static folder.
Decided not to delete them because later trying to determine what is missing would be a mess.
The rest of the code in this repository was written by me and is licensed under GPLv3.
