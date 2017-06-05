# autoImage
Take an image and automatically create other sizes, caching by saving the results to the folder.

Requesting 400x400_test.jpg will result (if test.jpg exists) in a cropped 400x400 version of test.jpg being returned.

In addition 400x400_test.jpg will be written to the folder to be returned next time the file is requested.
