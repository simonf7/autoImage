# autoImage
Take an image and automatically create other sizes, caching by saving the results to the folder.

Requesting test_400x400.jpg will result (if test.jpg exists) in a cropped 400x400 version of test.jpg being returned.

In addition test_400x400.jpg will be written to the folder to be returned next time the file is requested.

Prefixing the size with an 'm' instructs the script to use the size as a maximum size maintaining the aspect ratio of the source. Specifying a large size for either the height or width effectively sets a maximum width or height.

**Note:** *transparency is not retained with PNG and GIF files*

## Parameters
The script can be passed a number of parameters which allow its behaviour to be controlled.

Some of these require a passphrase to be passed to authenticate the request.

### pp={{passphrase}}
This passphrase needs to match that set in the script.

### cmd={{command}}
The command you wish to perform. Some are passed alongside the image request and others direct to the script.

**nocache**

Prevents the script from saving the cached copy of the new image on the server.

*Example*
```
/test_400x400.jpg?cmd=nocache
```

This command doesn't require the passphrase.

**delete**

Call the script directly and delete copies of cached images. 

An extra parameter **fn** needs to be passed to specify the name of the image.

*Example:*
```
/images.php?cmd=delete&fn=test.jpg&pp=secret_passphrase
```

This command requires the passphrase to be passed and correct.

## Configuration options
At the head of the file there are a number of configurable options -

### $imageSource
Specify where the source files are. For example in the current folder -
```
$imageSource = __DIR__ . '/';
```
Or remotely -
```
$imageSource = 'http://www.imageserver.com/';
```

### $defaultFile
If the source file cannot be found, this specifies the filename of a local file to be used as a default.
Note: this *does not* work when the image source is a remote server.
```
$defaultFile = 'default';
```

### $allowTrans
This allows you to specify a list of file types that the source file can be if one is not found matching
the requested file, or *null* if the file types must be the same.
```
$allowTrans = null;
```
For example, *file_100x100.jpg* could be requested but if *file.png* exists on the server, this will allow
the script to convert the PNG file into the requested JPEG.
```
$allowTrans = ['png'];
```
**Note:** *this only works with local files, not those downloaded from elsewhere.*

### $allowedSizes
An array containing a list of sizes that can be requested, specified as a string '400x400' etc.
```
$allowedSizes = ['100x100', '600x400'];
```
If this is null then any size can be requested.
```
$allowedSizes = null;
```

### $jpegQuality
Affects the quality of saved JPEG files. This is specified as a percentage, i.e. 100 being the best.
```
$jpegQuality = 85;
```

### $passPhrase
Passphrase for accessing administion functions when implemented - this should be changed to something else.
```
$passPhrase = 'secret_passphrase';
```
