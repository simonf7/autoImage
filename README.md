# autoImage
Take an image and automatically create other sizes, caching by saving the results to the folder.

Requesting 400x400_test.jpg will result (if test.jpg exists) in a cropped 400x400 version of test.jpg being returned.

In addition 400x400_test.jpg will be written to the folder to be returned next time the file is requested.

At the head of the file there are a number of configurable options -

## $allowedSizes
An array containing a list of sizes that can be requested, specified as a string '400x400' etc.

If this is null then any size can be requested.

## $jpegQuality
Affects the quality of saved JPEG files.

## $passPhrase
Pass phrase for accessing administion functions when implemented.
